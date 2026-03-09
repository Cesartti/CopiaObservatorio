<?php
$observatories = require __DIR__ . '/config/observatories.php';
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (!array_key_exists($slug, $observatories)) {
    http_response_code(404);
    die('Observatorio no encontrado');
}
$obs = $observatories[$slug];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($obs['name']) ?> · Red de Observatorios</title>
    <meta name="description" content="Micrositio de <?= htmlspecialchars($obs['name']) ?> con indicadores, actualidad y repositorio documental.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern/observatorio.css">
</head>
<body style="--obs-color: <?= htmlspecialchars($obs['color']) ?>; --obs-accent: <?= htmlspecialchars($obs['accent']) ?>;">
<header class="obs-header">
    <div class="container py-3 d-flex justify-content-between align-items-center">
        <a href="red-home.php" class="back">← Red de Observatorios</a>
        <a class="btn btn-sm btn-outline-light" href="<?= htmlspecialchars($obs['legacy_url']) ?>">Ir a vista actual</a>
    </div>
</header>

<main class="container py-4">
    <section class="obs-hero p-4 mb-4">
        <span class="badge rounded-pill text-bg-light mb-2"><?= htmlspecialchars($slug) ?></span>
        <h1><?= htmlspecialchars($obs['name']) ?></h1>
        <p><?= htmlspecialchars($obs['description']) ?></p>
    </section>

    <section class="row g-3 mb-4" aria-label="Cifras clave">
        <div class="col-12 col-md-4"><article class="kpi"><h2>Cifras clave</h2><p id="kpi-total">-</p><small>Indicadores en catálogo</small></article></div>
        <div class="col-12 col-md-4"><article class="kpi"><h2>Actualización</h2><p>Dinámica</p><small>Preparado para ETL/API</small></article></div>
        <div class="col-12 col-md-4"><article class="kpi"><h2>Fuentes</h2><p>Multi-fuente</p><small>Rastreo y trazabilidad</small></article></div>
    </section>

    <section class="card-block mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Indicadores destacados</h2>
            <a href="estado-observatorio.php">Ver catálogo completo</a>
        </div>
        <ul id="featuredIndicators" class="list-group"></ul>
    </section>

    <section class="row g-3">
        <div class="col-12 col-lg-6">
            <article class="card-block h-100">
                <h2>Noticias y actualidad</h2>
                <p>Bloque preparado para CMS editorial con estados de aprobación.</p>
                <ul>
                    <li>Destacado principal por observatorio</li>
                    <li>Noticias relacionadas por tema</li>
                    <li>Boletines y reportes recientes</li>
                </ul>
            </article>
        </div>
        <div class="col-12 col-lg-6">
            <article class="card-block h-100">
                <h2>Documentos y datasets</h2>
                <p>Repositorio versionado con filtros por categoría, fecha y tipo.</p>
                <ul>
                    <li>PDF, XLSX, CSV, fichas y anexos</li>
                    <li>Trazabilidad de carga y responsable</li>
                    <li>Descargas con metadatos</li>
                </ul>
            </article>
        </div>
    </section>
</main>

<script>
window.OBS_SLUG = <?= json_encode($slug) ?>;
</script>
<script src="assets/js/modern/observatorio.js" defer></script>
</body>
</html>
