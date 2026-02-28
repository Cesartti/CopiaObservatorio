from directions import VERTICAL

def getConfig1(): 
    return 'geo',['%i','%s','%i'],VERTICAL
    
def getConfig2(): 
    return ['%i','%s'],'%i',VERTICAL

def getConfig3():
    return '%i','%f',VERTICAL

def getConfig4():
     return '%s','%i',VERTICAL

def getConfig5():
    return '%s',['%s','%f','%i'],VERTICAL

def getConfig6(): 
    return '%s',['%f','%i'],VERTICAL
