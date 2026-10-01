# -*- coding: utf-8 -*-
"""
Convierte los CSV realineados en una migración SQL idempotente.

Los CSV de `database/seeds` no se versionan, así que no llegan al servidor; la
única vía de despliegue es una migración, que el cron aplica sola. Esta se
escribe como INSERT … ON DUPLICATE KEY UPDATE: correrla dos veces deja la tabla
igual que correrla una.

No borra nada. Las filas viejas que describen indicadores sin publicar se
quedan donde están —el portal ya no las muestra, porque el título no
corresponde— y su limpieza queda propuesta aparte, en
reportes/hoja_vida_codigos_huerfanos.sql, para que la revise el equipo.

Uso:  python scripts/gen_migracion_hoja_vida.py
"""
import csv
import glob
import io
import os

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SEEDS = os.path.join(BASE, 'database', 'seeds')
DESTINO = os.path.join(BASE, 'database', 'migrations', '031_hoja_vida_realineada.sql')

# Las columnas de `indicators` que trae la hoja de vida. `description` no está:
# la tabla la tiene pero la ficha usa `definition`, y pisarla borraría lo que
# haya cargado el CMS.
COLUMNAS = ['id', 'observatory_id', 'title', 'category_1', 'category_2', 'tags', 'unit',
            'thematic_breakdown', 'geographic_breakdown', 'definition',
            'calculation_formula', 'periodicity', 'baseline_date', 'delivery_form',
            'source', 'source_link', 'actors', 'responsible_entity', 'observations',
            'availability_status']
NUMERICAS = {'id', 'observatory_id'}

# Observatorios que se sincronizan. CTeI queda fuera a propósito: el equipo
# pidió no publicar nada de ese observatorio mientras revisa su base.
ARCHIVOS = ['indicators_economico_realineado.csv', 'indicators_social_realineado.csv',
            'indicators_ambiente_realineado.csv', 'indicators_genero_realineado.csv']


def sql(v):
    if v is None or v == '':
        return 'NULL'
    return "'" + str(v).replace('\\', '\\\\').replace("'", "''").replace('\n', ' ') + "'"


def main():
    filas = []
    for nombre in ARCHIVOS:
        p = os.path.join(SEEDS, nombre)
        if not os.path.isfile(p):
            print('falta', nombre)
            continue
        for r in csv.DictReader(io.open(p, encoding='utf-8-sig')):
            filas.append({c: (r.get(c) or '').strip() for c in COLUMNAS})
    filas.sort(key=lambda r: int(r['id']))
    if not filas:
        raise SystemExit('No hay CSV realineados. Corre primero '
                         'scripts/realinear_hoja_vida.py --escribir')

    cab = ', '.join('`' + c + '`' for c in COLUMNAS)
    actualiza = ',\n  '.join(f'`{c}` = VALUES(`{c}`)' for c in COLUMNAS if c != 'id')

    lineas = [
        '-- 031: pone en cada ficha de la hoja de vida el código del indicador que describe.',
        '--',
        '-- El portal busca la hoja de vida de un indicador por el código de su carpeta.',
        '-- Eso vale cuando el número nombra lo mismo en los dos lados: en Ambiental y',
        '-- Económico sí, en Social y Género no. Su hoja de vida está numerada por línea',
        '-- temática —donde un mismo indicador aparece varias veces, una por población—',
        '-- y las carpetas se numeraron por grupo poblacional. El efecto era que 53',
        '-- tarjetas del portal mostraban el título de un indicador con la definición, la',
        '-- fórmula, la periodicidad y la fuente de otro.',
        '--',
        '-- Esta migración reasigna el código de la ficha, no el de la carpeta: ningún',
        f'-- enlace publicado deja de funcionar. Son {len(filas)} fichas, emparejadas por título y',
        '-- verificadas con scripts/validar_hoja_vida.php.',
        '--',
        '-- No borra filas. Las que describen indicadores sin publicar se quedan y el',
        '-- portal no las muestra, porque el título no corresponde.',
        '',
        f'INSERT INTO indicators ({cab}) VALUES',
    ]
    for i, r in enumerate(filas):
        vals = ', '.join(r[c] if c in NUMERICAS else sql(r[c]) for c in COLUMNAS)
        lineas.append(f'  ({vals})' + (',' if i < len(filas) - 1 else ''))
    lineas.append('ON DUPLICATE KEY UPDATE')
    lineas.append('  ' + actualiza + ';')
    lineas.append('')

    io.open(DESTINO, 'w', encoding='utf-8', newline='\n').write('\n'.join(lineas))
    print(f'{len(filas)} fichas -> {os.path.relpath(DESTINO, BASE)}'
          f'  ({os.path.getsize(DESTINO) / 1024:.0f} KB)')


if __name__ == '__main__':
    main()
