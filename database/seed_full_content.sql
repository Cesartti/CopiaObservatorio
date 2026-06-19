-- ============================================================================
--  SEED: CONTENIDO MÍNIMO VIABLE PARA LA RED DE OBSERVATORIOS
-- ============================================================================
--  Ejecutar DESPUÉS de schema.sql + migraciones + seed_example.sql
--  Carga: contacto, banners, redes sociales, noticias por observatorio,
--         etiquetas, hero slides y datos de contacto.
--  Todas las sentencias usan INSERT IGNORE o NOT EXISTS para ser idempotentes.
-- ============================================================================

SET NAMES utf8mb4;

-- ══════════════════════════════════════════════════════════════════════════════
-- 1. DATOS DE CONTACTO INSTITUCIONAL
-- ══════════════════════════════════════════════════════════════════════════════
INSERT INTO cms_contact (id, institution, address, phone, email, hours, url_facebook, url_instagram, url_x, url_youtube)
VALUES (1,
    'Gobernación de Boyacá – Secretaría de Planeación',
    'Calle 20 No. 9-90, Palacio de la Torre, Tunja, Boyacá',
    '(608) 740 5076',
    'planeacion@boyaca.gov.co',
    'Lunes a viernes 8:00 a.m. - 12:00 m. y 2:00 p.m. - 6:00 p.m.',
    'https://www.facebook.com/GobernaciondeBoyaca',
    'https://www.instagram.com/secplaneacionboyaca/',
    'https://x.com/GobBoyaca',
    'https://www.youtube.com/@GobernaciondeBoyaca'
) ON DUPLICATE KEY UPDATE
    institution = VALUES(institution),
    address = VALUES(address),
    phone = VALUES(phone),
    email = VALUES(email),
    hours = VALUES(hours),
    url_facebook = VALUES(url_facebook),
    url_instagram = VALUES(url_instagram),
    url_x = VALUES(url_x),
    url_youtube = VALUES(url_youtube);

-- ══════════════════════════════════════════════════════════════════════════════
-- 2. BANNERS DEL PORTAL PRINCIPAL
-- ══════════════════════════════════════════════════════════════════════════════
DELETE FROM cms_home_banners;

INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, image_url, is_active) VALUES
(0, 'Red de Observatorios de Boyacá',
   'Plataforma unificada de datos públicos con 5 observatorios especializados para ciudadanía, academia y tomadores de decisión.',
   'Portal', 'assets/svg/logo.svg', 1),
(1, 'Observatorio Económico',
   'Coyuntura, variables macroeconómicas e indicadores de competitividad del departamento. TRM, inflación, empleo y PIB.',
   'Economía', 'assets/svg/icono-economico.svg', 1),
(2, 'Observatorio Social',
   'Dinámicas de violencia, salud pública, pobreza y bienestar. Indicadores con enfoque territorial y poblacional.',
   'Social', 'assets/svg/icono-social.svg', 1),
(3, 'Observatorio de Medio Ambiente',
   'Calidad del aire, agua, biodiversidad y residuos. Datos para acción climática y gestión ambiental.',
   'Ambiente', 'assets/svg/bg-green.svg', 1),
(4, 'Ciencia, Tecnología e Innovación',
   'Capacidades de I+D, innovación, talento humano y ecosistema CTI de Boyacá.',
   'CTI', 'assets/svg/icono-tecnologico.svg', 1),
(5, 'Observatorio de Asuntos de Género',
   'Brechas, violencias, participación y autonomía económica con enfoque diferencial.',
   'Género', 'assets/svg/img-genero/PROPOSITO.svg', 1);

-- ══════════════════════════════════════════════════════════════════════════════
-- 3. PUBLICACIONES DE REDES SOCIALES (Instagram / Facebook)
-- ══════════════════════════════════════════════════════════════════════════════
DELETE FROM cms_social_posts;

INSERT INTO cms_social_posts (sort_order, network, category, title, byline, excerpt, url, post_date, image, media_tag, media_caption, is_active) VALUES
(0, 'instagram', 'GOBERNACIÓN', 'Red de Observatorios activa',
 '@secplaneacionboyaca', 'La Secretaría de Planeación presenta la Red de Observatorios unificada con datos abiertos para Boyacá.',
 'https://www.instagram.com/secplaneacionboyaca/', '2026-04-01', '', 'OBSERVATORIOS', 'Datos abiertos Boyacá', 1),

