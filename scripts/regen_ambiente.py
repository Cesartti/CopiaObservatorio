# -*- coding: utf-8 -*-
"""
Genera los indicadores del Observatorio Ambiental (códigos 31xx–35xx) desde
database/seeds/BD DX OBS AMBIENTAL.xlsx (+ reportes/ambiental_2026/Respuesta.xlsx
de la Secretaría de Ambiente), organizados en las 5 categorías del tablero
Power BI oficial (agosto 2026). Escribe en STAGING: website/indicador_staging_ambiente/
"""
import os, re, json, unicodedata, collections
import pandas as pd

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DX = os.path.join(BASE, 'database', 'seeds', 'BD DX OBS AMBIENTAL.xlsx')
RESP = os.path.join(BASE, 'reportes', 'ambiental_2026', 'Respuesta.xlsx')
GEO = os.path.join(BASE, 'website', 'assets', 'js', 'boyaca_low.js')
OUT = os.path.join(BASE, 'website', 'indicador_staging_ambiente')

CAT = {
    1: 'Ecosistemas estratégicos y biodiversidad',
    2: 'Recurso hídrico y saneamiento ambiental',
    3: 'Gobernanza, control y gestión ambiental',
    4: 'Salud ambiental',
    5: 'Calidad ambiental y servicios públicos',
}
SRC_CORPO = 'CORPOBOYACÁ – Sistema de Información Geográfica y bases de datos de expedientes (datos.gov.co), corte 2025'
SRC_MINVIV = 'Ministerio de Vivienda, Ciudad y Territorio – Monitoreo de recursos SGP-APSB (informes 2020–2025)'
SRC_SIVIGILA = 'Instituto Nacional de Salud – SIVIGILA (portal público), 2020–2025'
SRC_SIEDCO = 'Policía Nacional – SIEDCO, base entregada por la Secretaría de Gobierno de Boyacá (2004 a mayo de 2026)'
SRC_UNGRD = 'Unidad Nacional para la Gestión del Riesgo de Desastres – Consolidado de atención de emergencias, 2020–2025'
SRC_UPME = 'UPME – Sistema de Información Minero Energético (SIMEC), Índice de Cobertura de Energía Eléctrica 2020–2024'
SRC_ESPB = 'Empresa de Servicios Públicos de Boyacá (ESPB) – Plan Departamental de Aguas, 2023–2025'
SRC_AIRE = 'Secretaría de Ambiente y Desarrollo Sostenible / CORPOBOYACÁ – Red de monitoreo de calidad del aire, 2020–2025'
SRC_SEC = 'Secretaría de Ambiente y Desarrollo Sostenible de Boyacá – Dirección de Gestión del Recurso Hídrico y Saneamiento Básico (oficio 28/08/2026)'

def norm(s):
    return unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower().strip()

# ---------- códigos DANE desde el geojson ----------
geo_txt = open(GEO, encoding='utf-8').read()
GEO_MAP = {}   # norm(name) -> (id, Name)
for gid, gname in re.findall(r'"id":"(\d{5})",\s*"name":\s*"([^"]+)"', geo_txt):
    GEO_MAP[norm(gname)] = (gid, gname.title())
GEO_BY_ID = {v[0]: v[1] for v in GEO_MAP.values()}
ALIAS = {'guican': 'guican de la sierra', 'cocuy': 'el cocuy', 'espino': 'el espino',
         'villa de leiva': 'villa de leyva', 'labranzagrande': 'labranza grande'}
SKIP_NAMES = {'departamento', 'nan', '', 'boyaca', 'total'}
UNMATCHED = collections.Counter()
def dane_by_name(name):
    n = norm(name)
    if n in SKIP_NAMES: return None
    n = ALIAS.get(n, n)
    if n in GEO_MAP: return GEO_MAP[n][0]
    for k in GEO_MAP:                      # variantes: "SAN PABLO DE BORBUR" vs "san pablo borbur"
        if k.replace(' de ', ' ') == n.replace(' de ', ' '): return GEO_MAP[k][0]
    cands = [k for k in GEO_MAP if k.startswith(n) or n.startswith(k)]
    if len(cands) == 1: return GEO_MAP[cands[0]][0]
    UNMATCHED[name] += 1
    return None
def dane_code(v):
    s = str(v).strip()
    if s in ('', 'nan', 'None'): return None
    s = s.split('.')[0]                     # 15087.0 -> 15087 (columnas leídas como float)
    s = re.sub(r'\D', '', s).zfill(5)
    return s if len(s) == 5 and s in GEO_BY_ID else None

# ---------- utilidades de escritura ----------
def w(p, c): open(p, 'w', encoding='utf-8', newline='\n').write(c)
def info(pairs): return ''.join(f'{k}:{v}\n' for k, v in pairs)
def fmt(v):
    if v is None or (isinstance(v, float) and pd.isna(v)): return ''
    if isinstance(v, (int,)) or (isinstance(v, float) and float(v).is_integer()): return str(int(v))
    return f'{float(v):.2f}'.rstrip('0').rstrip('.')
def csvq(s):
    s = str(s).replace('"', "'")
    return f'"{s}"' if (',' in s) else s

class Ind:
    def __init__(self, code, cat, title, desc, source, sub=None):
        self.code, self.cat, self.title, self.desc, self.source = code, cat, title, desc, source
        self.sub = sub or CAT[cat]
        self.charts = []   # (kind, title, desc, vert, horiz, header, rows, opts)
    def chart(self, kind, title, desc, vert, horiz, header, rows, **opts):
        if not rows: return
        self.charts.append((kind, title, desc, vert, horiz, header, rows, opts))
    def map(self, title, desc, vert, valname, rows, time=True):
        """rows: (geo, val[, year])"""
        rows = [r for r in rows if r[0]]
        if not rows: return
        header = ['geo', 'Municipio', valname] + (['Año'] if time else [])
        body = [[r[0], GEO_BY_ID[r[0]], fmt(r[1])] + ([str(int(r[2]))] if time else []) for r in rows]
        self.charts.append(('map', title, desc, vert, '', header, body, {'val': valname, 'time': 'Año' if time else None}))

def display_js(charts):
    cl = []; need_google = False
    for i, ch in enumerate(charts, 1):
        kind, opts = ch[0], ch[7]
        if kind == 'map':
            t = f"'{opts['time']}'" if opts.get('time') else 'null'
            cl.append("class Chart%d extends AbstractMap{\n\tconstructor(info,csv,chart){\n\t\tsuper(info,csv,chart,'%s',%s,null,'geo',false);\n\t}\n}" % (i, opts['val'], t))
            continue
        need_google = True
        yearx = opts.get('yearx', False)
        hfmt = ",format:'####'" if yearx else ''
        if kind == 'line':
            body = ("\tgetOptions(info){ return { hAxis:{title:info['horizontal']%s}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }\n"
                    "\tgetType(div){ return new google.visualization.LineChart(div); }") % hfmt
        elif kind == 'bar':
            body = ("\tgetOptions(info){ return { hAxis:{title:info['vertical']}, vAxis:{title:info['horizontal']}, legend:{position:'none'}, chartArea:{left:230,top:20,width:'62%%',height:'85%%'} }; }\n"
                    "\tgetType(div){ return new google.visualization.BarChart(div); }")
        else:  # column
            stacked = ", isStacked:true" if opts.get('stacked') else ''
            legend = "legend:{position:'none'}" if opts.get('single', True) else "legend:{position:'top'}"
            body = ("\tgetOptions(info){ return { hAxis:{title:info['horizontal']%s}, vAxis:{title:info['vertical']}, %s, bar:{groupWidth:'70%%'}%s }; }\n"
                    "\tgetType(div){ return new google.visualization.ColumnChart(div); }") % (hfmt, legend, stacked)
        cl.append("class Chart%d extends AbstractChart{\n%s\n}" % (i, body))
    lst = ','.join(f'Chart{i}' for i in range(1, len(charts) + 1))
    pk = "['corechart']" if need_google else 'null'
    return '\n\n'.join(cl) + f"\n\nclass Display extends AbstractDisplay{{\n\tconstructor(){{\n\t\tsuper({pk},[{lst}]);\n\t}}\n}}\n"

