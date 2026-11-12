-- 036: recoge los aportes de los asistentes al Primer Foro Anual de la Red de
-- Observatorios (Universidad de los Llanos, 12 de noviembre de 2026).
--
-- Para qué: el foro reúne a observatorios de varias regiones y el material que
-- salga de ahí es la base de un artículo posterior. En vez de pedir los aportes
-- por correo y perderlos, se recogen por un formulario del propio portal.
--
-- La tabla guarda el consentimiento junto al aporte, no aparte: si alguien
-- escribe pero no autoriza la publicación, su texto no se puede usar y eso
-- tiene que verse en la misma fila.
--
-- No guarda datos sensibles. El correo es opcional y solo sirve para enviar el
-- artículo cuando salga.

CREATE TABLE IF NOT EXISTS `foro_aportes` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `evento`        VARCHAR(80)  NOT NULL DEFAULT 'foro-unillanos-2026',
  `institucion`   VARCHAR(160) NULL,
  `rol`           VARCHAR(60)  NULL,
  `territorio`    VARCHAR(120) NULL,
  `falta_info`    TEXT         NULL COMMENT 'Qué información del territorio necesita y no encuentra',
  `barrera`       VARCHAR(60)  NULL COMMENT 'Principal barrera para usar datos territoriales',
  `prioridades`   VARCHAR(255) NULL COMMENT 'Qué debería priorizar la red (varias, separadas por |)',
  `aporte`        TEXT         NULL COMMENT 'Aporte libre para el artículo',
  `consentimiento` VARCHAR(20) NOT NULL DEFAULT 'no' COMMENT 'anonimo | con-credito | no',
  `nombre`        VARCHAR(120) NULL COMMENT 'Solo si autoriza crédito',
  `correo`        VARCHAR(160) NULL COMMENT 'Opcional, para enviarle el artículo',
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_evento` (`evento`),
  KEY `idx_creado` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
