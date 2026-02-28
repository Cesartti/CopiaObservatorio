from directions import VERTICAL, HORIZONTAL

def getConfig1():
    # Configuración para data1: Gráfica de columnas (Población 15 años y más)
    return ['%i', '%s'], '%i', VERTICAL

def getConfig2():
    # Configuración para data2: Gráfica de columnas (Población alfabetizada)
    return ['%i', '%s'], '%i', VERTICAL

def getConfig3():
    # Configuración para data3: Gráfica de columnas (Población analfabeta)
    return ['%i', '%s'], '%i', VERTICAL

def getConfig4():
    # Configuración para data4: Gráfica de columnas (Tasa de analfabetismo)
    return ['%i', '%f'], '%i', VERTICAL

def getConfig5():
    # Configuración para data5: Mapa (Tasa de analfabetismo por municipio)
    return 'geo',['%i','%i','%i','%f'], VERTICAL

def getConfig2():
    return 'geo',['%i','%s','%s','%i'],VERTICAL