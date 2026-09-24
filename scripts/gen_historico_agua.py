# -*- coding: utf-8 -*-
"""
Construye y mantiene el histórico del estado del agua en Boyacá.

Sirve para que el mapa no muestre solo la foto de hoy, sino la evolución:
cómo se fue secando o recuperando cada municipio y cómo subió o bajó el
embalse a lo largo del tiempo.

Dos series, cada una de su fuente:

  · VHI semanal por municipio, del producto de la NOAA. En vez de bajar el
    mosaico global de 32 MB por semana, se piden solo las filas de Boyacá
    (unos 570 KB, ver scripts/vhp_remoto.py), lo que hace viable reconstruir
    años de historia.

  · Volumen útil diario del embalse La Esmeralda, de la API de XM, que admite
    consultas de un mes por vez.

El script es incremental: lee lo que ya existe y solo pide lo que falta, así
que se puede programar sin repetir descargas.

Salida: website/data/fenomenos/historico_agua.json

Uso:
    python scripts/gen_historico_agua.py              # actualiza lo que falte
    python scripts/gen_historico_agua.py --anios 3    # reconstruye 3 años
"""
import argparse
import json
import os
import re
import ssl
import sys
import time
import urllib.request
from datetime import date, datetime, timedelta

import numpy as np

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from vhp_remoto import ventana  # noqa: E402

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
GEOJS = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')
DST = os.path.join(BASE, 'website', 'data', 'fenomenos', 'historico_agua.json')

DIR_NOAA = ('https://www.star.nesdis.noaa.gov/pub/corp/scsb/wguo/data/'
            'Blended_VH_4km/geo_TIFF/')
PORTAL_NOAA = 'https://www.star.nesdis.noaa.gov/smcd/emb/vci/VH/index.php'
API_XM = 'https://servapibi.xm.com.co/daily'
PORTAL_XM = 'https://www.xm.com.co/'

LON0, LAT0, PASO = -180.0, 75.024, 0.036
NODATO = -9999.0
CTX = ssl.create_default_context()
CAB = {'User-Agent': 'RedObservatoriosBoyaca/1.0 (observatorios.boyaca.gov.co)'}


# --------------------------------------------------------------- municipios
def municipios():
    txt = open(GEOJS, encoding='utf-8').read()
    geo = json.loads(txt[txt.index('{'):txt.rindex('}') + 1])
    out = []
    for f in geo['features']:
        g = f['geometry']
        partes = g['coordinates'] if g['type'] == 'MultiPolygon' else [g['coordinates']]
        anillos = [np.asarray(p[0], dtype=float) for p in partes if p]
        out.append({'dane': f['properties']['id'], 'nombre': f['properties']['name'],
                    'anillos': anillos})
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


def celdas_por_municipio(muni, f0, f1, c0, c1):
    """Qué celdas de la ventana caen dentro de cada municipio (se calcula una vez)."""
    lons = LON0 + (np.arange(c0, c1) + 0.5) * PASO
    lats = LAT0 - (np.arange(f0, f1) + 0.5) * PASO
    mapa = {}
    for m in muni:
        a = np.vstack(m['anillos'])
        imin = max(int(np.searchsorted(lons, a[:, 0].min())) - 1, 0)
        imax = min(int(np.searchsorted(lons, a[:, 0].max())) + 1, len(lons) - 1)
        jmin = max(int(np.searchsorted(-lats, -a[:, 1].max())) - 1, 0)
        jmax = min(int(np.searchsorted(-lats, -a[:, 1].min())) + 1, len(lats) - 1)
        celdas = [(j, i)
                  for j in range(jmin, jmax + 1)
                  for i in range(imin, imax + 1)
                  if dentro(m['anillos'], lons[i], lats[j])]
        if not celdas:  # municipio más pequeño que la celda de 4 km
            i = int(np.clip(np.searchsorted(lons, a[:, 0].mean()), 0, len(lons) - 1))
            j = int(np.clip(np.searchsorted(-lats, -a[:, 1].mean()), 0, len(lats) - 1))
            celdas = [(j, i)]
        mapa[m['dane']] = celdas
    return mapa


