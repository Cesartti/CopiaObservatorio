-- 020_boletin_salud.sql
-- Agrega el Boletín de Salud (Dimensión Social). Idempotente.
-- Nota: el PDF pesa 175 MB; conviene reemplazarlo por una versión comprimida desde el CMS.
SET NAMES utf8mb4;

INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT 2, 'Salud', 'Boletín de Salud — Dimensión Social', 'Análisis de indicadores de salud del departamento de Boyacá.', 'assets/pdf/BOLETIN_SALUD_NOVIEMBRE_2025.pdf', NULL, '2025-11-01', 3
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín de Salud — Dimensión Social');
