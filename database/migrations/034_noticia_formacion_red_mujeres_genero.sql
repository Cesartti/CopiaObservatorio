-- 034: ciclo de formación de la Red Nacional de Observatorios de Mujeres y Género.
--
-- Lo solicitó la Dirección de Seguimiento y Planeación Territorial para el
-- micrositio del Observatorio de Asuntos de Género. La convocatoria llegó el 19
-- de octubre y la primera sesión fue el 21, de modo que la noticia se publica
-- con las cuatro sesiones que siguen abiertas.
--
-- La portada no reproduce la pieza original, que es una captura del formulario
-- con los logos de los financiadores del proyecto. Se anuncia con la identidad
-- del observatorio y se acredita el origen en el cuerpo.
--
-- Idempotente por slug: volver a aplicarla no duplica la noticia.

SET NAMES utf8mb4;

INSERT INTO news (observatory_id, title, slug, summary, body, source, image_url, published_at, content_status, created_by)
SELECT (SELECT id FROM observatories WHERE slug='genero' LIMIT 1),
'Ciclo de jornadas de formación virtual de la Red Nacional de Observatorios de Mujeres y Género',
'formacion-red-nacional-observatorios-mujeres-genero-2026',
'Cinco sesiones virtuales sobre indicadores de violencias, autonomía económica y trabajo de cuidado, y sobre los instrumentos de gobernanza de la Red Nacional. Las jornadas van de 3:00 a 5:00 p. m. por Google Meet y la inscripción está abierta.',
'<p>La <strong>Red Nacional de Observatorios de Mujeres y Género</strong> convoca a un ciclo de jornadas de formación virtual dirigido a los equipos de los observatorios territoriales. El ciclo aborda la construcción y lectura de indicadores de género y, en su tramo final, los instrumentos de gobernanza de la propia Red.</p>
<p>Las sesiones se realizan <strong>de 3:00 a 5:00 p. m., por Google Meet</strong>. Cada una se inscribe por separado, de modo que es posible participar en las que resulten pertinentes sin asistir a todo el ciclo.</p>
<h3>Programación</h3>
<ul>
<li><strong>21 de octubre de 2026.</strong> Indicadores cuantitativos y cualitativos sobre las violencias contra las mujeres en Colombia.</li>
<li><strong>22 de octubre de 2026.</strong> Indicadores cuantitativos y cualitativos sobre la autonomía económica de las mujeres en Colombia.</li>
<li><strong>23 de octubre de 2026.</strong> Indicadores cuantitativos y cualitativos sobre cuidados y trabajo no remunerado en Colombia.</li>
<li><strong>5 de noviembre de 2026.</strong> Encuentro de retroalimentación de instrumentos de gobernanza de la Red Nacional de Observatorios de Mujeres y Género.</li>
<li><strong>17 de noviembre de 2026.</strong> Jornada virtual de presentación final e intercambio de la Red Nacional de Observatorios de Mujeres y Género.</li>
</ul>
<h3>Inscripción</h3>
<p>La inscripción se diligencia en el formulario dispuesto por la Red, donde se seleccionan las sesiones a las que se desea asistir.</p>
<p><a class="btn btn-primary" href="https://docs.google.com/forms/d/e/1FAIpQLSdIznY_sJqr8dWZ3FUxAbNEZhx7exYaw_QEXWKp3l21BOj4JA/viewform" target="_blank" rel="noopener">Inscribirse en el formulario</a></p>
<h3>Por qué le interesa a Boyacá</h3>
<p>Las tres primeras sesiones tratan precisamente las materias sobre las que el <strong>Observatorio de Asuntos de Género de Boyacá</strong> publica indicadores: violencias contra la mujer, empoderamiento económico y uso del tiempo. Las dos últimas abren la discusión sobre cómo se coordinan los observatorios territoriales con la Red Nacional.</p>
<p class="small text-muted">Esta formación hace parte del proyecto ¡LIBRES!, financiado por la Delegación de la Unión Europea en Colombia y la Agencia Española de Cooperación Internacional para el Desarrollo (AECID), con ejecución del Gobierno de Colombia y Expertise France y el apoyo técnico de la Cooperación Alemana para el Desarrollo (GIZ).</p>',
'Red Nacional de Observatorios de Mujeres y Género',
'uploads/cms/2026/10/formacion-red-observatorios-mujeres-genero.jpg',
'2026-10-21 08:00:00', 'published', NULL
WHERE NOT EXISTS (SELECT 1 FROM news WHERE slug='formacion-red-nacional-observatorios-mujeres-genero-2026');
