<?php
/**
 * Encabezado compartido con el estilo del microsite (obs-header) para páginas
 * públicas como noticias y publicaciones.
 *
 * Variables opcionales a definir antes de incluir:
 *   $pageTitle       Título de la pestaña del navegador.
 *   $pageDescription Meta descripción.
 *   $themeColor      Color principal (barra superior); por defecto azul de la Red.
 *   $themeAccent     Color de acento.
 */
$pageTitle = isset($pageTitle) && $pageTitle !== '' ? $pageTitle : 'Red de Observatorios de Boyacá';
$pageDescription = $pageDescription ?? '';
$themeColor = isset($themeColor) && $themeColor !== '' ? $themeColor : '#0b2744';
$themeAccent = isset($themeAccent) && $themeAccent !== '' ? $themeAccent : '#23a9b8';

if (!function_exists('site_hex_to_rgb')) {
    function site_hex_to_rgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return '13,110,253';
        }

        return implode(',', [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))]);
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?php if ($pageDescription !== ''): ?>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/modern/microsite-pro.css">
    <link rel="icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png" sizes="192x192">
</head>
<body class="observatorio-page" style="--obs-color: <?= htmlspecialchars($themeColor) ?>; --obs-accent: <?= htmlspecialchars($themeAccent) ?>; --obs-tab-active: <?= htmlspecialchars($themeColor) ?>; --obs-color-rgb: <?= htmlspecialchars(site_hex_to_rgb($themeColor)) ?>; --obs-accent-rgb: <?= htmlspecialchars(site_hex_to_rgb($themeAccent)) ?>;">

<header class="obs-header">
    <div class="container d-flex justify-content-between align-items-center py-3 gap-3 flex-wrap">
        <a href="index.php" class="back-link d-flex align-items-center gap-2"><img src="assets/svg/logo.svg" alt="Logo Red de Observatorios" class="brand-logo"> <span>Inicio Red</span></a>
        <nav class="d-flex gap-2 flex-wrap ms-md-auto">
            <a href="index.php">Inicio</a>
            <a href="nosotros.php">Nosotros</a>
            <a href="noticias.php">Noticias</a>
            <a href="boletines.php">Boletines</a>
            <a href="publicaciones.php">Publicaciones</a>
            <a href="estado-observatorio.php">Estado de datos</a>
        </nav>
    </div>
</header>
