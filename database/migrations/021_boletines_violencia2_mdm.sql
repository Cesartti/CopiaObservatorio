-- 021_boletines_violencia2_mdm.sql
-- Boletines que faltaban (estaban subidos por FileZilla en producción): idempotente.
SET NAMES utf8mb4;

-- Boletín 2 de Violencia (Social)
INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT 2, 'Violencia', 'Boletín 2 de Violencia — Dimensión Social', 'Análisis detallado de los indicadores de violencia de enero de 2024 a junio de 2025.', 'assets/pdf/BOLETIN_VIOLENCIAS_NOVIEMBRE_2025.pdf', 'assets/svg/portadaB_violencia01.png', '2025-11-01', 2
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín 2 de Violencia — Dimensión Social');

-- Boletín de Medición de Desempeño Municipal (Económico)
INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT 1, 'Medición de Desempeño Municipal', 'Boletín 1 — Medición de Desempeño Municipal', 'Análisis de indicadores clave de desempeño municipal, gestión y resultados.', 'assets/pdf/Boletin01_MDM.pdf', NULL, '2024-01-01', 1
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín 1 — Medición de Desempeño Municipal');
