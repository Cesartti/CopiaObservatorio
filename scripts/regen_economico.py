# -*- coding: utf-8 -*-
"""
Genera los indicadores del Observatorio Económico desde el catálogo maestro.

Sigue el mismo enfoque de regen_social.py y regen_genero.py: el maestro dice
qué indicadores existen, con qué código y de qué hoja salen; el libro
«BASE DX OBS ECONÓMICO.xlsx» pone los datos. Escribe en una carpeta de staging,
nunca directamente sobre el sitio, para poder comparar antes de publicar.

Por qué hacía falta: el micrositio muestra «0 indicadores» en Minería, Turismo,
Política fiscal y Calidad de vida. No es un fallo de la página: esos
indicadores nunca se crearon. El maestro declara 133 para Económico y el portal
publica 87, con dos numeraciones distintas conviviendo —96 códigos del maestro
sin publicar y 50 publicados que no figuran en el maestro—. Por eso este script
admite filtrar por categoría: permite ir cargando por bloques y revisando, en
vez de volcar 133 indicadores de un golpe sobre una numeración que choca.

Diferencias con el de Social: aquí las series rara vez traen sexo y sí traen
dimensiones propias del dato económico —mineral, país de destino, actividad—,
así que la segunda gráfica sale de la columna categórica más informativa.

Uso:
    python scripts/regen_economico.py --categoria Minería Turismo
    python scripts/regen_economico.py            # todas las categorías
"""
import argparse
import collections
import json
import os
import re
import shutil
import unicodedata
import warnings

import pandas as pd

warnings.filterwarnings('ignore')

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SEEDS = os.path.join(BASE, 'database', 'seeds')
MASTER = os.path.join(SEEDS, 'Base de datos indicadores Red de Observatorios Abril 2026.xlsx')
LIBRO = os.path.join(SEEDS, 'BASE DX OBS ECONÓMICO.xlsx')
OUT = os.path.join(BASE, 'website', 'indicador_staging_economico')

ANIO_MIN, ANIO_MAX = 2015, 2026


def norm(s):
    return unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower().strip()


def tokens(s):
    # Se quita la «s» final: el maestro escribe «Hidrocarburos» y la hoja
    # «Hidrocarburo», y sin esto el parecido queda por debajo del umbral.
    return {t[:-1] if t.endswith('s') and len(t) > 4 else t
            for t in re.findall(r'[a-z0-9]+', norm(s)) if len(t) >= 4}


# --------------------------------------------------------------- hojas
hojas = {}
for h in pd.ExcelFile(LIBRO).sheet_names:
    hojas[norm(h)] = h


def resolver_hoja(nombre):
    """El maestro escribe el nombre de la hoja a mano: «Producción Hidrocarburos»
    por «Producción Hidrocarburo», «Accidentalidad minera» por «Accidentalidad
    Mínera». Se resuelve por parecido de palabras."""
    n = norm(nombre)
    if n in hojas:
        return hojas[n]
    nt = tokens(nombre)
    mejor = (0.0, None)
    for k, real in hojas.items():
        kt = tokens(k)
        if not kt or not nt:
            continue
        # «Presupuestal» y «Presup» son la misma palabra abreviada: cuentan como
        # coincidencia si una empieza por la otra.
        comunes = sum(1 for a in nt if any(a.startswith(b) or b.startswith(a) for b in kt))
        j = comunes / max(len(nt | kt) - comunes + comunes, 1)
        j = max(j, len(nt & kt) / len(nt | kt))
        if j > mejor[0]:
            mejor = (j, real)
    return mejor[1] if mejor[0] >= 0.34 else None


def columna(df, *claves, exacto=None):
    for c in df.columns:
        if exacto and norm(c) == exacto:
            return c
    for c in df.columns:
        if any(k in norm(c) for k in claves):
            return c
    return None


def col_anio(df):
    return (columna(df, exacto='ano') or columna(df, exacto='anio')
            or columna(df, 'ano de fecha', 'ano de', 'vigencia', 'anio', 'year')
            or columna(df, 'ano', 'fecha'))


def col_valor(df):
    return columna(df, 'valor', 'cif', 'fob', 'cantidad', 'produccion', 'total',
                   'monto', 'numero de', 'casos')


