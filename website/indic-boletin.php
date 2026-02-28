<?php
require_once 'tracker.php';
include 'include/header.php';
?>

<!-- ICONOS FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" type="text/css" href="assets/css/tabs.css">
<script type="text/javascript" src="assets/js/tabs.js"></script>

<div id="main-body" class="main-body">

<section class="container-fluid section-indicador">
    <section class="section-indicador-top">
        <br/>
    </section>
    <div class="row indicador-content">
        <div class="col-12 col-sm-12">
            <h2 class="indicador-title">Boletines Red de Observatorios de Boyacá</h2>
            <div class="indicador-description">
                <p class="description">
                    ¡Bienvenidos al espacio oficial de la Red de Observatorios de Boyacá! Este es el punto de encuentro donde se publicarán los boletines oficiales que integran información actualizada, análisis y reportes alineados con las dimensiones y categorías que estructuran los diferentes observatorios de la región.<br><br>

                    La Red de Observatorios de Boyacá es una iniciativa que articula esfuerzos de investigación, análisis y generación de conocimiento en torno a las dinámicas sociales, económicas, ambientales y tecnológicas del departamento. Este espacio tiene como objetivo centralizar y socializar la información relevante para la toma de decisiones y la implementación de estrategias que promuevan el desarrollo sostenible y competitivo del territorio.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- SISTEMA DE TABS -->
<div class="container-fluid mt-4">
    <div class="container mt-4">

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab-link active" data-bs-target="#social">Boletines Dimensión Social</div>
            <div class="tab-link" data-bs-target="#economic">Boletines Dimensión Económica</div>
            <div class="tab-link" data-bs-target="#general">Boletines Generales</div>
        </div>

        <!-- TAB CONTENT -->
        <div class="tab-content">

            <!-- SOCIAL TAB -->
            <div class="tab-pane active" id="social">
                <div class="accordion" id="socialAccordion">

                    <!-- CATEGORIA VIOLENCIA -->
                    <div class="accordion-item">
                        <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                            <i class="fa-solid fa-shield-halved me-2"></i>
                            Categoría: Violencia
                        </button>

                        <div class="accordion-content">

                            <!-- BOLETÍN 1 -->
                            <div class="boletin-card">
                                <div class="boletin-icon">
                                    <img src="assets/svg/portadaB_violencia01.png" class="img-fluid rounded" alt="">
                                </div>
                                <div class="boletin-body">
                                    <h5>Boletín 1 de violencia Dimensión Social</h5>
                                    <p>Un análisis detallado de los indicadores de violencia desde enero del 2020 a junio del 2024.</p>
                                    <a href="assets/pdf/BOLETIN 1 de Violencia 10 dic 2024.pdf" class="btn-read">Leer más</a>
                                </div>
                            </div>

                            <!-- BOLETÍN 2 -->
                            <div class="boletin-card">
                                <div class="boletin-icon">
                                    <i class="fa-solid fa-person-military-pointing boletin-fa"></i>
                                </div>
                                <div class="boletin-body">
                                    <h5>Boletín 2 de violencia Dimensión Social</h5>
                                    <p>Un análisis detallado de los indicadores de violencia desde enero del 2024 a junio del 2025.</p>
                                    <a href="assets/pdf/BOLETIN_VIOLENCIAS_NOVIEMBRE_2025.pdf" class="btn-read">Leer más</a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- CATEGORIA SALUD -->
                    <div class="accordion-item">
                        <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                            <i class="fa-solid fa-heart-pulse me-2"></i>
                            Categoría: Salud
                        </button>

                        <div class="accordion-content">

                            <div class="boletin-card">
                                <div class="boletin-icon">
                                    <i class="fa-solid fa-file-medical boletin-fa"></i>
                                </div>
                                <div class="boletin-body">
                                    <h5>Boletín 1 de salud Dimensión Social</h5>
                                    <p>Análisis de indicadores de salud desde enero del 2020 a diciembre del 2024.</p>
                                    <a href="assets/pdf/BOLETIN_SALUD_NOVIEMBRE_2025.pdf" class="btn-read">Leer más</a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- ECONOMIC TAB -->
            <div class="tab-pane" id="economic">

            <!-- CATEGORÍA MDM -->
            <div class="accordion-item">
                <button class="accordion-button" type="button" onclick="toggleAccordion(this)">
                    <i class="fa-solid fa-building-columns me-2"></i>
                    Categoría: Medición de Desempeño Municipal
                </button>

                <div class="accordion-content">

                    <div class="boletin-card">
                        <div class="boletin-icon">
                            <i class="fa-solid fa-chart-line boletin-fa"></i>
                        </div>

                        <div class="boletin-body">
                            <h5>Boletín 1 de Medición de Desempeño Municipal</h5>
                            <p>Análisis de indicadores clave de desempeño municipal, gestión y resultados.</p>
                            <a href="assets/pdf/Boletin01_MDM.pdf" class="btn-read">Leer más</a>
                        </div>
                    </div>

                </div>
            </div>


            </div>

            <!-- GENERAL TAB -->
            <div class="tab-pane" id="general">

                <!-- BOLETINES GENERALES -->
                <p>Boletines 2022</p>

                <?php
                // Para evitar repetición manual podrías automatizar esto,
                // pero dejo tu estructura tal cual e intacta:

                $general_boletines = [
                    ["img" => "boletin-01.png", "titulo" => "Boletín General 1",
                     "desc" => "Un análisis de Métricas en Boyacá Económico, Social y Ambiental.",
                     "pdf" => "Boletin 1-04 julio-2023.pdf"],

                    ["img" => "boletin-02.png", "titulo" => "Boletín General 2",
                     "desc" => "Un análisis de los Determinantes de Pobreza.",
                     "pdf" => "Boletin 2-04 julio 2023.pdf"],

                    ["img" => "boletin-03.png", "titulo" => "Boletín General 3",
                     "desc" => "Un análisis de los Objetivos del Desarrollo Sostenible.",
                     "pdf" => "Boletin 3-17 de julio 2023.pdf"],

                    ["img" => "boletin-04.png", "titulo" => "Boletín General 4",
                     "desc" => "Un análisis de Caracterización de la Producción Agrícola del Departamento de Boyacá.",
                     "pdf" => "Boletin 4-06 julio 2023.pdf"],

                    ["img" => "boletin-05.png", "titulo" => "Boletín General 5",
                     "desc" => "Un análisis de la calidad de aire.",
                     "pdf" => "Boletin 5 - 11 julio 2023.pdf"],

                    ["img" => "boletin-06.png", "titulo" => "Boletín General 6",
                     "desc" => "Un análisis de CTeI en el Departamento.",
                     "pdf" => "Boletin 6-13 julio 2023.pdf"]
                ];

                foreach ($general_boletines as $b) {
                    echo '
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <img src="assets/svg/'.$b["img"].'" class="img-fluid" alt="'.$b["titulo"].'">
                        </div>
                        <div class="col-md-9">
                            <h5>'.$b["titulo"].'</h5>
                            <p>'.$b["desc"].'</p>
                            <a href="assets/pdf/'.$b["pdf"].'" class="btn-read">Leer más</a>
                        </div>
                    </div>';
                }
                ?>

            </div>
        </div>
    </div>
