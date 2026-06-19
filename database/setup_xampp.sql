-- Ejecutar en phpMyAdmin (pestaña SQL) o mysql CLI antes de importar schema.sql
-- Crea la base de datos si no existe.

CREATE DATABASE IF NOT EXISTS observatorio_boyaca
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Opcional: usuario dedicado (como en el código por defecto)
-- CREATE USER IF NOT EXISTS 'observa_user'@'localhost' IDENTIFIED BY 'Observa2025*';
-- GRANT ALL PRIVILEGES ON observatorio_boyaca.* TO 'observa_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Luego importe: database/schema.sql, database/seed_example.sql (observatorios y roles)
-- y migraciones 001–008 (006 = layout gráficos; 007 = tableros principal; 008 = gráficos demo opcional).
