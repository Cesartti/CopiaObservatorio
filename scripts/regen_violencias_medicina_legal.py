# -*- coding: utf-8 -*-
"""
Rehace los indicadores de violencia que están publicados con la serie en ceros.

En el portal hay 24 indicadores de violencia cuyos cuatro archivos de datos
están completos de ceros. Son páginas públicas que muestran una línea plana en
cero, lo que le dice al ciudadano que el fenómeno no existe. El dato sí está:
vive en la hoja «MEDICINA LEGAL VIOLENCIAS» de BASE DX OBS SOCIAL - VIOLENCIA,
que es el microdato de las valoraciones médico-legales, una fila por caso.

Tres decisiones que conviene tener a la vista:

1. Medicina Legal no tiene una categoría llamada «violencia intrafamiliar».
   Lo que el portal rotula así corresponde a «VIOLENCIA ENTRE OTROS
   FAMILIARES», que es una de las cuatro subcategorías del ámbito familiar
   junto con pareja, niñez y adulto mayor. El indicador se renombra.

2. La serie llega hasta 2025. El año 2026 está incompleto en la base —sólo
   hasta junio— y una barra de medio año al lado de años completos se lee como
   una caída que no ocurrió.

3. La columna «Edad Absoluta» está vacía en todos los registros de 2025 y
   2026. Por eso los indicadores de población joven no se filtran por edad
   exacta sino por ciclo de vida, que sí está completo. El rango efectivo pasa
   a ser 12 a 28 años, y así queda dicho en la ficha.

Escribe en website/indicador_staging_violencias para revisar antes de publicar.
Con --publicar sobreescribe los indicadores del portal.

Uso:
    python scripts/regen_violencias_medicina_legal.py
    python scripts/regen_violencias_medicina_legal.py --publicar
"""
import io
import os
import sys

import pandas as pd

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
FUENTE = os.path.join(BASE, 'database', 'seeds',
                      'BASE DX OBS SOCIAL - VIOLENCIA.xlsx')
HOJA = 'MEDICINA LEGAL VIOLENCIAS'
STAGING = os.path.join(BASE, 'website', 'indicador_staging_violencias')
PORTAL = os.path.join(BASE, 'website', 'indicador')

ANIOS = list(range(2020, 2026))          # 2026 está incompleto en la base
JOVEN = ('Adolescencia (12 a 17 años)', 'Juventud (18 a 28 años)')
NINEZ = ('Primera infancia (0 a 5 años)', 'Infancia (6 a 11 años)',
         'Adolescencia (12 a 17 años)')

PAREJA = 'VIOLENCIA DE PAREJA'
INTERPERSONAL = 'VIOLENCIA INTERPERSONAL'
SEXUAL = 'PRESUNTO DELITO SEXUAL'
FAMILIARES = 'VIOLENCIA ENTRE OTROS FAMILIARES'
NNA = 'VIOLENCIA A NIÑOS, NIÑAS Y ADOLESCENTES'
MAYOR = 'VIOLENCIA AL ADULTO MAYOR'

# codigo: (titulo, contexto, filtro extra, segunda serie)
#   filtro extra: None | 'joven' | 'ninez' | 'mujer'
#   segunda serie: 'sexo' | 'ciclo'
INDICADORES = {
    # Social · Violencia
    '2008': ('Violencia a niños, niñas y adolescentes', NNA, None, 'sexo'),
    '2009': ('Violencia entre otros familiares', FAMILIARES, None, 'sexo'),
    '2010': ('Presunto delito sexual', SEXUAL, None, 'sexo'),
    '2011': ('Violencia de pareja', PAREJA, None, 'sexo'),
    '2012': ('Violencia interpersonal', INTERPERSONAL, None, 'sexo'),
    '2013': ('Violencia a adulto mayor', MAYOR, None, 'sexo'),
    # Social · Línea Familia
    '2713': ('Violencia a niños, niñas y adolescentes', NNA, None, 'sexo'),
    '2714': ('Violencia de pareja', PAREJA, None, 'sexo'),
    '2715': ('Presunto delito sexual a niños, niñas y adolescentes', SEXUAL,
             'ninez', 'sexo'),
    '2716': ('Violencia a adulto mayor', MAYOR, None, 'sexo'),
    '2717': ('Violencia entre otros familiares', FAMILIARES, None, 'sexo'),
    # Social · Línea Juventud (12 a 28 años, por ciclo de vida)
    '2508': ('Violencia de pareja en población de 12 a 28 años', PAREJA,
             'joven', 'sexo'),
    '2509': ('Violencia interpersonal en población de 12 a 28 años',
             INTERPERSONAL, 'joven', 'sexo'),
    '2510': ('Presunto delito sexual en población de 12 a 28 años', SEXUAL,
             'joven', 'sexo'),
    '2511': ('Violencia entre otros familiares en población de 12 a 28 años',
             FAMILIARES, 'joven', 'sexo'),
    # Género · víctimas mujeres
    '5408': ('Violencia de pareja con enfoque de género', PAREJA, 'mujer',
             'ciclo'),
    '5409': ('Violencia interpersonal con enfoque de género', INTERPERSONAL,
             'mujer', 'ciclo'),
    '5410': ('Presunto delito sexual con enfoque de género', SEXUAL, 'mujer',
             'ciclo'),
    '5411': ('Violencia entre otros familiares con enfoque de género',
             FAMILIARES, 'mujer', 'ciclo'),
    '5412': ('Violencia a niñas y adolescentes con enfoque de género', NNA,
             'mujer', 'ciclo'),
    '5413': ('Violencia a adulto mayor con enfoque de género', MAYOR, 'mujer',
             'ciclo'),
}

