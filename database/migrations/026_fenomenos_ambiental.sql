-- 026: Pestaña "Fenómenos en Boyacá" del Observatorio Ambiental.
--   env_reportes_ciudadanos : alertas que reporta la ciudadanía en el mapa.
--   env_bomberos            : directorio de cuerpos de bomberos por municipio.
-- Idempotente: CREATE TABLE IF NOT EXISTS + inserciones condicionadas.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS env_reportes_ciudadanos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    tipo            VARCHAR(40)  NOT NULL DEFAULT 'incendio',
    municipio_dane  VARCHAR(5)   NULL,
    municipio       VARCHAR(120) NULL,
    lat             DECIMAL(9,6) NOT NULL,
    lon             DECIMAL(9,6) NOT NULL,
    descripcion     TEXT         NULL,
    referencia      VARCHAR(200) NULL,
    contacto        VARCHAR(120) NULL,
    estado          ENUM('pendiente','verificado','atendido','descartado') NOT NULL DEFAULT 'pendiente',
    nota_revision   VARCHAR(400) NULL,
    ip_hash         CHAR(64)     NULL,
    creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    revisado_en     DATETIME     NULL,
    INDEX idx_estado (estado),
    INDEX idx_creado (creado_en),
    INDEX idx_dane (municipio_dane)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS env_bomberos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(180) NOT NULL,
    municipio_dane  VARCHAR(5)   NULL,
    municipio       VARCHAR(120) NULL,
    provincia       VARCHAR(80)  NULL,
    telefono        VARCHAR(120) NULL,
    celular         VARCHAR(120) NULL,
    direccion       VARCHAR(200) NULL,
    correo          VARCHAR(160) NULL,
    tipo            VARCHAR(60)  NULL DEFAULT 'Cuerpo de bomberos voluntarios',
    lat             DECIMAL(9,6) NULL,
    lon             DECIMAL(9,6) NULL,
    fuente          VARCHAR(160) NULL,
    verificado      TINYINT(1)   NOT NULL DEFAULT 0,
    activo          TINYINT(1)   NOT NULL DEFAULT 1,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_nombre_muni (nombre, municipio),
    INDEX idx_muni (municipio_dane)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas de emergencia de cobertura nacional/departamental. Son las únicas
-- que se publican verificadas; el directorio municipal lo carga la entidad
-- competente desde el CMS (Ambiental → Bomberos).
INSERT INTO env_bomberos (nombre, municipio, tipo, telefono, fuente, verificado, activo)
SELECT 'Línea única de emergencias', 'Todo el departamento', 'Línea nacional', '123',
       'Número único nacional de emergencias', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM env_bomberos WHERE nombre = 'Línea única de emergencias');

INSERT INTO env_bomberos (nombre, municipio, tipo, telefono, fuente, verificado, activo)
SELECT 'Línea nacional de bomberos', 'Todo el departamento', 'Línea nacional', '119',
       'Dirección Nacional de Bomberos de Colombia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM env_bomberos WHERE nombre = 'Línea nacional de bomberos');
