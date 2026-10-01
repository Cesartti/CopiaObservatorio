# -*- coding: utf-8 -*-
"""
Genera los 13 indicadores de «Política Fiscal» del Observatorio Económico.

Estos no los puede armar `regen_economico.py`. Ese script sirve para hojas donde
las filas son observaciones que se pueden sumar; aquí cada fila es un municipio
y la columna de interés casi siempre es un índice, un puntaje o un porcentaje.
Sumar el índice de desempeño fiscal de los 123 municipios da 1.541,77 —un número
que no significa nada— y eso es exactamente lo que salía antes. Lo que hay que
hacer con un índice es promediarlo; y lo que hay que hacer con una clasificación
(«Riesgo», «Vulnerable», «G1. Nivel alto») es contar cuántos municipios caen en
cada rango.

Por eso cada indicador declara aquí su hoja, su columna y su forma de agregar.
Son trece: no vale la pena adivinarlo cuando se puede escribir.

De cada indicador salen hasta tres paneles:

  1. la serie del departamento por año (promedio si es índice, suma si es plata);
  2. el mapa por municipio del último año disponible;
  3. un desglose propio del indicador (componentes del gasto, cumplimiento de
     la Ley 617, distribución por rango…).

Uso:  python scripts/regen_economico_fiscal.py
      python scripts/regen_economico_fiscal.py --destino website/indicador
"""
import argparse
import io
import json
import os
import re
import unicodedata
import warnings

import pandas as pd

warnings.filterwarnings('ignore')

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
LIBRO = os.path.join(BASE, 'database', 'seeds', 'BASE DX OBS ECONÓMICO.xlsx')
MUNICIPIOS = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')
OUT = os.path.join(BASE, 'website', 'indicador_staging_economico')

NUMERO = re.compile(r'[\d.]+')
FUENTE_DNP = 'DNP — Consolidador de Hacienda e Información Pública (CHIP/CIFFIT)'

