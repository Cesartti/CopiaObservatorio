# -*- coding: utf-8 -*-
"""
Genera la migración del directorio de bomberos de Boyacá desde el archivo
«Directorio de Bomberos Boyacá.xlsx» remitido por la Secretaría.

 - Hoja «Cuerpos de Bomberos (51)»: las estaciones con dirección y teléfonos.
 - Hoja «Análisis de Asignación»: para cada municipio, si tiene estación propia
   y a qué cuerpos acude en orden de respuesta (primero, segundo y tercero).

Las estaciones se ubican en el centroide de su municipio base, porque el
archivo no trae coordenadas. La migración es idempotente.
"""
import os, re, json, unicodedata
import pandas as pd

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
XLS = r'C:\Users\cesar\Downloads\Directorio de Bomberos Boyacá.xlsx'
GEO = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')
EMG = os.path.join(BASE, 'website', 'data', 'fenomenos', 'emergencias.json')
MIG = os.path.join(BASE, 'database', 'migrations', '029_directorio_bomberos.sql')
FUENTE = ('Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá '
          '(bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026')

def norm(s):
    return unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower().strip()

# ---------------------------------------------------------------- municipios
geo = open(GEO, encoding='utf-8').read()
OFICIAL = {}
for dane, nombre in re.findall(r'"id":"(\d{5})",\s*"name":\s*"([^"]+)"', geo):
    OFICIAL[norm(nombre)] = (dane, nombre.title())
ALIAS = {'labranzagrande': 'labranza grande', 'san luis de gasceno': 'san luis de gaceno',
         'guican': 'guican de la sierra', 'villa de leiva': 'villa de leyva',
         'campo hermoso': 'campohermoso', 'miraflorez': 'miraflores', 'belen': 'belen',
         'chiquiza': 'chiquiza', 'busbanza': 'busbanza', 'tunungua': 'tununguaito'}

def municipio(nombre):
    n = ALIAS.get(norm(nombre), norm(nombre))
    if n in OFICIAL:
        return OFICIAL[n]
    cand = [k for k in OFICIAL if k.startswith(n) or n.startswith(k)]
    if len(cand) == 1:
        return OFICIAL[cand[0]]
    return (None, str(nombre).strip())

# centroides ya calculados para el mapa de novedades
CENTRO = {}
if os.path.isfile(EMG):
    for dane, m in json.load(open(EMG, encoding='utf-8')).get('municipios', {}).items():
        CENTRO[dane] = (m.get('lat'), m.get('lon'))

# ---------------------------------------------------------------- estaciones
est = pd.read_excel(XLS, sheet_name='Cuerpos de Bomberos (51)', header=4, dtype=str).fillna('')
est = est.loc[:, ~est.columns.str.startswith('Unnamed')]
est = est[est['Cuerpo de Bomberos'].str.strip() != '']

asig = pd.read_excel(XLS, sheet_name='Análisis de Asignación', header=5, dtype=str).fillna('')
asig = asig.loc[:, ~asig.columns.str.startswith('Unnamed')]
asig = asig[asig['Municipio'].str.strip() != '']
PROV = {norm(r['Municipio']): r['Provincia'].strip() for _, r in asig.iterrows()}

TIPO = {'bomberos voluntarios': 'Cuerpo de Bomberos Voluntarios',
        'bomberos oficiales': 'Cuerpo de Bomberos Oficiales',
        'bomberos aeronauticos': 'Cuerpo de Bomberos Aeronáuticos'}

def esc(v):
    v = str(v).strip()
    return 'NULL' if v == '' else "'" + v.replace('\\', '\\\\').replace("'", "''") + "'"

def telefonos(v):
    """Normaliza «310... - 312...» a una lista separada por ' / '."""
    partes = [p.strip() for p in re.split(r'[-–/;,]| y ', str(v)) if p.strip()]
    fusion, buf = [], ''
    for p in partes:                      # une los trozos de un mismo número partido
        d = re.sub(r'\D', '', p)
        if len(d) >= 7:
            fusion.append(d)
        else:
            buf += d
            if len(buf) >= 7:
                fusion.append(buf); buf = ''
    vistos, salida = set(), []
    for t in fusion:
        if t not in vistos:
            vistos.add(t); salida.append(t)
    return ' / '.join(salida)

