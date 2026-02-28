<?php
require_once 'tracker.php';
include 'include/header.php';
?>
<div class="economico-page">
<!-- ======== CSS ESPECÍFICO PARA ESTA PÁGINA ======== -->
<link rel="stylesheet" href="assets/css/IndicadoresEconomico.css">
<script src="assets/js/IndicadoresEconomico.js" defer></script>

<div id="main-body" class="main-body">

<!-- ======== BANNER SUPERIOR DIMENSIÓN ECONÓMICA ======== -->
<section class="banner-economico-wrapper">
    <img src="assets/svg/bg-banner-economico.png" 
         alt="Dimensión Económica" 
         class="banner-economico">

             <!-- CAPA OSCURA -->
    <div class="banner-overlay"></div>

    <div class="banner-economico-text">
        <h2>Dimensión Económica</h2>
        <p>
            Monitorear periódicamente los principales indicadores que permiten conocer la
            realidad económica del departamento a través de los principales renglones de la
            actividad productiva tales como: agropecuario, minero, desarrollo empresarial
            y de servicios, así como las variables que afectan la competitividad,
            productividad y finanzas públicas del departamento.
        </p>
    </div>
</section>

<!-- 🚀 SISTEMA DE PESTAÑAS -->
<div class="container-fluid mt-4 econ-tabs-wrapper">

    <ul class="nav nav-tabs justify-content-center" id="econTabs" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link econ-tab active" data-bs-toggle="tab" data-bs-target="#tablero" type="button">
                📊 <span>Tablero</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link econ-tab" data-bs-toggle="tab" data-bs-target="#hojavida" type="button">
                📄 <span>Hoja de Vida</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link econ-tab" data-bs-toggle="tab" data-bs-target="#categorias" type="button">
                📂 <span>Categorías</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link econ-tab" data-bs-toggle="tab" data-bs-target="#datalake" type="button">
                📥 <span>DATALAKE</span>
            </button>
        </li>

    </ul>


    <div class="tab-content econ-tab-content">

        <!-- ⭐ TAB 1: TABLERO POWER BI -->
        <div class="tab-pane fade show active econ-pane econ-pane--highlight" id="tablero" role="tabpanel">
            <iframe class="iframe-full"
                src="https://app.powerbi.com/view?r=eyJrIjoiNTAyNjgyNTYtMWQyNC00Njc3LWJkMzgtMzRiNTBjNTUyODYwIiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=07fb08234b68b1d828a7"
                allowfullscreen>
            </iframe>
        </div>
        <!-- ⭐ TAB 2: HOJA DE VIDA DE INDICADORES -->
        <div class="tab-pane fade econ-pane" id="hojavida" role="tabpanel">

            <h2 class="indicador-title mb-3">Hoja de Vida de los Indicadores</h2>
            <p class="description">
                Selecciona un indicador de la lista para consultar su ficha técnica básica.
            </p>

            <!-- Barra de selección de indicador -->
            <div id="buscador" class="mt-4">
                <select id="indicatorSelect" class="form-select" onchange="showIndicatorInfo()">
                    <option value="">Seleccione un indicador...</option>

                    <!-- ========================================================= -->
                    <!-- 📌 INDICADORES DE VIOLENCIA (2001–2021) -->
                    <!-- ========================================================= -->
                    <option value="2001">Violencia intrafamiliar</option>
                    <option value="2002">Violencia contra la mujer</option>
                    <option value="2003">Homicidios</option>
                    <option value="2004">Delitos sexuales</option>
                    <option value="2005">Lesiones personales</option>
                    <option value="2006">Violencia de pareja</option>
                    <option value="2007">Violencia sexual infantil</option>
                    <option value="2008">Tentativa de homicidio</option>
                    <option value="2009">Accidentes de tránsito con víctimas</option>
                    <option value="2010">Suicidios</option>
                    <option value="2011">Extorsión</option>
                    <option value="2012">Amenazas</option>
                    <option value="2013">Secuestro</option>
                    <option value="2014">Hurto a personas</option>
                    <option value="2015">Hurto a residencias</option>
                    <option value="2016">Hurto a comercio</option>
                    <option value="2017">Hurto a motocicletas</option>
                    <option value="2018">Hurto a vehículos</option>
                    <option value="2019">Riñas</option>
                    <option value="2020">Violencia escolar</option>
                    <option value="2021">Violencia basada en género</option>

                    <!-- ========================================================= -->
                    <!-- 📌 INDICADORES DE SALUD (2033–2203) -->
                    <!-- ========================================================= -->
                    <option value="2033">Atención en salud por EAPB</option>
                    <option value="2034">Población afiliada al SGSSS</option>
                    <option value="2035">Atención de urgencias</option>
                    <option value="2036">Enfermedades transmisibles</option>
                    <option value="2037">Enfermedades no transmisibles</option>
                    <option value="2038">Mortalidad general</option>
                    <option value="2039">Mortalidad materna</option>
                    <option value="2040">Mortalidad infantil</option>
                    <option value="2041">Desnutrición aguda en niños</option>
                    <option value="2042">Desnutrición crónica</option>
                    <option value="2043">Bajo peso al nacer</option>
                    <option value="2044">Cobertura de vacunación</option>
                    <option value="2045">Control prenatal</option>
                    <option value="2046">Atención de parto institucional</option>
                    <option value="2047">Salud mental – trastornos</option>
                    <option value="2048">Intentos de suicidio</option>
                    <option value="2049">Consumo de sustancias psicoactivas</option>
                    <option value="2050">Accidentes laborales</option>
                    <option value="2051">Enfermedades laborales</option>
                    <option value="2052">Aseguramiento en salud por municipio</option>
                    <option value="2053">Red de prestadores de salud</option>
                    <option value="2054">Morbilidad por eventos priorizados</option>
                    <option value="2055">Eventos de vigilancia epidemiológica</option>
                    <option value="2056">Brotes epidemiológicos</option>
                    <!-- Si deseas ingresar del 2057 al 2203, los agrego también -->

                    <!-- ========================================================= -->
                    <!-- 📌 INDICADORES DE EDUCACIÓN (2047–2056) -->
                    <!-- ========================================================= -->
                    <option value="2047">Cobertura bruta escolar</option>
                    <option value="2048">Cobertura neta escolar</option>
                    <option value="2049">Tasa de deserción escolar</option>
                    <option value="2050">Transición a educación media</option>
                    <option value="2051">Resultados Saber 11</option>
                    <option value="2052">Promoción escolar</option>
                    <option value="2053">Relación docentes–estudiantes</option>
                    <option value="2054">Infraestructura educativa adecuada</option>
                    <option value="2055">Acceso a internet educativo</option>
                    <option value="2056">Equipamiento TIC escolar</option>


                </select>
            </div>

            <!-- Información del indicador seleccionado -->
            <div id="indicatorInfo" class="mt-4 description">
                <p>Seleccione un indicador para ver la información.</p>
            </div>

        </div>

        <!-- ⭐ TAB 3: CATEGORÍAS (ACORDEÓN) -->
        <div class="tab-pane fade econ-pane econ-pane--highlight" id="categorias" role="tabpanel">

            <h2 class="indicador-title mb-3">Indicadores por Categoría</h2>

            <input type="text" id="searchInput" placeholder="Buscar indicadores..."
                class="form-control mb-4" onkeyup="filterIndicators()">

            <div class="accordion-list">

                <!-- Variables macroeconómicas -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Variables macroeconómicas
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item">
                                <span class="icon">📊</span>
                                <a href="./indicador.php?id=1001">Producto Interno Bruto por departamento</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Agropecuario - Producción Agrícola -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Educación – Pobreza Multidimensional
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📚</span><a href="./indicador.php?id=1001"> Analfabetismo</a></li>
                            <li class="icon-list-item"><span class="icon">📘</span><a href="./indicador.php?id=1002"> Bajo logro educativo</a></li>
                            <li class="icon-list-item"><span class="icon">🍼</span><a href="./indicador.php?id=1003"> Barreras a servicios de cuidado para la primera infancia</a></li>
                            <li class="icon-list-item"><span class="icon">🏫</span><a href="./indicador.php?id=1007"> Inasistencia escolar</a></li>
                            <li class="icon-list-item"><span class="icon">📖</span><a href="./indicador.php?id=1008"> Rezago escolar</a></li>
                        </ul>
                    </div>
                </div>


                <!-- Agropecuario - Participación Agrícola -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Salud – Pobreza Multidimensional
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🏥</span><a href="./indicador.php?id=1004"> Barreras de acceso a servicios de salud</a></li>
                            <li class="icon-list-item"><span class="icon">🚑</span><a href="./indicador.php?id=1010"> Sin aseguramiento en salud</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Vivienda – Pobreza Multidimensional
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🏚️</span><a href="./indicador.php?id=1005"> Porcentaje de hacinamiento crítico</a></li>
                            <li class="icon-list-item"><span class="icon">🚽</span><a href="./indicador.php?id=1006"> Inadecuada eliminación de excretas</a></li>
                            <li class="icon-list-item"><span class="icon">🧱</span><a href="./indicador.php?id=1012"> Material inadecuado de paredes exteriores</a></li>
                            <li class="icon-list-item"><span class="icon">🧱</span><a href="./indicador.php?id=1013"> Material inadecuado de pisos</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Servicios Públicos – Pobreza Multidimensional
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🚱</span><a href="./indicador.php?id=1009"> Sin acceso a fuente de agua mejorada</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Mercado Laboral – Pobreza Multidimensional
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📉</span><a href="./indicador.php?id=1011"> Desempleo de larga duración</a></li>
                            <li class="icon-list-item"><span class="icon">🧒</span><a href="./indicador.php?id=1014"> Trabajo infantil</a></li>
                            <li class="icon-list-item"><span class="icon">👷</span><a href="./indicador.php?id=1015"> Trabajo informal</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Pobreza Monetaria
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">💵</span><a href="./indicador.php?id=1018"> Línea de pobreza</a></li>
                            <li class="icon-list-item"><span class="icon">💵</span><a href="./indicador.php?id=1019"> Línea de pobreza monetaria extrema</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1020"> Coeficiente de Gini</a></li>
                            <li class="icon-list-item"><span class="icon">♀️♂️</span><a href="./indicador.php?id=1021"> Incidencia pobreza monetaria según sexo</a></li>
                            <li class="icon-list-item"><span class="icon">♀️♂️</span><a href="./indicador.php?id=1022"> Incidencia pobreza monetaria extrema según sexo</a></li>
                            <li class="icon-list-item"><span class="icon">💰</span><a href="./indicador.php?id=1023"> Ingreso per cápita – unidad de gasto</a></li>
                            <li class="icon-list-item"><span class="icon">📉</span><a href="./indicador.php?id=1024"> Brecha de pobreza monetaria</a></li>
                            <li class="icon-list-item"><span class="icon">📉</span><a href="./indicador.php?id=1025"> Brecha de pobreza monetaria extrema</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Calidad de Vida Campesina – Condiciones Demográficas y Hogares
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🏠</span><a href="./indicador.php?id=1101"> Viviendas, hogares y personas en hogares campesinos</a></li>
                            <li class="icon-list-item"><span class="icon">🚰</span><a href="./indicador.php?id=1102"> Acceso a servicios públicos en hogares campesinos</a></li>
                            <li class="icon-list-item"><span class="icon">💧</span><a href="./indicador.php?id=1103"> Fuente de aprovisionamiento de agua para alimentos</a></li>
                            <li class="icon-list-item"><span class="icon">🚽</span><a href="./indicador.php?id=1104"> Tipo de servicio sanitario en hogares campesinos</a></li>
                            <li class="icon-list-item"><span class="icon">🔑</span><a href="./indicador.php?id=1105"> Tenencia de vivienda en hogares campesinos</a></li>
                            <li class="icon-list-item"><span class="icon">🤔</span><a href="./indicador.php?id=1106"> Percepción subjetiva de pobreza</a></li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Identidad, Salud y Cuidado Campesino
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🧑‍🌾</span><a href="./indicador.php?id=1107"> Identidad campesina por rangos de edad</a></li>
                            <li class="icon-list-item"><span class="icon">🩺</span><a href="./indicador.php?id=1108"> Afiliación al SGSSS por regímenes</a></li>
                            <li class="icon-list-item"><span class="icon">🧒</span><a href="./indicador.php?id=1109"> Cuidado de niños y niñas menores de 5 años</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Educación y Conectividad Campesina
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📚</span><a href="./indicador.php?id=1110"> Asistencia escolar 15–21 años</a></li>
                            <li class="icon-list-item"><span class="icon">🌐</span><a href="./indicador.php?id=1111"> Acceso a internet por tipo de conexión</a></li>
                            <li class="icon-list-item"><span class="icon">📶</span><a href="./indicador.php?id=1112"> Uso de internet por frecuencia</a></li>
                            <li class="icon-list-item"><span class="icon">🎓</span><a href="./indicador.php?id=1113"> Promedio de años de educación</a></li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Bienestar Subjetivo y Condiciones Familiares
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🙂</span><a href="./indicador.php?id=1114"> Satisfacción con la vida y otros aspectos</a></li>
                            <li class="icon-list-item"><span class="icon">👨‍👩‍👧</span><a href="./indicador.php?id=1115"> Hogares según sexo del jefe/a y presencia de hijos</a></li>
                            <li class="icon-list-item"><span class="icon">👥</span><a href="./indicador.php?id=1116"> Actividad principal por sexo (15 años y más)</a></li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Desempeño Municipal – Gestión y Resultados
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🏛️</span><a href="./indicador.php?id=1600"> Medición de Desempeño Municipal (MDM)</a></li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Agropecuario – Producción Agrícola
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🌱</span><a href="./indicador.php?id=1200"> Volumen de producción agrícola total</a></li>
                            <li class="icon-list-item"><span class="icon">🧭</span><a href="./indicador.php?id=1201"> Áreas de producción agrícola</a></li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Agropecuario – Producción Pecuaria
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🐄</span><a href="./indicador.php?id=1202"> Inventario bovino</a></li>
                            <li class="icon-list-item"><span class="icon">🐃</span><a href="./indicador.php?id=1203"> Inventario bufalino</a></li>
                            <li class="icon-list-item"><span class="icon">🐔</span><a href="./indicador.php?id=1204"> Inventario avícola</a></li>
                            <li class="icon-list-item"><span class="icon">🐑</span><a href="./indicador.php?id=1205"> Inventario caprino, ovino y equino</a></li>
                            <li class="icon-list-item"><span class="icon">🐖</span><a href="./indicador.php?id=1206"> Inventario porcino</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Finanzas Públicas – Operaciones Efectivas de Caja -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Finanzas Públicas – Operaciones Efectivas de Caja
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">💰</span><a href="./indicador.php?id=1500"> Ingresos totales municipales</a></li>
                            <li class="icon-list-item"><span class="icon">💸</span><a href="./indicador.php?id=1501"> Gastos totales municipales</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Finanzas Públicas – Ejecución de Ingresos (CUIPO) -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Finanzas Públicas – Ejecución de Ingresos (CUIPO)
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🏦</span><a href="./indicador.php?id=1502"> Recursos de capital municipales</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Finanzas Públicas – Gestión Fiscal -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Finanzas Públicas – Gestión Fiscal
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">⚖️</span><a href="./indicador.php?id=1503"> Indicador de racionalidad del gasto (Ley 617)</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1504"> Índice de Desempeño Fiscal (IDF)</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Finanzas Públicas – Cierre Fiscal -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Finanzas Públicas – Cierre Fiscal (FUT)
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📑</span><a href="./indicador.php?id=1505"> Cuentas por pagar y reservas presupuestales</a></li>
                        </ul>
                    </div>
                </div>


            </div>
        </div>

                <!-- ⭐ TAB 4: DATALAKE -->
                <div class="tab-pane fade econ-pane econ-pane--highlight" id="datalake" role="tabpanel">

                    <h2 class="indicador-title mb-3">DATALAKE Económico</h2>
                    <p class="description mb-4">
                        Descargue los archivos abiertos del Observatorio Económico de Boyacá (formato XLSX), organizados por categoría.
                    </p>

                <?php
                /* ===============================================================
                1️⃣ MATRIZ DE CATEGORÍAS E INDICADORES
                =============================================================== */
                $datalakeCategories = [

                    "Educación – Pobreza Multidimensional" => [
                        "1001" => "Analfabetismo",
                        "1002" => "Bajo logro educativo",
                        "1003" => "Barreras a servicios para cuidado de la primera infancia",
                        "1007" => "Inasistencia escolar",
                        "1008" => "Rezago escolar",
                    ],

                    "Salud – Pobreza Multidimensional" => [
                        "1004" => "Barreras de acceso a servicios de salud",
                        "1010" => "Sin aseguramiento en salud",
                    ],

                    "Vivienda – Pobreza Multidimensional" => [
                        "1005" => "Porcentaje de hacinamiento crítico",
                        "1006" => "Inadecuada eliminación de excretas",
                        "1012" => "Material inadecuado de paredes exteriores",
                        "1013" => "Material inadecuado de pisos",
                    ],

                    "Servicios Públicos – Pobreza Multidimensional" => [
                        "1009" => "Sin acceso a fuente de agua mejorada",
                    ],

                    "Mercado Laboral – Pobreza Multidimensional" => [
                        "1011" => "Desempleo de larga duración",
                        "1014" => "Trabajo infantil",
                        "1015" => "Trabajo informal",
                    ],

                    "Pobreza Monetaria" => [
                        "1016" => "Incidencia de pobreza multidimensional según sexo del jefe de hogar",
                        "1017" => "Incidencia de pobreza multidimensional según sexo de la persona",
                        "1018" => "Línea de pobreza (Pobreza Monetaria)",
                        "1019" => "Líneas de pobreza monetaria extrema",
                        "1020" => "Coeficiente de Gini",
                        "1021" => "Incidencia de la pobreza monetaria según sexo de la persona",
                        "1022" => "Incidencia de la pobreza monetaria extrema según sexo de la persona",
                        "1023" => "Promedio del ingreso per cápita de la unidad de gasto",
                        "1024" => "Brecha de la pobreza monetaria",
                        "1025" => "Brecha de la pobreza monetaria extrema",
                    ],

                    "Calidad de Vida Campesina – Condiciones Demográficas y Hogares" => [
                        "1101" => "Viviendas, hogares campesinos y personas en hogares campesinos",
                        "1102" => "Hogares por acceso a servicios públicos, privados o comunales",
                        "1103" => "Hogares por fuente de aprovisionamiento de agua para preparar alimentos",
                        "1104" => "Hogares por tipo de servicio sanitario",
                        "1105" => "Hogares por tenencia de vivienda",
                        "1106" => "Hogares por percepción subjetiva de pobreza",
                    ],

                    "Identidad, Salud y Cuidado Campesino" => [
                        "1107" => "Personas que se identifican como campesinos (15 años y más) por rangos de edad",
                        "1108" => "Personas de 15 años y más afiliadas al SGSSS por regímenes",
                        "1109" => "Niños y niñas menores de 5 años por sitio o persona con quien permanecen",
                    ],

                    "Educación y Conectividad Campesina" => [
                        "1110" => "Personas de 15 a 21 años por asistencia escolar",
                        "1111" => "Hogares con acceso a internet por tipo de conexión",
                        "1112" => "Personas de 15 años y más que usan internet por frecuencia de uso",
                        "1113" => "Promedio de años de educación (personas 15 años y más)",
                    ],

                    "Bienestar Subjetivo y Condiciones Familiares" => [
                        "1114" => "Calificación promedio de satisfacción con la vida y otros aspectos",
                        "1115" => "Hogares por sexo del jefe/a y presencia de hijos menores",
                        "1116" => "Población de 15 años y más por sexo según actividad principal",
                    ],

                    "Agropecuario – Producción Agrícola" => [
                        "1200" => "Volumen de producción agrícola total del departamento",
                        "1201" => "Áreas de producción agrícola",
                    ],

                    "Agropecuario – Producción Pecuaria" => [
                        "1202" => "Inventario bovino en el departamento",
                        "1203" => "Inventario bufalino en el departamento",
                        "1204" => "Inventario avícola en el departamento",
                        "1205" => "Inventario caprino, ovino y equino en el departamento",
                        "1206" => "Inventario porcino en el departamento",
                    ],

                    "Finanzas Públicas – Operaciones Efectivas de Caja" => [
                        "1500" => "Ingresos totales municipales",
                        "1501" => "Gastos totales municipales",
                    ],

                    "Finanzas Públicas – CUIPO (Ejecución de Ingresos)" => [
                        "1502" => "Recursos de capital municipales",
                    ],

                    "Finanzas Públicas – Gestión Fiscal (Ley 617/2000)" => [
                        "1503" => "Indicador de racionalidad del gasto (Ley 617 de 2000)",
                    ],

                    "Finanzas Públicas – Desempeño Fiscal Municipal" => [
                        "1504" => "Índice de Desempeño Fiscal (IDF)",
                    ],

                    "Finanzas Públicas – FUT (Cierre Fiscal – Situación Fiscal)" => [
                        "1505" => "Cuentas por pagar y reservas presupuestales municipales",
                    ],

                    "Desempeño Municipal – Gestión y Resultados" => [
                        "1600" => "Medición de Desempeño Municipal (MDM)",
                    ],
                ];

                /* ===============================================================
                2️⃣ COMO TODOS LOS ARCHIVOS EXISTEN Y SON XLSX → RUTA AUTOMÁTICA
                =============================================================== */

                function getFilePath($id) {
                    return "/indicador/dataEconomico/{$id}.xlsx";
                }
                ?>

                <!-- 📂 LISTA GENERADA DE ARCHIVOS XLSX -->
                <div class="datalake-list mt-4">

                    <?php foreach ($datalakeCategories as $category => $indicators) { ?>

                        <div class="datalake-block mb-5 p-4 shadow-sm rounded">

                            <h4 class="mb-3">📁 <?= $category; ?></h4>

                            <ul class="datalake-ul">

                                <?php foreach ($indicators as $id => $label) { ?>

                                    <li class="datalake-item d-flex align-items-center py-2">

                                        <span class="dl-icon me-2">📎</span>

                                        <span class="dl-text flex-grow-1"><?= $label; ?></span>

                                        <a 
                                            href="<?= getFilePath($id) ?>"
                                            download="<?= $label ?>.xlsx"
                                            class="btn btn-outline-dark btn-sm ms-3"
                                        >
                                            📥 Descargar XLSX
                                        </a>

                                    </li>

                                <?php } ?>

                            </ul>

                        </div>

                    <?php } ?>

                </div>

                </div>





                            <!-- MODAL DE CARACTERIZACIÓN -->