def write_ind(ind):
    cp = os.path.join(OUT, str(ind.code)); os.makedirs(cp, exist_ok=True)
    w(os.path.join(cp, 'indicador.info'), info([
        ('Categoría', CAT[ind.cat]), ('Descripción', ind.desc), ('Titulo', ind.title),
        ('Subcategoría', ind.sub), ('Etiquetas', 'Observatorio Ambiental'), ('Fuentes', ind.source)]))
    for n, (kind, title, desc, vert, horiz, header, rows, opts) in enumerate(ind.charts, 1):
        w(os.path.join(cp, f'{n}.csv'), ','.join(csvq(h) for h in header) + '\n' +
          ''.join(','.join(csvq(c) for c in r) + '\n' for r in rows))
        pairs = [('Tipo', 'Mapa' if kind == 'map' else 'Gráfico'), ('Titulo', title), ('Descripción', desc), ('Vertical', vert)]
        if horiz: pairs.append(('Horizontal', horiz))
        w(os.path.join(cp, f'{n}.info'), info(pairs))
    w(os.path.join(cp, 'display.js'), display_js(ind.charts))

# ---------- lectura ----------
def sheet(name):
    df = pd.read_excel(DX, sheet_name=name)
    df.columns = [str(c).strip() for c in df.columns]
    return df
def col(df, *needles, exact=None):
    for c in df.columns:
        if exact and norm(c) == norm(exact): return c
    for c in df.columns:
        if any(norm(n) in norm(c) for n in needles): return c
    return None
def years(df, ycol, lo=2010, hi=2026):
    y = pd.to_numeric(df[ycol], errors='coerce')
    if y.notna().mean() < 0.5:
        y = pd.to_datetime(df[ycol], errors='coerce').dt.year
    return y.where((y >= lo) & (y <= hi))
def with_year(df, ycol, lo=2010, hi=2026):
    d = df.assign(_y=years(df, ycol, lo, hi)).dropna(subset=['_y']).copy(); d['_y'] = d['_y'].astype(int); return d
def with_geo(df, code_col=None, name_col=None):
    if code_col and code_col in df.columns:
        g = df[code_col].map(dane_code)
    else:
        g = df[name_col].astype(str).map(dane_by_name)
    return df.assign(_g=g).dropna(subset=['_g']).copy()

def rows_year(s):            # Series index year
    return [[str(int(y)), fmt(v)] for y, v in s.sort_index().items()]
def rows_pivot(tab):         # DataFrame index year, columns series
    return [[str(int(y))] + [fmt(tab.loc[y, c]) for c in tab.columns] for y in sorted(tab.index)]
def rows_cat(s, top=None):
    s = s.sort_values(ascending=False)
    if top: s = s.head(top)
    return [[str(k), fmt(v)] for k, v in s.items()]
def map_rows(df, vcol=None, agg='sum', ycol=None):
    if ycol:
        g = (df.groupby(['_g', '_y'])[vcol].agg(agg) if vcol else df.groupby(['_g', '_y']).size())
        return [(k[0], v, k[1]) for k, v in g.items()]
    g = (df.groupby('_g')[vcol].agg(agg) if vcol else df.groupby('_g').size())
    return [(k, v) for k, v in g.items()]
def pivot_rows(tab):         # index categoría (texto), columns series
    return [[str(p)] + [fmt(tab.loc[p, c]) for c in tab.columns] for p in tab.index]

INDS = []
def add(ind): INDS.append(ind); return ind

# =====================================================================
# 1. ECOSISTEMAS ESTRATÉGICOS Y BIODIVERSIDAD (CORPOBOYACÁ)
# =====================================================================
hum = with_geo(sheet('Humedales'), name_col='mun_nombre')
i = add(Ind(3101, 1, 'Área de humedales por municipio',
    'Superficie (hectáreas) de humedales delimitados por CORPOBOYACÁ dentro de cada municipio de su jurisdicción. Los humedales regulan el ciclo hídrico, almacenan carbono y albergan biodiversidad; su extensión por municipio orienta las acciones de conservación.', SRC_CORPO, 'Humedales'))
i.map('Área de humedales por municipio (ha)', 'Hectáreas de humedal en cada municipio de la jurisdicción de CORPOBOYACÁ.', 'Hectáreas', 'Hectareas', map_rows(hum, 'AreaMunHumedal'), time=False)
i.chart('column', 'Área de humedales por provincia', 'Suma del área de humedales (ha) por provincia.', 'Hectáreas', 'Provincia', ['Provincia', 'Hectáreas'], rows_cat(hum.groupby('Provincia')['AreaMunHumedal'].sum()))
i.chart('bar', 'Principales humedales por área', 'Los 15 humedales con mayor superficie (ha).', 'Hectáreas', 'Humedal', ['Humedal', 'Hectáreas'], rows_cat(hum.groupby('Humedal')['AreaMunHumedal'].sum(), 15))

acu = with_geo(sheet('AcuiferoTunja'), code_col='mun_codigo')
i = add(Ind(3102, 1, 'Área de acuíferos (sistema acuífero de Tunja) por municipio',
    'Superficie (hectáreas) de las unidades del sistema acuífero de Tunja y municipios vecinos, según la delimitación de CORPOBOYACÁ. Los acuíferos son reserva estratégica de agua subterránea para el abastecimiento humano y productivo.', SRC_CORPO, 'Acuíferos'))
i.map('Área en acuífero por municipio (ha)', 'Hectáreas del municipio ubicadas sobre unidades acuíferas.', 'Hectáreas', 'Hectareas', map_rows(acu, 'AreaenAcuifero'), time=False)
i.chart('column', 'Área de acuíferos por provincia', 'Suma del área acuífera (ha) por provincia.', 'Hectáreas', 'Provincia', ['Provincia', 'Hectáreas'], rows_cat(acu.groupby('Provincia')['AreaenAcuifero'].sum()))
i.chart('column', 'Área por unidad acuífera', 'Área (ha) de cada unidad hidrogeológica (código CORPOBOYACÁ).', 'Hectáreas', 'Unidad', ['Unidad', 'Hectáreas'], rows_cat(acu.groupby('COD')['AreaenAcuifero'].sum(), 12))

ron = with_geo(sheet('Rondas Hidricas'), name_col='mun_nombre')
i = add(Ind(3103, 1, 'Área de rondas hídricas acotadas por municipio',
    'Superficie (hectáreas) de rondas hídricas acotadas por CORPOBOYACÁ (franjas de protección de ríos y quebradas) en cada municipio. La ronda hídrica es una zona de manejo especial que protege el cauce, la ribera y sus ecosistemas.', SRC_CORPO, 'Rondas hídricas'))
i.map('Área de ronda hídrica por municipio (ha)', 'Hectáreas de ronda hídrica acotada en cada municipio.', 'Hectáreas', 'Hectareas', map_rows(ron, 'AreaRondaH'), time=False)
i.chart('column', 'Área por ronda hídrica', 'Área acotada (ha) por cuerpo de agua.', 'Hectáreas', 'Ronda hídrica', ['Ronda', 'Hectáreas'], rows_cat(ron.groupby('RHidrica_N')['AreaRondaH'].sum(), 15))
i.chart('column', 'Área de rondas hídricas por provincia', 'Suma del área de rondas (ha) por provincia.', 'Hectáreas', 'Provincia', ['Provincia', 'Hectáreas'], rows_cat(ron.groupby('Provincia')['AreaRondaH'].sum()))

par = with_geo(sheet('Paramos'), code_col='mun_codigo')
i = add(Ind(3104, 1, 'Área de páramos por municipio y complejo de páramos',
    'Superficie (hectáreas) de ecosistemas de páramo en cada municipio, según la delimitación de los complejos de páramos (Tota–Bijagual–Mamapacha, Guantiva–La Rusia, Sierra Nevada del Cocuy, Iguaque–Merchán, Pisba, Rabanal–Río Bogotá y Altiplano Cundiboyacense). Los páramos son la principal fábrica de agua del departamento.', SRC_CORPO, 'Páramos'))
