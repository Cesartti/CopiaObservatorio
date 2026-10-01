# -*- coding: utf-8 -*-
"""
Compara la numeración de la hoja de vida con la de las carpetas publicadas.

El número que aparece en la hoja de vida de un indicador tiene que ser el mismo
que abre el enlace. Hoy no siempre lo es: el portal creció con una numeración
propia y el catálogo institucional asignó otra, así que hay indicadores
publicados en un código y fichados en otro, códigos de la ficha que no existen
como carpeta, y carpetas sin ficha.

Este script no cambia nada: empareja por título y dice, indicador por
indicador, en qué situación está. El emparejamiento es por similitud de texto
—los títulos no se escriben igual en las dos fuentes— y por eso informa el
puntaje: lo que quede por debajo de 0,80 hay que revisarlo a mano antes de
renumerar nada.

Uso:  python scripts/revisar_numeracion_indicadores.py
      python scripts/revisar_numeracion_indicadores.py --observatorio 1
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

# Qué archivo es la hoja de vida vigente de cada observatorio. Los
# «dynamic_import» traen la numeración del catálogo maestro; «base_general» es
# el volcado anterior y solo manda donde no hay archivo más nuevo.
FICHAS = {
    1: ['indicators_economico_dynamic_import.csv', 'indicators_base_general.csv'],
    2: ['indicators_social_dynamic_import.csv', 'indicators_base_general.csv'],
    3: ['indicators_base_general.csv'],
    4: ['indicators_base_general.csv'],
    5: ['indicators_genero_dynamic_import.csv', 'indicators_base_general.csv'],
}
OBSERVATORIOS = {1: 'Económico', 2: 'Social', 3: 'Ambiental', 4: 'CTeI', 5: 'Género'}
UMBRAL = 0.80


def norm(s):
    s = unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower()
    s = re.sub(r'[^a-z0-9 ]', ' ', s)
    return re.sub(r'\s+', ' ', s).strip()


def parecido(a, b):
    na, nb = norm(a), norm(b)
    if not na or not nb:
        return 0.0
    r = difflib.SequenceMatcher(None, na, nb).ratio()
    # Un título puede ser el otro más una aclaración («Inventario bovino» contra
    # «Inventario bovino del departamento»): eso es la misma cosa.
    if na in nb or nb in na:
        r = max(r, 0.9)
    return r


def leer_fichas(obs):
    """Filas de la hoja de vida del observatorio, de la fuente más reciente que
    tenga alguna."""
    for nombre in FICHAS[obs]:
        p = os.path.join(SEEDS, nombre)
        if not os.path.isfile(p):
            continue
        filas = []
        for r in csv.DictReader(io.open(p, encoding='utf-8-sig')):
            cod = str(r.get('id', '')).strip()
            if re.fullmatch(r'\d{4}', cod) and cod[0] == str(obs):
                filas.append({'id': cod, 'titulo': (r.get('title') or '').strip(),
                              'categoria': (r.get('category_1') or '').strip(),
                              'fuente_ficha': nombre})
        if filas:
            return filas
    return []


def leer_info(p):
    d = {}
    if not os.path.isfile(p):
        return d
    for linea in io.open(p, encoding='utf-8', errors='replace'):
        if ':' in linea:
            k, v = linea.split(':', 1)
            d[norm(k).replace(' ', '')] = v.strip()
    return d


def leer_carpetas(obs):
    out = []
    for d in sorted(os.listdir(PUB)):
        if not re.fullmatch(r'\d{4}', d) or d[0] != str(obs):
            continue
        inf = leer_info(os.path.join(PUB, d, 'indicador.info'))
        if not inf or inf.get('retirado'):
            continue
        graficas = 0
        while os.path.isfile(os.path.join(PUB, d, f'{graficas + 1}.csv')):
            graficas += 1
        out.append({'id': d, 'titulo': inf.get('titulo', ''),
                    'categoria': inf.get('categoria', ''), 'graficas': graficas})
    return out


def emparejar(fichas, carpetas):
    """Empareja ficha ↔ carpeta por título, de mayor a menor similitud, sin
    reutilizar ninguna de las dos puntas."""
    pares = sorted(
        ((parecido(f['titulo'], c['titulo']), f['id'], c['id']) for f in fichas
         for c in carpetas),
        key=lambda x: -x[0])
    pf = {f['id']: f for f in fichas}
    pc = {c['id']: c for c in carpetas}
    usadas_f, usadas_c, hechos = set(), set(), []
    for r, fid, cid in pares:
        if r < 0.55 or fid in usadas_f or cid in usadas_c:
            continue
        usadas_f.add(fid)
        usadas_c.add(cid)
        hechos.append({'ficha': pf[fid], 'carpeta': pc[cid], 'parecido': round(r, 3)})
    sueltas_f = [pf[i] for i in pf if i not in usadas_f]
    sueltas_c = [pc[i] for i in pc if i not in usadas_c]
    return hechos, sueltas_f, sueltas_c


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--observatorio', type=int, nargs='*', default=sorted(OBSERVATORIOS))
    args = ap.parse_args()

    informe = {}
    for obs in args.observatorio:
        fichas, carpetas = leer_fichas(obs), leer_carpetas(obs)
        hechos, sin_carpeta, sin_ficha = emparejar(fichas, carpetas)
        coinciden = [h for h in hechos if h['ficha']['id'] == h['carpeta']['id']]
        difieren = [h for h in hechos if h['ficha']['id'] != h['carpeta']['id']]
        dudosos = [h for h in hechos if h['parecido'] < UMBRAL]

        print('=' * 78)
        print(f'{obs} — {OBSERVATORIOS[obs]}:  {len(fichas)} fichas, '
              f'{len(carpetas)} carpetas publicadas')
        if fichas:
            print(f'   hoja de vida tomada de {fichas[0]["fuente_ficha"]}')
        print(f'   ya coinciden ficha y carpeta: {len(coinciden)}')
        print(f'   el número de la ficha no es el de la carpeta: {len(difieren)}')
        print(f'   fichas sin ningún indicador publicado: {len(sin_carpeta)}')
        print(f'   carpetas publicadas sin ficha: {len(sin_ficha)}')
        print(f'   emparejamientos por revisar a mano (parecido < {UMBRAL}): {len(dudosos)}')

        if difieren:
            print('\n   ficha -> carpeta                                   parecido')
            for h in sorted(difieren, key=lambda x: x['ficha']['id']):
                print(f'     {h["ficha"]["id"]} -> {h["carpeta"]["id"]}  '
                      f'{h["ficha"]["titulo"][:44]:46} {h["parecido"]}')
        if sin_carpeta:
            print('\n   fichas sin indicador publicado:')
            for f in sorted(sin_carpeta, key=lambda x: x['id']):
                print(f'     {f["id"]}  {f["titulo"][:62]}')
        if sin_ficha:
            print('\n   carpetas sin ficha en la hoja de vida:')
            for c in sorted(sin_ficha, key=lambda x: x['id']):
                print(f'     {c["id"]}  {c["titulo"][:54]:56} {c["graficas"]} gráficas')

        informe[obs] = {'observatorio': OBSERVATORIOS[obs], 'coinciden': coinciden,
                        'difieren': difieren, 'fichas_sin_carpeta': sin_carpeta,
                        'carpetas_sin_ficha': sin_ficha, 'dudosos': dudosos}

    os.makedirs(os.path.join(BASE, 'reportes'), exist_ok=True)
    salida = os.path.join(BASE, 'reportes', 'numeracion_indicadores.json')
    json.dump(informe, io.open(salida, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
    print('\n->', os.path.relpath(salida, BASE))


if __name__ == '__main__':
    main()
