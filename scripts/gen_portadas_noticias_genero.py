# -*- coding: utf-8 -*-
"""
Genera portadas propias para las noticias del Observatorio de Asuntos de Género
que llegaron sin material gráfico (publicaciones del ICBF – Regional Boyacá).

Son piezas del portal, no del ICBF: usan la paleta del observatorio, citan la
fuente al pie y no reproducen la identidad visual de la entidad remitente.
Se guardan en website/uploads/cms/2026/09/ (versionadas en el repositorio).
"""
import os
from PIL import Image, ImageDraw, ImageFont, ImageFilter

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(BASE, 'website', 'uploads', 'cms', '2026', '09')
W, H = 1200, 630

F_BOLD = r'C:\Windows\Fonts\segoeuib.ttf'
F_REG = r'C:\Windows\Fonts\segoeui.ttf'
F_EMO = r'C:\Windows\Fonts\seguiemj.ttf'

MORADO = (125, 45, 145)
ROSA = (239, 111, 143)

# slug, cifra, unidad, titular corto, emoji
NOTICIAS = [
    ('icbf-una-hora-por-la-prevencion', '225', 'instituciones educativas',
     'Una Hora por la Prevención', '🏫', (94, 37, 129), (196, 63, 121)),
    ('icbf-padres-en-tu-trabajo', '3.653', 'participantes en empresas',
     'Padres en tu Trabajo', '👨‍👩‍👧', (31, 78, 121), (56, 154, 162)),
    ('icbf-feria-del-buen-trato', '12.708', 'niñas, niños y adolescentes',
     'Feria del Buen Trato', '🎈', (176, 62, 40), (232, 149, 58)),
    ('icbf-llegar-juntos-y-a-tiempo-puerto-boyaca', 'Puerto Boyacá', '',
     'Llegar Juntos y a Tiempo', '🤝', (17, 94, 89), (86, 168, 120)),
    ('icbf-territorio-embera-puerto-boyaca', 'Emberá Katío', '',
     'Diálogo intercultural por los derechos de niñas y mujeres', '🌿', (72, 52, 130), (140, 92, 178)),
    ('icbf-resultados-estrategia-boyactuar', 'BOYACTUAR', '',
     'Prevención de violencias contra niñas, niños y adolescentes', '🛡️', (125, 45, 145), (239, 111, 143)),
]

def font(path, size):
    return ImageFont.truetype(path, size)

def emoji(size):
    try:
        return ImageFont.truetype(F_EMO, size)
    except Exception:
        return None

def degradado(c1, c2):
    """Fondo diagonal suave entre dos colores."""
    small = Image.new('RGB', (W // 6, H // 6))
    px = small.load()
    for y in range(small.height):
        for x in range(small.width):
            t = (x / small.width * 0.65) + (y / small.height * 0.35)
            px[x, y] = tuple(int(c1[i] + (c2[i] - c1[i]) * t) for i in range(3))
    return small.resize((W, H), Image.LANCZOS).filter(ImageFilter.GaussianBlur(2))

def wrap(draw, texto, fnt, ancho):
    lineas, actual = [], ''
    for palabra in texto.split():
        prueba = (actual + ' ' + palabra).strip()
        if draw.textlength(prueba, font=fnt) <= ancho:
            actual = prueba
        else:
            if actual:
                lineas.append(actual)
            actual = palabra
    if actual:
        lineas.append(actual)
    return lineas

def portada(slug, cifra, unidad, titular, emo, c1, c2):
    img = degradado(c1, c2)
    d = ImageDraw.Draw(img, 'RGBA')

    # círculos decorativos
    d.ellipse((W - 300, -160, W + 160, 300), fill=(255, 255, 255, 22))
    d.ellipse((W - 190, H - 240, W + 130, H + 80), fill=(255, 255, 255, 16))
    d.ellipse((-120, H - 210, 190, H + 100), fill=(0, 0, 0, 28))

    # franja superior con el observatorio
    d.rectangle((0, 0, W, 8), fill=(255, 255, 255, 70))
    f_sup = font(F_BOLD, 24)
    d.text((64, 52), 'OBSERVATORIO DE ASUNTOS DE GÉNERO', font=f_sup, fill=(255, 255, 255, 235))
    d.text((64, 88), 'Red de Observatorios de Boyacá', font=font(F_REG, 22), fill=(255, 255, 255, 190))

    # emoji grande a la derecha
    fe = emoji(190)
    if fe:
        try:
            d.text((W - 300, 190), emo, font=fe, embedded_color=True)
        except Exception:
            pass

    # cifra destacada
    y = 190
    f_cifra = font(F_BOLD, 104 if len(cifra) <= 6 else 74)
    d.text((64, y), cifra, font=f_cifra, fill=(255, 255, 255))
    y += (118 if len(cifra) <= 6 else 92)
    if unidad:
        d.text((66, y), unidad.upper(), font=font(F_BOLD, 26), fill=(255, 255, 255, 225))
        y += 46

    # titular
    f_tit = font(F_REG, 40)
    lineas = wrap(d, titular, f_tit, 690)[:3]
    y = y + 26
    # línea de acento sobre el titular
    d.rectangle((66, y - 14, 66 + 96, y - 8), fill=(255, 255, 255, 200))
    y += 12
    for ln in lineas:
        d.text((64, y), ln, font=f_tit, fill=(255, 255, 255, 245))
        y += 48

    # pie con la fuente
    d.rectangle((0, H - 54, W, H), fill=(0, 0, 0, 105))
    d.text((64, H - 40), 'Información remitida por el ICBF – Regional Boyacá · 2026',
           font=font(F_REG, 21), fill=(255, 255, 255, 225))

    ruta = os.path.join(OUT, slug + '.jpg')
    img.convert('RGB').save(ruta, 'JPEG', quality=88, optimize=True, progressive=True)
    return ruta

os.makedirs(OUT, exist_ok=True)
for n in NOTICIAS:
    p = portada(*n)
    print('->', os.path.relpath(p, BASE), round(os.path.getsize(p) / 1024), 'KB')
