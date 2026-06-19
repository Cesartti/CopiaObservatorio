# -*- coding: utf-8 -*-
"""Verificación cruzada: hojas del Excel <-> catálogo <-> datos cargados."""
import openpyxl, os, csv, unicodedata, re

def norm(s):
    return unicodedata.normalize("NFKD", str(s)).encode("ascii", "ignore").decode().lower().strip()

XL = r"C:\Users\cesar\Downloads\BASE DX OBS ASUNTOS DE GÉNERO.xlsx"
CAT = r"C:\xampp\htdocs\Observatorio2026\database\seeds\indicators_genero_dynamic_import.csv"
BASE = r"C:\xampp\htdocs\Observatorio2026\website\indicador"

wb = openpyxl.load_workbook(XL, read_only=True)
hojas = wb.sheetnames
print("HOJAS EN EXCEL:", len(hojas))

cat = {}
with open(CAT, encoding="utf-8") as fh:
    for r in csv.DictReader(fh):
        m = re.search(r"Hoja base DX:\s*(.+?)\s*$", r["thematic_breakdown"] or "")
        cat[r["id"]] = (r["title"], m.group(1).strip() if m else "?")
print("INDICADORES EN CATALOGO:", len(cat))
print()
print(f'{"ID":<6}{"HOJA EXCEL":<28}{"INFO":<6}{"GRAF":<6}{"FILAS":<8}TITULO')

hojas_norm = {norm(h): h for h in hojas}
usadas = set()
problemas = []
for idv, (titulo, hoja) in sorted(cat.items()):
    carp = os.path.join(BASE, idv)
    tiene = "SI" if os.path.exists(os.path.join(carp, "indicador.info")) else "NO"
    ng = 0; filas = 0; i = 1
    while os.path.exists(os.path.join(carp, f"{i}.csv")):
        ng += 1
        with open(os.path.join(carp, f"{i}.csv"), encoding="utf-8") as fh:
            filas += sum(1 for _ in fh) - 1
        i += 1
    hm = hojas_norm.get(norm(hoja)); usadas.add(norm(hoja))
    flag = "" if hm else " <-HOJA NO COINCIDE"
    if tiene == "NO" or ng == 0 or filas == 0:
        problemas.append(idv)
    print(f"{idv:<6}{hoja[:27]:<28}{tiene:<6}{ng:<6}{filas:<8}{titulo[:32]}{flag}")

sobran = [h for h in hojas if norm(h) not in usadas]
print()
print("HOJAS DEL EXCEL SIN INDICADOR EN CATALOGO:", sobran if sobran else "ninguna")
print("INDICADORES CON PROBLEMA (sin info/graficas/datos):", problemas if problemas else "ninguno")
print(f"\nRESUMEN: {len(cat)} indicadores, {len(cat)-len(problemas)} con datos OK")
