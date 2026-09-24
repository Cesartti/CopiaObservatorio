# -*- coding: utf-8 -*-
"""
Calcula el Índice de Salud de la Vegetación (VHI) por municipio de Boyacá.

El VHI combina el estrés hídrico (VCI, a partir del NDVI) y el estrés térmico
(TCI, a partir de la temperatura de superficie) frente a la serie histórica, y
es el indicador que usa la NOAA para el seguimiento de la sequía agrícola:

    VHI < 20   sequía severa
    20 a 40    estrés moderado
    > 40       condición normal o favorable

En vez de recalcularlo, se toma el producto ya elaborado que publica la NOAA
(Blended Vegetation Health Product, 4 km, semanal, construido sobre las series
de los satélites NOAA y de MODIS). Se descarga el GeoTIFF global de la semana
más reciente y se promedian las celdas que caen dentro de cada municipio.

Salida: website/data/fenomenos/vhi_boyaca.json  (unos 20 KB)

Uso:  python scripts/gen_vhi_boyaca.py
"""
import json
import os
import re
import ssl
import urllib.request
from datetime import date, datetime, timedelta

import numpy as np
from PIL import Image

Image.MAX_IMAGE_PIXELS = None  # el mosaico global supera el tope de seguridad

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
GEOJS = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')
DST = os.path.join(BASE, 'website', 'data', 'fenomenos', 'vhi_boyaca.json')
DIR = ('https://www.star.nesdis.noaa.gov/pub/corp/scsb/wguo/data/'
       'Blended_VH_4km/geo_TIFF/')
PORTAL = 'https://www.star.nesdis.noaa.gov/smcd/emb/vci/VH/index.php'

CTX = ssl.create_default_context()
CAB = {'User-Agent': 'RedObservatoriosBoyaca/1.0 (observatorios.boyaca.gov.co)'}

# La malla del producto es fija: EPSG:4326, celda de 0,036° desde (-180; 75,024).
LON0, LAT0, PASO = -180.0, 75.024, 0.036
NODATO = -9999.0


def bajar(url, timeout=600):
    return urllib.request.urlopen(
        urllib.request.Request(url, headers=CAB), timeout=timeout, context=CTX).read()


def ultimo_archivo():
    """El GeoTIFF de VHI más reciente publicado en el directorio de la NOAA."""
    html = bajar(DIR, 120).decode('utf-8', 'ignore')
    archivos = sorted(re.findall(r'href="([^"]*\.VH\.VHI\.tif)"', html))
    if not archivos:
        raise SystemExit('La NOAA no está listando archivos VHI en ' + DIR)
    return archivos[-1]


def semana_del_archivo(nombre):
    """VHP.G04.C07.j01.P2026037.VH.VHI.tif -> (2026, 37) y la fecha de cierre."""
    m = re.search(r'\.P(\d{4})(\d{3})\.', nombre)
    if not m:
        return None, None, ''
    anio, semana = int(m.group(1)), int(m.group(2))
    # Las semanas del producto arrancan el 1 de enero.
    fin = datetime(anio, 1, 1) + timedelta(days=semana * 7 - 1)
    return anio, semana, fin.strftime('%Y-%m-%d')


def municipios():
    """Lee los polígonos municipales que ya usa el portal para sus mapas."""
    txt = open(GEOJS, encoding='utf-8').read()
    geo = json.loads(txt[txt.index('{'):txt.rindex('}') + 1])
    out = []
    for f in geo['features']:
        anillos = []
        g = f['geometry']
        partes = g['coordinates'] if g['type'] == 'MultiPolygon' else [g['coordinates']]
        for poli in partes:
            if poli:
                anillos.append(np.asarray(poli[0], dtype=float))  # anillo exterior
        out.append({'dane': f['properties']['id'],
                    'nombre': f['properties']['name'],
                    'anillos': anillos})
    return out


def dentro(anillos, lon, lat):
    """Punto en polígono por lanzamiento de rayo, sobre cualquiera de los anillos."""
    for a in anillos:
        x, y = a[:, 0], a[:, 1]
        xj, yj = np.roll(x, 1), np.roll(y, 1)
        cruza = ((y > lat) != (yj > lat)) & \
                (lon < (xj - x) * (lat - y) / np.where(yj == y, 1e-12, yj - y) + x)
        if bool(cruza.sum() % 2):
            return True
    return False


def severidad(v):
    if v < 20:
        return 'Sequía severa'
    if v < 40:
        return 'Estrés moderado'
    return 'Condición favorable'


print('Consultando el último producto de la NOAA…')
nombre = ultimo_archivo()
anio, semana, corte = semana_del_archivo(nombre)
print(f'   {nombre}  (semana {semana} de {anio}, cierre {corte})')

print('Descargando el mosaico global (unos 32 MB)…')
crudo = bajar(DIR + nombre)
tmp = os.path.join(os.environ.get('TEMP', '.'), nombre)
open(tmp, 'wb').write(crudo)
malla = np.array(Image.open(tmp), dtype=np.float32)
print(f'   malla {malla.shape[1]}x{malla.shape[0]} celdas')

