-- 037_boletin_violencia_2026.sql
-- Boletín de Violencias Boyacá 2026 (Observatorio Social): periodo enero 2025 a junio 2026.
-- Va primero en la lista del observatorio (sort_order 0). Idempotente.
SET NAMES utf8mb4;

INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT 2, 'Violencia', 'Boletín 3 de Violencia — Boyacá 2026', 'Violencia letal, violencia no letal y conflicto armado en Boyacá, de enero de 2025 a junio de 2026, por ciclo de vida, víctima y victimario.', 'assets/pdf/BOLETIN_VIOLENCIAS_2026.pdf', 'assets/svg/portadaB_violencia2026.jpg', '2026-11-24', 0
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE pdf_url='assets/pdf/BOLETIN_VIOLENCIAS_2026.pdf');
