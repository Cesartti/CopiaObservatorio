# -*- coding: utf-8 -*-
"""
Corrige la ortografía del texto que se ve en el sitio: páginas PHP/HTML,
rótulos de las gráficas y títulos de las fichas de indicadores.

Complementa a corregir_ortografia_indicadores.py, que ya pasó por las tildes
ausentes de las fichas. Aquí van las erratas que ese cruce no podía ver:
palabras mal escritas una sola vez («merccados», «depertamentos»), faltas de
concordancia («hectáreas cosechada») y texto visible de las páginas.

Nada de esto sale de un diccionario: cada caso se revisó en su contexto. Por
eso la lista es corta y los reemplazos son literales, no patrones generales.

El rótulo 'Hectareas' es un caso aparte: display.js lo usa para buscar la
columna en el CSV y MapChart la imprime como título. Hay que cambiar los dos
archivos a la vez o el mapa queda sin datos, así que van emparejados.

Uso:
    python scripts/corregir_ortografia_sitio.py            (muestra qué cambiaría)
    python scripts/corregir_ortografia_sitio.py --aplicar  (escribe los archivos)
"""
import io
import os
import sys

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
WEB = os.path.join(BASE, 'website')

# ---------------------------------------------------------------- erratas
# (ruta relativa a website/, texto viejo, texto nuevo)
CAMBIOS = [
    # --- texto visible de las páginas ---
    ('about.php', 'divulgación y dialogo', 'divulgación y diálogo'),
    ('about.php', 'teniendo como secretaria técnica', 'teniendo como secretaría técnica'),
    ('indic-ambiental-.php', 'calidad del aíre', 'calidad del aire'),
    ('indic-economico2.php', 'Destinación Especifica FUT', 'Destinación Específica FUT'),
    ('indic-economico.php', ' grafica(s) del indicador', ' gráfica(s) del indicador'),
    ('indic-social.php', ' grafica(s) del indicador', ' gráfica(s) del indicador'),
    ('componentes/dimensiones.php', 'alt="Dimensión Asuntos de genero"',
     'alt="Dimensión Asuntos de género"'),
    ('componentes/dimensiones.php', '>Genero</h3>', '>Género</h3>'),
    ('componentes/dimensiones.php', 'Asuntos de genero.', 'Asuntos de género.'),
    ('assets/js/prestadores.js', 'EMPTRESA SOCIAL DEL ESTADO', 'EMPRESA SOCIAL DEL ESTADO'),

    # --- fichas: erratas ---
    ('indicador/2072/indicador.info', 'Porcentaja de barreras', 'Porcentaje de barreras'),
    ('indicador/2971/indicador.info', 'Porcentaja de barreras', 'Porcentaje de barreras'),
    ('indicador/1028/1.info', 'permanentes de firba', 'permanentes de fibra'),
    ('indicador/1902/3.info', 'otros depertamentos', 'otros departamentos'),
    ('indicador/4013/1.info', 'según sus a ctividades', 'según sus actividades'),
    # Con el salto de línea al final, para que correr el script dos veces no
    # deje «Colcienciass».
    ('indicador/4027/indicador.info', 'reconocidos en Colciencia\n', 'reconocidos en Colciencias\n'),
    ('indicador/4030/indicador.info', 'reconocido en Colciencia\n', 'reconocido en Colciencias\n'),
]

# Erratas que se repiten en varias fichas: se buscan en todas.
GLOBAL_FICHAS = [
    ('desagragación', 'desagregación'),
    ('deiversificación', 'diversificación'),
    ('merccados', 'mercados'),
    ('hectáreas cosechada ', 'hectáreas cosechadas '),
    ('áreas cosechada ', 'áreas cosechadas '),
    ('área sembradas ', 'área sembrada '),
]

# El propósito del acordeón de género, en las tres copias de la página.
PROPOSITO = ['indic-genero.php', 'indic-genero2.php', 'indic-genero - copia.php']
GENERO = ['indic-genero.php', 'indic-genero2.php', 'indic-genero - copia.php']

for f in PROPOSITO:
    CAMBIOS.append((f, '>Proposito</button>', '>Propósito</button>'))
    CAMBIOS.append((f, '>Proposito</span>', '>Propósito</span>'))
for f in GENERO:
    CAMBIOS.append((f, 'relacionadas con asuntos de genero.',
                    'relacionadas con asuntos de género.'))
    CAMBIOS.append((f, 'Asuntos de Genero', 'Asuntos de Género'))

# Rótulo del eje/leyenda de los mapas de hectáreas: CSV y display.js a la vez.
HECTAREAS = [
    ('indicador/1998/5.csv', 'indicador/1998/display.js'),
    ('indicador/3101/1.csv', 'indicador/3101/display.js'),
    ('indicador/3102/1.csv', 'indicador/3102/display.js'),
    ('indicador/3103/1.csv', 'indicador/3103/display.js'),
    ('indicador/3104/1.csv', 'indicador/3104/display.js'),
    ('indicador/3105/1.csv', 'indicador/3105/display.js'),
    ('indicador/3106/1.csv', 'indicador/3106/display.js'),
    ('indicador/3106/2.csv', 'indicador/3106/display.js'),
]
for csv, djs in HECTAREAS:
    CAMBIOS.append((csv, ',Hectareas', ',Hectáreas'))
    CAMBIOS.append((djs, "'Hectareas'", "'Hectáreas'"))


def leer(ruta):
    return io.open(ruta, encoding='utf-8', newline='').read()


def main():
    aplicar = '--aplicar' in sys.argv

    # Las erratas globales se convierten en cambios concretos por archivo.
    fichas = os.path.join(WEB, 'indicador')
    for carpeta in sorted(os.listdir(fichas)):
        d = os.path.join(fichas, carpeta)
        if not os.path.isdir(d):
            continue
        for nombre in sorted(os.listdir(d)):
            if not nombre.endswith('.info'):
                continue
            try:
                t = leer(os.path.join(d, nombre))
            except (UnicodeDecodeError, OSError):
                continue
            for mal, bien in GLOBAL_FICHAS:
                if mal in t:
                    CAMBIOS.append(('indicador/%s/%s' % (carpeta, nombre), mal, bien))

    # Agrupa por archivo para abrir y escribir una sola vez.
    porarchivo = {}
    for rel, mal, bien in CAMBIOS:
        porarchivo.setdefault(rel, []).append((mal, bien))

    tocados, total, faltan = 0, 0, []
    for rel in sorted(porarchivo):
        ruta = os.path.join(WEB, rel.replace('/', os.sep))
        if not os.path.isfile(ruta):
            faltan.append(rel)
            continue
        texto = leer(ruta)
        nuevo = texto
        hechos = []
        for mal, bien in porarchivo[rel]:
            n = nuevo.count(mal)
            if n:
                nuevo = nuevo.replace(mal, bien)
                hechos.append((n, mal, bien))
                total += n
        if not hechos:
            continue
        tocados += 1
        print(rel)
        for n, mal, bien in hechos:
            print('    %dx  %s  ->  %s' % (n, mal.strip(), bien.strip()))
        if aplicar:
            io.open(ruta, 'w', encoding='utf-8', newline='').write(nuevo)

    for rel in faltan:
        print('!! no existe, se omite: %s' % rel)
    print('')
    print('%d cambios en %d archivos%s'
          % (total, tocados, ' (ESCRITOS)' if aplicar else ' (simulación)'))


if __name__ == '__main__':
    main()
