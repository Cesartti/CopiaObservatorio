-- 018_eventos_navegacion.sql
-- Analítica de comportamiento: un evento por interacción, asociado al visitante anónimo.
-- Idempotente.

CREATE TABLE IF NOT EXISTS cms_events (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  visitor_id CHAR(32) NULL,
  event_type VARCHAR(40) NOT NULL,
  observatory VARCHAR(40) NULL,
  object_type VARCHAR(40) NULL,
  object_id VARCHAR(120) NULL,
  label VARCHAR(200) NULL,
  path VARCHAR(255) NULL,
  country VARCHAR(80) NULL,
  region VARCHAR(120) NULL,
  city VARCHAR(120) NULL,
  lat DECIMAL(9,6) NULL,
  lng DECIMAL(9,6) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ev_type (event_type),
  INDEX idx_ev_obs (observatory),
  INDEX idx_ev_created (created_at),
  INDEX idx_ev_visitor (visitor_id)
);

-- Asociar la encuesta al visitante (para cruzar encuesta <-> navegación <-> geo)
ALTER TABLE cms_visitor_surveys
  ADD COLUMN IF NOT EXISTS visitor_id CHAR(32) NULL AFTER page_context;