# Cada entrada declara cómo se construye un indicador. «valor» es la columna que
# se grafica; «modo» dice si se suma (plata), se promedia (índice) o se cuenta
# (clasificación). «factor» pasa pesos a millones para que todos los de plata se
# lean en la misma unidad.
FISCAL = {
    '1500': dict(
        titulo='Ingresos Totales Municipales', hoja='Ingresos totales Mun',
        valor='Ingresos Totales', modo='suma', unidad='Millones de pesos',
        componentes=['Ingresos Corrientes', 'Ingresos tributarios',
                     'Ingresos No Tributarios', 'Ingresos de Capital', 'Regalías'],
        fuente=FUENTE_DNP),
    '1501': dict(
        titulo='Gastos de funcionamiento', hoja='Gastos Funcionamiento Mun',
        valor='Gastos de funcionamiento', modo='suma', unidad='Millones de pesos',
        componentes=['Servicios personales', 'Gastos generales',
                     'Transferencias pagadas', 'Intereses de la deuda pública'],
        fuente=FUENTE_DNP),
    '1502': dict(
        titulo='Recursos de Capital Municipales', hoja='Recursos capital Mun',
        valor='Total Recaudo Recursos de Capital', modo='suma',
        unidad='Millones de pesos', factor=1e-6,
        componentes=['Recaudo Vigencia Actual Con Fondos',
                     'Recaudo Vigencia Anterior Sin Fondos'],
        fuente=FUENTE_DNP),
    '1503': dict(
        titulo='Indicador de Racionalidad del Gasto', hoja='Racionalidad gastos Mun',
        valor='GF/ICLD (%)', modo='promedio', unidad='Porcentaje',
        municipio='Entidad', conteo='Cumple GF/ICLD (Si/No)',
        nota='Relación entre gastos de funcionamiento e ingresos corrientes de '
             'libre destinación. La Ley 617 de 2000 fija el límite según la '
             'categoría del municipio.',
        fuente='DNP — Consolidador de Hacienda e Información Pública (CHIP)'),
    '1504': dict(
        titulo='Índice de Desempeño Fiscal', hoja='IDF Municipios',
        valor='Nuevo IDF', modo='promedio', unidad='Índice (0 a 100)',
        codigo='Código', conteo='Rango',
        nota='Metodología vigente del DNP. El índice combina resultados fiscales '
             'y capacidad de gestión.',
        fuente='DNP — Desempeño fiscal municipal'),
    '1505': dict(
        titulo='Cuentas Por Pagar y Reservas Presupuestales Municipales',
        hoja='CXP Reserva Presup Mun V0', valor='Total Exigibilidades y Reservas',
        modo='suma', unidad='Millones de pesos', factor=1e-6,
        filtro=('Cod Concepto', 0),
        componentes=['Cuentas Por Pagar de la Vigencia',
                     'Cuents por Pagar Vigencias Anteriores',
                     'Reservas Presupuestales', 'Otras Exigibilidades'],
        nota='Se toma únicamente la fila TOTAL de cada municipio: el formulario '
             'trae además el detalle por concepto, y sumarlo todo contaría dos '
             'veces el mismo dinero.',
        fuente=FUENTE_DNP),
    '1506': dict(
        titulo='Medición de Desempeño Municipal (MDM)', hoja='Ind Desempeño Mun',
        valor='Medición de Desempeño Municipal', modo='promedio',
        unidad='Puntaje (0 a 100)',
        componentes=['Componente de gestión', 'Componente de resultados',
                     'Educación', 'Salud', 'Servicios públicos', 'Seguridad'],
        fuente='DNP — Medición del Desempeño Municipal'),
    '1507': dict(
        titulo='Índice de clasificación municipal', hoja='IDF Municipios',
        valor='Categorías', modo='conteo', unidad='Municipios', codigo='Código',
        nota='Categoría de ley de cada municipio (1 a 6, y especial), que define '
             'los límites de gasto que debe cumplir.',
        fuente='DNP — Desempeño fiscal municipal'),
    '1508': dict(
        titulo='Rango de clasificación según IDF', hoja='IDF Municipios',
        valor='Rango', modo='conteo', unidad='Municipios', codigo='Código',
        fuente='DNP — Desempeño fiscal municipal'),
    '1509': dict(
        titulo='Índice de clasificación grupo de capacidades iniciales',
        hoja='Ind Desempeño Mun', valor='Grupo de Capacidades iniciales',
        modo='conteo', unidad='Municipios',
        nota='El DNP agrupa los municipios por capacidades iniciales para '
             'comparar solo entre pares.',
        fuente='DNP — Medición del Desempeño Municipal'),
    '1510': dict(
        titulo='Variación del índice de eficacia municipal',
        hoja='Variación. indic de Eficacia M.', valor='Puntaje', modo='promedio',
        unidad='Puntaje (0 a 100)',
        nota='En el libro la hoja se llama «Variación. indic de Eficacia M.»; el '
             'catálogo la nombra «índice de eficiencia».',
        fuente='DNP — Evaluación del desempeño integral'),
    '1511': dict(
        titulo='Viabilidad ICLD', hoja='Ranquin de Viabilidad ICLD', valor='RANGO',
        modo='conteo', unidad='Municipios', atributo='Atributo', monto='Valor',
        nota='Clasificación de sostenibilidad a partir de los ingresos corrientes '
             'de libre destinación frente a los gastos de funcionamiento.',
        fuente='DNP — Ley 617 de 2000'),
    '1512': dict(
        titulo='Nivel de cumplimiento del sistema general de participaciones',
        hoja='IN. DE CUM. DE RL LEG. SGP', valor='Requisitos Legales',
        modo='promedio', unidad='Índice (0 a 1)', conteo='NIVEL DE CUMPLIMIENTO',
        fuente='DNP — Seguimiento a requisitos legales del SGP'),
}


