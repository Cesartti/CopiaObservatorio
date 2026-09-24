# -*- coding: utf-8 -*-
"""
Lee solo la ventana de Boyacá de los mosaicos globales de VHI de la NOAA.

Cada archivo pesa unos 32 MB porque cubre el planeta entero, pero Boyacá ocupa
apenas 79 filas de las 3.616. Como el TIFF guarda una fila por «strip» y el
servidor de la NOAA acepta peticiones por rango, se pueden traer únicamente
esas filas: unos 300 KB en vez de 32 MB, cien veces menos. Eso es lo que hace
viable reconstruir el histórico semana por semana.

El procedimiento es el de cualquier lector de TIFF:
  1. se leen los primeros bytes para ubicar el directorio de imagen (IFD);
  2. del IFD se sacan las tablas de posición y tamaño de cada fila;
  3. se pide el rango de bytes que cubre solo las filas de interés;
  4. se arma en memoria un TIFF pequeño con esas filas y lo decodifica Pillow,
     que ya sabe deshacer la compresión LZW.

No requiere GDAL ni rasterio.
"""
import io
import ssl
import struct
import urllib.request

import numpy as np
from PIL import Image

Image.MAX_IMAGE_PIXELS = None

CTX = ssl.create_default_context()
CAB = {'User-Agent': 'RedObservatoriosBoyaca/1.0 (observatorios.boyaca.gov.co)'}

# Etiquetas TIFF que interesan.
ANCHO, ALTO, BITS, COMPRESION = 256, 257, 258, 259
FOTOMETRIA, POSICIONES, MUESTRAS = 262, 273, 277
FILAS_POR_STRIP, TAMANOS, PLANAR, FORMATO = 278, 279, 284, 339

# Tamaño en bytes de cada tipo de dato del formato.
TAM = {1: 1, 2: 1, 3: 2, 4: 4, 5: 8, 6: 1, 7: 1, 8: 2, 9: 4, 10: 8, 11: 4, 12: 8}


def _pedir(url, desde=None, hasta=None, timeout=120):
    cab = dict(CAB)
    if desde is not None:
        cab['Range'] = f'bytes={desde}-' + ('' if hasta is None else str(hasta))
    r = urllib.request.Request(url, headers=cab)
    resp = urllib.request.urlopen(r, timeout=timeout, context=CTX)

    return resp.read(), resp


def _leer_ifd(url):
    """Devuelve (orden, etiquetas) donde etiquetas[tag] = (tipo, cantidad, valor_o_offset)."""
    cabecera, _ = _pedir(url, 0, 15)
    orden = '<' if cabecera[:2] == b'II' else '>'
    (magia,) = struct.unpack(orden + 'H', cabecera[2:4])
    if magia != 42:
        raise ValueError('No es un TIFF clásico (magia %d)' % magia)
    (off_ifd,) = struct.unpack(orden + 'I', cabecera[4:8])

    crudo, _ = _pedir(url, off_ifd, off_ifd + 1)
    (n,) = struct.unpack(orden + 'H', crudo[:2])
    cuerpo, _ = _pedir(url, off_ifd + 2, off_ifd + 2 + n * 12 - 1)

    etiquetas = {}
    for i in range(n):
        tag, tipo, cant = struct.unpack(orden + 'HHI', cuerpo[i * 12:i * 12 + 8])
        bruto = cuerpo[i * 12 + 8:i * 12 + 12]
        etiquetas[tag] = (tipo, cant, bruto)

    return orden, etiquetas, off_ifd


def _valores(url, orden, etiqueta):
    """Resuelve una etiqueta: si no cabe en 4 bytes, va a buscar el arreglo."""
    tipo, cant, bruto = etiqueta
    ancho = TAM.get(tipo, 4)
    fmt = {1: 'B', 3: 'H', 4: 'I', 8: 'h', 9: 'i'}.get(tipo, 'I')
    if cant * ancho <= 4:
        datos = bruto
    else:
        (off,) = struct.unpack(orden + 'I', bruto)
        datos, _ = _pedir(url, off, off + cant * ancho - 1)

    return list(struct.unpack(orden + fmt * cant, datos[:cant * ancho]))


def ventana(url, fila0, fila1):
    """
    Descarga y decodifica las filas [fila0, fila1) del mosaico.

    Devuelve un arreglo numpy de (fila1-fila0) x ancho, y el número de bytes
    que hubo que traer.
    """
    orden, tags, _ = _leer_ifd(url)
    ancho = _valores(url, orden, tags[ANCHO])[0]
    alto = _valores(url, orden, tags[ALTO])[0]
    bits = _valores(url, orden, tags[BITS])[0]
    compresion = _valores(url, orden, tags[COMPRESION])[0]
    fotometria = _valores(url, orden, tags[FOTOMETRIA])[0]
    por_strip = _valores(url, orden, tags[FILAS_POR_STRIP])[0]
    formato = _valores(url, orden, tags[FORMATO])[0] if FORMATO in tags else 1

    if por_strip != 1:
        raise ValueError('Se esperaba una fila por strip, no %d' % por_strip)

    fila0 = max(0, min(fila0, alto))
    fila1 = max(fila0, min(fila1, alto))
    posiciones = _valores(url, orden, tags[POSICIONES])[fila0:fila1]
    tamanos = _valores(url, orden, tags[TAMANOS])[fila0:fila1]
    if not posiciones:
        raise ValueError('Rango de filas vacío')

    # Los strips son consecutivos: basta una sola petición para todos.
    desde = posiciones[0]
    hasta = posiciones[-1] + tamanos[-1] - 1
    bloque, _ = _pedir(url, desde, hasta, timeout=300)

    # Se arma un TIFF mínimo con esas filas para que Pillow haga la decodificación.
    n = len(posiciones)
    datos = bytearray()
    nuevas_pos = []
    for p, t in zip(posiciones, tamanos):
        nuevas_pos.append(8 + len(datos))
        datos += bloque[p - desde:p - desde + t]

    # El IFD va después de los datos; las tablas largas, justo después del IFD.
    off_ifd = 8 + len(datos)
    cabecera = struct.pack('<2sHI', b'II', 42, off_ifd)
    campos = [(ANCHO, 3, 1, ancho), (ALTO, 3, 1, n), (BITS, 3, 1, bits),
              (COMPRESION, 3, 1, compresion), (FOTOMETRIA, 3, 1, fotometria),
              (POSICIONES, 4, n, None), (MUESTRAS, 3, 1, 1),
              (FILAS_POR_STRIP, 3, 1, 1), (TAMANOS, 4, n, None),
              (PLANAR, 3, 1, 1), (FORMATO, 3, 1, formato)]
    campos.sort()
    base_tablas = off_ifd + 2 + len(campos) * 12 + 4

    ifd = struct.pack('<H', len(campos))
    desplazamiento = base_tablas
    tablas = bytearray()
    for tag, tipo, cant, valor in campos:
        if valor is None:
            ifd += struct.pack('<HHII', tag, tipo, cant, desplazamiento)
            arr = nuevas_pos if tag == POSICIONES else tamanos
            tablas += struct.pack('<' + 'I' * cant, *arr)
            desplazamiento += cant * 4
        elif tipo == 3:
            ifd += struct.pack('<HHIHH', tag, tipo, cant, valor, 0)
        else:
            ifd += struct.pack('<HHII', tag, tipo, cant, valor)
    ifd += struct.pack('<I', 0)

    im = Image.open(io.BytesIO(cabecera + bytes(datos) + ifd + bytes(tablas)))

    return np.array(im, dtype=np.float32), len(bloque)
