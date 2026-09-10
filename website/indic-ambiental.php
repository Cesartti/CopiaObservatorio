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
                src="https://app.powerbi.com/view?r=eyJrIjoiNmEyMDcxMTMtZjg4ZC00NGZjLTlkMjMtMjUyYzgxMjE2YTNhIiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9"
                allowfullscreen>
            </iframe>
        </div>

        <!-- ================= TAB 2 — HOJA DE VIDA ================= -->
        <div class="tab-pane fade ambiental-pane" id="hojavida">

            <h3 class="hv-title">Ficha técnica del indicador</h3>

            <select id="indicatorSelect" class="form-select mb-3" onchange="showIndicatorInfo()">
                <option value="">Seleccione un indicador</option>

                <optgroup label="Ecosistemas estratégicos y biodiversidad">
                    <option value="3101">Área de humedales por municipio</option>
                    <option value="3102">Área de acuíferos (sistema acuífero de Tunja) por municipio</option>
                    <option value="3103">Área de rondas hídricas acotadas por municipio</option>
                    <option value="3104">Área de páramos por municipio y complejo de páramos</option>
                    <option value="3105">Cobertura de bosque estable por municipio</option>
                    <option value="3106">Áreas forestales de protección y producción por municipio</option>
                </optgroup>
                <optgroup label="Recurso hídrico y saneamiento ambiental">
                    <option value="3201">Cobertura del servicio de acueducto en zona urbana</option>
                    <option value="3202">Cobertura del servicio de acueducto en zona rural</option>
                    <option value="3203">Cobertura del servicio de alcantarillado en zona urbana</option>
                    <option value="3204">Cobertura del servicio de alcantarillado en zona rural</option>
                    <option value="3205">Continuidad del servicio de acueducto (horas/día)</option>
                    <option value="3206">Municipios con sistema de tratamiento de aguas residuales (STAR) reportado</option>
                    <option value="3207">Acueductos rurales y suscriptores por municipio</option>
                    <option value="3208">Índice de Riesgo de la Calidad del Agua (IRCA) – zona urbana</option>
                    <option value="3209">Índice de Riesgo de la Calidad del Agua (IRCA) – zona rural nucleada</option>
                    <option value="3210">Municipios vinculados al Plan Departamental de Aguas (PDA)</option>
                    <option value="3211">Concesiones de aguas superficiales (expedientes CORPOBOYACÁ)</option>
                    <option value="3212">Concesiones de aguas subterráneas (expedientes CORPOBOYACÁ)</option>
                    <option value="3213">Permisos de vertimientos (expedientes CORPOBOYACÁ)</option>
                </optgroup>
                <optgroup label="Gobernanza, control y gestión ambiental">
                    <option value="3301">Permisos de aprovechamiento forestal (expedientes CORPOBOYACÁ)</option>
                    <option value="3302">Licencias ambientales (expedientes CORPOBOYACÁ)</option>
                    <option value="3303">Registro de plantaciones forestales protectoras-productoras</option>
                    <option value="3304">Delitos contra los recursos naturales y el medio ambiente</option>
                    <option value="3305">Emergencias ambientales y de origen natural atendidas</option>
                    <option value="3306">Campañas de recolección de envases de agroquímicos (posconsumo)</option>
                    <option value="3307">Educación ambiental: PRAES, PROCEDAS y CIDEAS acompañados</option>
                </optgroup>
                <optgroup label="Salud ambiental">
                    <option value="3401">Agresiones por animales potencialmente transmisores de rabia</option>
                    <option value="3402">Accidente ofídico (mordedura de serpiente)</option>
                    <option value="3403">Accidentes por otros animales venenosos</option>
                </optgroup>
                <optgroup label="Calidad ambiental y servicios públicos">
                    <option value="3501">Cobertura del servicio de aseo en zona urbana</option>
                    <option value="3502">Municipios prestadores directos de acueducto, alcantarillado y aseo</option>
                    <option value="3503">Disposición final de residuos sólidos (toneladas/día y tipo de sitio)</option>
                    <option value="3504">Índice de Cobertura de Energía Eléctrica (ICEE)</option>
                    <option value="3505">Viviendas con y sin servicio de energía eléctrica</option>
                    <option value="3506">Calidad del aire: Material particulado PM10 por estación de monitoreo</option>
                    <option value="3507">Calidad del aire: Material particulado PM2.5 por estación de monitoreo</option>
                    <option value="3508">Calidad del aire: Dióxido de azufre (SO₂) por estación de monitoreo</option>
                    <option value="3509">Calidad del aire: Dióxido de nitrógeno (NO₂) por estación de monitoreo</option>
                    <option value="3510">Calidad del aire: Monóxido de carbono (CO) por estación de monitoreo</option>
                    <option value="3511">Calidad del aire: Ozono troposférico (O₃) por estación de monitoreo</option>
                    <option value="3512">Prácticas de ahorro de energía y agua en edificaciones culminadas</option>
                </optgroup>
            </select>

            <div id="indicatorInfo" class="hv-data-box">
                <p class="text-muted">Seleccione un indicador para ver su ficha técnica.</p>
            </div>

        </div>

        <!-- ================= TAB 3 — CATEGORÍAS ================= -->
        <div class="tab-pane fade ambiental-pane" id="categorias">

            <h2 class="cat-title">Categorías del Observatorio Ambiental</h2>

            <!-- ACORDEÓN -->
            <div class="accordion-container">

                <button class="accordion-button" onclick="toggleAccordion(this)">Ecosistemas estratégicos y biodiversidad</button>
                <div class="accordion-content">
                    <ul class="icon-list-items">
                        <li class="indicador-subtitle">Humedales</li>
                        <li><a href="./indicador.php?id=3101">Área de humedales por municipio</a></li>
                        <li class="indicador-subtitle">Acuíferos</li>
                        <li><a href="./indicador.php?id=3102">Área de acuíferos (sistema acuífero de Tunja) por municipio</a></li>
                        <li class="indicador-subtitle">Rondas hídricas</li>
                        <li><a href="./indicador.php?id=3103">Área de rondas hídricas acotadas por municipio</a></li>
                        <li class="indicador-subtitle">Páramos</li>
                        <li><a href="./indicador.php?id=3104">Área de páramos por municipio y complejo de páramos</a></li>
                        <li class="indicador-subtitle">Bosques</li>
                        <li><a href="./indicador.php?id=3105">Cobertura de bosque estable por municipio</a></li>
                        <li class="indicador-subtitle">Áreas forestales</li>
                        <li><a href="./indicador.php?id=3106">Áreas forestales de protección y producción por municipio</a></li>
                    </ul>
                </div>
                <button class="accordion-button" onclick="toggleAccordion(this)">Recurso hídrico y saneamiento ambiental</button>
                <div class="accordion-content">
                    <ul class="icon-list-items">
                        <li class="indicador-subtitle">Acueducto</li>
                        <li><a href="./indicador.php?id=3201">Cobertura del servicio de acueducto en zona urbana</a></li>
                        <li><a href="./indicador.php?id=3202">Cobertura del servicio de acueducto en zona rural</a></li>
                        <li class="indicador-subtitle">Alcantarillado</li>
                        <li><a href="./indicador.php?id=3203">Cobertura del servicio de alcantarillado en zona urbana</a></li>
                        <li><a href="./indicador.php?id=3204">Cobertura del servicio de alcantarillado en zona rural</a></li>
                        <li class="indicador-subtitle">Acueducto</li>
                        <li><a href="./indicador.php?id=3205">Continuidad del servicio de acueducto (horas/día)</a></li>
                        <li class="indicador-subtitle">Saneamiento</li>
                        <li><a href="./indicador.php?id=3206">Municipios con sistema de tratamiento de aguas residuales (STAR) reportado</a></li>
                        <li class="indicador-subtitle">Acueductos rurales</li>
                        <li><a href="./indicador.php?id=3207">Acueductos rurales y suscriptores por municipio</a></li>
                        <li class="indicador-subtitle">Calidad del agua</li>
                        <li><a href="./indicador.php?id=3208">Índice de Riesgo de la Calidad del Agua (IRCA) – zona urbana</a></li>
                        <li><a href="./indicador.php?id=3209">Índice de Riesgo de la Calidad del Agua (IRCA) – zona rural nucleada</a></li>
                        <li class="indicador-subtitle">Plan Departamental de Aguas</li>
                        <li><a href="./indicador.php?id=3210">Municipios vinculados al Plan Departamental de Aguas (PDA)</a></li>
                        <li class="indicador-subtitle">Concesiones de agua</li>
                        <li><a href="./indicador.php?id=3211">Concesiones de aguas superficiales (expedientes CORPOBOYACÁ)</a></li>
                        <li><a href="./indicador.php?id=3212">Concesiones de aguas subterráneas (expedientes CORPOBOYACÁ)</a></li>
                        <li class="indicador-subtitle">Vertimientos</li>
                        <li><a href="./indicador.php?id=3213">Permisos de vertimientos (expedientes CORPOBOYACÁ)</a></li>
                    </ul>
                </div>
                <button class="accordion-button" onclick="toggleAccordion(this)">Gobernanza, control y gestión ambiental</button>
                <div class="accordion-content">
                    <ul class="icon-list-items">
                        <li class="indicador-subtitle">Control forestal</li>
                        <li><a href="./indicador.php?id=3301">Permisos de aprovechamiento forestal (expedientes CORPOBOYACÁ)</a></li>
                        <li class="indicador-subtitle">Licenciamiento</li>
                        <li><a href="./indicador.php?id=3302">Licencias ambientales (expedientes CORPOBOYACÁ)</a></li>
                        <li class="indicador-subtitle">Control forestal</li>
                        <li><a href="./indicador.php?id=3303">Registro de plantaciones forestales protectoras-productoras</a></li>
                        <li class="indicador-subtitle">Delitos ambientales</li>
                        <li><a href="./indicador.php?id=3304">Delitos contra los recursos naturales y el medio ambiente</a></li>
                        <li class="indicador-subtitle">Gestión del riesgo</li>
                        <li><a href="./indicador.php?id=3305">Emergencias ambientales y de origen natural atendidas</a></li>
                        <li class="indicador-subtitle">Residuos posconsumo</li>
                        <li><a href="./indicador.php?id=3306">Campañas de recolección de envases de agroquímicos (posconsumo)</a></li>
                        <li class="indicador-subtitle">Educación ambiental</li>
                        <li><a href="./indicador.php?id=3307">Educación ambiental: PRAES, PROCEDAS y CIDEAS acompañados</a></li>
                    </ul>
                </div>
                <button class="accordion-button" onclick="toggleAccordion(this)">Salud ambiental</button>
                <div class="accordion-content">
                    <ul class="icon-list-items">
                        <li class="indicador-subtitle">Zoonosis</li>
                        <li><a href="./indicador.php?id=3401">Agresiones por animales potencialmente transmisores de rabia</a></li>
                        <li><a href="./indicador.php?id=3402">Accidente ofídico (mordedura de serpiente)</a></li>
                        <li><a href="./indicador.php?id=3403">Accidentes por otros animales venenosos</a></li>
                    </ul>
                </div>
                <button class="accordion-button" onclick="toggleAccordion(this)">Calidad ambiental y servicios públicos</button>
                <div class="accordion-content">
                    <ul class="icon-list-items">
                        <li class="indicador-subtitle">Aseo</li>
                        <li><a href="./indicador.php?id=3501">Cobertura del servicio de aseo en zona urbana</a></li>
                        <li class="indicador-subtitle">Prestación de servicios</li>
                        <li><a href="./indicador.php?id=3502">Municipios prestadores directos de acueducto, alcantarillado y aseo</a></li>
                        <li class="indicador-subtitle">Residuos sólidos</li>
                        <li><a href="./indicador.php?id=3503">Disposición final de residuos sólidos (toneladas/día y tipo de sitio)</a></li>
                        <li class="indicador-subtitle">Energía eléctrica</li>
                        <li><a href="./indicador.php?id=3504">Índice de Cobertura de Energía Eléctrica (ICEE)</a></li>
                        <li><a href="./indicador.php?id=3505">Viviendas con y sin servicio de energía eléctrica</a></li>
                        <li class="indicador-subtitle">Calidad del aire</li>
                        <li><a href="./indicador.php?id=3506">Calidad del aire: Material particulado PM10 por estación de monitoreo</a></li>
                        <li><a href="./indicador.php?id=3507">Calidad del aire: Material particulado PM2.5 por estación de monitoreo</a></li>
                        <li><a href="./indicador.php?id=3508">Calidad del aire: Dióxido de azufre (SO₂) por estación de monitoreo</a></li>
                        <li><a href="./indicador.php?id=3509">Calidad del aire: Dióxido de nitrógeno (NO₂) por estación de monitoreo</a></li>
                        <li><a href="./indicador.php?id=3510">Calidad del aire: Monóxido de carbono (CO) por estación de monitoreo</a></li>
                        <li><a href="./indicador.php?id=3511">Calidad del aire: Ozono troposférico (O₃) por estación de monitoreo</a></li>
                        <li class="indicador-subtitle">Economía circular</li>
                        <li><a href="./indicador.php?id=3512">Prácticas de ahorro de energía y agua en edificaciones culminadas</a></li>
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
                    <h4>Ecosistemas estratégicos y biodiversidad</h4>
                    <ul class="datalake-ul">
                        <li class="datalake-item">
                            <span>Área de humedales por municipio</span>
                            <a href="indicador/3101/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Área de acuíferos (sistema acuífero de Tunja) por municipio</span>
                            <a href="indicador/3102/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Área de rondas hídricas acotadas por municipio</span>
                            <a href="indicador/3103/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Área de páramos por municipio y complejo de páramos</span>
                            <a href="indicador/3104/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Cobertura de bosque estable por municipio</span>
                            <a href="indicador/3105/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Áreas forestales de protección y producción por municipio</span>
                            <a href="indicador/3106/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                    </ul>
                </div>
                <div class="datalake-block mb-3">
                    <h4>Recurso hídrico y saneamiento ambiental</h4>
                    <ul class="datalake-ul">
                        <li class="datalake-item">
                            <span>Cobertura del servicio de acueducto en zona urbana</span>
                            <a href="indicador/3201/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Cobertura del servicio de acueducto en zona rural</span>
                            <a href="indicador/3202/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Cobertura del servicio de alcantarillado en zona urbana</span>
                            <a href="indicador/3203/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Cobertura del servicio de alcantarillado en zona rural</span>
                            <a href="indicador/3204/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Continuidad del servicio de acueducto (horas/día)</span>
                            <a href="indicador/3205/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Municipios con sistema de tratamiento de aguas residuales (STAR) reportado</span>
                            <a href="indicador/3206/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Acueductos rurales y suscriptores por municipio</span>
                            <a href="indicador/3207/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Índice de Riesgo de la Calidad del Agua (IRCA) – zona urbana</span>
                            <a href="indicador/3208/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Índice de Riesgo de la Calidad del Agua (IRCA) – zona rural nucleada</span>
                            <a href="indicador/3209/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Municipios vinculados al Plan Departamental de Aguas (PDA)</span>
                            <a href="indicador/3210/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Concesiones de aguas superficiales (expedientes CORPOBOYACÁ)</span>
                            <a href="indicador/3211/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Concesiones de aguas subterráneas (expedientes CORPOBOYACÁ)</span>
                            <a href="indicador/3212/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Permisos de vertimientos (expedientes CORPOBOYACÁ)</span>
                            <a href="indicador/3213/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                    </ul>
                </div>
                <div class="datalake-block mb-3">
                    <h4>Gobernanza, control y gestión ambiental</h4>
                    <ul class="datalake-ul">
                        <li class="datalake-item">
                            <span>Permisos de aprovechamiento forestal (expedientes CORPOBOYACÁ)</span>
                            <a href="indicador/3301/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Licencias ambientales (expedientes CORPOBOYACÁ)</span>
                            <a href="indicador/3302/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Registro de plantaciones forestales protectoras-productoras</span>
                            <a href="indicador/3303/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Delitos contra los recursos naturales y el medio ambiente</span>
                            <a href="indicador/3304/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Emergencias ambientales y de origen natural atendidas</span>
                            <a href="indicador/3305/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Campañas de recolección de envases de agroquímicos (posconsumo)</span>
                            <a href="indicador/3306/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Educación ambiental: PRAES, PROCEDAS y CIDEAS acompañados</span>
                            <a href="indicador/3307/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                    </ul>
                </div>
                <div class="datalake-block mb-3">
                    <h4>Salud ambiental</h4>
                    <ul class="datalake-ul">
                        <li class="datalake-item">
                            <span>Agresiones por animales potencialmente transmisores de rabia</span>
                            <a href="indicador/3401/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Accidente ofídico (mordedura de serpiente)</span>
                            <a href="indicador/3402/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Accidentes por otros animales venenosos</span>
                            <a href="indicador/3403/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                    </ul>
                </div>
                <div class="datalake-block mb-3">
                    <h4>Calidad ambiental y servicios públicos</h4>
                    <ul class="datalake-ul">
                        <li class="datalake-item">
                            <span>Cobertura del servicio de aseo en zona urbana</span>
                            <a href="indicador/3501/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Municipios prestadores directos de acueducto, alcantarillado y aseo</span>
                            <a href="indicador/3502/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Disposición final de residuos sólidos (toneladas/día y tipo de sitio)</span>
                            <a href="indicador/3503/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Índice de Cobertura de Energía Eléctrica (ICEE)</span>
                            <a href="indicador/3504/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Viviendas con y sin servicio de energía eléctrica</span>
                            <a href="indicador/3505/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Calidad del aire: Material particulado PM10 por estación de monitoreo</span>
                            <a href="indicador/3506/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Calidad del aire: Material particulado PM2.5 por estación de monitoreo</span>
                            <a href="indicador/3507/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Calidad del aire: Dióxido de azufre (SO₂) por estación de monitoreo</span>
                            <a href="indicador/3508/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Calidad del aire: Dióxido de nitrógeno (NO₂) por estación de monitoreo</span>
                            <a href="indicador/3509/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Calidad del aire: Monóxido de carbono (CO) por estación de monitoreo</span>
                            <a href="indicador/3510/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Calidad del aire: Ozono troposférico (O₃) por estación de monitoreo</span>
                            <a href="indicador/3511/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                        <li class="datalake-item">
                            <span>Prácticas de ahorro de energía y agua en edificaciones culminadas</span>
                            <a href="indicador/3512/1.csv" download class="btn btn-sm btn-outline-primary">Descargar</a>
                        </li>
                    </ul>
                </div>
                </div>

            </div>

        </div>

    </div>

</div>

<?php include 'include/footer.php'; ?>