muni = municipios()
print(f'Cruzando con {len(muni)} municipios…')

# Recorte a Boyacá para no recorrer el planeta entero.
todos = np.vstack([a for m in muni for a in m['anillos']])
lon_min, lat_min = todos[:, 0].min(), todos[:, 1].min()
lon_max, lat_max = todos[:, 0].max(), todos[:, 1].max()
c0 = max(int((lon_min - LON0) / PASO) - 1, 0)
c1 = min(int((lon_max - LON0) / PASO) + 2, malla.shape[1])
f0 = max(int((LAT0 - lat_max) / PASO) - 1, 0)
f1 = min(int((LAT0 - lat_min) / PASO) + 2, malla.shape[0])
recorte = malla[f0:f1, c0:c1]
print(f'   ventana de Boyacá: {recorte.shape[1]}x{recorte.shape[0]} celdas de 4 km')

# Centro de cada celda de la ventana.
cols = np.arange(c0, c1)
filas = np.arange(f0, f1)
lons = LON0 + (cols + 0.5) * PASO
lats = LAT0 - (filas + 0.5) * PASO

resultado = {}
aproximados = 0
for m in muni:
    a = np.vstack(m['anillos'])
    imin = np.searchsorted(lons, a[:, 0].min()) - 1
    imax = np.searchsorted(lons, a[:, 0].max()) + 1
    jmin = np.searchsorted(-lats, -a[:, 1].max()) - 1
    jmax = np.searchsorted(-lats, -a[:, 1].min()) + 1

    vals = []
    for j in range(max(jmin, 0), min(jmax + 1, len(lats))):
        for i in range(max(imin, 0), min(imax + 1, len(lons))):
            v = float(recorte[j, i])
            if v == NODATO or v < 0 or v > 100:
                continue
            if dentro(m['anillos'], lons[i], lats[j]):
                vals.append(v)

    aprox = False
    if not vals:
        # Municipio más pequeño que la celda: se toma la celda válida más cercana
        # al centro del polígono y se deja constancia de la aproximación.
        aprox = True
        aproximados += 1
        cl, ct = a[:, 0].mean(), a[:, 1].mean()
        i = int(np.clip(np.searchsorted(lons, cl), 0, len(lons) - 1))
        j = int(np.clip(np.searchsorted(-lats, -ct), 0, len(lats) - 1))
        mejor = None
        for dj in range(-2, 3):
            for di in range(-2, 3):
                jj, ii = j + dj, i + di
                if 0 <= jj < recorte.shape[0] and 0 <= ii < recorte.shape[1]:
                    v = float(recorte[jj, ii])
                    if v != NODATO and 0 <= v <= 100:
                        d = dj * dj + di * di
                        if mejor is None or d < mejor[0]:
                            mejor = (d, v)
        if mejor is None:
            continue
        vals = [mejor[1]]

    vhi = round(float(np.mean(vals)), 2)
    resultado[m['dane']] = {
        'nombre': m['nombre'],
        'vhi': vhi,
        'severidad': severidad(vhi),
        'celdas': len(vals),
        'aproximado': aprox,
    }

salida = {
    'fuente': 'NOAA STAR · Blended Vegetation Health Product (VHI), 4 km, semanal',
    'url': PORTAL,
    'archivo': nombre,
    'anio': anio,
    'semana': semana,
    'corte': corte,
    'generado': str(date.today()),
    'nota': ('El VHI combina estrés hídrico y térmico frente a la serie histórica. '
             'Por debajo de 20 indica sequía severa; entre 20 y 40, estrés moderado; '
             'por encima de 40, condición favorable.'),
    'municipios': dict(sorted(resultado.items())),
}
os.makedirs(os.path.dirname(DST), exist_ok=True)
with open(DST, 'w', encoding='utf-8', newline='\n') as fh:
    json.dump(salida, fh, ensure_ascii=False, indent=1)

vals = [m['vhi'] for m in resultado.values()]
sev = {}
for m in resultado.values():
    sev[m['severidad']] = sev.get(m['severidad'], 0) + 1
print(f'\nmunicipios calculados: {len(resultado)} de {len(muni)}'
      f'   (por celda más cercana: {aproximados})')
print(f'VHI: mínimo {min(vals):.1f} · promedio {np.mean(vals):.1f} · máximo {max(vals):.1f}')
for k, v in sorted(sev.items(), key=lambda x: -x[1]):
    print(f'   {k:22} {v:>3}')
peor = sorted(resultado.items(), key=lambda x: x[1]['vhi'])[:8]
print('\nmunicipios con menor VHI:')
for d, m in peor:
    print(f'   {m["nombre"]:22} {m["vhi"]:>6}  {m["severidad"]}')
print(f'\n-> {os.path.relpath(DST, BASE)}  ({os.path.getsize(DST) / 1024:.0f} KB)')
