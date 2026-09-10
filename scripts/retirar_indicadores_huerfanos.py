# -*- coding: utf-8 -*-
"""
Marca como retiradas las carpetas de indicadores que siguen vivas en producción
pero que el repositorio ya no tiene.

El despliegue copia con rsync sin --delete: las carpetas que se eliminaron del
repositorio (duplicados exactos y la regeneración del Observatorio Social)
siguen publicadas en el servidor. Por eso el micrositio Social listaba 317
indicadores en vez de 162 y el Económico 88 en vez de 87.

Como no se pueden borrar por despliegue, se vuelven a crear en el repositorio
con un `indicador.info` mínimo que lleva `Retirado:1` y, cuando existe un
indicador vigente con el mismo título, `Reemplazado:<id>`. observatorio.php y
lib/indicator_files.php los excluyen del listado y indicador.php responde 301
hacia el indicador vigente.
"""
import os, re, sys, unicodedata, urllib.request
import concurrent.futures as cf

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IND = os.path.join(BASE, 'website', 'indicador')
SITIO = 'https://observatorios.boyaca.gov.co'
SLUGS = {'1': 'economico', '2': 'social', '3': 'ambiente', '4': 'cti', '5': 'genero'}

def norm(s):
    s = unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower()
    return re.sub(r'[^a-z0-9 ]+', ' ', re.sub(r'\s+', ' ', s)).strip()

def get(url):
    try:
        with urllib.request.urlopen(url, timeout=25) as r:
            return r.read().decode('utf-8', 'replace')
    except Exception:
        return ''

def parse_info(txt):
    out = {}
    for line in txt.splitlines():
        if ':' in line:
            k, v = line.split(':', 1)
            out[norm(k).replace(' ', '')] = v.strip()
    return out

def locales(dig):
    ids = []
    for e in sorted(os.listdir(IND)):
        d = os.path.join(IND, e)
        if not (e.isdigit() and len(e) == 4 and e[0] == dig and os.path.isdir(d)):
            continue
        f = os.path.join(d, 'indicador.info')
        if not os.path.isfile(f):
            continue
        inf = parse_info(open(f, encoding='utf-8', errors='ignore').read())
        if inf.get('retirado'):
            continue
        ids.append((e, inf.get('titulo', '')))
    return ids

def produccion(slug):
    html = get(f'{SITIO}/observatorio.php?slug={slug}')
    return sorted(set(re.findall(r'indicador\.php\?id=(\d{4})', html)))

resumen = []
for dig, slug in SLUGS.items():
    loc = locales(dig)
    loc_ids = {i for i, _ in loc}
    prod = produccion(slug)
    # Se omiten las carpetas que el repositorio ya tiene marcadas como
    # retiradas: conservan su propio mapeo de reemplazo.
    huerfanos = [i for i in prod
                 if i not in loc_ids
                 and not os.path.isfile(os.path.join(IND, i, 'indicador.info'))]
    if not huerfanos:
        print(f'{slug}: sin huérfanos'); continue
    print(f'{slug}: {len(huerfanos)} carpetas huérfanas en producción')

    def titulo(i):
        return i, parse_info(get(f'{SITIO}/indicador/{i}/indicador.info'))
    with cf.ThreadPoolExecutor(max_workers=12) as ex:
        info_prod = dict(ex.map(titulo, huerfanos))

    # índice de títulos vigentes de la misma dimensión
    por_titulo = {}
    for i, t in loc:
        por_titulo.setdefault(norm(t), i)

    for i in huerfanos:
        inf = info_prod.get(i) or {}
        t = inf.get('titulo', '') or ('Indicador ' + i)
        dest = por_titulo.get(norm(t))
        if not dest:                       # coincidencia parcial por tokens
            tk = set(norm(t).split())
            mejor, punt = None, 0.0
            for nt, cand in por_titulo.items():
                ct = set(nt.split())
                if not ct:
                    continue
                j = len(tk & ct) / max(1, len(tk | ct))
                if j > punt:
                    mejor, punt = cand, j
            if punt >= 0.6:
                dest = mejor
        carpeta = os.path.join(IND, i)
        os.makedirs(carpeta, exist_ok=True)
        lineas = ['Retirado:1']
        if dest:
            lineas.append('Reemplazado:' + dest)
        lineas += [
            'Titulo:' + t,
            'Categoría:' + inf.get('categoria', ''),
            'Descripción:Indicador retirado del catálogo. Su información se publica '
            'ahora en un indicador vigente del observatorio.',
            'Subcategoría:' + inf.get('subcategoria', ''),
            'Etiquetas:Retirado',
            'Fuentes:' + inf.get('fuentes', ''),
        ]
        open(os.path.join(carpeta, 'indicador.info'), 'w', encoding='utf-8', newline='\n').write(
            '\n'.join(lineas) + '\n')
        open(os.path.join(carpeta, 'display.js'), 'w', encoding='utf-8', newline='\n').write(
            '/* Indicador retirado: ver el indicador vigente. */\n')
        resumen.append((slug, i, dest or '-', t[:56]))

print('\ncarpetas marcadas:', len(resumen))
con = sum(1 for r in resumen if r[2] != '-')
print('con indicador de reemplazo:', con, '| sin equivalente:', len(resumen) - con)
for r in resumen:
    print('  %-10s %s -> %-5s %s' % r)
