-- 035: corrige la ortografía del contenido que vive en la base de datos.
--
-- El repaso de ortografía del sitio se hizo cruzando el propio texto del
-- portal: si una palabra aparece 300 veces con tilde y 25 sin ella, esas 25
-- están mal. Lo que estaba en archivos se corrigió en el repositorio; lo que
-- está en la base necesita esta migración.
--
-- Qué se arregla:
--   · 25 hojas de vida decían «Departamento Nacional de Estadistica». Se
--     corrige la tilde y además el nombre, que es «Departamento
--     Administrativo Nacional de Estadística».
--   · Los títulos de los indicadores 1020 y 1021 decían «Segun»/«segun».
--   · El observatorio 1 se llamaba «Observatorio Economico».
--   · La sección «Marco institucional» titulaba un acordeón «Proposito»; la
--     de «Reportes sociales» hablaba de «asuntos de genero».
--   · Los seis gráficos de demostración decían «Demostracion», «categorias»
--     y «composicion».
--
-- Todo son REPLACE sobre el texto exacto: si la cadena ya está corregida la
-- sentencia no hace nada, así que la migración puede volver a correr sin
-- efecto. No inserta ni borra filas.

-- ── Hojas de vida: nombre del DANE ──────────────────────────────────────────
UPDATE indicators
   SET source = REPLACE(source,
                        'Departamento Nacional de Estadistica - DANE',
                        'Departamento Administrativo Nacional de Estadística - DANE')
 WHERE source LIKE BINARY '%Departamento Nacional de Estadistica - DANE%';

-- Por si quedara la forma sin tilde con otra redacción alrededor.
UPDATE indicators SET source = REPLACE(source, 'Estadistica', 'Estadística')
 WHERE source LIKE BINARY '%Estadistica%';
UPDATE indicators SET actors = REPLACE(actors, 'Estadistica', 'Estadística')
 WHERE actors LIKE BINARY '%Estadistica%';
UPDATE indicators SET observations = REPLACE(observations, 'Estadistica', 'Estadística')
 WHERE observations LIKE BINARY '%Estadistica%';

-- ── Títulos de indicadores ──────────────────────────────────────────────────
UPDATE indicators SET title = REPLACE(title, 'Monetaria Segun Sexo', 'Monetaria Según Sexo')
 WHERE title LIKE BINARY '%Monetaria Segun Sexo%';
UPDATE indicators SET title = REPLACE(title, 'Extrema segun sexo', 'Extrema según sexo')
 WHERE title LIKE BINARY '%Extrema segun sexo%';

-- ── Nombre del observatorio ─────────────────────────────────────────────────
UPDATE observatories SET name = 'Observatorio Económico'
 WHERE name = 'Observatorio Economico';

-- ── Secciones de micrositio ─────────────────────────────────────────────────
UPDATE cms_microsite_sections
   SET body_html = REPLACE(body_html, '>Proposito<', '>Propósito<')
 WHERE body_html LIKE BINARY '%>Proposito<%';

UPDATE cms_microsite_sections
   SET body_html = REPLACE(body_html, 'asuntos de genero.', 'asuntos de género.')
 WHERE body_html LIKE BINARY '%asuntos de genero.%';

-- ── Gráficos de demostración del tablero ────────────────────────────────────
UPDATE cms_charts SET title = REPLACE(title, 'Demostracion', 'Demostración')
 WHERE title LIKE BINARY '%Demostracion%';
UPDATE cms_charts SET title = REPLACE(title, 'categorias', 'categorías')
 WHERE title LIKE BINARY '%categorias%';
UPDATE cms_charts SET title = REPLACE(title, 'composicion', 'composición')
 WHERE title LIKE BINARY '%composicion%';

-- ── Texto heredado de la base inicial de la Gobernación ─────────────────────
-- Estas filas ya no están en la base local (la hoja de vida se rehízo), pero
-- la migración 031 todavía las contiene tal como llegaron: en mayúsculas y sin
-- tildes. Si alguna sobrevive en el servidor, queda corregida aquí; si no, las
-- sentencias no encuentran nada y siguen de largo.
UPDATE indicators SET actors = REPLACE(REPLACE(REPLACE(REPLACE(actors,
            'Secretaria De', 'Secretaría De'),
            'Planeacion', 'Planeación'),
            'Estadistico', 'Estadístico'),
            'Observatorio Economico', 'Observatorio Económico')
 WHERE actors LIKE BINARY '%Secretaria De%'
    OR actors LIKE BINARY '%Planeacion%'
    OR actors LIKE BINARY '%Estadistico%'
    OR actors LIKE BINARY '%Observatorio Economico%';

UPDATE indicators SET responsible_entity = REPLACE(REPLACE(REPLACE(responsible_entity,
            'Secretaria De', 'Secretaría De'),
            'Planeacion', 'Planeación'),
            'Observatorio Economico', 'Observatorio Económico')
 WHERE responsible_entity LIKE BINARY '%Secretaria De%'
    OR responsible_entity LIKE BINARY '%Planeacion%'
    OR responsible_entity LIKE BINARY '%Observatorio Economico%';

UPDATE indicators SET calculation_formula = REPLACE(REPLACE(REPLACE(calculation_formula,
            'Hectareas', 'Hectáreas'),
            'Participacion', 'Participación'),
            'Area Sembrada', 'Área Sembrada')
 WHERE calculation_formula LIKE BINARY '%Hectareas%'
    OR calculation_formula LIKE BINARY '%Participacion%'
    OR calculation_formula LIKE BINARY '%Area Sembrada%';

UPDATE indicators SET unit = 'HECTÁREAS' WHERE unit = 'HECTAREAS';

UPDATE indicators SET observations = REPLACE(REPLACE(observations,
            'GOBERNACION', 'GOBERNACIÓN'),
            'BOYACA', 'BOYACÁ')
 WHERE observations LIKE BINARY '%GOBERNACION%'
    OR observations LIKE BINARY '%BOYACA%';