<div id="modalCaracterizacion" class="modal is-active">
  <div class="modal-background"></div>
  <div class="modal-content box">
    <h3><b>Ayúdanos a mejorar</b></h3>
    <p>Cuéntanos un poco sobre ti (opcional)</p>

    <form id="formCarac">
      <label>Profesión</label>
      <select class="select" name="profesion">
        <option value="">Seleccione</option>
        <option>Docente Universitario</option>
        <option>Docente Colegio</option>
        <option>Estudiante Universitario</option>
        <option>Estudiante Colegio</option>
        <option>Servidor Público</option>
        <option>ONG</option>
        <option>Sociedad Civil</option>
      </select>

      <label class="mt-3">Edad</label>
      <select class="select" name="edad">
        <option value="">Seleccione</option>
        <option>11-17</option>
        <option>18-24</option>
        <option>25-34</option>
        <option>35-44</option>
        <option>45-54</option>
        <option>55-64</option>
        <option>65+</option>
      </select>

      <label class="mt-3">Otra clasificación</label>
      <input type="text" class="input" name="otro">

      <button class="button is-primary mt-4" type="submit">Enviar</button>
    </form>
  </div>
</div>



<script>
// --------------------------------------------------
// Filtro de indicadores en el acordeón
// --------------------------------------------------
function filterIndicators() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const items = document.querySelectorAll('.icon-list-items li');

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(input) ? '' : 'none';
    });
}