def norm(s):
    s = unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode()
    return re.sub(r'\s+', ' ', s).strip().lower()


def escribir(p, c):
    io.open(p, 'w', encoding='utf-8', newline='\n').write(c)


def ficha(pares):
    return ''.join(f'{k}:{v}\n' for k, v in pares if v)


def campo(v):
    t = str(v).strip()
    return '"' + t.replace('"', '""') + '"' if any(c in t for c in ',"\n') else t


def clave_municipio(nombre):
    """Clave de comparación de nombres de municipio.

    Los nombres no coinciden entre fuentes: el mapa dice «LABRANZA GRANDE» y el
    DNP «Labranzagrande»; el mapa dice «GÜICÁN» y el DNP usa el nombre oficial
    nuevo, «Güicán de La Sierra». Se quitan tildes, espacios y el apellido
    geográfico («de la sierra», «del ...») para que los dos lados se encuentren.
    """
    n = norm(nombre)
    n = re.sub(r'\s+(de|del|de la|de los)\s+(sierra|boyaca|castilla)$', '', n)
    return n.replace(' ', '')


def codigos_municipio():
    """Nombre de municipio → código DANE, tomado del mismo GeoJSON que pinta el
    mapa: si un código no está ahí, el municipio no se dibuja."""
    texto = io.open(MUNICIPIOS, encoding='utf-8').read()
    pares = re.findall(r'"id"\s*:\s*"(\d+)"\s*,\s*"name"\s*:\s*"([^"]+)"', texto)
    tabla = {}
    for cod, nombre in pares:
        tabla[clave_municipio(nombre)] = cod
    return tabla


def columna(df, nombre):
    """Las hojas traen columnas con espacios de sobra («Rango », «Municipio »):
    se busca por nombre normalizado."""
    objetivo = norm(nombre)
    for c in df.columns:
        if norm(c) == objetivo:
            return c
    return None


def leer(hoja):
    df = pd.read_excel(LIBRO, sheet_name=hoja)
    df.columns = [str(c) for c in df.columns]
    return df


def anio_col(df):
    return columna(df, 'Año') or columna(df, 'AÑO')


def muni_col(df, cfg):
    for n in (cfg.get('municipio'), 'Municipio', 'Entidad'):
        if n and columna(df, n):
            return columna(df, n)
    return None


def serie_csv(nombre_x, nombre_y, pares):
    return (f'{nombre_x},{campo(nombre_y)}\n'
            + ''.join(f'{a},{v:.2f}\n' for a, v in pares))


def tabla_csv(cab, filas):
    return (','.join(campo(c) for c in cab) + '\n'
            + ''.join(','.join(campo(c) for c in f) + '\n' for f in filas))


def display_js(tipos):
    clases, i = [], 0
    for t in tipos:
        # Un panel de mapa viene como ('mapa', nombre de la columna de valor).
        columna_valor = None
        if isinstance(t, tuple):
            t, columna_valor = t
        i += 1
        if t == 'mapa':
            # MapChart busca la columna con indexOf sobre la cabecera del CSV y,
            # si no la encuentra, le muestra un alert() al visitante. Se deja el
            # nombre escrito aquí —como en los indicadores ambientales— en vez de
            # confiar en que «Vertical» y la cabecera coincidan carácter a carácter.
            cuerpo = ("\tconstructor(info,csv,chart){\n"
                      "\t\tsuper(info,csv,chart,%s,null,null,'geo',false);\n\t}"
                      % json.dumps(columna_valor, ensure_ascii=False))
            clases.append('class Chart%d extends AbstractMap{\n%s\n}' % (i, cuerpo))
            continue
        if t == 'column':
            opciones = ("{ hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, "
                        "legend:{position:'none'}, bar:{groupWidth:'70%'} }")
            tipo = 'ColumnChart'
        elif t == 'stack':
            opciones = ("{ hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, "
                        "isStacked:true, bar:{groupWidth:'70%'} }")
            tipo = 'ColumnChart'
        else:
            opciones = ("{ hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, "
                        "curveType:'function', pointSize:5 }")
            tipo = 'LineChart'
        clases.append('class Chart%d extends AbstractChart{\n'
                      '\tgetOptions(info){ return %s; }\n'
                      '\tgetType(div){ return new google.visualization.%s(div); }\n}'
                      % (i, opciones, tipo))
    lista = ','.join(f'Chart{n}' for n in range(1, i + 1))
    return ('\n\n'.join(clases)
            + "\n\nclass Display extends AbstractDisplay{\n\tconstructor(){\n"
              f"\t\tsuper(['corechart'],[{lista}]);\n\t}}\n}}\n")


