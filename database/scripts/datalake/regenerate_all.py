"""
Orquestador principal. Lee los indicadores desde la BD y dispara la generación.

Uso:
  py -3 database/scripts/datalake/regenerate_all.py                # todos
  py -3 database/scripts/datalake/regenerate_all.py 1000           # uno
  py -3 database/scripts/datalake/regenerate_all.py --obs 1        # toda la dimensión
  py -3 database/scripts/datalake/regenerate_all.py --sheet "ECV"  # por hoja origen
"""
from __future__ import annotations
import sys, argparse
from pathlib import Path
import pymysql
sys.stdout.reconfigure(encoding="utf-8")

ROOT = Path(__file__).resolve().parents[3]
if str(ROOT) not in sys.path:
    sys.path.insert(0, str(ROOT))

from database.scripts.datalake.generator import generate_for_indicator, RECIPE_REGISTRY

def get_pdo():
    """Conexión MariaDB usando los mismos defaults que el website."""
    import os
    host = os.environ.get('OBS_DB_HOST', '127.0.0.1')
    name = os.environ.get('OBS_DB_NAME', 'observatorio_boyaca')
    user = os.environ.get('OBS_DB_USER', 'root')
    pw   = os.environ.get('OBS_DB_PASS', '')
    return pymysql.connect(host=host, user=user, password=pw, database=name, charset='utf8mb4')

def fetch_indicators(conn, where: str = "", params: list = None):
    sql = (
        "SELECT id, observatory_id, title, category_1, category_2, tags, unit, "
        "thematic_breakdown, definition, calculation_formula, source, source_link "
        "FROM indicators "
        + (f"WHERE {where} " if where else "")
        + "ORDER BY observatory_id, id"
    )
    cur = conn.cursor(pymysql.cursors.DictCursor)
    cur.execute(sql, params or [])
    return cur.fetchall()

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("ids", nargs="*", type=int, help="IDs específicos (vacío = todos)")
    ap.add_argument("--obs", type=int, help="Filtrar por observatory_id (1=Eco, 2=Soc, 5=Gen)")
    ap.add_argument("--sheet", type=str, help="Filtrar por nombre de hoja origen")
    args = ap.parse_args()

    conn = get_pdo()
    if args.ids:
        rows = fetch_indicators(conn, f"id IN ({','.join(['%s']*len(args.ids))})", args.ids)
    elif args.sheet:
        # Match exacto: el nombre de la hoja debe terminar en final-de-línea o seguir con espacio
        rows = fetch_indicators(
            conn,
            "thematic_breakdown REGEXP %s",
            [f"Hoja base DX: {args.sheet}([^[:alnum:]]|$)"]
        )
    elif args.obs:
        rows = fetch_indicators(conn, "observatory_id = %s", [args.obs])
    else:
        rows = fetch_indicators(conn)

    print(f"\nIndicadores objetivo: {len(rows)}")
    print(f"Recetas registradas:  {len(RECIPE_REGISTRY)}")
    print(f"  → {sorted(RECIPE_REGISTRY.keys())[:6]}...\n")

    ok = 0
    fail_no_recipe = 0
    fail_other = 0
    examples = []
    for r in rows:
        success, msg = generate_for_indicator(r)
        if success:
            ok += 1
            if ok <= 3 or ok % 25 == 0:
                print(f"  ✅ {msg}")
        else:
            if "receta no registrada" in msg:
                fail_no_recipe += 1
            else:
                fail_other += 1
            if len(examples) < 8:
                examples.append(msg)

    print(f"\n=== Resumen ===")
    print(f"  Exitosos:           {ok}")
    print(f"  Sin receta:         {fail_no_recipe}")
    print(f"  Otros errores:      {fail_other}")
    if examples:
        print(f"\nEjemplos de fallos (primeros {len(examples)}):")
        for e in examples:
            print(f"  • {e}")
    conn.close()

if __name__ == "__main__":
    main()
