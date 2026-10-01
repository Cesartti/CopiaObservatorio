# -*- coding: utf-8 -*-
"""
Copia los indicadores económicos del staging a website/indicador.

El Observatorio Económico arrastra un choque de numeración: el catálogo maestro
asigna códigos que en el portal ya están ocupados por indicadores distintos que
hoy funcionan. El código 1113, por ejemplo, es «Inventario bovino del
departamento» en el portal y «Promedio de años de educación» en el maestro.

Este script nunca sobrescribe. Si el código del maestro está libre, lo usa; si
está ocupado, reasigna el indicador nuevo al primer código libre del bloque de
reserva y deja constancia de la reasignación en reportes/. La decisión de
renumerar los que ya están publicados —si se toma— es de la mesa técnica, no de
un script.

Uso:  python scripts/publicar_economico.py            (solo informa)
      python scripts/publicar_economico.py --aplicar  (copia de verdad)
"""
import argparse
import io
import json
import os
import re
import shutil

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
STAGING = os.path.join(BASE, 'website', 'indicador_staging_economico')
DESTINO = os.path.join(BASE, 'website', 'indicador')
REPORTE = os.path.join(BASE, 'reportes', 'economico_reasignaciones.json')

# Bloque libre para los indicadores cuyo código del maestro está ocupado. Va
# después de 1165, el último que usa el maestro en Calidad de vida.
RESERVA = range(1166, 1300)


def titulo(p):
    try:
        t = io.open(p, encoding='utf-8', errors='replace').read()
    except OSError:
        return ''
    m = re.search(r'^T[ií]tulo\s*:(.*)$', t, re.M | re.I)
    return m.group(1).strip() if m else ''


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--aplicar', action='store_true')
    args = ap.parse_args()

    ocupados = {d for d in os.listdir(DESTINO) if re.fullmatch(r'\d{4}', d)}
    libres = (str(n) for n in RESERVA if str(n) not in ocupados)

    plan, reasignados = [], []
    for cod in sorted(os.listdir(STAGING)):
        origen = os.path.join(STAGING, cod)
        if not os.path.isfile(os.path.join(origen, 'display.js')):
            continue
        t = titulo(os.path.join(origen, 'indicador.info'))
        if cod in ocupados:
            nuevo = next(libres)
            ocupados.add(nuevo)
            reasignados.append({
                'codigo_maestro': cod, 'codigo_publicado': nuevo, 'titulo': t,
                'ocupado_por': titulo(os.path.join(DESTINO, cod, 'indicador.info')),
            })
            plan.append((origen, nuevo, t, cod))
        else:
            ocupados.add(cod)
            plan.append((origen, cod, t, None))

    for origen, cod, t, maestro in plan:
        marca = f'  (maestro {maestro}, ocupado)' if maestro else ''
        print(f'   {cod}  {t[:58]:60}{marca}')

    print(f'\n{len(plan)} indicadores; {len(reasignados)} reasignados por choque de código')
    if not args.aplicar:
        print('\nEnsayo. Para copiar de verdad: --aplicar')
        return

    for origen, cod, _, _ in plan:
        shutil.copytree(origen, os.path.join(DESTINO, cod), dirs_exist_ok=False)
    os.makedirs(os.path.dirname(REPORTE), exist_ok=True)
    json.dump(reasignados, io.open(REPORTE, 'w', encoding='utf-8'),
              ensure_ascii=False, indent=1)
    print(f'\ncopiados {len(plan)} a website/indicador')
    print('reasignaciones en', os.path.relpath(REPORTE, BASE))


if __name__ == '__main__':
    main()