def generar(cod, cfg, destino, geo):
    df = leer(cfg['hoja'])
    cv = columna(df, cfg['valor'])
    if cv is None:
        return f'falta la columna «{cfg["valor"]}» en {cfg["hoja"]}'
    ca, cm = anio_col(df), muni_col(df, cfg)
    if ca is None:
        return f'la hoja {cfg["hoja"]} no tiene columna de año'

    if 'filtro' in cfg:
        col, val = cfg['filtro']
        c = columna(df, col)
        if c is not None:
            df = df[pd.to_numeric(df[c], errors='coerce') == val]

    df = df.assign(_a=pd.to_numeric(df[ca], errors='coerce'))
    df = df[df['_a'].between(1990, 2035)]
    df['_a'] = df['_a'].astype(int)
    if df.empty:
        return 'sin años válidos'

    # Un «total departamental» calculado sobre un municipio no es un total: las
    # hojas suelen traer el año en curso a medio diligenciar. Y otras repiten las
    # filas del último año, lo que inflaría cualquier conteo. Las dos cosas se
    # arreglan quedándose con una fila por municipio y descartando los años con
    # cobertura muy por debajo del resto.
    cobertura = {}
    crudo = df
    if cm is not None:
        df = df.drop_duplicates(subset=['_a', cm])
        cobertura = df.groupby('_a')[cm].nunique().to_dict()
        if cobertura:
            minimo = max(cobertura.values()) * 0.6
            flacos = sorted(a for a, k in cobertura.items() if k < minimo)
            if flacos:
                df = df[~df['_a'].isin(flacos)]
    if df.empty:
        return 'ningún año con cobertura suficiente'

    aviso_cobertura = ''
    if cm is not None and cobertura:
        faltan = sorted(a for a, k in cobertura.items() if k < max(cobertura.values()) * 0.6)
        if faltan:
            aviso_cobertura = (' No se muestra ' + ', '.join(str(a) for a in faltan)
                               + ': la fuente aún no reporta todos los municipios.')

    factor = cfg.get('factor', 1.0)
    carpeta = os.path.join(destino, cod)
    os.makedirs(carpeta, exist_ok=True)
    tipos, n = [], 0

    def panel(tipo, csv, info):
        nonlocal n
        n += 1
        escribir(os.path.join(carpeta, f'{n}.csv'), csv)
        escribir(os.path.join(carpeta, f'{n}.info'), ficha(info))
        tipos.append(tipo)

    # ---- panel 1: el departamento año por año -------------------------------
    if cfg['modo'] == 'conteo':
        d = df.assign(_c=df[cv].fillna('Sin dato').astype(str).str.strip())
        d = d[~d['_c'].str.lower().isin(('', 'nan', 'sin dato'))]
        tab = d.groupby(['_a', '_c']).size().unstack(fill_value=0)
        if tab.empty:
            return 'sin categorías para contar'
        cols = list(tab.columns)[:10]
        etiq = (lambda c: f'Categoría {c}') if all(
            re.fullmatch(NUMERO, str(c)) for c in cols) else (lambda c: str(c))
        panel('stack',
              tabla_csv(['Año'] + [etiq(c) for c in cols],
                        [[str(a)] + [str(int(tab.loc[a, c])) for c in cols] for a in tab.index]),
              [('Tipo', 'Gráfico'), ('Titulo', f'{cfg["titulo"]} — municipios por categoría'),
               ('Descripción', 'Número de municipios de Boyacá en cada categoría, por año.'
                + aviso_cobertura + ' ' + cfg.get('nota', '')),
               ('Vertical', 'Municipios'), ('Horizontal', 'Año')])
    else:
        v = pd.to_numeric(df[cv], errors='coerce') * factor
        g = df.assign(_v=v).dropna(subset=['_v']).groupby('_a')['_v']
        g = g.mean() if cfg['modo'] == 'promedio' else g.sum()
        if g.empty:
            return 'la columna de valor no tiene números'
        etiqueta = ('Promedio departamental' if cfg['modo'] == 'promedio'
                    else 'Total departamental')
        panel('line', serie_csv('Año', etiqueta, g.items()),
              [('Tipo', 'Gráfico'), ('Titulo', f'{cfg["titulo"]} — {etiqueta.lower()} por año'),
               ('Descripción',
                ('Promedio de los municipios de Boyacá.' if cfg['modo'] == 'promedio'
                 else 'Suma de los municipios de Boyacá.')
                + aviso_cobertura + ' ' + cfg.get('nota', '')),
               ('Vertical', cfg['unidad']), ('Horizontal', 'Año')])

    # ---- panel 2: mapa del último año --------------------------------------
    if cm is not None and cfg['modo'] != 'conteo':
        ultimo = int(df['_a'].max())
        d = df[df['_a'] == ultimo].copy()
        cc = columna(d, cfg['codigo']) if cfg.get('codigo') else None
        if cc is not None:
            d['_geo'] = pd.to_numeric(d[cc], errors='coerce').astype('Int64').astype(str)
        else:
            d['_geo'] = d[cm].map(lambda x: geo.get(clave_municipio(x), ''))
        d['_m'] = d[cm].fillna('').astype(str).str.strip().str.title()
        d['_v'] = pd.to_numeric(d[cv], errors='coerce') * factor
        d = d[(d['_geo'] != '') & (d['_geo'] != '<NA>')].dropna(subset=['_v'])
        d = d.groupby(['_geo', '_m'], as_index=False)['_v'].mean()
        if len(d):
            panel(('mapa', cfg['unidad']),
                  tabla_csv(['geo', 'Municipio', cfg['unidad']],
                            [[r['_geo'], r['_m'], f'{r["_v"]:.2f}'] for _, r in d.iterrows()]),
                  [('Tipo', 'Mapa'), ('Titulo', f'{cfg["titulo"]} por municipio ({ultimo})'),
                   ('Descripción', f'Valor de cada municipio de Boyacá en {ultimo}.'),
                   ('Vertical', cfg['unidad'])])

    # ---- panel 3: el desglose propio de cada indicador ----------------------
    if cfg.get('componentes'):
        cols = [columna(df, c) for c in cfg['componentes']]
        cols = [c for c in cols if c is not None]
        if cols:
            agregar = ('mean' if cfg['modo'] == 'promedio' else 'sum')
            tab = df.groupby('_a')[cols].apply(
                lambda t: t.apply(lambda s: getattr(
                    pd.to_numeric(s, errors='coerce'), agregar)() * factor))
            panel('line',
                  tabla_csv(['Año'] + [str(c).strip() for c in cols],
                            [[str(a)] + [f'{tab.loc[a, c]:.2f}' for c in cols]
                             for a in tab.index]),
                  [('Tipo', 'Gráfico'), ('Titulo', f'{cfg["titulo"]} — por componente'),
                   ('Descripción', ('Promedio' if cfg['modo'] == 'promedio' else 'Suma')
                    + ' departamental de cada componente, por año.'),
                   ('Vertical', cfg['unidad']), ('Horizontal', 'Año')])
    elif cfg.get('conteo'):
        cc = columna(df, cfg['conteo'])
        if cc is not None:
            d = df.assign(_c=df[cc].fillna('Sin dato').astype(str).str.strip())
            d = d[~d['_c'].str.lower().isin(('', 'nan', 'sin dato'))]
            tab = d.groupby(['_a', '_c']).size().unstack(fill_value=0)
            cols = list(tab.columns)[:10]
            if cols:
                panel('stack',
                      tabla_csv(['Año'] + [str(c) for c in cols],
                                [[str(a)] + [str(int(tab.loc[a, c])) for c in cols]
                                 for a in tab.index]),
                      [('Tipo', 'Gráfico'),
                       ('Titulo', f'{cfg["titulo"]} — municipios por {str(cc).strip().lower()}'),
                       ('Descripción', 'Número de municipios en cada categoría, por año.'),
                       ('Vertical', 'Municipios'), ('Horizontal', 'Año')])
    elif cfg.get('atributo') and cfg.get('monto'):
        ct, cmt = columna(crudo, cfg['atributo']), columna(crudo, cfg['monto'])
        if ct is not None and cmt is not None:
            # Aquí sí se usa la hoja completa: cada municipio trae una fila por
            # atributo y la deduplicación habría dejado solo la primera.
            d = crudo[crudo['_a'].isin(df['_a'].unique())]
            d = d.assign(_t=d[ct].astype(str).str.strip(),
                         _v=pd.to_numeric(d[cmt], errors='coerce') * 1e-6)
            tab = d.pivot_table(index='_a', columns='_t', values='_v', aggfunc='sum',
                                fill_value=0)
            # Se dejan fuera los atributos que no son montos: un indicador de
            # razón, pasado a millones de pesos, se dibuja como una línea en cero.
            cols = [c for c in tab.columns if tab[c].abs().max() > 1][:6]
            if cols:
                panel('line',
                      tabla_csv(['Año'] + [str(c) for c in cols],
                                [[str(a)] + [f'{tab.loc[a, c]:.2f}' for c in cols]
                                 for a in tab.index]),
                      [('Tipo', 'Gráfico'), ('Titulo', f'{cfg["titulo"]} — montos por concepto'),
                       ('Descripción', 'Suma departamental de cada concepto, por año.'),
                       ('Vertical', 'Millones de pesos'), ('Horizontal', 'Año')])

    if not tipos:
        return 'no se pudo armar ningún panel'
    escribir(os.path.join(carpeta, 'indicador.info'), ficha([
        ('Categoría', 'Política Fiscal'), ('Subcategoría', 'Política Fiscal'),
        ('Titulo', cfg['titulo']),
        ('Descripción', (cfg.get('nota') or cfg['titulo']) + ' Fuente: ' + cfg['fuente'] + '.'),
        ('Etiquetas', 'ND'), ('Fuentes', cfg['fuente']),
    ]))
    escribir(os.path.join(carpeta, 'display.js'), display_js(tipos))
    return None


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--destino', default=OUT)
    args = ap.parse_args()
    destino = args.destino if os.path.isabs(args.destino) else os.path.join(BASE, args.destino)
    os.makedirs(destino, exist_ok=True)
    geo = codigos_municipio()

    ok, fallos = 0, []
    for cod, cfg in FISCAL.items():
        error = generar(cod, cfg, destino, geo)
        if error:
            fallos.append((cod, cfg['titulo'], error))
            print(f'   {cod}  {cfg["titulo"][:46]:48} {error}')
        else:
            paneles = sum(1 for i in range(1, 6)
                          if os.path.isfile(os.path.join(destino, cod, f'{i}.csv')))
            ok += 1
            print(f'   {cod}  {cfg["titulo"][:46]:48} {paneles} panel(es)')
    print(f'\ncon datos: {ok}   sin resolver: {len(fallos)}')
    print('->', os.path.relpath(destino, BASE))


if __name__ == '__main__':
    main()