def col_municipio(df):
    return columna(df, 'municipio', 'muncipio')


def col_dimension(df, usadas):
    """Columna categórica con pocas clases, para la segunda gráfica."""
    mejor = (0, None)
    for c in df.columns:
        if c in usadas:
            continue
        if not any(k in norm(c) for k in ('mineral', 'pais', 'producto', 'actividad',
                                          'sector', 'tipo', 'clase', 'descripcion',
                                          'categoria', 'subpartida', 'grupo')):
            continue
        n = df[c].dropna().astype(str).str.strip().nunique()
        if 1 < n <= 15 and n > mejor[0]:
            mejor = (n, c)
    return mejor[1]


def solo_boyaca(df):
    """
    Deja únicamente las filas del departamento.

    Varias hojas traen todos los departamentos y el total nacional. Sin este
    recorte la serie sumaría al país entero y el indicador diría cualquier cosa.
    """
    for c in df.columns:
        if norm(c) in ('departamento', 'dpto', 'nombre departamento'):
            v = df[c].astype(str).apply(norm)
            if v.str.contains('boyaca').any():
                return df[v.str.contains('boyaca')]
    return df


def filtrar_indicador(df, nombre):
    """
    Aísla las filas del indicador pedido en las hojas de formato largo.

    Hojas como «ECV HCAMP» apilan decenas de indicadores distintos en la misma
    tabla y los distinguen por una columna «INDICADOR». Si no se filtra, los
    dieciocho indicadores que salen de esa hoja quedarían con la misma gráfica.
    """
    col = next((c for c in df.columns if norm(c) in ('indicador', 'nombre indicador')), None)
    if col is None:
        return df, None
    objetivo = tokens(nombre)
    mejor = (0.0, None)
    for v in df[col].dropna().astype(str).unique():
        vt = tokens(v)
        if not vt:
            continue
        j = len(objetivo & vt) / len(objetivo | vt)
        if j > mejor[0]:
            mejor = (j, v)
    if mejor[0] >= 0.3 and mejor[1] is not None:
        return df[df[col].astype(str) == mejor[1]], mejor[1]
    return df, None


def unidades_mezcladas(df, dim):
    """
    ¿Las filas están en unidades distintas?

    Si lo están, el total anual no se puede calcular: sumar toneladas de carbón
    con quilates de esmeralda y metros cúbicos de arena da un número que no
    significa nada. En ese caso solo se publica la serie desagregada.

    Se detecta de dos formas: por una columna de unidad con más de un valor, o
    porque la propia dimensión lleva la unidad entre paréntesis, como en
    «PRODUCCIÓN DE GAS … (MILLONES DE PIES CÚBICOS)» frente a
    «PRODUCCIÓN DE PETRÓLEO … (BARRILES)».
    """
    for c in df.columns:
        if 'unidad' in norm(c) and df[c].astype(str).str.strip().nunique() > 1:
            return True
    if dim is not None:
        parentesis = {re.sub(r'\s+', ' ', m.group(1)).strip().lower()
                      for v in df[dim].dropna().astype(str).unique()
                      for m in [re.search(r'\(([^)]*)\)', v)] if m}
        if len(parentesis) > 1:
            return True
    return False


def unidad_unica(df):
    """Devuelve la unidad de la hoja y si hay que promediar en vez de sumar.

    Las hojas que traen columna de unidad lo dicen de frente: «Miles», «Pesos»,
    «Promedio», «Porcentaje». Sumar los dos primeros tiene sentido; sumar los
    dos ultimos no, porque un promedio de promedios se calcula promediando y un
    porcentaje de categorias que no son partes de un mismo total no suma 100.
    """
    for c in df.columns:
        if 'unidad' in norm(c):
            vals = df[c].dropna().astype(str).str.strip()
            vals = vals[vals.str.lower().isin(('nan', '')) == False]
            if vals.nunique() == 1:
                u = vals.iloc[0]
                return u, any(k in norm(u) for k in
                              ('promedio', 'porcentaje', 'indice', 'tasa', 'puntaje', 'razon'))
    return None, False


def anios(df, c):
    num = pd.to_numeric(df[c], errors='coerce')
    if num.between(1990, 2035).mean() > 0.4:
        return num.where(num.between(1990, 2035))
    return pd.to_datetime(df[c], errors='coerce').dt.year


