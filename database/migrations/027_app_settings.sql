-- 027: Tabla de ajustes de la aplicación (clave/valor).
-- Permite guardar credenciales de servicios externos desde el CMS sin
-- ponerlas en el repositorio, que es público. La tabla se crea vacía: los
-- valores los carga cada entidad en su propio entorno.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS app_settings (
    clave          VARCHAR(80)  NOT NULL PRIMARY KEY,
    valor          TEXT         NULL,
    descripcion    VARCHAR(255) NULL,
    secreto        TINYINT(1)   NOT NULL DEFAULT 0,
    actualizado_en DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fila de referencia, sin valor: el CMS la completa.
INSERT INTO app_settings (clave, valor, descripcion, secreto)
SELECT 'firms_map_key', NULL,
       'Clave de NASA FIRMS para los focos de calor del Observatorio Ambiental (se solicita en firms.modaps.eosdis.nasa.gov/api/map_key/)', 1
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE clave = 'firms_map_key');