# --------------------------------------------------------------------- NOAA
def archivos_noaa():
    r = urllib.request.Request(DIR_NOAA, headers=CAB)
    html = urllib.request.urlopen(r, timeout=120, context=CTX).read().decode('utf-8', 'ignore')
    out = []
    for nombre in sorted(set(re.findall(r'href="([^"]*\.VH\.VHI\.tif)"', html))):
        m = re.search(r'\.P(\d{4})(\d{3})\.', nombre)
        if not m:
            continue
        anio, semana = int(m.group(1)), int(m.group(2))
        cierre = datetime(anio, 1, 1) + timedelta(days=semana * 7 - 1)
        out.append((cierre.strftime('%Y-%m-%d'), nombre))
    return out


# ----------------------------------------------------------------------- XM
def embalse_mes(ini, fin):
    cuerpo = json.dumps({'MetricId': 'VoluUtilDiarEner', 'StartDate': str(ini),
                         'EndDate': str(fin), 'Entity': 'Embalse'}).encode()
    cab = dict(CAB)
    cab['Content-Type'] = 'application/json'
    r = urllib.request.Request(API_XM, data=cuerpo, headers=cab)
    j = json.loads(urllib.request.urlopen(r, timeout=180, context=CTX).read())

    cap = {}
    cuerpo2 = json.dumps({'MetricId': 'CapaUtilDiarEner', 'StartDate': str(ini),
                          'EndDate': str(fin), 'Entity': 'Embalse'}).encode()
    r2 = urllib.request.Request(API_XM, data=cuerpo2, headers=cab)
    try:
        j2 = json.loads(urllib.request.urlopen(r2, timeout=180, context=CTX).read())
        for b in j2.get('Items', []):
            for e in b.get('DailyEntities', []):
                if e.get('Name') == 'ESMERALDA':
                    cap[b['Date'][:10]] = float(e['Value'])
    except Exception:
        pass

    out = {}
    for b in j.get('Items', []):
        for e in b.get('DailyEntities', []):
            if e.get('Name') != 'ESMERALDA':
                continue
            f = b['Date'][:10]
            c = cap.get(f)
            if c:
                out[f] = round(100.0 * float(e['Value']) / c, 1)
    return out


