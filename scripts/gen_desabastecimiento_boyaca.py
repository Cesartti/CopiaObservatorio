# -*- coding: utf-8 -*-
"""
Extrae el riesgo de desabastecimiento de agua de los municipios de Boyacá.

La capa «Desabastecimiento» del visor FEWS del IDEAM pesa 19 MB porque trae la
geometría de los 1.140 municipios del país, repetida dos veces. Como es un
estudio fijo (1998-2021) y el portal ya tiene sus propios polígonos, aquí se
descarga una sola vez y se guarda únicamente lo que se necesita: el código
DANE, el nombre y la categoría de afectación de los municipios de Boyacá.

Salida: website/data/fenomenos/desabastecimiento_boyaca.json

Uso:  python scripts/gen_desabastecimiento_boyaca.py
"""
import json
import os
import ssl
import urllib.request
from datetime import date

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CA = os.path.join(BASE, 'website', 'data', 'fenomenos', 'certs', 'ideam_ca_bundle.pem')
DST = os.path.join(BASE, 'website', 'data', 'fenomenos', 'desabastecimiento_boyaca.json')
URL = 'https://fews.ideam.gov.co/visorfews/data/Desabastecimiento.json'

# El IDEAM entrega la cadena TLS incompleta: se verifica contra el paquete
# propio, que incluye el intermedio que su servidor omite.
ctx = ssl.create_default_context(cafile=CA) if os.path.isfile(CA) else ssl.create_default_context()

print(f'Descargando {URL} (unos 19 MB, puede tardar un minuto)…')
req = urllib.request.Request(URL, headers={'User-Agent': 'RedObservatoriosBoyaca/1.0'})
crudo = urllib.request.urlopen(req, timeout=180, context=ctx).read()
print(f'   {len(crudo) / 1e6:.1f} MB descargados')

geo = json.loads(crudo.decode('utf-8', 'ignore'))
muni = {}
for f in geo.get('features', []):
    p = f.get('properties', {})
    dane = str(p.get('Codigo_DANE') or '').strip()
    if len(dane) != 5 or not dane.startswith('15'):
        continue
    if dane in muni:
        continue  # cada municipio viene repetido
    muni[dane] = {
        'nombre': str(p.get('Municipio') or '').strip(),
        'categoria': str(p.get('Categoria') or 'Sin afectación').strip(),
        'seco': (p.get('Desabastecimiento_seco') == 'Si'),
        'humedo': (p.get('Desabastecimiento_humedo') == 'Si'),
    }

salida = {
    'fuente': 'IDEAM · Visor FEWS Colombia, capa «Desabastecimiento por municipio (1998-2021)»',
    'url': 'https://fews.ideam.gov.co/visorfews/nacional',
    'generado': str(date.today()),
    'municipios': dict(sorted(muni.items())),
}
os.makedirs(os.path.dirname(DST), exist_ok=True)
with open(DST, 'w', encoding='utf-8', newline='\n') as fh:
    json.dump(salida, fh, ensure_ascii=False, indent=1)

cat = {}
for m in muni.values():
    cat[m['categoria']] = cat.get(m['categoria'], 0) + 1
print(f'\nmunicipios de Boyacá: {len(muni)}')
for k, v in sorted(cat.items(), key=lambda x: -x[1]):
    print(f'   {k:26} {v:>3}')
print(f'\n-> {os.path.relpath(DST, BASE)}  ({os.path.getsize(DST) / 1024:.0f} KB)')