CICLOS = ['Primera infancia (0 a 5 años)', 'Infancia (6 a 11 años)',
          'Adolescencia (12 a 17 años)', 'Juventud (18 a 28 años)',
          'Adultez (29 a 59 años)', 'Vejez (Mayor = 60 años)']


def campo(v):
    """Un valor de CSV que puede traer comas se entrega entre comillas."""
    s = '' if v is None else str(v)
    return '"%s"' % s.replace('"', '""') if (',' in s or '"' in s) else s


def escribir(ruta, lineas):
    with open(ruta, 'w', encoding='utf-8', newline='\n') as fh:
        fh.write('\n'.join(lineas) + '\n')


def info(ruta, titulo, descripcion, vertical='Casos', horizontal='Año'):
    escribir(ruta, ['Titulo:%s' % titulo, 'Descripción:%s' % descripcion,
                    'Vertical:%s' % vertical, 'Horizontal:%s' % horizontal])


def cargar():
    d = pd.read_excel(FUENTE, sheet_name=HOJA)
    d = d[['Contexto del Hecho', 'AÑO VALORACION', 'SEXO FINAL',
           'CICLO DE VIDA']].copy()
    for c in d.columns:
        d[c] = d[c].astype(str).str.strip()
    d['AÑO VALORACION'] = pd.to_numeric(d['AÑO VALORACION'], errors='coerce')
    return d.dropna(subset=['AÑO VALORACION'])


def generar(d, destino):
    os.makedirs(destino, exist_ok=True)
    resumen, renombres = [], []
    for cod, (titulo, contexto, extra, segunda) in sorted(INDICADORES.items()):
        sel = d[(d['Contexto del Hecho'] == contexto)
                & (d['AÑO VALORACION'].isin(ANIOS))]
        if extra == 'joven':
            sel = sel[sel['CICLO DE VIDA'].isin(JOVEN)]
        elif extra == 'ninez':
            sel = sel[sel['CICLO DE VIDA'].isin(NINEZ)]
        elif extra == 'mujer':
            sel = sel[sel['SEXO FINAL'] == 'Mujer']

        carpeta = os.path.join(destino, cod)
        os.makedirs(carpeta, exist_ok=True)

        serie = sel.groupby('AÑO VALORACION').size().reindex(ANIOS, fill_value=0)
        escribir(os.path.join(carpeta, '1.csv'),
                 ['Año,Valor'] + ['%d,%d' % (a, int(v)) for a, v in serie.items()])
        nota = ('Casos valorados por el Instituto Nacional de Medicina Legal y '
                'Ciencias Forenses en Boyacá.')
        info(os.path.join(carpeta, '1.info'), '%s — serie anual' % titulo, nota)

        if segunda == 'sexo':
            tabla = (sel.pivot_table(index='AÑO VALORACION', columns='SEXO FINAL',
                                     aggfunc='size', fill_value=0)
                     .reindex(ANIOS, fill_value=0))
            for c in ('Mujer', 'Hombre'):
                if c not in tabla.columns:
                    tabla[c] = 0
            escribir(os.path.join(carpeta, '2.csv'),
                     ['Año,Femenino,Masculino']
                     + ['%d,%d,%d' % (a, int(f['Mujer']), int(f['Hombre']))
                        for a, f in tabla.iterrows()])
            info(os.path.join(carpeta, '2.info'), '%s — por sexo' % titulo,
                 'Sexo de la víctima registrado en la valoración.')
        else:
            tabla = (sel.pivot_table(index='AÑO VALORACION', columns='CICLO DE VIDA',
                                     aggfunc='size', fill_value=0)
                     .reindex(ANIOS, fill_value=0))
            ciclos = [c for c in CICLOS if c in tabla.columns]
            escribir(os.path.join(carpeta, '2.csv'),
                     ['Año,' + ','.join(campo(c) for c in ciclos)]
                     + ['%d,%s' % (a, ','.join(str(int(f[c])) for c in ciclos))
                        for a, f in tabla.iterrows()])
            info(os.path.join(carpeta, '2.info'), '%s — por ciclo de vida' % titulo,
                 'Ciclo de vida de la víctima al momento de la valoración.')

        # La ficha del indicador conserva su categoría y su fuente; sólo se
        # corrige el nombre cuando la fuente llama distinto al fenómeno.
        ficha = os.path.join(PORTAL, cod, 'indicador.info')
        if os.path.isfile(ficha):
            lineas, antes = [], None
            for ln in io.open(ficha, encoding='utf-8-sig').read().splitlines():
                if ln.lower().startswith('titulo:'):
                    antes = ln.split(':', 1)[1].strip()
                    lineas.append('Titulo:%s' % titulo)
                elif ln.lower().startswith(('descripcion:', 'descripción:')):
                    lineas.append('Descripción:%s' % titulo)
                else:
                    lineas.append(ln)
            escribir(os.path.join(carpeta, 'indicador.info'), lineas)
            if antes and antes != titulo:
                renombres.append((cod, antes, titulo))

        resumen.append((cod, titulo, int(serie.sum()), list(serie.astype(int))))
    return resumen, renombres


