<?php

/**
 * Publicaciones académicas de universidades aliadas (pestaña "Publicaciones
 * universidades" del micrositio).
 *
 *  - 'obs'   : slugs de observatorios donde aparece; ['*'] = todos.
 *  - 'type'  : Artículo | Informe | Tesis | Boletín | Dataset
 *  - 'line'  : línea temática o categoría del observatorio a la que pertenece
 *              (las mismas líneas de config/observatory_categories.php); el
 *              filtro "Línea temática" se genera con las líneas presentes.
 *  - 'demo'  : true marca el registro como contenido de ejemplo (se muestra
 *              con un aviso). Elimine los demo al cargar publicaciones reales.
 *
 * Las universidades se definen aquí para que los filtros se generen solos.
 */
return [
    'universities' => [
        'uptc' => ['name' => 'UPTC', 'full' => 'Universidad Pedagógica y Tecnológica de Colombia', 'color' => '#f2a900'],
        'uniboyaca' => ['name' => 'U. de Boyacá', 'full' => 'Universidad de Boyacá', 'color' => '#00529b'],
        'santoto' => ['name' => 'Santo Tomás', 'full' => 'Universidad Santo Tomás — Tunja', 'color' => '#1d3c34'],
        'juandecastellanos' => ['name' => 'Juan de Castellanos', 'full' => 'Fundación Universitaria Juan de Castellanos', 'color' => '#7a1f2b'],
    ],
    'items' => [
        [
            'title' => 'Dinámica del empleo formal en Boyacá: un análisis territorial 2020–2025',
            'authors' => 'Grupo de investigación OIKOS',
            'university' => 'uptc',
            'type' => 'Artículo',
            'year' => 2026,
            'summary' => 'Analiza la evolución del empleo formal por provincias y ramas de actividad, con énfasis en los efectos de la reactivación económica.',
            'url' => '#',
            'line' => 'Variables Macroeconómicas',
            'obs' => ['economico'],
            'demo' => true,
        ],
        [
            'title' => 'Boletín de coyuntura agroindustrial del altiplano cundiboyacense',
            'authors' => 'Facultad de Ciencias Económicas y Administrativas',
            'university' => 'uniboyaca',
            'type' => 'Boletín',
            'year' => 2025,
            'summary' => 'Seguimiento trimestral a precios, producción y comercialización de los principales productos agroindustriales del departamento.',
            'url' => '#',
            'line' => 'Agropecuario',
            'obs' => ['economico'],
            'demo' => true,
        ],
        [
            'title' => 'Determinantes de la pobreza multidimensional en municipios rurales de Boyacá',
            'authors' => 'Semillero de investigación en desarrollo social',
            'university' => 'uptc',
            'type' => 'Tesis',
            'year' => 2025,
            'summary' => 'Tesis de maestría que identifica los factores asociados a la incidencia de pobreza multidimensional con datos del Censo 2018 y registros administrativos.',
            'url' => '#',
            'line' => 'Familia',
            'obs' => ['social'],
            'demo' => true,
        ],
        [
            'title' => 'Informe de caracterización de la población migrante en Tunja',
            'authors' => 'Observatorio socioeconómico regional',
            'university' => 'santoto',
            'type' => 'Informe',
            'year' => 2026,
            'summary' => 'Caracterización demográfica y de acceso a servicios de la población migrante asentada en la capital del departamento.',
            'url' => '#',
            'line' => 'Demografía',
            'obs' => ['social'],
            'demo' => true,
        ],
        [
            'title' => 'Calidad del agua en la cuenca alta del río Chicamocha: monitoreo participativo',
            'authors' => 'Grupo de estudios ambientales',
            'university' => 'uptc',
            'type' => 'Artículo',
            'year' => 2026,
            'summary' => 'Resultados del monitoreo fisicoquímico y microbiológico de la cuenca alta con participación de juntas de acueductos veredales.',
            'url' => '#',
            'line' => 'Agua',
            'obs' => ['ambiente'],
            'demo' => true,
        ],
        [
            'title' => 'Inventario de emisiones de gases de efecto invernadero del sector ladrillero',
            'authors' => 'Facultad de Ingeniería Ambiental',
            'university' => 'uniboyaca',
            'type' => 'Informe',
            'year' => 2025,
            'summary' => 'Estimación de emisiones del corredor industrial de Sogamoso y escenarios de reconversión tecnológica.',
            'url' => '#',
            'line' => 'Aire',
            'obs' => ['ambiente'],
            'demo' => true,
        ],
        [
            'title' => 'Capacidades de I+D+i en Boyacá: mapeo de grupos y semilleros 2025',
            'authors' => 'Vicerrectoría de Investigación',
            'university' => 'uptc',
            'type' => 'Dataset',
            'year' => 2025,
            'summary' => 'Base de datos abierta con los grupos de investigación, semilleros y líneas activas reportadas en el departamento.',
            'url' => '#',
            'line' => 'Capacidades de I+D',
            'obs' => ['cti'],
            'demo' => true,
        ],
        [
            'title' => 'Apropiación social del conocimiento en instituciones educativas rurales',
            'authors' => 'Grupo de investigación en educación y TIC',
            'university' => 'juandecastellanos',
            'type' => 'Artículo',
            'year' => 2026,
            'summary' => 'Experiencias de apropiación social de CTeI en sedes educativas rurales de las provincias Centro y Márquez.',
            'url' => '#',
            'line' => 'Apropiación social del conocimiento',
            'obs' => ['cti'],
            'demo' => true,
        ],
        [
            'title' => 'Brechas salariales de género en el mercado laboral boyacense',
            'authors' => 'Grupo de investigación en economía y género',
            'university' => 'uptc',
            'type' => 'Artículo',
            'year' => 2026,
            'summary' => 'Descomposición de la brecha salarial por niveles educativos y sectores, con microdatos de la GEIH.',
            'url' => '#',
            'line' => 'Empoderamiento económico',
            'obs' => ['genero', 'economico'],
            'demo' => true,
        ],
        [
            'title' => 'Informe sobre violencias basadas en género en el ámbito universitario',
            'authors' => 'Comité de equidad de género',
            'university' => 'santoto',
            'type' => 'Informe',
            'year' => 2025,
            'summary' => 'Diagnóstico de prevalencia, rutas de atención y recomendaciones de política institucional.',
            'url' => '#',
            'line' => 'Violencias contra la mujer',
            'obs' => ['genero'],
            'demo' => true,
        ],
    ],
];
