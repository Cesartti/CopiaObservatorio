<?php

/**
 * Configuración del feed de Instagram del inicio.
 *
 * Para mostrar VARIAS publicaciones por hashtag y que se actualice solo, use un
 * widget externo (LightWidget, SnapWidget, Elfsight, EmbedSocial…):
 *   1. Cree una cuenta gratuita en el servicio.
 *   2. Configure un widget de tipo HASHTAG con: #RedDeObservatoriosBoyacá
 *      (o de perfil @secplaneacionboyaca).
 *   3. Copie el CÓDIGO DE INSERCIÓN (un <iframe ...> o un <script ...>) y
 *      péguelo COMPLETO dentro de 'embed_html' (entre los <<<HTML y HTML;).
 *
 * Mientras 'embed_html' esté vacío, el inicio muestra las publicaciones
 * cargadas en el CMS (sección Redes / Instagram) como embeds oficiales.
 */

return [
    // Hashtag oficial de la Red (se muestra y enlaza en la sección).
    'hashtag' => 'RedObservatoriosBoyacá',

    // OPCIÓN A (recomendada para "por hashtag automático"): código de inserción
    // de un widget (LightWidget/SnapWidget/Elfsight) configurado con el hashtag.
    // Mientras esté vacío, se muestran las publicaciones de 'posts' (Opción B).
    //   'embed_html' => <<<HTML
    //   <script src="https://cdn.lightwidget.com/widgets/lightwidget.js"></script>
    //   <iframe src="//lightwidget.com/widgets/XXXXXXXX.html" scrolling="no"
    //           allowtransparency="true" class="lightwidget-widget"
    //           style="width:100%;border:0;overflow:hidden;"></iframe>
    //   HTML,
    'embed_html' => '',

    // OPCIÓN B (manual): URLs de las publicaciones, de la MÁS RECIENTE a la más
    // antigua. Se muestran las primeras 5 en una fila horizontal. Para "rotar",
    // agregue la nueva arriba y quite la última. (Sin auto-actualización: para eso
    // use el widget de la Opción A.)
    'posts' => [
        'https://www.instagram.com/p/DZN8z65IPai/',
        'https://www.instagram.com/p/DYCrMhRRm-I/',
        'https://www.instagram.com/p/DYAPxmCEfCV/',
        'https://www.instagram.com/p/DXr7MQpkdeI/',
    ],
];
