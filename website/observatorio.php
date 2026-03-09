<?php
$observatories = require __DIR__ . '/config/observatories.php';
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (!array_key_exists($slug, $observatories)) {
    http_response_code(404);
    die('Observatorio no encontrado');
}
$obs = $observatories[$slug];
$economicMode = $slug === 'economico';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($obs['name']) ?> · Red de Observatorios</title>
    <meta name="description" content="Micrositio de <?= htmlspecialchars($obs['name']) ?> con indicadores, noticias, documentos y datasets.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern/microsite-pro.css">
</head>
<body style="--obs-color: <?= htmlspecialchars($obs['color']) ?>; --obs-accent: <?= htmlspecialchars($obs['accent']) ?>;">
<div class="market-strip <?= $economicMode ? 'market-strip--active' : '' ?>" aria-label="Cinta superior de indicadores">
    <div class="market-strip__track" id="marketTickerTrack"></div>
</div>

<header class="obs-header">
    <div class="container d-flex justify-content-between align-items-center py-3 gap-3 flex-wrap">
        <a href="index.php" class="back-link">← Inicio Red</a>
        <nav class="d-flex gap-2 flex-wrap">
            <a href="estado-observatorio.php">Estado de datos</a>
            <a href="<?= htmlspecialchars($obs['legacy_url']) ?>">Vista actual</a>
            <a href="admin/auth/login.php">Administración</a>
        </nav>
    </div>
</header>

<main class="container py-4">
    <section class="hero p-4 mb-4">
        <span class="badge rounded-pill text-bg-light mb-2"><?= htmlspecialchars($obs['name']) ?></span>
        <h1>Micrositio <?= htmlspecialchars($obs['name']) ?></h1>
        <p><?= htmlspecialchars($obs['description']) ?></p>
    </section>

    <?php if ($economicMode): ?>
    <section class="econ-dashboard mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2>Panel de coyuntura económica</h2>
            <small>Maqueta visual tipo dashboard para TRM, tasas, dólar, macro y commodities</small>
        </div>
        <div class="row g-3">
            <div class="col-lg-8">
                <article class="dash-card">
                    <h3>Dólar spot (referencia)</h3>
                    <p class="big">$ <span id="fxMain">3.812,40</span> <span class="up">▲ +0,42%</span></p>
                    <div class="mini-grid">
                        <div><small>Apertura</small><strong>$ 3.790,00</strong></div>
                        <div><small>Máximo</small><strong>$ 3.830,00</strong></div>
                        <div><small>Mínimo</small><strong>$ 3.772,40</strong></div>
                        <div><small>Cierre ant.</small><strong>$ 3.796,50</strong></div>
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <article class="dash-card h-100">
                    <h3>Tasas e inflación</h3>
                    <ul class="kpi-list">
                        <li><span>IPC mensual</span><strong>1,08%</strong></li>
                        <li><span>Tasa intervención</span><strong>10,25%</strong></li>
                        <li><span>Desempleo nacional</span><strong>10,9%</strong></li>
                        <li><span>PIB anual</span><strong>2,3%</strong></li>
                    </ul>
                </article>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>TRM Hoy</h3><p class="big">$ 3.795,55</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Euro</h3><p class="big">$ 4.394,68</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Petróleo Brent</h3><p class="big">US$ 92,40</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Oro</h3><p class="big">US$ 2.153,10</p></article></div>
        </div>
    </section>
    <?php endif; ?>

    <section class="row g-3 mb-4">
        <div class="col-md-4"><article class="base-card"><h2>Indicadores</h2><p id="kpi-total">-</p><small>Disponibles en esta dimensión</small></article></div>
        <div class="col-md-4"><article class="base-card"><h2>Noticias</h2><p>12</p><small>Con flujo editorial</small></article></div>
        <div class="col-md-4"><article class="base-card"><h2>Documentos</h2><p>38</p><small>Con descarga y metadatos</small></article></div>
    </section>

    <section class="row g-3">
        <div class="col-lg-7">
            <article class="content-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2>Noticias importantes</h2>
                    <a href="#">Ver todas</a>
                </div>
                <div class="news-list">
                    <div><strong>Actualización de serie histórica territorial</strong><span>hace 2 días</span></div>
                    <div><strong>Nuevo boletín temático trimestral</strong><span>hace 5 días</span></div>
                    <div><strong>Informe especial por subregión</strong><span>hace 1 semana</span></div>
                    <div><strong>Convocatoria de participación ciudadana</strong><span>hace 2 semanas</span></div>
                </div>
            </article>
        </div>
        <div class="col-lg-5">
            <article class="content-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2>Indicadores destacados</h2>
                    <a href="estado-observatorio.php">Catálogo</a>
                </div>
                <ul id="featuredIndicators" class="list-group"></ul>
            </article>
        </div>
    </section>
</main>

<script>window.OBS_SLUG = <?= json_encode($slug) ?>;</script>
<script src="assets/js/modern/microsite-pro.js" defer></script>
</body>
</html>
