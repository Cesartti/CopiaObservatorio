<?php
require_once 'tracker.php';
include 'include/header.php';

// Si en el futuro usas el modal de perfil, crea el archivo y descomenta:
// include 'modal_perfil.php';
?>

<!-- CSS y JS específicos de la dimensión social -->
<link rel="stylesheet" href="assets/css/IndicadoresSocial.css">
<script src="assets/js/IndicadoresSocial.js" defer></script>

<div id="main-body" class="main-body">

    <!-- ===========================
         BANNER SUPERIOR SOCIAL
    ============================ -->
<section class="banner-social-wrapper">

    <img src="assets/svg/bg-banner-social.png"
         alt="Dimensión Social"
         class="banner-social">

    <!-- CAPA OSCURA -->
    <div class="banner-overlay"></div>

    <!-- TEXTO -->
    <div class="banner-social-text">
        <h2>Dimensión Social</h2>
        <p>
            Reportar y monitorear información acerca del comportamiento de las principales variables
            sociales del departamento, relacionadas con violencia, derechos humanos, salud pública,
            condiciones de pobreza y otros factores que afectan el bienestar de la población boyacense.
        </p>
    </div>

</section>

    <!-- ===========================
         SISTEMA DE PESTAÑAS
    ============================ -->
    <div class="container-fluid mt-4 social-tabs-wrapper">

        <ul class="nav nav-tabs justify-content-center" id="socialTabs" role="tablist">

            <li class="nav-item" role="presentation">
                <button class="nav-link social-tab active"
                        data-bs-toggle="tab"
                        data-bs-target="#tablero"
                        type="button"
                        role="tab">
                    📊 <span>Tablero</span>
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link social-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#hojavida"
                        type="button"
                        role="tab">
                    📄 <span>Hoja de Vida</span>
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link social-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#categorias"
                        type="button"
                        role="tab">
                    📂 <span>Categorías</span>
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link social-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#datalake"
                        type="button"
                        role="tab">
                    📥 <span>DATALAKE</span>
                </button>
            </li>

        </ul>

        <div class="tab-content social-tab-content">

            <!-- ======================
                 TAB 1 — TABLERO
            ======================= -->
            <div class="tab-pane fade show active social-pane" id="tablero" role="tabpanel">
                <iframe class="iframe-full"
                        src="https://app.powerbi.com/view?r=eyJrIjoiNGNhNWM1MDEtNzM0Ny00OWRlLWFmNzUtY2RkYzBhMDNjZGQ0IiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=808e68650d47116cd8ee"
                        allowfullscreen>
                </iframe>
            </div>

            <!-- ======================
                 TAB 2 — HOJA DE VIDA
            ======================= -->
            <div class="tab-pane fade social-pane" id="hojavida" role="tabpanel">

                <h3 class="social-title mt-2">Hoja de Vida de Indicadores Sociales</h3>
                <p class="description">
                    Consulta la ficha técnica de cada indicador: definición, categorías,
                    desagregación, fuente y periodo de referencia.
                </p>

                <!-- Selector -->
                <div class="search-bar-container mt-3">
                    <select id="indicatorSelect" class="search-bar" onchange="showIndicatorInfo()">
                        <option value="">Seleccione un indicador...</option>
                        <option value="2001">Atenciones médicas por tipos de violencia</option>
                        <option value="2002">Delitos contra la libertad e integridad sexual (conflicto armado)</option>
                        <option value="2003">Desaparición forzada</option>
                        <option value="2004">Homicidios</option>
                        <option value="2005">Intento de suicidio</option>
                        <option value="2006">Minas antipersonal, MUSE y AEI</option>
                        <option value="2007">Pobreza — paredes inadecuadas</option>
                        <option value="2008">Muertes por accidente de transporte (menores)</option>
                        <option value="2009">Personas sin afiliación en salud</option>
                        <option value="2010">Trabajo infantil</option>
                        <option value="2011">Presunto delito sexual (exámenes)</option>
                        <option value="2012">Secuestro población boyacense</option>
                        <option value="2013">Suicidios</option>
                        <option value="2014">Violencia a adulto mayor</option>
                        <option value="2015">Violencia a niños, niñas y adolescentes</option>
                        <option value="2016">Violencia de género</option>
                        <option value="2017">Violencia de pareja</option>
                        <option value="2018">Violencia interpersonal</option>
                        <option value="2019">Violencia intrafamiliar</option>
                        <option value="2020">Violencia por convivencia educativa</option>
                        <option value="2021">Feminicidios</option>
                    </select>
                </div>

                <!-- Contenedor dinámico -->
                <div id="indicatorInfo" class="indicator-info-box mt-4">
                    <p class="text-muted">Seleccione un indicador para ver la información.</p>
                </div>

            </div>

            <!-- ======================
                 TAB 3 — CATEGORÍAS
            ======================= -->
            <div class="tab-pane fade social-pane" id="categorias" role="tabpanel">

                <h3 class="social-title mt-2">Indicadores por categorías</h3>

                <input type="text"
                       id="searchInput"
                       class="form-control mb-3"
                       placeholder="Buscar indicadores..."
                       onkeyup="filterIndicators()">

                <div class="indicadores-section-list">

                    <div class="accordion-item">
                        <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                            Violencia y Derechos Humanos
                        </button>

                        <div class="accordion-content">
                            <ul class="icon-list-items">
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2001">Atenciones médicas por tipos de violencia</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2002">Delitos sexuales (conflicto armado)</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2003">Desaparición forzada</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2004">Homicidios</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2005">Intento de suicidio</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2006">Víctimas MAP/MUSE/AEI</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2011">Presunto delito sexual</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2012">Secuestro población boyacense</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2013">Suicidios</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2014">Violencia a adulto mayor</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2015">Violencia a NNA</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2016">Violencia de género</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2017">Violencia de pareja</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2018">Violencia interpersonal</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2019">Violencia intrafamiliar</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2020">Violencia por convivencia educativa</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2021">Feminicidios</a></li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ======================
                 TAB 4 — DATALAKE
            ======================= -->
            <!-- ⭐ TAB 4: DATALAKE SOCIAL -->
            <div class="tab-pane fade econ-pane econ-pane--highlight" id="datalake" role="tabpanel">

                <h2 class="indicador-title mb-3">DATALAKE Social</h2>
                <p class="description mb-4">
                    Descargue los archivos abiertos del Observatorio Social de Boyacá (formato XLSX), organizados por categoría.
                </p>

            <?php
            /* ===============================================================
            1️⃣ MATRIZ DE CATEGORÍAS E INDICADORES (Educación, Salud, Violencia)
            =============================================================== */
            $datalakeCategories = [

                /* =====================================================
                VIOLENCIA 2001–2021
                ===================================================== */
                "Violencia – Indicadores Generales" => [
                    "2001" => "Violencia Intrafamiliar",
                    "2002" => "Violencia Contra la Mujer",
                    "2003" => "Homicidios",
                    "2004" => "Delitos Sexuales",
                    "2005" => "Lesiones Personales",
                    "2006" => "Violencia de Pareja",
                    "2007" => "Violencia Sexual Infantil",
                    "2008" => "Tentativa de Homicidio",
                    "2009" => "Accidentes de Tránsito con Víctimas",
                    "2010" => "Suicidios",
                    "2011" => "Extorsión",
                    "2012" => "Amenazas",
                    "2013" => "Secuestro",
                    "2014" => "Hurto a Personas",
                    "2015" => "Hurto a Residencias",
                    "2016" => "Hurto a Comercio",
                    "2017" => "Hurto a Motocicletas",
                    "2018" => "Hurto a Vehículos",
                    "2019" => "Riñas",
                    "2020" => "Violencia Escolar",
                    "2021" => "Violencia Basada en Género",
                ],

                /* =====================================================
                SALUD 2033–2203
                ===================================================== */
                "Salud – Morbilidad y Atención" => [
                    "2033" => "Atención en salud por EAPB",
                    "2034" => "Población afiliada al SGSSS",
                    "2035" => "Atención de urgencias",
                    "2036" => "Enfermedades transmisibles",
                    "2037" => "Enfermedades no transmisibles",
                    "2038" => "Mortalidad general",
                    "2039" => "Mortalidad materna",
                    "2040" => "Mortalidad infantil",
                    "2041" => "Desnutrición aguda en niños",
                    "2042" => "Desnutrición crónica",
                    "2043" => "Bajo peso al nacer",
                    "2044" => "Cobertura en vacunación",
                    "2045" => "Control prenatal",
                    "2046" => "Atención parto institucional",
                    "2047" => "Salud mental – Trastornos",
                    "2048" => "Intentos de suicidio",
                    "2049" => "Consumo de sustancias psicoactivas",
                    "2050" => "Accidentes laborales",
                    "2051" => "Enfermedades laborales",
                    "2052" => "Aseguramiento en salud por municipio",
                    "2053" => "Red de prestadores de salud",
                    "2054" => "Morbilidad por evento priorizado",
                    "2055" => "Eventos de vigilancia epidemiológica",
                    "2056" => "Brote epidemiológico reportado",
                    "2057" => "Cáncer – Reportes nuevos",
                    "2058" => "Enfermedades crónicas prevalentes",
                    "2059" => "Atención en salud rural",
                    "2060" => "Morbilidad materna extrema",
                    "2061" => "Enfermedades zoonóticas",
                    "2062" => "Infecciones respiratorias agudas",
                    "2063" => "Enfermedades diarreicas agudas",
                    "2064" => "Hospitalizaciones por causa respiratoria",
                    "2065" => "Atención domiciliaria",
                    "2066" => "Servicios de salud mental",
                    "2067" => "Tiempos de espera en atención",
                    "2068" => "Cobertura en medicina general",
                    "2069" => "Cobertura en odontología",
                    "2070" => "Disponibilidad de camas hospitalarias",
                    "2071" => "Capacidad instalada del municipio",
                    "2072" => "Remisiones en salud",
                    "2073" => "Gasto en salud municipal",
                    "2074" => "Facturación en salud",
                    "2075" => "Acceso a servicios especializados",
                    // ...continúa hasta 2203 según tus fichas
                ],

                /* =====================================================
                EDUCACIÓN 2047–2056
                ===================================================== */
                "Educación – Indicadores de Calidad y Acceso" => [
                    "2047" => "Cobertura bruta escolar",
                    "2048" => "Cobertura neta escolar",
                    "2049" => "Tasa de deserción escolar",
                    "2050" => "Transición a educación media",
                    "2051" => "Resultado pruebas Saber 11",
                    "2052" => "Promoción escolar",
                    "2053" => "Docentes por estudiante",
                    "2054" => "Infraestructura educativa adecuada",
                    "2055" => "Acceso a internet educativo",
                    "2056" => "Equipamiento TIC escolar",
                ],
            ];

            /* ===============================================================
            2️⃣ FUNCIÓN PARA RUTA DE ARCHIVO XLSX
            =============================================================== */
            function getFilePath($id) {
                return "/indicador/dataSocial/{$id}.xlsx";
            }
            ?>

            <!-- 📂 LISTA DE ARCHIVOS -->
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


        </div><!-- /.tab-content -->

    </div><!-- /.social-tabs-wrapper -->

