# -*- coding: utf-8 -*-
"""
Verifica en producción que cada indicador responda como debe:

  - los vigentes devuelven 200 y su página trae el título de la ficha;
  - los retirados devuelven 301 hacia el indicador vigente o al micrositio;
  - ningún código publicado devuelve 404.

Uso: python scripts/verificar_enlaces_indicadores.py [base_url]
"""
import os, re, sys, json, urllib.request, urllib.error
from concurrent.futures import ThreadPoolExecutor

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IND = os.path.join(BASE, 'website', 'indicador')
SITIO = (sys.argv[1] if len(sys.argv) > 1 else 'https://observatorios.boyaca.gov.co').rstrip('/')

OBS = {'1': 'Económico', '2': 'Social', '3': 'Ambiental', '4': 'CTeI', '5': 'Género'}


def ficha(cod):
    p = os.path.join(IND, cod, 'indicador.info')
    txt = open(p, encoding='utf-8', errors='ignore').read() if os.path.isfile(p) else ''
    retirado = bool(re.search(r'^Retirado:\s*\S', txt, re.M))
    m = re.search(r'^T[íi]tulo:(.*)$', txt, re.M)
    return retirado, (m.group(1).strip() if m else '')


class SinRedireccion(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *a, **k):
        return None


ABRE = urllib.request.build_opener(SinRedireccion)


def pedir(cod):
    url = f'{SITIO}/indicador.php?id={cod}'
    try:
        r = ABRE.open(urllib.request.Request(url, headers={'User-Agent': 'AuditoriaROB/1.0'}), timeout=45)
        return r.getcode(), r.read().decode('utf-8', 'ignore'), ''
    except urllib.error.HTTPError as e:
        return e.code, '', e.headers.get('Location', '')
    except Exception as e:
        return 0, '', str(e)[:60]


codigos = sorted(d for d in os.listdir(IND)
                 if d.isdigit() and len(d) == 4 and d[0] in OBS and os.path.isdir(os.path.join(IND, d)))
print(f'Verificando {len(codigos)} códigos contra {SITIO}\n')

fallos = []


def revisar(cod):
    retirado, titulo = ficha(cod)
    estado, cuerpo, destino = pedir(cod)
    if retirado:
        if estado not in (301, 302):
            return (cod, 'retirado', f'esperaba redirección y devolvió {estado}')
        if 'indicador.php?id=' not in destino and 'observatorio.php' not in destino:
            return (cod, 'retirado', f'redirige a un destino inesperado: {destino[:60]}')
        return None
    if estado != 200:
        return (cod, 'vigente', f'devolvió {estado} {destino}')
    if 'Indicador no encontrado' in cuerpo or 'info de indicador sin datos' in cuerpo:
        return (cod, 'vigente', 'la página dice que el indicador no existe')
    if titulo and titulo[:28] not in cuerpo:
        return (cod, 'vigente', f'la página no muestra el título de la ficha ({titulo[:34]})')
    return None


with ThreadPoolExecutor(max_workers=6) as ex:
    for r in ex.map(revisar, codigos):
        if r:
            fallos.append(r)

print(f'códigos revisados: {len(codigos)}   |   con problema: {len(fallos)}\n')
if fallos:
    for cod, clase, msg in sorted(fallos):
        print(f'   #{cod} ({OBS[cod[0]]}, {clase}): {msg}')
else:
    print('   todos responden correctamente')

json.dump([{'codigo': c, 'clase': k, 'problema': m} for c, k, m in fallos],
          open(os.path.join(BASE, 'reportes', 'enlaces_indicadores.json'), 'w', encoding='utf-8'),
          ensure_ascii=False, indent=1)
