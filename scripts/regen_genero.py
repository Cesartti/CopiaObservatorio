# -*- coding: utf-8 -*-
"""
Regenera los 43 indicadores de Género desde el master (Hoja1) + BASE DX género.
Metadatos del master (100% fiables). Datos: serie anual + por sexo, con
filtrado por tipo para hojas compartidas, match aproximado de hojas y
fallback por municipio. Genera en STAGING.
"""
import os, re, json, unicodedata, collections
import pandas as pd

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
MASTER = r'C:\Users\cesar\Downloads\Base de datos indicadores Red de Observatorios Abril 2026.xlsx'
DX = os.path.join(BASE, 'database', 'seeds', 'BASE DX OBS ASUNTOS DE GENERO.xlsx')
OUT = os.path.join(BASE, 'website', 'indicador_staging_genero')

def norm(s):
    return unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower().strip()
def tokens(s):
    return set(t for t in re.findall(r'[a-z0-9]+', norm(s)) if len(t) >= 4)

dfm = pd.read_excel(MASTER, sheet_name='Hoja1', dtype=str).fillna('')
dfm.columns = [str(c).strip() for c in dfm.columns]
def mcol(n):
    return next((c for c in dfm.columns if norm(c).startswith(norm(n))), None)
c_dim, c_cat, c_no, c_nom, c_fue, c_dx = (mcol('Dimensi'), mcol('Categor'), mcol('No indicador'),
                                          mcol('Nombre indicador'), mcol('Fuente'), mcol('Nombre hoja'))
gen = dfm[dfm[c_dim].apply(lambda x: 'genero' in norm(x))].copy()

xls = pd.ExcelFile(DX)
def real_sheet(name):
    for s in xls.sheet_names:
        if norm(s) == norm(name): return s
    nt = tokens(name); best = (0.0, None)            # fuzzy por solapamiento de tokens
    for s in xls.sheet_names:
        st = tokens(s)
        if not st: continue
        j = len(nt & st) / max(1, len(nt | st))
        if (nt & st) and j > best[0]: best = (j, s)
    return best[1] if best[0] >= 0.30 else None

hoja_count = collections.Counter(norm(r[c_dx]) for _, r in gen.iterrows())

def find_col(cols, *needles, exact=None):
    for c in cols:
        if exact and norm(c) == exact: return c
    for c in cols:
        if any(n in norm(c) for n in needles): return c
    return None
def pick_year(df):
    return (find_col(df.columns, 'ano de radicado', 'ano valoracion', 'ano de', 'anio', 'year', 'vigencia')
            or find_col(df.columns, exact='ano') or find_col(df.columns, 'ano', 'fecha'))
def pick_sex(df):
    return find_col(df.columns, exact='sexo') or find_col(df.columns, exact='sexo final') or find_col(df.columns, 'sexo', 'genero')
def pick_muni(df):
    return find_col(df.columns, 'municipio', 'muncipio')
def pick_val(df):
    return find_col(df.columns, 'numero de', 'cantidad', 'total', 'casos', 'nacidos', 'valor')

def best_filter(df, nombre, used):
    if used < 2: return None, None
    nn = norm(nombre); best = (0, None, None)
    for c in df.columns:
        if any(k in norm(c) for k in ('manera', 'contexto', 'tipo', 'clase', 'modalidad', 'causa', 'evento', 'grupo', 'delito')):
            vals = [v for v in df[c].dropna().astype(str).unique() if str(v).strip()]
            if len(vals) > 40: continue
            for v in vals:
                vt = [t for t in re.findall(r'[a-z0-9]+', norm(v)) if len(t) >= 5]
                sc = sum(1 for t in vt if t in nn)
                if sc > best[0]: best = (sc, c, v)
    return (best[1], best[2]) if best[0] > 0 else (None, None)

def years(df, ycol):
    num = pd.to_numeric(df[ycol], errors='coerce')
    if num.between(1990, 2035).mean() > 0.4:
        return num.where(num.between(1990, 2035))
    return pd.to_datetime(df[ycol], errors='coerce').dt.year

def w(p, c):
    open(p, 'w', encoding='utf-8', newline='\n').write(c)
def info(pairs):
    return ''.join(f'{k}:{v}\n' for k, v in pairs)
def display_js(types):
    cl = []
    for i, t in enumerate(types, 1):
        if t == 'column':
            body = ("  getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, legend:{position:'none'}, bar:{groupWidth:'70%'} }; }\n"
                    "  getType(div){ return new google.visualization.ColumnChart(div); }")
        else:
            body = ("  getOptions(info){ return { hAxis:{title:info['horizontal']}, vAxis:{title:info['vertical']}, curveType:'function', pointSize:5 }; }\n"
                    "  getType(div){ return new google.visualization.LineChart(div); }")
        cl.append("class Chart%d extends AbstractChart{\n%s\n}" % (i, body))
    lst = ','.join(f'Chart{i}' for i in range(1, len(types) + 1))
    return '\n\n'.join(cl) + f"\n\nclass Display extends AbstractDisplay{{\n  constructor(){{ super('corechart',[{lst}]); }}\n}}\n"