i.map('Área de páramo por municipio (ha)', 'Hectáreas de páramo en cada municipio.', 'Hectáreas', 'Hectareas', map_rows(par, 'AreaParamo'), time=False)
i.chart('column', 'Área por complejo de páramos', 'Área (ha) de cada complejo de páramos en Boyacá.', 'Hectáreas', 'Complejo', ['Complejo', 'Hectáreas'], rows_cat(par.groupby('par_nom_co')['AreaParamo'].sum()))
i.chart('column', 'Área de páramos por provincia', 'Suma del área de páramo (ha) por provincia.', 'Hectáreas', 'Provincia', ['Provincia', 'Hectáreas'], rows_cat(par.groupby('Provincia')['AreaParamo'].sum()))

bos = with_geo(sheet('Bosque'), name_col='mun_nombre')
be = bos[bos['Cobertura'] == 'Bosque Estable']
i = add(Ind(3105, 1, 'Cobertura de bosque estable por municipio',
    'Superficie (hectáreas) de bosque estable (cobertura boscosa que se mantiene entre periodos de monitoreo) en cada municipio de la jurisdicción de CORPOBOYACÁ, y su participación frente al área municipal.', SRC_CORPO, 'Bosques'))
i.map('Bosque estable por municipio (ha)', 'Hectáreas de bosque estable en cada municipio.', 'Hectáreas', 'Hectareas', map_rows(be, 'AreaEnZona'), time=False)
pct = (be.groupby('_g')['AreaEnZona'].sum() / bos.groupby('_g')['AreaMunici'].first() * 100).dropna()
i.map('Porcentaje del área municipal con bosque estable', 'Proporción (%) del área del municipio cubierta por bosque estable.', 'Porcentaje', 'Porcentaje', [(k, v) for k, v in pct.items()], time=False)
tab = bos[bos['Cobertura'] != 'Sin Información'].pivot_table(index='Provincia', columns='Cobertura', values='AreaEnZona', aggfunc='sum', fill_value=0)
i.chart('column', 'Bosque estable y no bosque por provincia', 'Área (ha) de bosque estable y de no bosque estable por provincia.', 'Hectáreas', 'Provincia', ['Provincia'] + list(tab.columns), pivot_rows(tab), single=False)

afo = with_geo(sheet('Áreas Forestales'), name_col='Municipio')
i = add(Ind(3106, 1, 'Áreas forestales de protección y producción por municipio',
    'Superficie (hectáreas) por tipo de área forestal (protección, producción, áreas protegidas, áreas agrícolas, cuerpos de agua y zona urbana) según la zonificación forestal de CORPOBOYACÁ en cada municipio.', SRC_CORPO, 'Áreas forestales'))
prot = afo[afo['Áreas'].astype(str).str.contains('Protec', na=False)]
i.map('Área forestal de protección por municipio (ha)', 'Hectáreas zonificadas como área forestal de protección.', 'Hectáreas', 'Hectareas', map_rows(prot, 'Suma de area_1'), time=False)
prod = afo[afo['Áreas'].astype(str).str.contains('Producci', na=False) & afo['Áreas'].astype(str).str.startswith('ÁF')]
i.map('Área forestal de producción por municipio (ha)', 'Hectáreas zonificadas como área forestal de producción.', 'Hectáreas', 'Hectareas', map_rows(prod, 'Suma de area_1'), time=False)
i.chart('column', 'Superficie por tipo de área forestal', 'Total departamental (ha) por tipo de zonificación.', 'Hectáreas', 'Tipo de área', ['Tipo', 'Hectáreas'], rows_cat(afo.groupby('Áreas')['Suma de area_1'].sum()))
tab = afo.pivot_table(index='Provincia', columns='Áreas', values='Suma de area_1', aggfunc='sum', fill_value=0)
i.chart('column', 'Áreas forestales por provincia y tipo', 'Área (ha) por provincia y tipo de zonificación.', 'Hectáreas', 'Provincia', ['Provincia'] + list(tab.columns), pivot_rows(tab), single=False, stacked=True)

# =====================================================================
# 2. RECURSO HÍDRICO Y SANEAMIENTO AMBIENTAL
# =====================================================================
def coverage_ind(code, cat, title, desc, src, sh, zona_col, clas_col, sub, zonas):
    """Un indicador por hoja del Excel, con las zonas como gráficas separadas."""
    df0 = sheet(sh); df0 = with_year(df0, col(df0, exact='Año') or col(df0, 'año', 'ano'))
    df0 = with_geo(df0, code_col=col(df0, 'dane'))
    df0['_v'] = pd.to_numeric(df0['Valor'], errors='coerce') * 100
    ind = add(Ind(code, cat, title, desc, src, sub))

    series = {}
    for etiqueta, valor in zonas:
        df = df0[df0[zona_col].astype(str).str.strip().str.lower() == valor].copy() if zona_col else df0.copy()
        d = df.dropna(subset=['_v'])
        if d.empty:
            continue
        ind.map('Cobertura por municipio · %s (%%)' % etiqueta.lower(),
                'Cobertura reportada por municipio y año en la zona %s (use la línea de tiempo).' % etiqueta.lower(),
                'Porcentaje', 'Porcentaje', map_rows(d, '_v', 'mean', ycol='_y'))
        series[etiqueta] = d.groupby('_y')['_v'].mean().round(1)

    if series:
        anios = sorted({a for s_ in series.values() for a in s_.index})
        cols = list(series.keys())
        filas = [[str(a)] + [fmt(series[c].get(a)) if a in series[c].index else '' for c in cols] for a in anios]
        ind.chart('line', 'Cobertura promedio departamental por año',
                  'Promedio simple de la cobertura de los municipios con reporte, por zona.',
                  'Porcentaje', 'Año', ['Año'] + cols, filas, yearx=True)
    if clas_col:
        tab = df0.groupby(['_y', clas_col]).size().unstack(fill_value=0)
        ind.chart('column', 'Municipios por nivel de cobertura y año',
                  'Número de municipios en cada rango de cobertura (todas las zonas).',
                  'Municipios', 'Año', ['Año'] + list(tab.columns), rows_pivot(tab),
                  yearx=True, single=False, stacked=True)
    return ind

coverage_ind(3201, 2, 'Cobertura del servicio de acueducto',
    'Porcentaje de viviendas con servicio de acueducto (cobertura REC reportada al SUI y consolidada por el Ministerio de Vivienda en el monitoreo del SGP-APSB), por municipio, zona y año.',
    SRC_MINVIV, 'COB ACUEDUCTO', 'Zona', 'Cobertura', 'Acueducto', [('Urbana', 'urbana'), ('Rural', 'rural')])
coverage_ind(3203, 2, 'Cobertura del servicio de alcantarillado',
    'Porcentaje de viviendas con servicio de alcantarillado (cobertura REC), por municipio, zona y año. El rango RI/RII/RIII corresponde a la clasificación del monitoreo SGP-APSB.',
    SRC_MINVIV, 'COB ALCANTARILLADO', 'Zona', 'Rango', 'Alcantarillado', [('Urbana', 'urbano'), ('Rural', 'rural')])

cont = sheet('CONTINUIDAD HORAS AGUA'); cont = with_year(cont, 'AÑO'); cont = with_geo(cont, code_col='DANE')
cont['_v'] = pd.to_numeric(cont['VALOR'], errors='coerce'); cd = cont.dropna(subset=['_v'])
i = add(Ind(3205, 2, 'Continuidad del servicio de acueducto (horas/día)',
    'Promedio de horas al día con suministro de agua en la zona urbana, por municipio y año, con su clasificación (continuo ≥ 23 h; suficiente; insuficiente; no satisfactorio) según el monitoreo del SGP-APSB.', SRC_MINVIV, 'Acueducto'))
