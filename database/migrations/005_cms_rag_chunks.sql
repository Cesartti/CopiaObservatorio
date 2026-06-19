-- Fichas de conocimiento para el asistente RAG (MySQL = borrador; sincronizar a PostgreSQL con sync_cms_chunks_to_pg.py)

CREATE TABLE IF NOT EXISTS cms_rag_chunks (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  dimension VARCHAR(80) NOT NULL COMMENT 'Debe coincidir con dimension en PG (indicadores), p. ej. Económica, Social',
  title VARCHAR(255) NULL COMMENT 'Título o actividad corta',
  sector VARCHAR(160) NULL,
  anio SMALLINT NULL,
  tipo_precio VARCHAR(120) NULL,
  valor DECIMAL(18,4) NULL,
  fuente VARCHAR(255) NULL,
  departamento VARCHAR(120) NOT NULL DEFAULT 'Boyacá',
  body_text LONGTEXT NOT NULL COMMENT 'Texto que se vectoriza y consulta el chat',
  status VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending|synced|error',
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

INSERT IGNORE INTO role_permissions (role_id, module, can_read, can_write) VALUES
(1, 'rag', 1, 1),
(2, 'rag', 1, 1);
