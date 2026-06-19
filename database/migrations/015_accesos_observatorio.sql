-- Migración 015: tabla de analítica de accesos (tracker.php / dashboard).
-- Esta tabla era usada por tracker.php, dashboard.php y dashboard/api/* pero no
-- estaba versionada en schema.sql ni en ninguna migración previa. Su ausencia
-- provocaba un error fatal en el panel de analítica. Se versiona aquí.

CREATE TABLE IF NOT EXISTS accesos_observatorio (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    ip           VARCHAR(64)   NULL,
    pais         VARCHAR(120)  NULL,
    codigo_pais  VARCHAR(8)    NULL,
    ciudad       VARCHAR(160)  NULL,
    region       VARCHAR(160)  NULL,
    latitud      VARCHAR(32)   NULL,
    longitud     VARCHAR(32)   NULL,
    isp          VARCHAR(255)  NULL,
    navegador    VARCHAR(120)  NULL,
    so           VARCHAR(120)  NULL,
    dispositivo  VARCHAR(60)   NULL,
    profesion    VARCHAR(160)  NULL,
    edad         VARCHAR(40)   NULL,
    otro         VARCHAR(255)  NULL,
    pagina       VARCHAR(255)  NULL,
    referer      VARCHAR(512)  NULL,
    idioma       VARCHAR(60)   NULL,
    fecha        DATETIME      NULL,
    PRIMARY KEY (id),
    KEY idx_accesos_fecha (fecha),
    KEY idx_accesos_pais (pais),
    KEY idx_accesos_pagina (pagina)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
