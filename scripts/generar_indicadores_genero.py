# -*- coding: utf-8 -*-
"""
Genera los archivos de datos (indicador.info, N.info, N.csv, display.js) para los
indicadores de género, a partir de 'BASE DX OBS ASUNTOS DE GÉNERO.xlsx'.

Modo automático: detecta columnas (año, sexo, municipio, valor) en cada hoja,
agrega y produce hasta 2 gráficas por indicador:
  - G1: serie anual (línea).
  - G2: por sexo/año (línea) si hay sexo; si no, ranking por municipio (columna).

El id 5000 se omite (ya existe, cargado a mano).
"""
import os
import re
import unicodedata
import pandas as pd

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
XLSX = os.path.join(BASE, "database", "seeds", "BASE DX OBS ASUNTOS DE GENERO.xlsx")
OUT  = os.path.join(BASE, "website", "indicador")
META_CSV = os.path.join(BASE, "database", "seeds", "indicators_genero_dynamic_import.csv")

OMITIR = {"5000",  # ya cargado a mano
          # los siguientes se generan en generar_genero_especiales.py (no son series por año):
          "5300", "5400", "5500", "5501", "5503", "5700", "5701"}

meta = pd.read_csv(META_CSV, dtype=str).fillna("")


def norm(s):
    return unicodedata.normalize("NFKD", str(s)).encode("ascii", "ignore").decode().lower().strip()


def w(path, contenido):
    with open(path, "w", encoding="utf-8", newline="\n") as fh:
        fh.write(contenido)


def info_block(pairs):
    return "".join(f"{k}:{v}\n" for k, v in pairs)


def find_col(cols, *needles, exact=None):
    for c in cols:
        n = norm(c)
        if exact and n == exact:
            return c
    for c in cols:
        n = norm(c)
        if any(x in n for x in needles):
            return c
    return None


def detectar(df):
    cols = list(df.columns)
    col_anio = find_col(cols, "ano", "anio", "year") or find_col(cols, exact="ano")
    # preferir 'sexo' exacto, luego 'genero' (evitar 'identidad de genero')
    col_sexo = find_col(cols, exact="sexo") or find_col(cols, exact="genero")
    if not col_sexo:
        col_sexo = find_col(cols, "sexo")
    col_muni = find_col(cols, "municipio", "muncipio")
    # columna de valor numérica con nombre indicativo del INDICADOR (no edad/código/ciclo)
    col_val = None
    INC = ("numero de caso", "numero de persona", "cantidad", "total", "no. casos", "no de casos")
    EXC = ("edad", "cod", "ciclo", "quinq", "mes", "dia", "semestre", "ano", "anio", "identidad", "valor edad")
    for c in cols:
        n = norm(c)
        if any(x in n for x in INC) and not any(e in n for e in EXC):
            serie = pd.to_numeric(df[c], errors="coerce")
            if serie.notna().mean() > 0.5:  # mayoría numérica
                col_val = c
                break
    return col_anio, col_sexo, col_muni, col_val


def display_js(tipos):
    clases = []
    for i, t in enumerate(tipos, start=1):
        if t in ("line", "lineCat"):
            cuerpo = ("    getOptions(info){\n"
                      "        return { hAxis: {title: info['horizontal'], format: Patterns.year},\n"
                      "                 vAxis: {title: info['vertical']}, curveType: 'function', pointSize: 6 };\n"
                      "    }\n"
                      "    getType(div){ return new google.visualization.LineChart(div); }")
        else:
            cuerpo = ("    getOptions(info){\n"
                      "        return { hAxis: {title: info['horizontal']}, vAxis: {title: info['vertical']},\n"
                      "                 bar: {groupWidth: '70%'}, legend: {position: 'none'} };\n"
                      "    }\n"
                      "    getType(div){ return new google.visualization.ColumnChart(div); }")
        clases.append(f"class Chart{i} extends AbstractChart{{\n{cuerpo}\n}}")
    lista = ",".join(f"Chart{i}" for i in range(1, len(tipos) + 1))
    return "\n\n".join(clases) + f"\n\nclass Display extends AbstractDisplay{{\n    constructor(){{ super('corechart',[{lista}]); }}\n}}\n"


def serie_anual(df, col_anio, col_val):
    s = df.dropna(subset=[col_anio]).copy()
    s[col_anio] = pd.to_numeric(s[col_anio], errors="coerce")
    s = s.dropna(subset=[col_anio])
    s[col_anio] = s[col_anio].astype(int)
    if col_val:
        s["_v"] = pd.to_numeric(s[col_val], errors="coerce").fillna(0)
        g = s.groupby(col_anio)["_v"].sum().sort_index()
    else:
        g = s.groupby(col_anio).size().sort_index()
    return g[g.index >= 1990]


def serie_anual_cat(df, col_anio, cat, col_val):
    s = df.dropna(subset=[col_anio]).copy()
    s[col_anio] = pd.to_numeric(s[col_anio], errors="coerce")
    s = s.dropna(subset=[col_anio])
    s[col_anio] = s[col_anio].astype(int)
    s[cat] = s[cat].fillna("Sin dato").astype(str).str.strip().str.capitalize().replace({"": "Sin dato"})
    if col_val:
        s["_v"] = pd.to_numeric(s[col_val], errors="coerce").fillna(0)
        tab = s.pivot_table(index=col_anio, columns=cat, values="_v", aggfunc="sum", fill_value=0).sort_index()
    else:
        tab = s.groupby([col_anio, cat]).size().unstack(fill_value=0).sort_index()
    return tab[tab.index >= 1990]


