<?php
$observatories = require __DIR__ . '/config/observatories.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Red de Observatorios de Boyacá</title>
    <meta name="description" content="Portal oficial de la Red de Observatorios de Boyacá con indicadores, noticias, documentos y datasets.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern/portal-pro.css">
</head>
<body>
<header class="portal-header">
    <div class="container d-flex flex-wrap justify-content-between align-items-center py-3 gap-3">
        <div>
            <p class="m-0 small text-uppercase">Gobernación de Boyacá</p>
            <h1 class="m-0">Red de Observatorios</h1>
        </div>
        <nav class="portal-nav d-flex flex-wrap gap-2">
            <a href="index.php" class="active">Inicio</a>
            <a href="estado-observatorio.php">Estado de datos</a>
            <a href="red-home.php">Vista red</a>
            <a href="admin/auth/login.php">Administración</a>
        </nav>
    </div>
</header>

<main>
    <section class="hero container py-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <span class="chip">Plataforma de datos públicos y análisis territorial</span>
                <h2 class="mt-3">Una experiencia única: 5 observatorios especializados, una sola navegación</h2>
                <p>
                    Explore indicadores, noticias, documentos y datasets con enfoque ciudadano, técnico y administrativo.
                    Diseño moderno, accesible y preparado para actualizaciones automatizadas.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-dark" href="#observatorios">Explorar observatorios</a>
                    <a class="btn btn-outline-dark" href="admin/auth/login.php">Panel administrativo</a>
                </div>
            </div>
            <div class="col-lg-5">
                <article class="quick-panel">
                    <h3>Búsqueda global</h3>
                    <label class="form-label" for="globalSearch">Buscar por indicador, tema o palabra</label>
                    <div class="input-group">
                        <input id="globalSearch" type="search" class="form-control" placeholder="Ej: inflación, pobreza, calidad del aire">
                        <button class="btn btn-primary" id="searchButton">Buscar</button>
                    </div>
                    <div id="searchResults" class="search-results mt-3" aria-live="polite"></div>
                </article>
            </div>
        </div>
    </section>

    <section id="observatorios" class="container py-4">
        <div class="section-head">
            <h2>Micrositios por observatorio</h2>
            <p>Identidad visual propia por dimensión, pero en un ecosistema unificado.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($observatories as $slug => $obs): ?>
                <div class="col-md-6 col-xl-4">
                    <article class="obs-card" style="--obs-color: <?= htmlspecialchars($obs['color']) ?>; --obs-accent: <?= htmlspecialchars($obs['accent']) ?>;">
                        <div class="obs-card__icon"><i class="fa-solid <?= htmlspecialchars($obs['icon']) ?>"></i></div>
                        <h3><?= htmlspecialchars($obs['name']) ?></h3>
                        <p><?= htmlspecialchars($obs['description']) ?></p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a class="btn btn-sm btn-dark" href="observatorio.php?slug=<?= urlencode($slug) ?>">Entrar</a>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($obs['legacy_url']) ?>">Vista actual</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="news-band py-5">
        <div class="container">
            <div class="section-head text-white">
                <h2>Actualidad y contenidos destacados</h2>
                <p>Bloques editoriales para informar con claridad a ciudadanía, academia y tomadores de decisión.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-6 col-xl-3"><article class="news-card"><span>Boletín</span><h3>Panorama de empleo y formalidad</h3><p>Lectura rápida con cifras territoriales y tendencias.</p></article></div>
                <div class="col-md-6 col-xl-3"><article class="news-card"><span>Noticia</span><h3>Calidad del aire: corte trimestral</h3><p>Actualización de estaciones y variación anual comparada.</p></article></div>
                <div class="col-md-6 col-xl-3"><article class="news-card"><span>Informe</span><h3>Brechas de género en participación</h3><p>Reporte para política pública y enfoque diferencial.</p></article></div>
                <div class="col-md-6 col-xl-3"><article class="news-card"><span>Dataset</span><h3>Series históricas abiertas</h3><p>Descarga CSV/XLSX con metadatos y fuente.</p></article></div>
            </div>
        </div>
    </section>
</main>

<script src="assets/js/modern/portal-pro.js" defer></script>
</body>
</html>
