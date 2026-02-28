<?php
require_once 'tracker.php';
include 'include/header.php';
?>

<!-- ======== CSS ESPECÍFICO PARA ESTA PÁGINA ======== -->
<link rel="stylesheet" href="assets/css/IndicadoresEconomico.css">

<div id="main-body" class="main-body">

    <!-- ENCABEZADO Y DESCRIPCIÓN -->
    <section class="container-fluid section-indicador pt-4">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="indicador-title">Dimensión Económica</h2>
                <p class="indicador-description mt-3 px-4">
                    Monitorear periódicamente los principales indicadores que permiten conocer la
                    realidad económica del departamento a través de los principales renglones de la
                    actividad productiva tales como: agropecuario, minero, desarrollo empresarial
                    y de servicios, así como las variables que afectan la competitividad,
                    productividad y finanzas públicas del departamento.
                </p>
            </div>
        </div>
    </section>

</div>

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
                    <!-- LISTA DE INDICADORES (IDS) -->
                    <option value="1001">Producto Interno Bruto del Departamento</option>
                    <option value="1002">Volumen de producción agrícola total del departamento</option>
                    <option value="1003">Producción agrícola de cultivos permanentes</option>
                    <option value="1004">Producción Minera Boyacá</option>
                    <option value="1005">Exportaciones minera de Boyacá</option>
                    <option value="1006">Importaciones Mineras</option>
                    <option value="1007">Asignación De Regalías Mineras</option>
                    <option value="1008">Producción De Leguminosas</option>
                    <option value="1009">Producción total de tubérculos</option>
                    <option value="1010">Producción De Fibras</option>
                    <option value="1011">Producción De Otros Cultivos Permanentes</option>
                    <option value="1012">Producción De Frutales</option>
                    <option value="1013">Producción De Plantas Medicinales</option>
                    <option value="1014">Producción De Cereales</option>
                    <option value="1015">Producción De Otros Cultivos Transitorios</option>
                    <option value="1016">Producción De Hortalizas</option>
                    <option value="1017">Distribución de las áreas cosechadas</option>
                    <option value="1018">Número de hectáreas cosechada de cultivos de fibras</option>
                    <option value="1019">Participación área cosechada de cultivos de leguminosas</option>
                    <option value="1020">Participación área cosechada de cultivos de plantas medicinales</option>
                    <option value="1021">Participación área cosechada de tubérculos</option>
                    <option value="1022">Participación área cosechada flores y follajes</option>
                    <option value="1023">Participación área cosechada frutales</option>
                    <option value="1024">Rendimiento en toneladas por hectárea cosechada de hortalizas</option>
                    <option value="1025">Participación en área cosechada de otros cultivos permanentes</option>
                    <option value="1026">Participación área cosechada otros cultivos transitorios</option>
                    <option value="1028">Participación áreas sembradas de fibras</option>
                    <option value="1029">Participación área sembrada de frutales</option>
                    <option value="1030">Participación área sembrada de hortalizas</option>
                    <option value="1031">Participación área sembrada de leguminosas</option>
                    <option value="1032">Participación área sembrada de otros cultivos permanentes</option>
                    <option value="1033">Participación área sembrada de otros cultivos transitorios</option>
                    <option value="1034">Participación área sembrada de plantas medicinales</option>
                    <option value="1035">Participación área sembrada de tubérculos</option>
                    <option value="1036">Participación área sembrada flores y follajes</option>
                    <option value="1038">Rendimiento de frutales</option>
                    <option value="1039">Rendimiento de hortalizas</option>
                    <option value="1040">Rendimiento de otros cultivos permanentes</option>
                    <option value="1041">Rendimiento de cereales</option>
                    <option value="1042">Rendimiento de fibras</option>
                    <option value="1043">Rendimiento de flores y follajes</option>
                    <option value="1044">Rendimiento de tubérculos y plátanos</option>
                    <option value="1045">Rendimiento de leguminosas</option>
                    <option value="1046">Rendimiento de otros cultivos transitorios</option>
                    <option value="1047">Rendimiento de plantas medicinales</option>
                    <option value="1055">Porcentaje De Cultivos Transitorios Según Producción (Toneladas - Tn)</option>
                    <option value="1057">Producción Por Provincias</option>
                    <option value="1058">Índice Departamental De Competitividad</option>
                    <option value="1059">Índice Departamental De Competitividad Pilar 1 Instituciones</option>
                    <option value="1061">Índice Departamental De Competitividad Pilar 2 Infraestructura</option>
                    <option value="1062">Índice Departamental De Competitividad Pilar 3 TIC</option>
                    <option value="1063">Índice Departamental De Competitividad Pilar 4 Sostenibilidad Ambiental</option>
                    <option value="1064">Índice Departamental De Competitividad Pilar 5 Salud</option>
                    <option value="1065">Índice Departamental De Competitividad Pilar 6 Educación básica y media</option>
                    <option value="1066">Índice Departamental De Competitividad Pilar 7 Educación Superior y Capacitación</option>
                    <option value="1067">Índice Departamental De Competitividad Pilar 8 Eficiencia de los mercados</option>
                    <option value="1068">Índice Departamental De Competitividad Pilar 9 Mercado Laboral</option>
                    <option value="1069">Índice Departamental De Competitividad Pilar 10 Sistema Financiero</option>
                    <option value="1070">Índice Departamental De Competitividad Pilar 11 Tamaño del mercado</option>
                    <option value="1071">Índice Departamental De Competitividad Pilar 12 Diversificación y Sofisticación</option>
                    <option value="1072">Índice Departamental De Competitividad Pilar 13 Innovación</option>
                    <option value="1077">Total De Inversión Según Destinación Específica Compromisos y Gastos FUT</option>
                    <option value="1079">Índice De Desempeño Fiscal Por Año</option>
                    <option value="1080">Desempeño Fiscal Posición A Nivel Nacional</option>
                    <option value="1081">Rango De Clasificación Del Desempeño Fiscal</option>
                    <option value="1082">Ejecución De Gastos - Sistema General De Regalías</option>
                    <option value="1083">Ejecución De Ingresos - Sistema General De Regalías</option>
                    <option value="1084">Deudas Por Pagar FUT</option>
                    <option value="1085">Clasificación En Grupo De Capacidades</option>
                    <option value="1086">Puesto De Grupo De Capacidades</option>
                    <option value="1087">Puntaje En Gestión</option>
                    <option value="1088">Puntaje En Resultados</option>
                    <option value="1089">Puntaje General MDM Por Año</option>
                    <option value="1096">Total De Inversión Según Destinación Especifica FUT</option>
                    <option value="1112">Inventario Avícola</option>
                    <option value="1113">Inventario Bovino</option>
                    <option value="1114">Inventario Otras Especies</option>
                    <option value="1116">Ciudad De Procedencia</option>
                    <option value="1119">Llegada De Visitantes Extranjeros No Residentes Por Departamento Destino</option>
                    <option value="1120">Motivo Del Viaje (Naturaleza, Aventura, Cultural, Bienestar, Trabajo Y Otros)</option>
                    <option value="1125">Tiempo De Permanencia</option>
                    <option value="1126">Tipo De Alojamiento</option>
                    <option value="1127">Tipo De Transporte</option>
                    <option value="1128">Variaciones Anuales Del Personal Total Y Por Categoría</option>
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
                        Agropecuario - Producción Agrícola
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1002"> Volumen de producción agrícola total</a></li>
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1003"> Producción agrícola cultivos permanentes</a></li>
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1008"> Producción de leguminosas</a></li>
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1009"> Producción de tubérculos</a></li>
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1010"> Producción de fibras</a></li>
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1011"> Producción de otros cultivos permanentes</a></li>
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1012"> Producción de frutales</a></li>
                            <li class="icon-list-item"><span class="icon">🌿</span><a href="./indicador.php?id=1013"> Producción de plantas medicinales</a></li>
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1014"> Producción de cereales</a></li>
                            <li class="icon-list-item"><span class="icon">🌽</span><a href="./indicador.php?id=1015"> Producción de otros cultivos transitorios</a></li>
                            <li class="icon-list-item"><span class="icon">🥦</span><a href="./indicador.php?id=1016"> Producción de hortalizas</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1055"> % cultivos transitorios (toneladas)</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1057"> Producción por provincias</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Agropecuario - Participación Agrícola -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Agropecuario - Participación Agrícola
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1017"> Distribución de las área cosechada</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1018"> Hectáreas cosechadas de fibras</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1019"> Área cosechada leguminosas</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1020"> Área cosechada plantas medicinales</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1021"> Área cosechada tubérculos</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1022"> Área cosechada flores y follajes</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1023"> Área cosechada frutales</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1024"> Área cosechada hortalizas</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1025"> Área cosechada otros cultivos permanentes</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1026"> Área cosechada otros cultivos transitorios</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Agropecuario - Rendimiento -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Agropecuario - Rendimiento
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1038"> Rendimiento de frutales</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1039"> Rendimiento de hortalizas</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1040"> Rendimiento de otros cultivos permanentes</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1041"> Rendimiento de cereales</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1042"> Rendimiento de fibras</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1043"> Rendimiento de flores y follajes</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1044"> Rendimiento de tubérculos y plátanos</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1045"> Rendimiento de leguminosas</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1046"> Rendimiento de otros cultivos transitorios</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1047"> Rendimiento de plantas medicinales</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Agropecuario - Pecuario -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Agropecuario - Pecuario
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🐄</span><a href="./indicador.php?id=1112"> Inventario avícola</a></li>
                            <li class="icon-list-item"><span class="icon">🐄</span><a href="./indicador.php?id=1113"> Inventario bovino</a></li>
                            <li class="icon-list-item"><span class="icon">🐄</span><a href="./indicador.php?id=1114"> Inventario otras especies</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Finanzas públicas departamentales -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Finanzas públicas departamentales
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1077"> Total de inversión según destinación específica compromisos y gastos FUT</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1084"> Deudas por pagar FUT</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1096"> Total de inversión según destinación específica FUT</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1082"> Ejecución de gastos - sistema general de regalías</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1083"> Ejecución de ingresos - sistema general de regalías</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Desempeño municipal -->
                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Desempeño municipal
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1079"> Índice de desempeño fiscal por año</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1080"> Desempeño fiscal posición a nivel nacional</a></li>
                            <li class="icon-list-item"><span class="icon">📊</span><a href="./indicador.php?id=1081">
                    </div>
                </div>
            </div>
        </div>

                <!-- ⭐ TAB 4: DATALAKE -->
                <div class="tab-pane fade econ-pane econ-pane--highlight" id="datalake" role="tabpanel">

                    <h2 class="indicador-title mb-3">DATALAKE Económico</h2>
                    <p class="description mb-4">
                        Descargue los archivos CSV abiertos del Observatorio Económico de Boyacá, organizados por categoría.
                    </p>

                    <?php
                    // MATRIZ DE CATEGORÍAS E INDICADORES → GENERA LA LISTA AUTOMÁTICA
                    $datalakeCategories = [

                        "Variables macroeconómicas" => [
                            "1001" => "Producto Interno Bruto del Departamento",
                        ],

                        "Agropecuario – Producción Agrícola" => [
                            "1002" => "Volumen de producción agrícola total",
                            "1003" => "Producción agrícola cultivos permanentes",
                            "1008" => "Producción de leguminosas",
                            "1009" => "Producción de tubérculos",
                            "1010" => "Producción de fibras",
                            "1011" => "Producción de otros cultivos permanentes",
                            "1012" => "Producción de frutales",
                            "1013" => "Producción de plantas medicinales",
                            "1014" => "Producción de cereales",
                            "1015" => "Producción de otros cultivos transitorios",
                            "1016" => "Producción de hortalizas",
                            "1055" => "% cultivos transitorios (toneladas)",
                            "1057" => "Producción por provincias",
                        ],

                        "Agropecuario – Participación Agrícola" => [
                            "1017" => "Distribución área cosechada",
                            "1018" => "Hectáreas cosechadas de fibras",
                            "1019" => "Área cosechada leguminosas",
                            "1020" => "Área cosechada plantas medicinales",
                            "1021" => "Área cosechada tubérculos",
                            "1022" => "Área cosechada flores y follajes",
                            "1023" => "Área cosechada frutales",
                            "1024" => "Área cosechada hortalizas",
                            "1025" => "Área cosechada otros cultivos permanentes",
                            "1026" => "Área cosechada otros cultivos transitorios",
                        ],

                        "Agropecuario – Rendimiento" => [
                            "1038" => "Rendimiento frutales",
                            "1039" => "Rendimiento hortalizas",
                            "1040" => "Rendimiento otros cultivos permanentes",
                            "1041" => "Rendimiento cereales",
                            "1042" => "Rendimiento fibras",
                            "1043" => "Rendimiento flores y follajes",
                            "1044" => "Rendimiento tubérculos y plátanos",
                            "1045" => "Rendimiento leguminosas",
                            "1046" => "Rendimiento otros cultivos transitorios",
                            "1047" => "Rendimiento plantas medicinales",
                        ],

                        "Agropecuario – Pecuario" => [
                            "1112" => "Inventario avícola",
                            "1113" => "Inventario bovino",
                            "1114" => "Inventario otras especies",
                        ],

                        "Finanzas Públicas Departamentales" => [
                            "1077" => "Total inversión FUT (compromisos y gastos)",
                            "1084" => "Deudas por pagar FUT",
                            "1096" => "Total inversión FUT (general)",
                            "1082" => "Ejecución gastos SGR",
                            "1083" => "Ejecución ingresos SGR",
                        ],

                        "Desempeño Municipal" => [
                            "1079" => "Índice desempeño fiscal",
                            "1080" => "Posición desempeño fiscal nacional",
                            "1081" => "Clasificación desempeño fiscal",
                            "1085" => "Clasificación grupo capacidades",
                            "1086" => "Puesto grupo capacidades",
                            "1087" => "Puntaje en gestión",
                            "1088" => "Puntaje en resultados",
                            "1089" => "Puntaje general MDM",
                        ],

                        "Servicios y Turismo" => [
                            "1116" => "Ciudad de procedencia",
                            "1119" => "Llegada de visitantes extranjeros",
                            "1120" => "Motivo del viaje",
                            "1125" => "Tiempo de permanencia",
                            "1126" => "Tipo de alojamiento",
                            "1127" => "Tipo de transporte",
                            "1128" => "Variación anual del personal total",
                        ],

                        "Minería" => [
                            "1004" => "Producción minera",
                            "1005" => "Exportaciones mineras",
                            "1006" => "Importaciones mineras",
                            "1007" => "Regalías mineras",
                        ],

                        "Competitividad" => [
                            "1058" => "Índice departamental de competitividad",
                            "1059" => "Pilar 1: Instituciones",
                            "1061" => "Pilar 2: Infraestructura",
                            "1062" => "Pilar 3: TIC",
                            "1063" => "Pilar 4: Sostenibilidad Ambiental",
                            "1064" => "Pilar 5: Salud",
                            "1065" => "Pilar 6: Educación básica y media",
                            "1066" => "Pilar 7: Educación superior",
                            "1067" => "Pilar 8: Eficiencia mercados",
                            "1068" => "Pilar 9: Mercado laboral",
                            "1069" => "Pilar 10: Sistema financiero",
                            "1070" => "Pilar 11: Tamaño del mercado",
                            "1071" => "Pilar 12: Diversificación",
                            "1072" => "Pilar 13: Innovación",
                        ],
                    ];
                    ?>

                    <!-- 📂 LISTA GENERADA DE ARCHIVOS CSV -->
                    <div class="datalake-list mt-4">

                        <?php foreach ($datalakeCategories as $category => $indicators) { ?>

                            <div class="datalake-block mb-5 p-4 shadow-sm rounded">

                                <h4 class="mb-3">📁 <?= $category; ?></h4>

                                <ul class="datalake-ul">

                                    <?php foreach ($indicators as $id => $label) { ?>

                                        <li class="datalake-item d-flex align-items-center py-2">

                                            <span class="dl-icon me-2">📎</span>

                                            <span class="dl-text flex-grow-1"><?= $label; ?></span>

                                            <!-- 📥 DESCARGA DIRECTA DEL CSV -->
                                            <a 
                                                href="/datalake/<?= $id ?>.csv"
                                                download="<?= $label ?>.csv"
                                                class="btn btn-outline-dark btn-sm ms-3"
                                            >
                                                📥 Descargar CSV
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
        "categoria1": "Variables macroeconómicas",
        "categoria2": "PIB",
        "etiqueta": "ND",
        "unidad": "Miles De Millones De Pesos",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producto Interno Bruto del Departamento",
        "fecha": "2015 - 2020",
        "fuente": "No especificada"
    },
    "1002": {
        "codigo": "1002",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Hectáreas y toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Volumen de producción agrícola total del departamento",
        "fecha": "2020",
        "fuente": "No especificada"
    },
    "1003": {
        "codigo": "1003",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Hectáreas y toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción agrícola de cultivos permanentes",
        "fecha": "2022",
        "fuente": "No especificada"
    },
    "1004": {
        "codigo": "1004",
        "observatorio": "Económico",
        "categoria1": "Minería",
        "categoria2": "Producción",
        "etiqueta": "ND",
        "unidad": "Kilates, toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción Minera Boyacá",
        "fecha": "2015 - 2021",
        "fuente": "No especificada"
    },
    "1005": {
        "codigo": "1005",
        "observatorio": "Económico",
        "categoria1": "Minería",
        "categoria2": "Exportaciones",
        "etiqueta": "ND",
        "unidad": "Miles de dólares",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Exportaciones minera de Boyacá",
        "fecha": "2015 - 2021",
        "fuente": "No especificada"
    },
    "1006": {
        "codigo": "1006",
        "observatorio": "Económico",
        "categoria1": "Minería",
        "categoria2": "Importaciones",
        "etiqueta": "ND",
        "unidad": "Miles de dólares",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Importaciones Mineras",
        "fecha": "2015 - 2021",
        "fuente": "No especificada"
    },
    "1007": {
        "codigo": "1007",
        "observatorio": "Económico",
        "categoria1": "Minería",
        "categoria2": "Regalías",
        "etiqueta": "ND",
        "unidad": "Pesos",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Asignación de regalías mineras",
        "fecha": "2021",
        "fuente": "No especificada"
    },
    "1008": {
        "codigo": "1008",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción de leguminosas",
        "fecha": "2019 - 2021",
        "fuente": "No especificada"
    },
    "1009": {
        "codigo": "1009",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción total de tubérculos",
        "fecha": "2007 - 2021",
        "fuente": "No especificada"
    },
    "1010": {
        "codigo": "1010",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción de fibras",
        "fecha": "2014 - 2018",
        "fuente": "No especificada"
    },
    "1011": {
        "codigo": "1011",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción de otros cultivos permanentes",
        "fecha": "2007 - 2018",
        "fuente": "No especificada"
    },
    "1012": {
        "codigo": "1012",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción de frutales",
        "fecha": "2007 - 2021",
        "fuente": "No especificada"
    },
    "1013": {
        "codigo": "1013",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción de plantas medicinales",
        "fecha": "2007 - 2021",
        "fuente": "No especificada"
    },
    "1014": {
        "codigo": "1014",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción de cereales",
        "fecha": "2007 - 2021",
        "fuente": "No especificada"
    },
    "1015": {
        "codigo": "1015",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción de otros cultivos transitorios",
        "fecha": "2007 - 2020",
        "fuente": "No especificada"
    },
    "1016": {
        "codigo": "1016",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Producción agrícola",
        "etiqueta": "ND",
        "unidad": "Toneladas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Producción de hortalizas",
        "fecha": "2007 - 2021",
        "fuente": "No especificada"
    },
    "1017": {
        "codigo": "1017",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Participación agrícola",
        "etiqueta": "Área cosechada",
        "unidad": "Hectáreas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Distribución de las áreas cosechadas",
        "fecha": "2006 - 2021",
        "fuente": "No especificada"
    },
    "1018": {
        "codigo": "1018",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Participación agrícola",
        "etiqueta": "Área cosechada",
        "unidad": "Hectáreas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Número de hectáreas cosechada de cultivos de fibras",
        "fecha": "2019 - 2020",
        "fuente": "No especificada"
    },
    "1019": {
        "codigo": "1019",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Participación agrícola",
        "etiqueta": "Área cosechada",
        "unidad": "Hectáreas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Participación área cosechada de cultivos de leguminosas",
        "fecha": "2019 - 2020",
        "fuente": "No especificada"
    },
    "1020": {
        "codigo": "1020",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Participación agrícola",
        "etiqueta": "Área cosechada",
        "unidad": "Hectáreas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Participación área cosechada de cultivos de plantas medicinales",
        "fecha": "2019 - 2020",
        "fuente": "No especificada"
    },
    "1021": {
        "codigo": "1021",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Participación agrícola",
        "etiqueta": "Área cosechada",
        "unidad": "Hectáreas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Participación área cosechada de tubérculos",
        "fecha": "2019 - 2021",
        "fuente": "No especificada"
    },
    "1022": {
        "codigo": "1022",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Participación agrícola",
        "etiqueta": "Área cosechada",
        "unidad": "Hectáreas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Participación área cosechada flores y follajes",
        "fecha": "2007 - 2018",
        "fuente": "No especificada"
    },
    "1023": {
        "codigo": "1023",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Participación agrícola",
        "etiqueta": "Área cosechada",
        "unidad": "Hectáreas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Participación área cosechada frutales",
        "fecha": "2019 - 2021",
        "fuente": "No especificada"
    },
    "1024": {
        "codigo": "1024",
        "observatorio": "Económico",
        "categoria1": "Agropecuario",
        "categoria2": "Participación agrícola",
        "etiqueta": "Área cosechada",
        "unidad": "Hectáreas",
        "desagregacion": "No disponible",
        "geografia": "Departamento de Boyacá",
        "definicion": "Rendimiento en toneladas por hectárea cosechada de hortalizas",
        "fecha": "2019 - 2021",
        "fuente": "No especificada"
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
