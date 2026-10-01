# -*- coding: utf-8 -*-
"""
Revisa que cada enlace a un indicador lleve al indicador que anuncia.

Las páginas `indic-*.php` llevan los enlaces escritos a mano, con el rótulo y
el código uno al lado del otro. Cuando la numeración de las carpetas cambió,
los rótulos se quedaron apuntando a otra parte. No es un enlace suelto: son
bloques enteros corridos un puesto. En el Observatorio Social el enlace que
dice «Suicidios» abre «Violencia a adulto mayor», el que dice «Violencia a
adulto mayor» abre «Violencia por convivencia educativa», y así diecisiete
veces seguidas.

Por eso el emparejamiento se resuelve por página completa y no enlace por
enlace: se buscan todas las parejas rótulo-indicador posibles, se toman de la
más parecida a la menos y ni un rótulo ni un indicador se usan dos veces.
Arreglando cada enlace por separado, un bloque corrido termina con dos rótulos
distintos abriendo el mismo indicador.

Un enlace a un indicador retirado no cuenta como roto: `indicador.php` responde
un 301 al que lo reemplaza, así que se sigue esa cadena antes de juzgar.

Solo se cambia lo que se puede demostrar. Un rótulo que no se parece a ningún
indicador publicado del observatorio —los de pobreza monetaria en Económico,
que están publicados en Social— queda listado para que lo decida el equipo.

Uso:  python scripts/revisar_enlaces_indicadores.py
      python scripts/revisar_enlaces_indicadores.py --arreglar
"""
import argparse
import difflib
import io
import os
import re
import unicodedata

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PUB = os.path.join(BASE, 'website', 'indicador')

# Solo las páginas que el menú del sitio enlaza de verdad; cada una pertenece a
# un observatorio y un enlace suyo solo puede llevar a un indicador de ese
# observatorio. Sin esa restricción el buscador proponía el 5500 de Género,
# «Pobreza monetaria según sexo», para los enlaces de pobreza de Económico.
PAGINAS = {'indic-economico.php': '1', 'indic-social.php': '2',
           'indic-ambiental.php': '3', 'indic-tecnologia.php': '4'}
ENLACE = re.compile(r'(indicador\.php\?id=)(\d{4})("[^>]*>)(\s*)([^<]+?)(\s*</a>)')

# Rótulos que nombran el indicador de otra manera: abreviaturas y sinónimos que
# ninguna comparación de texto puede resolver sin riesgo de equivocarse. Van
# escritos a mano, con el título de la carpeta al lado para poder revisarlos.
ALIAS = {
    'atenciones medicas por tipos de violencia': '2015',  # Atenciones médicas por violencia
    'delitos sexuales conflicto armado': '2017',          # Delitos contra la libertad e integridad sexual
    'homicidios': '2003',                                 # Presuntos homicidios
    'victimas map muse aei': '2019',                      # Minas antipersonal
    'secuestro poblacion boyacense': '2020',              # Secuestro
    'violencia a nna': '2008',                            # Violencia a niños, niñas y adolescentes
}
UMBRAL_OK = 0.78       # por encima de esto el rótulo y su destino concuerdan
UMBRAL_FIX = 0.88      # por encima de esto se puede reasignar sin preguntar


def comparable(s):
    s = unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower()
    return re.sub(r'\s+', ' ', re.sub(r'[^a-z0-9]+', ' ', s)).strip()


def parecido(a, b):
    ca, cb = comparable(a), comparable(b)
    if not ca or not cb:
        return 0.0
    if ca == cb:
        return 1.0
    corto, largo = sorted((ca, cb), key=len)
    if len(corto) >= 12 and corto in largo:
        return 0.95
    return difflib.SequenceMatcher(None, ca, cb).ratio()


def mismo_indicador(rotulo, titulo):
    """¿El rótulo y el título nombran lo mismo, aunque no se escriban igual?

    Casi todos los rótulos son una versión corta del título: «Tenencia de
    vivienda en hogares campesinos» para «Hogares Por Tenencia De Vivienda».
    Comparando carácter por carácter esos quedaban por debajo del umbral y
    engrosaban la lista de sospechosos sin motivo, así que también se mira
    cuántas palabras largas comparten.
    """
    if parecido(rotulo, titulo) >= UMBRAL_OK:
        return True
    pa = {p for p in comparable(rotulo).split() if len(p) >= 4}
    pb = {p for p in comparable(titulo).split() if len(p) >= 4}
    if not pa or not pb:
        return False
    comunes = len(pa & pb)
    return comunes >= 2 and comunes / min(len(pa), len(pb)) >= 0.7


def carpetas():
    out = {}
    for d in sorted(os.listdir(PUB)):
        if not re.fullmatch(r'\d{4}', d):
            continue
        p = os.path.join(PUB, d, 'indicador.info')
        if not os.path.isfile(p):
            continue
        t, retirado, reemplazo = '', False, ''
        for linea in io.open(p, encoding='utf-8', errors='replace'):
            if ':' not in linea:
                continue
            k, v = linea.split(':', 1)
            k, v = comparable(k).replace(' ', ''), v.strip()
            if k == 'titulo':
                t = v
            elif k == 'retirado' and v:
                retirado = True
            elif k == 'reemplazado':
                reemplazo = v
        g = 0
        while os.path.isfile(os.path.join(PUB, d, f'{g + 1}.csv')):
            g += 1
        out[d] = {'titulo': t, 'graficas': g, 'retirado': retirado, 'reemplazo': reemplazo}
    return out


