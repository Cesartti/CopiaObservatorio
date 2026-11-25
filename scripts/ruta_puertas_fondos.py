# -*- coding: utf-8 -*-
"""
Escenas de las cuatro puertas de la ruta de atencion (ruta-atencion-genero.html).

El diseno llego por capas (carpeta «observatorios fondos»): fondo, ola, dos
personas, dos ramos de flores y el icono, mas una imagen de ejemplo armada a
4311 x 2477. Aqui se optimiza cada capa a WebP y se calcula su posicion en
porcentaje de ese lienzo, para que la tarjeta se arme como el ejemplo y escale
igual en cualquier pantalla.

Las posiciones de personas, ola e icono salen de ubicar cada capa dentro del
ejemplo (cv2.matchTemplate); las flores quedan tapadas en el ejemplo y se
anclan a las esquinas superiores, que es donde estan. Orientacion y apoyo no
trae ejemplo: se arma con el mismo esquema.

Uso:  python scripts/ruta_puertas_fondos.py [carpeta_origen]
Salida: website/assets/img/ruta-puertas/<puerta>/*.webp
        y el bloque JS de posiciones que va en ruta-atencion-genero.html
"""
import json
import os
import sys
from PIL import Image

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
ORIGEN = sys.argv[1] if len(sys.argv) > 1 else \
    r'C:\Users\cesar\Downloads\observatorios fondos\observatorios fondos'
DESTINO = os.path.join(BASE, 'website', 'assets', 'img', 'ruta-puertas')
LW, LH = 4311, 2477  # lienzo del ejemplo

# capa -> (archivo, x, y) en pixeles del lienzo; None = anclar a la esquina
PUERTAS = {
    'proteccion': {'carpeta': 'ATENCION', 'capas': {
        'fondo': ('7.png', 0, 0),
        'flores-izq': ('5.png', 0, 0),
        'flores-der': ('6.png', 'der', 0),
        'persona-izq': ('3.png', 0, 116),
        'persona-der': ('4.png', 3176, 112),
        'ola': ('2.png', 0, 1144),
        'icono': ('1.png', 1733, 0),
    }},
    'salud': {'carpeta': 'ATENCION EN SALUD', 'capas': {
        'fondo': ('7.png', 0, 0),
        'flores-izq': ('4.png', 0, 0),
        'flores-der': ('3.png', 'der', 0),
        'persona-izq': ('6.png', 0, 128),
        'persona-der': ('5.png', 'der', 532),
        'ola': ('1.png', 0, 1248),
        'icono': ('2.png', 1733, 0),
    }},
    'denuncia': {'carpeta': 'DENUNCIA', 'capas': {
        'fondo': ('7.png', 0, 0),
        'flores-izq': ('6.png', 0, 0),
        'flores-der': ('5.png', 'der', 0),
        'persona-izq': ('3.png', 0, 156),
        'persona-der': ('4.png', 'der', 488),
        'ola': ('1.png', 0, 1112),
        'icono': ('2.png', 1733, 0),
    }},
    'orientacion': {'carpeta': 'ORIENTACION Y APOYO', 'capas': {
        'fondo': ('7.png', 0, 0),
        'flores-izq': ('5.png', 0, 0),
        'flores-der': ('6.png', 'der', 0),
        'persona-izq': ('2.png', 0, 'abajo-300'),
        'persona-der': ('3.png', 'der', 'abajo-300'),
        'ola': ('1.png', 0, 'abajo'),
        'icono': ('4.png', 1733, 0),
    }},
}

# ancho maximo de cada capa ya optimizada (la tarjeta mas grande ronda 560 px;
# se deja margen para pantallas de alta densidad)
ANCHO_MAX = {'fondo': 1400, 'ola': 1400, 'icono': 260, 'persona-izq': 420,
             'persona-der': 420, 'flores-izq': 360, 'flores-der': 360}


def main():
    datos = {}
    vistas = []
    for clave, d in PUERTAS.items():
        out_dir = os.path.join(DESTINO, clave)
        os.makedirs(out_dir, exist_ok=True)
        capas = {}
        lienzo = Image.new('RGBA', (LW, LH))
        for nombre in ['fondo', 'flores-izq', 'flores-der', 'persona-izq',
                       'persona-der', 'ola', 'icono']:
            archivo, x, y = d['capas'][nombre]
            im = Image.open(os.path.join(ORIGEN, d['carpeta'], archivo)).convert('RGBA')
            if x == 'der':
                x = LW - im.width
            if isinstance(y, str) and y.startswith('abajo'):
                # «abajo-300»: pegado al borde inferior y subido 300 px
                y = LH - im.height + int(y[5:] or 0)
            lienzo.alpha_composite(im, (max(0, x), max(0, y)))

            w = min(ANCHO_MAX[nombre], im.width)
            peq = im.resize((w, round(im.height * w / im.width)), Image.LANCZOS)
            ruta = os.path.join(out_dir, nombre + '.webp')
            if nombre == 'fondo':
                peq.convert('RGB').save(ruta, 'WEBP', quality=72, method=6)
            else:
                peq.save(ruta, 'WEBP', quality=80, method=6)
            capas[nombre] = {
                'l': round(x / LW * 100, 2), 't': round(y / LH * 100, 2),
                'w': round(im.width / LW * 100, 2),
            }
            print('%-12s %-12s %5d KB' % (clave, nombre, os.path.getsize(ruta) // 1024))
        datos[clave] = capas
        vistas.append(lienzo.convert('RGB').resize((900, round(900 * LH / LW))))

    # hoja de control con las cuatro escenas armadas
    hoja = Image.new('RGB', (1810, vistas[0].height * 2 + 10), 'white')
    for i, v in enumerate(vistas):
        hoja.paste(v, ((i % 2) * 910, (i // 2) * (v.height + 10)))
    control = os.path.join(os.environ.get('TEMP', '.'), 'ruta_puertas_control.png')
    hoja.save(control)
    print('\ncontrol:', control)
    print('\nconst ESCENAS_PUERTA = ' + json.dumps(datos, separators=(',', ':')) + ';')


if __name__ == '__main__':
    main()
