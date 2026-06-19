<?php

/**
 * Configuración del conector de Instagram (Graph API).
 *
 * El token y el ID de cuenta NO se versionan: copie
 * config/instagram.local.php.example a config/instagram.local.php
 * y rellénelo con sus credenciales de Meta (ver scripts/INSTAGRAM_SETUP.md).
 */

$defaults = [
    // Active el conector una vez tenga token válido.
    'enabled'       => false,
    // Token de acceso de larga duración (Graph API).
    'access_token'  => getenv('IG_ACCESS_TOKEN') ?: '',
    // ID numérico de la cuenta de Instagram Business/Creator (no el @usuario).
    'ig_user_id'    => getenv('IG_USER_ID') ?: '',
    // Hashtag a filtrar (sin el #).
    'hashtag'       => 'RedObservatoriosBoyacá',
    // Máximo de publicaciones a mostrar en el home.
    'max_posts'     => 12,
    // Versión de la Graph API.
    'graph_version' => 'v21.0',
];

$local = __DIR__ . '/instagram.local.php';
if (is_readable($local)) {
    $x = require $local;
    if (is_array($x)) {
        $defaults = array_replace($defaults, $x);
    }
}

return $defaults;