os.makedirs(OUT, exist_ok=True)
res = {'con_datos': [], 'solo_meta': []}
for _, r in gen.iterrows():
    code = str(r[c_no]).split('.')[0].strip()
    if not re.match(r'^\d+$', code): continue
    cp = os.path.join(OUT, code); os.makedirs(cp, exist_ok=True)
    w(os.path.join(cp, 'indicador.info'), info([
        ('Categoría', str(r[c_cat]).strip()), ('Descripción', r[c_nom]), ('Titulo', r[c_nom]),
        ('Subcategoría', str(r[c_cat]).strip()), ('Etiquetas', 'ND'),
        ('Fuentes', r[c_fue] or 'BASE DX OBS ASUNTOS DE GÉNERO.xlsx')]))
    rn = real_sheet(r[c_dx])
    if not rn:
        res['solo_meta'].append((code, r[c_nom][:38], 'hoja no encontrada: ' + r[c_dx])); continue
    try:
        df = pd.read_excel(DX, sheet_name=rn); df.columns = [str(c).strip() for c in df.columns]
        fcol, fval = best_filter(df, r[c_nom], hoja_count[norm(r[c_dx])])
        d = df[df[fcol].astype(str) == str(fval)] if (fcol and fval) else df
        ycol = pick_year(d); vcol = pick_val(d); types = []
        if ycol:
            s = d.assign(_y=years(d, ycol)).dropna(subset=['_y']); s['_y'] = s['_y'].astype(int)
            s = s[(s['_y'] >= 2015) & (s['_y'] <= 2026)]
            if len(s):
                g = (s.assign(_v=pd.to_numeric(s[vcol], errors='coerce').fillna(0)).groupby('_y')['_v'].sum()
                     if vcol else s.groupby('_y').size())
                w(os.path.join(cp, '1.csv'), 'Año,Valor\n' + ''.join(f'{int(a)},{v:g}\n' for a, v in g.items()))
                w(os.path.join(cp, '1.info'), info([('Titulo', f'{r[c_nom]} — serie anual'),
                    ('Descripción', 'Evolución anual.'), ('Vertical', 'Casos'), ('Horizontal', 'Año')]))
                types.append('line')
                scol = pick_sex(s)
                if scol:
                    s['_s'] = s[scol].fillna('Sin dato').astype(str).str.strip().str.capitalize().replace({'': 'Sin dato'})
                    tab = (s.assign(_v=pd.to_numeric(s[vcol], errors='coerce').fillna(0)).pivot_table(index='_y', columns='_s', values='_v', aggfunc='sum', fill_value=0)
                           if vcol else s.groupby(['_y', '_s']).size().unstack(fill_value=0))
                    cols = [c for c in tab.columns if str(c).lower() not in ('sin dato', 'nan')]
                    if cols:
                        hdr = 'Año,' + ','.join(map(str, cols)) + '\n'
                        body = ''.join(str(int(a)) + ',' + ','.join(f'{tab.loc[a, c]:g}' for c in cols) + '\n' for a in tab.index)
                        w(os.path.join(cp, '2.csv'), hdr + body)
                        w(os.path.join(cp, '2.info'), info([('Titulo', f'{r[c_nom]} — por sexo'),
                            ('Descripción', 'Desagregación por sexo.'), ('Vertical', 'Casos'), ('Horizontal', 'Año')]))
                        types.append('line')
        if not types:                                # fallback por municipio
            mcol_ = pick_muni(d)
            if mcol_:
                mm = d.copy(); mm[mcol_] = mm[mcol_].fillna('').astype(str).str.strip().str.title()
                mm = mm[~mm[mcol_].str.lower().isin(['', 'nan', 'sin dato'])]
                gm = (mm.assign(_v=pd.to_numeric(mm[vcol], errors='coerce').fillna(0)).groupby(mcol_)['_v'].sum()
                      if vcol else mm.groupby(mcol_).size()).sort_values(ascending=False).head(12)
                if len(gm):
                    w(os.path.join(cp, '1.csv'), 'Municipio,Valor\n' + ''.join(f'{m},{v:g}\n' for m, v in gm.items()))
                    w(os.path.join(cp, '1.info'), info([('Titulo', f'{r[c_nom]} — por municipio'),
                        ('Descripción', 'Principales municipios.'), ('Vertical', 'Casos'), ('Horizontal', 'Municipio')]))
                    types.append('column')
        if types:
            w(os.path.join(cp, 'display.js'), display_js(types))
            res['con_datos'].append((code, r[c_nom][:38], f"{len(types)} graf" + (f" [filtro={fval}]" if fval else "")))
        else:
            res['solo_meta'].append((code, r[c_nom][:38], 'sin datos extraibles'))
    except Exception as e:
        res['solo_meta'].append((code, r[c_nom][:38], f'ERR {type(e).__name__}'))

print(f"TOTAL: {len(gen)} | con datos: {len(res['con_datos'])} | solo metadata: {len(res['solo_meta'])}")
print("\n== SIN DATOS (revisar) ==")
for x in res['solo_meta']: print('  ', x[0], x[1], '->', x[2])
json.dump(res, open(os.path.join(BASE, 'reportes', 'regen_genero_result.json'), 'w'), ensure_ascii=False)
