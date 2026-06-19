-- Demostración: observatorios 3-5, banners, tableros, gráficos, noticias, indicadores.
-- Importar en phpMyAdmin sobre la base observatorio_boyaca (después de schema + migraciones CMS).
-- Si algo ya existe, muchas sentencias no duplican gracias a NOT EXISTS / INSERT IGNORE.

SET NAMES utf8mb4;

INSERT IGNORE INTO observatories (id, slug, name, theme_color, accent_color, status) VALUES
(3, 'ambiente', 'Observatorio de Medio Ambiente', '#1f6b45', '#23a9b8', 'active'),
(4, 'cti', 'Observatorio CTI', '#1847b7', '#6d4aff', 'active'),
(5, 'genero', 'Observatorio de Asuntos de Género', '#7d2d91', '#ef6f8f', 'active');

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

INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, image_url, is_active)
SELECT 0, 'Observatorio Económico', 'Indicadores de coyuntura y competitividad territorial', 'Economía', 'assets/svg/icono-economico.svg', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM cms_home_banners b WHERE b.title = 'Observatorio Económico' LIMIT 1);

INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, image_url, is_active)
SELECT 1, 'Observatorio Social', 'Bienestar, educación y cohesión', 'Social', 'assets/svg/icono-social.svg', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM cms_home_banners b WHERE b.title = 'Observatorio Social' LIMIT 1);

INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, image_url, is_active)
SELECT 2, 'Medio ambiente', 'Aire, agua y territorio', 'Ambiente', 'assets/svg/bg-green.svg', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM cms_home_banners b WHERE b.title = 'Medio ambiente' LIMIT 1);

INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, image_url, is_active)
SELECT 3, 'CTI Boyacá', 'Ciencia, tecnología e innovación', 'CTI', 'assets/svg/icono-tecnologico.svg', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM cms_home_banners b WHERE b.title = 'CTI Boyacá' LIMIT 1);

INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, image_url, is_active)
SELECT 4, 'Asuntos de género', 'Políticas públicas y derechos', 'Género', 'assets/svg/img-genero/PROPOSITO.svg', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM cms_home_banners b WHERE b.title = 'Asuntos de género' LIMIT 1);

INSERT IGNORE INTO cms_dashboards (observatory_id, title, slug, description, sort_order, is_active)
SELECT o.id, 'Tablero principal', 'principal', 'Indicadores publicados desde el CMS. CSV UTF-8 desde Excel.', 0, 1
FROM observatories o
WHERE o.slug IN ('economico', 'social', 'ambiente', 'cti', 'genero');

INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json, sort_order, layout_span, tile_height_px, is_active, created_by)
SELECT d.id, t.title, t.chart_type, t.options_json, t.data_json, t.sort_order, t.layout_span, t.tile_height_px, 1, NULL
FROM cms_dashboards d
INNER JOIN observatories o ON o.id = d.observatory_id
CROSS JOIN (
  SELECT 'Demostracion: categorias' AS title, 'ColumnChart' AS chart_type,
    '{"colors":["#5f2a8a"],"legend":{"position":"none"}}' AS options_json,
    '[["Categoria","Casos"],["A",48],["B",72],["C",35],["D",61]]' AS data_json,
    1 AS sort_order, 6 AS layout_span, 300 AS tile_height_px
  UNION ALL
  SELECT 'Demostracion: barras horizontales', 'BarChart',
    '{"colors":["#5f2a8a"],"legend":{"position":"none"}}',
    '[["Grupo","Valor"],["Etapa 1",210],["Etapa 2",450],["Etapa 3",890],["Etapa 4",320]]',
    2, 6, 300
  UNION ALL
  SELECT 'Demostracion: serie temporal', 'LineChart',
    '{"colors":["#5f2a8a"],"pointSize":5,"legend":{"position":"none"}}',
    '[["Periodo","Total"],["2020-01",42],["2020-06",55],["2021-01",61],["2021-06",58],["2022-01",70],["2022-06",66],["2023-01",63]]',
    3, 12, 340
) AS t
WHERE o.slug = 'social' AND d.slug = 'principal'
  AND NOT EXISTS (SELECT 1 FROM cms_charts c WHERE c.dashboard_id = d.id);

INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json, sort_order, layout_span, tile_height_px, is_active, created_by)
SELECT d.id, t.title, t.chart_type, t.options_json, t.data_json, t.sort_order, t.layout_span, t.tile_height_px, 1, NULL
FROM cms_dashboards d
INNER JOIN observatories o ON o.id = d.observatory_id
CROSS JOIN (
  SELECT 'Demostracion: indicadores anuales' AS title, 'ColumnChart' AS chart_type,
    '{"colors":["#0f3557"],"legend":{"position":"none"}}' AS options_json,
    '[["Anio","Indice"],["2020",100],["2021",103],["2022",107],["2023",105],["2024",109]]' AS data_json,
    1 AS sort_order, 6 AS layout_span, 300 AS tile_height_px
  UNION ALL
  SELECT 'Demostracion: tendencia', 'AreaChart',
    '{"colors":["#d8a21d"],"legend":{"position":"none"}}',
    '[["Trimestre","Variacion"],["T1",0.8],["T2",1.1],["T3",0.9],["T4",1.2]]',
    2, 6, 300
  UNION ALL
  SELECT 'Demostracion: composicion', 'PieChart',
    '{"colors":["#0f3557","#1a4a73","#d8a21d","#2a5f8f","#e8b84a"]}',
    '[["Rubro","Participacion"],["Rubro A",35],["Rubro B",28],["Rubro C",22],["Rubro D",15]]',
    3, 12, 320
) AS t
WHERE o.slug = 'economico' AND d.slug = 'principal'
  AND NOT EXISTS (SELECT 1 FROM cms_charts c WHERE c.dashboard_id = d.id);

INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json, sort_order, layout_span, tile_height_px, is_active, created_by)
SELECT d.id, t.title, t.chart_type, t.options_json, t.data_json, t.sort_order, t.layout_span, t.tile_height_px, 1, NULL
FROM cms_dashboards d
INNER JOIN observatories o ON o.id = d.observatory_id
CROSS JOIN (
  SELECT 'Indicadores ambientales (demo)' AS title, 'ColumnChart' AS chart_type,
    '{"colors":["#1f6b45"],"legend":{"position":"none"}}' AS options_json,
    '[["Variable","Valor"],["Calidad aire",72],["Agua",65],["Suelo",58],["Residuos",81]]' AS data_json,
    1 AS sort_order, 6 AS layout_span, 300 AS tile_height_px
  UNION ALL
  SELECT 'Avance por eje', 'BarChart',
    '{"colors":["#23a9b8"],"legend":{"position":"none"}}',
    '[["Eje","Puntos"],["Biodiversidad",42],["Cambio climatico",55],["Gestion",48]]',
    2, 6, 300
  UNION ALL
  SELECT 'Serie anual', 'LineChart',
    '{"colors":["#1f6b45"],"pointSize":5,"legend":{"position":"none"}}',
    '[["Anio","Indice"],["2021",62],["2022",64],["2023",67],["2024",70],["2025",72]]',
    3, 12, 340
) AS t
WHERE o.slug = 'ambiente' AND d.slug = 'principal'
  AND NOT EXISTS (SELECT 1 FROM cms_charts c WHERE c.dashboard_id = d.id);

INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json, sort_order, layout_span, tile_height_px, is_active, created_by)
SELECT d.id, t.title, t.chart_type, t.options_json, t.data_json, t.sort_order, t.layout_span, t.tile_height_px, 1, NULL
FROM cms_dashboards d
INNER JOIN observatories o ON o.id = d.observatory_id
CROSS JOIN (
  SELECT 'I+D e innovación (demo)' AS title, 'ColumnChart' AS chart_type,
    '{"colors":["#1847b7"],"legend":{"position":"none"}}' AS options_json,
    '[["Area","Millones"],["Investigacion",120],["Extension",85],["Proyectos",210]]' AS data_json,
    1 AS sort_order, 6 AS layout_span, 300 AS tile_height_px
  UNION ALL
  SELECT 'Talento CTI', 'PieChart',
    '{"colors":["#1847b7","#6d4aff","#8fa8ff","#b8c5ff"]}',
    '[["Nivel","Personas"],["Posgrado",420],["Especializacion",310],["Tecnico",890],["Otro",150]]',
    2, 6, 300
  UNION ALL
  SELECT 'Evolución trimestral', 'AreaChart',
    '{"colors":["#6d4aff"],"legend":{"position":"none"}}',
    '[["T","Indicador"],["T1",2.1],["T2",2.4],["T3",2.6],["T4",2.8]]',
    3, 12, 320
) AS t
WHERE o.slug = 'cti' AND d.slug = 'principal'
  AND NOT EXISTS (SELECT 1 FROM cms_charts c WHERE c.dashboard_id = d.id);

INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json, sort_order, layout_span, tile_height_px, is_active, created_by)
SELECT d.id, t.title, t.chart_type, t.options_json, t.data_json, t.sort_order, t.layout_span, t.tile_height_px, 1, NULL
FROM cms_dashboards d
INNER JOIN observatories o ON o.id = d.observatory_id
CROSS JOIN (
  SELECT 'Participación (demo)' AS title, 'ColumnChart' AS chart_type,
    '{"colors":["#7d2d91"],"legend":{"position":"none"}}' AS options_json,
    '[["Ambito","Porcentaje"],["Politica",28],["Economica",35],["Social",41]]' AS data_json,
    1 AS sort_order, 6 AS layout_span, 300 AS tile_height_px
  UNION ALL
  SELECT 'Atención integral', 'BarChart',
    '{"colors":["#ef6f8f"],"legend":{"position":"none"}}',
    '[["Servicio","Casos"],["Orientacion",120],["Ruta",95],["Seguimiento",210]]',
    2, 6, 300
  UNION ALL
  SELECT 'Brechas por territorio', 'LineChart',
    '{"colors":["#7d2d91"],"pointSize":4,"legend":{"position":"none"}}',
    '[["Periodo","Brecha"],["2022",0.18],["2023",0.16],["2024",0.15],["2025",0.14]]',
    3, 12, 330
) AS t
WHERE o.slug = 'genero' AND d.slug = 'principal'
  AND NOT EXISTS (SELECT 1 FROM cms_charts c WHERE c.dashboard_id = d.id);

INSERT INTO news (observatory_id, title, slug, summary, body, published_at, content_status)
SELECT 1, 'Coyuntura económica Boyacá — primer trimestre 2026', 'noticia-coyuntura-2026-t1-econ', 'Variación de indicadores macro y sectoriales.', '<p>Contenido de demostración generado para validar noticias en el micrositio.</p>', NOW(), 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM news n WHERE n.slug = 'noticia-coyuntura-2026-t1-econ');

INSERT INTO news (observatory_id, title, slug, summary, body, published_at, content_status)
SELECT 2, 'Índice de bienestar social: actualización 2026', 'noticia-bienestar-social-2026', 'Resultados por municipios y líneas temáticas.', '<p>Contenido de demostración generado para validar noticias en el micrositio.</p>', NOW(), 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM news n WHERE n.slug = 'noticia-bienestar-social-2026');

INSERT INTO news (observatory_id, title, slug, summary, body, published_at, content_status)
SELECT 3, 'Calidad del aire: seguimiento regional', 'noticia-calidad-aire-2026', 'Estaciones y tendencias en el departamento.', '<p>Contenido de demostración generado para validar noticias en el micrositio.</p>', NOW(), 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM news n WHERE n.slug = 'noticia-calidad-aire-2026');

INSERT INTO news (observatory_id, title, slug, summary, body, published_at, content_status)
SELECT 4, 'Innovación abierta y transferencia CTI', 'noticia-innovacion-cti-2026', 'Convocatorias y ecosistema de I+D+i.', '<p>Contenido de demostración generado para validar noticias en el micrositio.</p>', NOW(), 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM news n WHERE n.slug = 'noticia-innovacion-cti-2026');

INSERT INTO news (observatory_id, title, slug, summary, body, published_at, content_status)
SELECT 5, 'Políticas de género y autonomía económica', 'noticia-genero-autonomia-2026', 'Avances y retos territoriales.', '<p>Contenido de demostración generado para validar noticias en el micrositio.</p>', NOW(), 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM news n WHERE n.slug = 'noticia-genero-autonomia-2026');

INSERT INTO indicators (id, observatory_id, title, description, content_status)
SELECT 3001, 1, 'PIB per cápita departamental', 'Producto interno bruto por habitante.', 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM indicators i WHERE i.id = 3001);

INSERT INTO indicators (id, observatory_id, title, description, content_status)
SELECT 3002, 2, 'Índice de pobreza multidimensional', 'IPM según metodología DANE.', 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM indicators i WHERE i.id = 3002);

INSERT INTO indicators (id, observatory_id, title, description, content_status)
SELECT 3003, 3, 'Cobertura de áreas protegidas', 'Superficie protegida sobre el territorio.', 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM indicators i WHERE i.id = 3003);

INSERT INTO indicators (id, observatory_id, title, description, content_status)
SELECT 3004, 4, 'Gasto en ciencia y tecnología', 'Inversión en I+D como porcentaje del presupuesto regional.', 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM indicators i WHERE i.id = 3004);

INSERT INTO indicators (id, observatory_id, title, description, content_status)
SELECT 3005, 5, 'Tasa de participación laboral femenina', 'Mercado laboral con enfoque de género.', 'published'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM indicators i WHERE i.id = 3005);
