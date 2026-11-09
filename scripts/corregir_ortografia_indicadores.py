# -*- coding: utf-8 -*-
"""
Corrige la ortografía de las fichas de indicadores (website/indicador/*/*.info).

Las reglas no salen de un diccionario genérico: salen de revisar el propio
sitio. Si «educación» aparece 121 veces con tilde y 10 sin ella, las diez están
mal. Ese cruce encontró además dos casos donde la forma CON tilde era la
equivocada —«aíre» y «túberculos»—, que un corrector automático habría dado por
buenas.

Lo que no se toca:
  · Las líneas con URL. «compite.com.co/indice-departamental» es una dirección,
    no una palabra mal escrita.
  · El verbo «publica». «Su información se publica ahora en…» está bien; solo
    el adjetivo lleva tilde, y no aparece en las fichas.
  · «como» y «esta», que son correctos en todos los casos revisados.

Uso:
    python scripts/corregir_ortografia_indicadores.py           (muestra qué cambiaría)
    python scripts/corregir_ortografia_indicadores.py --aplicar (escribe los archivos)
"""
import io
import os
import re
import sys

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
FICHAS = os.path.join(BASE, 'website', 'indicador')

# Erratas: palabras mal escritas, no solo tildes ausentes.
ERRATAS = [
    (r'Nacidad', 'Nacidas'), (r'nacidad', 'nacidas'),
    (r'Adolecent', 'Adolescent'), (r'adolecent', 'adolescent'),
    (r'contral la libertad', 'contra la libertad'),
    (r'conflicto armada', 'conflicto armado'),
    (r'Ejecición', 'Ejecución'),
    (r'municpios', 'municipios'),
    (r'inidice', 'índice'),
    (r'Boyocá', 'Boyacá'),
    (r'sembrada se túberculos', 'sembrada de tubérculos'),
    (r'extra pulmonar', 'extrapulmonar'),
    (r'Atenciónes', 'Atenciones'), (r'atenciónes', 'atenciones'),
    (r'\baíre\b', 'aire'),
    (r'\btúberculos\b', 'tubérculos'), (r'\btuberculos\b', 'tubérculos'),
    (r'4 o mas controles', '4 o más controles'),
]

