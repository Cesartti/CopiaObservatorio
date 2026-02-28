<?php 
require_once 'tracker.php';
include 'include/header.php';
?>

<link rel="stylesheet" href="assets/css/IndicadoresTecnologia.css">
<script src="assets/js/IndicadoresTecnologia.js"></script>

<!-- BANNER -->
<div class="banner-tecnologia" style="background-image: url('assets/svg/bg-banner-CTeI.png');">
    <div class="banner-tecnologia-content">
        <h1>Dimensión Tecnología e Innovación</h1>
        <p>
            Promover, analizar y monitorear los indicadores de ciencia, tecnología e innovación que permiten evaluar 
            el desarrollo digital, la adopción tecnológica, el avance en capacidades científicas y el fortalecimiento 
            del ecosistema de innovación en Boyacá.
        </p>
    </div>
</div>

<!-- TABS -->
<div class="container-fluid tecno-tabs-wrapper">
    <ul class="nav nav-tabs">

        <li class="nav-item">
            <button class="nav-link tecno-tab active" data-bs-target="#tablero">
                💻 <span>Tablero</span>
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link tecno-tab" data-bs-target="#hojavida">
                📄 <span>Hoja de Vida</span>
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link tecno-tab" data-bs-target="#categorias">
                📂 <span>Categorías</span>
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link tecno-tab" data-bs-target="#datalake">
                📥 <span>DATALAKE</span>
            </button>
        </li>

    </ul>
</div>

<!-- CONTENIDO DE TABS -->
<div class="container">

    <!-- TABLERO -->
    <div id="tablero" class="tecno-pane" style="display:block;">
        <h3 class="text-center">Tablero de indicadores</h3>
        <iframe class="iframe-full" src="#"></iframe>
    </div>

    <!-- HOJA DE VIDA -->
    <div id="hojavida" class="tecno-pane" style="display:none;">
        <h3 class="text-center">Hoja de Vida de Indicadores</h3>
        <p>Próximamente...</p>
    </div>

    <!-- CATEGORÍAS -->
    <div id="categorias" class="tecno-pane" style="display:none;">
        <h3 class="category-title">Categorías</h3>

        <div class="category-block">
            <h5>Transformación Digital</h5>
            <ul class="icon-list-items">
                <li><a href="#">Acceso a Internet</a></li>
                <li><a href="#">Velocidad de banda ancha</a></li>
            </ul>
        </div>

        <div class="category-block">
            <h5>Ciencia y Tecnología</h5>
            <ul class="icon-list-items">
                <li><a href="#">Investigadores categorizados</a></li>
                <li><a href="#">Grupos de investigación</a></li>
            </ul>
        </div>

    </div>

    <!-- DATALAKE -->
    <div id="datalake" class="tecno-pane" style="display:none;">
        <h3 class="datalake-title">Repositorio de Datos (CSV)</h3>

        <div class="datalake-block">
            <div class="datalake-item">
                <span>Acceso a Internet (CSV)</span>
                <button class="dl-download-btn">Descargar</button>
            </div>
        </div>

    </div>

</div>

<?php include 'include/footer.php'; ?>
