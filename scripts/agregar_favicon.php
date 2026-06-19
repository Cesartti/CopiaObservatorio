<?php
// Añade el favicon del observatorio a las páginas públicas (website/ raíz) que
// tienen <head> propio y aún no declaran rel="icon". Idempotente.

$dir = dirname(__DIR__) . '/website';
$fav = <<<HTML
<head>
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png">
    <link rel="apple-touch-icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-180x180.png">
HTML;

$cambiados = [];
$omitidos = [];
foreach (array_merge(glob($dir . '/*.php'), glob($dir . '/*.html')) as $f) {
    $html = file_get_contents($f);
    if (strpos($html, '<head>') === false) {
        continue;
    }
    if (stripos($html, 'rel="icon"') !== false || stripos($html, "rel='icon'") !== false) {
        $omitidos[] = basename($f) . ' (ya tiene)';
        continue;
    }
    // Insertar tras la primera etiqueta <head>
    $nuevo = preg_replace('/<head>/', $fav, $html, 1);
    if ($nuevo !== null && $nuevo !== $html) {
        file_put_contents($f, $nuevo);
        $cambiados[] = basename($f);
    }
}

echo "AÑADIDO favicon a " . count($cambiados) . " archivos:\n";
foreach ($cambiados as $c) echo "  + $c\n";
echo "\nOmitidos (ya tenían): " . count($omitidos) . "\n";
foreach ($omitidos as $o) echo "  - $o\n";
