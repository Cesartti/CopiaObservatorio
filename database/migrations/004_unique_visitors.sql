-- Visitas únicas por página (un dispositivo = un ID en cookie, sin repetir conteo por recargas)

CREATE TABLE IF NOT EXISTS cms_unique_visitors (
  page_key VARCHAR(64) NOT NULL,
  visitor_id CHAR(32) NOT NULL,
  first_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (page_key, visitor_id),
  INDEX idx_page_seen (page_key, first_seen_at)
);