(1, 'instagram', 'DATOS', 'Indicadores económicos actualizados',
 '@secplaneacionboyaca', 'Consulte PIB, empleo, TRM y más variables macroeconómicas del departamento.',
 'https://www.instagram.com/secplaneacionboyaca/', '2026-04-05', '', 'ECONOMÍA', 'Coyuntura económica', 1),

(2, 'instagram', 'SOCIAL', 'Indicadores de bienestar social',
 '@secplaneacionboyaca', 'Monitoreo de violencia, salud pública y condiciones de pobreza en los municipios de Boyacá.',
 'https://www.instagram.com/secplaneacionboyaca/', '2026-04-08', '', 'SOCIAL', 'Bienestar poblacional', 1),

(3, 'facebook', 'AMBIENTE', 'Datos ambientales disponibles',
 'Gobernación de Boyacá', 'Estado del aire, agua y biodiversidad disponibles en el observatorio ambiental.',
 'https://www.facebook.com/GobernaciondeBoyaca', '2026-04-10', '', 'AMBIENTE', 'Calidad ambiental', 1),

(4, 'instagram', 'GÉNERO', 'Observatorio de Género con rutas de atención',
 '@secplaneacionboyaca', 'Consulte rutas, servicios y seguimiento con enfoque diferencial para mujeres en Boyacá.',
 'https://www.instagram.com/secplaneacionboyaca/', '2026-04-12', '', 'GÉNERO', 'Equidad y derechos', 1),

(5, 'facebook', 'CTI', 'Ciencia e innovación en Boyacá',
 'Gobernación de Boyacá', 'El ecosistema CTI del departamento: investigación, talento y transferencia tecnológica.',
 'https://www.facebook.com/GobernaciondeBoyaca', '2026-04-13', '', 'TECNOLOGÍA', 'Innovación territorial', 1);

-- ══════════════════════════════════════════════════════════════════════════════
-- 4. HERO SLIDES POR MICROSITIO (los 5 observatorios)
-- ══════════════════════════════════════════════════════════════════════════════
-- Económico (obs_id=1)
INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 1 AS observatory_id, 0 AS sort_order,
    'Coyuntura económica territorial' AS title,
    'Monitoree TRM, inflación, empleo y variables macro en una sola vista.' AS slide_text,
    'assets/svg/bg-banner-economico.png' AS image_url
  UNION ALL SELECT 1, 1, 'Indicadores de mercado y finanzas',
    'Panel ejecutivo para análisis rápido de tendencias y variaciones.', 'assets/svg/icono-economico.svg'
  UNION ALL SELECT 1, 2, 'Noticias y alertas económicas',
    'Actualidad, boletines y eventos para toma de decisiones.', 'assets/svg/bg-yellow.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides WHERE observatory_id = 1) = 0;

-- Social (obs_id=2)
INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 2 AS observatory_id, 0 AS sort_order,
    'Bienestar y desarrollo social' AS title,
    'Siga indicadores de salud, educación, empleo y calidad de vida.' AS slide_text,
    'assets/svg/bg-banner-social.png' AS image_url
  UNION ALL SELECT 2, 1, 'Enfoque territorial y poblacional',
    'Analice brechas por municipio, grupos etarios y poblaciones priorizadas.', 'assets/svg/icono-social.svg'
  UNION ALL SELECT 2, 2, 'Información útil para ciudadanía',
    'Exploración simple con contexto para comprender cada indicador.', 'assets/svg/bg-blue.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides WHERE observatory_id = 2) = 0;

-- Ambiente (obs_id=3)
INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 3 AS observatory_id, 0 AS sort_order,
    'Estado ambiental del territorio' AS title,
    'Calidad del aire, agua, residuos y biodiversidad en seguimiento permanente.' AS slide_text,
    'assets/svg/bg-green.svg' AS image_url
  UNION ALL SELECT 3, 1, 'Datos para acción climática',
    'Indicadores temáticos y trazabilidad para apoyar gestión ambiental.', 'assets/svg/bg-menu-b.svg'
  UNION ALL SELECT 3, 2, 'Visualización pública y transparente',
    'Tarjetas, categorías y descargas para uso ciudadano e institucional.', 'assets/svg/bg-blue.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides WHERE observatory_id = 3) = 0;

