-- ============================================================================
-- 013_microsite_sections.sql
-- Pestañas y sub-secciones dinámicas para los micrositios (observatorio.php).
-- Permite gestionar desde el CMS contenido jerárquico (pestañas → chips/
-- acordeones internos) con editor WYSIWYG. Reemplaza progresivamente la lectura
-- de website/data/content.json y la versión estática de indic-genero.php.
-- ============================================================================

CREATE TABLE IF NOT EXISTS cms_microsite_sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  observatory_id SMALLINT NOT NULL,
  parent_id INT NULL,
  section_key VARCHAR(64) NOT NULL,
  title VARCHAR(255) NOT NULL,
  subtitle VARCHAR(512) NULL,
  body_html LONGTEXT NULL,
  layout VARCHAR(32) NOT NULL DEFAULT 'standard',
  icon VARCHAR(64) NULL,
  image_url VARCHAR(512) NULL,
  cta_label VARCHAR(120) NULL,
  cta_url VARCHAR(512) NULL,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_ms_obs_parent_sort (observatory_id, parent_id, sort_order),
  KEY idx_ms_obs_key (observatory_id, section_key),
  KEY idx_ms_parent (parent_id),
  CONSTRAINT fk_ms_observatory FOREIGN KEY (observatory_id) REFERENCES observatories(id) ON DELETE CASCADE
);

-- Layout esperados:
--   'standard'   → render HTML libre (default WYSIWYG)
--   'chips'      → pestaña con sub-secciones tipo chip-group (hijos: chips internos)
--   'accordion'  → sub-secciones como acordeón (hijos: items)
--   'cards'      → sub-secciones como rejilla de mini-cards (hijos: tarjetas)
--   'split'      → bloque con imagen + texto a dos columnas

-- Carpeta de uploads (creada por upload-media.php si no existe):
--   website/uploads/cms/   (PNG/JPG/WebP/SVG, ≤ 5 MB por archivo)
