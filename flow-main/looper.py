# coding=utf-8
from importlib import reload

import functions as fn

import configparser
import traceback
import openpyxl
import datetime
import logging
import pathlib
import time
import csv
import sys
import os

config=configparser.ConfigParser()
config.read('config.ini')
config=config['DEFAULT']

receiverEmail=config.get('receiverEmail','')
senderEmail=config.get('senderEmail','')
password=config.get('password','')
git=(True if config.get('git','false')=='true' else False)
logFile=config.get('logFile','')
logVerbosity=int(config.get('logVerbosity',logging.INFO))

logger=logging.getLogger()
logger.setLevel(level=logVerbosity)

handler=logging.StreamHandler()
if len(logFile)>0:
    handler=logging.FileHandler(logFile, 'w', 'utf-8')

logFormat=config.get('logFormat','%(asctime)s - %(name)s - %(levelname)s - %(message)s')
handler.setFormatter(logging.Formatter(logFormat))
logger.addHandler(handler)

logging.getLogger('PIL.PngImagePlugin').disabled = True

sleepTime=int(config.get('sleepTime',5))
logging.info('Iniciando motor de actualización de fuentes, sleepTime='+str(sleepTime))

if os.path.exists('looper.stop'):
    logging.error('El motor parece estar en ejecución, se encontró el archivo looper.stop')
else:
    outPath=config.get('outPath','')
    gitPre='git --git-dir '+outPath+'.git --work-tree '+outPath+' '
    gitPost=' > /dev/null'
    if len(outPath)==0:
        logging.error('Debe configurar la ruta de salida en config.ini')
    else:
        logging.info('outPath='+outPath)
        with open('looper.stop', 'w') as looper:
            looper.close()
        while os.path.exists('looper.stop'):
            try:
                for source in os.listdir('datalake/'):
                    if(source.endswith('.xlsx')):
                        id=source[0:source.find('.')]
                        lastUpdate=None
                        if os.path.exists('datalake/'+id+'.update') or os.path.exists('datalake/'+id+'.error'):
                            ext='.update'
                            if os.path.exists('datalake/'+id+'.error'):
                                ext='.error'
                            lastUpdate=pathlib.Path('datalake/'+id+ext).stat().st_mtime

                        etl=lastUpdate
                        sourcePath='etl/'+id+'.py'
                        if os.path.exists(sourcePath):
                            etl=pathlib.Path(sourcePath).stat().st_mtime
                        
                        sourcePath='datalake/'+source
                        dt=pathlib.Path(sourcePath).stat().st_mtime
                        
                        if lastUpdate is None or lastUpdate<dt or lastUpdate<etl:
                            count=1
                            logging.info(id+' Requiere actualización, Abriendo...')
                            workbook=openpyxl.load_workbook(sourcePath)
                            
                            error=False
                            errorMessage=''
                            try:
                                fn.run0(workbook['data0'],outPath+'indicador/'+id+'/')
                            except Exception as e:
                                error=True
                                logging.error('Error cargando data0 para '+id)
                                logging.exception("message")
                                errorMessage=repr(e)
                                #fn.sendError(senderEmail,password,receiverEmail,'Error cargando data0 para '+id,
                                #    errorMessage+'\n'+traceback.format_exc())
                            
                            if error==False:
                                try:
                                    module=getattr(__import__('etl.'+id), id)
                                    module=reload(module)
                                except Exception as e:
                                    error=True
                                    logging.error('Error cargando módulo etl/'+id)
                                    logging.exception("message")
                                    errorMessage=repr(e)
                                    #fn.sendError(senderEmail,password,receiverEmail,'Error cargando módulo etl/'+id+'.py',
                                    #    errorMessage+'\n'+traceback.format_exc())
                            if error==False:
                                while 'data'+str(count) in workbook.sheetnames:
                                    sheet=workbook['data'+str(count)]
                                    fn.dataParams(sheet,outPath+'indicador/'+id+'/',str(count))
                                    try:
                                        conf=getattr(module,'getConfig'+str(count))()
                                        index=conf[0]
                                        formats=conf[1]
                                        direction=conf[2]
                                        categories=1
                                        if len(conf)>3:
                                            categories=conf[3]
                                        fn.genericRun(sheet,outPath+'indicador/'+id+'/'+str(count)+'.csv',index,formats,direction,categories)
                                        logging.debug('Hoja '+id+'-'+str(count)+' procesada con éxito')
                                    except Exception as e:
                                        error=True
                                        logging.error('Error procesando Hoja '+id+'-'+str(count))
                                        logging.exception("message")
                                        errorMessage=repr(e)
                                        #fn.sendError(senderEmail,password,receiverEmail,'Error procesando Hoja '+id+'-'+str(count),
                                        #    errorMessage+'\n'+traceback.format_exc())
                                        break
                                    count+=1
                            ext='.update'
                            if error:
                                ext='.error'
                            if os.path.exists('datalake/'+id+'.update'):
                                os.remove('datalake/'+id+'.update')
                            if os.path.exists('datalake/'+id+'.error'):
                                os.remove('datalake/'+id+'.error')
                            with open('datalake/'+id+ext, 'w') as update_file:
                                if error:
                                    update_file.write(errorMessage)
                                else:
                                    update_file.write(f'{time.time()}\n')
                                update_file.close()
                                logging.info(id+' Finalizado ('+ext+')')
                            if error==False and git:
                                task='add'
                                res=os.system(gitPre+task+' indicador/'+id+'/'+gitPost)
                                if res==0:
                                    task='commit'
                                    res=os.system(gitPre+task+' -m "actualización automatizada por looper para indicador '+id+'"'+gitPost)
                                    if res==0:
                                        task='push'
                                        res=os.system(gitPre+task+' -q'+gitPost)
                                        if res==0:
                                            logging.info(id+' actualizado en repositorio')
                                if res>0:
                                    logging.error('Error ('+str(res)+') procesando repositorio: '+task)
                                    #fn.sendError(senderEmail,password,receiverEmail,'Error actualizando '+id,'Error ('+str(res)+') procesando repositorio: '+task+' en indicador '+id)
                        else:
                            logging.debug('Omitiendo fuente '+id+', No requiere actualización')
                time.sleep(sleepTime)
            except Exception as e:
                logging.error('Error de loop principal')
                logging.exception("message")
                #fn.sendError(senderEmail,password,receiverEmail,'Error de loop principal',str(e)+'\n'+traceback.format_exc())
logging.info('Finalizando el motor de actualización de fuentes')