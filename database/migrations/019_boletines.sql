-- 019_boletines.sql
-- Módulo de Boletines (general + por observatorio). Idempotente.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS cms_bulletins (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NULL,            -- NULL = general
  category VARCHAR(120) NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  pdf_url VARCHAR(255) NOT NULL,
  cover_url VARCHAR(255) NULL,
  published_at DATE NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bul_obs (observatory_id),
  INDEX idx_bul_active (is_active)
);

-- Migrar los boletines que ya existían (idempotente por título)
INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT NULL, 'General', 'Boletín General 1', 'Análisis de métricas en Boyacá: económico, social y ambiental.', 'assets/pdf/Boletin 1-04 julio-2023.pdf', 'assets/svg/boletin-01.png', '2023-07-04', 1
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín General 1');

INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT NULL, 'General', 'Boletín General 2', 'Análisis de los determinantes de pobreza.', 'assets/pdf/Boletin 2-04 julio 2023.pdf', 'assets/svg/boletin-02.png', '2023-07-04', 2
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín General 2');

INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT NULL, 'General', 'Boletín General 3', 'Análisis de los Objetivos de Desarrollo Sostenible.', 'assets/pdf/Boletin 3-17 de julio 2023.pdf', 'assets/svg/boletin-03.png', '2023-07-17', 3
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín General 3');

INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT NULL, 'General', 'Boletín General 4', 'Caracterización de la producción agrícola del departamento de Boyacá.', 'assets/pdf/Boletin 4-06 julio 2023.pdf', 'assets/svg/boletin-04.png', '2023-07-06', 4
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín General 4');

INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT NULL, 'General', 'Boletín General 5', 'Análisis de la calidad del aire.', 'assets/pdf/Boletin 5 - 11 julio 2023.pdf', 'assets/svg/boletin-05.png', '2023-07-11', 5
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín General 5');

INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT NULL, 'General', 'Boletín General 6', 'Análisis de la CTeI en el departamento.', 'assets/pdf/Boletin 6-13 julio 2023.pdf', 'assets/svg/boletin-06.png', '2023-07-13', 6
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín General 6');

INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order)
SELECT 2, 'Violencia', 'Boletín 1 de Violencia — Dimensión Social', 'Análisis detallado de los indicadores de violencia de enero de 2020 a junio de 2024.', 'assets/pdf/BOLETIN 1 de Violencia 10 dic 2024.pdf', 'assets/svg/portadaB_violencia01.png', '2024-12-10', 1
WHERE NOT EXISTS (SELECT 1 FROM cms_bulletins WHERE title='Boletín 1 de Violencia — Dimensión Social');

-- Nota: el "Boletín 1 de Salud" no se incluye porque su PDF pesa 175 MB (no apto
-- para web/repo). Súbelo comprimido desde el CMS → Boletines (observatorio Social).
