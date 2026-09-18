# -*- coding: utf-8 -*-
"""
Corrige los indicadores de población que salían duplicados.

La base del DANE («BASE DX OBS SOCIAL - DEMOGRAFÍA.xlsx») trae cada registro
tres veces: «Cabecera Municipal», «Centros Poblados y Rural Disperso» y
«Total». El generador sumaba las tres filas, así que todas las cifras quedaban
multiplicadas por dos (2,62 millones de habitantes en 2024, cuando el DANE
reporta 1,31 millones). Además, el indicador de jóvenes (2518) no filtraba la
edad y mostraba la población de todo el departamento.

Este script toma solo el área «Total» y reescribe los CSV de:
  - 2400  Población proyectada DANE del departamento (total y por sexo)
  - 2518  Población de 14 a 28 años (total y por sexo)
  - 5100  Proyección de población femenina

Verificación de 2024 contra fuentes oficiales:
  DANE total 1.311.983 · hombres 647.741 · mujeres 664.242
  MEN población de 5 a 16 años 233.240
"""
import os
import pandas as pd

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SRC = os.path.join(BASE, 'database', 'seeds', 'BASE DX OBS SOCIAL - DEMOGRAFÍA.xlsx')
IND = os.path.join(BASE, 'website', 'indicador')

df = pd.read_excel(SRC, sheet_name='Demografía')
df.columns = [str(c).strip() for c in df.columns]
t = df[df['ÁREA GEOGRÁFICA'].astype(str).str.strip() == 'Total'].copy()
t['TOTAL'] = pd.to_numeric(t['TOTAL'], errors='coerce').fillna(0)
t['edad'] = pd.to_numeric(t['No. Edad'], errors='coerce')
t['AÑO'] = pd.to_numeric(t['AÑO'], errors='coerce').astype(int)

# Se conservan los mismos años que ya publicaba cada indicador.
def anios(cod, n):
    p = os.path.join(IND, cod, f'{n}.csv')
    return [int(float(l.split(',')[0])) for l in open(p, encoding='utf-8').read().splitlines()[1:] if l.strip()]

def esc(v):
    return str(int(round(v)))

def escribir(cod, n, cab, filas):
    p = os.path.join(IND, cod, f'{n}.csv')
    open(p, 'w', encoding='utf-8', newline='\n').write(
        ','.join(cab) + '\n' + ''.join(','.join(f) + '\n' for f in filas))
    print(f'   {cod}/{n}.csv  {len(filas)} filas')

def serie(sub, a):
    return sub[sub['AÑO'] == a]['TOTAL'].sum()

total = t
jov = t[t['edad'].between(14, 28)]
H = lambda d: d[d['SEXO'].astype(str).str.strip() == 'Hombres']
M = lambda d: d[d['SEXO'].astype(str).str.strip() == 'Mujeres']

print('2400 · población total del departamento')
ya = anios('2400', 1)
escribir('2400', 1, ['Año', 'Valor'], [[str(a), esc(serie(total, a))] for a in ya])
escribir('2400', 2, ['Año', 'Hombres', 'Mujeres'],
         [[str(a), esc(serie(H(total), a)), esc(serie(M(total), a))] for a in anios('2400', 2)])

print('2518 · población de 14 a 28 años')
escribir('2518', 1, ['Año', 'Valor'], [[str(a), esc(serie(jov, a))] for a in anios('2518', 1)])
escribir('2518', 2, ['Año', 'Hombres', 'Mujeres'],
         [[str(a), esc(serie(H(jov), a)), esc(serie(M(jov), a))] for a in anios('2518', 2)])

print('5100 · población femenina')
escribir('5100', 1, ['Año', 'Valor'], [[str(a), esc(serie(M(total), a))] for a in anios('5100', 1)])
escribir('5100', 2, ['Año', 'Mujeres'], [[str(a), esc(serie(M(total), a))] for a in anios('5100', 2)])

# comprobación contra las fuentes oficiales
v = lambda d: serie(d, 2024)
assert int(v(total)) == 1311983, v(total)
assert int(v(H(total))) == 647741 and int(v(M(total))) == 664242
assert int(v(t[t['edad'].between(5, 16)])) == 233240
print('\nVerificado 2024: total 1.311.983 (DANE) y 5 a 16 años 233.240 (MEN).')
