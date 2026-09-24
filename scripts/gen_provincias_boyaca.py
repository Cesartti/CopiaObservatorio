# -*- coding: utf-8 -*-
"""
Arma la tabla de municipio → provincia que usan los filtros de los mapas.

La correspondencia ya está en las migraciones del directorio de bomberos, que
se consolidó con la Secretaría de Planeación; aquí solo se extrae a un archivo
plano para que el navegador pueda filtrar sin consultar la base de datos.

Salida: website/data/fenomenos/provincias_boyaca.json
"""
import glob
import io
import json
import os
import re
from datetime import date

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DST = os.path.join(BASE, 'website', 'data', 'fenomenos', 'provincias_boyaca.json')
GEOJS = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')

pares = {}
for f in sorted(glob.glob(os.path.join(BASE, 'database', 'migrations', '*.sql'))):
    txt = io.open(f, encoding='utf-8').read()
    # Estaciones: (nombre, municipio, dane, provincia, …)
    for m in re.finditer(r"VALUES \('[^']*', '([^']*)', '(15\d{3})', '([^']*)'", txt):
        pares[m.group(2)] = {'municipio': m.group(1), 'provincia': m.group(3)}
    # Cobertura: (municipio, dane, provincia, …)
    for m in re.finditer(r"VALUES \('([^']*)', '(15\d{3})', '([^']*)'", txt):
        pares.setdefault(m.group(2), {'municipio': m.group(1), 'provincia': m.group(3)})

geo = io.open(GEOJS, encoding='utf-8').read()
danes = sorted(set(re.findall(r'"id":"(15\d{3})"', geo)))
faltan = [d for d in danes if d not in pares]
if faltan:
    raise SystemExit('Sin provincia: ' + ', '.join(faltan))

salida = {
    'fuente': 'Directorio de cuerpos de bomberos de Boyacá, consolidado por la '
              'Secretaría de Planeación (migraciones 029 y 030)',
    'generado': str(date.today()),
    'municipios': {d: pares[d] for d in danes},
}
os.makedirs(os.path.dirname(DST), exist_ok=True)
with open(DST, 'w', encoding='utf-8', newline='\n') as fh:
    json.dump(salida, fh, ensure_ascii=False, indent=1)

prov = {}
for d in danes:
    p = pares[d]['provincia']
    prov[p] = prov.get(p, 0) + 1
print(f'municipios: {len(danes)}   provincias: {len(prov)}')
for p, n in sorted(prov.items(), key=lambda x: -x[1]):
    print(f'   {p:24} {n:>3}')
print(f'-> {os.path.relpath(DST, BASE)}')