def escribir(p, c):
    open(p, 'w', encoding='utf-8', newline='\n').write(c)


def campo(v):
    """Encierra en comillas lo que lleve coma o comillas. Hay categorías del
    DANE que las traen dentro («Negro/a, mulato/a, afrodescendiente…») y sin
    comillas parten la fila en columnas que no existen."""
    t = str(v).strip()
    if any(ch in t for ch in ',"\n'):
        return '"' + t.replace('"', '""') + '"'
    return t


def unificar(serie):
    """Junta las categorías que solo se diferencian en mayúsculas o tildes
    («Total Personas» y «Total personas» son la misma), quedándose con la
    primera forma que aparece."""
    canon = {}
    for v in serie:
        canon.setdefault(norm(v), str(v).strip())
    return serie.map(lambda v: canon[norm(v)])


def ficha(pares):
    return ''.join(f'{k}:{v}\n' for k, v in pares)


def display_js(tipos):
    clases = []
    for i, t in enumerate(tipos, 1):
        if t == 'column':
            cuerpo = ("  getOptions(info){ return { hAxis:{title:info['horizontal']}, "
                      "vAxis:{title:info['vertical']}, legend:{position:'none'}, "
                      "bar:{groupWidth:'70%'} }; }\n"
                      "  getType(div){ return new google.visualization.ColumnChart(div); }")
        else:
            cuerpo = ("  getOptions(info){ return { hAxis:{title:info['horizontal']}, "
                      "vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }\n"
                      "  getType(div){ return new google.visualization.LineChart(div); }")
        clases.append('class Chart%d extends AbstractChart{\n%s\n}' % (i, cuerpo))
    lista = ','.join(f'Chart{i}' for i in range(1, len(tipos) + 1))
    return ('\n\n'.join(clases) +
            f"\n\nclass Display extends AbstractDisplay{{\n  constructor(){{ "
            f"super('corechart',[{lista}]); }}\n}}\n")


_cache = {}


