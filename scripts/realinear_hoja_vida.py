# -*- coding: utf-8 -*-
"""
Pone en cada ficha de la hoja de vida el código del indicador que describe.

El portal muestra la hoja de vida de un indicador buscando en la tabla
`indicators` la fila que tiene el mismo código que la carpeta. Eso funciona
cuando el número significa lo mismo en los dos lados. En Ambiental y Económico
sí; en Social y Género no, porque su hoja de vida está numerada por línea
temática —y ahí «Enfermedades transmitidas por vector» aparece tres veces, una
por población— mientras las carpetas se numeraron por grupo poblacional. El
resultado era que 53 tarjetas del portal mostraban el título de un indicador
con la definición, la fórmula, la periodicidad y la fuente de otro.

Este script empareja ficha y carpeta por el título y reescribe el código de la
ficha con el de la carpeta. No invierte la relación: no renumera carpetas, así
que ningún enlace publicado deja de funcionar.

Lo que no puede emparejar no lo adivina. Una ficha que describe un indicador
que no está publicado, o un título que aparece repetido en varias carpetas
—porque el catálogo lo desagrega por población y el portal lo tiene como
indicadores distintos— queda listado aparte para que lo resuelva el equipo.

Salidas:
  database/seeds/indicators_<obs>_realineado.csv  para importar con
      php database/scripts/run_import_all_observatorios.php
  reportes/hoja_vida_realineacion.json            el detalle de cada decisión
  reportes/hoja_vida_codigos_huerfanos.sql        las filas viejas que quedan sin uso

Uso:  python scripts/realinear_hoja_vida.py
      python scripts/realinear_hoja_vida.py --escribir
"""
import argparse
import csv
import difflib
import io
import json
import os
import re
import unicodedata

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PUB = os.path.join(BASE, 'website', 'indicador')
SEEDS = os.path.join(BASE, 'database', 'seeds')
REPORTES = os.path.join(BASE, 'reportes')

CAMPOS = ['id', 'observatory_id', 'title', 'category_1', 'category_2', 'tags', 'unit',
          'thematic_breakdown', 'geographic_breakdown', 'definition',
          'calculation_formula', 'periodicity', 'baseline_date', 'delivery_form',
          'source', 'source_link', 'actors', 'responsible_entity', 'observations',
          'availability_status']

OBSERVATORIOS = {
    1: ('economico', ['indicators_economico_dynamic_import.csv', 'indicators_base_general.csv']),
    2: ('social', ['indicators_social_dynamic_import.csv']),
    3: ('ambiente', ['indicators_base_general.csv']),
    4: ('cti', ['indicators_base_general.csv']),
    5: ('genero', ['indicators_genero_dynamic_import.csv']),
}


def comparable(s):
    """Título normalizado para comparar. Replica im_titulo_comparable() de
    website/lib/indicator_metadata.php: si las dos puntas no normalizan igual,
    el portal descartaría una ficha que este script sí emparejó."""
    s = unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower()
    return re.sub(r'\s+', ' ', re.sub(r'[^a-z0-9]+', ' ', s)).strip()


def corresponde(a, b):
    """Mismas tres reglas que la función del portal, en el mismo orden."""
    ca, cb = comparable(a), comparable(b)
    if not ca or not cb:
        return 0.0
    if ca == cb:
        return 1.0
    corto, largo = sorted((ca, cb), key=len)
    if len(corto) >= 12 and corto in largo:
        return 0.95
    return difflib.SequenceMatcher(None, ca, cb).ratio()


UMBRAL = 0.85


def leer_info(cod):
    p = os.path.join(PUB, cod, 'indicador.info')
    if not os.path.isfile(p):
        return None
    d = {}
    for linea in io.open(p, encoding='utf-8', errors='replace'):
        if ':' in linea:
            k, v = linea.split(':', 1)
            d[comparable(k).replace(' ', '')] = v.strip()
    return d


def carpetas_de(obs):
    out = {}
    for d in sorted(os.listdir(PUB)):
        if not re.fullmatch(r'\d{4}', d) or d[0] != str(obs):
            continue
        inf = leer_info(d)
        if not inf or inf.get('retirado'):
            continue
        g = 0
        while os.path.isfile(os.path.join(PUB, d, f'{g + 1}.csv')):
            g += 1
        if g == 0:
            # Una ficha que lleva a un indicador sin una sola gráfica es un
            # enlace roto con otra cara. Mejor dejar la carpeta sin ficha hasta
            # que tenga datos: son los nueve conocidos (2214, 2300, 2534,
            # 2803-2807 y 5205).
            continue
        out[d] = {'titulo': inf.get('titulo', ''), 'categoria': inf.get('categoria', ''),
                  'descripcion': inf.get('descripcion', ''), 'fuentes': inf.get('fuentes', ''),
                  'graficas': g}
    return out


def fichas_de(obs, archivos):
    vistos, out = set(), []
    for nombre in archivos:
        p = os.path.join(SEEDS, nombre)
        if not os.path.isfile(p):
            continue
        for r in csv.DictReader(io.open(p, encoding='utf-8-sig')):
            cod = str(r.get('id', '')).strip()
            if not re.fullmatch(r'\d{4}', cod) or cod[0] != str(obs) or cod in vistos:
                continue
            vistos.add(cod)
            fila = {c: (r.get(c) or '').strip() for c in CAMPOS}
            fila['_origen'] = nombre
            out.append(fila)
    return out


