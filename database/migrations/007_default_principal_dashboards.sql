-- Tableros vacíos "principal" para micrositios Social y Económico (pestaña Tablero embebida).
-- Requiere filas en `observatories` con slug social y economico (p. ej. database/seed_example.sql).
-- Tras esto, cargue gráficos en CMS o ejecute 008_seed_demo_charts_principal.sql para datos de prueba.

INSERT IGNORE INTO cms_dashboards (observatory_id, title, slug, description, sort_order, is_active)
SELECT o.id,
       'Tablero principal',
       'principal',
       'Indicadores publicados desde el CMS. Organice sus Excel como CSV UTF-8 y súbalos por gráfico.',
       0,
       1
FROM observatories o
WHERE o.slug IN ('social', 'economico');
