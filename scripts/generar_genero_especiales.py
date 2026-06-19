# -*- coding: utf-8 -*-
"""
Indicadores de género cuyas hojas NO son series temporales por año, sino
listados/conteos por categoría (cargo, provincia, tipo de discapacidad, grupo).
Se generan como gráficas de barras por categoría.
"""
import os
import pandas as pd

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
XLSX = os.path.join(BASE, "database", "seeds", "BASE DX OBS ASUNTOS DE GENERO.xlsx")
OUT  = os.path.join(BASE, "website", "indicador")
META_CSV = os.path.join(BASE, "database", "seeds", "indicators_genero_dynamic_import.csv")
meta = pd.read_csv(META_CSV, dtype=str).fillna("")


def w(p, c):
    with open(p, "w", encoding="utf-8", newline="\n") as fh:
        fh.write(c)


def info_block(pairs):
    return "".join(f"{k}:{v}\n" for k, v in pairs)


def display_js(tipos):
    """tipos: int (todas columnas) o lista 'column'|'line'."""
    if isinstance(tipos, int):
        tipos = ["column"] * tipos
    clases = []
    for i, t in enumerate(tipos, start=1):
        if t == "line":
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


def contar_si(df, columnas):
    out = {}
    for c in columnas:
        if c in df.columns:
            out[c] = int((df[c].astype(str).str.strip().str.upper() == "SI").sum())
    return pd.Series(out).sort_values(ascending=False)


def barras(df, cat, val=None, top=15, titlecase=True):
    s = df.copy()
    s[cat] = s[cat].fillna("Sin dato").astype(str).str.strip()
    if titlecase:
        s[cat] = s[cat].str.title()
    s = s[~s[cat].str.lower().isin(["sin dato", "nan", ""])]
    if val:
        s["_v"] = pd.to_numeric(s[val], errors="coerce").fillna(0)
        g = s.groupby(cat)["_v"].sum()
    else:
        g = s.groupby(cat).size()
    return g.sort_values(ascending=False).head(top)


def suma_columnas(df, columnas):
    out = {}
    for c in columnas:
        if c in df.columns:
            out[c] = pd.to_numeric(df[c], errors="coerce").fillna(0).sum()
    return pd.Series(out).sort_values(ascending=False)


def escribir(idv, graficas):
    """graficas: lista de dicts {serie, titulo, vertical, horizontal}"""
    m = meta[meta["id"] == idv].iloc[0]
    carpeta = os.path.join(OUT, idv)
    os.makedirs(carpeta, exist_ok=True)
    fuente = m["source_link"] if m["source_link"] not in ("", "ND") else m["source"]
    w(os.path.join(carpeta, "indicador.info"), info_block([
        ("Categoría", m["category_2"]), ("Descripción", m["definition"]),
        ("Titulo", m["title"]), ("Subcategoría", m["category_2"]),
        ("Etiquetas", "ND"), ("Fuentes", fuente),
    ]))
    for i, g in enumerate(graficas, start=1):
        serie = g["serie"]
        w(os.path.join(carpeta, f"{i}.csv"),
          f"{g['horizontal']},Valor\n" + "".join(f"{k},{v:g}\n" for k, v in serie.items()))
        w(os.path.join(carpeta, f"{i}.info"), info_block([
            ("Titulo", g["titulo"]), ("Descripción", g.get("desc", "Distribución del indicador.")),
            ("Vertical", g["vertical"]), ("Horizontal", g["horizontal"]),
        ]))
    tipos = [g.get("tipo", "column") for g in graficas]
    w(os.path.join(carpeta, "display.js"), display_js(tipos))
    print(f"  [{idv}] {m['title'][:40]:<40} OK ({len(graficas)} gráficas)")