i.map('Continuidad por municipio (horas/día)', 'Horas promedio de servicio al día por municipio y año.', 'Horas/día', 'Horas', map_rows(cd, '_v', 'mean', ycol='_y'))
i.chart('line', 'Continuidad promedio departamental', 'Promedio de horas/día de los municipios con reporte.', 'Horas/día', 'Año', ['Año', 'Horas/día'], rows_year(cd.groupby('_y')['_v'].mean().round(1)), yearx=True)
tab = cont.groupby(['_y', 'Clasificación']).size().unstack(fill_value=0)
i.chart('column', 'Municipios por clasificación de continuidad', 'Número de municipios por categoría de continuidad y año.', 'Municipios', 'Año', ['Año'] + list(tab.columns), rows_pivot(tab), yearx=True, single=False, stacked=True)

star = sheet('TRATAMIENTO AGUAS'); star = with_year(star, 'AÑO'); star = with_geo(star, code_col='DANE')
star['_v'] = (star['VALOR'].astype(str).str.strip().str.lower() == 'si').astype(int)
i = add(Ind(3206, 2, 'Municipios con sistema de tratamiento de aguas residuales (STAR) reportado',
    'Municipios que reportaron al SUI contar con al menos un Sistema de Tratamiento de Aguas Residuales (STAR) en operación durante la vigencia. Indicador clave del saneamiento de vertimientos urbanos.', SRC_MINVIV, 'Saneamiento'))
i.map('Municipios con STAR reportado (1 = sí, 0 = no)', 'Reporte de STAR por municipio y año.', 'Reporta STAR', 'STAR', map_rows(star, '_v', 'max', ycol='_y'))
g = star.groupby('_y')['_v'].agg(['sum', 'count'])
i.chart('column', 'Municipios con STAR por año', 'Cantidad de municipios que reportaron STAR.', 'Municipios', 'Año', ['Año', 'Con STAR', 'Sin STAR'], [[str(y), fmt(r['sum']), fmt(r['count'] - r['sum'])] for y, r in g.iterrows()], yearx=True, single=False, stacked=True)
i.chart('line', 'Porcentaje de municipios con STAR', 'Participación (%) de municipios con STAR reportado.', 'Porcentaje', 'Año', ['Año', 'Porcentaje'], rows_year((g['sum'] / g['count'] * 100).round(1)), yearx=True)

ar = sheet('ACUEDUCTOS RURALES'); ar = with_geo(ar, code_col='COD_DANE')
ar['_s'] = pd.to_numeric(ar['Nº DE SUSCRIPTORES'], errors='coerce').fillna(0)
i = add(Ind(3207, 2, 'Acueductos rurales y suscriptores por municipio',
    'Inventario de acueductos rurales (juntas, asociaciones y comités) del departamento con su número de suscriptores, corporación ambiental de jurisdicción y formalización (registro en cámara de comercio y concesión de aguas).', SRC_SEC, 'Acueductos rurales'))
i.map('Número de acueductos rurales por municipio', 'Cantidad de acueductos rurales inventariados por municipio.', 'Acueductos', 'Acueductos', map_rows(ar), time=False)
i.map('Suscriptores de acueductos rurales por municipio', 'Suma de suscriptores de los acueductos rurales por municipio.', 'Suscriptores', 'Suscriptores', map_rows(ar, '_s'), time=False)
i.chart('column', 'Acueductos rurales por provincia', 'Cantidad de acueductos rurales por provincia.', 'Acueductos', 'Provincia', ['Provincia', 'Acueductos'], rows_cat(ar.groupby('PROVINCIA').size()))
f1 = int(ar['CAMARA DE COMERCIO'].astype(str).str.strip().str.lower().eq('si').sum()); f2 = int(ar['CONCESIÓN DE AGUAS'].astype(str).str.strip().str.lower().eq('si').sum()); n = len(ar)
i.chart('column', 'Formalización de acueductos rurales', 'Acueductos con registro en cámara de comercio y con concesión de aguas frente al total inventariado.', 'Acueductos', 'Condición', ['Condición', 'Sí', 'No'], [['Cámara de comercio', fmt(f1), fmt(n - f1)], ['Concesión de aguas', fmt(f2), fmt(n - f2)]], single=False, stacked=True)
i.chart('column', 'Acueductos rurales por corporación autónoma', 'Distribución por autoridad ambiental de jurisdicción.', 'Acueductos', 'Corporación', ['Corporación', 'Acueductos'], rows_cat(ar.groupby('CORPORACIÓN').size()))

irca = sheet('IRCA MUNICIPIO'); irca = with_year(irca, 'AÑO'); irca = with_geo(irca, code_col='DANE')
irca['_v'] = pd.to_numeric(irca['IRCA'], errors='coerce') * 100
i = add(Ind(3208, 2, 'Índice de Riesgo de la Calidad del Agua para el Consumo Humano (IRCA)',
    'IRCA del agua para consumo humano por municipio, zona y año (Resolución 2115 de 2007). Mide el grado de riesgo de ocurrencia de enfermedades por las características del agua: sin riesgo (0–5), bajo (5,1–14), medio (14,1–35), alto (35,1–80) e inviable sanitariamente (80,1–100).',
    SRC_MINVIV, 'Calidad del agua'))
series_irca = {}
for etiqueta, clave in [('Urbana', 'urbano'), ('Rural nucleada', 'rural')]:
    d = irca[irca['ZONA'].astype(str).str.lower().str.contains(clave)].dropna(subset=['_v'])
    if d.empty:
        continue
    i.map('IRCA por municipio · zona %s (%%)' % etiqueta.lower(),
          'Índice por municipio y año (0 = sin riesgo; más de 80 = inviable sanitariamente).',
          'IRCA (%)', 'IRCA', map_rows(d, '_v', 'mean', ycol='_y'))
    series_irca[etiqueta] = d.groupby('_y')['_v'].mean().round(1)
if series_irca:
    anios = sorted({a for s_ in series_irca.values() for a in s_.index})
    cols = list(series_irca.keys())
    i.chart('line', 'IRCA promedio departamental por zona',
            'Promedio del IRCA de los municipios con reporte.', 'IRCA (%)', 'Año',
            ['Año'] + cols,
            [[str(a)] + [fmt(series_irca[c].get(a)) if a in series_irca[c].index else '' for c in cols] for a in anios],
            yearx=True)
tab = irca.groupby(['_y', 'NIVEL DE RIESGO']).size().unstack(fill_value=0)
order = [c for c in ['Sin riesgo', 'Bajo', 'Medio', 'Alto', 'Inviable sanitariamente', 'Sin información'] if c in tab.columns]
i.chart('column', 'Municipios por nivel de riesgo', 'Número de municipios en cada nivel de riesgo del IRCA por año.',
        'Municipios', 'Año', ['Año'] + order, rows_pivot(tab[order]), yearx=True, single=False, stacked=True)

pda = sheet('VINCULADO PDA'); pda = with_year(pda, 'AÑO'); pda = with_geo(pda, code_col='DANE')
pda['_v'] = (pda['VALOR'].astype(str).str.strip().str.upper() == 'SI').astype(int)
i = add(Ind(3210, 2, 'Municipios vinculados al Plan Departamental de Aguas (PDA)',
    'Municipios vinculados al Plan Departamental para el Manejo Empresarial de los Servicios de Agua y Saneamiento (PDA), cuyo gestor es la Empresa de Servicios Públicos de Boyacá, por año.', SRC_ESPB, 'Plan Departamental de Aguas'))
i.map('Vinculación al PDA (1 = vinculado)', 'Municipios vinculados al PDA por año.', 'Vinculado', 'PDA', map_rows(pda, '_v', 'max', ycol='_y'))
g = pda.groupby('_y')['_v'].agg(['sum', 'count'])
i.chart('column', 'Municipios vinculados y no vinculados', 'Número de municipios vinculados al PDA por año.', 'Municipios', 'Año', ['Año', 'Vinculados', 'No vinculados'], [[str(y), fmt(r['sum']), fmt(r['count'] - r['sum'])] for y, r in g.iterrows()], yearx=True, single=False, stacked=True)

