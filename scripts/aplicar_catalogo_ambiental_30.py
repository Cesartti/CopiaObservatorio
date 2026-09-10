# -*- coding: utf-8 -*-
"""
Publica el catálogo ambiental reducido a los 30 indicadores del Excel oficial.

 1. Respalda las carpetas 3xxx vigentes.
 2. Copia el staging (30 indicadores) sobre website/indicador/.
 3. Marca como retiradas las carpetas que ya no forman parte del catálogo,
    con su equivalente vigente cuando existe, porque el despliegue usa rsync
    sin --delete y borrarlas del repositorio no las quita del servidor.
"""
import os, re, shutil, glob

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IND = os.path.join(BASE, 'website', 'indicador')
STG = os.path.join(BASE, 'website', 'indicador_staging_ambiente')
BK = os.path.join(BASE, 'reportes', 'ambiental_2026', 'backup_42_indicadores')

# Códigos que salen del catálogo y a dónde redirigen (None = al micrositio).
RETIRAR = {
    '3202': 3201,   # cobertura de acueducto rural -> indicador único de acueducto
    '3204': 3203,   # cobertura de alcantarillado rural -> indicador único
    '3209': 3208,   # IRCA rural nucleada -> indicador único de IRCA
    '3507': 3506, '3508': 3506, '3509': 3506, '3510': 3506, '3511': 3506,  # ICA
    '3306': None,   # posconsumo (venía de Respuesta.xlsx)
    '3307': None,   # PRAES/PROCEDAS/CIDEAS (venía de Respuesta.xlsx)
    '3308': None,   # incendios de cobertura vegetal (venía de datos.gov.co)
    '3512': None,   # prácticas de ahorro (DANE)
}

def leer_info(path):
    out = {}
    if os.path.isfile(path):
        for linea in open(path, encoding='utf-8', errors='ignore'):
            if ':' in linea:
                k, v = linea.split(':', 1)
                out[k.strip().lower()] = v.strip()
    return out

vigentes = sorted(e for e in os.listdir(STG) if e.isdigit() and len(e) == 4)
print('indicadores en staging:', len(vigentes))

# 1) respaldo
os.makedirs(BK, exist_ok=True)
respaldados = 0
for e in sorted(os.listdir(IND)):
    if not (e.isdigit() and len(e) == 4 and e[0] == '3'):
        continue
    if leer_info(os.path.join(IND, e, 'indicador.info')).get('retirado'):
        continue
    dst = os.path.join(BK, e)
    if os.path.isdir(dst):
        shutil.rmtree(dst)
    shutil.copytree(os.path.join(IND, e), dst)
    respaldados += 1
print('carpetas respaldadas en reportes/ambiental_2026/backup_42_indicadores:', respaldados)

# 2) publicar los 30
for e in vigentes:
    dst = os.path.join(IND, e)
    if os.path.isdir(dst):
        shutil.rmtree(dst)
    shutil.copytree(os.path.join(STG, e), dst)
print('publicados:', len(vigentes))

# 3) lápidas para los que salen
for cod, destino in RETIRAR.items():
    origen = leer_info(os.path.join(BK, cod, 'indicador.info'))
    carpeta = os.path.join(IND, cod)
    os.makedirs(carpeta, exist_ok=True)
    lineas = ['Retirado:1']
    if destino:
        lineas.append('Reemplazado:%d' % destino)
    lineas += [
        'Titulo:' + origen.get('titulo', 'Indicador ' + cod),
        'Categoría:' + origen.get('categoría', origen.get('categoria', '')),
        'Descripción:Indicador retirado al ajustar el catálogo al Excel oficial del '
        'Observatorio Ambiental (un indicador por hoja con datos, septiembre de 2026).',
        'Subcategoría:' + origen.get('subcategoría', origen.get('subcategoria', '')),
        'Etiquetas:Retirado',
        'Fuentes:' + origen.get('fuentes', ''),
    ]
    open(os.path.join(carpeta, 'indicador.info'), 'w', encoding='utf-8', newline='\n').write(
        '\n'.join(lineas) + '\n')
    open(os.path.join(carpeta, 'display.js'), 'w', encoding='utf-8', newline='\n').write(
        '/* Indicador retirado: ver el indicador vigente. */\n')
    for f in os.listdir(carpeta):
        if re.match(r'^\d+\.(csv|info)$', f):
            os.remove(os.path.join(carpeta, f))
    print('  retirado', cod, '->', destino or 'micrositio')

# 4) recuento final
activos = [e for e in sorted(os.listdir(IND))
           if e.isdigit() and len(e) == 4 and e[0] == '3'
           and not leer_info(os.path.join(IND, e, 'indicador.info')).get('retirado')]
print('\nINDICADORES AMBIENTALES ACTIVOS:', len(activos))
print(' ', ', '.join(activos))
shutil.rmtree(STG, ignore_errors=True)
