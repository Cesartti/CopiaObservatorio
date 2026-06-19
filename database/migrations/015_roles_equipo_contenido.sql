-- 015: Roles para el equipo de contenido y sus permisos por módulo.
--   1 admin_general       -> acceso total (incluye gestión de usuarios)
--   2 editor_observatorio -> todo el contenido excepto usuarios
--   3 editor_noticias     -> solo Noticias (sube/edita noticias)
--   4 editor_datos        -> Tableros/Indicadores (charts) y Pestañas de micrositios (tabs)
--
-- Módulos del CMS: banners, social, contact, news, charts, tabs, rag, users.

INSERT INTO roles (id, code, name) VALUES
  (1, 'admin_general', 'Administrador general'),
  (2, 'editor_observatorio', 'Editor por observatorio'),
  (3, 'editor_noticias', 'Editor de noticias'),
  (4, 'editor_datos', 'Editor de datos e indicadores')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Admin general: todo
INSERT INTO role_permissions (role_id, module, can_read, can_write) VALUES
  (1, 'banners', 1, 1),
  (1, 'social', 1, 1),
  (1, 'contact', 1, 1),
  (1, 'news', 1, 1),
  (1, 'charts', 1, 1),
  (1, 'tabs', 1, 1),
  (1, 'rag', 1, 1),
  (1, 'users', 1, 1),
  -- Editor por observatorio: todo menos usuarios
  (2, 'banners', 1, 1),
  (2, 'social', 1, 1),
  (2, 'contact', 1, 1),
  (2, 'news', 1, 1),
  (2, 'charts', 1, 1),
  (2, 'tabs', 1, 1),
  (2, 'rag', 1, 1),
  (2, 'users', 0, 0),
  -- Editor de noticias: solo noticias
  (3, 'news', 1, 1),
  -- Editor de datos: tableros/indicadores y pestañas de micrositios
  (4, 'charts', 1, 1),
  (4, 'tabs', 1, 1)
ON DUPLICATE KEY UPDATE can_read = VALUES(can_read), can_write = VALUES(can_write);