# --------------------------------------------------------------------- main
def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--anios', type=float, default=2.0,
                    help='años de historia a reconstruir (por defecto 2)')
    args = ap.parse_args()
    desde = (date.today() - timedelta(days=int(args.anios * 365))).isoformat()

    hist = {}
    if os.path.isfile(DST):
        hist = json.load(open(DST, encoding='utf-8'))

    vhi = hist.get('vhi', {'fechas': [], 'municipios': {}})
    emb = hist.get('embalse', {'fechas': [], 'valores': []})

    # ---------------------------------------------------------------- VHI
    muni = municipios()
    todos = np.vstack([a for m in muni for a in m['anillos']])
    c0 = max(int((todos[:, 0].min() - LON0) / PASO) - 1, 0)
    c1 = int((todos[:, 0].max() - LON0) / PASO) + 2
    f0 = max(int((LAT0 - todos[:, 1].max()) / PASO) - 1, 0)
    f1 = int((LAT0 - todos[:, 1].min()) / PASO) + 2

    disponibles = [(f, n) for f, n in archivos_noaa() if f >= desde]
    faltan = [(f, n) for f, n in disponibles if f not in vhi['fechas']]
    print(f'VHI · semanas disponibles desde {desde}: {len(disponibles)}'
          f'   ya guardadas: {len(disponibles) - len(faltan)}   por traer: {len(faltan)}')

    if faltan:
        print('   preparando el cruce con los municipios…')
        celdas = celdas_por_municipio(muni, f0, f1, c0, c1)
        bajado = 0
        for k, (fecha, nombre) in enumerate(faltan, 1):
            try:
                t0 = time.time()
                win, b = ventana(DIR_NOAA + nombre, f0, f1)
                bajado += b
                win = win[:, c0:c1]
                vhi['fechas'].append(fecha)
                for dane, cs in celdas.items():
                    vals = [float(win[j, i]) for j, i in cs
                            if 0 <= win[j, i] <= 100 and win[j, i] != NODATO]
                    serie = vhi['municipios'].setdefault(dane, [])
                    # Se rellena con nulos si el municipio entró tarde a la serie.
                    while len(serie) < len(vhi['fechas']) - 1:
                        serie.append(None)
                    serie.append(round(float(np.mean(vals)), 1) if vals else None)
                print(f'   [{k}/{len(faltan)}] {fecha}  {b / 1024:.0f} KB  {time.time() - t0:.1f}s')
            except Exception as e:
                print(f'   [{k}/{len(faltan)}] {fecha}  ERROR {type(e).__name__}: {str(e)[:70]}')
        print(f'   descargado en total: {bajado / 1e6:.1f} MB')

        # Se ordenan las semanas por fecha, manteniendo alineadas todas las series.
        orden = sorted(range(len(vhi['fechas'])), key=lambda i: vhi['fechas'][i])
        vhi['fechas'] = [vhi['fechas'][i] for i in orden]
        for dane, serie in vhi['municipios'].items():
            while len(serie) < len(orden):
                serie.append(None)
            vhi['municipios'][dane] = [serie[i] for i in orden]

    # ------------------------------------------------------------ embalse
    ya = set(emb['fechas'])
    pares = dict(zip(emb['fechas'], emb['valores']))
    ini = date.fromisoformat(desde).replace(day=1)
    hoy = date.today()
    meses = []
    while ini <= hoy:
        fin = (ini.replace(day=28) + timedelta(days=4)).replace(day=1) - timedelta(days=1)
        meses.append((ini, min(fin, hoy)))
        ini = fin + timedelta(days=1)
    # Solo se reconsultan los meses incompletos (el actual y los que falten).
    pendientes = [(a, b) for a, b in meses
                  if sum(1 for d in range((b - a).days + 1)
                         if (a + timedelta(days=d)).isoformat() in ya) < (b - a).days + 1]
    print(f'\nEmbalse · meses a consultar: {len(pendientes)} de {len(meses)}')
    for k, (a, b) in enumerate(pendientes, 1):
        try:
            datos = embalse_mes(a, b)
            pares.update(datos)
            print(f'   [{k}/{len(pendientes)}] {a:%Y-%m}  {len(datos)} días')
        except Exception as e:
            print(f'   [{k}/{len(pendientes)}] {a:%Y-%m}  ERROR {type(e).__name__}: {str(e)[:60]}')
    emb['fechas'] = sorted(pares)
    emb['valores'] = [pares[f] for f in emb['fechas']]

    salida = {
        'generado': str(date.today()),
        'vhi': {
            'fuente': 'NOAA STAR · Blended Vegetation Health Product (VHI), 4 km, semanal',
            'url': PORTAL_NOAA,
            'fechas': vhi['fechas'],
            'municipios': vhi['municipios'],
        },
        'embalse': {
            'fuente': 'XM · Operador del Mercado Eléctrico, volumen útil diario',
            'url': PORTAL_XM,
            'nombre': 'Embalse La Esmeralda (Chivor)',
            'fechas': emb['fechas'],
            'valores': emb['valores'],
        },
    }
    os.makedirs(os.path.dirname(DST), exist_ok=True)
    with open(DST, 'w', encoding='utf-8', newline='\n') as fh:
        json.dump(salida, fh, ensure_ascii=False, separators=(',', ':'))

    print(f'\nVHI: {len(vhi["fechas"])} semanas'
          f' ({vhi["fechas"][0] if vhi["fechas"] else "-"} a'
          f' {vhi["fechas"][-1] if vhi["fechas"] else "-"})'
          f' · {len(vhi["municipios"])} municipios')
    print(f'Embalse: {len(emb["fechas"])} días'
          f' ({emb["fechas"][0] if emb["fechas"] else "-"} a'
          f' {emb["fechas"][-1] if emb["fechas"] else "-"})')
    print(f'-> {os.path.relpath(DST, BASE)}  ({os.path.getsize(DST) / 1024:.0f} KB)')


if __name__ == '__main__':
    main()
