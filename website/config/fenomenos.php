<?php

/**
 * Configuración de la pestaña "Fenómenos en Boyacá".
 *
 * NASA FIRMS (focos de calor satelitales en tiempo casi real)
 * ----------------------------------------------------------
 * La clave es gratuita e inmediata. Para obtenerla:
 *   1. Entrar a https://firms.modaps.eosdis.nasa.gov/api/area/
 *   2. Pulsar "Get MAP_KEY" y registrar un correo institucional.
 *   3. Copiar la clave que llega por correo.
 *
 * La clave NO se versiona: se pone en config/fenomenos.local.php, que está
 * excluido del repositorio, con el mismo formato de este archivo:
 *
 *   <?php return ['firms_map_key' => 'la-clave-que-envio-la-NASA'];
 *
 * Mientras no haya clave, el mapa usa los eventos abiertos de NASA EONET,
 * que son públicos y no requieren registro, además de todo el histórico
 * local de emergencias.
 */

$config = [
    'firms_map_key' => '',
];

$local = __DIR__ . '/fenomenos.local.php';
if (is_file($local)) {
    $extra = include $local;
    if (is_array($extra)) {
        $config = array_merge($config, $extra);
    }
}

return $config;
