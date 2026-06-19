-- Extensión: etiquetas por observatorio y vínculo con noticias (ubicación editorial).
-- Ejecutar después de database/schema.sql en el mismo esquema.
-- Los usuarios y roles ya están definidos en schema.sql; aquí se documentan etiquetas.

CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  code VARCHAR(80) NOT NULL,
  label VARCHAR(160) NOT NULL,
  placement_hint VARCHAR(80) NULL COMMENT 'Ej: noticias_portada, micrositio, infografias, pub_universidades',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_obs_tag_code (observatory_id, code),
  FOREIGN KEY (observatory_id) REFERENCES observatories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS news_tags (
  news_id BIGINT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (news_id, tag_id),
  FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
