<?php
require_once 'tracker.php';
/* La Hoja de Vida de esta página se armaba a mano dentro de un objeto de
   JavaScript, y se desalineó dos veces: los códigos dejaron de ser los de las
   carpetas publicadas y la lista acabó mezclando indicadores de otro
   observatorio. Ahora se construye desde las carpetas, que son las que abre
   indicador.php, de modo que el código que se muestra es el que lleva al
   indicador. */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/lib/indicator_metadata.php';
require_once __DIR__ . '/config/database.php';
$hvPdo = function_exists('cms_pdo') ? @cms_pdo() : null;
$hvIndicadores = im_hoja_vida_publicada(1, __DIR__, $hvPdo);
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
<?php foreach ($hvIndicadores as $hvCod => $hvInd): ?>
                    <option value="<?= $hvCod ?>"><?= $hvCod ?> &middot; <?= htmlspecialchars($hvInd['titulo']) ?></option>
<?php endforeach; ?>
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
                                <a href="./indicador.php?id=1902">Producto Interno Bruto por departamento</a>
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
                            <li class="icon-list-item"><span class="icon">📚</span><a href="./indicador.php?id=1000"> Analfabetismo</a></li>
                            <li class="icon-list-item"><span class="icon">📘</span><a href="./indicador.php?id=1001"> Bajo logro educativo</a></li>
                            <li class="icon-list-item"><span class="icon">🍼</span><a href="./indicador.php?id=1002"> Barreras a servicios de cuidado para la primera infancia</a></li>
                            <li class="icon-list-item"><span class="icon">🏫</span><a href="./indicador.php?id=1006"> Inasistencia escolar</a></li>
                            <li class="icon-list-item"><span class="icon">📖</span><a href="./indicador.php?id=1007"> Rezago escolar</a></li>
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
                            <li class="icon-list-item"><span class="icon">🏥</span><a href="./indicador.php?id=1003"> Barreras de acceso a servicios de salud</a></li>
                            <li class="icon-list-item"><span class="icon">🚑</span><a href="./indicador.php?id=1009"> Sin aseguramiento en salud</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Vivienda – Pobreza Multidimensional
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🏚️</span><a href="./indicador.php?id=1004"> Porcentaje de hacinamiento crítico</a></li>
                            <li class="icon-list-item"><span class="icon">🚽</span><a href="./indicador.php?id=1005"> Inadecuada eliminación de excretas</a></li>
                            <li class="icon-list-item"><span class="icon">🧱</span><a href="./indicador.php?id=1011"> Material inadecuado de paredes exteriores</a></li>
                            <li class="icon-list-item"><span class="icon">🧱</span><a href="./indicador.php?id=1012"> Material inadecuado de pisos</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Servicios Públicos – Pobreza Multidimensional
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🚱</span><a href="./indicador.php?id=1008"> Sin acceso a fuente de agua mejorada</a></li>
                        </ul>
                    </div>
                </div>


                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Mercado Laboral – Pobreza Multidimensional
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">📉</span><a href="./indicador.php?id=1010"> Desempleo de larga duración</a></li>
                            <li class="icon-list-item"><span class="icon">🧒</span><a href="./indicador.php?id=1013"> Trabajo infantil</a></li>
                            <li class="icon-list-item"><span class="icon">👷</span><a href="./indicador.php?id=1014"> Trabajo informal</a></li>
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
                            <li class="icon-list-item"><span class="icon">🏠</span><a href="./indicador.php?id=1145"> Viviendas, hogares y personas en hogares campesinos</a></li>
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
                            <li class="icon-list-item"><span class="icon">📶</span><a href="./indicador.php?id=1166"> Uso de internet por frecuencia</a></li>
                            <li class="icon-list-item"><span class="icon">🎓</span><a href="./indicador.php?id=1167"> Promedio de años de educación</a></li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Bienestar Subjetivo y Condiciones Familiares
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🙂</span><a href="./indicador.php?id=1168"> Satisfacción con la vida y otros aspectos</a></li>
                            <li class="icon-list-item"><span class="icon">👨‍👩‍👧</span><a href="./indicador.php?id=1115"> Hogares según sexo del jefe/a y presencia de hijos</a></li>
                            <li class="icon-list-item"><span class="icon">👥</span><a href="./indicador.php?id=1169"> Actividad principal por sexo (15 años y más)</a></li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Desempeño Municipal – Gestión y Resultados
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🏛️</span><a href="./indicador.php?id=1506"> Medición de Desempeño Municipal (MDM)</a></li>
                            <li class="icon-list-item"><span class="icon">🧩</span><a href="./indicador.php?id=1509"> Grupo de capacidades iniciales del municipio</a></li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                        Agropecuario – Producción Agrícola
                    </button>
                    <div class="accordion-content">
                        <ul class="icon-list-items">
                            <li class="icon-list-item"><span class="icon">🌱</span><a href="./indicador.php?id=1998"> Volumen de producción agrícola total</a></li>
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
                            <li class="icon-list-item"><span class="icon">🐄</span><a href="./indicador.php?id=1113"> Inventario bovino</a></li>
                            <li class="icon-list-item"><span class="icon">🐃</span><a href="./indicador.php?id=1203"> Inventario bufalino</a></li>
                            <li class="icon-list-item"><span class="icon">🐔</span><a href="./indicador.php?id=1112"> Inventario avícola</a></li>
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
                            <li class="icon-list-item"><span class="icon">💸</span><a href="./indicador.php?id=1501"> Gastos de funcionamiento municipales</a></li>
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
                            <li class="icon-list-item"><span class="icon">🏷️</span><a href="./indicador.php?id=1507"> Categoría de ley de los municipios</a></li>
                            <li class="icon-list-item"><span class="icon">📶</span><a href="./indicador.php?id=1508"> Rango de clasificación según el IDF</a></li>
                            <li class="icon-list-item"><span class="icon">📈</span><a href="./indicador.php?id=1510"> Variación del índice de eficacia municipal</a></li>
                            <li class="icon-list-item"><span class="icon">🏦</span><a href="./indicador.php?id=1511"> Viabilidad del ICLD (Ley 617)</a></li>
                            <li class="icon-list-item"><span class="icon">✅</span><a href="./indicador.php?id=1512"> Cumplimiento de requisitos legales del SGP</a></li>
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
const indicatorsData = <?= json_encode($hvIndicadores, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

// --------------------------------------------------
// Mostrar información del indicador seleccionado
// --------------------------------------------------
function showIndicatorInfo() {
    const select = document.getElementById("indicatorSelect");
    const selectedId = select.value;
    const infoContainer = document.getElementById("indicatorInfo");

    if (!selectedId || !indicatorsData[selectedId]) {
        infoContainer.innerHTML = "<p>Seleccione un indicador para ver la informacion.</p>";
        return;
    }
    const data = indicatorsData[selectedId];
    const fila = (etiqueta, valor) => valor
        ? `<p><strong>${etiqueta}:</strong> ${valor}</p>` : '';
    infoContainer.innerHTML = `
        <h3>${data.codigo} &middot; ${data.titulo}</h3>
        ${fila('Observatorio', data.observatorio)}
        ${fila('Categoria', data.categoria1)}
        ${fila('Subcategoria', data.categoria2)}
        ${fila('Etiquetas', data.etiqueta)}
        ${fila('Unidad de medida', data.unidad)}
        ${fila('Desagregacion', data.desagregacion)}
        ${fila('Ambito geografico', data.geografia)}
        ${fila('Definicion', data.definicion)}
        ${fila('Calculo', data.calculo)}
        ${fila('Periodo de datos', data.fecha)}
        ${fila('Periodicidad', data.periodicidad)}
        ${fila('Fuente', data.fuente)}
        ${fila('Entidad responsable', data.entidad_responsable)}
        ${fila('Observaciones', data.observaciones)}
        <p class="mt-3"><a class="btn btn-sm btn-outline-primary"
           href="indicador.php?id=${data.codigo}">Ver las ${data.graficas} grafica(s) del indicador</a></p>
    `;
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