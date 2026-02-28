# coding=utf-8
from directions import VERTICAL
import utils as ut
import logging

import os
import ssl,smtplib
from email.message import EmailMessage

def genericRun(worksheet,outPath,iFormats,formats,direction,n):
    """Función encargada de procesar de forma genérica archivos excel encontrado en el datalake
    
    Parameters
    ----------
    worksheet : str
        objeto de excel que representa una hoja del archivo
    outPath:
        ruta de salida de los archivos CSV e info para el despliegue visual
    iFormats : str 
        formato de dato llave principal inicial de la tabla
    formats : str
        formato de datos en la tabla
    direction : str
        orientación recorrido para lectura del archivo excel; dato ETL (VERTICAL - HORIZONTAL)
    n : int
        cantidad de categorias
    
    Returns
    -------
    list
        index,row,col
        
    """   
    
    if isinstance(iFormats,str):
        iFormats=[iFormats]
    categories=ut.getCategories(worksheet,iFormats,-direction,n)
    row=1
    col=1
    rowInc=0
    colInc=0
    if direction==VERTICAL:
        rowInc=1
    else:
        colInc=1
    
    indexName=''
    i=0
    for iFormat in iFormats:
        aux=ut.getValue(worksheet,row,col,'%s')
        if not aux:
            aux='Index'
        if iFormat=='geo':
            iFormats[i]='%i'
            aux='geo'
        if len(indexName)>0:
            indexName+=','
        indexName+=aux
        
        row+=colInc
        col+=rowInc
        i+=1
    
    for i in range(n):
        row+=rowInc
        col+=colInc
    index,row,col = ut.getValueMulti(worksheet,row,col,iFormats,rowInc,colInc)
    
    with open(outPath,'w',encoding='utf-8') as csvfile:
        csvfile.write(ut.categoryToTitle(indexName,categories,formats))
        while index is not None:
            v=ut.readCategoryValues(categories,worksheet,row,col,-direction,formats)
            csvfile.write('%s%s\n' % (index, v))
            row+=rowInc
            col+=colInc
            index,row,col=ut.getValueMulti(worksheet,row,col,iFormats,rowInc,colInc)

def extractParams(worksheet,outPath,name,row):
    
    """ Funcion encargada de imprimir los archivos ( .info); que contienen descripciones e información alredededor del indicador para las n hojas 
    
    Parameters
    ----------
    worksheet : str
        objeto de excel que recorre la data de cada hoja para imprimir el archivo
    outPath : str
        ruta de salida archivo para imprimir los archivos .info de cada indicador
    name : str
        nombre de archivo 
    row : int
        posición # inicial de fila dato tabla
    
    """
    with open(outPath+name+'.info', 'w',encoding='utf-8') as csvfile:
        param=worksheet.cell(row,1).value
        i=0
        while param:
            value=worksheet.cell(row+i,2).value
            if value:
                value=" ".join(str(value).replace('\n',' ').split())
            else:
                value=''
            csvfile.write("%s%s\n" % (param,value))
            i+=1
            param=worksheet.cell(row+i,1).value
    if i==0:
        logging.error('No se encontraron parámetros en '+outPath+name)
    else:
        logging.debug('Parámetros extraidos con éxito de '+outPath+name)

def run0(worksheet,outPath):
    """ Funcion encargada validar si archivo indicador.info existe y proceder a crearlo para registros data0
    
    Parameters
    ----------
    worksheet : str
        objeto de excel que recorre la data de la hoja1 (data0)
    outPath:
        ruta de salida del archivo para la carpeta del indicador
    """
    
    if not os.path.isdir(outPath):
        os.makedirs(outPath)
    extractParams(worksheet,outPath,'indicador',1)
    

    
    
def dataParams(worksheet,outPath,name):
    """ Funcion encargada de recorrer cada hoja de archivo excel indicador segun hojas que contenga data0...data n
    
    Parameters
    ----------
    worksheet : str
        objeto de excel que recorre las hojas que contenga el archivo
    outPath:
        ruta de salida del archivo para la carpeta del indicador
    name : int 
        numero de la hoja en la lectura
    
    """
    
    row=2
    param=worksheet.cell(row,1).value
    while param:
        row+=1
        param=worksheet.cell(row,1).value
    extractParams(worksheet,outPath,name,row+1)

#SCOPES = ['https://www.googleapis.com/auth/gmail.send']
def sendError(senderEmail,password,receiverEmail,title,message):
    """ Funcion encargada de enviar alerta de error en lectura de indicador
    
    Parameters
    ----------
    receiverEmail : str
        correo configurado donde se recibirán los mensajes de alerta del proceso
    title : str
        asunto correo: Error procesando Hoja mencionando el # indicador
    message : str 
        mensaje con contenido descripción del error 
    
    """
    
    if(len(receiverEmail)>0):
        smtp=None
        try:
            em=EmailMessage()
            em.set_content(message)
            em['To'] = receiverEmail
            em['From'] = senderEmail
            em['Subject'] = title
            context=ssl.create_default_context()
            smtp=smtplib.SMTP_SSL('smtp.gmail.com',465,context=context)
            smtp.login(senderEmail,password)
            smtp.sendmail(senderEmail,receiverEmail,em.as_string())
        except Exception:
            logging.error('Error Enviando correo: '+title)
            logging.exception("message")
        finally:
            if smtp is not None:
                try:
                    smtp.quit()
                except:
                    pass