def emparejar(fichas, carpetas):
    """Asignación uno a uno, de la pareja más parecida a la menos. Un título que
    el catálogo trae una vez y el portal tiene en varias carpetas solo puede
    quedarse con una: las demás quedan sin ficha, que es la verdad."""
    pares = sorted(((corresponde(f['title'], c['titulo']), f['id'], cid)
                    for f in fichas for cid, c in carpetas.items()), key=lambda x: -x[0])
    porid = {f['id']: f for f in fichas}
    uf, uc, asignadas = set(), set(), {}
    for r, fid, cid in pares:
        if r < UMBRAL or fid in uf or cid in uc:
            continue
        uf.add(fid)
        uc.add(cid)
        asignadas[cid] = (porid[fid], round(r, 3))
    sin_carpeta = [f for f in fichas if f['id'] not in uf]
    sin_ficha = [cid for cid in carpetas if cid not in uc]
    return asignadas, sin_carpeta, sin_ficha


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--escribir', action='store_true',
                    help='escribe los CSV realineados y los reportes')
    args = ap.parse_args()

    informe, huerfanos = {}, []
    for obs, (slug, archivos) in OBSERVATORIOS.items():
        fichas, carpetas = fichas_de(obs, archivos), carpetas_de(obs)
        asignadas, sin_carpeta, sin_ficha = emparejar(fichas, carpetas)
        movidas = {cid: v for cid, v in asignadas.items() if v[0]['id'] != cid}
        quietas = {cid: v for cid, v in asignadas.items() if v[0]['id'] == cid}

        print('=' * 78)
        print(f'{obs} — {slug}:  {len(fichas)} fichas, {len(carpetas)} carpetas')
        print(f'   la ficha ya tenía el código correcto: {len(quietas)}')
        print(f'   la ficha cambia de código: {len(movidas)}')
        print(f'   fichas que no describen nada publicado: {len(sin_carpeta)}')
        print(f'   carpetas que se quedan sin ficha: {len(sin_ficha)}')
        for cid, (f, r) in sorted(movidas.items()):
            print(f'     {f["id"]} -> {cid}  {r}  {f["title"][:50]}')

        # Las filas de la tabla que ya no las reclama ninguna carpeta: si se dejan,
        # el portal no las muestra (no corresponden) pero estorban en el CMS.
        reclamados = {cid for cid in asignadas}
        for f in fichas:
            if f['id'] not in {v[0]['id'] for v in asignadas.values()} and f['id'] not in reclamados:
                huerfanos.append((f['id'], f['title']))

        if args.escribir:
            filas = []
            for cid in sorted(asignadas):
                f, _ = asignadas[cid]
                fila = {c: f[c] for c in CAMPOS}
                fila['id'] = cid
                fila['observatory_id'] = str(obs)
                # El título que manda es el de la carpeta: es el que ve la gente
                # en el portal y el que debe coincidir con la ficha.
                fila['title'] = carpetas[cid]['titulo'] or f['title']
                fila['category_1'] = carpetas[cid]['categoria'] or fila['category_1']
                filas.append(fila)
            if filas:
                destino = os.path.join(SEEDS, f'indicators_{slug}_realineado.csv')
                with io.open(destino, 'w', encoding='utf-8', newline='') as fh:
                    w = csv.DictWriter(fh, fieldnames=CAMPOS)
                    w.writeheader()
                    w.writerows(filas)
                print(f'   -> {os.path.relpath(destino, BASE)}  ({len(filas)} filas)')

        informe[obs] = {
            'observatorio': slug,
            'ya_correctas': sorted(quietas),
            'movidas': {cid: {'desde': v[0]['id'], 'titulo': v[0]['title'],
                              'parecido': v[1]} for cid, v in movidas.items()},
            'fichas_sin_carpeta': [{'id': f['id'], 'titulo': f['title']} for f in sin_carpeta],
            'carpetas_sin_ficha': [{'id': c, 'titulo': carpetas[c]['titulo']} for c in sin_ficha],
        }

    if args.escribir:
        os.makedirs(REPORTES, exist_ok=True)
        json.dump(informe, io.open(os.path.join(REPORTES, 'hoja_vida_realineacion.json'),
                                   'w', encoding='utf-8'), ensure_ascii=False, indent=1)
        if huerfanos:
            sql = ['-- Filas de `indicators` que ya no describen ningún indicador publicado.',
                   '-- El portal no las muestra (el título no corresponde), pero conviene',
                   '-- retirarlas para que el CMS no ofrezca fichas sin indicador.',
                   '-- Revisar la lista antes de ejecutar.', '']
            for cod, tit in sorted(huerfanos):
                sql.append(f'-- {cod}  {tit}')
            sql.append('')
            sql.append('DELETE FROM indicators WHERE id IN ('
                       + ', '.join(c for c, _ in sorted(huerfanos)) + ');')
            io.open(os.path.join(REPORTES, 'hoja_vida_codigos_huerfanos.sql'), 'w',
                    encoding='utf-8', newline='\n').write('\n'.join(sql) + '\n')
        print(f'\n-> reportes/hoja_vida_realineacion.json')
        print(f'-> reportes/hoja_vida_codigos_huerfanos.sql  ({len(huerfanos)} filas)')
    else:
        print('\nEnsayo. Para escribir los CSV y los reportes: --escribir')


if __name__ == '__main__':
    main()