def vigente(carp, cod):
    """Adónde llega de verdad indicador.php?id=cod, siguiendo los 301 de los
    indicadores retirados."""
    visto, actual = set(), carp.get(cod)
    while actual and actual['retirado'] and actual['reemplazo'] and cod not in visto:
        visto.add(cod)
        cod = actual['reemplazo']
        actual = carp.get(cod)
    if actual and actual['retirado']:
        return None
    return actual


def asignar(enlaces, candidatos, carp):
    """Asignación uno a uno entre los rótulos de una página y los indicadores
    del observatorio, de la pareja más parecida a la menos.

    El desempate es por cercanía al código que el enlace ya tenía, y no es un
    detalle: en Social hay seis títulos repetidos, porque el mismo indicador
    está publicado aparte para cada grupo poblacional. «Suicidios» es el 2005 y
    también el 2802. Sin desempatar, el bloque general de la página terminaba
    apuntando al de una población concreta.
    """
    usados_r, usados_c, asignado = set(), set(), {}
    # Los alias se reservan primero: son decisiones tomadas, no candidatas.
    for i, (_, rot) in enumerate(enlaces):
        cid = ALIAS.get(comparable(rot))
        if cid and cid in candidatos:
            usados_r.add(i)
            usados_c.add(cid)
            asignado[i] = (cid, 1.0)
    pares = sorted(((parecido(rot, carp[cid]['titulo']), -abs(int(cid) - int(cod)),
                     -int(cid), i, cid)
                    for i, (cod, rot) in enumerate(enlaces) for cid in candidatos),
                   reverse=True)
    for r, _, _, i, cid in pares:
        if r < UMBRAL_FIX or i in usados_r or cid in usados_c:
            continue
        usados_r.add(i)
        usados_c.add(cid)
        asignado[i] = (cid, round(r, 3))
    return asignado


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--arreglar', action='store_true')
    args = ap.parse_args()

    carp = carpetas()
    total = correctos = 0
    cambiados, dudosos, vacios = [], [], []

    for pagina, digito in PAGINAS.items():
        ruta = os.path.join(BASE, 'website', pagina)
        if not os.path.isfile(ruta):
            continue
        html = io.open(ruta, encoding='utf-8').read()
        enlaces = [(m.group(2), m.group(5)) for m in ENLACE.finditer(html)]
        total += len(enlaces)
        if not enlaces:
            continue
        candidatos = [cid for cid, c in carp.items()
                      if cid[0] == digito and c['graficas'] > 0 and not c['retirado']]
        asignado = asignar(enlaces, candidatos, carp)

        # La asignación cubre todos los enlaces, no solo los que se ven mal. El
        # umbral de «se ve bien» deja pasar falsos positivos entre títulos de la
        # misma familia: «Material inadecuado de pisos» y «Material inadecuado de
        # paredes exteriores» se parecen lo suficiente como para que cada uno
        # acepte la carpeta del otro. Resuelto en conjunto, cada rótulo se queda
        # con su coincidencia exacta y ninguno repite carpeta.
        cambios = {}
        for i, (cod, rot) in enumerate(enlaces):
            destino = vigente(carp, cod)
            titulo = destino['titulo'] if destino else '(no existe)'
            prop = asignado.get(i)
            if prop and prop[0] != cod:
                cambios[i] = prop[0]
                cambiados.append((pagina, cod, prop[0], rot, titulo, prop[1]))
            elif prop:
                correctos += 1
            elif destino and mismo_indicador(rot, titulo):
                if destino['graficas'] == 0:
                    vacios.append((pagina, cod, rot))
                else:
                    correctos += 1
            else:
                dudosos.append((pagina, cod, rot, titulo))

        if args.arreglar and cambios:
            n = [0]

            def sustituir(m):
                i = n[0]
                n[0] += 1
                nuevo = cambios.get(i, m.group(2))
                return m.group(1) + nuevo + m.group(3) + m.group(4) + m.group(5) + m.group(6)

            io.open(ruta, 'w', encoding='utf-8', newline='\n').write(ENLACE.sub(sustituir, html))
            print(f'   {pagina}: {len(cambios)} enlace(s) reapuntado(s)')

    print(f'{total} enlaces revisados')
    print(f'   el rótulo y el indicador que abren coinciden: {correctos}')
    print(f'   reapuntados / reapuntables: {len(cambiados)}')
    print(f'   hay que decidirlos a mano: {len(dudosos)}')
    print(f'   llevan a un indicador sin gráficas: {len(vacios)}')

    if cambiados:
        print('\nreapuntados:')
        for pag, viejo, nuevo, rot, tit, r in cambiados:
            print(f'   {pag[6:-4]:11} {viejo} -> {nuevo}  «{rot[:40]:42}» '
                  f'antes abría «{tit[:36]}» ({r})')
    if dudosos:
        print('\npara decidir a mano (no hay indicador publicado que corresponda):')
        for pag, cod, rot, tit in dudosos:
            print(f'   {pag[6:-4]:11} {cod}  «{rot[:40]:42}» abre «{tit[:36]}»')
    if vacios:
        print('\nllevan a un indicador publicado sin ninguna gráfica:')
        for pag, cod, rot in vacios:
            print(f'   {pag[6:-4]:11} {cod}  «{rot[:50]}»')


if __name__ == '__main__':
    main()