filas_est, sin_dane = [], []
for _, r in est.iterrows():
    dane, nombre_of = municipio(r['Municipio Base'])
    if not dane:
        sin_dane.append(r['Municipio Base'])
    lat, lon = CENTRO.get(dane, (None, None))
    filas_est.append({
        'nombre': 'Cuerpo de Bomberos de ' + str(r['Cuerpo de Bomberos']).strip(),
        'municipio': nombre_of,
        'municipio_dane': dane,
        'provincia': PROV.get(norm(r['Municipio Base']), ''),
        'telefono': telefonos(r['Teléfono(s) de Emergencia']),
        'direccion': str(r['Dirección Sede / Ubicación']).strip(),
        'tipo': TIPO.get(norm(r['Tipo de Cuerpo']), str(r['Tipo de Cuerpo']).strip().title()),
        'lat': lat, 'lon': lon,
    })

# ---------------------------------------------------------------- cobertura
COLS = list(asig.columns)
cuerpos = [c for c in COLS if c.strip().lower().startswith('cuerpo de bomberos')]
tels = [c for c in COLS if c.strip().lower().startswith('teléfono contacto')]
filas_cob = []
for _, r in asig.iterrows():
    dane, nombre_of = municipio(r['Municipio'])
    propia = norm(r['¿Estación Propia?']).startswith('s')
    for orden, (cc, tc) in enumerate(zip(cuerpos, tels), 1):
        cuerpo = str(r[cc]).strip()
        if cuerpo == '':
            continue
        filas_cob.append({
            'municipio': nombre_of, 'municipio_dane': dane,
            'provincia': str(r['Provincia']).strip(),
            'estacion_propia': 1 if propia else 0,
            'orden': orden, 'cuerpo': cuerpo,
            'telefono': telefonos(r[tc]),
        })

# ---------------------------------------------------------------- migración
out = [
    '-- 029: Directorio de bomberos de Boyacá (estaciones y municipios que atienden).',
    '-- Fuente: archivo «Directorio de Bomberos Boyacá.xlsx» remitido por la Secretaría',
    '-- de Planeación, construido con el Directorio Nacional de Bomberos y el portal',
    '-- bomberos.boyaca.gov.co. Idempotente: se puede volver a aplicar sin duplicar.',
    'SET NAMES utf8mb4;',
    '',
    '-- Municipios y el orden en que acuden a cada cuerpo de bomberos.',
    '''CREATE TABLE IF NOT EXISTS env_bomberos_cobertura (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    municipio       VARCHAR(120) NOT NULL,
    municipio_dane  VARCHAR(5)   NULL,
    provincia       VARCHAR(80)  NULL,
    estacion_propia TINYINT(1)   NOT NULL DEFAULT 0,
    orden           TINYINT      NOT NULL,
    cuerpo          VARCHAR(180) NOT NULL,
    telefono        VARCHAR(160) NULL,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_muni_orden (municipio, orden),
    INDEX idx_prov (provincia),
    INDEX idx_dane (municipio_dane)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;''',
    '',
    '-- Se reemplaza el contenido anterior (dos líneas de referencia cargadas en la 026).',
    "DELETE FROM env_bomberos WHERE fuente IS NULL OR fuente <> " + esc(FUENTE) + ";",
    '',
]

for f in filas_est:
    out.append(
        'INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, '
        'direccion, tipo, lat, lon, fuente, verificado, activo)\n'
        'VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 1, 1)\n'
        'ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), '
        'telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), '
        'lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;'
        % (esc(f['nombre']), esc(f['municipio']), esc(f['municipio_dane']), esc(f['provincia']),
           esc(f['telefono']), esc(f['direccion']), esc(f['tipo']),
           f['lat'] if f['lat'] else 'NULL', f['lon'] if f['lon'] else 'NULL', esc(FUENTE)))

out.append('')
for f in filas_cob:
    out.append(
        'INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)\n'
        'VALUES (%s, %s, %s, %d, %d, %s, %s)\n'
        'ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), '
        'estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);'
        % (esc(f['municipio']), esc(f['municipio_dane']), esc(f['provincia']),
           f['estacion_propia'], f['orden'], esc(f['cuerpo']), esc(f['telefono'])))

open(MIG, 'w', encoding='utf-8', newline='\n').write('\n'.join(out) + '\n')
print('->', os.path.relpath(MIG, BASE))
print('   estaciones:', len(filas_est), '| filas de cobertura:', len(filas_cob))
print('   municipios cubiertos:', len({f['municipio'] for f in filas_cob}))
print('   con estación propia:', len({f['municipio'] for f in filas_cob if f['estacion_propia']}))
print('   estaciones sin código DANE:', sin_dane or 'ninguna')
print('   sin coordenadas:', [f['nombre'] for f in filas_est if not f['lat']] or 'ninguna')
