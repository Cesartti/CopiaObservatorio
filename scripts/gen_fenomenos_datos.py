# -*- coding: utf-8 -*-
"""
Prepara los datos del mapa de novedades de la pestaña "Fenómenos en Boyacá".

Fuentes:
  1. database/seeds/BD DX OBS AMBIENTAL.xlsx, hoja "EMERGENCIAS AMB"
     (UNGRD, 2020-2025: evento, municipio, afectaciones y hectáreas).
  2. datos.gov.co · xsag-hp7j "Emergencias atendidas y reportadas" de la
     Gobernación de Boyacá (2019-2022, incluye quién atendió: bomberos,
     defensa civil, etc.). Se descarga si hay red; si no, se omite.
  3. datos.gov.co · ryr5-rs2a "Corpoboyacá, reporte de incendios de la
     cobertura vegetal" (2021).

Salida: website/data/fenomenos/emergencias.json
  - centroide de cada municipio (calculado del GeoJSON del portal) para
    ubicar los puntos del mapa de calor;
  - conteo por municipio, año y tipo de evento;
  - clasificación de cada tipo de evento según el fenómeno con el que se
    asocia (El Niño = déficit de lluvia; La Niña = exceso).
"""
import json, os, re, sys, unicodedata, collections, urllib.request

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DX = os.path.join(BASE, 'database', 'seeds', 'BD DX OBS AMBIENTAL.xlsx')
GEO = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')
OUT_DIR = os.path.join(BASE, 'website', 'data', 'fenomenos')
OUT = os.path.join(OUT_DIR, 'emergencias.json')
CACHE = os.path.join(BASE, 'reportes', 'ambiental_2026', 'cache_datos_gov')

SIN_RED = '--sin-red' in sys.argv

def norm(s):
    return unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower().strip()

# ---------------------------------------------------------------- municipios
geo_txt = open(GEO, encoding='utf-8').read()
inicio = geo_txt.index('{')
GJ = json.loads(geo_txt[inicio:geo_txt.rindex('}') + 1])

def centroide(geom):
    """Centroide del anillo exterior más grande (suficiente para ubicar un punto)."""
    anillos = []
    if geom['type'] == 'Polygon':
        anillos = [geom['coordinates'][0]]
    else:
        anillos = [p[0] for p in geom['coordinates']]
    mejor, area_max = None, -1
    for anillo in anillos:
        a = cx = cy = 0.0
        for i in range(len(anillo) - 1):
            x1, y1 = anillo[i][0], anillo[i][1]
            x2, y2 = anillo[i + 1][0], anillo[i + 1][1]
            cruz = x1 * y2 - x2 * y1
            a += cruz; cx += (x1 + x2) * cruz; cy += (y1 + y2) * cruz
        a *= 0.5
        if abs(a) > area_max and a != 0:
            area_max = abs(a); mejor = (cx / (6 * a), cy / (6 * a))
    return mejor

MUN = {}          # dane -> {nombre, lat, lon}
POR_NOMBRE = {}   # nombre normalizado -> dane
for f in GJ['features']:
    p = f['properties']; dane = str(p['id']); nombre = str(p['name']).title()
    c = centroide(f['geometry'])
    if not c:
        continue
    MUN[dane] = {'nombre': nombre, 'lon': round(c[0], 5), 'lat': round(c[1], 5)}
    POR_NOMBRE[norm(nombre)] = dane

ALIAS = {'guican': 'guican de la sierra', 'cocuy': 'el cocuy', 'espino': 'el espino',
         'villa de leiva': 'villa de leyva', 'labranzagrande': 'labranza grande',
         'san pablo borbur': 'san pablo de borbur', 'sativa norte': 'sativanorte',
         'sativa sur': 'sativasur', 'ventquemada': 'ventaquemada',
         'sotaa': 'sota', 'nirafloresarrow': 'miraflores', 'el cocouy': 'el cocuy'}
SIN_MATCH = collections.Counter()

def dane_de(nombre):
    n = norm(nombre)
    n = ALIAS.get(n, n)
    if n in POR_NOMBRE:
        return POR_NOMBRE[n]
    for k in POR_NOMBRE:
        if k.replace(' de ', ' ') == n.replace(' de ', ' '):
            return POR_NOMBRE[k]
    cand = [k for k in POR_NOMBRE if k.startswith(n) or n.startswith(k)]
    if len(cand) == 1:
        return POR_NOMBRE[cand[0]]
    SIN_MATCH[nombre] += 1
    return None

