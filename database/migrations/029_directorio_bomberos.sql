-- 029: Directorio de bomberos de Boyacá (estaciones y municipios que atienden).
-- Fuente: archivo «Directorio de Bomberos Boyacá.xlsx» remitido por la Secretaría
-- de Planeación, construido con el Directorio Nacional de Bomberos y el portal
-- bomberos.boyaca.gov.co. Idempotente: se puede volver a aplicar sin duplicar.
SET NAMES utf8mb4;

-- Municipios y el orden en que acuden a cada cuerpo de bomberos.
CREATE TABLE IF NOT EXISTS env_bomberos_cobertura (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    municipio       VARCHAR(120) NOT NULL,
    municipio_dane  VARCHAR(5)   NULL,
    provincia       VARCHAR(80)  NULL,
    estacion_propia TINYINT(1)   NOT NULL DEFAULT 0,
    orden           TINYINT      NOT NULL,
    cuerpo          VARCHAR(180) NOT NULL,
    telefono        VARCHAR(160) NULL,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_muni_orden (municipio, orden),
    INDEX idx_prov (provincia),
    INDEX idx_dane (municipio_dane)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Se reemplaza el contenido anterior (dos líneas de referencia cargadas en la 026).
DELETE FROM env_bomberos WHERE fuente IS NULL OR fuente <> 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026';

INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Aquitania', 'Aquitania', '15047', 'Sugamuxi', '3123188833', 'Transversal 7, vereda de Vargas. Sector el rincón', 'Cuerpo de Bomberos Voluntarios', 5.43739, -72.87153, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Arcabuco', 'Arcabuco', '15051', 'Ricaurte', '3202323374', 'Avenida 3 # 5 - 49', 'Cuerpo de Bomberos Voluntarios', 5.74949, -73.43885, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Belen', 'Belén', '15087', 'Tundama', '3106713694', 'Cra 5 # 3-29 Gimnasio Municipal', 'Cuerpo de Bomberos Voluntarios', 6.00506, -72.89365, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Betéitiva', 'Betéitiva', '15092', 'Valderrama', '3235863534', 'Cra 4#6-52', 'Cuerpo de Bomberos Voluntarios', 5.92081, -72.8485, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Busbanza', 'Busbanzá', '15114', 'Tundama', '3103408027', 'Calle. 3 # 45 – 5', 'Cuerpo de Bomberos Voluntarios', 5.84339, -72.87574, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Campo Hermoso', 'Campohermoso', '15135', NULL, '3124818863', 'CALLE 3 # 2-09 SECTOR 1', 'Cuerpo de Bomberos Voluntarios', 5.00681, -73.14471, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Chinavita', 'Chinavita', '15172', 'Neira', '3137032327', 'Calle 3 # 4 – 27', 'Cuerpo de Bomberos Voluntarios', 5.20307, -73.34058, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Chiquinquirá', 'Chiquinquirá', '15176', 'Occidente', '3124643336 / 3144624952', 'Carrera 12 # 13 – 70 Barrio Santo Domingo', 'Cuerpo de Bomberos Voluntarios', 5.62261, -73.80423, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Ciénega', 'Ciénega', '15189', 'Márquez', '3207398821', 'Vereda Plan Salida Ramiriquí kilometro 0', 'Cuerpo de Bomberos Voluntarios', 5.39308, -73.28208, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Chiquiza', 'Chíquiza', '15232', 'Centro', '3203059955', 'Carrera 4 calle 6', 'Cuerpo de Bomberos Voluntarios', 5.64296, -73.44561, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Chivatá', 'Chivatá', '15187', 'Centro', '3108177380 / 3214776228', 'KR 4 No 3-95', 'Cuerpo de Bomberos Voluntarios', 5.56397, -73.26326, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Cómbita', 'Combita', '15204', 'Centro', '3102932629 / 3214644387', 'Calle 3 # 5-55', 'Cuerpo de Bomberos Voluntarios', 5.68768, -73.32931, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Duitama', 'Duitama', '15238', 'Tundama', '3105788203 / 3118750847', 'Carrera 15 # 10-51', 'Cuerpo de Bomberos Voluntarios', 5.89059, -73.06707, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Firavitoba', 'Firavitoba', '15272', 'Sugamuxi', '3222932217', 'Calle 5 con cra 5', 'Cuerpo de Bomberos Voluntarios', 5.67357, -73.01995, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Garagoa', 'Garagoa', '15299', 'Neira', '3118489540', 'Cll 14 No 9-30', 'Cuerpo de Bomberos Voluntarios', 5.08948, -73.31432, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Guateque', 'Guateque', '15322', 'Oriente', '3142194981', 'CARRERA 8 # 10 - 27', 'Cuerpo de Bomberos Voluntarios', 5.0146, -73.48814, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Guayatá', 'Guayatá', '15325', 'Oriente', '3203133786 / 3134754942', 'Carrera 6 N° 8A - 40', 'Cuerpo de Bomberos Voluntarios', 4.93139, -73.49666, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Güicán de la Sierra', 'Güicán', '15332', NULL, '3202857460 / 3232120859', 'Transversal 4 No 6', 'Cuerpo de Bomberos Voluntarios', 6.56706, -72.25598, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Jenesano', 'Jenesano', '15367', 'Márquez', '3114025762 / 3212202310', 'Carrera 1 con Calle 2', 'Cuerpo de Bomberos Voluntarios', 5.37705, -73.37682, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Labranzagrande', 'Labranza Grande', '15377', 'La Libertad', '3112601359 / 3203328203', 'Carrera 9 # 8-05', 'Cuerpo de Bomberos Voluntarios', 5.54149, -72.5904, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Miraflorez', 'Miraflores', '15455', NULL, '3147791727 / 3102230768', 'Calle 3 # 2 117', 'Cuerpo de Bomberos Voluntarios', 5.15094, -73.17895, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Moniquirá', 'Moniquira', '15469', 'Ricaurte', '3145820436', 'Av Carrera Central #14A-05', 'Cuerpo de Bomberos Voluntarios', 5.86338, -73.55855, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Muzo', 'Muzo', '15480', 'Occidente', '3125147780', 'CALLE 4 CARRERA 3 Polideportivo', 'Cuerpo de Bomberos Voluntarios', 5.52526, -74.1166, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Nobsa', 'Nobsa', '15491', 'Sugamuxi', '3143555664 / 3102534914', 'Carrera 7 # 6-00', 'Cuerpo de Bomberos Voluntarios', 5.77876, -72.93262, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Nuevo Colón', 'Nuevo Colón', '15494', 'Márquez', '3118649746 / 3126822696', 'Transversal 2 # 31-142', 'Cuerpo de Bomberos Voluntarios', 5.35477, -73.44865, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Oicatá', 'Oicata', '15500', 'Centro', '3114919141 / 3133887822', 'Carrera 5 # 5-18', 'Cuerpo de Bomberos Voluntarios', 5.61081, -73.27988, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Otanche', 'Otanche', '15507', 'Occidente', '3134757148 / 3203634408', 'calle 8 # 4 - 26', 'Cuerpo de Bomberos Voluntarios', 5.75384, -74.19717, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Paipa', 'Paipa', '15516', 'Tundama', '3209193755', 'Plaza de mercado local 2', 'Cuerpo de Bomberos Voluntarios', 5.8266, -73.13795, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Pajarito Voluntarios', 'Pajarito', '15518', 'La Libertad', '3132629436 / 3229018878', 'No registrada', 'Cuerpo de Bomberos Voluntarios', 5.38234, -72.69671, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Pauna', 'Pauna', '15531', 'Occidente', '3118016917 / 3107599446', 'Calle 6 # 5-40/44', 'Cuerpo de Bomberos Voluntarios', 5.68869, -74.00502, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Paz de Río', 'Paz De Río', '15537', 'Valderrama', '3107651045', 'Carrera 3 # 4-30 Jorge Eliecer Gaitan', 'Cuerpo de Bomberos Voluntarios', 6.02881, -72.76745, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Ramiriquí', 'Ramiriquí', '15599', 'Márquez', '3132400079 / 3118827243', 'Carrera 5 # 5-103', 'Cuerpo de Bomberos Voluntarios', 5.31996, -73.3108, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Samacá', 'Samacá', '15646', 'Centro', '3125490057 / 3204322704', 'Cl. 9 #7 - 03', 'Cuerpo de Bomberos Voluntarios', 5.47053, -73.5213, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de San Eduardo', 'San Eduardo', '15660', 'Lengupá', '3138361841', 'calle 5 # 3-01 San Eduardo Centro', 'Cuerpo de Bomberos Voluntarios', 5.23477, -73.04909, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de San José de Pare', 'San José De Pare', '15664', 'Ricaurte', '3007539932 / 3229074241', 'Cra 2 No.2 - 105', 'Cuerpo de Bomberos Voluntarios', 5.99409, -73.54383, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Santa María', 'Santa María', '15690', 'Neira', '3112766092 / 3133524187', 'Barrio la Libertad.', 'Cuerpo de Bomberos Voluntarios', 4.82309, -73.25377, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Soatá', 'Soatá', '15753', 'Norte', '3143317251', 'Carrera 14 # 8-21', 'Cuerpo de Bomberos Voluntarios', 6.32308, -72.69603, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Sogamoso', 'Sogamoso', '15759', 'Sugamuxi', '7717373 / 3108159587', 'Kra 14 # 8 21', 'Cuerpo de Bomberos Voluntarios', 5.66801, -72.88648, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Sotaquirá', 'Sotaquirá', '15763', 'Centro', '3123009704 / 3143374180', 'Carrera 7 # 6-64 Centro', 'Cuerpo de Bomberos Voluntarios', 5.78526, -73.253, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Sutatenza', 'Sutatenza', '15778', 'Oriente', '3219116666 / 3105685239', 'Cra. 4 #1-75', 'Cuerpo de Bomberos Voluntarios', 5.0269, -73.43348, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Tibasosa', 'Tibasosa', '15806', 'Sugamuxi', '3117607461 / 3114982439', 'Cl. 5 #11-46,', 'Cuerpo de Bomberos Voluntarios', 5.74739, -73.01119, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Tinjacá', 'Tinjacá', '15808', 'Ricaurte', '3145570069', 'CALLE 4 # 2-48 CENTRO', 'Cuerpo de Bomberos Voluntarios', 5.57757, -73.67595, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Toca', 'Toca', '15814', 'Centro', '3138946229 / 3143331106', 'Salida Paipa - Campamento', 'Cuerpo de Bomberos Voluntarios', 5.58016, -73.16066, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Tunja', 'Tunja', '15001', 'Centro', '7426070 / 3187350352', 'Calle 22 # 6-22', 'Cuerpo de Bomberos Voluntarios', 5.51839, -73.37806, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Tuta', 'Tuta', '15837', 'Centro', '3132494078', 'Cr 8 No 4-15 Plaza de Mercado', 'Cuerpo de Bomberos Voluntarios', 5.6721, -73.18312, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Turmeque', 'Turmequé', '15835', 'Márquez', '3125655812', 'Casa de la Cultura Municipal', 'Cuerpo de Bomberos Voluntarios', 5.30564, -73.50897, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Soracá', 'Soracá', '15764', 'Centro', '3224353088 / 3138388932', 'Carrera 6 #4-55', 'Cuerpo de Bomberos Voluntarios', 5.49489, -73.31901, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Cubará', 'Cubará', '15223', 'Distrito Fronterizo', '3134215642', 'CARRERA 4 CALLE 4-43', 'Cuerpo de Bomberos Voluntarios', 6.88702, -72.18154, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Villa de Leyva', 'Villa De Leyva', '15407', 'Ricaurte', '6087649007 / 3224163975', 'Km 1 Salida a Arcabuco Vereda el Roble', 'Cuerpo de Bomberos Voluntarios', 5.66581, -73.51485, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de Puerto Boyacá', 'Puerto Boyacá', '15572', 'Zona de Manejo Especial', '5787383220 / 87384099310 / 5728472', 'Avenida Carrera 3 #8-40', 'Cuerpo de Bomberos Oficiales', 5.95349, -74.45278, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;
INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, direccion, tipo, lat, lon, fuente, verificado, activo)
VALUES ('Cuerpo de Bomberos de CBA Juan Jose Rondón', 'Paipa', '15516', 'Tundama', '3142741886', 'Aeropuerto Juan Jose Rondón de Paipa', 'Cuerpo de Bomberos Aeronáuticos', 5.8266, -73.13795, 'Dirección Nacional de Bomberos de Colombia y Cuerpos de Bomberos de Boyacá (bomberos.boyaca.gov.co), consolidado por la Secretaría de Planeación, 2026', 1, 1)
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), telefono=VALUES(telefono), direccion=VALUES(direccion), tipo=VALUES(tipo), lat=VALUES(lat), lon=VALUES(lon), fuente=VALUES(fuente), verificado=1, activo=1;

INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Almeida', '15022', 'Oriente', 0, 1, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Almeida', '15022', 'Oriente', 0, 2, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Almeida', '15022', 'Oriente', 0, 3, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Aquitania', '15047', 'Sugamuxi', 1, 1, 'Aquitania', '3123188833')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Aquitania', '15047', 'Sugamuxi', 1, 2, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Aquitania', '15047', 'Sugamuxi', 1, 3, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Arcabuco', '15051', 'Ricaurte', 1, 1, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Arcabuco', '15051', 'Ricaurte', 1, 2, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Arcabuco', '15051', 'Ricaurte', 1, 3, 'Moniquirá', '3145820436')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Belén', '15087', 'Tundama', 1, 1, 'Belen', '3106713694')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Belén', '15087', 'Tundama', 1, 2, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Belén', '15087', 'Tundama', 1, 3, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Berbeo', '15090', 'Lengupá', 0, 1, 'Miraflorez', '3147791727 / 3102230768')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Berbeo', '15090', 'Lengupá', 0, 2, 'San Eduardo', '3138361841')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Berbeo', '15090', 'Lengupá', 0, 3, 'Campo Hermoso', '3124818863')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Betéitiva', '15092', 'Valderrama', 1, 1, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Betéitiva', '15092', 'Valderrama', 1, 2, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Betéitiva', '15092', 'Valderrama', 1, 3, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Boavita', '15097', 'Norte', 0, 1, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Boavita', '15097', 'Norte', 0, 2, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Boavita', '15097', 'Norte', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Boyacá', '15104', 'Márquez', 0, 1, 'Ramiriquí', '3132400079 / 3118827243')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Boyacá', '15104', 'Márquez', 0, 2, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Boyacá', '15104', 'Márquez', 0, 3, 'Ciénega', '3207398821')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Briceño', '15106', 'Occidente', 0, 1, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Briceño', '15106', 'Occidente', 0, 2, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Briceño', '15106', 'Occidente', 0, 3, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Buenavista', '15109', 'Occidente', 0, 1, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Buenavista', '15109', 'Occidente', 0, 2, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Buenavista', '15109', 'Occidente', 0, 3, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Busbanzá', '15114', 'Tundama', 1, 1, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Busbanzá', '15114', 'Tundama', 1, 2, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Busbanzá', '15114', 'Tundama', 1, 3, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Caldas', '15131', 'Occidente', 0, 1, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Caldas', '15131', 'Occidente', 0, 2, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Caldas', '15131', 'Occidente', 0, 3, 'Tinjacá', '3145570069')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Campohermoso', '15135', 'Lengupá', 1, 1, 'Campo Hermoso', '3124818863')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Campohermoso', '15135', 'Lengupá', 1, 2, 'San Eduardo', '3138361841')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Campohermoso', '15135', 'Lengupá', 1, 3, 'Miraflorez', '3147791727 / 3102230768')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cerinza', '15162', 'Tundama', 0, 1, 'Belen', '3106713694')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cerinza', '15162', 'Tundama', 0, 2, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cerinza', '15162', 'Tundama', 0, 3, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chinavita', '15172', 'Neira', 1, 1, 'Chinavita', '3137032327')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chinavita', '15172', 'Neira', 1, 2, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chinavita', '15172', 'Neira', 1, 3, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chiquinquirá', '15176', 'Occidente', 1, 1, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chiquinquirá', '15176', 'Occidente', 1, 2, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chiquinquirá', '15176', 'Occidente', 1, 3, 'Tinjacá', '3145570069')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chíquiza', '15232', 'Centro', 1, 1, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chíquiza', '15232', 'Centro', 1, 2, 'Samacá', '3125490057 / 3204322704')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chíquiza', '15232', 'Centro', 1, 3, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chiscas', '15180', 'Gutiérrez', 0, 1, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chiscas', '15180', 'Gutiérrez', 0, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chiscas', '15180', 'Gutiérrez', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chita', '15183', 'Valderrama', 0, 1, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chita', '15183', 'Valderrama', 0, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chita', '15183', 'Valderrama', 0, 3, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chitaraque', '15185', 'Ricaurte', 0, 1, 'San José de Pare', '3007539932 / 3229074241')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chitaraque', '15185', 'Ricaurte', 0, 2, 'Moniquirá', '3145820436')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chitaraque', '15185', 'Ricaurte', 0, 3, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chivatá', '15187', 'Centro', 1, 1, 'Chivatá', '3108177380 / 3214776228')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chivatá', '15187', 'Centro', 1, 2, 'Oicatá', '3114919141 / 3133887822')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chivatá', '15187', 'Centro', 1, 3, 'Cómbita', '3102932629 / 3214644387')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chivor', '15236', 'Oriente', 0, 1, 'Santa María', '3112766092 / 3133524187')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chivor', '15236', 'Oriente', 0, 2, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Chivor', '15236', 'Oriente', 0, 3, 'Guateque', '3142194981')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ciénega', '15189', 'Márquez', 1, 1, 'Ciénega', '3207398821')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ciénega', '15189', 'Márquez', 1, 2, 'Ramiriquí', '3132400079 / 3118827243')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ciénega', '15189', 'Márquez', 1, 3, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Combita', '15204', 'Centro', 1, 1, 'Cómbita', '3102932629 / 3214644387')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Combita', '15204', 'Centro', 1, 2, 'Oicatá', '3114919141 / 3133887822')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Combita', '15204', 'Centro', 1, 3, 'Chivatá', '3108177380 / 3214776228')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Coper', '15212', 'Occidente', 0, 1, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Coper', '15212', 'Occidente', 0, 2, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Coper', '15212', 'Occidente', 0, 3, 'Otanche', '3134757148 / 3203634408')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Corrales', '15215', 'Tundama', 0, 1, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Corrales', '15215', 'Tundama', 0, 2, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Corrales', '15215', 'Tundama', 0, 3, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Covarachia', '15218', 'Norte', 0, 1, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Covarachia', '15218', 'Norte', 0, 2, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Covarachia', '15218', 'Norte', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cubará', '15223', 'Distrito Fronterizo', 1, 1, 'Cubará', '3134215642')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cubará', '15223', 'Distrito Fronterizo', 1, 2, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cubará', '15223', 'Distrito Fronterizo', 1, 3, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cucaita', '15224', 'Centro', 0, 1, 'Samacá', '3125490057 / 3204322704')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cucaita', '15224', 'Centro', 0, 2, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cucaita', '15224', 'Centro', 0, 3, 'Tunja', '7426070 / 3187350352')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cuìtiva', '15226', 'Sugamuxi', 0, 1, 'Aquitania', '3123188833')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cuìtiva', '15226', 'Sugamuxi', 0, 2, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Cuìtiva', '15226', 'Sugamuxi', 0, 3, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Duitama', '15238', 'Tundama', 1, 1, 'Duitama', '3105788203 / 3118750847')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Duitama', '15238', 'Tundama', 1, 2, 'Tibasosa', '3117607461 / 3114982439')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Duitama', '15238', 'Tundama', 1, 3, 'Paipa', '3209193755')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('El Cocuy', '15244', 'Gutiérrez', 0, 1, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('El Cocuy', '15244', 'Gutiérrez', 0, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('El Cocuy', '15244', 'Gutiérrez', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('El Espino', '15248', 'Gutiérrez', 0, 1, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('El Espino', '15248', 'Gutiérrez', 0, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('El Espino', '15248', 'Gutiérrez', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Firavitoba', '15272', 'Sugamuxi', 1, 1, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Firavitoba', '15272', 'Sugamuxi', 1, 2, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Firavitoba', '15272', 'Sugamuxi', 1, 3, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Floresta', '15276', 'Tundama', 0, 1, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Floresta', '15276', 'Tundama', 0, 2, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Floresta', '15276', 'Tundama', 0, 3, 'Duitama', '3105788203 / 3118750847')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Gachantivá', '15293', 'Ricaurte', 0, 1, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Gachantivá', '15293', 'Ricaurte', 0, 2, 'Moniquirá', '3145820436')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Gachantivá', '15293', 'Ricaurte', 0, 3, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Gameza', '15296', 'Sugamuxi', 0, 1, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Gameza', '15296', 'Sugamuxi', 0, 2, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Gameza', '15296', 'Sugamuxi', 0, 3, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Garagoa', '15299', 'Neira', 1, 1, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Garagoa', '15299', 'Neira', 1, 2, 'Chinavita', '3137032327')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Garagoa', '15299', 'Neira', 1, 3, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guacamayas', '15317', 'Gutiérrez', 0, 1, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guacamayas', '15317', 'Gutiérrez', 0, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guacamayas', '15317', 'Gutiérrez', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guateque', '15322', 'Oriente', 1, 1, 'Guateque', '3142194981')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guateque', '15322', 'Oriente', 1, 2, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guateque', '15322', 'Oriente', 1, 3, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guayatá', '15325', 'Oriente', 1, 1, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guayatá', '15325', 'Oriente', 1, 2, 'Guateque', '3142194981')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Guayatá', '15325', 'Oriente', 1, 3, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Güicán', '15332', 'Gutiérrez', 1, 1, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Güicán', '15332', 'Gutiérrez', 1, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Güicán', '15332', 'Gutiérrez', 1, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Iza', '15362', 'Sugamuxi', 0, 1, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Iza', '15362', 'Sugamuxi', 0, 2, 'Aquitania', '3123188833')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Iza', '15362', 'Sugamuxi', 0, 3, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Jenesano', '15367', 'Márquez', 1, 1, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Jenesano', '15367', 'Márquez', 1, 2, 'Ramiriquí', '3132400079 / 3118827243')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Jenesano', '15367', 'Márquez', 1, 3, 'Ciénega', '3207398821')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Jericó', '15368', 'Valderrama', 0, 1, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Jericó', '15368', 'Valderrama', 0, 2, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Jericó', '15368', 'Valderrama', 0, 3, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Capilla', '15380', 'Oriente', 0, 1, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Capilla', '15380', 'Oriente', 0, 2, 'Guateque', '3142194981')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Capilla', '15380', 'Oriente', 0, 3, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Uvita', '15403', 'Norte', 0, 1, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Uvita', '15403', 'Norte', 0, 2, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Uvita', '15403', 'Norte', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Victoria', '15401', 'Occidente', 0, 1, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Victoria', '15401', 'Occidente', 0, 2, 'Otanche', '3134757148 / 3203634408')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('La Victoria', '15401', 'Occidente', 0, 3, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Labranza Grande', '15377', 'La Libertad', 1, 1, 'Labranzagrande', '3112601359 / 3203328203')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Labranza Grande', '15377', 'La Libertad', 1, 2, 'Pajarito Voluntarios', '3132629436 / 3229018878')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Labranza Grande', '15377', 'La Libertad', 1, 3, 'Aquitania', '3123188833')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Macanal', '15425', 'Neira', 0, 1, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Macanal', '15425', 'Neira', 0, 2, 'Santa María', '3112766092 / 3133524187')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Macanal', '15425', 'Neira', 0, 3, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Maripí', '15442', 'Occidente', 0, 1, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Maripí', '15442', 'Occidente', 0, 2, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Maripí', '15442', 'Occidente', 0, 3, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Miraflores', '15455', 'Lengupá', 1, 1, 'Miraflorez', '3147791727 / 3102230768')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Miraflores', '15455', 'Lengupá', 1, 2, 'San Eduardo', '3138361841')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Miraflores', '15455', 'Lengupá', 1, 3, 'Campo Hermoso', '3124818863')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Mongua', '15464', 'Sugamuxi', 0, 1, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Mongua', '15464', 'Sugamuxi', 0, 2, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Mongua', '15464', 'Sugamuxi', 0, 3, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Monguì', '15466', 'Sugamuxi', 0, 1, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Monguì', '15466', 'Sugamuxi', 0, 2, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Monguì', '15466', 'Sugamuxi', 0, 3, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Moniquira', '15469', 'Ricaurte', 1, 1, 'Moniquirá', '3145820436')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Moniquira', '15469', 'Ricaurte', 1, 2, 'San José de Pare', '3007539932 / 3229074241')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Moniquira', '15469', 'Ricaurte', 1, 3, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Motavita', '15476', 'Centro', 0, 1, 'Tunja', '7426070 / 3187350352')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Motavita', '15476', 'Centro', 0, 2, 'Oicatá', '3114919141 / 3133887822')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Motavita', '15476', 'Centro', 0, 3, 'Cómbita', '3102932629 / 3214644387')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Muzo', '15480', 'Occidente', 1, 1, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Muzo', '15480', 'Occidente', 1, 2, 'Otanche', '3134757148 / 3203634408')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Muzo', '15480', 'Occidente', 1, 3, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Nobsa', '15491', 'Sugamuxi', 1, 1, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Nobsa', '15491', 'Sugamuxi', 1, 2, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Nobsa', '15491', 'Sugamuxi', 1, 3, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Nuevo Colón', '15494', 'Márquez', 1, 1, 'Nuevo Colón', '3118649746 / 3126822696')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Nuevo Colón', '15494', 'Márquez', 1, 2, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Nuevo Colón', '15494', 'Márquez', 1, 3, 'Ramiriquí', '3132400079 / 3118827243')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Oicata', '15500', 'Centro', 1, 1, 'Oicatá', '3114919141 / 3133887822')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Oicata', '15500', 'Centro', 1, 2, 'Cómbita', '3102932629 / 3214644387')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Oicata', '15500', 'Centro', 1, 3, 'Chivatá', '3108177380 / 3214776228')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Otanche', '15507', 'Occidente', 1, 1, 'Otanche', '3134757148 / 3203634408')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Otanche', '15507', 'Occidente', 1, 2, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Otanche', '15507', 'Occidente', 1, 3, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pachavita', '15511', 'Neira', 0, 1, 'Chinavita', '3137032327')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pachavita', '15511', 'Neira', 0, 2, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pachavita', '15511', 'Neira', 0, 3, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Páez', '15514', 'Lengupá', 0, 1, 'Campo Hermoso', '3124818863')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Páez', '15514', 'Lengupá', 0, 2, 'San Eduardo', '3138361841')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Páez', '15514', 'Lengupá', 0, 3, 'Miraflorez', '3147791727 / 3102230768')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paipa', '15516', 'Tundama', 1, 1, 'Paipa', '3209193755')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paipa', '15516', 'Tundama', 1, 2, 'Duitama', '3105788203 / 3118750847')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paipa', '15516', 'Tundama', 1, 3, 'Tibasosa', '3117607461 / 3114982439')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pajarito', '15518', 'La Libertad', 1, 1, 'Pajarito Voluntarios', '3132629436 / 3229018878')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pajarito', '15518', 'La Libertad', 1, 2, 'Labranzagrande', '3112601359 / 3203328203')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pajarito', '15518', 'La Libertad', 1, 3, 'Aquitania', '3123188833')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Panqueba', '15522', 'Gutiérrez', 0, 1, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Panqueba', '15522', 'Gutiérrez', 0, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Panqueba', '15522', 'Gutiérrez', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pauna', '15531', 'Occidente', 1, 1, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pauna', '15531', 'Occidente', 1, 2, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pauna', '15531', 'Occidente', 1, 3, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paya', '15533', 'La Libertad', 0, 1, 'Labranzagrande', '3112601359 / 3203328203')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paya', '15533', 'La Libertad', 0, 2, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paya', '15533', 'La Libertad', 0, 3, 'Pajarito Voluntarios', '3132629436 / 3229018878')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paz De Río', '15537', 'Valderrama', 1, 1, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paz De Río', '15537', 'Valderrama', 1, 2, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Paz De Río', '15537', 'Valderrama', 1, 3, 'Belen', '3106713694')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pesca', '15542', 'Sugamuxi', 0, 1, 'Aquitania', '3123188833')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pesca', '15542', 'Sugamuxi', 0, 2, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pesca', '15542', 'Sugamuxi', 0, 3, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pisba', '15550', 'La Libertad', 0, 1, 'Labranzagrande', '3112601359 / 3203328203')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pisba', '15550', 'La Libertad', 0, 2, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Pisba', '15550', 'La Libertad', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Puerto Boyacá', '15572', 'Zona de Manejo Especial', 1, 1, 'Puerto Boyacá', '5787383220 / 87384099310 / 5728472')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Puerto Boyacá', '15572', 'Zona de Manejo Especial', 1, 2, 'Otanche', '3134757148 / 3203634408')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Puerto Boyacá', '15572', 'Zona de Manejo Especial', 1, 3, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Quípama', '15580', 'Occidente', 0, 1, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Quípama', '15580', 'Occidente', 0, 2, 'Otanche', '3134757148 / 3203634408')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Quípama', '15580', 'Occidente', 0, 3, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ramiriquí', '15599', 'Márquez', 1, 1, 'Ramiriquí', '3132400079 / 3118827243')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ramiriquí', '15599', 'Márquez', 1, 2, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ramiriquí', '15599', 'Márquez', 1, 3, 'Ciénega', '3207398821')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Rondón', '15621', 'Márquez', 0, 1, 'Ciénega', '3207398821')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Rondón', '15621', 'Márquez', 0, 2, 'Ramiriquí', '3132400079 / 3118827243')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Rondón', '15621', 'Márquez', 0, 3, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Saboyá', '15632', 'Occidente', 0, 1, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Saboyá', '15632', 'Occidente', 0, 2, 'Tinjacá', '3145570069')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Saboyá', '15632', 'Occidente', 0, 3, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sáchica', '15638', 'Ricaurte', 0, 1, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sáchica', '15638', 'Ricaurte', 0, 2, 'Samacá', '3125490057 / 3204322704')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sáchica', '15638', 'Ricaurte', 0, 3, 'Tinjacá', '3145570069')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Samacá', '15646', 'Centro', 1, 1, 'Samacá', '3125490057 / 3204322704')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Samacá', '15646', 'Centro', 1, 2, 'Tunja', '7426070 / 3187350352')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Samacá', '15646', 'Centro', 1, 3, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Eduardo', '15660', 'Lengupá', 1, 1, 'San Eduardo', '3138361841')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Eduardo', '15660', 'Lengupá', 1, 2, 'Miraflorez', '3147791727 / 3102230768')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Eduardo', '15660', 'Lengupá', 1, 3, 'Campo Hermoso', '3124818863')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San José De Pare', '15664', 'Ricaurte', 1, 1, 'San José de Pare', '3007539932 / 3229074241')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San José De Pare', '15664', 'Ricaurte', 1, 2, 'Moniquirá', '3145820436')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San José De Pare', '15664', 'Ricaurte', 1, 3, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Luis De Gaceno', '15667', 'Neira', 0, 1, 'Santa María', '3112766092 / 3133524187')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Luis De Gaceno', '15667', 'Neira', 0, 2, 'Campo Hermoso', '3124818863')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Luis De Gaceno', '15667', 'Neira', 0, 3, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Mateo', '15673', 'Norte', 0, 1, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Mateo', '15673', 'Norte', 0, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Mateo', '15673', 'Norte', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Miguel De Sema', '15676', 'Occidente', 0, 1, 'Tinjacá', '3145570069')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Miguel De Sema', '15676', 'Occidente', 0, 2, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Miguel De Sema', '15676', 'Occidente', 0, 3, 'Samacá', '3125490057 / 3204322704')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Pablo De Borbur', '15681', 'Occidente', 0, 1, 'Otanche', '3134757148 / 3203634408')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Pablo De Borbur', '15681', 'Occidente', 0, 2, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('San Pablo De Borbur', '15681', 'Occidente', 0, 3, 'Muzo', '3125147780')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa María', '15690', 'Neira', 1, 1, 'Santa María', '3112766092 / 3133524187')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa María', '15690', 'Neira', 1, 2, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa María', '15690', 'Neira', 1, 3, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa Rosa De Viterbo', '15693', 'Tundama', 0, 1, 'Duitama', '3105788203 / 3118750847')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa Rosa De Viterbo', '15693', 'Tundama', 0, 2, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa Rosa De Viterbo', '15693', 'Tundama', 0, 3, 'Tibasosa', '3117607461 / 3114982439')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa Sofía', '15696', 'Ricaurte', 0, 1, 'Tinjacá', '3145570069')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa Sofía', '15696', 'Ricaurte', 0, 2, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santa Sofía', '15696', 'Ricaurte', 0, 3, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santana', '15686', 'Ricaurte', 0, 1, 'San José de Pare', '3007539932 / 3229074241')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santana', '15686', 'Ricaurte', 0, 2, 'Moniquirá', '3145820436')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Santana', '15686', 'Ricaurte', 0, 3, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sativanorte', '15720', 'Norte', 0, 1, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sativanorte', '15720', 'Norte', 0, 2, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sativanorte', '15720', 'Norte', 0, 3, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sativasur', '15723', 'Norte', 0, 1, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sativasur', '15723', 'Norte', 0, 2, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sativasur', '15723', 'Norte', 0, 3, 'Belen', '3106713694')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Siachoque', '15740', 'Centro', 0, 1, 'Chivatá', '3108177380 / 3214776228')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Siachoque', '15740', 'Centro', 0, 2, 'Toca', '3138946229 / 3143331106')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Siachoque', '15740', 'Centro', 0, 3, 'Oicatá', '3114919141 / 3133887822')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Soatá', '15753', 'Norte', 1, 1, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Soatá', '15753', 'Norte', 1, 2, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Soatá', '15753', 'Norte', 1, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Socha', '15757', 'Valderrama', 0, 1, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Socha', '15757', 'Valderrama', 0, 2, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Socha', '15757', 'Valderrama', 0, 3, 'Belen', '3106713694')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Socotá', '15755', 'Valderrama', 0, 1, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Socotá', '15755', 'Valderrama', 0, 2, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Socotá', '15755', 'Valderrama', 0, 3, 'Belen', '3106713694')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sogamoso', '15759', 'Sugamuxi', 1, 1, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sogamoso', '15759', 'Sugamuxi', 1, 2, 'Tibasosa', '3117607461 / 3114982439')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sogamoso', '15759', 'Sugamuxi', 1, 3, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Somondoco', '15761', 'Oriente', 0, 1, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Somondoco', '15761', 'Oriente', 0, 2, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Somondoco', '15761', 'Oriente', 0, 3, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sora', '15762', 'Centro', 0, 1, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sora', '15762', 'Centro', 0, 2, 'Samacá', '3125490057 / 3204322704')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sora', '15762', 'Centro', 0, 3, 'Tunja', '7426070 / 3187350352')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Soracá', '15764', 'Centro', 1, 1, 'Soracá', '3224353088 / 3138388932')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Soracá', '15764', 'Centro', 1, 2, 'Tunja', '7426070 / 3187350352')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Soracá', '15764', 'Centro', 1, 3, 'Chivatá', '3108177380 / 3214776228')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sotaquirá', '15763', 'Centro', 1, 1, 'Sotaquirá', '3123009704 / 3143374180')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sotaquirá', '15763', 'Centro', 1, 2, 'Tuta', '3132494078')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sotaquirá', '15763', 'Centro', 1, 3, 'Paipa', '3209193755')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Susacón', '15774', 'Norte', 0, 1, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Susacón', '15774', 'Norte', 0, 2, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Susacón', '15774', 'Norte', 0, 3, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sutamarchàn', '15776', 'Ricaurte', 0, 1, 'Tinjacá', '3145570069')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sutamarchàn', '15776', 'Ricaurte', 0, 2, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sutamarchàn', '15776', 'Ricaurte', 0, 3, 'Samacá', '3125490057 / 3204322704')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sutatenza', '15778', 'Oriente', 1, 1, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sutatenza', '15778', 'Oriente', 1, 2, 'Guateque', '3142194981')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Sutatenza', '15778', 'Oriente', 1, 3, 'Guayatá', '3203133786 / 3134754942')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tasco', '15790', 'Valderrama', 0, 1, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tasco', '15790', 'Valderrama', 0, 2, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tasco', '15790', 'Valderrama', 0, 3, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tenza', '15798', 'Oriente', 0, 1, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tenza', '15798', 'Oriente', 0, 2, 'Sutatenza', '3219116666 / 3105685239')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tenza', '15798', 'Oriente', 0, 3, 'Guateque', '3142194981')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tibana', '15804', 'Márquez', 0, 1, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tibana', '15804', 'Márquez', 0, 2, 'Nuevo Colón', '3118649746 / 3126822696')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tibana', '15804', 'Márquez', 0, 3, 'Ramiriquí', '3132400079 / 3118827243')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tibasosa', '15806', 'Sugamuxi', 1, 1, 'Tibasosa', '3117607461 / 3114982439')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tibasosa', '15806', 'Sugamuxi', 1, 2, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tibasosa', '15806', 'Sugamuxi', 1, 3, 'Duitama', '3105788203 / 3118750847')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tinjacá', '15808', 'Ricaurte', 1, 1, 'Tinjacá', '3145570069')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tinjacá', '15808', 'Ricaurte', 1, 2, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tinjacá', '15808', 'Ricaurte', 1, 3, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tipacoque', '15810', 'Norte', 0, 1, 'Soatá', '3143317251')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tipacoque', '15810', 'Norte', 0, 2, 'Güicán de la Sierra', '3202857460 / 3232120859')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tipacoque', '15810', 'Norte', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Toca', '15814', 'Centro', 1, 1, 'Toca', '3138946229 / 3143331106')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Toca', '15814', 'Centro', 1, 2, 'Chivatá', '3108177380 / 3214776228')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Toca', '15814', 'Centro', 1, 3, 'Oicatá', '3114919141 / 3133887822')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Togüí', '15816', 'Ricaurte', 0, 1, 'Moniquirá', '3145820436')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Togüí', '15816', 'Ricaurte', 0, 2, 'San José de Pare', '3007539932 / 3229074241')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Togüí', '15816', 'Ricaurte', 0, 3, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tópaga', '15820', 'Sugamuxi', 0, 1, 'Nobsa', '3143555664 / 3102534914')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tópaga', '15820', 'Sugamuxi', 0, 2, 'Busbanza', '3103408027')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tópaga', '15820', 'Sugamuxi', 0, 3, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tota', '15822', 'Sugamuxi', 0, 1, 'Aquitania', '3123188833')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tota', '15822', 'Sugamuxi', 0, 2, 'Sogamoso', '7717373 / 3108159587')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tota', '15822', 'Sugamuxi', 0, 3, 'Firavitoba', '3222932217')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tunja', '15001', 'Centro', 1, 1, 'Tunja', '7426070 / 3187350352')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tunja', '15001', 'Centro', 1, 2, 'Oicatá', '3114919141 / 3133887822')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tunja', '15001', 'Centro', 1, 3, 'Chivatá', '3108177380 / 3214776228')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tununguá', '15832', 'Occidente', 0, 1, 'Pauna', '3118016917 / 3107599446')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tununguá', '15832', 'Occidente', 0, 2, 'Chiquinquirá', '3124643336 / 3144624952')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tununguá', '15832', 'Occidente', 0, 3, 'Otanche', '3134757148 / 3203634408')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Turmequé', '15835', 'Márquez', 1, 1, 'Turmequé', '3125655812')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Turmequé', '15835', 'Márquez', 1, 2, 'Nuevo Colón', '3118649746 / 3126822696')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Turmequé', '15835', 'Márquez', 1, 3, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tuta', '15837', 'Centro', 1, 1, 'Tuta', '3132494078')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tuta', '15837', 'Centro', 1, 2, 'Sotaquirá', '3123009704 / 3143374180')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tuta', '15837', 'Centro', 1, 3, 'Cómbita', '3102932629 / 3214644387')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tutazá', '15839', 'Tundama', 0, 1, 'Belen', '3106713694')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tutazá', '15839', 'Tundama', 0, 2, 'Betéitiva', '3235863534')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Tutazá', '15839', 'Tundama', 0, 3, 'Paz de Río', '3107651045')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Umbita', '15842', 'Márquez', 0, 1, 'Chinavita', '3137032327')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Umbita', '15842', 'Márquez', 0, 2, 'Nuevo Colón', '3118649746 / 3126822696')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Umbita', '15842', 'Márquez', 0, 3, 'Garagoa', '3118489540')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ventaquemada', '15861', 'Centro', 0, 1, 'Nuevo Colón', '3118649746 / 3126822696')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ventaquemada', '15861', 'Centro', 0, 2, 'Samacá', '3125490057 / 3204322704')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Ventaquemada', '15861', 'Centro', 0, 3, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Villa De Leyva', '15407', 'Ricaurte', 1, 1, 'Villa de Leyva', '6087649007 / 3224163975')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Villa De Leyva', '15407', 'Ricaurte', 1, 2, 'Chiquiza', '3203059955')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Villa De Leyva', '15407', 'Ricaurte', 1, 3, 'Arcabuco', '3202323374')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Viracachá', '15879', 'Márquez', 0, 1, 'Ciénega', '3207398821')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Viracachá', '15879', 'Márquez', 0, 2, 'Ramiriquí', '3132400079 / 3118827243')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Viracachá', '15879', 'Márquez', 0, 3, 'Jenesano', '3114025762 / 3212202310')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Zetaquirá', '15897', 'Lengupá', 0, 1, 'Miraflorez', '3147791727 / 3102230768')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Zetaquirá', '15897', 'Lengupá', 0, 2, 'Chinavita', '3137032327')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
INSERT INTO env_bomberos_cobertura (municipio, municipio_dane, provincia, estacion_propia, orden, cuerpo, telefono)
VALUES ('Zetaquirá', '15897', 'Lengupá', 0, 3, 'San Eduardo', '3138361841')
ON DUPLICATE KEY UPDATE municipio_dane=VALUES(municipio_dane), provincia=VALUES(provincia), estacion_propia=VALUES(estacion_propia), cuerpo=VALUES(cuerpo), telefono=VALUES(telefono);
