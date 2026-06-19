-- Banners del portal con imagen; carrusel hero de micrositios por observatorio.
-- Ejecutar después de 002_cms_core.sql.

ALTER TABLE cms_home_banners
  ADD COLUMN image_url VARCHAR(512) NULL COMMENT 'Ruta relativa al sitio, ej: assets/svg/icono-economico.svg' AFTER tag;

CREATE TABLE IF NOT EXISTS cms_microsite_hero_slides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  sort_order SMALLINT DEFAULT 0,
  title VARCHAR(255) NOT NULL,
  slide_text TEXT,
  image_url VARCHAR(512) NULL COMMENT 'SVG/PNG bajo website/assets/...',
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_obs_sort (observatory_id, sort_order),
  FOREIGN KEY (observatory_id) REFERENCES observatories(id) ON DELETE CASCADE
);

-- Semilla por observatorio (solo si aún no hay filas para ese observatorio)
INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 1 AS observatory_id, 0 AS sort_order, 'Coyuntura económica territorial' AS title, 'Monitoree TRM, inflación, empleo y variables macro en una sola vista.' AS slide_text, 'assets/svg/icono-economico.svg' AS image_url
  UNION ALL SELECT 1, 1, 'Indicadores de mercado y finanzas', 'Panel ejecutivo para análisis rápido de tendencias y variaciones.', 'assets/svg/bg-yellow.svg'
  UNION ALL SELECT 1, 2, 'Noticias y alertas económicas', 'Actualidad, boletines y eventos para toma de decisiones.', 'assets/svg/bg-blue.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides s WHERE s.observatory_id = 1) = 0;

INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 2 AS observatory_id, 0 AS sort_order, 'Bienestar y desarrollo social' AS title, 'Siga indicadores de salud, educación, empleo y calidad de vida.' AS slide_text, 'assets/svg/icono-social.svg' AS image_url
  UNION ALL SELECT 2, 1, 'Enfoque territorial y poblacional', 'Analice brechas por municipio, grupos etarios y poblaciones priorizadas.', 'assets/svg/bg-purple.svg'
  UNION ALL SELECT 2, 2, 'Información útil para ciudadanía', 'Exploración simple con contexto para comprender cada indicador.', 'assets/svg/bg-menu-t.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides s WHERE s.observatory_id = 2) = 0;

INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 3 AS observatory_id, 0 AS sort_order, 'Estado ambiental del territorio' AS title, 'Calidad del aire, agua, residuos y biodiversidad en seguimiento permanente.' AS slide_text, 'assets/svg/bg-green.svg' AS image_url
  UNION ALL SELECT 3, 1, 'Datos para acción climática', 'Indicadores temáticos y trazabilidad para apoyar gestión ambiental.', 'assets/svg/bg-menu-b.svg'
  UNION ALL SELECT 3, 2, 'Visualización pública y transparente', 'Tarjetas, categorías y descargas para uso ciudadano e institucional.', 'assets/svg/bg-blue.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides s WHERE s.observatory_id = 3) = 0
  AND EXISTS (SELECT 1 FROM observatories o WHERE o.id = 3);

INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 4 AS observatory_id, 0 AS sort_order, 'Ciencia, tecnología e innovación' AS title, 'Mida capacidades, proyectos, inversión y resultados del ecosistema CTI.' AS slide_text, 'assets/svg/icono-tecnologico.svg' AS image_url
  UNION ALL SELECT 4, 1, 'Monitoreo estratégico de capacidades', 'Panel con métricas de investigación, talento y transferencia.', 'assets/svg/bg-blue.svg'
  UNION ALL SELECT 4, 2, 'Conexión entre academia y territorio', 'Información para orientar decisiones de política pública e innovación.', 'assets/svg/bg-yellow.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides s WHERE s.observatory_id = 4) = 0
  AND EXISTS (SELECT 1 FROM observatories o WHERE o.id = 4);

INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, is_active)
SELECT t.observatory_id, t.sort_order, t.title, t.slide_text, t.image_url, 1
FROM (
  SELECT 5 AS observatory_id, 0 AS sort_order, 'Asuntos de género con enfoque integral' AS title, 'Brechas, violencias, participación y autonomía con lectura comprensible.' AS slide_text, 'assets/svg/img-genero/MARCO-INSTITUCIONAL.svg' AS image_url
  UNION ALL SELECT 5, 1, 'Rutas, servicios y seguimiento', 'Contenidos de interés ciudadano con enfoque diferencial y territorial.', 'assets/svg/img-genero/POLITICAS-PUBLICAS.svg'
  UNION ALL SELECT 5, 2, 'Información para prevención y decisión', 'Datos y recursos para instituciones, organizaciones y comunidad.', 'assets/svg/img-genero/OBJETIVO.svg'
) AS t
WHERE (SELECT COUNT(*) FROM cms_microsite_hero_slides s WHERE s.observatory_id = 5) = 0
  AND EXISTS (SELECT 1 FROM observatories o WHERE o.id = 5);