def expedientes_ind(code, cat, sh, title, desc, sub, ycol_hint='AÑO', estado_col='Estado Expediente', lo=2010):
    df = sheet(sh)
    yc = col(df, exact=ycol_hint) or col(df, 'año comp') or col(df, 'año', 'ano', 'fecha')
    d = with_year(df, yc, lo=lo); d = with_geo(d, code_col=col(df, 'cod_dane', 'dane'), name_col=col(df, 'municipio'))
    ind = add(Ind(code, cat, title, desc, SRC_CORPO, sub))
    ind.chart('line', 'Expedientes por año', 'Número de expedientes (trámites) registrados por año.', 'Expedientes', 'Año', ['Año', 'Expedientes'], rows_year(d.groupby('_y').size()), yearx=True)
    ind.map('Expedientes por municipio', 'Número de expedientes por municipio y año.', 'Expedientes', 'Expedientes', map_rows(d, ycol='_y'))
    ec = col(df, exact=estado_col) or col(df, 'estado')
    if ec:
        ind.chart('column', 'Expedientes por estado', 'Distribución de los expedientes según su estado.', 'Expedientes', 'Estado', ['Estado', 'Expedientes'], rows_cat(d.groupby(ec).size(), 10))
    pc = col(df, exact='PROVINCIA') or col(df, 'provincia')
    if pc:
        ind.chart('column', 'Expedientes por provincia', 'Número de expedientes por provincia.', 'Expedientes', 'Provincia', ['Provincia', 'Expedientes'], rows_cat(d.groupby(d[pc].astype(str).str.strip().str.title()).size()))
    return ind
expedientes_ind(3211, 2, 'AGUAS SUPERFICIALES', 'Concesiones de aguas superficiales (expedientes CORPOBOYACÁ)',
    'Expedientes de concesión de aguas superficiales tramitados ante CORPOBOYACÁ, por año, municipio y estado. La concesión es el permiso para usar el agua de ríos, quebradas y nacimientos.', 'Concesiones de agua', ycol_hint='AÑO COMP')
expedientes_ind(3212, 2, 'AGUAS SUBTERRANEAS', 'Concesiones de aguas subterráneas (expedientes CORPOBOYACÁ)',
    'Expedientes de concesión de aguas subterráneas (pozos y aljibes) tramitados ante CORPOBOYACÁ, por año, municipio y estado.', 'Concesiones de agua', lo=2000)
expedientes_ind(3213, 2, 'PERMISO DE VERTIMIENTOS', 'Permisos de vertimientos (expedientes CORPOBOYACÁ)',
    'Expedientes de permiso de vertimiento de aguas residuales a fuentes hídricas o al suelo tramitados ante CORPOBOYACÁ, por año, municipio y estado (corte abril de 2026).', 'Vertimientos', estado_col='Estado')

# =====================================================================
# 3. GOBERNANZA, CONTROL Y GESTIÓN AMBIENTAL
# =====================================================================
expedientes_ind(3301, 3, 'APROVECHAMIENTO FORESTAL', 'Permisos de aprovechamiento forestal (expedientes CORPOBOYACÁ)',
    'Permisos o autorizaciones de aprovechamiento forestal tramitados ante CORPOBOYACÁ, por año, municipio y estado del expediente (activo, inactivo, en evaluación).', 'Control forestal')
expedientes_ind(3302, 3, 'LICENCIAS AMBIENTALES', 'Licencias ambientales (expedientes CORPOBOYACÁ)',
    'Expedientes de licencia ambiental (proyectos de mayor impacto: vías, minería, infraestructura) tramitados ante CORPOBOYACÁ, por año, municipio y estado.', 'Licenciamiento', estado_col='Estado')
expedientes_ind(3303, 3, 'PLANTACIONES FORESTALES', 'Registro de plantaciones forestales protectoras-productoras',
    'Registros de plantaciones forestales ante CORPOBOYACÁ, por año, municipio y estado del expediente.', 'Control forestal')

dl = sheet('Delitos medio amb'); dl = dl[dl['DEPARTAMENTO'].astype(str).str.strip().str.upper() == 'BOYACA']
dl = with_year(dl, 'año', lo=2010); dl = with_geo(dl, code_col='COD_MUNI'); dl['_c'] = pd.to_numeric(dl['CANTIDAD'], errors='coerce').fillna(0)
i = add(Ind(3304, 3, 'Delitos contra los recursos naturales y el medio ambiente',
    'Casos reportados en Boyacá por conductas del Título XI del Código Penal (explotación ilícita de yacimientos mineros, aprovechamiento ilícito de recursos naturales, daños en los recursos naturales y ecocidio, contaminación ambiental, caza y pesca ilegal, tráfico de fauna, entre otros), por año, conducta, zona y municipio.', SRC_SIEDCO, 'Delitos ambientales'))
i.chart('line', 'Casos por año', 'Número de casos reportados por año (2010 a mayo de 2026).', 'Casos', 'Año', ['Año', 'Casos'], rows_year(dl.groupby('_y')['_c'].sum()), yearx=True)
i.chart('bar', 'Casos por conducta', 'Principales conductas delictivas contra el medio ambiente (acumulado).', 'Casos', 'Conducta', ['Conducta', 'Casos'], rows_cat(dl.groupby(dl['DESCRIPCION_CONDUCTA'].astype(str).str.strip().str.capitalize())['_c'].sum(), 10))
i.map('Casos por municipio', 'Casos por municipio y año.', 'Casos', 'Casos', map_rows(dl, '_c', ycol='_y'))
tab = dl.pivot_table(index='_y', columns='ZONA', values='_c', aggfunc='sum', fill_value=0)
i.chart('column', 'Casos por zona (rural/urbana)', 'Distribución de los casos por zona de ocurrencia y año.', 'Casos', 'Año', ['Año'] + [str(c).capitalize() for c in tab.columns], rows_pivot(tab), yearx=True, single=False, stacked=True)

em = sheet('EMERGENCIAS AMB'); em = with_year(em, 'AÑO'); em = with_geo(em, name_col='MUNICIPIO')
for c in ['MUERTOS', 'HERIDOS', 'PERSONAS', 'FAMILIAS', 'HECTAREAS']: em[c] = pd.to_numeric(em[c], errors='coerce').fillna(0)
i = add(Ind(3305, 3, 'Emergencias ambientales y de origen natural atendidas',
    'Eventos de emergencia registrados por la UNGRD en Boyacá (incendios forestales y de cobertura vegetal, movimientos en masa, inundaciones, vendavales, accidentes mineros, desabastecimiento de agua, entre otros), con personas y familias afectadas y hectáreas comprometidas.', SRC_UNGRD, 'Gestión del riesgo'))
i.chart('line', 'Eventos por año', 'Número de emergencias atendidas por año.', 'Eventos', 'Año', ['Año', 'Eventos'], rows_year(em.groupby('_y').size()), yearx=True)
i.chart('bar', 'Eventos por tipo', 'Principales tipos de evento (acumulado 2020–2025).', 'Eventos', 'Tipo de evento', ['Evento', 'Eventos'], rows_cat(em.groupby(em['EVENTO'].astype(str).str.strip().str.capitalize()).size(), 12))
i.map('Eventos por municipio', 'Emergencias por municipio y año.', 'Eventos', 'Eventos', map_rows(em, ycol='_y'))
inc = em[em['EVENTO'].astype(str).str.contains('INCENDIO', na=False)]
i.chart('column', 'Hectáreas afectadas por incendios', 'Hectáreas afectadas por incendios forestales y de cobertura vegetal por año.', 'Hectáreas', 'Año', ['Año', 'Hectáreas'], rows_year(inc.groupby('_y')['HECTAREAS'].sum()), yearx=True)
tab = em.groupby('_y')[['MUERTOS', 'HERIDOS', 'PERSONAS', 'FAMILIAS']].sum()
i.chart('column', 'Afectaciones por año', 'Fallecidos, heridos, personas y familias afectadas por año.', 'Personas / familias', 'Año', ['Año', 'Fallecidos', 'Heridos', 'Personas afectadas', 'Familias afectadas'], rows_pivot(tab), yearx=True, single=False)

# NOTA: el catálogo se ciñe al Excel «BD DX OBS AMBIENTAL.xlsx»: un indicador
# por hoja con datos. Las campañas de posconsumo y los PRAES venían del archivo
# Respuesta.xlsx, y los incendios de cobertura vegetal de datos.gov.co, así que
# quedan fuera de este catálogo.

