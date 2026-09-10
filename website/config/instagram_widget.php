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
    // NOTA 2026-08-10: LightWidget gratis NO funciona en sitios HTTPS (muestra
    // "Widget add-on required"). El observatorio es HTTPS, así que el widget
    // gratis queda descartado. Se dejó vacío para usar la Opción B (lista manual)
    // mientras se activa el feed automático por la API Graph de Meta
    // (scripts/sync_instagram.php). El upgrade de LightWidget (US$15) habilitaría
    // HTTPS; el widget creado era: c9bff49d0b405e5a988d6fd3de6e5e8f
    'embed_html' => '',

    // OPCIÓN B (manual): URLs de las publicaciones, de la MÁS RECIENTE a la más
    // antigua. El inicio muestra las primeras 4. Para rotar, agregue la nueva
    // arriba. (Sin auto-actualización: para eso hace falta la Opción A o la API
    // Graph de Meta, scripts/sync_instagram.php.)
    //
    // Revisión del perfil @secplaneacionboyaca del 10/09/2026: se recorrieron
    // las publicaciones recientes y se dejaron las de la Red de Observatorios,
    // sea por el hashtag #RedObservatoriosBoyacá o porque remiten al portal.
    // El perfil público solo deja ver las últimas ~12 publicaciones sin iniciar
    // sesión, así que la revisión abarcó ese tramo.
    'posts' => [
        // 3 indicadores de calidad de vida de la juventud → remite al portal
        'https://www.instagram.com/p/Dclk6Y2EVYi/',
        // «5 datos que hacen grande a Boyacá» → #RedObservatoriosBoyacá
        'https://www.instagram.com/p/DbzG517gHIX/',
        // Embarazo adolescente 2025 → #RedObservatoriosBoyacá
        'https://www.instagram.com/p/DZN8z65IPai/',
        // Observatorio de Asuntos de Género → #RedObservatoriosBoyacá
        'https://www.instagram.com/p/DYCrMhRRm-I/',
        // Estudios y análisis del Observatorio de Género → #RedObservatoriosBoyacá
        'https://www.instagram.com/p/DYAPxmCEfCV/',
        // Jornada Nacional de Vacunación → #RedObservatoriosBoyacá
        'https://www.instagram.com/p/DXr7MQpkdeI/',
    ],
];