-- CTI (obs_id=4)
INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 4 AS observatory_id, 0 AS sort_order,
    'Ciencia, tecnología e innovación' AS title,
    'Mida capacidades, proyectos, inversión y resultados del ecosistema CTI.' AS slide_text,
    'assets/svg/icono-tecnologico.svg' AS image_url
  UNION ALL SELECT 4, 1, 'Monitoreo estratégico de capacidades',
    'Panel con métricas de investigación, talento y transferencia.', 'assets/svg/bg-blue.svg'
  UNION ALL SELECT 4, 2, 'Conexión entre academia y territorio',
    'Información para orientar decisiones de política pública e innovación.', 'assets/svg/bg-yellow.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides WHERE observatory_id = 4) = 0;

-- Género (obs_id=5)
INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 5 AS observatory_id, 0 AS sort_order,
    'Asuntos de género con enfoque integral' AS title,
    'Brechas, violencias, participación y autonomía con lectura comprensible.' AS slide_text,
    'assets/svg/img-genero/MARCO-INSTITUCIONAL.svg' AS image_url
  UNION ALL SELECT 5, 1, 'Rutas, servicios y seguimiento',
    'Contenidos de interés ciudadano con enfoque diferencial y territorial.', 'assets/svg/img-genero/POLITICAS-PUBLICAS.svg'
  UNION ALL SELECT 5, 2, 'Información para prevención y decisión',
    'Datos y recursos para instituciones, organizaciones y comunidad.', 'assets/svg/img-genero/OBJETIVO.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides WHERE observatory_id = 5) = 0;

-- ══════════════════════════════════════════════════════════════════════════════
-- 5. NOTICIAS INICIALES (3 por observatorio = 15 total)
-- ══════════════════════════════════════════════════════════════════════════════

-- Económico
INSERT IGNORE INTO news (observatory_id, title, slug, summary, body, source, published_at, content_status, created_by)
VALUES
(1, 'PIB de Boyacá creció 2,3% en el último período',
 'pib-boyaca-crecimiento-2026',
 'El departamento registra un crecimiento positivo impulsado por el sector agropecuario y de servicios.',
 '<p>El DANE publicó las cifras más recientes del Producto Interno Bruto departamental, donde Boyacá muestra una variación positiva del 2,3% respecto al período anterior. Los sectores que más aportaron fueron: agropecuario, comercio y administración pública.</p><p>Estos datos están disponibles en el tablero del Observatorio Económico.</p>',
 'DANE – Cuentas nacionales departamentales', '2026-04-01 08:00:00', 'published', NULL),

(1, 'Tasa de desempleo departamental se ubica en 10,9%',
 'desempleo-boyaca-109-2026',
 'La cifra representa una leve mejoría frente al trimestre anterior.',
 '<p>Según el Gran Encuesta Integrada de Hogares (GEIH), Boyacá registra una tasa de desempleo del 10,9%, manteniéndose por debajo del promedio nacional.</p>',
 'DANE – GEIH', '2026-04-05 09:00:00', 'published', NULL),

(1, 'TRM alcanza niveles de $3.800 en abril',
 'trm-abril-2026',
 'El dólar mantiene tendencia alcista influenciado por factores externos.',
 '<p>La tasa representativa del mercado se ha mantenido en el rango de $3.750-$3.830 durante las primeras semanas de abril de 2026. El Observatorio Económico hace seguimiento diario de estas variables.</p>',
 'Banco de la República', '2026-04-10 10:00:00', 'published', NULL),

-- Social
(2, 'Boyacá reduce casos de violencia intrafamiliar en un 8%',
 'violencia-intrafamiliar-reduccion-2026',
 'Los programas de prevención muestran impacto positivo en los municipios priorizados.',
 '<p>Según los datos reportados por la Secretaría de Salud departamental a través del SIVIGILA, los casos de violencia intrafamiliar presentaron una reducción del 8% comparado con el mismo período del año anterior.</p>',
 'SIVIGILA – Secretaría de Salud', '2026-04-02 08:30:00', 'published', NULL),

(2, 'Indicadores de salud pública actualizados para primer trimestre',
 'salud-publica-q1-2026',
 'Nuevos datos de mortalidad, morbilidad y cobertura de vacunación disponibles.',
 '<p>El Observatorio Social ha actualizado los indicadores de salud pública con datos del primer trimestre de 2026, incluyendo tasas de mortalidad infantil, cobertura de vacunación y principales causas de consulta médica.</p>',
 'INS – Secretaría de Salud Departamental', '2026-04-06 09:30:00', 'published', NULL),

