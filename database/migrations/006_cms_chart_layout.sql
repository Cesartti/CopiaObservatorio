-- Disposición tipo “canvas” (Power BI) por visual en el tablero público

ALTER TABLE cms_charts
  ADD COLUMN layout_span TINYINT UNSIGNED NOT NULL DEFAULT 12 COMMENT 'Ancho en rejilla 12 cols (3,4,6,8,12)' AFTER sort_order,
  ADD COLUMN tile_height_px SMALLINT UNSIGNED NULL DEFAULT 320 COMMENT 'Alto mínimo del área del gráfico en px' AFTER layout_span;
