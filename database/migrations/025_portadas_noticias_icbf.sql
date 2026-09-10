-- 025: Portadas propias para las noticias del ICBF publicadas en el
-- Observatorio de Asuntos de Género (antes usaban el banner genérico).
-- Idempotente: solo actualiza si la noticia sigue con el banner genérico.
SET NAMES utf8mb4;

UPDATE news SET image_url = 'uploads/cms/2026/09/icbf-una-hora-por-la-prevencion.jpg'
WHERE slug = 'icbf-una-hora-por-la-prevencion' AND image_url LIKE '%banner-genero%';

UPDATE news SET image_url = 'uploads/cms/2026/09/icbf-padres-en-tu-trabajo.jpg'
WHERE slug = 'icbf-padres-en-tu-trabajo' AND image_url LIKE '%banner-genero%';

UPDATE news SET image_url = 'uploads/cms/2026/09/icbf-feria-del-buen-trato.jpg'
WHERE slug = 'icbf-feria-del-buen-trato' AND image_url LIKE '%banner-genero%';

UPDATE news SET image_url = 'uploads/cms/2026/09/icbf-llegar-juntos-y-a-tiempo-puerto-boyaca.jpg'
WHERE slug = 'icbf-llegar-juntos-y-a-tiempo-puerto-boyaca' AND image_url LIKE '%banner-genero%';

UPDATE news SET image_url = 'uploads/cms/2026/09/icbf-territorio-embera-puerto-boyaca.jpg'
WHERE slug = 'icbf-territorio-embera-puerto-boyaca' AND image_url LIKE '%banner-genero%';

UPDATE news SET image_url = 'uploads/cms/2026/09/icbf-resultados-estrategia-boyactuar.jpg'
WHERE slug = 'icbf-resultados-estrategia-boyactuar' AND image_url LIKE '%banner-genero%';
