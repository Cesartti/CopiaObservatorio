<?php
$observatories = require __DIR__ . '/config/observatories.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Red de Observatorios · Boyacá</title>
    <meta name="description" content="Plataforma unificada de la Red de Observatorios de Boyacá: económico, social, ambiente, CTI y género.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern/red-home.css">
</head>
<body>
<header class="topbar">
    <div class="container py-3 d-flex justify-content-between align-items-center">
        <h1 class="m-0">Red de Observatorios</h1>
        <nav class="d-flex gap-3 align-items-center">
            <a href="index.php">Portal actual</a>
            <a href="estado-observatorio.php">Estado de datos</a>
            <a class="btn btn-sm btn-primary" href="admin/auth/login.php">Ingreso admin</a>
        </nav>
    </div>
</header>

<main>
    <section class="hero container py-5">
        <p class="eyebrow">Plataforma institucional moderna</p>
        <h2>Un ecosistema, cinco observatorios especializados</h2>
        <p>Acceda a indicadores, noticias, documentos y datasets con una experiencia unificada y navegación temática.</p>
        <div class="searchbox mt-3">
            <label for="globalSearch" class="form-label">Buscador global</label>
            <div class="input-group">
                <input id="globalSearch" type="search" class="form-control" placeholder="Buscar indicador, tema o palabra clave">
                <button class="btn btn-dark" id="searchButton">Buscar</button>
            </div>
        </div>
        <div id="searchResults" class="search-results mt-3" aria-live="polite"></div>
    </section>

    <section class="container pb-5">
        <div class="row g-4">
            <?php foreach ($observatories as $slug => $obs): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <article class="obs-card" style="--obs-color: <?= htmlspecialchars($obs['color']) ?>; --obs-accent: <?= htmlspecialchars($obs['accent']) ?>;">
                        <span class="obs-icon"><i class="fa-solid <?= htmlspecialchars($obs['icon']) ?>"></i></span>
                        <h3><?= htmlspecialchars($obs['name']) ?></h3>
                        <p><?= htmlspecialchars($obs['description']) ?></p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a class="btn btn-sm btn-dark" href="observatorio.php?slug=<?= urlencode($slug) ?>">Entrar micrositio</a>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($obs['legacy_url']) ?>">Vista actual</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<script src="assets/js/modern/red-home.js" defer></script>
</body>
</html>
