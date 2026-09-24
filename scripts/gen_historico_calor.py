# -*- coding: utf-8 -*-
"""
Construye el histórico diario de focos de calor en Boyacá (NASA FIRMS).

El mapa de fenómenos ya muestra los focos activos de los últimos días, pero esa
consulta solo alcanza la ventana en tiempo casi real. FIRMS también publica el
archivo procesado de VIIRS desde 2012, de modo que sí se puede reconstruir la
serie y ver cómo se comportó cada temporada seca.

Dos detalles del servicio que conviene tener presentes:

  · el parámetro de rango falla en silencio —devuelve vacío— con 7 días o más;
    con 5 responde bien, así que se recorre en tramos de 5 días;
  · las fuentes NRT solo cubren los últimos meses y las SP terminan unos meses
    atrás, así que se usa la que corresponda a cada fecha.

Guarda el conteo diario por municipio (liviano, para la línea de tiempo) y los
puntos individuales de los últimos 30 días (para dibujarlos en el mapa).

La clave de FIRMS no se guarda en el repositorio: se lee de la variable de
entorno OBS_FIRMS_MAP_KEY o de website/config/fenomenos.local.php, igual que
hace el sitio.

Salida: website/data/fenomenos/historico_calor.json

Uso:
    export OBS_FIRMS_MAP_KEY=...        # o configurarla en el CMS
    python scripts/gen_historico_calor.py --anios 2
"""
import argparse
import csv
import io
import json
import os
import re
import ssl
import sys
import time
import urllib.request
from datetime import date, datetime, timedelta

import numpy as np

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
GEOJS = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')
DST = os.path.join(BASE, 'website', 'data', 'fenomenos', 'historico_calor.json')
CFG = os.path.join(BASE, 'website', 'config', 'fenomenos.local.php')

API = 'https://firms.modaps.eosdis.nasa.gov/api/area/csv'
PORTAL = 'https://firms.modaps.eosdis.nasa.gov/'
CAJA = '-74.9,4.4,-71.8,7.2'     # Boyacá con margen; luego se recorta al polígono
TRAMO = 5                        # días por petición (con 7 o más el servicio falla)
CTX = ssl.create_default_context()
CAB = {'User-Agent': 'RedObservatoriosBoyaca/1.0 (observatorios.boyaca.gov.co)'}

# Rangos que cubre cada fuente, según el propio servicio.
FUENTES = [
    ('VIIRS_SNPP_SP', date(2012, 1, 20), date(2026, 6, 30)),
    ('VIIRS_NOAA20_NRT', date(2026, 7, 1), date(2100, 1, 1)),
]


def clave_firms():
    k = (os.environ.get('OBS_FIRMS_MAP_KEY') or '').strip()
    if k:
        return k
    if os.path.isfile(CFG):
        txt = io.open(CFG, encoding='utf-8').read()
        m = re.search(r"'firms_map_key'\s*=>\s*'([^']+)'", txt)
        if m:
            return m.group(1).strip()
    raise SystemExit(
        'Falta la clave de FIRMS. Expórtala en OBS_FIRMS_MAP_KEY o guárdala '
        'en el CMS (Fenómenos → Configuración).')


def fuente_para(f):
    for nombre, ini, fin in FUENTES:
        if ini <= f <= fin:
            return nombre
    return None


def municipios():
    txt = io.open(GEOJS, encoding='utf-8').read()
    geo = json.loads(txt[txt.index('{'):txt.rindex('}') + 1])
    out = []
    for f in geo['features']:
        g = f['geometry']
        partes = g['coordinates'] if g['type'] == 'MultiPolygon' else [g['coordinates']]
        anillos = [np.asarray(p[0], dtype=float) for p in partes if p]
        caja = np.vstack(anillos)
        out.append({'dane': f['properties']['id'], 'nombre': f['properties']['name'],
                    'anillos': anillos,
                    'caja': (caja[:, 0].min(), caja[:, 0].max(),
                             caja[:, 1].min(), caja[:, 1].max())})
    return out


def dentro(anillos, lon, lat):
    for a in anillos:
        x, y = a[:, 0], a[:, 1]
        xj, yj = np.roll(x, 1), np.roll(y, 1)
        cruza = ((y > lat) != (yj > lat)) & \
                (lon < (xj - x) * (lat - y) / np.where(yj == y, 1e-12, yj - y) + x)
        if bool(cruza.sum() % 2):
            return True
    return False


def ubicar(muni, lon, lat):
    for m in muni:
        x0, x1, y0, y1 = m['caja']
        if x0 <= lon <= x1 and y0 <= lat <= y1 and dentro(m['anillos'], lon, lat):
            return m['dane']
    return None