// --------------------------------------------------
// Toggle del acordeón
// --------------------------------------------------
function toggleAccordion(button) {
    const content = button.nextElementSibling;
    const isOpen = content.style.display === 'block';

    // Cerrar todos
    document.querySelectorAll('.accordion-content').forEach(c => c.style.display = 'none');
    document.querySelectorAll('.accordion-button').forEach(b => b.classList.remove('active'));

    // Abrir el actual si estaba cerrado
    if (!isOpen) {
        content.style.display = 'block';
        button.classList.add('active');
    }
}

// --------------------------------------------------
// Datos de los indicadores (ficha técnica)
// --------------------------------------------------
const indicatorsData = {
     "1001": {
        "codigo": "1001",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el porcentaje de la población de centros poblados y rural disperso que no sabe leer ni escribir en un idioma.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1002": {
        "codigo": "1002",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas de 15 años o más que viven en hogares donde el nivel educativo alcanzado por sus miembros es considerado bajo.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1003": {
        "codigo": "1003",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el porcentaje de hogares que reportan barreras para acceder a servicios de cuidado de la primera infancia.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1004": {
        "codigo": "1004",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Salud",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la proporción de personas que enfrentan obstáculos para acceder a servicios de salud.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1005": {
        "codigo": "1005",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Vivienda",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide las condiciones de hacinamiento crítico en los hogares.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1006": {
        "codigo": "1006",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Vivienda",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de hogares que no disponen de sistemas adecuados para la eliminación de excretas.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1007": {
        "codigo": "1007",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de población en edad escolar que no asiste a una institución educativa.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1008": {
        "codigo": "1008",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Educación",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el porcentaje de estudiantes con rezago escolar.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1009": {
        "codigo": "1009",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Servicios públicos",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas u hogares sin acceso a una fuente de agua mejorada.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1010": {
        "codigo": "1010",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Salud",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas que no están afiliadas a un sistema de aseguramiento en salud.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1011": {
        "codigo": "1011",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Empleo",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la proporción de personas desempleadas por más de un año.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1012": {
        "codigo": "1012",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Vivienda",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Evalúa la calidad del material utilizado en paredes exteriores de las viviendas.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1013": {
        "codigo": "1013",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Vivienda",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Evalúa los materiales utilizados en la construcción de los pisos de las viviendas.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1014": {
        "codigo": "1014",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Trabajo infantil",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la prevalencia de trabajo infantil en la región.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1015": {
        "codigo": "1015",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Trabajo informal",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas ocupadas fuera de la formalidad.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1016": {
        "codigo": "1016",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Género",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la pobreza multidimensional según el sexo del jefe del hogar.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1017": {
        "codigo": "1017",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza multidimensional",
        "etiqueta": "Género",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Evalúa la incidencia de pobreza multidimensional según el sexo de la persona.",
        "fecha": "2018 - 2023",
        "fuente": "DANE"
    },

    "1018": {
        "codigo": "1018",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el nivel de pobreza basado en ingresos en la región.",
        "fecha": "2021 - 2023",
        "fuente": "DANE"
    },

    "1019": {
        "codigo": "1019",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria extrema",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el nivel de ingresos mínimos necesarios para alimentación en pobreza extrema.",
        "fecha": "2021 - 2023",
        "fuente": "DANE"
    },

    "1020": {
        "codigo": "1020",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria",
        "etiqueta": "Coeficiente de Gini",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide la desigualdad en la distribución del ingreso.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1021": {
        "codigo": "1021",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria",
        "etiqueta": "Género",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Proporción de población en pobreza monetaria desagregada por sexo.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1022": {
        "codigo": "1022",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Pobreza monetaria extrema",
        "etiqueta": "Género",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Porcentaje de personas en pobreza extrema monetaria según sexo.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1023": {
        "codigo": "1023",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Ingreso",
        "etiqueta": "Ingreso per cápita",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Promedio del ingreso per cápita de la población.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1024": {
        "codigo": "1024",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Brecha de pobreza monetaria",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Mide el déficit relativo de ingresos respecto a la línea de pobreza.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },

    "1025": {
        "codigo": "1025",
        "observatorio": "Económico",
        "categoria1": "Pobreza",
        "categoria2": "Brecha de pobreza monetaria extrema",
        "etiqueta": "Ingreso",
        "unidad": "Porcentaje",
        "desagregacion": "Hombres y Mujeres",
        "geografia": "Departamentos de la región centro oriente",
        "definicion": "Distancia promedio entre el ingreso per cápita y la línea de pobreza extrema.",
        "fecha": "2021 - 2024",
        "fuente": "DANE"
    },
      "1101": {
        "codigo": "1101",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Condiciones de vida y estructura demográfica rural",
        "etiqueta": "ND",
        "unidad": "Miles de viviendas, hogares, personas; personas por hogar",
        "desagregacion": "Totales de viviendas campesinas, hogares campesinos y personas; promedio de personas por hogar",
        "geografia": "Departamento de Boyacá",
        "definicion": "Reúne información sobre cantidad de viviendas campesinas, hogares campesinos, personas que los habitan y el tamaño promedio de hogar en zonas rurales.",
        "calculo": "Valores provenientes de tabulados oficiales del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Información reorganizada para análisis territorial en región centro oriente.",
        "disponibilidad": "SI"
    },

    "1102": {
        "codigo": "1102",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Acceso a servicios básicos en hogares campesinos",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Energía eléctrica, gas natural, acueducto, alcantarillado, recolección de basuras, teléfono fijo y hogares sin servicios",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de hogares campesinos con acceso a servicios públicos, privados o comunales fundamentales para el bienestar.",
        "calculo": "Valores tomados directamente de tabulados del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Plataforma digital y boletines",
        "observaciones": "Incluye reorganización anual por tipo de servicio.",
        "disponibilidad": "SI"
    },

    "1103": {
        "codigo": "1103",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Acceso a agua para preparar alimentos",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Acueducto público, acueducto comunal/veredal, pozos, agua lluvia, río, pila pública, carrotanque, aguatero, agua embotellada",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de hogares campesinos según la fuente principal de agua usada para preparar alimentos.",
        "calculo": "Valores del DANE sobre condiciones de vida rural.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Base integrada por año y fuente de agua.",
        "disponibilidad": "SI"
    },

    "1104": {
        "codigo": "1104",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Acceso a servicios sanitarios",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Inodoro con alcantarillado, pozo séptico, sin conexión, letrina, descarga directa, sin servicio",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de hogares campesinos según el tipo de servicio sanitario disponible.",
        "calculo": "Valores provenientes de tabulados oficiales del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma",
        "observaciones": "Permite evaluar saneamiento básico rural.",
        "disponibilidad": "SI"
    },

    "1105": {
        "codigo": "1105",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Tenencia y seguridad residencial",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Propia pagada, propia en pago, arriendo, permiso sin pago, posesión sin título, propiedad colectiva",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de hogares campesinos según la forma de tenencia de la vivienda.",
        "calculo": "Datos directos del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Incluye análisis sobre seguridad residencial rural.",
        "disponibilidad": "SI"
    },

    "1106": {
        "codigo": "1106",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Percepción subjetiva de pobreza",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Hogares donde se considera pobre vs. no pobre",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de hogares campesinos según la percepción del jefe/a o cónyuge sobre si se considera pobre.",
        "calculo": "Proporciones tomadas directamente del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Plataforma y boletines",
        "observaciones": "Complementa las mediciones objetivas de pobreza.",
        "disponibilidad": "SI"
    },

    "1107": {
        "codigo": "1107",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Identidad campesina y ciclo de vida",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "15+, 15-25, 26-40, 41-64, 65+",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de personas de 15 años y más que se identifican como campesinas, por rangos de edad.",
        "calculo": "Tabulados del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma",
        "observaciones": "Permite análisis generacional de identidad campesina.",
        "disponibilidad": "SI"
    },

    "1108": {
        "codigo": "1108",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Acceso al SGSSS",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Afiliados, no afiliados, NS/NI; régimen contributivo y subsidiado",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de personas de 15 años y más afiliadas al SGSSS según condición de afiliación y tipo de régimen.",
        "calculo": "Proporciones del DANE para cada categoría.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma",
        "observaciones": "Permite analizar brechas en aseguramiento en salud.",
        "disponibilidad": "SI"
    },

    "1109": {
        "codigo": "1109",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Cuidado y permanencia de primera infancia",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "CDI/Jardín, padre/madre casa, padre/madre trabajo, niñera, pariente 18+, pariente <18, otros",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de niños y niñas menores de 5 años según dónde o con quién permanecen durante la semana.",
        "calculo": "Valores tomados de tabulados del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Permite identificar riesgos en arreglos de cuidado infantil.",
        "disponibilidad": "SI"
    },

    "1110": {
        "codigo": "1110",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Educación y continuidad escolar",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Total 15-21, 15-16, 17-21",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de personas de 15 a 21 años que asisten a la escuela.",
        "calculo": "Proporciones del DANE por grupo etario.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Permite evaluar deserción y transición educativa.",
        "disponibilidad": "SI"
    },

    "1111": {
        "codigo": "1111",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Acceso a TIC y conectividad",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Internet total, fijo, móvil, combinado fijo-móvil",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de hogares campesinos con acceso a internet según el tipo de conexión.",
        "calculo": "Proporciones del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y medios digitales",
        "observaciones": "Permite analizar brecha digital rural.",
        "disponibilidad": "SI"
    },

    "1112": {
        "codigo": "1112",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Inclusión digital y usos de internet",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Uso total, diario, semanal, mensual y anual",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de personas de 15 años y más según la frecuencia con que usan internet.",
        "calculo": "Tabulados del DANE por frecuencia.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma",
        "observaciones": "Permite analizar alfabetización digital rural.",
        "disponibilidad": "SI"
    },

    "1113": {
        "codigo": "1113",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Educación y formación",
        "etiqueta": "ND",
        "unidad": "Años (promedio)",
        "desagregacion": "15+, campesinos 15+, campesinos 15-24, 25-34, 35+",
        "geografia": "Departamento de Boyacá",
        "definicion": "Promedio de años de educación alcanzados por la población de 15 años y más.",
        "calculo": "Valores directos del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Clave para análisis de brechas educativas rurales.",
        "disponibilidad": "SI"
    },

    "1114": {
        "codigo": "1114",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Bienestar subjetivo",
        "etiqueta": "ND",
        "unidad": "Escala 0-10",
        "desagregacion": "Vida en general, salud, seguridad, trabajo/actividad, tiempo libre, ingreso",
        "geografia": "Departamento de Boyacá",
        "definicion": "Calificación promedio de satisfacción con distintos aspectos de la vida en población campesina de 15 años y más.",
        "calculo": "Promedios del DANE",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Complementa indicadores objetivos de bienestar.",
        "disponibilidad": "SI"
    },

    "1115": {
        "codigo": "1115",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Condiciones del hogar rural",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%)",
        "desagregacion": "Jefatura masculina/femenina, sin cónyuge, con hijos menores",
        "geografia": "Departamento de Boyacá",
        "definicion": "Porcentaje de hogares campesinos según sexo del jefe/a, presencia de cónyuge y presencia de hijos menores de 18 años.",
        "calculo": "Valores directos del DANE",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Incluye hogares monoparentales masculinos y femeninos.",
        "disponibilidad": "SI"
    },

    "1116": {
        "codigo": "1116",
        "observatorio": "Económico",
        "categoria1": "Calidad de Vida Campesina",
        "categoria2": "Condiciones socioeconómicas rurales",
        "etiqueta": "ND",
        "unidad": "Miles de personas / Porcentaje (%)",
        "desagregacion": "Población por sexo total y actividad principal",
        "geografia": "Departamento de Boyacá",
        "definicion": "Población de 15 años y más según sexo y actividad principal realizada la semana anterior.",
        "calculo": "Valores tomados directamente del DANE.",
        "fecha": "2019 - 2023",
        "periodicidad": "Anual",
        "fuente": "DANE – Estadísticas de pobreza y condiciones de vida",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico",
        "forma_entrega": "Boletines y plataforma digital",
        "observaciones": "Permite analizar estructura de ocupación rural.",
        "disponibilidad": "SI"
    },

    "1200": {
        "codigo": "1200",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción Agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "Relación producción-rendimiento; distribución geográfica por ciclo y cultivo; comparativo por ciclo, grupo y provincia.",
        "geografia": "Departamento de Boyacá",
        "definicion": "Mide el comportamiento de la producción agrícola del departamento según ciclo, cultivo y ubicación geográfica.",
        "calculo": "Sumatoria según desagregación temática de Producción.",
        "fecha": "2019 - 2024",
        "periodicidad": "Anual",
        "fuente": "Evaluaciones agropecuarias municipales - EVA, Base agrícola UPRA 2019 - 2024.",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Agricultura",
        "forma_entrega": "Boletines, bases digitales y página oficial de observatorios.",
        "observaciones": "Información tomada de las EVA’s.",
        "disponibilidad": "SI"
    },

    "1201": {
        "codigo": "1201",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción Agrícola",
        "etiqueta": "ND",
        "unidad": "Hectáreas",
        "desagregacion": "Área sembrada; área cosechada; distribución por ciclo de cultivo, por cultivo, por grupo y por provincia.",
        "geografia": "Departamento de Boyacá",
        "definicion": "Muestra la extensión de tierra sembrada y cosechada, representada por ciclo, cultivo, grupo de cultivo y provincia.",
        "calculo": "Sumatoria según desagregación temática de áreas sembradas y cosechadas.",
        "fecha": "2019 - 2024",
        "periodicidad": "Anual",
        "fuente": "Evaluaciones agropecuarias municipales - EVA, Base agrícola UPRA 2019 - 2024.",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Agricultura",
        "forma_entrega": "Boletines y medios digitales de la Red de Observatorios.",
        "observaciones": "Información basada en EVA’s.",
        "disponibilidad": "SI"
    },
    "1202": {
        "codigo": "1202",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción Pecuaria",
        "etiqueta": "ND",
        "unidad": "Número de animales",
        "desagregacion": "Por sexo, sacrificio, edad y sexo, unidad productiva agropecuaria.",
        "geografia": "Departamento de Boyacá",
        "definicion": "Representa el número de bovinos en el departamento, desagregado por edad, sexo, municipio, provincia y sacrificio.",
        "calculo": "Sumatoria del número de animales por municipios, edad y sexo.",
        "fecha": "2019 - 2024",
        "periodicidad": "Anual",
        "fuente": "EVA – Base pecuaria UPRA 2019 - 2024.",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Agricultura",
        "forma_entrega": "Boletines y medios digitales de la Red de Observatorios.",
        "observaciones": "Para UPA se usan datos disponibles entre 2021 y 2024.",
        "disponibilidad": "SI"
    },
    "1203": {
        "codigo": "1203",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción Pecuaria",
        "etiqueta": "ND",
        "unidad": "Número de animales",
        "desagregacion": "Unidades productivas agropecuarias",
        "geografia": "Departamento de Boyacá",
        "definicion": "Representa el número de animales bufalinos y unidades productivas que poseen dicha especie.",
        "calculo": "Sumatoria del número de animales por municipios.",
        "fecha": "2019 - 2024",
        "periodicidad": "Anual",
        "fuente": "EVA – Base pecuaria UPRA 2019 - 2024.",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Agricultura",
        "forma_entrega": "Boletines y bases digitales de la Red de Observatorios.",
        "observaciones": "No se realiza desagregación por sexo o edad debido a ausencia de datos en algunos municipios.",
        "disponibilidad": "SI"
    },
    "1204": {
        "codigo": "1204",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción Pecuaria",
        "etiqueta": "ND",
        "unidad": "Número de animales",
        "desagregacion": "Por tipo de ave, capacidad y predio",
        "geografia": "Departamento de Boyacá",
        "definicion": "Representa la población avícola departamental según tipo de ave y capacidad productiva.",
        "calculo": "Sumatoria del número de aves o predios por tipo y por municipios productores.",
        "fecha": "2019 - 2024",
        "periodicidad": "Anual",
        "fuente": "EVA – Base pecuaria UPRA 2019 - 2024.",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Agricultura",
        "forma_entrega": "Boletines y medios digitales de la Red de Observatorios.",
        "observaciones": "Información tomada de las EVA’s.",
        "disponibilidad": "SI"
    },
    "1205": {
        "codigo": "1205",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción Pecuaria",
        "etiqueta": "ND",
        "unidad": "Número de animales",
        "desagregacion": "Por sexo",
        "geografia": "Departamento de Boyacá",
        "definicion": "Muestra el número de especies caprinas, equinas y ovinas por municipio y año.",
        "calculo": "Sumatoria por especie.",
        "fecha": "2019 - 2024",
        "periodicidad": "Anual",
        "fuente": "EVA – Base pecuaria UPRA 2019 - 2024.",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Agricultura",
        "forma_entrega": "Boletines y medios digitales.",
        "observaciones": "Desagregación por sexo disponible únicamente para 2023 y 2024.",
        "disponibilidad": "SI"
    },
    "1206": {
        "codigo": "1206",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción Pecuaria",
        "etiqueta": "ND",
        "unidad": "Número de animales",
        "desagregacion": "Sistema de producción general, etapas de desarrollo y unidades productivas",
        "geografia": "Departamento de Boyacá",
        "definicion": "Muestra el número de animales porcinos según municipio y año, considerando capacidades productivas y etapas de desarrollo.",
        "calculo": "Sumatoria por especie.",
        "fecha": "2019 - 2024",
        "periodicidad": "Anual",
        "fuente": "EVA – Base pecuaria UPRA 2019 - 2024.",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Agricultura",
        "forma_entrega": "Boletines y medios digitales.",
        "observaciones": "Se complementa con información registrada en el ICA.",
        "disponibilidad": "SI"
    },

    "1500": {
        "codigo": "1500",
        "observatorio": "Económico",
        "categoria1": "Finanzas Públicas",
        "categoria2": "Operaciones Efectivas de Caja",
        "etiqueta": "ND",
        "unidad": "Millones de pesos corrientes",
        "desagregacion": "Ingresos corrientes, tributarios, no tributarios, transferencias corrientes, ingresos de capital",
        "geografia": "Municipios del Departamento de Boyacá",
        "definicion": "Nivel y estructura de los ingresos totales municipales según operaciones efectivas de caja, incluyendo ingresos corrientes, tributarios, no tributarios, transferencias y recursos de capital.",
        "calculo": "Valores reportados por los municipios en las Operaciones Efectivas de Caja – CIFFIT (DNP).",
        "fecha": "2015 - 2023",
        "periodicidad": "Anual",
        "fuente": "Departamento Nacional de Planeación – CIFFIT",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico, Secretaría de Planeación",
        "forma_entrega": "Bases de datos, web y boletines digitales",
        "observaciones": "Dato validado mediante mesas de trabajo con la Secretaría de Planeación.",
        "disponibilidad": "SI"
    },

    "1501": {
        "codigo": "1501",
        "observatorio": "Económico",
        "categoria1": "Finanzas Públicas",
        "categoria2": "Operaciones Efectivas de Caja",
        "etiqueta": "ND",
        "unidad": "Millones de pesos corrientes",
        "desagregacion": "Gastos corrientes, funcionamiento, servicios personales, gastos generales, transferencias pagadas, intereses",
        "geografia": "Municipios del Departamento de Boyacá",
        "definicion": "Conjunto de erogaciones ejecutadas por los municipios según la clasificación de operaciones efectivas de caja, permitiendo identificar estructura y capacidad operativa del gasto.",
        "calculo": "Valores reportados oficialmente al DNP mediante Operaciones Efectivas de Caja.",
        "fecha": "2015 - 2023",
        "periodicidad": "Anual",
        "fuente": "Departamento Nacional de Planeación – CIFFIT",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Planeación, Observatorio Económico",
        "forma_entrega": "Bases de datos, web y boletines digitales",
        "observaciones": "Datos actualizados según reportes oficiales del DNP.",
        "disponibilidad": "SI"
    },

    "1502": {
        "codigo": "1502",
        "observatorio": "Económico",
        "categoria1": "Finanzas Públicas",
        "categoria2": "CUIPO – Ejecución de Ingresos",
        "etiqueta": "ND",
        "unidad": "Pesos corrientes",
        "desagregacion": "Recaudo de vigencia anterior con/sin fondos, recaudo vigencia actual con/sin fondos",
        "geografia": "Municipios del Departamento de Boyacá",
        "definicion": "Monto total de recursos de capital recaudados por los municipios según el formulario CUIPO de ejecución de ingresos.",
        "calculo": "Sumatoria de los rubros reportados en el formulario CUIPO – Ejecución de Ingresos.",
        "fecha": "2015 - 2023",
        "periodicidad": "Anual",
        "fuente": "Departamento Nacional de Planeación – CIFFIT (CUIPO)",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Planeación, Observatorio Económico",
        "forma_entrega": "Bases de datos, web y boletines digitales",
        "observaciones": "Dato homologado con reportes del CIFFIT.",
        "disponibilidad": "SI"
    },

    "1503": {
        "codigo": "1503",
        "observatorio": "Económico",
        "categoria1": "Finanzas Públicas",
        "categoria2": "Gestión Fiscal – Ley 617 de 2000",
        "etiqueta": "ND",
        "unidad": "Porcentaje (%) y estado de cumplimiento",
        "desagregacion": "Razón GF/ICLD (%), límite según categoría, cumplimiento",
        "geografia": "Municipios del Departamento de Boyacá",
        "definicion": "Evalúa si los municipios cumplen con los límites máximos permitidos por la Ley 617 frente al gasto de funcionamiento y los ingresos corrientes de libre destinación.",
        "calculo": "Razón GF/ICLD (%) según metodología del DNP.",
        "fecha": "2015 - 2023",
        "periodicidad": "Anual",
        "fuente": "Departamento Nacional de Planeación – CIFFIT",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Planeación, Observatorio Económico",
        "forma_entrega": "Boletines, web y bases de datos",
        "observaciones": "Incluye cumplimiento según límites de Ley 617.",
        "disponibilidad": "SI"
    },

    "1504": {
        "codigo": "1504",
        "observatorio": "Económico",
        "categoria1": "Finanzas Públicas",
        "categoria2": "Desempeño Fiscal Municipal",
        "etiqueta": "ND",
        "unidad": "Puntaje de calificación (0–100)",
        "desagregacion": "Puntaje general y rangos de clasificación",
        "geografia": "Municipios del Departamento de Boyacá",
        "definicion": "Evalúa sostenibilidad fiscal y gestión financiera municipal mediante el Índice de Desempeño Fiscal del DNP.",
        "calculo": "Cálculo oficial del DNP según metodología del IDF.",
        "fecha": "2015 - 2023",
        "periodicidad": "Anual",
        "fuente": "Departamento Nacional de Planeación – CIFFIT",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Planeación, Observatorio Económico",
        "forma_entrega": "Bases de datos, boletines y web",
        "observaciones": "Se usa metodología del IDF vigente.",
        "disponibilidad": "SI"
    },

    "1505": {
        "codigo": "1505",
        "observatorio": "Económico",
        "categoria1": "Finanzas Públicas",
        "categoria2": "FUT – Cierre Fiscal (Situación Fiscal)",
        "etiqueta": "ND",
        "unidad": "Pesos corrientes",
        "desagregacion": "Cuentas por pagar vigencia actual, vigencias anteriores y reservas presupuestales",
        "geografia": "Municipios del Departamento de Boyacá",
        "definicion": "Registra obligaciones pendientes de pago y reservas presupuestales reportadas en el FUT – Cierre Fiscal del DNP.",
        "calculo": "Compilación directa de los valores registrados en el módulo FUT – Situación Fiscal.",
        "fecha": "2015 - 2023",
        "periodicidad": "Anual",
        "fuente": "Departamento Nacional de Planeación – CIFFIT – FUT",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Secretaría de Planeación, Observatorio Económico",
        "forma_entrega": "Bases de datos, boletines y web",
        "observaciones": "Permite evaluar sostenibilidad y obligaciones fiscales.",
        "disponibilidad": "SI"
    },

    
    "1600":  {
        "codigo": "1600",
        "observatorio": "Económico",
        "categoria1": "Desempeño Municipal",
        "categoria2": "Medición de Desempeño Municipal",
        "etiqueta": "ND",
        "unidad": "Puntaje de calificación y grupo de capacidades",
        "desagregacion": "Puntaje general, puntaje en dimensión de gestión, puntaje en dimensión de resultados, clasificación por grupos de capacidades (G1–G5)",
        "geografia": "Municipios del Departamento de Boyacá",
        "definicion": "Mide y compara el desempeño municipal entendido como la gestión de las entidades territoriales y los resultados de desarrollo, integrando dimensiones como educación, salud, servicios públicos, seguridad, ordenamiento territorial, movilización de recursos y gobierno abierto. La metodología del DNP produce un puntaje entre 0 y 100 y clasifica los municipios en grupos de capacidades (G1–G5).",
        "calculo": "El indicador es construido por el Departamento Nacional de Planeación (DNP) aplicando la metodología oficial de Medición de Desempeño Municipal.",
        "fecha": "2018 - 2023",
        "periodicidad": "Anual",
        "fuente": "Departamento Nacional de Planeación - DNP",
        "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
        "actores_involucrados": "Observatorio Económico, Secretaría de Planeación",
        "forma_entrega": "Físico y medio digital",
        "observaciones": "Indicador validado mediante mesas de trabajo con Secretaría de Planeación. Se actualiza a partir de la base oficial del DNP con la metodología más reciente.",
        "disponibilidad": "SI"
    },

    // ... (RESTO DE INDICATORSDATA IGUAL A TU VERSIÓN LARGA) ...
};

// --------------------------------------------------
// Mostrar información del indicador seleccionado
// --------------------------------------------------
function showIndicatorInfo() {
    const select = document.getElementById("indicatorSelect");
    const selectedId = select.value;
    const infoContainer = document.getElementById("indicatorInfo");

    if (selectedId && indicatorsData[selectedId]) {
        const data = indicatorsData[selectedId];
        infoContainer.innerHTML = `
            <h3>Indicador: ${data.codigo}</h3>
            <p><strong>Observatorio:</strong> ${data.observatorio}</p>
            <p><strong>Categoría principal:</strong> ${data.categoria1}</p>
            <p><strong>Categoría secundaria:</strong> ${data.categoria2}</p>
            <p><strong>Etiqueta:</strong> ${data.etiqueta}</p>
            <p><strong>Unidad de medida:</strong> ${data.unidad}</p>
            <p><strong>Desagregación:</strong> ${data.desagregacion}</p>
            <p><strong>Ámbito geográfico:</strong> ${data.geografia}</p>
            <p><strong>Definición:</strong> ${data.definicion}</p>
            <p><strong>Periodo de datos:</strong> ${data.fecha}</p>
            <p><strong>Fuente:</strong> ${data.fuente}</p>
        `;
    } else {
        infoContainer.innerHTML = "<p>Seleccione un indicador para ver la información.</p>";
    }
}

// --------------------------------------------------
// Envío del formulario de caracterización
// --------------------------------------------------
document.getElementById("formCarac").onsubmit = async (e) => {
  e.preventDefault();

  const form = new FormData(e.target);

  await fetch("/website/formulario_usuario.php", {
    method: "POST",
    body: form
  });

  document.getElementById("modalCaracterizacion").style.display = "none";
};
</script>
</div>
<?php include 'include/footer.php'; ?>