# =====================================================================
# 4. SALUD AMBIENTAL (SIVIGILA)
# =====================================================================
def sivigila_ind(code, sh, title, desc, sub):
    df = sheet(sh); df = with_year(df, 'ANO'); df = with_geo(df, name_col='Municipio_ocurrencia')
    ind = add(Ind(code, 4, title, desc, SRC_SIVIGILA, sub))
    ys = df.groupby('_y').size()
    sx = df['SEXO'].astype(str).str.strip().str.capitalize()
    if len(ys) > 1:
        ind.chart('line', 'Casos por año', 'Número de casos notificados por año.', 'Casos', 'Año', ['Año', 'Casos'], rows_year(ys), yearx=True)
        tab = df.assign(_s=sx).groupby(['_y', '_s']).size().unstack(fill_value=0)
        ind.chart('column', 'Casos por sexo y año', 'Distribución de los casos por sexo.', 'Casos', 'Año', ['Año'] + list(tab.columns), rows_pivot(tab), yearx=True, single=False)
    else:
        ind.chart('column', 'Casos por sexo', 'Distribución de los casos por sexo.', 'Casos', 'Sexo', ['Sexo', 'Casos'], rows_cat(df.groupby(sx).size()))
    ind.map('Casos por municipio de ocurrencia', 'Casos por municipio y año.', 'Casos', 'Casos', map_rows(df, ycol='_y'))
    ind.chart('column', 'Casos por ciclo de vida', 'Distribución de los casos por ciclo de vida de la persona afectada (acumulado).', 'Casos', 'Ciclo de vida', ['Ciclo de vida', 'Casos'], rows_cat(df.groupby(df['Ciclo de vida'].astype(str).str.strip()).size()))
    pcol = 'Provincia Ocurrencia'
    if pcol in df.columns:
        ind.chart('column', 'Casos por provincia', 'Casos por provincia de ocurrencia (acumulado).', 'Casos', 'Provincia', ['Provincia', 'Casos'], rows_cat(df.groupby(df[pcol].astype(str).str.strip().str.title()).size()))
    return ind
sivigila_ind(3401, 'Agresiones', 'Agresiones por animales potencialmente transmisores de rabia',
    'Casos notificados al SIVIGILA de personas agredidas por animales potencialmente transmisores de rabia (perros, gatos, murciélagos y otros), por año, sexo, ciclo de vida y municipio de ocurrencia. Es el evento de salud ambiental de mayor notificación en el departamento.', 'Zoonosis')
sivigila_ind(3402, 'Accidente Ofidico', 'Accidente ofídico (mordedura de serpiente)',
    'Casos de accidente ofídico notificados al SIVIGILA en Boyacá, por año, sexo, ciclo de vida y municipio de ocurrencia.', 'Zoonosis')
sivigila_ind(3403, 'Accidente otros', 'Accidentes por otros animales venenosos',
    'Casos notificados al SIVIGILA de accidentes por otros animales venenosos (arañas, escorpiones, abejas, orugas, entre otros) en Boyacá, vigencia 2025.', 'Zoonosis')

# =====================================================================
# 5. CALIDAD AMBIENTAL Y SERVICIOS PÚBLICOS
# =====================================================================
coverage_ind(3501, 5, 'Cobertura del servicio de aseo en zona urbana',
    'Porcentaje de viviendas de la zona urbana con servicio de recolección de residuos sólidos (cobertura REC), por municipio y año, según el monitoreo del SGP-APSB.', SRC_MINVIV, 'COB ASEO', None, 'Clasificación', 'Aseo', [('Urbana', '')])

pdir = sheet('PRESTADOR DIR'); pdir = with_year(pdir, 'Año'); pdir = with_geo(pdir, code_col='Código DANE')
pdir['_v'] = (pdir['Valor'].astype(str).str.strip().str.lower() == 'si').astype(int)
i = add(Ind(3502, 5, 'Municipios prestadores directos de acueducto, alcantarillado y aseo',
    'Municipios que prestan directamente (sin empresa u operador especializado) los servicios públicos de acueducto, alcantarillado y aseo, por año. La prestación directa se asocia a menor capacidad técnica y financiera en el sector.', SRC_MINVIV, 'Prestación de servicios'))
tab = pdir.pivot_table(index='_y', columns='Servicio', values='_v', aggfunc='sum', fill_value=0)
i.chart('column', 'Municipios prestadores directos por servicio y año', 'Número de municipios prestadores directos.', 'Municipios', 'Año', ['Año'] + list(tab.columns), rows_pivot(tab), yearx=True, single=False)
acu_d = pdir[pdir['Servicio'].astype(str).str.lower().str.contains('acue')]
i.map('Prestación directa de acueducto (1 = sí)', 'Municipios prestadores directos de acueducto por año.', 'Prestador directo', 'Directo', map_rows(acu_d, '_v', 'max', ycol='_y'))
tot = pdir.groupby('_y')['_g'].nunique()
i.chart('line', 'Porcentaje de municipios prestadores directos', 'Participación (%) por servicio.', 'Porcentaje', 'Año', ['Año'] + list(tab.columns), [[str(y)] + [fmt(round(tab.loc[y, c] / tot[y] * 100, 1)) for c in tab.columns] for y in tab.index], yearx=True)

df_ = sheet('DISPOSICION FINAL'); df_ = with_year(df_, 'AÑO'); df_ = with_geo(df_, code_col='COD_DANE'); df_['_t'] = pd.to_numeric(df_['TONELADAS DIA'], errors='coerce').fillna(0)
i = add(Ind(3503, 5, 'Disposición final de residuos sólidos (toneladas/día y tipo de sitio)',
    'Toneladas diarias de residuos sólidos dispuestas por cada municipio, tipo de sitio (relleno sanitario, celda de contingencia o transitoria) y estado (adecuado/inadecuado), según el monitoreo del SGP-APSB.', SRC_MINVIV, 'Residuos sólidos'))
i.chart('line', 'Toneladas/día dispuestas en el departamento', 'Suma de toneladas diarias reportadas por los municipios.', 'Toneladas/día', 'Año', ['Año', 'Toneladas/día'], rows_year(df_.groupby('_y')['_t'].sum().round(1)), yearx=True)
i.map('Toneladas/día por municipio', 'Toneladas diarias dispuestas por municipio y año.', 'Toneladas/día', 'Toneladas', map_rows(df_, '_t', ycol='_y'))
tabs = df_.assign(_s=df_['SITIO'].astype(str).str.strip().str.capitalize()).pivot_table(index='_y', columns='_s', values='_g', aggfunc='count', fill_value=0)
i.chart('column', 'Municipios por tipo de sitio de disposición', 'Número de municipios según el tipo de sitio, por año.', 'Municipios', 'Año', ['Año'] + list(tabs.columns), rows_pivot(tabs), yearx=True, single=False, stacked=True)
last = df_[df_['_y'] == df_['_y'].max()]
i.chart('bar', 'Sitios de disposición final que más toneladas reciben', f'Toneladas/día por sitio ({int(df_["_y"].max())}).', 'Toneladas/día', 'Sitio', ['Sitio', 'Toneladas/día'], rows_cat(last.groupby('NOMBRE DEL SITIO')['_t'].sum(), 12))

icee = sheet('ICEE'); icee = with_year(icee, 'Año'); icee = with_geo(icee, code_col='Divipola'); icee['_v'] = pd.to_numeric(icee['Valor'], errors='coerce') * 100
i = add(Ind(3504, 5, 'Índice de Cobertura de Energía Eléctrica (ICEE)',
    'Proporción de viviendas con servicio de energía eléctrica sobre el total de viviendas (ICEE, UPME), por municipio, zona (urbana, rural y total) y año, con su clasificación (alto ≥ 95 %, medio 80–95 %, bajo < 80 %).', SRC_UPME, 'Energía eléctrica'))
