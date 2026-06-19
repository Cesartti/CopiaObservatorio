#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Lee Hojas_de_vida_indicadores_Economicos.xlsx (hoja «Fichas») y genera un CSV
compatible con website/lib/indicator_metadata.php (im_import_csv).

Uso:
  py -3 database/scripts/import_hojas_vida_economico_xlsx.py [ruta.xlsx] [salida.csv]

Si no se pasa salida, escribe database/seeds/indicators_economico_xlsx_import.csv
"""

from __future__ import annotations

import csv
import re
import sys
from pathlib import Path

import pandas as pd

ROOT = Path(__file__).resolve().parents[2]
DEFAULT_OUT = ROOT / "database/seeds/indicators_economico_xlsx_import.csv"

CSV_COLUMNS = [
    "id",
    "observatory_id",
    "title",
    "category_1",
    "category_2",
    "tags",
    "unit",
    "thematic_breakdown",
    "geographic_breakdown",
    "definition",
    "calculation_formula",
    "periodicity",
    "baseline_date",
    "delivery_form",
    "source",
    "source_link",
    "actors",
    "responsible_entity",
    "observations",
    "availability_status",
]


def _s(v) -> str:
    if v is None or (isinstance(v, float) and pd.isna(v)):
        return ""
    t = str(v).strip()
    if t.lower() == "nan":
        return ""
    return t


def _one_line(s: str) -> str:
    return re.sub(r"\s+", " ", s).strip()


def _find_ficha_starts(df: pd.DataFrame) -> list[int]:
    out: list[int] = []
    for i in range(len(df)):
        a = df.iat[i, 0]
        if pd.isna(a):
            continue
        t = _s(a)
        if t.startswith("FICHA T") or t.startswith("FICHA T\u00c9CNICA") or "FICHA T" == t[:7]:
            out.append(i)
        elif "FICHA T\u00c9CNICA" in t or "FICHA TCNICA" in t.replace(" ", ""):
            out.append(i)
        elif t.upper().startswith("FICHA ") and "CNICA" in t.upper():
            out.append(i)
    return out


def _parse_block(df: pd.DataFrame, start: int, end: int) -> dict:
    row: dict = {"observatory_id": 1, "availability_status": "DISPONIBLE", "source_link": "", "geographic_breakdown": ""}
    for r in range(start, end):
        a0 = _s(df.iat[r, 0])
        if not a0:
            continue
        # Fila código numérico 1xxx
        if a0.isdigit() and 1000 <= int(a0) < 2000:
            row["id"] = int(a0)
            row["category_1"] = _one_line(_s(df.iat[r, 1]))
            row["category_2"] = _one_line(_s(df.iat[r, 3]))
            row["tags"] = _one_line(_s(df.iat[r, 5]))
        if "NOMBRE DEL INDICADOR" in a0:
            row["title"] = _one_line(_s(df.iat[r + 1, 0]))
            row["source"] = _one_line(_s(df.iat[r + 1, 3]))
            tbreak = _one_line(_s(df.iat[r + 1, 6]))
            if tbreak:
                row["thematic_breakdown"] = tbreak
        if "DEFINICI" in a0:
            row["definition"] = _one_line(_s(df.iat[r + 1, 0]))
            obs = _one_line(_s(df.iat[r + 1, 6]))
            if obs:
                row["observations"] = obs
        if "MO SE CALCULA" in a0.upper() or "C\x3fMO SE CALCULA" in a0.upper():
            row["calculation_formula"] = _one_line(_s(df.iat[r + 1, 0]))
            med = _one_line(_s(df.iat[r + 1, 9]))
            if med:
                row["delivery_form"] = med
        if "ACTORES INVOLUCRADOS" in a0.upper():
            row["actors"] = _one_line(_s(df.iat[r + 1, 0]))
            row["unit"] = _one_line(_s(df.iat[r + 1, 2]))
            row["periodicity"] = _one_line(_s(df.iat[r + 1, 4]))
            row["responsible_entity"] = _one_line(_s(df.iat[r + 1, 6]))
        if "PER" in a0 and "DISPONIBLE" in a0.upper():
            row["baseline_date"] = _one_line(_s(df.iat[r, 3]))
            hb = _one_line(_s(df.iat[r, 9]))
            if hb:
                base = row.get("thematic_breakdown", "")
                suff = f"Hoja base DX: {hb}"
                row["thematic_breakdown"] = f"{base} | {suff}".strip(" |") if base else suff
    return row


def parse_fichas(path: str) -> list[dict]:
    df = pd.read_excel(path, sheet_name="Fichas", header=None)
    starts = _find_ficha_starts(df)
    rows: list[dict] = []
    for idx, st in enumerate(starts):
        end = starts[idx + 1] if idx + 1 < len(starts) else len(df)
        rec = _parse_block(df, st, end)
        if "id" in rec and rec.get("title"):
            rows.append(rec)
    return rows


def write_csv(rows: list[dict], out_path: Path) -> None:
    out_path.parent.mkdir(parents=True, exist_ok=True)
    with out_path.open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=CSV_COLUMNS, extrasaction="ignore")
        w.writeheader()
        for r in rows:
            line = {k: _one_line(_s(r.get(k, ""))) for k in CSV_COLUMNS}
            w.writerow(line)


def main() -> int:
    xlsx = sys.argv[1] if len(sys.argv) > 1 else str(Path.home() / "Downloads/Hojas_de_vida_indicadores_Economicos.xlsx")
    out = Path(sys.argv[2]) if len(sys.argv) > 2 else DEFAULT_OUT
    if not Path(xlsx).is_file():
        print(f"No existe el archivo: {xlsx}", file=sys.stderr)
        return 1
    rows = parse_fichas(xlsx)
    if not rows:
        print("No se extrajo ninguna ficha.", file=sys.stderr)
        return 1
    write_csv(rows, out)
    ids = sorted({r["id"] for r in rows})
    print(f"Extraidas {len(rows)} filas -> {out}")
    print(f"Rango códigos: {ids[0]} … {ids[-1]}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
