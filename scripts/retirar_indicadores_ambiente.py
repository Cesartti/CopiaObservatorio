# -*- coding: utf-8 -*-
"""
Marca como RETIRADAS las carpetas de indicadores de la estructura anterior del
Observatorio Ambiental (3001–3047).

El despliegue de produccion usa `rsync` sin `--delete`, de modo que borrar las
carpetas del repositorio no las elimina del servidor: seguirian apareciendo en
el micrositio con las categorias viejas. En su lugar se reescribe su
`indicador.info` con las claves `Retirado:1` y `Reemplazado:<nuevo id>`, que el
sitio usa para ocultarlas del listado y redirigir al indicador vigente.
"""
import os, re, unicodedata

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IND = os.path.join(BASE, 'website', 'indicador')
BK = os.path.join(BASE, 'reportes', 'ambiental_2026', 'backup_indicadores_3xxx')

# Equivalencias explicitas (servicios publicos y agua)
MAP = {
    '3001': 3512,  # edificaciones con ahorro de energia -> practicas de ahorro
    '3002': 3201,  # cobertura acueducto urbano
    '3003': 3203,  # alcantarillado urbano
    '3004': 3501,  # aseo urbano
    '3005': 3205,  # continuidad (horas)
    '3006': 3208,  # IRCA
    '3007': 3205,  # continuidad (dias/semana)
    '3008': 3206,  # tratamiento de aguas residuales
}
# Calidad del aire: por contaminante
POLL = [('pm-2.5', 3507), ('pm2.5', 3507), ('pm-10', 3506), ('pm10', 3506),
        ('so2', 3508), ('no2', 3509), ('o3', 3511), (' co ', 3510)]

def norm(s):
    return unicodedata.normalize('NFKD', str(s)).encode('ascii', 'ignore').decode().lower()

def target_for(code, titulo):
    if code in MAP:
        return MAP[code]
    t = ' ' + norm(titulo) + ' '
    for key, dest in POLL:
        if key in t:
            return dest
    return None

def read_info(path):
    out = {}
    if os.path.isfile(path):
        for line in open(path, encoding='utf-8', errors='ignore'):
            if ':' in line:
                k, v = line.split(':', 1)
                out[norm(k).strip()] = v.strip()
    return out

done = []
for code in sorted(e for e in os.listdir(BK) if e.isdigit() and len(e) == 4 and e[0] == '3'):
    info = read_info(os.path.join(BK, code, 'indicador.info'))
    titulo = info.get('titulo', 'Indicador ' + code)
    dest = target_for(code, titulo)
    folder = os.path.join(IND, code)
    os.makedirs(folder, exist_ok=True)
    lines = [
        'Retirado:1',
        'Titulo:' + titulo,
        'Categoría:' + info.get('categoria', ''),
        'Descripción:Indicador retirado en la reestructuración del Observatorio Ambiental '
        '(septiembre de 2026). Su información se publica ahora en un indicador vigente.',
        'Subcategoría:' + info.get('subcategoria', ''),
        'Etiquetas:Retirado',
        'Fuentes:' + info.get('fuentes', ''),
    ]
    if dest:
        lines.insert(1, 'Reemplazado:%d' % dest)
    open(os.path.join(folder, 'indicador.info'), 'w', encoding='utf-8', newline='\n').write(
        '\n'.join(lines) + '\n')
    # Sin graficas: el indicador ya no se dibuja.
    open(os.path.join(folder, 'display.js'), 'w', encoding='utf-8', newline='\n').write(
        '/* Indicador retirado: ver el indicador vigente. */\n')
    for f in os.listdir(folder):
        if re.match(r'^\d+\.(csv|info)$', f):
            os.remove(os.path.join(folder, f))
    done.append((code, dest, titulo[:52]))

print('carpetas marcadas como retiradas:', len(done))
for c, d, t in done:
    print(' ', c, '->', d or 'sin reemplazo', '|', t)
