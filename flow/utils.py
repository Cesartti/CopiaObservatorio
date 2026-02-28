# coding=utf-8

from directions import VERTICAL,HORIZONTAL

import numbers
import re

def getValueMulti(worksheet,row,col,_format,rowInc,colInc):
    
    """ Funcion encargada de leer y recorrer la data en cada una de las tablas en cada hoja(data n)
    
    Parameters
    ----------
    worksheet : str
        objeto de excel que recorre la data en tablas en cada hoja (data n)
    row : int
        posición # de fila donde se debe ubicar para leer dato tabla 
    col : int
        posición # de columna donde se debe ubicar para leer dato tabla
    rowInc : int
        posición # de fila, orientación de lectura en tabla; dato ETL (VERTICAL - HORIZONTAL)
    colInc : int
        posición # de columna, orientación de lectura tabla; dato ETL (VERTICAL - HORIZONTAL)
    
    """
    
    for f in _format:
        row-=colInc
        col-=rowInc
    
    res=worksheet.cell(row,col).value
    if res is not None:
        res=''
        for f in _format:
            if len(res)>0:
                res+=','
            res+=(f%getValue(worksheet,row,col,f))
            row+=colInc
            col+=rowInc
    return res,row,col

def getValue(worksheet,row,col,_format):
    
    """ Funcion encargada de construir cadena string con formato correcto del dato que contiene el archivo excel
    Parameters
    ----------
    worksheet : str
        objeto de excel que recorre la data en la tabla
    row : int
        posición # de fila dato tabla
    col : int
        posición # de columna dato tabla
    _format : str
        formato tipo de dato para la data
        
    """
    
    res=worksheet.cell(row,col).value
    if res is not None:
        if _format=='%i':
            if res=='-':
                res=None
            else:
                res=int(res)
        if _format=='%f':
            res=float(res)
        if _format=='%s':
            res=re.sub(' +',' ',str(res)).strip().replace(',','¬').replace('\n',' ')
    return res

def getCategories(worksheet,index,direction,n):
    """Función encargada de validar y recorrer categorias
    
    Parameters
    ----------
    worksheet : str
        objeto de excel que recorre data en la tabla
    index : str
        formatos de data
    direction : str
        orientación recorrido para lectura de la tabla en archivo excel
    n : int
        cantidad de categorias
    
    Returns
    -------
    list
        res
        
    """   
    
    row=1
    col=1
    rowInc=0
    colInc=0
    if direction==VERTICAL:
        rowInc=1
    else:
        colInc=1
    for i in index:
        row+=rowInc
        col+=colInc
    iniRow=row
    iniCol=col
    
    res=[]
    category=getValue(worksheet,row,col,'%s')
    for i in range(n):
        categories=[]
        while category:
            categories.append(category)
            row+=rowInc
            col+=colInc
            category=getValue(worksheet,row,col,'%s')
        res.append(categories)
        
        if direction==HORIZONTAL:
            row+=1
            col=iniCol-1
        else:
            col+=1
            row=iniRow-1
        category=getValue(worksheet,row,col,'%s')
    
    return res


def categoryToTitle(title,categories,formats):
    """Función encargada de leer para imprimir los titulos del archivo .csv para cada hoja ( data n)
    
    Parameters
    ----------
    title : str
        contiene información los titutlos tabla
    categories : str
        las categorias de la tabla
    formats : str
        formato de datos para imprimir la data del titulo
    
    Returns
    -------
    string
        title
    """
    
    for count in range(len(categories)):
        i=0
        for category in categories[count]:
            if formats is not None:
                _format=formats
                if not isinstance(formats,str):
                    _format=formats[i]
                if _format=='geo':
                    category='geo'
            if count==0 or i>0:
                title+=','
            title+=category
            i+=1
        title+='\n'
    return title

def readCategoryValues(categories,worksheet,row,col,direction,formats):
    """Función encargada recorrer la data en la tabla de acuerdo con las categorias y orientación de la ETL (VERTICAL, HORIZONTAL) que contenga la tabla
    
    Parameters
    ----------
    categories : str
        categorias que contiene la tabla
    worksheet : str
        objeto de excel que recorre data en la tabla
    row : int
        posición # de fila dato tabla
    col : int
        posición # de columna dato tabla
    direction : str
        orientación recorrido para lectura de la tabla en archivo excel
    formats : str
        formato de datos para imprimir cada registro
    """
    
    rowInc=0
    colInc=0
    if direction==VERTICAL:
        rowInc=1
    else:
        colInc=1
        
    v=''
    i=0
    for category in categories[0]:
        _format=formats
        if not isinstance(formats,str):
            _format=formats[i]
        if _format=='geo':
            _format='%i'
        value=getValue(worksheet,row,col,_format)
        if value is None:
            v+=','
        else:
            v+=((','+_format) % value)
        row+=rowInc
        col+=colInc
        i+=1
    return v