# ─────────────────────────────────────────────────────────────────────────
# Tres indicadores que no salen del microdato de Medicina Legal sino de otras
# hojas del mismo libro, y uno del libro de Género.
GENERO_XLSX = os.path.join(BASE, 'database', 'seeds',
                           'BASE DX OBS ASUNTOS DE GÉNERO.xlsx')


def generar_otras(destino):
    hechas = []

    # 2512 · atenciones médicas por violencia en población joven.
    d = pd.read_excel(FUENTE, sheet_name='ATENCIONES MED VIOL')
    d['CICLO DE VIDA'] = d['CICLO DE VIDA'].astype(str).str.strip()
    d['SEXO VICTIMA'] = d['SEXO VICTIMA'].astype(str).str.strip()
    sel = d[d['AÑO'].isin(ANIOS) & d['CICLO DE VIDA'].isin(JOVEN)]
    titulo = 'Atenciones médicas por violencia en población de 12 a 28 años'
    carpeta = os.path.join(destino, '2512')
    os.makedirs(carpeta, exist_ok=True)
    serie = sel.groupby('AÑO').size().reindex(ANIOS, fill_value=0)
    escribir(os.path.join(carpeta, '1.csv'),
             ['Año,Valor'] + ['%d,%d' % (a, int(v)) for a, v in serie.items()])
    info(os.path.join(carpeta, '1.info'), '%s — serie anual' % titulo,
         'Casos detectados en consulta médica y notificados a vigilancia en '
         'salud pública.')
    t = (sel.pivot_table(index='AÑO', columns='SEXO VICTIMA', aggfunc='size',
                         fill_value=0).reindex(ANIOS, fill_value=0))
    for c in ('Mujer', 'Hombre'):
        if c not in t.columns:
            t[c] = 0
    escribir(os.path.join(carpeta, '2.csv'),
             ['Año,Femenino,Masculino']
             + ['%d,%d,%d' % (a, int(f['Mujer']), int(f['Hombre']))
                for a, f in t.iterrows()])
    info(os.path.join(carpeta, '2.info'), '%s — por sexo' % titulo,
         'Sexo de la víctima registrado en la notificación.')
    hechas.append(('2512', titulo, int(serie.sum()), list(serie.astype(int))))

    # 5414 · convivencia educativa, víctimas mujeres.
    d = pd.read_excel(FUENTE, sheet_name='CONVIVENCIA EDUCATIVA')
    d['GENERO'] = d['GENERO'].astype(str).str.strip()
    d['Ciclo de vida'] = d['Ciclo de vida'].astype(str).str.strip()
    sel = d[d['AÑO'].isin(ANIOS) & (d['GENERO'] == 'Mujer')]
    titulo = 'Violencia en convivencia educativa con enfoque de género'
    carpeta = os.path.join(destino, '5414')
    os.makedirs(carpeta, exist_ok=True)
    serie = sel.groupby('AÑO').size().reindex(ANIOS, fill_value=0)
    escribir(os.path.join(carpeta, '1.csv'),
             ['Año,Valor'] + ['%d,%d' % (a, int(v)) for a, v in serie.items()])
    info(os.path.join(carpeta, '1.info'), '%s — serie anual' % titulo,
         'Casos de niñas y adolescentes atendidos por el Instituto Colombiano '
         'de Bienestar Familiar.')
    t = (sel.pivot_table(index='AÑO', columns='Ciclo de vida', aggfunc='size',
                         fill_value=0).reindex(ANIOS, fill_value=0))
    ciclos = [c for c in CICLOS if c in t.columns]
    escribir(os.path.join(carpeta, '2.csv'),
             ['Año,' + ','.join(campo(c) for c in ciclos)]
             + ['%d,%s' % (a, ','.join(str(int(f[c])) for c in ciclos))
                for a, f in t.iterrows()])
    info(os.path.join(carpeta, '2.info'), '%s — por ciclo de vida' % titulo,
         'Ciclo de vida de la víctima.')
    hechas.append(('5414', titulo, int(serie.sum()), list(serie.astype(int))))

    # 5700 · mujeres en reincorporación. No es una serie: es un censo con
    # fecha de corte, de modo que se presenta en barras y no en línea.
    d = pd.read_excel(GENERO_XLSX, sheet_name='Reincorporación')
    sel = d[(d['FechaCorte'] == 20250930) & (d['Estado_SIRR'] == 'Activo')]
    titulo = 'Mujeres con proceso de reincorporación activo'
    carpeta = os.path.join(destino, '5700')
    os.makedirs(carpeta, exist_ok=True)
    nota = ('Corte a 30 de septiembre de 2025. Agencia para la Reincorporación '
            'y la Normalización.')
    etario = (sel['GrupoEtario'].astype(str).str.strip()
              .value_counts().sort_index())
    escribir(os.path.join(carpeta, '1.csv'),
             ['Grupo etario,Mujeres']
             + ['%s,%d' % (campo(k), int(v)) for k, v in etario.items()])
    info(os.path.join(carpeta, '1.info'), '%s — por grupo etario' % titulo,
         nota, vertical='Mujeres', horizontal='Grupo etario')
    muni = (sel['Municipio de Residencia Descr'].astype(str).str.strip()
            .value_counts().sort_values(ascending=False))
    escribir(os.path.join(carpeta, '2.csv'),
             ['Municipio,Mujeres']
             + ['%s,%d' % (campo(k), int(v)) for k, v in muni.items()])
    info(os.path.join(carpeta, '2.info'),
         '%s — por municipio de residencia' % titulo, nota,
         vertical='Mujeres', horizontal='Municipio')
    escribir(os.path.join(carpeta, 'display.js'), [
        'class Chart1 extends AbstractChart{',
        "  getOptions(info){ return { hAxis:{title:info['horizontal']}, "
        "vAxis:{title:info['vertical']}, legend:'none' }; }",
        '  getType(div){ return new google.visualization.ColumnChart(div); }',
        '}',
        '',
        'class Chart2 extends AbstractChart{',
        "  getOptions(info){ return { hAxis:{title:info['horizontal']}, "
        "vAxis:{title:info['vertical']}, legend:'none' }; }",
        '  getType(div){ return new google.visualization.ColumnChart(div); }',
        '}',
        '',
        'class Display extends AbstractDisplay{',
        "  constructor(){ super('corechart',[Chart1,Chart2]); }",
        '}',
    ])
    hechas.append(('5700', titulo, int(len(sel)), []))
    return hechas


def main():
    publicar = '--publicar' in sys.argv
    destino = PORTAL if publicar else STAGING
    print('Fuente: %s · hoja %s' % (os.path.basename(FUENTE), HOJA))
    d = cargar()
    print('%d valoraciones leídas\n' % len(d))
    resumen, renombres = generar(d, destino)
    print('%-6s %-56s %7s  %s' % ('cod', 'titulo', 'total', ' '.join(str(a) for a in ANIOS)))
    for cod, titulo, total, serie in resumen:
        print('%-6s %-56s %7d  %s'
              % (cod, titulo[:56], total, ' '.join('%4d' % v for v in serie)))
    print()
    for cod, titulo, total, serie in generar_otras(destino):
        print('%-6s %-56s %7d  %s'
              % (cod, titulo[:56], total,
                 ' '.join('%4d' % x for x in serie) if serie else '(corte único)'))
    if renombres:
        print('\nIndicadores que cambian de nombre:')
        for cod, antes, ahora in renombres:
            print('   %s  %s  ->  %s' % (cod, antes, ahora))
    print('\n-> %s' % os.path.relpath(destino, BASE))
    if not publicar:
        print('   (staging: vuelva a correrlo con --publicar para llevarlo al portal)')


if __name__ == '__main__':
    main()
