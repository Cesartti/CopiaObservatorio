# -*- coding: utf-8 -*-
"""
Auditoría estructural de todos los indicadores publicados.

Para cada carpeta website/indicador/NNNN/ vigente revisa:
  - que la ficha tenga título y categoría;
  - que cada N.info tenga su N.csv y viceversa;
  - que display.js declare tantas clases como gráficas hay;
  - que el CSV tenga encabezado y al menos una fila de datos;
  - que los mapas traigan la columna 'geo' con códigos DANE válidos;
  - que no queden restos de plantilla (%%), rutas de proveedores con clave,
    ni constructores inexistentes.

No modifica nada: solo reporta.
"""
import os, re, csv, json, glob, collections, unicodedata

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IND = os.path.join(BASE, 'website', 'indicador')
GEO = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')

OBS = {'1': 'Económico', '2': 'Social', '3': 'Ambiental', '4': 'CTeI', '5': 'Género'}

DANE = set(re.findall(r'"id":"(\d{5})"', open(GEO, encoding='utf-8').read()))


def clave(k):
    """Las fichas mezclan «Titulo» y «Título»: se comparan sin tildes."""
    k = unicodedata.normalize('NFKD', k).encode('ascii', 'ignore').decode()
    return k.strip().lower()


def leer_info(path):
    out = {}
    if os.path.isfile(path):
        for linea in open(path, encoding='utf-8', errors='ignore'):
            if ':' in linea:
                k, v = linea.split(':', 1)
                out[clave(k)] = v.strip()
    return out


def filas_csv(path):
    try:
        with open(path, encoding='utf-8-sig', errors='ignore', newline='') as fh:
            filas = [r for r in csv.reader(fh) if any((c or '').strip() for c in r)]
        return filas
    except Exception:
        return []


problemas = collections.defaultdict(list)
resumen = collections.Counter()
detalle = []

for carpeta in sorted(os.listdir(IND)):
    if not (carpeta.isdigit() and len(carpeta) == 4 and carpeta[0] in OBS):
        continue
    ruta = os.path.join(IND, carpeta)
    if not os.path.isdir(ruta):
        continue
    ficha = leer_info(os.path.join(ruta, 'indicador.info'))
    if ficha.get('retirado'):
        resumen[OBS[carpeta[0]] + ' · retirados'] += 1
        continue
    obs = OBS[carpeta[0]]
    resumen[obs + ' · vigentes'] += 1

    def falla(msg):
        problemas[obs].append((carpeta, msg))

    if not ficha.get('titulo'):
        falla('ficha sin Titulo')
    if not ficha.get('categoria'):
        falla('ficha sin Categoría')

    infos = sorted(glob.glob(os.path.join(ruta, '[0-9]*.info')),
                   key=lambda p: int(os.path.basename(p).split('.')[0]))
    infos = [p for p in infos if os.path.basename(p) != 'indicador.info']
    csvs = {os.path.basename(p) for p in glob.glob(os.path.join(ruta, '[0-9]*.csv'))}

    if not infos:
        falla('sin ninguna gráfica definida')
        continue

    # numeración consecutiva desde 1
    nums = [int(os.path.basename(p).split('.')[0]) for p in infos]
    if nums != list(range(1, len(nums) + 1)):
        falla('gráficas con numeración rota: ' + str(nums))

    mapas = 0
    for p in infos:
        n = os.path.basename(p).split('.')[0]
        meta = leer_info(p)
        if not meta.get('titulo'):
            falla(f'gráfica {n} sin Titulo')
        if f'{n}.csv' not in csvs:
            falla(f'gráfica {n} sin su archivo de datos')
            continue
        filas = filas_csv(os.path.join(ruta, f'{n}.csv'))
        if len(filas) < 2:
            falla(f'gráfica {n} con datos vacíos ({len(filas)} filas)')
            continue
        es_mapa = (meta.get('tipo', '').strip().lower() == 'mapa')
        if es_mapa:
            mapas += 1
            cab = [c.strip().lower() for c in filas[0]]
            if 'geo' not in cab:
                falla(f'mapa {n} sin columna geo')
            else:
                ig = cab.index('geo')
                codigos = {(f[ig] or '').strip() for f in filas[1:] if len(f) > ig}
                malos = {c for c in codigos if c and c not in DANE}
                if malos:
                    falla(f'mapa {n} con {len(malos)} códigos DANE inválidos')
    # sobrantes: CSV sin su info
    huerfanos = csvs - {os.path.basename(p).replace('.info', '.csv') for p in infos}
    if huerfanos:
        falla('archivos de datos sin gráfica: ' + ', '.join(sorted(huerfanos)))

    djs = os.path.join(ruta, 'display.js')
    if not os.path.isfile(djs):
        falla('sin display.js')
    else:
        src = open(djs, encoding='utf-8', errors='ignore').read()
        if '%%' in src:
            falla('display.js con restos de plantilla (%%)')
        if 'cartocdn' in src:
            falla('display.js con proveedor de mapas que exige clave')
        clases = len(re.findall(r'class\s+Chart\d+\s+extends', src))
        listadas = re.search(r'\[([^\]]*Chart[^\]]*)\]', src)
        enlista = len(re.findall(r'Chart\d+', listadas.group(1))) if listadas else 0
        if clases != len(infos):
            falla(f'display.js define {clases} gráficas y la ficha tiene {len(infos)}')
        elif enlista != clases:
            falla(f'display.js declara {clases} clases pero registra {enlista}')
        # Si hay gráficas que no son mapas, Display debe cargar algún paquete de
        # Google Charts (corechart, table, gauge…), no necesariamente corechart.
        if len(infos) - mapas > 0:
            # El paquete se pasa como lista (['corechart']) o como texto
            # ('corechart'); ambas formas las acepta el cargador de Google.
            pk = re.search(r"super\(\s*(\[[^\]]*\]|'[^']+'|\"[^\"]+\"|null)", src)
            if not pk or pk.group(1) == 'null':
                falla('display.js sin paquete de gráficas pese a tener gráficas')
    detalle.append((carpeta, obs, len(infos), mapas))

print('=' * 78)
print('RESUMEN')
for k in sorted(resumen):
    print(f'   {k:28} {resumen[k]}')
print(f'   {"TOTAL gráficas revisadas":28} {sum(d[2] for d in detalle)}')

print('\n' + '=' * 78)
print('PROBLEMAS ENCONTRADOS')
if not problemas:
    print('   ninguno')
for obs in sorted(problemas):
    print(f'\n--- {obs} ({len(problemas[obs])})')
    for cod, msg in problemas[obs]:
        print(f'   #{cod}  {msg}')

json.dump({'problemas': {k: v for k, v in problemas.items()},
           'resumen': dict(resumen)},
          open(os.path.join(BASE, 'reportes', 'auditoria_indicadores.json'), 'w', encoding='utf-8'),
          ensure_ascii=False, indent=1)
print('\n-> reportes/auditoria_indicadores.json')
