"""
Receta para la hoja 'Pob Multidimensional' (BASE DX OBS ECONÓMICO).

Estructura:
  Departamento | Variable | Año | No Año | Total | Cabeceras | Centros poblados y rural disperso

Filtro por indicador: Variable == indicator.title.
Cubre los 15 indicadores 1000-1014 (Analfabetismo, Bajo logro educativo, ...).

Charts generados:
  1. LineChart      → Tasa anual en Boyacá (Total)
  2. ColumnChart    → Comparación Cabeceras vs Rural por año (urbano/rural)
"""
from database.scripts.datalake.generator import register_recipe
from database.scripts.datalake.core.helpers import find_col, parse_year, to_number, normalize_str


def recipe_pob_multidimensional(indicator: dict, headers: list, rows: list) -> dict:
    title = (indicator.get("title") or "").strip()
    target_var = normalize_str(title)

    c_dpto = find_col(headers, "Departamento")
    c_var  = find_col(headers, "Variable")
    c_year = find_col(headers, "Año", "Ano", "Year")
    c_tot  = find_col(headers, "Total")
    c_cab  = find_col(headers, "Cabeceras")
    c_rur  = find_col(headers, "Centros poblados", "rural disperso")

    # Construir lista de Variables disponibles para matching tolerante (fuzzy)
    available = set()
    for r in rows:
        if c_var is not None and c_var < len(r):
            v = normalize_str(r[c_var])
            if v:
                available.add(v)

    # Match flexible: exacto, o substring en cualquier dirección
    matched_var = None
    if target_var in available:
        matched_var = target_var
    else:
        for v in available:
            if v in target_var or target_var in v:
                matched_var = v
                break
    if not matched_var:
        return {"charts": []}

    series = []  # [(year, total, cabeceras, rural)]
    for r in rows:
        if c_dpto is not None and normalize_str(r[c_dpto] if c_dpto < len(r) else None) != "boyaca":
            continue
        if c_var is not None:
            v = r[c_var] if c_var < len(r) else None
            if normalize_str(v) != matched_var:
                continue
        y = parse_year(r[c_year] if c_year is not None and c_year < len(r) else None)
        if y is None:
            continue
        tot = to_number(r[c_tot] if c_tot is not None and c_tot < len(r) else None)
        cab = to_number(r[c_cab] if c_cab is not None and c_cab < len(r) else None)
        rur = to_number(r[c_rur] if c_rur is not None and c_rur < len(r) else None)
        series.append((y, tot, cab, rur))
    series.sort(key=lambda x: x[0])
    if not series:
        return {"charts": []}

    # Chart 1: serie total
    rows_total = [["Año", "Boyacá"]] + [[y, tot] for (y, tot, _, _) in series if tot is not None]
    # Chart 2: comparación cabeceras vs rural
    rows_split = [["Año", "Cabeceras", "Centros poblados y rural disperso"]] + \
                 [[y, cab, rur] for (y, _, cab, rur) in series if cab is not None or rur is not None]

    return {
        "charts": [
            {
                "titulo":      f"{title} en Boyacá",
                "descripcion": f"Evolución anual del indicador «{title}» en el departamento.",
                "vertical":    "Porcentaje",
                "horizontal":  "Año",
                "rows":        rows_total,
                "display_class": "Line",
            },
            {
                "titulo":      f"{title} por área geográfica",
                "descripcion": f"Comparación del indicador entre cabeceras municipales y zonas rurales / centros poblados.",
                "vertical":    "Porcentaje",
                "horizontal":  "Año",
                "rows":        rows_split,
                "display_class": "Column",
            },
        ],
    }


register_recipe("Pob Multidimensional", recipe_pob_multidimensional)