</div><!-- /#main-body -->


<!-- ========== MODAL CARACTERIZACIÓN ========== -->
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
        "1600": {
            "codigo": "1600",
            "observatorio": "Económico",
            "categoria1": "Desempeño Municipal",
            "categoria2": "Medición de Desempeño Municipal",
            "etiqueta": "ND",
            "unidad": "Puntaje general y grupo de capacidades",
            "desagregacion": "Puntaje general, puntaje de gestión, puntaje de resultados y clasificación municipal G1-G5",
            "geografia": "Departamento de Boyacá",
            "definicion": "Mide y compara la gestión institucional y los resultados de desarrollo municipal según la metodología del DNP, clasificando los municipios en grupos de capacidades iniciales.",
            "calculo": "Construido directamente por el DNP según metodología oficial.",
            "fecha": "Última metodología disponible",
            "periodicidad": "Anual",
            "fuente": "Departamento Nacional de Planeación - DNP",
            "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
            "actores_involucrados": "Secretaría de Planeación",
            "forma_entrega": "Plataforma de observatorios y boletines",
            "observaciones": "Indicador validado en mesas técnicas con la Secretaría de Planeación.",
            "disponibilidad": "SI"
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
            "desagregacion": "Producción total por ciclo, cultivo y geografía",
            "geografia": "Departamento de Boyacá",
            "definicion": "Mide el comportamiento de la producción agrícola según ciclo de cultivo, tipo de cultivo y ubicación geográfica.",
            "calculo": "Sumatoria según desagregación temática de producción.",
            "fecha": "2019 - 2024",
            "periodicidad": "Anual",
            "fuente": "EVA – Base agrícola UPRA 2019 - 2024",
            "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
            "actores_involucrados": "Secretaría de Agricultura",
            "forma_entrega": "Boletines y medios digitales",
            "observaciones": "Información tomada de las EVA.",
            "disponibilidad": "SI"
        },

        "1201": {
            "codigo": "1201",
            "observatorio": "Económico",
            "categoria1": "Agropecuario",
            "categoria2": "Producción Agrícola",
            "etiqueta": "ND",
            "unidad": "Hectáreas",
            "desagregacion": "Área sembrada y cosechada por cultivo, ciclo y provincia",
            "geografia": "Departamento de Boyacá",
            "definicion": "Mide la extensión de tierra sembrada y cosechada en el departamento por cultivo, ciclo y geografía.",
            "calculo": "Sumatoria de áreas sembradas y cosechadas.",
            "fecha": "2019 - 2024",
            "periodicidad": "Anual",
            "fuente": "EVA – Base agrícola UPRA 2019 - 2024",
            "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
            "actores_involucrados": "Secretaría de Agricultura",
            "forma_entrega": "Boletines y medios digitales",
            "observaciones": "Datos tomados de las EVA.",
            "disponibilidad": "SI"
        },

        "1202": {
            "codigo": "1202",
            "observatorio": "Económico",
            "categoria1": "Agropecuario",
            "categoria2": "Producción Pecuaria",
            "etiqueta": "ND",
            "unidad": "Número",
            "desagregacion": "Por sexo, edad, sacrificio y unidad productiva",
            "geografia": "Departamento de Boyacá",
            "definicion": "Representa el inventario de bovinos según edad, sexo, municipio y sacrificio.",
            "calculo": "Sumatoria de animales por municipio, edad y sexo.",
            "fecha": "2019 - 2024",
            "periodicidad": "Anual",
            "fuente": "EVA – Base pecuaria UPRA 2019 - 2024",
            "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
            "actores_involucrados": "Secretaría de Agricultura",
            "forma_entrega": "Boletines y medios digitales",
            "observaciones": "Para unidades productivas se consideran los años 2021 a 2024.",
            "disponibilidad": "SI"
        },

        "1203": {
            "codigo": "1203",
            "observatorio": "Económico",
            "categoria1": "Agropecuario",
            "categoria2": "Producción Pecuaria",
            "etiqueta": "ND",
            "unidad": "Número",
            "desagregacion": "Unidades productivas con especie bufalina",
            "geografia": "Departamento de Boyacá",
            "definicion": "Inventario de animales bufalinos y unidades productivas con presencia de esta especie.",
            "calculo": "Sumatoria por municipio.",
            "fecha": "2019 - 2024",
            "periodicidad": "Anual",
            "fuente": "EVA – Base pecuaria UPRA 2019 - 2024",
            "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
            "actores_involucrados": "Secretaría de Agricultura",
            "forma_entrega": "Boletines y medios digitales",
            "observaciones": "No aplica desagregación por sexo y edad por falta de información.",
            "disponibilidad": "SI"
        },

        "1204": {
            "codigo": "1204",
            "observatorio": "Económico",
            "categoria1": "Agropecuario",
            "categoria2": "Producción Pecuaria",
            "etiqueta": "ND",
            "unidad": "Número",
            "desagregacion": "Por tipo de ave, capacidad y predio productor",
            "geografia": "Departamento de Boyacá",
            "definicion": "Inventario avícola por tipo de ave, capacidad y predio productor.",
            "calculo": "Sumatoria de aves o predios por tipo y municipio.",
            "fecha": "2019 - 2024",
            "periodicidad": "Anual",
            "fuente": "EVA – Base pecuaria UPRA 2019 - 2024",
            "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
            "actores_involucrados": "Secretaría de Agricultura",
            "forma_entrega": "Boletines y medios digitales",
            "observaciones": "Datos tomados de las EVA.",
            "disponibilidad": "SI"
        },

        "1205": {
            "codigo": "1205",
            "observatorio": "Económico",
            "categoria1": "Agropecuario",
            "categoria2": "Producción Pecuaria",
            "etiqueta": "ND",
            "unidad": "Número",
            "desagregacion": "Por especie y sexo",
            "geografia": "Departamento de Boyacá",
            "definicion": "Inventario de especies caprinas, ovinas y equinas según municipio y sexo.",
            "calculo": "Sumatoria por especie.",
            "fecha": "2019 - 2024",
            "periodicidad": "Anual",
            "fuente": "EVA – Base pecuaria UPRA 2019 - 2024",
            "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
            "actores_involucrados": "Secretaría de Agricultura",
            "forma_entrega": "Boletines y medios digitales",
            "observaciones": "Sexo disponible solo para 2023 y 2024.",
            "disponibilidad": "SI"
        },

        "1206": {
            "codigo": "1206",
            "observatorio": "Económico",
            "categoria1": "Agropecuario",
            "categoria2": "Producción Pecuaria",
            "etiqueta": "ND",
            "unidad": "Número de animales",
            "desagregacion": "Sistema de producción, etapas de desarrollo y unidades productivas",
            "geografia": "Departamento de Boyacá",
            "definicion": "Inventario porcino según municipio, especie y etapas productivas.",
            "calculo": "Sumatoria por especie.",
            "fecha": "2019 - 2024",
            "periodicidad": "Anual",
            "fuente": "EVA – Base pecuaria UPRA 2019 - 2024",
            "entidad_responsable": "Secretaría de Planeación – Observatorio Económico",
            "actores_involucrados": "Secretaría de Agricultura",
            "forma_entrega": "Boletines y medios digitales",
            "observaciones": "Incluye datos complementarios del ICA.",
            "disponibilidad": "SI"
        }


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

<?php include 'include/footer.php'; ?>
