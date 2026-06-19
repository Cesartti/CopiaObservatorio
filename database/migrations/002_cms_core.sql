-- CMS: tableros, gráficos Google Charts, banners, redes, contacto, pestañas, permisos por rol.
-- Ejecutar después de schema.sql y 001 si aplica.

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
  chart_type VARCHAR(60) NOT NULL COMMENT 'Google Visualization: ColumnChart, GeoChart, etc.',
  options_json JSON NULL,
  data_json LONGTEXT NOT NULL COMMENT 'Array JSON: filas para google.visualization.arrayToDataTable',
  source_filename VARCHAR(255) NULL,
  sort_order SMALLINT DEFAULT 0,
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

-- Permisos por defecto: 1=admin, 2=editor (ajuste ids si sus roles difieren)
INSERT IGNORE INTO role_permissions (role_id, module, can_read, can_write) VALUES
(1, 'banners', 1, 1),
(1, 'social', 1, 1),
(1, 'contact', 1, 1),
(1, 'news', 1, 1),
(1, 'charts', 1, 1),
(1, 'tabs', 1, 1),
(1, 'rag', 1, 1),
(1, 'users', 1, 1),
(2, 'banners', 1, 1),
(2, 'social', 1, 1),
(2, 'contact', 1, 1),
(2, 'news', 1, 1),
(2, 'charts', 1, 1),
(2, 'tabs', 1, 1),
(2, 'rag', 1, 1),
(2, 'users', 0, 0);

INSERT IGNORE INTO cms_contact (id) VALUES (1);