# Tildes que faltan. Cada una la escribe bien el propio sitio en otro lugar.
TILDES = [
    ('agricola', 'agrícola'), ('area', 'área'), ('areas', 'áreas'),
    ('Boyaca', 'Boyacá'), ('boyaca', 'Boyacá'),
    ('categoria', 'categoría'), ('Categoria', 'Categoría'),
    ('clasificacion', 'clasificación'), ('Clasificacion', 'Clasificación'),
    ('critico', 'crítico'), ('Demografia', 'Demografía'),
    ('desercion', 'deserción'), ('Desercion', 'Deserción'),
    ('destinacion', 'destinación'), ('dimension', 'dimensión'),
    ('economico', 'económico'), ('educacion', 'educación'),
    ('ejecucion', 'ejecución'), ('Ejecucion', 'Ejecución'),
    ('especifica', 'específica'), ('ESTADISTICAS', 'ESTADÍSTICAS'),
    ('estadistico', 'estadístico'), ('estan', 'están'),
    ('evaluacion', 'evaluación'), ('Evaluacion', 'Evaluación'),
    ('Fiscalia', 'Fiscalía'), ('formacion', 'formación'),
    ('Formacion', 'Formación'), ('generacion', 'generación'),
    ('Generacion', 'Generación'), ('gestion', 'gestión'),
    ('Gestion', 'Gestión'), ('hectarea', 'hectárea'), ('hectareas', 'hectáreas'),
    ('Hectareas', 'Hectáreas'), ('HECTAREa', 'HECTÁREA'),
    ('Indice', 'Índice'), ('indice', 'índice'),
    ('informacion', 'información'), ('Informacion', 'Información'),
    ('inversion', 'inversión'), ('Inversion', 'Inversión'),
    ('items', 'ítems'), ('jovenes', 'jóvenes'), ('Jovenes', 'Jóvenes'),
    ('jurisdiccion', 'jurisdicción'), ('maestria', 'maestría'),
    ('medicion', 'medición'), ('Medicion', 'Medición'),
    ('metodologia', 'metodología'), ('Mineria', 'Minería'),
    ('nacion', 'nación'), ('Nacion', 'Nación'),
    ('ninas', 'niñas'), ('Ninas', 'Niñas'),
    ('ninos', 'niños'), ('Ninos', 'Niños'),
    ('numero', 'número'), ('Numero', 'Número'),
    ('ocupo', 'ocupó'), ('participacion', 'participación'),
    ('Participacion', 'Participación'), ('policia', 'policía'),
    ('posicion', 'posición'), ('Posicion', 'Posición'),
    ('produccion', 'producción'), ('Produccion', 'Producción'),
    ('publicas', 'públicas'), ('Publicas', 'Públicas'),
    ('regalias', 'regalías'), ('segun', 'según'), ('Segun', 'Según'),
    ('tecnologica', 'tecnológica'), ('Tecnologica', 'Tecnológica'),
    ('vacunacion', 'vacunación'), ('Vacunacion', 'Vacunación'),
    ('Victimas', 'Víctimas'), ('victimas', 'víctimas'),
    ('aprobacion', 'aprobación'), ('Aprobacion', 'Aprobación'),
    ('reprobacion', 'reprobación'), ('Reprobacion', 'Reprobación'),
    ('proyeccion', 'proyección'), ('Proyeccion', 'Proyección'),
    ('afiliacion', 'afiliación'), ('Afiliacion', 'Afiliación'),
    ('sancion', 'sanción'), ('sanciones', 'sanciones'),
    ('poblacion', 'población'), ('Poblacion', 'Población'),
    ('perdida', 'pérdida'), ('Perdida', 'Pérdida'),
    ('desaparicion', 'desaparición'), ('Desaparicion', 'Desaparición'),
    ('indigenas', 'indígenas'), ('Indigenas', 'Indígenas'),
    ('hidrico', 'hídrico'), ('Hidrico', 'Hídrico'),
    ('analisis', 'análisis'), ('Analisis', 'Análisis'),
    ('basica', 'básica'), ('Basica', 'Básica'),
    ('parametros', 'parámetros'), ('parametro', 'parámetro'),
    ('autofinancacion', 'autofinanciación'),
    ('continuacion', 'continuación'), ('movilizacion', 'movilización'),
]

REGLAS = ([(re.compile(p), r) for p, r in ERRATAS]
          + [(re.compile(r'\b%s\b' % re.escape(m)), b) for m, b in TILDES])


def corregir(linea):
    """Aplica las reglas a una línea, salvo que sea una URL."""
    if 'http' in linea or 'www.' in linea:
        return linea
    nueva = linea
    for rx, rep in REGLAS:
        nueva = rx.sub(rep, nueva)
    return nueva


def main():
    aplicar = '--aplicar' in sys.argv
    cambios, archivos = [], 0
    for carpeta in sorted(os.listdir(FICHAS)):
        d = os.path.join(FICHAS, carpeta)
        if not os.path.isdir(d):
            continue
        for nombre in sorted(os.listdir(d)):
            if not nombre.endswith('.info'):
                continue
            ruta = os.path.join(d, nombre)
            try:
                texto = io.open(ruta, encoding='utf-8').read()
            except UnicodeDecodeError:
                print('!! no es UTF-8, se omite:', ruta)
                continue
            lineas = texto.splitlines(True)
            nuevas, tocado = [], False
            for ln in lineas:
                n = corregir(ln)
                if n != ln:
                    tocado = True
                    cambios.append((carpeta + '/' + nombre, ln.strip(), n.strip()))
                nuevas.append(n)
            if tocado:
                archivos += 1
                if aplicar:
                    io.open(ruta, 'w', encoding='utf-8', newline='').write(''.join(nuevas))

    for archivo, antes, despues in cambios[:40]:
        print('  %-20s %s' % (archivo, antes[:76]))
        print('  %-20s %s' % ('', despues[:76]))
    if len(cambios) > 40:
        print('  … y %d líneas más' % (len(cambios) - 40))
    print('\n%d líneas en %d archivos%s'
          % (len(cambios), archivos, ' (ESCRITOS)' if aplicar else ' (simulación)'))


if __name__ == '__main__':
    main()
