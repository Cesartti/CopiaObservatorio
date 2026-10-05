-- 033: el video con el que la Gobernación presenta el Informe Local Voluntario.
--
-- Es la pieza que acompaña a la noticia del ILV (migración 032): el mismo informe,
-- contado en video. Se publica como noticia propia, con su fecha original, y además
-- se enlaza desde la nota del informe para que las dos queden conectadas.
--
-- Idempotente: el INSERT se salta si ya existe el slug y el UPDATE solo actúa si la
-- noticia del informe todavía no menciona el video.

SET NAMES utf8mb4;

INSERT INTO news (observatory_id, title, slug, summary, body, source, image_url, published_at, content_status, created_by)
SELECT NULL, 'En video: ¿qué es el Informe Local Voluntario de Boyacá?', 'video-informe-local-voluntario-boyaca-ods', 'La Gobernación de Boyacá explica en un video qué es el Informe Local Voluntario (ILV) 2025-2026 y cómo el departamento rinde cuentas de sus avances en los Objetivos de Desarrollo Sostenible.', '<p>La Gobernación de Boyacá publicó un video en el que explica, en lenguaje sencillo, <strong>qué es el Informe Local Voluntario (ILV) 2025-2026</strong> y por qué el departamento decidió presentarlo.</p> <p><a href="https://www.instagram.com/gobboyaca/reel/Dd7PesQpcTB/" target="_blank" rel="noopener"> <img src="uploads/cms/2026/10/gobboyaca-video-ilv-ods.jpg" alt="Video: Boyacá rinde cuentas sobre sus avances en los ODS"></a></p> <p style="text-align:center"> <a class="btn btn-dark" href="https://www.instagram.com/gobboyaca/reel/Dd7PesQpcTB/" target="_blank" rel="noopener"> Ver el video en Instagram</a></p> <p>Con el ILV, Boyacá rinde cuentas sobre sus avances en los Objetivos de Desarrollo Sostenible. Es un documento construido con evidencia, que muestra cómo el departamento avanza en el cumplimiento de la Agenda 2030 y cuáles son las brechas que siguen abiertas.</p> <p>Puede consultar el informe completo y su anexo metodológico en la nota <a href="noticia.php?slug=informe-local-voluntario-boyaca-agenda-2030">«Boyacá presenta su Informe Local Voluntario frente a la Agenda 2030 de las Naciones Unidas»</a>, donde están los dos documentos para descargar.</p> <p><em>Video publicado por la Gobernación de Boyacá (@gobboyaca) el 30 de septiembre de 2026. <a href="https://www.instagram.com/gobboyaca/reel/Dd7PesQpcTB/" target="_blank" rel="noopener">Ver la publicación original en Instagram</a>.</em></p>', 'Gobernación de Boyacá (@gobboyaca)', 'uploads/cms/2026/10/gobboyaca-video-ilv-ods.jpg', '2026-09-30 10:00:00', 'published', NULL
WHERE NOT EXISTS (SELECT 1 FROM news WHERE slug='video-informe-local-voluntario-boyaca-ods');

-- Enlace cruzado desde la noticia del informe hacia el video.
UPDATE news
   SET body = REPLACE(body, '<h3>Descargue los documentos</h3>',
                      CONCAT('<h3>El informe en video</h3> <p>La Gobernación de Boyacá explica en un minuto de qué se trata este informe: <a href="https://www.instagram.com/gobboyaca/reel/Dd7PesQpcTB/" target="_blank" rel="noopener">ver el video en Instagram</a>.</p> ', '<h3>Descargue los documentos</h3>'))
 WHERE slug = 'informe-local-voluntario-boyaca-agenda-2030'
   AND body NOT LIKE '%Dd7PesQpcTB%';
