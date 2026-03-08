-- Esquema base recomendado para automatizar el Observatorio de Boyacá
CREATE TABLE IF NOT EXISTS dimensions (
  id TINYINT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS indicators (
  id INT PRIMARY KEY,
  dimension_id TINYINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  category VARCHAR(180),
  subcategory VARCHAR(180),
  tags TEXT,
  source TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (dimension_id) REFERENCES dimensions(id)
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
