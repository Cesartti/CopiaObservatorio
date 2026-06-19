-- 012: Amplía la tabla indicators con los 14 campos de la Hoja de Vida (Base_general.xlsx)
ALTER TABLE indicators
  ADD COLUMN category_1 VARCHAR(160) NULL AFTER observatory_id,
  ADD COLUMN category_2 VARCHAR(160) NULL AFTER category_1,
  ADD COLUMN tags VARCHAR(255) NULL AFTER category_2,
  ADD COLUMN thematic_breakdown TEXT NULL AFTER formula_text,
  ADD COLUMN geographic_breakdown VARCHAR(160) NULL AFTER thematic_breakdown,
  ADD COLUMN definition TEXT NULL AFTER description,
  ADD COLUMN calculation_formula TEXT NULL AFTER definition,
  ADD COLUMN baseline_date VARCHAR(80) NULL AFTER periodicity,
  ADD COLUMN delivery_form VARCHAR(255) NULL AFTER baseline_date,
  ADD COLUMN source_link VARCHAR(512) NULL AFTER source,
  ADD COLUMN actors TEXT NULL AFTER source_link,
  ADD COLUMN responsible_entity VARCHAR(255) NULL AFTER actors,
  ADD COLUMN observations TEXT NULL AFTER responsible_entity,
  ADD COLUMN availability_status VARCHAR(40) DEFAULT 'DISPONIBLE' AFTER observations;

ALTER TABLE indicators CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE INDEX idx_indicators_category_1 ON indicators(category_1);
CREATE INDEX idx_indicators_availability ON indicators(availability_status);
