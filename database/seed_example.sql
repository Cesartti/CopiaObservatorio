-- Datos de ejemplo: roles, observatorios, usuario administrador (contraseña: Admin123*)
-- Ajuste correos y hash si integra autenticación contra MySQL.
-- El sitio actual usa autenticación demo en website/admin/auth/login.php.

INSERT IGNORE INTO roles (id, code, name) VALUES
  (1, 'admin_general', 'Administrador general'),
  (2, 'editor_observatorio', 'Editor por observatorio');

INSERT IGNORE INTO observatories (id, slug, name, theme_color, accent_color, status) VALUES
  (1, 'economico', 'Observatorio Económico', '#0f3557', '#d8a21d', 'active'),
  (2, 'social', 'Observatorio Social', '#5f2a8a', '#d74fa6', 'active'),
  (3, 'ambiente', 'Observatorio de Medio Ambiente', '#1f6b45', '#23a9b8', 'active'),
  (4, 'cti', 'Observatorio CTI', '#1847b7', '#6d4aff', 'active'),
  (5, 'genero', 'Observatorio de Asuntos de Género', '#7d2d91', '#ef6f8f', 'active');

-- Hash bcrypt para la contraseña Admin123* (generado con password_hash de PHP)
INSERT INTO users (full_name, email, password_hash, role_id, is_active)
VALUES (
  'Administrador plataforma',
  'admin@observatorio.gov.co',
  '$2y$10$idP6EkC543BlVqE.aQWgb.eNZqA.QF02xFNktKV0SG/SKLSj9WuNC',
  1,
  1
) ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- Ejemplo de etiquetas por observatorio (códigos estables para lógica de publicación)
INSERT IGNORE INTO tags (observatory_id, code, label, placement_hint) VALUES
  (1, 'noticias_portada', 'Noticias · portada general', 'noticias_portada'),
  (1, 'pub_universidades', 'Publicaciones universidades', 'pub_universidades'),
  (1, 'infografias', 'Infografías', 'infografias');
