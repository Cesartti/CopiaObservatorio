-- Esquema base recomendado para automatizar y administrar la Red de Observatorios

CREATE TABLE IF NOT EXISTS observatories (
  id SMALLINT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  theme_color VARCHAR(20),
  accent_color VARCHAR(20),
  status VARCHAR(30) DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS roles (
  id SMALLINT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(160) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role_id SMALLINT NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS user_observatories (
  user_id BIGINT NOT NULL,
  observatory_id SMALLINT NOT NULL,
  PRIMARY KEY (user_id, observatory_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (observatory_id) REFERENCES observatories(id)
);

CREATE TABLE IF NOT EXISTS indicators (
  id INT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  objective TEXT,
  formula_text TEXT,
  unit VARCHAR(80),
  source TEXT,
  periodicity VARCHAR(80),
  geographic_coverage VARCHAR(120),
  responsible_user_id BIGINT NULL,
  content_status VARCHAR(30) DEFAULT 'draft',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (observatory_id) REFERENCES observatories(id),
  FOREIGN KEY (responsible_user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS indicator_charts (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  indicator_id INT NOT NULL,
  chart_order SMALLINT NOT NULL,
  title VARCHAR(255),
  description TEXT,
  chart_type VARCHAR(50),
  source TEXT,
  UNIQUE KEY uk_indicator_chart (indicator_id, chart_order),
  FOREIGN KEY (indicator_id) REFERENCES indicators(id)
);

CREATE TABLE IF NOT EXISTS indicator_observations (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  indicator_id INT NOT NULL,
  chart_order SMALLINT NOT NULL,
  period_label VARCHAR(50),
  geography_code VARCHAR(30),
  geography_name VARCHAR(120),
  category VARCHAR(120),
  value_decimal DECIMAL(18,4),
  value_text VARCHAR(255),
  raw_payload JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_indicator_period (indicator_id, period_label),
  FOREIGN KEY (indicator_id) REFERENCES indicators(id)
);

CREATE TABLE IF NOT EXISTS news (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NULL, -- NULL = noticia general (toda la Red)
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  summary TEXT,
  body LONGTEXT,
  source VARCHAR(255),
  image_url VARCHAR(512) NULL,
  published_at DATETIME NULL,
  content_status VARCHAR(30) DEFAULT 'draft',
  created_by BIGINT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (observatory_id) REFERENCES observatories(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS documents (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_type VARCHAR(30),
  version_label VARCHAR(40),
  downloads_count INT DEFAULT 0,
  content_status VARCHAR(30) DEFAULT 'draft',
  created_by BIGINT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (observatory_id) REFERENCES observatories(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS datasets (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  format VARCHAR(20),
  update_frequency VARCHAR(50),
  metadata_json JSON,
  content_status VARCHAR(30) DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (observatory_id) REFERENCES observatories(id)
);

CREATE TABLE IF NOT EXISTS content_workflows (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  content_type VARCHAR(40) NOT NULL,
  content_id BIGINT NOT NULL,
  from_status VARCHAR(30),
  to_status VARCHAR(30) NOT NULL,
  comment TEXT,
  action_by BIGINT,
  action_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_workflow_content (content_type, content_id),
  FOREIGN KEY (action_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(50),
  entity_id BIGINT,
  payload JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Etiquetas por observatorio y publicación de noticias (ubicación editorial / canales)
CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  code VARCHAR(80) NOT NULL,
  label VARCHAR(160) NOT NULL,
  placement_hint VARCHAR(80) NULL,
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

-- CMS (panel web, tableros, gráficos Google Charts)
CREATE TABLE IF NOT EXISTS role_permissions (
  role_id SMALLINT NOT NULL,
  module VARCHAR(40) NOT NULL,
  can_read TINYINT(1) DEFAULT 1,
  can_write TINYINT(1) DEFAULT 0,
  PRIMARY KEY (role_id, module),
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cms_home_banners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order SMALLINT DEFAULT 0,
  title VARCHAR(255) NOT NULL,
  subtitle TEXT,
  tag VARCHAR(80) NULL,
  image_url VARCHAR(512) NULL,
  link_url VARCHAR(512) NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cms_social_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order SMALLINT DEFAULT 0,
  network VARCHAR(20) NOT NULL,
  category VARCHAR(160) NULL,
  title VARCHAR(255) NOT NULL,
  byline VARCHAR(255) NULL,
  excerpt TEXT,
  url VARCHAR(512) NOT NULL,
  post_date DATE NULL,
  image VARCHAR(512) NULL,
  media_tag VARCHAR(120) NULL,
  media_caption VARCHAR(255) NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cms_contact (
  id TINYINT PRIMARY KEY DEFAULT 1,
  institution VARCHAR(255) NULL,
  address VARCHAR(255) NULL,
  phone VARCHAR(80) NULL,
  email VARCHAR(160) NULL,
  hours VARCHAR(255) NULL,
  url_facebook VARCHAR(512) NULL,
  url_instagram VARCHAR(512) NULL,
  url_x VARCHAR(512) NULL,
  url_youtube VARCHAR(512) NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cms_dashboards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  description TEXT,
  sort_order SMALLINT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_obs_slug (observatory_id, slug),
  FOREIGN KEY (observatory_id) REFERENCES observatories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cms_charts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dashboard_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  chart_type VARCHAR(60) NOT NULL,
  options_json JSON NULL,
  data_json LONGTEXT NOT NULL,
  source_filename VARCHAR(255) NULL,
  sort_order SMALLINT DEFAULT 0,
  layout_span TINYINT UNSIGNED NOT NULL DEFAULT 12 COMMENT 'Ancho rejilla 12 cols',
  tile_height_px SMALLINT UNSIGNED NULL DEFAULT 320,
  is_active TINYINT(1) DEFAULT 1,
  created_by BIGINT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (dashboard_id) REFERENCES cms_dashboards(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS cms_tab_sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  tab_key VARCHAR(40) NOT NULL,
  title VARCHAR(255) NOT NULL,
  body_html LONGTEXT,
  sort_order SMALLINT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_obs_tab (observatory_id, tab_key),
  FOREIGN KEY (observatory_id) REFERENCES observatories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cms_microsite_hero_slides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  sort_order SMALLINT DEFAULT 0,
  title VARCHAR(255) NOT NULL DEFAULT '',
  slide_text TEXT,
  image_url VARCHAR(512) NULL,
  link_url VARCHAR(512) NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_obs_sort (observatory_id, sort_order),
  FOREIGN KEY (observatory_id) REFERENCES observatories(id) ON DELETE CASCADE
);

-- Secciones jerárquicas (pestañas + chips/acordeones) editables por WYSIWYG.
-- Mig. 013_microsite_sections.sql
CREATE TABLE IF NOT EXISTS cms_microsite_sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  parent_id INT NULL,
  section_key VARCHAR(64) NOT NULL,
  title VARCHAR(255) NOT NULL,
  subtitle VARCHAR(512) NULL,
  body_html LONGTEXT NULL,
  layout VARCHAR(32) NOT NULL DEFAULT 'standard',
  icon VARCHAR(64) NULL,
  image_url VARCHAR(512) NULL,
  cta_label VARCHAR(120) NULL,
  cta_url VARCHAR(512) NULL,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_ms_obs_parent_sort (observatory_id, parent_id, sort_order),
  KEY idx_ms_obs_key (observatory_id, section_key),
  KEY idx_ms_parent (parent_id),
  CONSTRAINT fk_ms_observatory FOREIGN KEY (observatory_id) REFERENCES observatories(id) ON DELETE CASCADE
);

-- Contadores públicos (portal y micrositios) y encuesta opcional del portal
CREATE TABLE IF NOT EXISTS cms_page_visits (
  page_key VARCHAR(64) NOT NULL PRIMARY KEY,
  hit_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cms_visitor_surveys (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  page_context VARCHAR(64) NOT NULL DEFAULT 'portal',
  age_range VARCHAR(32) NOT NULL,
  gender VARCHAR(24) NULL,
  sector VARCHAR(64) NOT NULL,
  visit_frequency VARCHAR(40) NOT NULL,
  country VARCHAR(80) NULL,
  region VARCHAR(120) NULL,
  city VARCHAR(120) NULL,
  lat DECIMAL(9,6) NULL,
  lng DECIMAL(9,6) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_survey_created (created_at)
);

-- Visitantes únicos (cookie anónima por navegador; una fila por par página + visitante)
CREATE TABLE IF NOT EXISTS cms_unique_visitors (
  page_key VARCHAR(64) NOT NULL,
  visitor_id CHAR(32) NOT NULL,
  country VARCHAR(80) NULL,
  region VARCHAR(120) NULL,
  city VARCHAR(120) NULL,
  lat DECIMAL(9,6) NULL,
  lng DECIMAL(9,6) NULL,
  first_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (page_key, visitor_id),
  INDEX idx_page_seen (page_key, first_seen_at)
);

-- Caché de geolocalización por IP (hash, no IP en claro) para la analítica
CREATE TABLE IF NOT EXISTS cms_geo_cache (
  ip_hash CHAR(64) NOT NULL PRIMARY KEY,
  status VARCHAR(20) NULL,
  country VARCHAR(80) NULL,
  region VARCHAR(120) NULL,
  city VARCHAR(120) NULL,
  lat DECIMAL(9,6) NULL,
  lng DECIMAL(9,6) NULL,
  looked_up_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Conocimiento para el asistente RAG (sincronizar a PostgreSQL con app_asistente/sync_cms_chunks_to_pg.py)
CREATE TABLE IF NOT EXISTS cms_rag_chunks (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  dimension VARCHAR(80) NOT NULL,
  title VARCHAR(255) NULL,
  sector VARCHAR(160) NULL,
  anio SMALLINT NULL,
  tipo_precio VARCHAR(120) NULL,
  valor DECIMAL(18,4) NULL,
  fuente VARCHAR(255) NULL,
  departamento VARCHAR(120) NOT NULL DEFAULT 'Boyacá',
  body_text LONGTEXT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  sync_error TEXT NULL,
  synced_at DATETIME NULL,
  created_by BIGINT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_rag_status (status),
  INDEX idx_rag_obs (observatory_id),
  FOREIGN KEY (observatory_id) REFERENCES observatories(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
