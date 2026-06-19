<?php

/**
 * Publicaciones de infografías por observatorio (pestaña "Infografías").
 *
 * Cada publicación es una carpeta dentro de assets/infografias/ cuyo nombre
 * coincide con 'folder'; sus imágenes se listan en orden natural y la primera
 * actúa como portada del carrusel.
 *
 *  - 'obs'   : slugs de observatorios donde aparece; ['*'] = todos.
 *  - 'date'  : fecha de publicación (orden descendente en la galería).
 *  - 'note'  : nota descriptiva que acompaña la publicación en el lightbox.
 */
return [
    [
        'folder' => 'presentacion-red',
        'title' => 'Red de Observatorios de Boyacá',
        'note' => 'Conozca la Red de Observatorios de Boyacá: la alianza entre la Secretaría de Planeación y las universidades que convierte los datos del territorio en decisiones, y sus cinco dimensiones de análisis (económica, social, ambiental, CTeI y asuntos de género).',
        'obs' => ['*'],
        'date' => '2025-11-26',
    ],
    [
        'folder' => 'capsulas-informativas',
        'title' => 'Cápsulas informativas 2025',
        'note' => 'Serie "Todo lo que sumercé debe saber": cápsulas con cifras clave de Boyacá sobre violencia por presuntos delitos sexuales, salud y otros temas de interés ciudadano.',
        'obs' => ['social'],
        'date' => '2025-12-02',
    ],
    [
        'folder' => 'mujer-y-ciencia',
        'title' => 'Día Internacional de la Mujer y la Niña en la Ciencia',
        'note' => 'Conmemoración del 11 de febrero: la participación de las mujeres y las niñas boyacenses en la ciencia, la tecnología y la investigación.',
        'obs' => ['genero'],
        'date' => '2026-02-11',
    ],
    [
        'folder' => 'dia-contra-el-cancer',
        'title' => 'Día Mundial contra el Cáncer',
        'note' => 'Cifras y datos de Boyacá en la conmemoración del Día Mundial contra el Cáncer (4 de febrero).',
        'obs' => ['social'],
        'date' => '2026-02-14',
    ],
    [
        'folder' => 'dia-de-la-mujer',
        'title' => 'Día Internacional de la Mujer',
        'note' => 'Conmemoración del 8 de marzo: datos sobre la situación de las mujeres en el departamento de Boyacá.',
        'obs' => ['genero'],
        'date' => '2026-03-08',
    ],
    [
        'folder' => 'discriminacion-racial',
        'title' => 'Día Internacional de la Eliminación de la Discriminación Racial',
        'note' => 'Conmemoración del 21 de marzo: información sobre la eliminación de la discriminación racial en Boyacá.',
        'obs' => ['social'],
        'date' => '2026-03-21',
    ],
    [
        'folder' => 'salud-y-nutricion',
        'title' => 'Salud y nutrición',
        'note' => 'Indicadores de salud y nutrición de la población boyacense en el marco del Día Mundial de la Salud.',
        'obs' => ['social'],
        'date' => '2026-04-07',
    ],
    [
        'folder' => 'chagas',
        'title' => 'Enfermedad de Chagas',
        'note' => 'Datos de vigilancia epidemiológica de la enfermedad de Chagas en el departamento de Boyacá.',
        'obs' => ['social'],
        'date' => '2026-04-13',
    ],
    [
        'folder' => 'semana-de-la-vacunacion',
        'title' => 'Semana de la Vacunación de las Américas',
        'note' => 'Cobertura y cifras de vacunación en Boyacá durante la Semana de la Vacunación de las Américas.',
        'obs' => ['social'],
        'date' => '2026-04-27',
    ],
    [
        'folder' => 'asuntos-de-genero',
        'title' => 'Observatorio de Asuntos de Género de Boyacá',
        'note' => 'Presentación del Observatorio de Asuntos de Género: una herramienta dinámica para compartir estudios, análisis e investigaciones que visibilizan las realidades del territorio boyacense.',
        'obs' => ['genero'],
        'date' => '2026-05-06',
    ],
    [
        'folder' => 'violencias',
        'title' => 'Violencia de género en cifras',
        'note' => 'Así está Boyacá en cifras 2025: indicadores clave de violencia de género, atenciones médicas por violencias y municipios con mayor presencia en los indicadores.',
        'obs' => ['genero'],
        'date' => '2026-05-27',
    ],
    [
        'folder' => 'embarazo-adolescente',
        'title' => 'Embarazo en adolescentes',
        'note' => 'La tasa de embarazo adolescente disminuyó en Boyacá en 2025: un avance en las acciones de promoción de derechos sexuales y reproductivos en población joven.',
        'obs' => ['social'],
        'date' => '2026-06-02',
    ],
];
