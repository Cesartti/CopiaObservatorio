<?php
require_once 'tracker.php';
include 'include/header.php';
?>

<!-- CSS ESPECÍFICO -->
<link rel="stylesheet" href="assets/css/IndicadoresAmbiental.css">

<!-- JS ESPECÍFICO -->
<script src="assets/js/IndicadoresAmbiental.js"></script>

<div id="main-body" class="main-body">

    <!-- BANNER SUPERIOR -->
    <section class="banner-ambiental-container">
        <img src="assets/svg/bg-banner-ambiental.png" class="banner-ambiental-img">
        <div class="banner-ambiental-overlay"></div>

        <div class="banner-ambiental-text">
            <h1 class="banner-ambiental-title">Dimensión Ambiental</h1>
            <p class="banner-ambiental-desc">
                Proporcionar información de indicadores relevantes que muestran análisis pertinentes 
                de las diferentes categorías ambientales, con el fin de tomar decisiones y salvaguardar 
                los recursos naturales y garantizar un desarrollo sostenible y sustentable en Boyacá.
            </p>
        </div>
    </section>

</div>

<!-- ===========================
     SISTEMA DE PESTAÑAS AMBIENTAL
=========================== -->
<div class="container-fluid mt-4 ambiental-tabs-wrapper">

    <ul class="nav nav-tabs justify-content-center" id="ambientalTabs" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link ambiental-tab active" data-bs-toggle="tab" data-bs-target="#tablero" type="button">
                📊 <span>Tablero</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link ambiental-tab" data-bs-toggle="tab" data-bs-target="#hojavida" type="button">
                📄 <span>Hoja de Vida</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link ambiental-tab" data-bs-toggle="tab" data-bs-target="#categorias" type="button">
                📂 <span>Categorías</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link ambiental-tab" data-bs-toggle="tab" data-bs-target="#datalake" type="button">
                📥 <span>DATALAKE</span>
            </button>
        </li>

    </ul>

    <div class="tab-content mt-4">

        <!-- ================= TAB 1 — TABLERO ================= -->
        <div class="tab-pane fade show active ambiental-pane" id="tablero">
            <iframe 
                class="iframe-full"
                src="#"
                allowfullscreen>
            </iframe>
        </div>

        <!-- ================= TAB 2 — HOJA DE VIDA ================= -->
        <div class="tab-pane fade ambiental-pane" id="hojavida">

            <h3 class="hv-title">Ficha técnica del indicador</h3>

            <select id="indicatorSelect" class="form-select mb-3" onchange="showIndicatorInfo()">
                <option value="">Seleccione un indicador</option>

                <option value="3002">Servicio de acueducto urbano</option>
                <option value="3003">Sistema de alcantarillado zona urbana</option>
                <option value="3004">Aseo en la zona urbana</option>
                <option value="3001">Edificaciones con ahorro energético</option>
                <option value="3008">Tratamiento de aguas residuales urbano</option>
                <option value="3005">Continuidad del servicio de acueducto (horas)</option>
                <option value="3007">Continuidad del servicio (días por semana)</option>
                <option value="3006">Índice de riesgo de calidad del agua</option>
                <option value="3017">Calidad del aire PM10 Nobsa</option>
                <option value="3018">Calidad del aire PM2.5 Nobsa</option>
                <option value="3019">SO2 Nobsa</option>
                <option value="3020">Ozono O3 Nobsa</option>
                <option value="3021">PM10 Nazareth</option>
                <option value="3022">PM2.5 Nazareth</option>
                <option value="3023">SO2 Nazareth</option>
                <option value="3024">O3 Nazareth</option>
                <option value="3025">PM10 Paipa</option>
                <option value="3026">SO2 Paipa</option>
                <option value="3027">PM10 Ráquira</option>
                <option value="3028">PM2.5 Ráquira</option>
                <option value="3029">PM10 urbano Ráquira</option>
                <option value="3030">PM2.5 urbano Ráquira</option>
                <option value="3031">SO2 urbano Ráquira</option>
                <option value="3032">PM10 Sogamoso Koika</option>
                <option value="3033">PM2.5 Sogamoso Koika</option>
                <option value="3034">SO2 Sogamoso Koika</option>
                <option value="3035">O3 Sogamoso Koika</option>
                <option value="3036">NO2 Sogamoso Koika</option>
                <option value="3037">CO Sogamoso Koika</option>
                <option value="3038">PM10 Sogamoso Recreo</option>
                <option value="3039">PM2.5 Sogamoso Recreo</option>
                <option value="3040">SO2 Sogamoso Recreo</option>
                <option value="3041">O3 Sogamoso Recreo</option>
                <option value="3042">NO2 Sogamoso Recreo</option>
                <option value="3043">CO Sogamoso Recreo</option>
                <option value="3044">PM10 Sogamoso SENA</option>
                <option value="3045">SO2 Sogamoso SENA</option>
                <option value="3046">O3 Sogamoso SENA</option>
                <option value="3047">PM10 UPTC</option>
            </select>

            <div id="indicatorInfo" class="hv-data-box">
                <p class="text-muted">Seleccione un indicador para ver su ficha técnica.</p>
            </div>

        </div>

        <!-- ================= TAB 3 — CATEGORÍAS ================= -->
        <div class="tab-pane fade ambiental-pane" id="categorias">

            <h2 class="cat-title">Categorías Ambientales</h2>

            <!-- ACORDEÓN -->
            <div class="accordion-container">

                <!-- 1. Servicios Públicos -->
                <button class="accordion-button" onclick="toggleAccordion(this)">Servicios Públicos</button>
                <div class="accordion-content">
                    <ul class="icon-list-items">
                        <li class="indicador-subtitle">Acueducto</li>
                        <li><a href="./indicador.php?id=3002">Servicio de acueducto urbano</a></li>

                        <li class="indicador-subtitle">Alcantarillado</li>
                        <li><a href="./indicador.php?id=3003">Sistema de alcantarillado urbano</a></li>

                        <li class="indicador-subtitle">Aseo</li>
                        <li><a href="./indicador.php?id=3004">Aseo en la zona urbana</a></li>
                    </ul>
                </div>

                <!-- 2. Recursos Naturales -->
                <button class="accordion-button" onclick="toggleAccordion(this)">Recursos Naturales</button>
                <div class="accordion-content">
                    <ul class="icon-list-items">
                        <li class="indicador-subtitle">Agua</li>
                        <li><a href="./indicador.php?id=3005">Continuidad del servicio (horas)</a></li>
                        <li><a href="./indicador.php?id=3007">Continuidad del servicio (días)</a></li>
                        <li><a href="./indicador.php?id=3006">Índice de riesgo del agua</a></li>

                        <li class="indicador-subtitle">Aire</li>
                        <li><a href="./indicador.php?id=3017">PM10 Nobsa</a></li>
                        <li><a href="./indicador.php?id=3018">PM2.5 Nobsa</a></li>
                        <li><a href="./indicador.php?id=3019">SO2 Nobsa</a></li>
                        <li><a href="./indicador.php?id=3020">O3 Nobsa</a></li>
                        <li><a href="./indicador.php?id=3047">PM10 UPTC</a></li>
                    </ul>
                </div>

                <!-- 3. Categoría Especial -->
                <button class="accordion-button" onclick="toggleAccordion(this)">Categoría Especial</button>
                <div class="accordion-content">
                    <ul class="icon-list-items">
                        <li><a href="./indicador.php?id=3002">Servicio de acueducto urbano</a></li>
                        <li><a href="./indicador.php?id=3003">Sistema de alcantarillado</a></li>
                        <li><a href="./indicador.php?id=3004">Aseo urbano</a></li>
                        <li><a href="./indicador.php?id=3005">Continuidad del servicio (horas)</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- ================= TAB 4 — DATALAKE ================= -->
        <div class="tab-pane fade ambiental-pane" id="datalake">

            <h2 class="datalake-title">Repositorio de Datos (CSV)</h2>
            <p class="description">Descarga los archivos oficiales de la dimensión ambiental.</p>

            <div class="datalake-list">

                <div class="datalake-block mb-3">
                    <h4>Servicios Públicos</h4>
                    <ul class="datalake-ul">
                        <li class="datalake-item">
                            <span>Servicio de acueducto urbano</span>
                            <a href="datalake/ambiental/3002.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Sistema de alcantarillado urbano</span>
                            <a href="datalake/ambiental/3003.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                    </ul>
                </div>

            </div>

        </div>

    </div>

</div>

<?php include 'include/footer.php'; ?>
