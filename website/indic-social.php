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
$hvIndicadores = im_hoja_vida_publicada(2, __DIR__, $hvPdo);
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
<?php foreach ($hvIndicadores as $hvCod => $hvInd): ?>
                    <option value="<?= $hvCod ?>"><?= $hvCod ?> &middot; <?= htmlspecialchars($hvInd['titulo']) ?></option>
<?php endforeach; ?>
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
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2015">Atenciones médicas por tipos de violencia</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2017">Delitos sexuales (conflicto armado)</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2018">Desaparición forzada</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2003">Homicidios</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2016">Intento de suicidio</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2019">Víctimas MAP/MUSE/AEI</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2010">Presunto delito sexual</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2020">Secuestro población boyacense</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2005">Suicidios</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2013">Violencia a adulto mayor</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2008">Violencia a NNA</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2016">Violencia de género</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2011">Violencia de pareja</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2012">Violencia interpersonal</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2009">Violencia intrafamiliar</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2014">Violencia por convivencia educativa</a></li>
                                <li class="icon-list-item"><span class="icon">📊</span><a href="indicador.php?id=2001">Feminicidios</a></li>
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

<?php include 'include/footer.php'; ?>
