-- 017_encuesta_genero.sql
-- Agrega el género a la encuesta opcional (idempotente).
ALTER TABLE cms_visitor_surveys
  ADD COLUMN IF NOT EXISTS gender VARCHAR(24) NULL AFTER age_range;
