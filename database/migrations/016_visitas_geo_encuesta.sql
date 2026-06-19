-- 016_visitas_geo_encuesta.sql
-- Analítica de visitas y encuesta con geolocalización aproximada por IP.
-- Idempotente: crea las tablas si faltan y agrega columnas geo si no existen.
-- Compatible con MariaDB 10.5+ (ADD COLUMN IF NOT EXISTS).

-- 1) Visitantes únicos (una fila por par página + navegador)
CREATE TABLE IF NOT EXISTS cms_unique_visitors (
  page_key VARCHAR(64) NOT NULL,
  visitor_id CHAR(32) NOT NULL,
  first_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (page_key, visitor_id),
  INDEX idx_page_seen (page_key, first_seen_at)
);

ALTER TABLE cms_unique_visitors
  ADD COLUMN IF NOT EXISTS country VARCHAR(80) NULL,
  ADD COLUMN IF NOT EXISTS region  VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS city    VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS lat     DECIMAL(9,6) NULL,
  ADD COLUMN IF NOT EXISTS lng     DECIMAL(9,6) NULL;

-- 2) Encuesta opcional (con observatorio de origen y geolocalización)
CREATE TABLE IF NOT EXISTS cms_visitor_surveys (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  page_context VARCHAR(64) NOT NULL DEFAULT 'portal',
  age_range VARCHAR(32) NOT NULL,
  sector VARCHAR(64) NOT NULL,
  visit_frequency VARCHAR(40) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_survey_created (created_at)
);

ALTER TABLE cms_visitor_surveys
  ADD COLUMN IF NOT EXISTS country VARCHAR(80) NULL,
  ADD COLUMN IF NOT EXISTS region  VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS city    VARCHAR(120) NULL,
  ADD COLUMN IF NOT EXISTS lat     DECIMAL(9,6) NULL,
  ADD COLUMN IF NOT EXISTS lng     DECIMAL(9,6) NULL;

-- 3) Caché de geolocalización por IP (evita repetir llamadas externas).
--    Se guarda el hash de la IP (privacidad), no la IP en claro.
CREATE TABLE IF NOT EXISTS cms_geo_cache (
  ip_hash CHAR(64) NOT NULL PRIMARY KEY,
  status VARCHAR(20) NULL,
  country VARCHAR(80) NULL,
  region  VARCHAR(120) NULL,
  city    VARCHAR(120) NULL,
  lat     DECIMAL(9,6) NULL,
  lng     DECIMAL(9,6) NULL,
  looked_up_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