tot_z = icee[icee['Zona'].astype(str).str.contains('Total')].dropna(subset=['_v'])
i.map('ICEE total municipal (%)', 'Índice de cobertura de energía eléctrica total por municipio y año.', 'ICEE (%)', 'ICEE', map_rows(tot_z, '_v', 'mean', ycol='_y'))
rur = icee[icee['Zona'].astype(str).str.contains('Rural')].dropna(subset=['_v'])
i.map('ICEE zona rural (%)', 'Índice de cobertura de energía eléctrica rural por municipio y año.', 'ICEE (%)', 'ICEE', map_rows(rur, '_v', 'mean', ycol='_y'))
tab = icee.dropna(subset=['_v']).pivot_table(index='_y', columns='Zona', values='_v', aggfunc='mean').round(1)
i.chart('line', 'ICEE promedio por zona', 'Promedio municipal del ICEE por zona y año.', 'ICEE (%)', 'Año', ['Año'] + list(tab.columns), rows_pivot(tab), yearx=True)
tab = tot_z.groupby(['_y', 'Clasificación']).size().unstack(fill_value=0)
i.chart('column', 'Municipios por clasificación del ICEE total', 'Número de municipios por rango del índice.', 'Municipios', 'Año', ['Año'] + list(tab.columns), rows_pivot(tab), yearx=True, single=False, stacked=True)

vcs = sheet('VCS'); vcs = with_year(vcs, 'Año'); vcs = with_geo(vcs, code_col='Divipola')
for c in ['Vic Con Servicio', 'Viv Sin Servicio', 'Viv Totales']: vcs[c] = pd.to_numeric(vcs[c], errors='coerce').fillna(0)
tz = vcs[vcs['Zona'].astype(str).str.contains('Total')]
i = add(Ind(3505, 5, 'Viviendas con y sin servicio de energía eléctrica',
    'Número de viviendas con servicio (VCS), sin servicio (VSS) y totales (VT) de energía eléctrica por municipio, zona y año, según el sistema de información de la UPME.', SRC_UPME, 'Energía eléctrica'))
vcs['_p'] = pd.to_numeric(vcs['% Viv con servicio'], errors='coerce') * 100
tzp = vcs[vcs['Zona'].astype(str).str.contains('Total')].dropna(subset=['_p'])
i.map('Porcentaje de viviendas con energía eléctrica por municipio', 'Participación (%) de viviendas con servicio sobre el total, por municipio y año.', 'Porcentaje', 'Porcentaje', map_rows(tzp, '_p', 'mean', ycol='_y'))
tab = tz.groupby('_y')[['Vic Con Servicio', 'Viv Sin Servicio']].sum()
i.chart('column', 'Viviendas con y sin servicio en el departamento', 'Suma departamental por año (total municipal).', 'Viviendas', 'Año', ['Año', 'Con servicio', 'Sin servicio'], rows_pivot(tab), yearx=True, single=False, stacked=True)
i.map('Viviendas sin servicio de energía por municipio', 'Viviendas sin servicio (total municipal) por municipio y año.', 'Viviendas', 'Viviendas', map_rows(tz, 'Viv Sin Servicio', ycol='_y'))
tabz = vcs.pivot_table(index='_y', columns='Zona', values='Viv Sin Servicio', aggfunc='sum', fill_value=0)
tabz = tabz[[c for c in tabz.columns if 'Total' not in str(c)]]
i.chart('column', 'Viviendas sin servicio por zona', 'Viviendas sin energía eléctrica en zona urbana y rural por año.', 'Viviendas', 'Año', ['Año'] + list(tabz.columns), rows_pivot(tabz), yearx=True, single=False)

ica = sheet('ICA'); ica = with_year(ica, 'AÑO'); ica['_e'] = (ica['MUNICIPIO'].astype(str).str.strip().str.title() + ' – ' + ica['ESTACIÓN'].astype(str).str.strip().str.title())
POLL = [('PM-10', 'material particulado PM10'),
        ('PM-2.5', 'material particulado PM2.5'),
        ('SO2', 'dióxido de azufre (SO₂)'),
        ('NO2', 'dióxido de nitrógeno (NO₂)'),
        ('CO (', 'monóxido de carbono (CO)'),
        ('O3', 'ozono troposférico (O₃)')]
i = add(Ind(3506, 5, 'Calidad del aire por estación de monitoreo',
    'Promedio anual de los contaminantes medidos en las estaciones de monitoreo del corredor industrial de Boyacá (Sogamoso, Nobsa, Paipa y Tunja): material particulado PM10 y PM2.5, dióxido de azufre, dióxido de nitrógeno, monóxido de carbono y ozono troposférico. Los límites anuales están definidos en la Resolución 2254 de 2017.',
    SRC_AIRE, 'Calidad del aire'))
for ckey, nombre in POLL:
    c = next((cc for cc in ica.columns if norm(ckey) in norm(cc) and 'promedio' in norm(cc)), None)
    if c is None:
        continue
    d = ica.assign(_v=pd.to_numeric(ica[c], errors='coerce')).dropna(subset=['_v'])
    if d.empty:
        continue
    tab = d.pivot_table(index='_y', columns='_e', values='_v', aggfunc='mean').round(1)
    i.chart('line', 'Promedio anual de ' + nombre,
            'Concentración media anual (µg/m³) registrada en cada estación de monitoreo.',
            'µg/m³', 'Año', ['Año'] + list(tab.columns),
            [[str(int(y))] + [('' if pd.isna(tab.loc[y, st]) else fmt(tab.loc[y, st])) for st in tab.columns]
             for y in sorted(tab.index)], yearx=True)

# =====================================================================
# Nombres oficiales del tablero Power BI del Observatorio Ambiental
# (kit gráfico remitido por la Secretaría de Ambiente el 31/08/2026).
# El texto descriptivo se conserva en la Descripción de cada indicador.
# =====================================================================
OFICIAL = {
    3101: 'Humedales', 3102: 'Acuíferos', 3103: 'Rondas hídricas', 3104: 'Páramos',
    3105: 'Bosques', 3106: 'Áreas forestales',
    3201: 'Porcentaje de cobertura de acueducto',
    3203: 'Porcentaje de cobertura de alcantarillado',
    3205: 'Continuidad del servicio de acueducto urbano (promedio horas/día)',
    3206: 'Municipios con tratamiento de aguas residuales en zona urbana',
    3207: 'Acueductos rurales',
    3208: 'Índice de Riesgo de la Calidad del Agua para el Consumo Humano – IRCA',
    3210: 'Municipio vinculado al Plan Departamental de Aguas',
    3211: 'Concesión de aguas superficiales', 3212: 'Concesión de agua subterránea',
    3213: 'Permisos de vertimientos',
    3301: 'Aprovechamiento forestal de árboles aislados', 3302: 'Licencias ambientales',
    3303: 'Registro de plantaciones forestales protectoras y productoras',
    3304: 'Delitos ambientales reportados a Policía Nacional',
    3401: 'Agresiones por animales potencialmente transmisores de rabia',
    3402: 'Accidentes ofídicos', 3403: 'Accidentes por otros animales venenosos',
    3501: 'Porcentaje de cobertura de aseo – zona urbana',
    3502: 'Municipios que cuentan con prestador directo de aseo, alcantarillado y acueducto',
    3503: 'Disposición final adecuada de residuos (toneladas/día)',
    3504: 'Índice de cobertura de energía eléctrica',
    3505: 'Porcentaje de viviendas con energía eléctrica',
    3506: 'Índice de Calidad del Aire – ICA',
}
for _i in INDS:
    if _i.code in OFICIAL:
        if _i.title not in _i.desc:
            _i.desc = _i.desc  # la descripción ya es autoexplicativa
        _i.title = OFICIAL[_i.code]

# ---------- escritura ----------
os.makedirs(OUT, exist_ok=True)
import shutil, glob as _glob
summary = []
for ind in INDS:
    write_ind(ind)
    summary.append({'code': ind.code, 'cat': CAT[ind.cat], 'title': ind.title, 'sub': ind.sub, 'source': ind.source, 'desc': ind.desc,
                    'charts': [(c[0], c[1], len(c[6])) for c in ind.charts]})