# ------------------------------------------------- clasificación por fenómeno
# Solo se mapean eventos ligados a la variabilidad climática. El Niño trae
# déficit de lluvia y más calor (incendios, desabastecimiento); La Niña trae
# exceso de lluvia (inundaciones, movimientos en masa, vendavales).
REGLAS = [
    ('nino', 'Incendio de cobertura vegetal', ('incendio forestal', 'incendio de cobertura vegetal',
                                               'incendio cobertura vegetal', 'incendio de la cobertura vegetal')),
    ('nino', 'Desabastecimiento de agua', ('desabastecimiento', 'racionamiento')),
    ('nino', 'Sequía', ('sequia',)),
    ('nino', 'Helada', ('helada',)),
    ('nina', 'Inundación', ('inundacion',)),
    ('nina', 'Movimiento en masa', ('movimiento en masa', 'deslizamiento')),
    ('nina', 'Vendaval', ('vendaval', 'caida de arbol', 'caída de árbol')),
    ('nina', 'Creciente súbita o avenida torrencial', ('creciente', 'avenida torrencial')),
    ('nina', 'Lluvia torrencial o temporal', ('lluvia torrencial', 'temporal', 'temporada invernal')),
    ('nina', 'Granizada', ('granizada',)),
]

def clasifica(evento):
    """Devuelve (fenomeno, etiqueta) o None si el evento no es climático."""
    e = norm(evento)
    for fen, etiqueta, claves in REGLAS:
        if any(k in e for k in claves):
            return fen, etiqueta
    return None

# ---------------------------------------------------------------- agregación
registros = []   # (dane, anio, evento, fenomeno, hectareas, fuente)

import pandas as pd
df = pd.read_excel(DX, sheet_name='EMERGENCIAS AMB')
df.columns = [str(c).strip() for c in df.columns]
df['AÑO'] = pd.to_numeric(df['AÑO'], errors='coerce')
df['HECTAREAS'] = pd.to_numeric(df['HECTAREAS'], errors='coerce').fillna(0)
for _, r in df.dropna(subset=['AÑO']).iterrows():
    d = dane_de(r['MUNICIPIO'])
    if not d:
        continue
    cls = clasifica(r['EVENTO'])
    if not cls:
        continue
    registros.append((d, int(r['AÑO']), cls[1], cls[0], float(r['HECTAREAS']), 'UNGRD'))
print('UNGRD (Excel):', len(registros), 'eventos')

def baja(recurso, limite=50000):
    """Descarga un recurso de datos.gov.co con caché en disco."""
    os.makedirs(CACHE, exist_ok=True)
    destino = os.path.join(CACHE, recurso + '.json')
    if os.path.isfile(destino) and os.path.getsize(destino) > 100:
        return json.load(open(destino, encoding='utf-8'))
    if SIN_RED:
        return []
    url = f'https://www.datos.gov.co/resource/{recurso}.json?$limit={limite}'
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'ObservatoriosBoyaca/1.0'})
        with urllib.request.urlopen(req, timeout=90) as resp:
            datos = json.loads(resp.read().decode('utf-8'))
        json.dump(datos, open(destino, 'w', encoding='utf-8'), ensure_ascii=False)
        return datos
    except Exception as e:
        print('  aviso: no se pudo descargar', recurso, '->', type(e).__name__, str(e)[:70])
        return []

# 2) Emergencias atendidas de la Gobernación (2019-2022)
gob = baja('xsag-hp7j')
n = 0
for r in gob:
    d = dane_de(r.get('municipio', ''))
    fecha = str(r.get('fecha_evento', ''))[:4]
    if not d or not fecha.isdigit():
        continue
    cls = clasifica(r.get('tipo_de_evento', ''))
    if not cls:
        continue
    registros.append((d, int(fecha), cls[1], cls[0], 0.0, 'CDGRD Boyacá'))
    n += 1
print('CDGRD Boyacá (datos.gov.co):', n, 'eventos')

