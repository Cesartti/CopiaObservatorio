# -*- coding: utf-8 -*-
"""
Portada de la noticia del ciclo de formación de la Red Nacional de
Observatorios de Mujeres y Género.

La pieza que circuló es una captura del formulario de inscripción, con los
logos de la Unión Europea, la AECID, Expertise France y la GIZ. No se
reproduce: esos logos pertenecen a los financiadores del proyecto y no nos
corresponde usarlos. Esta portada anuncia el ciclo con la identidad del
Observatorio de Asuntos de Género y acredita el origen en una línea de texto.

Sale en 1200 x 630, la proporción que usan las portadas de noticias del
portal para compartir en redes.

Uso:  python scripts/gen_portada_formacion_genero.py
"""
import os

from PIL import ImageFont

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SALIDA = os.path.join(BASE, 'website', 'uploads', 'cms', '2026', '10')

# Identidad del Observatorio de Asuntos de Género (website/config/observatories.php)
MORADO = '#7D2D91'
MORADO_OSCURO = '#4A1A57'
ROSA = '#EF6F8F'
BLANCO = '#FFFFFF'
LILA = '#E8D3EE'
FUENTE = "'Segoe UI','Calibri',Arial,sans-serif"

W, H = 1200, 630

SESIONES = [
    ('21 oct', 'Violencias contra las mujeres'),
    ('22 oct', 'Autonomía económica de las mujeres'),
    ('23 oct', 'Cuidados y trabajo no remunerado'),
    ('5 nov', 'Instrumentos de gobernanza de la Red'),
    ('17 nov', 'Presentación final e intercambio'),
]

TTF = {'400': r'C:\Windows\Fonts\segoeui.ttf',
       '600': r'C:\Windows\Fonts\segoeuisb.ttf',
       '700': r'C:\Windows\Fonts\segoeuib.ttf'}
_cache = {}


def medir(texto, tam, peso='400'):
    clave = (tam, peso)
    if clave not in _cache:
        ruta = TTF.get(peso) or TTF['400']
        if not os.path.isfile(ruta):
            ruta = r'C:\Windows\Fonts\arial.ttf'
        _cache[clave] = ImageFont.truetype(ruta, int(tam))
    return _cache[clave].getlength(texto)


def esc(t):
    return str(t).replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;')


def T(x, y, t, tam=14, color=BLANCO, peso='400', anclaje='start'):
    return (f'<text x="{x}" y="{y}" font-family="{FUENTE}" font-size="{tam}" '
            f'fill="{color}" font-weight="{peso}" text-anchor="{anclaje}">{esc(t)}</text>')


def envolver(texto, tam, ancho_max, peso='400'):
    palabras, lineas, actual = texto.split(), [], ''
    for p in palabras:
        prueba = (actual + ' ' + p).strip()
        if medir(prueba, tam, peso) <= ancho_max or not actual:
            actual = prueba
        else:
            lineas.append(actual)
            actual = p
    if actual:
        lineas.append(actual)
    return lineas


def construir():
    p = [f'<rect width="{W}" height="{H}" fill="{MORADO}"/>',
         f'<rect x="0" y="0" width="{W}" height="10" fill="{ROSA}"/>']

    x = 60
    p.append(T(x, 84, 'OBSERVATORIO DE ASUNTOS DE GÉNERO · RED DE OBSERVATORIOS DE BOYACÁ',
               15, LILA, '700'))

    y = 152
    for ln in envolver('Ciclo de jornadas de formación virtual', 46, 700, '700'):
        p.append(T(x, y, ln, 46, BLANCO, '700'))
        y += 56
    for ln in envolver('Red Nacional de Observatorios de Mujeres y Género', 23, 640, '400'):
        p.append(T(x, y + 6, ln, 23, LILA, '400'))
        y += 32

    # Cinco sesiones, cada una con su fecha. La fecha va en cápsula para que
    # se lea de un vistazo sin competir con el tema.
    y += 34
    for fecha, tema in SESIONES:
        an = medir(fecha, 15, '700') + 28
        p.append(f'<rect x="{x}" y="{y - 16}" width="{an:.0f}" height="26" rx="13" '
                 f'fill="{MORADO_OSCURO}"/>')
        p.append(T(x + an / 2, y + 2, fecha, 15, BLANCO, '700', 'middle'))
        p.append(T(x + an + 16, y + 2, tema, 17, BLANCO, '400'))
        y += 40

    # Franja de cierre: horario, modalidad e inscripción.
    yf = H - 96
    p.append(f'<rect x="0" y="{yf}" width="{W}" height="{H - yf}" fill="{MORADO_OSCURO}"/>')
    p.append(T(x, yf + 40, 'De 3:00 a 5:00 p. m. · Virtual, por Google Meet · '
                           'Inscripción abierta', 19, BLANCO, '700'))
    p.append(T(x, yf + 70, 'Formación del proyecto ¡LIBRES!, con el acompañamiento de la '
                           'Red Nacional de Observatorios de Mujeres y Género.',
               14, LILA, '400'))

    return (f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" '
            f'viewBox="0 0 {W} {H}" role="img">\n' + '\n'.join(p) + '\n</svg>')


if __name__ == '__main__':
    os.makedirs(SALIDA, exist_ok=True)
    ruta = os.path.join(SALIDA, 'formacion-red-observatorios-mujeres-genero.svg')
    with open(ruta, 'w', encoding='utf-8', newline='\n') as fh:
        fh.write(construir())
    print('-> %s (%.0f KB)' % (os.path.relpath(ruta, BASE), os.path.getsize(ruta) / 1024))