def ranking_muni(df, col_muni, col_val, top=12):
    s = df.copy()
    s[col_muni] = s[col_muni].fillna("Sin dato").astype(str).str.strip().str.title()
    s = s[s[col_muni].str.lower().isin(["sin dato", "nan", ""]) == False]
    if col_val:
        s["_v"] = pd.to_numeric(s[col_val], errors="coerce").fillna(0)
        g = s.groupby(col_muni)["_v"].sum()
    else:
        g = s.groupby(col_muni).size()
    return g.sort_values(ascending=False).head(top)


def generar(idv):
    m = meta[meta["id"] == idv].iloc[0]
    hoja_m = re.search(r"Hoja base DX:\s*(.+?)\s*$", m["thematic_breakdown"] or "")
    hoja_name = hoja_m.group(1).strip() if hoja_m else None
    xls = pd.ExcelFile(XLSX)
    real = next((s for s in xls.sheet_names if norm(s) == norm(hoja_name)), None)
    if not real:
        return f"[{idv}] sin hoja '{hoja_name}'"
    df = pd.read_excel(XLSX, sheet_name=real)
    df.columns = [str(c).strip() for c in df.columns]
    col_anio, col_sexo, col_muni, col_val = detectar(df)
    if not col_anio:
        return f"[{idv}] {m['title'][:35]} -> SIN COLUMNA AÑO (revisar a mano)"

    carpeta = os.path.join(OUT, idv)
    os.makedirs(carpeta, exist_ok=True)

    fuente = m["source_link"] if m["source_link"] not in ("", "ND") else m["source"]
    w(os.path.join(carpeta, "indicador.info"), info_block([
        ("Categoría", m["category_2"]),
        ("Descripción", m["definition"]),
        ("Titulo", m["title"]),
        ("Subcategoría", m["category_2"]),
        ("Etiquetas", "ND"),
        ("Fuentes", fuente),
    ]))

    tipos = []
    g1 = serie_anual(df, col_anio, col_val)
    if len(g1) == 0:
        return f"[{idv}] {m['title'][:35]} -> sin datos anuales"
    w(os.path.join(carpeta, "1.csv"), "Año,Valor\n" + "".join(f"{int(a)},{v:g}\n" for a, v in g1.items()))
    unidad = m["unit"] if m["unit"] else "Valor"
    w(os.path.join(carpeta, "1.info"), info_block([
        ("Titulo", f"{m['title']} — serie anual"),
        ("Descripción", "Evolución anual del indicador en Boyacá."),
        ("Vertical", unidad),
        ("Horizontal", "Año"),
    ]))
    tipos.append("line")

    # Gráfica 2
    hizo_g2 = False
    if col_sexo and col_sexo in df.columns:
        tab = serie_anual_cat(df, col_anio, col_sexo, col_val)
        if tab.shape[1] >= 1 and tab.shape[0] >= 1:
            cols = [str(c) for c in tab.columns]
            header = "Año," + ",".join(cols) + "\n"
            filas = "".join(str(int(a)) + "," + ",".join(f"{tab.loc[a, c]:g}" for c in tab.columns) + "\n" for a in tab.index)
            w(os.path.join(carpeta, "2.csv"), header + filas)
            w(os.path.join(carpeta, "2.info"), info_block([
                ("Titulo", f"{m['title']} — por sexo"),
                ("Descripción", "Desagregación por sexo y año."),
                ("Vertical", unidad), ("Horizontal", "Año"),
            ]))
            tipos.append("lineCat")
            hizo_g2 = True
    if not hizo_g2 and col_muni and col_muni in df.columns:
        g2 = ranking_muni(df, col_muni, col_val)
        if len(g2) >= 1:
            w(os.path.join(carpeta, "2.csv"), "Municipio,Valor\n" + "".join(f"{mu},{v:g}\n" for mu, v in g2.items()))
            w(os.path.join(carpeta, "2.info"), info_block([
                ("Titulo", f"{m['title']} — por municipio"),
                ("Descripción", "Distribución territorial (principales municipios)."),
                ("Vertical", unidad), ("Horizontal", "Municipio"),
            ]))
            tipos.append("column")
            hizo_g2 = True

    w(os.path.join(carpeta, "display.js"), display_js(tipos))
    extra = f"+{tipos[1]}" if len(tipos) > 1 else "(solo serie)"
    return f"[{idv}] {m['title'][:38]:<38} OK  {int(g1.index.min())}-{int(g1.index.max())}  {extra}"


if __name__ == "__main__":
    ids = [i for i in meta["id"].tolist() if i not in OMITIR]
    print(f"Generando {len(ids)} indicadores de género...\n")
    for idv in ids:
        try:
            print(" ", generar(idv))
        except Exception as e:
            print(f"  [{idv}] ERROR: {type(e).__name__}: {e}")
    print("\nListo.")
