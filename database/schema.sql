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
  observatory_id SMALLINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  summary TEXT,
  body LONGTEXT,
  source VARCHAR(255),
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
