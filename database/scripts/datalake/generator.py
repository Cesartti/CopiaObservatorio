"""
Generator: dado un código de indicador, busca su receta y genera el datalake.
"""
from __future__ import annotations
import importlib, sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
if str(ROOT) not in sys.path:
    sys.path.insert(0, str(ROOT))

from database.scripts.datalake.core.helpers import (
    write_indicator_files, find_workbook_for_sheet, load_sheet, normalize_str
)

# Registro de recetas: source_sheet (normalizada) → módulo
RECIPE_REGISTRY = {}

def register_recipe(sheet_name: str, recipe_fn):
    RECIPE_REGISTRY[normalize_str(sheet_name)] = recipe_fn

def _load_recipes():
    """Importa todos los módulos en recipes/ que registran sus recetas al cargarse."""
    recipes_pkg = "database.scripts.datalake.recipes"
    recipes_dir = Path(__file__).parent / "recipes"
    if not recipes_dir.exists():
        return
    for f in recipes_dir.glob("*.py"):
        if f.name.startswith("_"):
            continue
        mod_name = f"{recipes_pkg}.{f.stem}"
        try:
            importlib.import_module(mod_name)
        except Exception as e:
            print(f"[WARN] No se pudo cargar receta {mod_name}: {e}", file=sys.stderr)

_load_recipes()


def generate_for_indicator(indicator: dict) -> tuple[bool, str]:
    """
    indicator: row del SELECT a la tabla `indicators` con campos:
      id, observatory_id, title, category_2, thematic_breakdown, source, source_link, ...

    Devuelve (ok, mensaje).
    """
    iid = int(indicator["id"])
    obs = int(indicator["observatory_id"])
    title = (indicator.get("title") or "").strip()

    # Extraer "Hoja base DX" del thematic_breakdown
    tb = indicator.get("thematic_breakdown") or ""
    sheet_name = None
    if "Hoja base DX:" in tb:
        sheet_name = tb.split("Hoja base DX:", 1)[1].strip()
    if not sheet_name:
        return False, f"[{iid}] sin Hoja base DX en thematic_breakdown"

    key = normalize_str(sheet_name)
    recipe = RECIPE_REGISTRY.get(key)
    if not recipe:
        return False, f"[{iid}] receta no registrada para hoja {sheet_name!r}"

    # Cargar la hoja
    wb_path = find_workbook_for_sheet(obs, sheet_name)
    if wb_path is None:
        return False, f"[{iid}] hoja {sheet_name!r} no encontrada en archivos del obs {obs}"
    headers, rows = load_sheet(wb_path, sheet_name)

    # Llamar a la receta
    try:
        result = recipe(indicator, headers, rows)
    except Exception as e:
        return False, f"[{iid}] receta {sheet_name!r} falló: {e}"
    if not result or not result.get("charts"):
        return False, f"[{iid}] receta no produjo charts"

    indicator_info = result.get("indicator_info") or {
        "Categoría":    indicator.get("category_2") or "",
        "Subcategoría": indicator.get("category_2") or "",
        "Titulo":       title,
        "Descripción":  indicator.get("definition") or "",
        "Etiquetas":    indicator.get("tags") or "ND",
        "Fuentes":      indicator.get("source_link") or indicator.get("source") or "",
    }
    write_indicator_files(iid, indicator_info, result["charts"])
    return True, f"[{iid}] OK · {len(result['charts'])} charts"
