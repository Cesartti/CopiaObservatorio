<?php
include 'include/header.php';
?>
<link rel="stylesheet" href="assets/css/estado-observatorio.css">
<script src="assets/js/estado-observatorio.js" defer></script>

<div id="main-body" class="main-body estado-observatorio">
    <section class="estado-hero container py-4">
        <h1>Estado del Observatorio de Boyacá</h1>
        <p>
            Este panel resume los indicadores disponibles y organiza la información por dimensión,
            para facilitar la explicación a ciudadanía, equipos técnicos y tomadores de decisión.
        </p>
    </section>

    <section class="container mb-4" aria-label="Resumen de indicadores">
        <div class="row g-3" id="summaryCards">
            <div class="col-12 col-md-6 col-xl-3"><article class="estado-card"><h2>Total</h2><p id="totalIndicators">-</p></article></div>
            <div class="col-12 col-md-6 col-xl-3"><article class="estado-card"><h2>Social</h2><p id="totalSocial">-</p></article></div>
            <div class="col-12 col-md-6 col-xl-3"><article class="estado-card"><h2>Económica</h2><p id="totalEconomica">-</p></article></div>
            <div class="col-12 col-md-6 col-xl-3"><article class="estado-card"><h2>Ambiental + Tecnología</h2><p id="totalOtros">-</p></article></div>
        </div>
    </section>

    <section class="container mb-4" aria-label="Filtro de búsqueda">
        <label for="searchInput" class="form-label">Buscar indicador</label>
        <input id="searchInput" class="form-control" type="search" placeholder="Ejemplo: violencia, pobreza, salud" />
        <small class="text-muted">La lista se actualiza automáticamente según su búsqueda.</small>
    </section>

    <section class="container mb-5" aria-label="Listado de indicadores">
        <div id="indicatorsByDimension" class="dimension-grid"></div>
    </section>
</div>

<?php include 'include/footer.php'; ?>