def leer(hoja):
    if hoja not in _cache:
        df = pd.read_excel(LIBRO, sheet_name=hoja)
        # Al limpiar espacios pueden quedar nombres repetidos («Mineral» y
        # «Mineral »); se numeran para que df[col] siga devolviendo una Serie.
        vistos, limpias = {}, []
        for c in df.columns:
            n = str(c).strip()
            vistos[n] = vistos.get(n, 0) + 1
            limpias.append(n if vistos[n] == 1 else f'{n} ({vistos[n]})')
        df.columns = limpias
        _cache[hoja] = df
    return _cache[hoja]


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--categoria', nargs='*', default=None,
                    help='categorías del maestro a generar (por defecto todas)')
    args = ap.parse_args()

    dfm = pd.read_excel(MASTER, sheet_name='Hoja1', dtype=str).fillna('')
    dfm.columns = [str(c).strip() for c in dfm.columns]
    eco = dfm[dfm['Dimensión'].apply(lambda x: 'econ' in norm(x))].copy()
    if args.categoria:
        pedidas = {norm(c) for c in args.categoria}
        eco = eco[eco['Categoría'].apply(lambda x: norm(x) in pedidas)]
        if eco.empty:
            disponibles = sorted({c.strip() for c in dfm[dfm['Dimensión'].apply(
                lambda x: 'econ' in norm(x))]['Categoría']})
            raise SystemExit('Ninguna categoría coincide. Disponibles:\n   '
                             + '\n   '.join(disponibles))

    print(f'indicadores a generar: {len(eco)}')
    os.makedirs(OUT, exist_ok=True)
    # Cuántos indicadores comparten cada hoja: si son varios hay que filtrar.
    uso = collections.Counter(norm(r['Nombre hoja base dx']) for _, r in eco.iterrows())

    hechos, pendientes = 0, []
    for _, r in eco.iterrows():
        cod = str(r['No indicador']).split('.')[0].strip()
        if not re.match(r'^\d{4}$', cod):
            continue
        nombre = str(r['Nombre indicador']).strip()
        carpeta = os.path.join(OUT, cod)
        os.makedirs(carpeta, exist_ok=True)
        info_indicador = ficha([
            ('Categoría', str(r['Categoría']).strip()),
            ('Descripción', nombre), ('Titulo', nombre),
            ('Subcategoría', str(r['Categoría']).strip()), ('Etiquetas', 'ND'),
            ('Fuentes', str(r['Fuente']).strip() or 'BASE DX OBS ECONÓMICO'),
        ])

        hoja = resolver_hoja(r['Nombre hoja base dx'])
        if not hoja:
            pendientes.append((cod, nombre[:40], 'hoja no encontrada: ' + str(r['Nombre hoja base dx'])))
            continue
        try:
            d = leer(hoja)
            d = solo_boyaca(d)
            d, fila_ind = filtrar_indicador(d, nombre)
            if len(d) == 0:
                pendientes.append((cod, nombre[:40], f'sin filas tras filtrar en {hoja}'))
                continue
            tipos = []
            ca, cv = col_anio(d), col_valor(d)
            if ca:
                s = d.assign(_a=anios(d, ca)).dropna(subset=['_a'])
                s['_a'] = s['_a'].astype(int)
                s = s[(s['_a'] >= ANIO_MIN) & (s['_a'] <= ANIO_MAX)]
                if len(s):
                    cd = col_dimension(s, {ca, cv})
                    # Si las filas vienen en unidades distintas no se publica un
                    # total: sumar toneladas con quilates no significa nada.
                    mezcla = unidades_mezcladas(s, cd)
                    if not mezcla:
                        # Un promedio, un porcentaje o un índice no se suman: la
                        # suma de los años promedio de educación por grupo de
                        # edad daba 30,8 años de escolaridad.
                        unidad, promediar = unidad_unica(s)
                        if cv:
                            gr = s.assign(_v=pd.to_numeric(s[cv], errors='coerce')
                                          .fillna(0)).groupby('_a')['_v']
                            g = gr.mean() if promediar else gr.sum()
                        else:
                            g = s.groupby('_a').size()
                        etiqueta = 'Promedio' if promediar else 'Valor'
                        escribir(os.path.join(carpeta, '1.csv'),
                                 f'Año,{etiqueta}\n'
                                 + ''.join(f'{int(a)},{v:g}\n' for a, v in g.items()))
                        escribir(os.path.join(carpeta, '1.info'), ficha([
                            ('Titulo', f'{nombre} — serie anual'),
                            ('Descripción', 'Promedio de las categorías de la fuente, año por '
                                            'año.' if promediar else 'Evolución anual.'),
                            ('Vertical', unidad or etiqueta), ('Horizontal', 'Año')]))
                        tipos.append('line')

                    # Con unidades mezcladas tampoco sirve una sola gráfica: en
                    # un mismo eje, los quilates de esmeralda y las toneladas de
                    # carbón no se pueden comparar. Se hace una por unidad.
                    cu = next((c for c in s.columns if 'unidad' in norm(c)
                               and s[c].astype(str).nunique() > 1), None)
                    if mezcla and cd and cu:
                        for unidad in sorted(s[cu].dropna().astype(str).str.strip().unique()):
                            su = s[s[cu].astype(str).str.strip() == unidad]
                            su = su.assign(_d=unificar(
                                su[cd].fillna('Sin dato').astype(str).str.strip()))
                            tab = (su.assign(_v=pd.to_numeric(su[cv], errors='coerce').fillna(0))
                                   .pivot_table(index='_a', columns='_d', values='_v',
                                                aggfunc='sum', fill_value=0)
                                   if cv else su.groupby(['_a', '_d']).size().unstack(fill_value=0))
                            cols = [c for c in tab.columns
                                    if str(c).lower() not in ('sin dato', 'nan', '')][:8]
                            if not cols or not len(tab):
                                continue
                            n = len(tipos) + 1
                            escribir(os.path.join(carpeta, f'{n}.csv'),
                                     'Año,' + ','.join(campo(c) for c in cols) + '\n'
                                     + ''.join(str(int(a)) + ',' +
                                               ','.join(f'{tab.loc[a, c]:g}' for c in cols) + '\n'
                                               for a in tab.index))
                            escribir(os.path.join(carpeta, f'{n}.info'), ficha([
                                ('Titulo', f'{nombre} — en {unidad.lower()}'),
                                ('Descripción', f'Solo las categorías medidas en {unidad.lower()}; '
                                                'las demás unidades van en su propia gráfica.'),
                                ('Vertical', unidad), ('Horizontal', 'Año')]))
                            tipos.append('line')
                        cd = None       # ya quedó cubierto por unidad

                    if cd:
                        s['_d'] = unificar(s[cd].fillna('Sin dato').astype(str).str.strip())
                        tab = (s.assign(_v=pd.to_numeric(s[cv], errors='coerce').fillna(0))
                               .pivot_table(index='_a', columns='_d', values='_v',
                                            aggfunc='sum', fill_value=0)
                               if cv else s.groupby(['_a', '_d']).size().unstack(fill_value=0))
                        cols = [c for c in tab.columns
                                if str(c).lower() not in ('sin dato', 'nan', '')][:8]
                        if cols:
                            n = len(tipos) + 1
                            escribir(os.path.join(carpeta, f'{n}.csv'),
                                     'Año,' + ','.join(campo(c) for c in cols) + '\n'
                                     + ''.join(str(int(a)) + ',' +
                                               ','.join(f'{tab.loc[a, c]:g}' for c in cols) + '\n'
                                               for a in tab.index))
                            nota = (' Las categorías van en unidades distintas, '
                                    'así que no se presentan sumadas.' if mezcla else '')
                            escribir(os.path.join(carpeta, f'{n}.info'), ficha([
                                ('Titulo', f'{nombre} — por {str(cd).lower()}'),
                                ('Descripción', f'Desagregación por {str(cd).lower()}.' + nota),
                                ('Vertical', 'Valor'), ('Horizontal', 'Año')]))
                            tipos.append('line')

            if not tipos:
                cm = col_municipio(d)
                if cm:
                    m = d.copy()
                    m[cm] = m[cm].fillna('').astype(str).str.strip().str.title()
                    m = m[~m[cm].str.lower().isin(['', 'nan', 'sin dato'])]
                    g = ((m.assign(_v=pd.to_numeric(m[cv], errors='coerce').fillna(0))
                          .groupby(cm)['_v'].sum() if cv else m.groupby(cm).size())
                         .sort_values(ascending=False).head(12))
                    if len(g):
                        escribir(os.path.join(carpeta, '1.csv'),
                                 'Municipio,Valor\n'
                                 + ''.join(f'{campo(k)},{v:g}\n' for k, v in g.items()))
                        escribir(os.path.join(carpeta, '1.info'), ficha([
                            ('Titulo', f'{nombre} — por municipio'),
                            ('Descripción', 'Principales municipios.'),
                            ('Vertical', 'Valor'), ('Horizontal', 'Municipio')]))
                        tipos.append('column')

            if tipos:
                escribir(os.path.join(carpeta, 'indicador.info'), info_indicador)
                escribir(os.path.join(carpeta, 'display.js'), display_js(tipos))
                hechos += 1
                print(f'   {cod}  {nombre[:46]:48} hoja «{hoja[:22]}»  {len(tipos)} gráfica(s)')
            else:
                pendientes.append((cod, nombre[:40], 'sin datos extraíbles de ' + hoja))
        except Exception as e:
            pendientes.append((cod, nombre[:40], f'ERROR {type(e).__name__}: {str(e)[:50]}'))

    # Las carpetas de los indicadores que no se resolvieron se borran: si se
    # dejan, el portal los lista como indicadores sin una sola gráfica, que es
    # justo el problema que arrastran los nueve de Social y Género.
    for d in sorted(os.listdir(OUT)):
        if not os.path.isfile(os.path.join(OUT, d, 'display.js')):
            shutil.rmtree(os.path.join(OUT, d), ignore_errors=True)

    print(f'\ncon datos: {hechos}   sin resolver: {len(pendientes)}')
    for c, n, m in pendientes:
        print(f'   {c}  {n:42} {m}')
    os.makedirs(os.path.join(BASE, 'reportes'), exist_ok=True)
    json.dump(pendientes, open(os.path.join(BASE, 'reportes', 'regen_economico_pendientes.json'),
                               'w', encoding='utf-8'), ensure_ascii=False, indent=1)
    print(f'\n-> {os.path.relpath(OUT, BASE)}')


if __name__ == '__main__':
    main()