json.dump(summary, open(os.path.join(OUT, '_resumen.json'), 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
LINKS = {
    SRC_CORPO: 'https://www.datos.gov.co/browse?q=CORPOBOYACA',
    SRC_MINVIV: 'https://minvivienda.gov.co/monitoreo-los-recursos-del-sgp-apsb/informes-de-monitoreo-sgp',
    SRC_SIVIGILA: 'https://portalsivigila.ins.gov.co/',
    SRC_SIEDCO: 'https://www.policia.gov.co/estadistica-delictiva',
    SRC_UNGRD: 'https://portal.gestiondelriesgo.gov.co/Paginas/Consolidado-Atencion-de-Emergencias.aspx',
    SRC_UPME: 'https://www.upme.gov.co/simec/energia-electrica/cobertura/indice-de-cobertura-de-energia-electrica-icee/',
    SRC_ESPB: 'https://www.espb.gov.co/',
    SRC_AIRE: 'https://www.corpoboyaca.gov.co/',
    SRC_SEC: 'https://www.boyaca.gov.co/secretaria-de-ambiente-y-desarrollo-sostenible/',
}
ENTIDAD = {
    SRC_CORPO: 'CORPOBOYACÁ', SRC_MINVIV: 'Ministerio de Vivienda, Ciudad y Territorio',
    SRC_SIVIGILA: 'Instituto Nacional de Salud', SRC_SIEDCO: 'Policía Nacional – SIEDCO',
    SRC_UNGRD: 'UNGRD', SRC_UPME: 'UPME', SRC_ESPB: 'Empresa de Servicios Públicos de Boyacá',
    SRC_AIRE: 'Secretaría de Ambiente y Desarrollo Sostenible', SRC_SEC: 'Secretaría de Ambiente y Desarrollo Sostenible',
}
FORMULA = {
    'Porcentaje': '(Unidades con la condición / total de unidades) × 100',
    'Hectáreas': 'Suma del área (ha) de los polígonos dentro de cada unidad territorial',
    'Casos': 'Conteo de casos registrados en el periodo y la unidad territorial',
    'Eventos': 'Conteo de eventos registrados en el periodo y la unidad territorial',
    'Expedientes': 'Conteo de expedientes registrados en el periodo y la unidad territorial',
    'Municipios': 'Conteo de municipios que cumplen la condición en la vigencia',
    'Horas/día': 'Promedio de horas de servicio al día reportadas por el prestador',
    'IRCA (%)': 'Sumatoria de puntajes de riesgo de las características no aceptables / sumatoria de puntajes de riesgo de todas las características analizadas × 100',
    'ICEE (%)': 'Viviendas con servicio (VCS) / viviendas totales (VT) × 100',
    'Toneladas/día': 'Suma de toneladas dispuestas por día en los sitios de disposición final',
    'Acueductos': 'Conteo de acueductos rurales inventariados',
    'Viviendas': 'Conteo de viviendas según condición de servicio',
    'µg/m³': 'Promedio anual de las concentraciones horarias/diarias válidas registradas por la estación',
    'Kilogramos': 'Suma de kilogramos recolectados en las campañas de la vigencia',
    'Campañas': 'Conteo de campañas realizadas en la vigencia',
    'Cantidad': 'Conteo de instrumentos de educación ambiental acompañados',
    'Suscriptores': 'Suma de suscriptores registrados en los acueductos del municipio',
}


def meta_row(ind):
    unit = ind.charts[0][3] if ind.charts else 'Número'
    has_map = any(c[0] == 'map' for c in ind.charts)
    desag = ['Municipio'] if has_map else []
    for c in ind.charts:
        h = str(c[4]).strip()
        if h and h not in desag: desag.append(h)
    return {
        'id': ind.code, 'observatory_id': 3, 'title': ind.title,
        'category_1': CAT[ind.cat], 'category_2': ind.sub, 'tags': 'Observatorio Ambiental',
        'unit': unit,
        'thematic_breakdown': '; '.join(desag[:6]),
        'geographic_breakdown': 'Municipal y provincial' if has_map else 'Departamental',
        'definition': ind.desc,
        'calculation_formula': FORMULA.get(unit, 'Conteo o agregación directa del dato reportado por la fuente'),
        'periodicity': 'Anual', 'baseline_date': '2020',
        'delivery_form': 'Archivo remitido por la fuente / descarga del portal oficial',
        'source': ENTIDAD.get(ind.source, 'Secretaría de Ambiente y Desarrollo Sostenible'),
        'source_link': LINKS.get(ind.source, ''),
        'actors': 'Secretaría de Ambiente y Desarrollo Sostenible; Secretaría de Planeación; corporaciones autónomas regionales',
        'responsible_entity': 'Red de Observatorios de Boyacá – Secretaría Técnica',
        'observations': ind.source,
        'availability_status': 'DISPONIBLE',
    }
FIELDS = ['id','observatory_id','title','category_1','category_2','tags','unit','thematic_breakdown',
          'geographic_breakdown','definition','calculation_formula','periodicity','baseline_date',
          'delivery_form','source','source_link','actors','responsible_entity','observations','availability_status']

def sql_val(v):
    if v is None or v == '': return 'NULL'
    if isinstance(v, int): return str(v)
    return "'" + str(v).replace('\\', '\\\\').replace("'", "''") + "'"

def write_metadata(rows, retired):
    mig = os.path.join(BASE, 'database', 'migrations', '028_indicadores_ambientales_30.sql')
    out = ["-- 028: Hoja de vida del Observatorio Ambiental ajustada al Excel oficial",
           "-- «BD DX OBS AMBIENTAL.xlsx»: un indicador por hoja con datos (30 en total).",
           "-- Reemplaza la 024, que publicaba 42 al desagregar hojas y sumar fuentes externas.",
           "-- Idempotente: reemplaza por id.",
           'SET NAMES utf8mb4;', '']
    if retired:
        out.append('-- Indicadores reemplazados por la nueva estructura (sus carpetas ya no existen).')
        out.append('DELETE FROM indicators WHERE observatory_id = 3 AND id IN (%s);' % ', '.join(str(r) for r in sorted(retired)))
        out.append('')
    cols = ', '.join(FIELDS)
    for r in rows:
        vals = ', '.join(sql_val(r[f]) for f in FIELDS)
        upd = ', '.join(f'{f} = VALUES({f})' for f in FIELDS if f != 'id')
        out.append(f'INSERT INTO indicators ({cols}) VALUES ({vals})\nON DUPLICATE KEY UPDATE {upd};')
    w(mig, '\n\n'.join(out) + '\n')
    # seeds CSV: reemplaza las filas de la dimensión 3
    seed = os.path.join(BASE, 'database', 'seeds', 'indicators_base_general.csv')
    df = pd.read_csv(seed, dtype=str).fillna('')
    df = df[~df['id'].astype(str).str.startswith('3')]
    new = pd.DataFrame([{f: str(r[f]) for f in FIELDS} for r in rows])
    out_df = pd.concat([df, new], ignore_index=True)
    out_df['_o'] = pd.to_numeric(out_df['id'], errors='coerce')
    out_df = out_df.sort_values('_o').drop(columns='_o')
    out_df.to_csv(seed, index=False, encoding='utf-8')
    return mig


IND_DIR = os.path.join(BASE, 'website', 'indicador')
BK_DIR = os.path.join(BASE, 'reportes', 'ambiental_2026', 'backup_indicadores_3xxx')
old_codes = {int(e) for e in os.listdir(IND_DIR) if e.isdigit() and len(e) == 4 and e[0] == '3'}
if os.path.isdir(BK_DIR):   # códigos de la estructura anterior (respaldada)
    old_codes |= {int(e) for e in os.listdir(BK_DIR) if e.isdigit() and len(e) == 4 and e[0] == '3'}
new_codes = {i.code for i in INDS}
mig = write_metadata([meta_row(i) for i in INDS], sorted(old_codes - new_codes))
print('Migración:', mig, '| retirados:', sorted(old_codes - new_codes))
print('Indicadores:', len(INDS), '| gráficos:', sum(len(i.charts) for i in INDS))
for s in summary: print(s['code'], '|', s['cat'][:22], '|', s['title'][:60], '|', [(c[0], c[2]) for c in s['charts']])
print('Municipios sin código DANE:', UNMATCHED.most_common(20))
