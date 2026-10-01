# -*- coding: utf-8 -*-
"""
Revisa las bases de datos de database/seeds antes de cargar indicadores.

Nació de una confusión que conviene dejar escrita: en el Observatorio Ambiental
cada hoja del libro era un indicador, y con esa regla se depuró el observatorio
hasta dejar exactamente los 30 del Excel. En Social, Económico y CTeI la regla
NO aplica: ahí cada hoja es una tabla fuente de la que salen varios indicadores
—«Índice brecha digital», por ejemplo, trae cinco columnas que son cinco
indicadores— y además la misma hoja aparece repetida en varios libros porque
distintas líneas temáticas comparten la fuente.

Por eso este script no cuenta indicadores: reporta el estado de los libros para
poder decidir qué cargar. Señala lo que bloquea una carga limpia:

  · hojas vacías, que no tienen nada que cargar;
  · hojas con nombre provisional («nnnnn», «Hoja1»), que no se pueden mapear;
  · hojas repetidas entre libros, para saber cuál manda;
  · libros duplicados con el mismo contenido y distinto nombre.

No modifica nada: solo reporta.

Uso:  python scripts/revisar_bases_seeds.py
"""
import collections
import glob
import os
import re
import unicodedata
import warnings

import openpyxl

warnings.filterwarnings('ignore')

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SEEDS = os.path.join(BASE, 'seeds') if os.path.isdir(os.path.join(BASE, 'seeds')) \
    else os.path.join(BASE, 'database', 'seeds')

# A qué observatorio pertenece cada libro, por su nombre de archivo.
OBSERVATORIOS = [
    ('Social', 'BASE DX OBS SOCIAL'),
    ('Económico', 'BASE DX OBS ECON'),
    ('CTeI', 'BASE DX OBS CTI'),
    ('Género', 'BASE DX OBS ASUNTOS DE G'),
    ('Ambiental', 'BD DX OBS AMBIENTAL'),
]
PROVISIONALES = re.compile(r'^(hoja\s*\d*|sheet\s*\d*|n+|datos|tabla\s*\d*|sin\s*t)', re.I)


def sin_tildes(s):
    return unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode()


def observatorio_de(nombre):
    n = sin_tildes(nombre).upper()
    for obs, patron in OBSERVATORIOS:
        if sin_tildes(patron).upper() in n:
            return obs
    return None


def filas_reales(ws):
    """max_row suele venir inflado por formato: se cuenta hasta la última fila
    que tenga algún valor, mirando solo las primeras columnas."""
    n = 0
    for i, fila in enumerate(ws.iter_rows(max_col=6, values_only=True)):
        if any(v is not None and str(v).strip() != '' for v in fila):
            n = i + 1
        if i > 200000:
            break
    return n


print('=' * 78)
print('ESTADO DE LAS BASES EN database/seeds')
print('=' * 78)

libros = sorted(glob.glob(os.path.join(SEEDS, '*.xlsx')))
porobs = collections.defaultdict(list)
hojas_por_obs = collections.defaultdict(lambda: collections.defaultdict(list))
vacias, provisionales = [], []

for f in libros:
    nombre = os.path.basename(f)
    if nombre.startswith('NO '):
        continue                       # el prefijo «NO» marca los descartados
    obs = observatorio_de(nombre)
    if obs is None:
        continue
    try:
        wb = openpyxl.load_workbook(f, read_only=True, data_only=True)
    except Exception as e:
        print(f'   {nombre}: no se puede abrir ({type(e).__name__})')
        continue
    corto = nombre.replace('BASE DX OBS ', '').replace('.xlsx', '')
    porobs[obs].append((corto, len(wb.sheetnames), os.path.getmtime(f)))
    for h in wb.sheetnames:
        ws = wb[h]
        n = filas_reales(ws)
        hojas_por_obs[obs][h.strip()].append(corto)
        if n <= 1:
            vacias.append((obs, corto, h.strip(), n))
        if PROVISIONALES.match(h.strip()):
            provisionales.append((obs, corto, h.strip(), n))
    wb.close()

import datetime
for obs, _ in OBSERVATORIOS:
    if obs not in porobs:
        continue
    hojas = hojas_por_obs[obs]
    rep = {h: v for h, v in hojas.items() if len(v) > 1}
    print(f'\n--- {obs}')
    for corto, n, mt in sorted(porobs[obs]):
        fecha = datetime.date.fromtimestamp(mt).isoformat()
        print(f'      {corto[:46]:48} {n:>3} hojas   {fecha}')
    print(f'      → {len(hojas)} hojas distintas; {len(rep)} aparecen en más de un libro')
    for h, v in sorted(rep.items(), key=lambda x: -len(x[1]))[:6]:
        print(f'         «{h[:30]}» en {len(v)}: {", ".join(v)[:60]}')

print('\n' + '=' * 78)
print(f'HOJAS VACÍAS O SOLO CON ENCABEZADO: {len(vacias)}')
for obs, libro, h, n in vacias:
    print(f'   {obs:10} {libro[:30]:32} «{h}»  ({n} filas)')

print('\n' + '=' * 78)
print(f'HOJAS CON NOMBRE PROVISIONAL: {len(provisionales)}')
for obs, libro, h, n in provisionales:
    print(f'   {obs:10} {libro[:30]:32} «{h}»  ({n} filas con datos)')

print('\n' + '=' * 78)
print('LIBROS QUE PARECEN VERSIONES DEL MISMO CONTENIDO')
for obs in porobs:
    porhojas = collections.defaultdict(list)
    for corto, n, _ in porobs[obs]:
        porhojas[frozenset(h for h, v in hojas_por_obs[obs].items() if corto in v)].append(corto)
    for conjunto, libros_ in porhojas.items():
        if len(libros_) > 1:
            print(f'   {obs}: mismas {len(conjunto)} hojas en → {", ".join(libros_)}')
print('\nNota: una hoja puede alimentar varios indicadores. Este informe no dice '
      'cuántos\nindicadores hay, dice qué está listo para cargarse y qué no.')