</div>
</div>

<!-- MODAL -->
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

            <button class="button is-primary mt-4">Enviar</button>
        </form>
    </div>
</div>

<script>
document.getElementById("formCarac").onsubmit = async (e)=>{
    e.preventDefault();
    let form = new FormData(e.target);
    await fetch("/website/formulario_usuario.php", {
        method:"POST",
        body: form
    });
    document.getElementById("modalCaracterizacion").style.display="none";
};

/* ----------- TABS ----------- */
document.querySelectorAll('.tab-link').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-link').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.querySelector(tab.dataset.bsTarget).classList.add('active');
    });
});

/* ----------- ACCORDION ----------- */
function toggleAccordion(button) {
    const content = button.nextElementSibling;
    if (content.classList.contains('active')) {
        content.classList.remove('active');
        button.classList.remove('active');
    } else {
        document.querySelectorAll('.accordion-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.accordion-button').forEach(b => b.classList.remove('active'));
        content.classList.add('active');
        button.classList.add('active');
    }
}
</script>

<!-- ESTILOS MEJORADOS -->
<style>
/* --------------------
   TABS
-------------------- */
.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
.tab-link {
    background-color: #ececec;
    border-radius: 6px;
    padding: 10px 20px;
    cursor: pointer;
    font-weight: 600;
    text-align: center;
    flex: 1;
    transition: 0.3s;
}
.tab-link:hover { background: #d4cbe1; }
.tab-link.active { background-color: #3E2259; color: #fff; }
.tab-content {
    border-radius: 8px;
    background-color: #fff;
    padding: 20px;
}

/* --------------------
   ACCORDION
-------------------- */
.accordion-item {
    background: #fff;
    border-radius: 8px;
    margin-bottom: 12px;
    border: 1px solid #ddd;
}
.accordion-button {
    width: 100%;
    background: #f7f7f7;
    padding: 12px 16px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    transition: 0.3s;
    display: flex;
    align-items: center;
}
.accordion-button:hover { background: #e8e2ef; }
.accordion-button.active { background: #ddd; }
.accordion-content {
    max-height: 0;
    overflow: hidden;
    padding: 0 16px;
    background: #fafafa;
    border-top: 1px solid #eee;
    transition: max-height 0.35s ease, padding 0.25s ease;
}
.accordion-content.active {
    padding: 16px;
    max-height: 800px;
}

/* --------------------
   BOLETÍN CARDS
-------------------- */
.boletin-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e5e5e5;
    padding: 15px;
    margin-bottom: 15px;
    display: flex;
    gap: 15px;
    align-items: start;
    box-shadow: 0px 1px 3px rgba(0,0,0,0.08);
}
.boletin-icon {
    width: 80px;
    min-width: 80px;
    height: 80px;
    border-radius: 8px;
    background: #f0eef3;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.boletin-fa {
    font-size: 40px;
    color: #3E2259;
}
.boletin-body h5 {
    margin-bottom: 6px;
    font-weight: 700;
}
.btn-read {
    padding: 6px 14px;
    background: #3E2259;
    color: #fff !important;
    border-radius: 20px;
    font-size: 14px;
    transition: 0.3s;
    display: inline-block;
}
.btn-read:hover {
    background: #2a163e;
    color: #fff !important;
}
</style>

<?php include 'include/footer.php'; ?>