def pedir(key, fuente, desde):
    url = f'{API}/{key}/{fuente}/{CAJA}/{TRAMO}/{desde}'
    r = urllib.request.Request(url, headers=CAB)
    txt = urllib.request.urlopen(r, timeout=180, context=CTX).read().decode('utf-8', 'ignore')
    if not txt.strip() or txt.lstrip().startswith('<'):
        return []
    return list(csv.DictReader(io.StringIO(txt)))


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--anios', type=float, default=2.0)
    args = ap.parse_args()
    key = clave_firms()

    hist = {'dias': {}, 'puntos': []}
    if os.path.isfile(DST):
        viejo = json.load(open(DST, encoding='utf-8'))
        hist['dias'] = viejo.get('dias', {})

    muni = municipios()
    hoy = date.today()
    inicio = hoy - timedelta(days=int(args.anios * 365))

    # Solo se piden los tramos que tengan algún día sin registrar. El último
    # tramo siempre se vuelve a pedir, porque el día en curso está incompleto.
    tramos = []
    f = inicio
    while f <= hoy:
        dias = [(f + timedelta(days=i)).isoformat()
                for i in range(TRAMO) if f + timedelta(days=i) <= hoy]
        reciente = (hoy - f).days < 10
        if reciente or any(d not in hist['dias'] for d in dias):
            tramos.append(f)
        f += timedelta(days=TRAMO)

    print(f'Focos de calor · tramos por consultar: {len(tramos)}'
          f'  (desde {inicio} hasta {hoy})')
    total = 0
    for k, f in enumerate(tramos, 1):
        fuente = fuente_para(f)
        if not fuente:
            continue
        try:
            filas = pedir(key, fuente, f.isoformat())
        except Exception as e:
            print(f'   [{k}/{len(tramos)}] {f}  ERROR {type(e).__name__}: {str(e)[:60]}')
            continue
        # Se inicializan los días del tramo aunque vengan sin focos: un día sin
        # detecciones es información, no un vacío.
        for i in range(TRAMO):
            d = (f + timedelta(days=i))
            if d <= hoy:
                hist['dias'].setdefault(d.isoformat(), {})
        n = 0
        for r in filas:
            try:
                lat, lon = float(r['latitude']), float(r['longitude'])
            except (KeyError, ValueError):
                continue
            dane = ubicar(muni, lon, lat)
            if not dane:
                continue           # cayó en un departamento vecino
            dia = r.get('acq_date', '')
            if not dia:
                continue
            hist['dias'].setdefault(dia, {})
            hist['dias'][dia][dane] = hist['dias'][dia].get(dane, 0) + 1
            n += 1
            if (hoy - date.fromisoformat(dia)).days <= 30:
                hist['puntos'].append({
                    'fecha': dia, 'lat': round(lat, 5), 'lon': round(lon, 5),
                    'frp': round(float(r.get('frp') or 0), 1),
                    'dane': dane,
                })
        total += n
        if k % 10 == 0 or n:
            print(f'   [{k}/{len(tramos)}] {f}  {fuente:17} {n} focos en Boyacá')
        time.sleep(0.4)    # se espacian las peticiones para no saturar el servicio

    dias = dict(sorted(hist['dias'].items()))
    salida = {
        'fuente': 'NASA FIRMS · VIIRS (archivo procesado y tiempo casi real)',
        'url': PORTAL,
        'generado': str(date.today()),
        'desde': min(dias) if dias else None,
        'hasta': max(dias) if dias else None,
        'dias': dias,
        'puntos': sorted(hist['puntos'], key=lambda p: p['fecha']),
    }
    os.makedirs(os.path.dirname(DST), exist_ok=True)
    with open(DST, 'w', encoding='utf-8', newline='\n') as fh:
        json.dump(salida, fh, ensure_ascii=False, separators=(',', ':'))

    con = sum(1 for d in dias.values() if d)
    focos = sum(sum(d.values()) for d in dias.values())
    print(f'\ndías registrados: {len(dias)}   con al menos un foco: {con}')
    print(f'focos dentro de Boyacá: {focos}   (traídos en esta corrida: {total})')
    if dias:
        peor = sorted(dias.items(), key=lambda x: -sum(x[1].values()))[:5]
        print('días con más focos:')
        for d, m in peor:
            print(f'   {d}  {sum(m.values())} focos en {len(m)} municipios')
    print(f'-> {os.path.relpath(DST, BASE)}  ({os.path.getsize(DST) / 1024:.0f} KB)')


if __name__ == '__main__':
    main()
