<?php
/**
 * Material de ayuda que se muestra en la página de inicio (video institucional y
 * manual de usuario). Ambos se abren en una ventana emergente (lightbox).
 *
 * Para cambiar el video basta con reemplazar el id de YouTube; para cambiar el
 * manual, subir el nuevo PDF a website/assets/pdf/manuales/ y actualizar la ruta.
 */
return [
    'video' => [
        'youtube_id' => 'Pd--LC26odE',
        'titulo'     => 'Video de presentación de la Red de Observatorios',
        'texto'      => 'Recorrido por el portal en video.',
        'boton'      => 'Ver el video',
    ],
    'manual' => [
        'archivo' => 'assets/pdf/manuales/Manual_Usuario_ROB_2026.pdf',
        'titulo'  => 'Manual de usuario de la Red de Observatorios',
        'texto'   => 'Guía paso a paso del portal.',
        'boton'   => 'Ver el manual',
    ],
];