def run():
    f = XLSX
    # 5400 Certificados de discapacidad (columnas tipo = 'SI'/'NO')
    df = pd.read_excel(f, sheet_name="Certificados Discap")
    df.columns = [str(c).strip() for c in df.columns]
    tipos = contar_si(df, ["Física", "Visual", "Auditiva", "Intelectual", "Psicosocial", "Múltiple", "Sordoceguera"])
    escribir("5400", [
        {"serie": tipos, "titulo": "Certificados de discapacidad por tipo", "vertical": "Número", "horizontal": "Tipo",
         "desc": "Certificados emitidos a mujeres por tipo de discapacidad."},
        {"serie": barras(df, "Municipio Residencia", top=12), "titulo": "Por municipio de residencia", "vertical": "Número", "horizontal": "Municipio"},
    ])

    # 5300 Tasa de repitencia escolar (columna TASA -> promedio por año / por sexo)
    df = pd.read_excel(f, sheet_name="REPITENCIA")
    df.columns = [str(c).strip() for c in df.columns]
    df["AÑO"] = pd.to_numeric(df["AÑO"], errors="coerce")
    df["TASA"] = pd.to_numeric(df["TASA"], errors="coerce")
    df = df.dropna(subset=["AÑO", "TASA"])
    df["AÑO"] = df["AÑO"].astype(int)
    g1 = df.groupby("AÑO")["TASA"].mean().round(2)
    tab = df.assign(SEXO=df["SEXO"].astype(str).str.strip().str.capitalize()) \
            .pivot_table(index="AÑO", columns="SEXO", values="TASA", aggfunc="mean").round(2).fillna(0).sort_index()
    escribir("5300", [
        {"serie": g1, "titulo": "Tasa de repitencia escolar — promedio anual", "tipo": "line",
         "vertical": "Tasa (%)", "horizontal": "Año", "desc": "Promedio departamental de la tasa de repitencia."},
    ])
    # Gráfica 2 (por sexo) se escribe aparte para conservar varias series
    carpeta = os.path.join(OUT, "5300")
    cols = [str(c) for c in tab.columns]
    w(os.path.join(carpeta, "2.csv"), "Año," + ",".join(cols) + "\n" +
      "".join(str(int(a)) + "," + ",".join(f"{tab.loc[a,c]:g}" for c in tab.columns) + "\n" for a in tab.index))
    w(os.path.join(carpeta, "2.info"), info_block([
        ("Titulo", "Tasa de repitencia por sexo"), ("Descripción", "Promedio anual de la tasa por sexo."),
        ("Vertical", "Tasa (%)"), ("Horizontal", "Año")]))
    w(os.path.join(carpeta, "display.js"), display_js(["line", "line"]))
    print("  [5300] re-generado con TASA (línea + por sexo)")

    # 5500 Participación en juntas de acción comunal
    df = pd.read_excel(f, sheet_name="Participacion Juntas")
    df.columns = [str(c).strip() for c in df.columns]
    escribir("5500", [
        {"serie": barras(df, "Cargo", "Total"), "titulo": "Mujeres en JAC por cargo", "vertical": "Total", "horizontal": "Cargo",
         "desc": "Participación femenina en juntas de acción comunal por tipo de cargo."},
        {"serie": barras(df, "Provincia", "Total"), "titulo": "Por provincia", "vertical": "Total", "horizontal": "Provincia"},
    ])

    # 5501 Concejalas
    df = pd.read_excel(f, sheet_name="Consejalas")
    df.columns = [str(c).strip() for c in df.columns]
    escribir("5501", [
        {"serie": barras(df, "PROVINCIA", "CANTIDAD"), "titulo": "Concejalas electas por provincia", "vertical": "Cantidad", "horizontal": "Provincia"},
        {"serie": barras(df, "MUNICIPIO", "CANTIDAD", top=15), "titulo": "Por municipio (principales)", "vertical": "Cantidad", "horizontal": "Municipio"},
    ])

    # 5503 Referente mujer en CTP
    df = pd.read_excel(f, sheet_name="Concejo Territorial Plan")
    df.columns = [str(c).strip() for c in df.columns]
    escribir("5503", [
        {"serie": barras(df, "PROVINCIA"), "titulo": "Referentes mujer en CTP por provincia", "vertical": "Número de municipios", "horizontal": "Provincia",
         "desc": "Municipios con referente mujer en el Consejo Territorial de Planeación."},
    ])

    # 5700 Mujeres desmovilizadas
    df = pd.read_excel(f, sheet_name="Desmovilizadas")
    df.columns = [str(c).strip() for c in df.columns]
    escribir("5700", [
        {"serie": barras(df, "Ex Grupo", titlecase=False), "titulo": "Mujeres desmovilizadas por ex grupo", "vertical": "Número", "horizontal": "Grupo"},
    ])

    # 5701 Mujeres en reincorporación
    df = pd.read_excel(f, sheet_name="Reincorporación")
    df.columns = [str(c).strip() for c in df.columns]
    col_etario = next((c for c in df.columns if "etario" in c.lower() or "ciclo" in c.lower()), df.columns[3])
    escribir("5701", [
        {"serie": barras(df, col_etario, titlecase=False), "titulo": "Mujeres en reincorporación por grupo etario", "vertical": "Número", "horizontal": "Grupo etario"},
    ])


if __name__ == "__main__":
    print("Generando indicadores de género especiales (barras por categoría)...\n")
    run()
    print("\nListo.")
