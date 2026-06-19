-- Contadores de visitas (portal y micrositios) y respuestas de encuesta opcional

CREATE TABLE IF NOT EXISTS cms_page_visits (
  page_key VARCHAR(64) NOT NULL PRIMARY KEY,
  hit_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cms_visitor_surveys (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  page_context VARCHAR(64) NOT NULL DEFAULT 'portal',
  age_range VARCHAR(32) NOT NULL,
  sector VARCHAR(64) NOT NULL,
  visit_frequency VARCHAR(40) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_survey_created (created_at)
);