(2, 'Índice de pobreza multidimensional: avances y retos',
 'pobreza-multidimensional-boyaca-2026',
 'Boyacá mejora en educación y servicios pero persisten brechas rurales.',
 '<p>El análisis del IPM muestra mejoras significativas en las dimensiones de educación y acceso a servicios públicos, pero las brechas entre zonas urbanas y rurales continúan siendo un desafío.</p>',
 'DANE – Pobreza multidimensional', '2026-04-11 10:00:00', 'published', NULL),

-- Ambiente
(3, 'Calidad del aire en Sogamoso mejora por tercer mes consecutivo',
 'calidad-aire-sogamoso-mejora-2026',
 'Las mediciones de PM2.5 y PM10 muestran tendencia descendente.',
 '<p>Las estaciones de monitoreo de calidad del aire en el corredor industrial de Sogamoso reportan una mejora sostenida en los índices de material particulado.</p>',
 'CORPOBOYACÁ – IDEAM', '2026-04-03 08:00:00', 'published', NULL),

(3, 'Reporte de biodiversidad en el corredor Iguaque-Guantiva',
 'biodiversidad-iguaque-guantiva-2026',
 'Inventario actualizado de flora y fauna en ecosistemas estratégicos.',
 '<p>El Observatorio Ambiental presenta el reporte actualizado de biodiversidad del corredor biológico Iguaque-La Rusia-Guantiva, con registro de nuevas especies y estado de conservación.</p>',
 'CORPOBOYACÁ', '2026-04-07 09:00:00', 'published', NULL),

(3, 'Estado de los cuerpos de agua en el altiplano boyacense',
 'cuerpos-agua-altiplano-2026',
 'Monitoreo de la Laguna de Tota y principales fuentes hídricas.',
 '<p>Los indicadores de calidad hídrica del altiplano muestran la necesidad de continuar con las acciones de protección, especialmente en la Laguna de Tota y los ríos Chicamocha y Suárez.</p>',
 'CORPOBOYACÁ – Autoridades ambientales', '2026-04-12 10:00:00', 'published', NULL),

-- CTI
(4, 'Boyacá cuenta con 12 grupos de investigación categoría A',
 'grupos-investigacion-a-boyaca-2026',
 'Las universidades del departamento fortalecen sus capacidades de I+D.',
 '<p>Según la última convocatoria de Minciencias, 12 grupos de investigación de universidades boyacenses alcanzaron la categoría A, lo que representa un avance en las capacidades de I+D departamentales.</p>',
 'Minciencias', '2026-04-04 08:00:00', 'published', NULL),

(4, 'Programa Ondas atendió más de 5.000 niños en Boyacá',
 'programa-ondas-boyaca-2026',
 'La apropiación social de la ciencia llega a más municipios del departamento.',
 '<p>El programa Ondas de Minciencias amplió su cobertura en Boyacá, alcanzando a más de 5.000 niños y jóvenes en actividades de investigación y apropiación social del conocimiento.</p>',
 'Minciencias – Secretaría TIC', '2026-04-08 09:00:00', 'published', NULL),

(4, 'Conectividad digital: 78% de cabeceras municipales con fibra óptica',
 'conectividad-fibra-optica-boyaca-2026',
 'La infraestructura digital del departamento avanza hacia la meta del 90%.',
 '<p>El Observatorio de CTI reporta que 78% de las cabeceras municipales del departamento cuentan con conexión de fibra óptica, acercándose a la meta del Plan de Desarrollo.</p>',
 'Secretaría TIC – MinTIC', '2026-04-12 10:00:00', 'published', NULL),

-- Género
(5, 'Línea 155 atendió 2.300 llamadas de mujeres boyacenses en Q1 2026',
 'linea-155-atencion-genero-q1-2026',
 'El servicio de orientación a mujeres víctimas de violencia reporta aumento de consultas.',
 '<p>Durante el primer trimestre de 2026, la Línea 155 atendió más de 2.300 llamadas provenientes de Boyacá. El Observatorio de Género da seguimiento a estos datos para orientar política pública.</p>',
 'Secretaría de la Mujer – Consejería Presidencial', '2026-04-03 08:30:00', 'published', NULL),

