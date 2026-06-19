-- 014: Permitir noticias generales (observatory_id NULL = noticia general de la Red,
-- no asociada a un observatorio en particular).
ALTER TABLE news MODIFY observatory_id SMALLINT NULL;