# 3) Incendios de cobertura vegetal de Corpoboyacá (2021)
corpo = baja('ryr5-rs2a')
n = 0
for r in corpo:
    d = dane_de(r.get('municipio', ''))
    fecha = str(r.get('fecha_de_inicio', ''))[:4]
    if not d or not fecha.isdigit():
        continue
    ha = 0.0
    try:
        ha = float(r.get('rea_total_afectada_ha') or 0)
    except Exception:
        pass
    registros.append((d, int(fecha), 'Incendio de cobertura vegetal', 'nino', ha, 'CORPOBOYACÁ'))
    n += 1
print('CORPOBOYACÁ (datos.gov.co):', n, 'eventos')

# ---------------------------------------------------------------- salida
por_mun = collections.defaultdict(lambda: {'total': 0, 'ha': 0.0,
                                           'anios': collections.Counter(),
                                           'tipos': collections.Counter(),
                                           'fenomenos': collections.Counter()})
for d, anio, ev, fen, ha, fuente in registros:
    m = por_mun[d]
    m['total'] += 1; m['ha'] += ha
    m['anios'][anio] += 1; m['tipos'][ev] += 1; m['fenomenos'][fen] += 1

municipios = {}
for d, m in por_mun.items():
    municipios[d] = {
        'nombre': MUN[d]['nombre'], 'lat': MUN[d]['lat'], 'lon': MUN[d]['lon'],
        'total': m['total'], 'ha': round(m['ha'], 1),
        'anios': {str(k): v for k, v in sorted(m['anios'].items())},
        'tipos': dict(m['tipos'].most_common(8)),
        'fenomenos': dict(m['fenomenos']),
    }

tipos_glob = collections.Counter(ev for _, _, ev, _, _, _ in registros)
anios_glob = collections.Counter(a for _, a, _, _, _, _ in registros)
# Serie anual separada por fenómeno, para las gráficas de cada subpestaña.
anios_fen = {'nino': collections.Counter(), 'nina': collections.Counter()}
for _, anio, _, fen, _, _ in registros:
    if fen in anios_fen:
        anios_fen[fen][anio] += 1
# Fuente de cada registro, para poder citar cuánto aporta cada entidad.
fuentes_glob = collections.Counter(f for _, _, _, _, _, f in registros)

salida = {
    'generado': __import__('datetime').date.today().isoformat(),
    'fuentes': [
        {'nombre': 'Unidad Nacional para la Gestión del Riesgo de Desastres (UNGRD)',
         'detalle': 'Consolidado de atención de emergencias, 2020-2025',
         'url': 'https://portal.gestiondelriesgo.gov.co/Paginas/Consolidado-Atencion-de-Emergencias.aspx'},
        {'nombre': 'Consejo Departamental de Gestión del Riesgo de Desastres de Boyacá',
         'detalle': 'Emergencias atendidas y reportadas, 2019-2022',
         'url': 'https://www.datos.gov.co/d/xsag-hp7j'},
        {'nombre': 'CORPOBOYACÁ',
         'detalle': 'Reporte de incendios de la cobertura vegetal, 2021',
         'url': 'https://www.datos.gov.co/d/ryr5-rs2a'},
    ],
    'totales': {'eventos': len(registros),
                'municipios': len(municipios),
                'anios': {str(k): v for k, v in sorted(anios_glob.items())},
                'anios_fenomeno': {f: {str(k): v for k, v in sorted(c.items())}
                                   for f, c in anios_fen.items()},
                'por_fuente': dict(fuentes_glob.most_common()),
                'tipos': dict(tipos_glob.most_common(20))},
    'municipios': municipios,
}
os.makedirs(OUT_DIR, exist_ok=True)
json.dump(salida, open(OUT, 'w', encoding='utf-8'), ensure_ascii=False, separators=(',', ':'))

print('\n->', os.path.relpath(OUT, BASE), round(os.path.getsize(OUT) / 1024), 'KB')
print('   eventos:', len(registros), '| municipios con datos:', len(municipios))
print('   años:', dict(sorted(anios_glob.items())))
print('   tipos más frecuentes:', tipos_glob.most_common(8))
if SIN_MATCH:
    print('   sin código DANE:', SIN_MATCH.most_common(10))