(5, 'Brecha salarial de género en Boyacá: datos actualizados',
 'brecha-salarial-genero-boyaca-2026',
 'Las mujeres ganan en promedio 18% menos que los hombres en el departamento.',
 '<p>El análisis de la Gran Encuesta Integrada de Hogares muestra que la brecha salarial de género en Boyacá se mantiene en un 18%, con variaciones significativas por sector económico y nivel educativo.</p>',
 'DANE – GEIH', '2026-04-09 09:00:00', 'published', NULL),

(5, 'Rutas de atención para mujeres víctimas: guía actualizada',
 'rutas-atencion-mujeres-victimas-2026',
 'El observatorio publica la guía actualizada con los mecanismos de protección vigentes.',
 '<p>El Observatorio de Asuntos de Género publica la guía actualizada de rutas de atención para mujeres víctimas de violencia, incluyendo nuevos mecanismos de protección y contactos institucionales.</p>',
 'Gobernación de Boyacá – Secretaría de la Mujer', '2026-04-13 10:00:00', 'published', NULL);

-- ══════════════════════════════════════════════════════════════════════════════
-- 6. ETIQUETAS POR OBSERVATORIO (para clasificar noticias)
-- ══════════════════════════════════════════════════════════════════════════════
INSERT IGNORE INTO tags (observatory_id, code, label, placement_hint) VALUES
-- Económico
(1, 'noticias_portada', 'Noticias · portada general', 'noticias_portada'),
(1, 'pub_universidades', 'Publicaciones universidades', 'pub_universidades'),
(1, 'infografias', 'Infografías', 'infografias'),
(1, 'boletin', 'Boletín económico', 'boletin'),
-- Social
(2, 'noticias_portada', 'Noticias · portada general', 'noticias_portada'),
(2, 'pub_universidades', 'Publicaciones universidades', 'pub_universidades'),
(2, 'infografias', 'Infografías', 'infografias'),
(2, 'alertas', 'Alertas sociales', 'alertas'),
-- Ambiente
(3, 'noticias_portada', 'Noticias · portada general', 'noticias_portada'),
(3, 'pub_universidades', 'Publicaciones universidades', 'pub_universidades'),
(3, 'infografias', 'Infografías', 'infografias'),
-- CTI
(4, 'noticias_portada', 'Noticias · portada general', 'noticias_portada'),
(4, 'pub_universidades', 'Publicaciones universidades', 'pub_universidades'),
(4, 'infografias', 'Infografías', 'infografias'),
-- Género
(5, 'noticias_portada', 'Noticias · portada general', 'noticias_portada'),
(5, 'pub_universidades', 'Publicaciones universidades', 'pub_universidades'),
(5, 'infografias', 'Infografías', 'infografias'),
(5, 'rutas_atencion', 'Rutas de atención', 'rutas');

-- ══════════════════════════════════════════════════════════════════════════════
-- 7. TABLEROS PRINCIPALES POR OBSERVATORIO (si no existen)
-- ══════════════════════════════════════════════════════════════════════════════
INSERT IGNORE INTO cms_dashboards (observatory_id, title, slug, description, sort_order, is_active)
SELECT o.id, 'Tablero principal', 'principal',
       'Indicadores principales del observatorio. Datos cargados desde CSV.',
       0, 1
FROM observatories o
WHERE o.slug IN ('economico', 'social', 'ambiente', 'cti', 'genero');

-- Verificación
SELECT '=== RESUMEN DE CARGA ===' AS info;
SELECT 'Contacto' AS modulo, COUNT(*) AS registros FROM cms_contact
UNION ALL SELECT 'Banners', COUNT(*) FROM cms_home_banners WHERE is_active = 1
UNION ALL SELECT 'Redes sociales', COUNT(*) FROM cms_social_posts WHERE is_active = 1
UNION ALL SELECT 'Hero slides', COUNT(*) FROM cms_microsite_hero_slides WHERE is_active = 1
UNION ALL SELECT 'Noticias publicadas', COUNT(*) FROM news WHERE content_status = 'published'
UNION ALL SELECT 'Etiquetas', COUNT(*) FROM tags
UNION ALL SELECT 'Tableros', COUNT(*) FROM cms_dashboards WHERE is_active = 1;
