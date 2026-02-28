<?php 
require_once 'tracker.php';
include 'include/header.php';
?>

<link rel="stylesheet" href="assets/css/IndicadoresGenero.css">
<script src="assets/js/genero.js" defer></script>

<div id="main-body" class="main-body">

    <!-- ===========================
         BANNER SUPERIOR GENERO
    ============================ -->
<section class="banner-social-wrapper">

    <img src="assets/svg/bg-banner-genero.png"
         alt="Dimensión Asuntos de Genero"
         class="banner-genero">

    <!-- CAPA OSCURA -->
    <div class="banner-overlay"></div>

    <!-- TEXTO -->
    <div class="banner-social-text">
        <h2 class="titulo-banner">Observatorio de Género</h12>
        <p class="subtitulo-banner">Indicadores – Análisis – Brechas – Participación</p>
    </div>

</section>



   <!-- ============================= -->
<!--   TABS 9 DIMENSIONES GÉNERO   -->
<!-- ============================= -->
<ul class="nav nav-tabs genero-tabs" id="generoTabs" role="tablist">

    <!-- 1. Marco Institucional (MORADO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link active tab-purple" id="tab1" data-bs-toggle="tab" data-bs-target="#panel1" type="button" role="tab">
            <span class="tab-icon">🏛️</span> Marco institucional
        </button>
    </li>

    <!-- 2. Información (BLANCO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-white" id="tab2" data-bs-toggle="tab" data-bs-target="#panel2" type="button" role="tab">
            <span class="tab-icon">📘</span> Información
        </button>
    </li>

    <!-- 3. Seguimiento (MORADO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-purple" id="tab3" data-bs-toggle="tab" data-bs-target="#panel3" type="button" role="tab">
            <span class="tab-icon">📈</span> Seguimiento
        </button>
    </li>

    <!-- 4. Ruta de atención (BLANCO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-white" id="tab4" data-bs-toggle="tab" data-bs-target="#panel4" type="button" role="tab">
            <span class="tab-icon">🛣️</span> Ruta de atención
        </button>
    </li>

    <!-- 5. Barreras de acceso (MORADO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-purple" id="tab5" data-bs-toggle="tab" data-bs-target="#panel5" type="button" role="tab">
            <span class="tab-icon">🚧</span> Barreras de acceso
        </button>
    </li>

    <!-- 6. Atención integral (BLANCO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-white" id="tab6" data-bs-toggle="tab" data-bs-target="#panel6" type="button" role="tab">
            <span class="tab-icon">🤝</span> Atención integral
        </button>
    </li>

    <!-- 7. Política Pública (MORADO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-purple" id="tab7" data-bs-toggle="tab" data-bs-target="#panel7" type="button" role="tab">
            <span class="tab-icon">📜</span> Política Pública
        </button>
    </li>

    <!-- 8. Campañas y publicaciones (BLANCO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-white" id="tab8" data-bs-toggle="tab" data-bs-target="#panel8" type="button" role="tab">
            <span class="tab-icon">📰</span> Campañas y publicaciones
        </button>
    </li>

    <!-- 9. Reportes sociales (MORADO) -->
    <li class="nav-item" role="presentation">
        <button class="nav-link tab-purple" id="tab9" data-bs-toggle="tab" data-bs-target="#panel9" type="button" role="tab">
            <span class="tab-icon">📄</span> Reportes sociales
        </button>
    </li>

</ul>


    <!-- ============================= -->
    <!--         CONTENIDO DE TABS     -->
    <!-- ============================= -->
    <div class="tab-content genero-content mt-4">

    <div class="tab-pane fade show active panel-purple" id="panel1">
        <h3>Marco institucional</h3>
        
                                    <!-- Acordeón de Indicadores -->
                                                    <div id="tab" class="indicadores-section-list mt-4">
                                                        <div class="accordion-item">
                                                            <button class="accordion-button" onclick="toggleAccordion(this)">Normativo</button>
                                                            <div class="accordion-content">
                                                            <p>
                                                                A continuación encontrarás las diferentes leyes, resoluciones, decretos y convenios Internacionales, Nacionales y Departamentales sobre la erradicación de la brecha de género y creación de los diferentes Observatorios de Mujer y Equidad de Género:
                                                            </p>

                                                            <h4 style="color:#3E2259 !important;"><b>Internacional</b></h4>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/1.I Conferencia+regional+mujer+america+latina+caribe+2010.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Conferencia regional sobre la mujer de América latina y el caribe - Consenso de Brasilia">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Conferencia regional sobre la mujer de América latina y el caribe - Consenso de Brasilia</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Acuerdos de 2010 para impulsar políticas de igualdad, autonomía económica y participación política de las mujeres en ALC. Encontrarás compromisos y líneas de acción para transversalizar el enfoque de género.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/2.I Declaracion+del+milenio+asamblea+general+naciones+unidas+13+sep+2000.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Declaración del Milenio Asamblea General ONU">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Declaración del Milenio Asamblea General ONU</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Base de los Objetivos de Desarrollo del Milenio (2000), incluyendo la igualdad de género y el empoderamiento de las mujeres. Encontrarás metas y principios globales para cerrar brechas.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/3.I Resolucion+1325+del+consejo+de+seguridad+del+2000.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Resolución 1325 mujer, paz y seguridad - Consejo de seguridad ONU">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Resolución 1325 mujer, paz y seguridad - Consejo de seguridad ONU</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Marco “Mujeres, Paz y Seguridad” (2000) que ordena participación de las mujeres, protección frente a violencias y prevención en contextos de conflicto. Hallarás obligaciones para Estados y recomendaciones sectoriales.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/4.I cedaw_s.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Convención sobre la eliminación de todas las formas de discriminación contra la mujer">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Convención sobre la eliminación de todas las formas de discriminación contra la mujer</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Tratado CEDAW: obliga a los Estados a eliminar la discriminación contra la mujer en todas las esferas. Incluye medidas legales, políticas y mecanismos de seguimiento.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/5.I convencion.belen DO para.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Convención Interamericana para Prevenir, Sancionar y Erradicar la Violencia contra la Mujer 'Convención de Belém do Pará'">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Convención Interamericana para Prevenir, Sancionar y Erradicar la Violencia contra la Mujer 'Convención de Belém do Pará'</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Instrumento clave en América para combatir la violencia contra las mujeres. Define la violencia, responsabilidades estatales y rutas de protección y reparación.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/6.I UDHR_booklet_SP_web.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Declaración Universal de Derechos Humanos">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Declaración Universal de Derechos Humanos</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Declaración base de derechos y libertades para todas las personas. Sirve de marco general para la igualdad y no discriminación por razón de sexo y género.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/7.I r29904.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Pacto Internacional de Derechos Civiles y Políticos">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Pacto Internacional de Derechos Civiles y Políticos</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Tratado que garantiza igualdad ante la ley, participación política y no discriminación. Relevante para la protección de derechos civiles de las mujeres.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/8.I Declaración y plataforma.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Declaración y Plataforma de Acción de Beijing">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Declaración y Plataforma de Acción de Beijing</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Agenda global de 1995 con 12 áreas críticas (educación, salud, violencia, economía, poder, etc.). Encontrarás medidas concretas para políticas públicas con enfoque de género.
                                                                </p>
                                                            </section>

                                                            <br>
                                                            <h4 style="color:#3E2259 !important;"><b>Nacional</b></h4>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/0.N Cartilla_Genero_MinJusticia.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Cartilla Género - Ministerio de Justicia">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Cartilla Género - Ministerio de Justicia.pdf</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Guía práctica para incorporar el enfoque de género en el sector justicia. Incluye conceptos clave, rutas de atención y orientaciones institucionales.
                                                                </p>

                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/1.N Constitucion+politica+1991.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Constitución Política 20 de julio de 1991">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Constitución Política N&deg;116  20 de julio de 1991</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Carta Magna que consagra igualdad y no discriminación (arts. 13 y 43, entre otros). Base jurídica para políticas de equidad de género en Colombia.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/2.N Directiva+presidencial+01+de+2023.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Directiva 01 del 2023 Presidencia de la República">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Directiva 01 del 2023 Presidencia de la República</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Lineamientos para transversalizar el enfoque de género en planes, programas y presupuestos del orden nacional, con acciones e indicadores.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/3.N Directiva+presidencial+01+de+2023.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Ley 51 de 1981 Congreso de Colombia">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Ley 51 de 1981 Congreso de Colombia</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Ley aprobatoria de la CEDAW en Colombia. Encontrarás la incorporación del tratado internacional para eliminar la discriminación contra la mujer.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/4.N Directiva+presidencial+01+de+2023.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Ley 248 de 1995 Congreso de Colombia">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Ley 248 de 1995 Congreso de Colombia</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Ley aprobatoria de la Convención de Belém do Pará. Determina obligaciones estatales frente a la violencia contra las mujeres.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/5.N Directiva+presidencial+01+de+2023.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Ley 823 del 2003   Congreso de Colombia">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Ley 823 del 2003   Congreso de Colombia</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Ley de igualdad de oportunidades para la mujer. Presenta criterios para políticas públicas, acciones afirmativas y corresponsabilidad institucional.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/6.N Directiva+presidencial+01+de+2023.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Ley 1257 del 2008  Congreso de Colombia">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Ley 1257 del 2008  Congreso de Colombia</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Medidas para prevenir, sancionar y erradicar la violencia contra las mujeres. Contiene rutas de atención, protección, reparación y sanciones.
                                                                </p>
                                                            </section>

                                                            <h4 style="color:#3E2259 !important;"><b>Departamental</b></h4>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/1.D Ordenanza-042-de-15-de-dic-de-2023.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Ordenanza 42 de 2023, por la cual se actualiza el Consejo Consultivo de Mujeres del Departamento.">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Ordenanza 42 de 2023, por la cual se actualiza el Consejo Consultivo de Mujeres del Departamento.</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Actualiza y fortalece el Consejo Consultivo de Mujeres de Boyacá, definiendo sus funciones, conformación y participación en la toma de decisiones públicas.
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/2.D gobboy2024-decreto-0293-de-14-de-mar_c.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Decreto 0293 de 14 marzo 2024, por el cual se reglamenta el funcionamiento interno del Consejo Consultivo de Mujeres.">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Decreto 0293 de 14 marzo 2024, por el cual se reglamenta el funcionamiento interno del Consejo Consultivo de Mujeres.</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Reglamenta la operación interna del Consejo Consultivo de Mujeres (sesiones, vocerías, decisiones y articulación con la administración departamental).
                                                                </p>
                                                            </section>

                                                            <section>
                                                                <div class="d-flex w-100 text-left lead">
                                                                <a href="assets/pdf/asuntosGenero/3.D Decreto_conformacion_mecanismo.pdf"
                                                                    class="pdf-link list-group-item list-group-item-action flex-column"
                                                                    data-title="Decreto 1231 de 29 septiembre 2022, por el cual se reorganiza la estructura y el funcionamiento del Mecanismo Articulador.">
                                                                    <div class="mb-1">
                                                                    <i class="fas fa-file-pdf" style="color:#FF0000 !important;"></i>
                                                                    <span class="icon-list-pdf"><b>Decreto 1231 de 29 septiembre 2022, por el cual se reorganiza la estructura y el funcionamiento del Mecanismo Articulador.</b></span>
                                                                    </div>
                                                                </a>
                                                                </div>
                                                                <p class="small text-muted">
                                                                Reorganiza el Mecanismo Articulador para transversalizar el enfoque de género y coordinar acciones de prevención y atención de violencias contra las mujeres en el departamento.
                                                                </p>
                                                            </section>



                                                            <!-- Agregar más indicadores aquí -->

                                                    </div>
                                                </div>
                                                <div class="accordion-item">
                                                    <button class="accordion-button" onclick="toggleAccordion(this)">Misión y Visión</button>
                                                    <div class="accordion-content">
                                                        <ul class="icon-list-items">
                                                        <li class="icon-list-item"><span class="icon-list-text">Misión</span></li>
                                                        <p>Proveer un sistema integral de recolección, análisis y visualización de datos sobre la situación de las mujeres y la equidad de género en Boyacá, generando conocimiento e información confiable que facilite la toma de decisiones informadas, promueva políticas públicas efectivas y contribuya a la transformación social mediante la reducción de brechas de género y la garantía de los derechos de las mujeres.</p>
                                                        <li class="icon-list-item"><span class="icon-list-text">Visión</span></li>
                                                        <p>Ser el observatorio líder en Boyacá en el monitoreo, análisis y generación de datos sobre la equidad de género, reconocido por su impacto en la formulación de políticas públicas inclusivas y por su contribución a una sociedad más equitativa. Para 2027, se proyecta como referente regional y nacional en la producción de datos confiables que orienten la transformación social y aseguren igualdad de oportunidades para las mujeres del departamento.</p>
                                                            <!-- Agregar más indicadores aquí -->
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="accordion-item">
                                                    <button class="accordion-button" onclick="toggleAccordion(this)">Proposito</button>
                                                    <div class="accordion-content">
                                                        <ul class="icon-list-items">
                                                        <li class="icon-list-item"><span class="icon-list-text">Proposito</span></li>
                                                        <p>Impulsar la igualdad de género en Boyacá mediante la producción y difusión de conocimiento basado en datos verificables, que respalden políticas públicas efectivas y acciones estratégicas orientadas a la garantía de derechos y la disminución de las brechas de género.</p>
                                                            <!-- Agregar más indicadores aquí -->
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="accordion-item">
                                                    <button class="accordion-button" onclick="toggleAccordion(this)">Objetivo</button>
                                                    <div class="accordion-content">
                                                        <ul class="icon-list-items">
                                                        <li class="icon-list-item"><span>- Recopilar, sistematizar y gestionar datos relevantes sobre la situación de las mujeres en Boyacá en temas como salud, educación, economía, violencia basada en género, participación política y construcción de paz, garantizando su confiabilidad, pertinencia y actualización continua.</span></li>
                                                        <li class="icon-list-item"><span>- Identificar y analizar brechas de género en las diversas dimensiones de la autonomía de las mujeres del departamento, facilitando diagnósticos que orienten políticas públicas y programas sociales eficaces.</span></li>
                                                        <li class="icon-list-item"><span>- Fortalecer las capacidades institucionales, territoriales y comunitarias mediante el acceso a información clara y herramientas tecnológicas que fomenten el uso estratégico de los datos en la promoción de la equidad de género.</span></li>
                                                        <li class="icon-list-item"><span>- Generar informes, boletines y análisis periódicos que evidencien avances, desafíos y brechas en los derechos de las mujeres, con el propósito de informar, sensibilizar y orientar a los tomadores de decisiones y a la ciudadanía.</span></li>
                                                        <p></p>
                                                            <!-- Agregar más indicadores aquí -->
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="accordion-item">
                                                    <button class="accordion-button" onclick="toggleAccordion(this)">Integrantes</button>
                                                    <div class="accordion-content">
                                                        

                                                    <!-- ===== Carrusel de entidades aliadas (logos) ===== -->
                                                    <section class="logos-module" aria-label="Entidades aliadas">
                                                    <h3 class="logos-title">Entidades que participan</h3>

                                                    <div class="logo-marquee" id="logoMarquee">
                                                        <div class="logo-track" id="logoTrack">
                                                        <!-- Planeación primero -->
                                                        <a class="logo-item" href="#" aria-label="Secretaría de Planeación de Boyacá">
                                                            <img src="assets/svg/carruselGenero/SecPlaneacion.png" alt="Secretaría de Planeación de Boyacá">
                                                        </a>
                                                        <a class="logo-item" href="#" aria-label="Secretaría de Integración Social de Boyacá">
                                                            <img src="assets/svg/carruselGenero/SecIntegracion.png" alt="Secretaría de Integración Social de Boyacá">
                                                        </a>
                                                        <a class="logo-item" href="#" aria-label="Secretaría de Salud de Boyacá">
                                                            <img src="assets/svg/carruselGenero/SecSalud.png" alt="Secretaría de Salud de Boyacá">
                                                        </a>
                                                        <a class="logo-item" href="#" aria-label="Secretaría de Gobierno de Boyacá">
                                                            <img src="assets/svg/carruselGenero/SecGobierno.png" alt="Secretaría de Gobierno de Boyacá">
                                                        </a>
                                                        <a class="logo-item" href="#" aria-label="Fiscalía General de la Nación">
                                                            <img src="assets/svg/carruselGenero/Fiscalia.png" alt="Fiscalía General de la Nación">
                                                        </a>
                                                        <a class="logo-item" href="#" aria-label="Instituto Nacional de Medicina Legal y Ciencias Forenses">
                                                            <img src="assets/svg/carruselGenero/MedicinaLegal.png" alt="Instituto Nacional de Medicina Legal y Ciencias Forenses">
                                                        </a>
                                                        <a class="logo-item" href="#" aria-label="Universidad Pedagógica y Tecnológica de Colombia - UPTC">
                                                            <img src="assets/svg/carruselGenero/UPTC.png" alt="UPTC">
                                                        </a>
                                                        </div>
                                                    </div>
                                                    </section>

                                                    <style>
                                                    :root{
                                                        --brand:#7c3aed; --title:#2c3e50;
                                                        --logo-box-h:72px;      /* alto de la cajita (ajusta si lo quieres más grande/pequeño) */
                                                        --logo-max-h:48px;      /* alto máximo de cada logo */
                                                        --gap:28px;             /* separación entre logos */
                                                        --speed:45s;            /* velocidad del desplazamiento */
                                                    }
                                                    .logos-module{ width:min(1200px,94%); margin:18px auto 8px; }
                                                    .logos-title{ margin:0 0 10px; font-weight:800; color:var(--title); }

                                                    .logo-marquee{
                                                        position:relative; overflow:hidden; border-radius:14px;
                                                        background:#fff; border:1px solid rgba(0,0,0,.06);
                                                        box-shadow:0 2px 10px rgba(0,0,0,.06);
                                                        padding:10px 0;
                                                        /* desvanecido en bordes (opcional) */
                                                        -webkit-mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent);
                                                                mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent);
                                                    }
                                                    .logo-track{
                                                        display:flex; align-items:center; gap:var(--gap);
                                                        width:max-content;
                                                        animation: logos-scroll var(--speed) linear infinite;
                                                        padding-inline:16px;
                                                    }
                                                    .logo-marquee:hover .logo-track{ animation-play-state: paused; }

                                                    .logo-item{
                                                        flex:0 0 auto;
                                                        width:220px; height:var(--logo-box-h);
                                                        display:flex; align-items:center; justify-content:center;
                                                        border:1px solid rgba(0,0,0,.08); background:#fff;
                                                        border-radius:12px; padding:8px 12px;
                                                        box-shadow:0 1px 6px rgba(0,0,0,.05);
                                                        transition: transform .18s ease, box-shadow .18s ease;
                                                    }
                                                    .logo-item:hover{ transform:translateY(-2px); box-shadow:0 6px 18px rgba(0,0,0,.10); }
                                                    .logo-item img{
                                                        max-height:var(--logo-max-h);
                                                        max-width:100%;
                                                        width:auto; height:auto; object-fit:contain; display:block;
                                                        filter: grayscale(25%);
                                                        transition: filter .18s ease;
                                                    }
                                                    .logo-item:hover img{ filter:none; }

                                                    @keyframes logos-scroll{
                                                        from{ transform: translateX(0); }
                                                        to  { transform: translateX(-50%); } /* requiere duplicar el contenido vía JS */
                                                    }

                                                    /* Responsive fino */
                                                    @media (max-width: 900px){
                                                        :root{ --logo-box-h:64px; --logo-max-h:44px; --gap:22px; --speed:40s; }
                                                        .logo-item{ width:180px; }
                                                    }
                                                    @media (max-width: 560px){
                                                        :root{ --logo-box-h:58px; --logo-max-h:40px; --gap:18px; --speed:35s; }
                                                        .logo-item{ width:160px; }
                                                    }
                                                    </style>

                                                    <script>
                                                    // Duplicamos el contenido para crear bucle infinito suave
                                                    (function(){
                                                        const track = document.getElementById('logoTrack');
                                                        if(!track) return;
                                                        track.innerHTML = track.innerHTML + track.innerHTML; // duplica logos
                                                    })();
                                                    </script>


                                                    </div>
                                                </div>
                                </div>
    </div>

    <div class="tab-pane fade panel-white" id="panel2">

                        <div class="indicadores-section mt-4">
                <div style="display: flex; align-items: center;">
                <img src="assets/svg/img-genero/INFORMACION.svg"
                    alt="Icono Información"
                    style="width: 50px; height: 50px; margin-right: 15px;">
                <h2 style="margin: 0;" class="indicador-title">Información</h2>
                </div>
                <p>
                En esta sección podrás visualizar información clave sobre la situación de las mujeres en Boyacá, a través de indicadores, tableros interactivos y boletines especializados.
                </p>



            <!-- ===================== CONCEPTOS — MÓDULO AUTO-CONTENIDO ===================== -->
            <section class="seguimiento-block" id="mod-conceptos">
            <div class="seguimiento-title"><span class="icon">🧭</span> Conceptos</div>

            <!-- Pestañas -->
            <div class="chip-group" id="conceptos-tabs" role="tablist" aria-label="Conceptos de género">
                <button class="chip is-active" data-tab="discriminacion" role="tab" aria-selected="true">Discriminación</button>
                <button class="chip" data-tab="expresion" role="tab" aria-selected="false">Expresión</button>
                <button class="chip" data-tab="genero" role="tab" aria-selected="false">Género</button>
                <button class="chip" data-tab="identidad" role="tab" aria-selected="false">Identidad</button>
                <button class="chip" data-tab="interseccionalidad" role="tab" aria-selected="false">Interseccionalidad</button>
                <button class="chip" data-tab="nuevas-masculinidades-y-feminidades" role="tab" aria-selected="false">Nuevas masculinidades y feminidades</button>
                <button class="chip" data-tab="orientacion" role="tab" aria-selected="false">Orientación</button>
                <button class="chip" data-tab="sexo" role="tab" aria-selected="false">Sexo</button>
            </div>

            <!-- Contenido -->
            <div class="k-panes">
                <!-- Discriminación -->
                <div class="k-pane is-active" id="pane-discriminacion" aria-labelledby="tab-discriminacion">
                <div class="two-col">
                    <figure class="two-col__media" aria-hidden="true">
                    <picture>
                        <source srcset="assets/svg/img-genero/conceptos/discriminacion.svg" type="image/svg+xml">
                        <img src="assets/svg/img-genero/conceptos/discriminacion.png" alt="Ilustración: Discriminación" loading="lazy">
                    </picture>
                    </figure>
                    <div class="two-col__text">
                    <h4 class="section-title">Discriminación</h4>
                    <p>Trato desigual o desfavorable basado en características como sexo, género, orientación o identidad. Puede ser directa, indirecta o estructural y vulnera derechos y oportunidades.</p>
                    </div>
                </div>
                </div>

                <!-- Expresión -->
                <div class="k-pane" id="pane-expresion" aria-labelledby="tab-expresion">
                <div class="two-col">
                    <figure class="two-col__media" aria-hidden="true">
                    <picture>
                        <source srcset="assets/svg/img-genero/conceptos/expresion.svg" type="image/svg+xml">
                        <img src="assets/svg/img-genero/conceptos/expresion.png" alt="Ilustración: Expresión de género" loading="lazy">
                    </picture>
                    </figure>
                    <div class="two-col__text">
                    <h4 class="section-title">Expresión</h4>
                    <p>Forma en que una persona manifiesta su género a través de vestimenta, conducta, voz o apariencia. No determina la orientación ni la identidad de género.</p>
                    </div>
                </div>
                </div>

                <!-- Género -->
                <div class="k-pane" id="pane-genero" aria-labelledby="tab-genero">
                <div class="two-col">
                    <figure class="two-col__media" aria-hidden="true">
                    <picture>
                        <source srcset="assets/svg/img-genero/conceptos/genero.svg" type="image/svg+xml">
                        <img src="assets/svg/img-genero/conceptos/genero.png" alt="Ilustración: Género" loading="lazy">
                    </picture>
                    </figure>
                    <div class="two-col__text">
                    <h4 class="section-title">Género</h4>
                    <p>Construcción sociocultural de roles, expectativas y normas asociadas a ser mujer, hombre u otras identidades. Afecta acceso a recursos, poder y reconocimiento.</p>
                    </div>
                </div>
                </div>

                <!-- Identidad -->
                <div class="k-pane" id="pane-identidad" aria-labelledby="tab-identidad">
                <div class="two-col">
                    <figure class="two-col__media" aria-hidden="true">
                    <picture>
                        <source srcset="assets/svg/img-genero/conceptos/identidad.svg" type="image/svg+xml">
                        <img src="assets/svg/img-genero/conceptos/identidad.png" alt="Ilustración: Identidad de género" loading="lazy">
                    </picture>
                    </figure>
                    <div class="two-col__text">
                    <h4 class="section-title">Identidad</h4>
                    <p>Vivencia interna y personal del género que cada persona siente profundamente. Puede corresponder o no con el sexo asignado al nacer.</p>
                    </div>
                </div>
                </div>

                <!-- Interseccionalidad -->
                <div class="k-pane" id="pane-interseccionalidad" aria-labelledby="tab-interseccionalidad">
                <div class="two-col">
                    <figure class="two-col__media" aria-hidden="true">
                    <picture>
                        <source srcset="assets/svg/img-genero/conceptos/interseccionalidad.png" type="image/svg+xml">
                        <img src="assets/svg/img-genero/conceptos/interseccionalidad.png" alt="Ilustración: Interseccionalidad" loading="lazy">
                    </picture>
                    </figure>
                    <div class="two-col__text">
                    <h4 class="section-title">Interseccionalidad</h4>
                    <p>Enfoque que reconoce cómo se cruzan múltiples factores (género, clase, etnia, discapacidad, edad, territorio, etc.) generando ventajas o desventajas acumuladas.</p>
                    </div>
                </div>
                </div>

                <!-- Nuevas masculinidades y feminidades -->
                <div class="k-pane" id="pane-nuevas-masculinidades-y-feminidades" aria-labelledby="tab-nuevas-masculinidades-y-feminidades">
                <div class="two-col">
                    <figure class="two-col__media" aria-hidden="true">
                    <picture>
                        <source srcset="assets/svg/img-genero/conceptos/masculinidades_y_feminidades.png" type="image/svg+xml">
                        <img src="assets/svg/img-genero/conceptos/masculinidades_y_feminidades.png" alt="Ilustración: Nuevas masculinidades y feminidades" loading="lazy">
                    </picture>
                    </figure>
                    <div class="two-col__text">
                    <h4 class="section-title">Nuevas masculinidades y feminidades</h4>
                    <p>Propuestas que cuestionan estereotipos tradicionales y promueven relaciones igualitarias, cuidado, corresponsabilidad y diversidad de maneras de ser.</p>
                    </div>
                </div>
                </div>

                <!-- Orientación -->
                <div class="k-pane" id="pane-orientacion" aria-labelledby="tab-orientacion">
                <div class="two-col">
                    <figure class="two-col__media" aria-hidden="true">
                    <picture>
                        <source srcset="assets/svg/img-genero/conceptos/orientacion.svg" type="image/svg+xml">
                        <img src="assets/svg/img-genero/conceptos/orientacion.png" alt="Ilustración: Orientación" loading="lazy">
                    </picture>
                    </figure>
                    <div class="two-col__text">
                    <h4 class="section-title">Orientación</h4>
                    <p>Atracción afectiva, emocional y/o sexual hacia otras personas (por ejemplo, hacia un género, varios o independiente del género).</p>
                    </div>
                </div>
                </div>

                <!-- Sexo -->
                <div class="k-pane" id="pane-sexo" aria-labelledby="tab-sexo">
                <div class="two-col">
                    <figure class="two-col__media" aria-hidden="true">
                    <picture>
                        <source srcset="assets/svg/img-genero/conceptos/SEXO.svg" type="image/svg+xml">
                        <img src="assets/svg/img-genero/conceptos/SEXO.png" alt="Ilustración: Sexo" loading="lazy">
                    </picture>
                    </figure>
                    <div class="two-col__text">
                    <h4 class="section-title">Sexo</h4>
                    <p>Características biológicas (anatómicas, hormonales, cromosómicas) con las que nacen las personas. No determina por sí mismo la identidad o la expresión de género.</p>
                    </div>
                </div>
                </div>
            </div>

            <!-- Comportamiento local (no interfiere con otras secciones) -->
            <style>
                /* Visibilidad de paneles, limitada a este módulo */
                #mod-conceptos .k-pane{ display:none; }
                #mod-conceptos .k-pane.is-active{ display:block; }
                /* Estructura lateral/Texto (solo si no existe .two-col en tu CSS) */
                #mod-conceptos .two-col{ display:grid; grid-template-columns: minmax(260px, 420px) 1fr; gap:18px; align-items:start; }
                #mod-conceptos .two-col__media img{ width:100%; height:auto; border-radius:12px; box-shadow: 0 2px 10px rgba(0,0,0,.08); background:#fff; }
                #mod-conceptos .section-title{ margin: 0 0 .35rem; color:#34a853; font-size: clamp(1.2rem, 1.8vw, 1.8rem); }
                @media (max-width: 900px){
                #mod-conceptos .two-col{ grid-template-columns: 1fr; }
                }
            </style>

            <script>
                (function(){
                const root = document.getElementById('mod-conceptos');
                if(!root) return;
                const tabsWrap = root.querySelector('#conceptos-tabs');
                const panes = root.querySelectorAll('.k-pane');

                function activate(id){
                    // botones
                    tabsWrap.querySelectorAll('.chip').forEach(btn=>{
                    const on = btn.dataset.tab === id;
                    btn.classList.toggle('is-active', on);
                    btn.setAttribute('aria-selected', on ? 'true':'false');
                    });
                    // paneles
                    panes.forEach(p=>{
                    const on = p.id === `pane-${id}`;
                    p.classList.toggle('is-active', on);
                    p.setAttribute('aria-hidden', on ? 'false':'true');
                    });
                }

                tabsWrap.addEventListener('click', (e)=>{
                    const btn = e.target.closest('.chip');
                    if(!btn) return;
                    e.preventDefault();
                    activate(btn.dataset.tab);
                });

                // inicial
                const first = tabsWrap.querySelector('.chip.is-active') || tabsWrap.querySelector('.chip');
                if(first) activate(first.dataset.tab);
                })();
            </script>
            </section>
            <!-- ===================== /CONCEPTOS — FIN MÓDULO ===================== -->


                

                
                <!-- Grid de tarjetas -->
                <div class="row">
                <!-- Tarjeta Power BI -->
                    <!-- Tarjeta Tablero Asuntos de Género -->
                    <div class="col-md-4 mb-4">
                        <div class="card tablero-card"
                            data-iframe="https://app.powerbi.com/view?r=eyJrIjoiNjFlYTAwMWItOGY1MC00NzAzLWIyNTYtNDg3YzJjNmU1NmQ3IiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=ReportSection"
                            data-title="Indicadores Asuntos de Género">
                            <img src="assets/svg/img-genero/Informacion/informacion_tablero.png"
                                class="card-img-top"
                                alt="Tablero indicadores Asuntos de Género">
                            <div class="card-body text-center">
                                <h5 class="card-title">Asuntos de género</h5>
                                <p class="card-text small text-muted mb-3">
                                    El tablero de asuntos de género organiza y presenta indicadores clave que permiten analizar,
                                    desde una perspectiva de género, las realidades sociales, económicas, políticas y culturales del territorio.
                                </p>
                                <button class="btn btn-institucional ver-tablero-btn" type="button">
                                    Ver tablero
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta Boletín PDF -->
                    <div class="col-md-4 mb-4">
                        <div class="card tablero-card">
                            <img src="assets/svg/img-genero/Informacion/informacion_boletin.png"
                                class="card-img-top"
                                alt="Boletín de violencia dimensión social">
                            <div class="card-body text-center">
                                <h5 class="card-title">Boletín de violencia</h5>
                                <p class="card-text small text-muted mb-3">
                                    Este boletín analiza los indicadores de violencia registrados en Boyacá entre enero de 2020 y junio de 2024,
                                    con especial atención a la violencia que afecta a niños, niñas, adolescentes, mujeres y personas con identidades de género diversas.
                                    El documento busca aportar información clave para la formulación de políticas públicas, estrategias de prevención y atención a las víctimas.
                                </p>
                                <a href="assets/pdf/BOLETIN01-Violencia-dic-2024.pdf"
                                    class="pdf-link btn btn-institucional2 mt-2"
                                    data-title="Boletín de violencia dimensión social">
                                    Ver boletín
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta Observatorio CEPAL -->

                    <div class="col-md-4 mb-4">
                        <div class="card tablero-card"
                          
                            data-title="Observatorio de Igualdad y Género – CEPAL">

                            <img src="assets/svg/img-genero/Informacion/cepal-observatorio.png"
                                class="card-img-top"
                                alt="Observatorio de Igualdad de Género – CEPAL">

                            <div class="card-body text-center">
                                <h5 class="card-title">Observatorio de igualdad y género</h5>

                                <p class="card-text small text-muted mb-3">
                                    El Observatorio de Igualdad de Género de América Latina y el Caribe
                                    pone a disposición información oficial y seguimiento de acuerdos internacionales.
                                </p>

                                <!-- 🔗 Botón que abre el data-iframe en pestaña nueva -->
                                <a href="https://oig.cepal.org/es"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn-institucional">
                                    Ver Observatorio
                                </a>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card tablero-card"
                          
                            data-title="Observatorio de Igualdad y Género – CEPAL">

                            <img src="assets/svg/img-genero/Informacion/cepal-observatorio.png"
                                class="card-img-top"
                                alt="Observatorio de Igualdad de Género – CEPAL">

                            <div class="card-body text-center">
                                <h5 class="card-title">Boletín N°4 - Violencia feminicida en cifras</h5>

                                <p class="card-text small text-muted mb-3">
                                     Hacia la igualdad sustantiva y la sociedad del cuidado: 
                                        actuar con sentido de urgencia para garantizar a las mujeres y las niñas una vida libre de violencia,.
                                </p>

                                <!-- 🔗 Botón que abre el data-iframe en pestaña nueva -->
                                <a href="https://share.google/KnY1j4jv5TmaNqIyy"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn-institucional">
                                    Ver Boletín
                                </a>
                            </div>

                        </div>
                    </div>

   
                    
                    

            </div>
            </div>
        
        
    </div>

    <div class="tab-pane fade panel-purple" id="panel3">
   

        <div class="indicadores-section mt-4">
                    <div style="display: flex; align-items: center;">
                        <img src="assets/svg/img-genero/SEGUIMIENTO.svg" alt="Icono Seguimiento" style="width: 50px; height: 50px; margin-right: 15px;">
                        <h2 style="margin: 0;" class="indicador-title">Seguimiento a Mecanismos Municipales</h2>
                    </div>

                    <div class="seguimiento-block">
                    <div class="seguimiento-title"><span class="icon">📑</span> ¿Cuál es la función del Mecanismo articulador?</div>
                    <ul class="seguimiento-doc-list">
                    <p>
                        El <strong>Mecanismo Articulador para el Abordaje de las Violencias por Razón de Sexo y Género</strong> en Boyacá fue formalmente establecido mediante el 
                        <strong>Decreto 1231 del 12 de septiembre de 2022</strong>, expedido por la <strong>Gobernación de Boyacá</strong>.
                    </p>

                    <p>
                        Su objetivo es coordinar la respuesta interinstitucional para garantizar la <strong>atención, protección y justicia</strong> a las mujeres, niñas, niños, adolescentes y personas de identidades diversas víctimas de violencias basadas en género en el departamento.
                    </p>

                    <h5 class="mt-4">¿Para qué sirve?</h5>
                    <ul>
                        <li>🧭 <strong>Articula</strong> entidades de salud, justicia y protección para responder a casos complejos.</li>
                        <li>🛡️ <strong>Actúa cuando hay fallas o negligencia</strong> en la atención municipal o casos graves (feminicidios, violencia institucional, etc.).</li>
                        <li>🧩 <strong>Convoca salas situacionales</strong> para la toma de decisiones interinstitucionales.</li>
                        <li>📊 <strong>Realiza seguimiento</strong> hasta garantizar el acceso efectivo a los derechos.</li>
                        <li>📬 <strong>Recibe casos a través de organizaciones sociales o instituciones</strong>, no directamente por personas naturales.</li>
                    </ul>
                    <p></p>
                    <br>

                    </ul>
                    </div>

                    <div class="seguimiento-block">
                    <div class="seguimiento-title">
                        <span class="icon">🔍</span> Seguimiento a Mecanismos Municipales
                    </div>
                    <p style="font-size:1.08em;">
                        En esta sección encontrarás la metodología y los resultados del seguimiento realizado por la Secretaría de Gobierno y Acción Comunal de Boyacá a los mecanismos municipales para el abordaje de violencias por razones de sexo y género, según el Decreto 1231 de 2022 y la reglamentación vigente.
                    </p>
                    </div>

                    <div class="seguimiento-block">
                    <div class="seguimiento-title"><span class="icon">🚦</span> Semaforización departamental de mecanismos municipales</div>
                    <p>
                        Estado de respuesta y activación de los mecanismos municipales para el abordaje integral de violencias por razones de sexo y género en Boyacá (<b>datos a mayo 2025</b>):
                    </p>
                    <iframe src="mapMecanismo.php"
                            title="Mapa de Semaforización Municipal"
                            style="width:100%; height:600px; border:none;">
                    </iframe>
                    <p style="margin-top:10px;">
                        Puedes consultar el listado detallado de municipios y su estado descargando el informe actualizado:
                    </p>
                    <a href="assets/pdf/asuntosGenero/seguimiento/SEMAFORIZACIÓN ACTUALIZADA MECANISMOS MUNICIPALES.pdf" class="btn btn-link" target="_blank">
                        <span class="icon">📄</span> Descargar Semaforización completa &rarr;
                    </a>
                    </div>

                    <!-- Acordeón de Fechas de Salas y Casos -->
                    <div class="seguimiento-block">
                    <div class="seguimiento-title"><span class="icon">🖥️</span>salas situacionales y casos atendidos</div>
                    <p>Dando alcance a lo establecido en el Decreto 1231 de 2022 emitido por la Gobernación de 
                    Boyacá, la Secretaría de Gobierno y Acción Comunal ejerce como Secretaría Técnica del 
                    Mecanismo Articulador para el Abordaje de las Violencias por Razón de Sexo y Género de 
                    Mujeres, Niñas, Niños, y Adolescentes del Departamento de Boyacá.</p>
                    <br>
                    <p>En concordancia con lo anterior, me permito detallar la metodología empleada para el 
                    seguimiento de los casos allegados al Mecanismo Articulador departamental, cuyo 
                    reglamento interno fue aprobado el 18 de diciembre de 2024.</p>
                    <br>
                    <p>En este caso, las salas situacionales que buscan garantizar la respuesta institucional, según 
                    competencia, en primera medida se encuentra a nivel municipal y, en segunda medida, a 
                    nivel departamental, surtiéndose así, un agotamiento real y material de las instancias 
                    territoriales, así como el debido proceso, respetando la autonomía y la desconcentración 
                    administrativa.</p>
                    <br>
                    <p>Desde este espacio se busca, superar cualquier barrera de acceso a la justicia, protección y 
                    salud, que puedan presentar las víctimas de violencia por razones de sexo y género en 
                    mujeres y NNA.</p>
                    <br>
                    <p>Dicho lo anterior, me permito informar que posterior a la expedición del Decreto 1231 de 
                    fecha 12 de septiembre de 2022, se han realizado las siguientes salas situacionales:
                    </p>
                    <ul class="seguimiento-list">
                        <li><span class="icon">🗂️</span> <b>Año:</b> Número de casos</li>
                        <li><span class="icon">🗂️</span> <b>2023:</b> 4</li>
                        <li><span class="icon">🗂️</span> <b>2024:</b> 7</li>
                        <li><span class="icon">🗂️</span> <b>2025:</b> 4</li>
                    </ul>
                    </div>

                    <div class="seguimiento-block">
                    <div class="seguimiento-title"><span class="icon">📑</span> ¿Cómo se activa el Mecanismo Articulador en tu municipio?</div>
                    <ul class="seguimiento-doc-list">
                    <p>

                    <h3><strong>  Pasos a seguir si eres víctima de violencia de género: </strong> </h3>
                    <p>
                        Si eres víctima de violencia basada en género y las entidades locales (Salud, Protección o Justicia) no te brindan una atención adecuada, puedes solicitar la activación del Mecanismo Articulador. A continuación, te explicamos paso a paso cómo realizar este proceso:
                    </p>
                    <iframe src="rutaVictimas.html"
                            title="Mapa de Semaforización Municipal"
                            style="width:100%; height:650px; border:none;">
                    </iframe>
                    </ul>
                    </div>

                </div>        




    </div>

    <div class="tab-pane fade panel-white" id="panel4">
   
    
                <div class="indicadores-section mt-4">
                    <div style="display: flex; align-items: center;">
                    <img src="assets/svg/img-genero/RUTA-ATENCION.svg"
                        alt="Icono Ruta de atención"
                        style="width: 50px; height: 50px; margin-right: 15px;"
                        loading="lazy">
                    <h2 style="margin: 0;" class="indicador-title">Ruta de Atención Integral</h2>
                    </div>

                    <div class="row my-3">
                    <div class="col-md-12">
                        <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle fa-lg me-2" style="color:#a63f95;"></i>
                        <span>
                            <b>La Ruta de Atención Integral</b> busca garantizar la atención oportuna, integral y sin barreras para mujeres, niñas, niños y adolescentes víctimas de violencias basadas en género en Boyacá, articulando los sectores de salud, protección y justicia.
                        </span>
                        </div>
                    </div>
                    </div>

                    <!-- Contenedor de acordeón (opcional si usas Bootstrap) -->
                    <div class="accordion">
                    <!-- Acordeón: Intersectorial -->
                    <div class="accordion-item">
                        <button class="accordion-button" onclick="toggleAccordion(this)" aria-expanded="false">
                        <i class="fas fa-hospital me-2" style="color:#28a745;"></i> Ruta de atención intersectorial
                        </button>
                        <div class="accordion-content intersectorial">
                        <!-- Bloque 2 columnas -->
                        <section class="two-col" style="--media-w: clamp(280px, 36vw, 460px); --media-h: clamp(240px, 42vh, 440px);">
                            <!-- Columna izquierda: imagen -->
                            <figure class="two-col__media">
                                <img
                                src="assets/svg/img-genero/RutaAtencion/intersectorial.png" 
                                alt="Ilustración de la Ruta Intersectorial"
                                loading="lazy"
                                />
                            </figure>

                            <!-- Columna derecha: tu texto -->
                            <div class="two-col__text">
                                <div class="info-grid">
                                <div class="info-label">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    La Ruta Intersectorial:
                                </div>
                                <div class="info-value">
                                    Es un conjunto de <strong>procedimientos</strong> y <strong>acciones coordinadas</strong> entre
                                    diferentes entidades (salud, educación, justicia, protección, entre otras).<br><br>
                                    Garantiza <strong>atención integral</strong>, <strong>protección</strong>, <strong>prevención</strong> y
                                    <strong>restablecimiento de derechos</strong> de personas víctimas de violencia o en riesgo.
                                </div>

                                <div class="info-label">
                                    <i class="fas fa-balance-scale"></i>
                                    Está dirigida principalmente a:
                                </div>
                                <div class="info-value">
                                    Mujeres y niñas víctimas de <strong>violencia de género</strong> (física, sexual, psicológica,
                                    económica o patrimonial).<br><br>
                                    Incluye a <strong>niños, niñas y adolescentes</strong> en situaciones de violencia sexual o
                                    intrafamiliar cuando se enmarca en VBG.<br><br>
                                    También puede aplicarse a <strong>hombres</strong> cuando sean víctimas de violencia sexual u otras
                                    violencias por razones de género.
                                </div>
                                </div>
                            </div>
                        </section>

                        <iframe src="rutaIntersectorial.html"
                                title="Ruta intersectorial para atención integral en VBG"
                                style="width:100%; height:600px; border:none;"
                                loading="lazy">
                        </iframe>
                        </div>
                    </div>

                    <!-- Acordeón: Salud -->
                    <div class="accordion-item">
                        <button class="accordion-button" onclick="toggleAccordion(this)" aria-expanded="false">
                        <i class="fas fa-notes-medical me-2" style="color:#007bff;"></i> Salud
                        </button>
                        <div class="accordion-content salud">
                        <div class="info-grid">
                            <div class="info-label"><i class="fas fa-user-shield"></i> Activación sector Protección:</div>
                            <div class="info-value">Salud activa de inmediato a protección (ICBF si es menor de 18; Comisaría de Familia si es mujer adulta) y, en paralelo, justicia (Fiscalía/Policía Judicial) para restablecimiento de derechos.</div>
                            <div class="info-label"><i class="fas fa-home"></i> Medidas de protección:</div>
                            <div class="info-value">Desde salud: orientación y apoyo psicosocial/mental, y coordinación de remisiones. Con protección: gestión de casas de acogida y otras medidas. Cuando se requiera para la atención en salud, la EPS debe garantizar alojamiento, alimentación y transporte.</div>
                        </div>
                        
                        <br>
                        <h1><strong>Ruta interactiva: 15 pasos para atención integral en salud de víctimas de violencia sexual</strong></h1>
                        
                        <iframe src="ruta15pasos.html"
                                title="Ruta de 15 pasos: atención a mujeres víctimas de violencia sexual"
                                style="width:100%; height:600px; border:none;"
                                loading="lazy">
                        </iframe>

                        <a href="assets/pdf/asuntosGenero/ruta/CIRCULAR ACTIVACIÓN RUTA VBG FINAL.pdf"
                            class="pdf-link btn btn-institucional mt-2"
                            data-title="Circular Ruta VBG Salud">
                            <i class="fas fa-file-pdf"></i> Ver Circular completa
                        </a>
                        <a href="assets/pdf/asuntosGenero/ruta/resolucion-0459-de-2012%20salud.pdf"
                            class="pdf-link btn btn-institucional2 mt-2"
                            data-title="Resolución 0459 de 2012">
                            <i class="fas fa-file-pdf"></i> Protocolo Salud
                        </a>
                        </div>
                    </div>

                    <!-- Acordeón: Justicia -->
                    <div class="accordion-item">
                        <button class="accordion-button" onclick="toggleAccordion(this)" aria-expanded="false">
                        <i class="fas fa-gavel me-2" style="color:#c62828;"></i> Justicia
                        </button>
                        <div class="accordion-content justicia">
                        <section class="two-col two-col--sticky">
                    <!-- Izquierda: imagen -->
                    <figure class="two-col__media">
                        <img
                        src="assets/svg/img-genero/RutaAtencion/justicia.png"  
                        alt="Activación de la ruta de justicia"
                        loading="lazy"
                        />
                    </figure>

                    <!-- Derecha: contenido -->
                    <div class="two-col__text">
                        <div class="info-grid">

                        <div class="info-label">
                            <i class="fas fa-route"></i>
                            Activación de la ruta (Justicia):
                        </div>
                        <div class="info-value">
                            Desde salud se activa de forma inmediata el sector <strong>justicia</strong>:
                            <strong>Fiscalía</strong> o <strong>Policía Judicial</strong> (<strong>SIJIN</strong>, <strong>DIJIN</strong>, <strong>CTI</strong>) y,
                            si no es posible, se informa a la <strong>Policía Nacional</strong> (estaciones o líneas de denuncia).
                            Se hace en <strong>paralelo</strong> con protección cuando aplique.
                        </div>

                        <div class="info-label">
                            <i class="fas fa-hand-paper"></i>
                            ¿Dónde denunciar?
                        </div>
                        <div class="info-value">
                            <strong>Fiscalía</strong> (URI, seccionales) o <strong>Policía</strong> (SIJIN/DIJIN/CTI; estaciones; líneas de denuncia).
                            La activación puede realizarla directamente el <strong>prestador de salud</strong> con la víctima.
                        </div>

                        <div class="info-label">
                            <i class="fas fa-vial"></i>
                            Evidencia y forense:
                        </div>
                        <div class="info-value">
                            Toma de <strong>muestras forenses</strong> y manejo de <strong>evidencia</strong> en articulación con
                            <strong>Medicina Legal</strong> y cuerpos de <strong>Fiscalía</strong>, siguiendo los protocolos del
                            <strong>INMLCF</strong> (<em>cadena de custodia</em>).
                        </div>

                        <div class="info-label">
                            <i class="fas fa-user-shield"></i>
                            Medidas urgentes (NNA y adultas/os):
                        </div>
                        <div class="info-value">
                            Si la víctima es <strong>menor de 18 años</strong>: activar de inmediato <strong>Defensor(a) de Familia – ICBF</strong>.
                            En <strong>mujeres adultas</strong>: activar <strong>Comisaría de Familia</strong> para medidas de protección;
                            la activación de <strong>justicia</strong> es simultánea.
                        </div>

                        <div class="info-label">
                            <i class="fas fa-balance-scale"></i>
                            Enfoque de derechos y no revictimización:
                        </div>
                        <div class="info-value">
                            La ruta intersectorial (<strong>salud–protección–justicia</strong>) opera para <strong>restituir derechos</strong>,
                            con <strong>acceso oportuno</strong> y <strong>sin revictimizar</strong>; incluye apoyo para la
                            <strong>judicialización</strong> del caso.
                        </div>

                        <div class="info-label">
                            <i class="fas fa-info-circle"></i>
                            Acompañamiento e información:
                        </div>
                        <div class="info-value">
                            El equipo de salud <strong>orienta</strong> a la víctima y su familia sobre <strong>pasos legales</strong>,
                            contactos y seguimiento; <strong>reporta</strong> y <strong>articula</strong> con justicia y protección.
                        </div>

                        </div>
                    </div>
                    </section>

                        <a href="assets/pdf/asuntosGenero/ruta/fiscalia.pdf"
                            class="pdf-link btn btn-institucional2 mt-2"
                            data-title="Canales de atención Fiscalía">
                            <i class="fas fa-file-pdf"></i> Canales de atención
                        </a>
                        </div>
                    </div>

                    <!-- Acordeón: Ruta integral NNA - ICBF -->
                    <div class="accordion-item">
                        <button class="accordion-button" onclick="toggleAccordion(this)" aria-expanded="false">
                        <i class="fas fa-people-arrows me-2" style="color:#009688;"></i> Ruta Integral para la Protección de Niñas, Niños y Adolescentes – ICBF
                        </button>
                        <div class="accordion-content salud">
                        <p style="margin-bottom: 20px; text-align: justify;">
                            Esta <strong>Ruta Integral para la Protección de Niñas, Niños y Adolescentes</strong> establece el paso a paso para la <strong>identificación, activación y seguimiento</strong> de casos de vulneración de derechos, garantizando su protección integral.
                            Involucra la articulación del <strong>ICBF</strong> con Comisarías de Familia, Policía Nacional, Fiscalía y demás entidades competentes, aplicando un enfoque de género, etario y diferencial.
                            Contempla la atención de situaciones de violencia física, psicológica, sexual, negligencia, abandono y afectaciones económicas o laborales, asegurando los derechos a la <em>verdad, la justicia y la reparación integral</em>.
                        </p>
                        <<section class="two-col two-col--sticky">
  <!-- Izquierda: imagen -->
  <figure class="two-col__media">
    <img
      src="assets/svg/img-genero/RutaAtencion/icbf.png" 
      alt="Identificación y activación de la ruta para NNA"
      loading="lazy"
    />
  </figure>

  <!-- Derecha: contenido -->
  <div class="two-col__text">
    <div class="info-grid">
      <div class="info-label">
        <i class="fas fa-search-location"></i>
        Identificación de casos:
      </div>
      <div class="info-value">
        Paso inicial para detectar cualquier situación de <strong>vulneración de derechos</strong> en niñas, niños y adolescentes.
      </div>

      <div class="info-label">
        <i class="fas fa-play-circle"></i>
        Activación de la ruta:
      </div>
      <div class="info-value">
        Coordinación inmediata entre <strong>ICBF</strong>, Comisarías de Familia, Policía Nacional, Fiscalía y otras entidades competentes.
      </div>

      <div class="info-label">
        <i class="fas fa-venus-mars"></i>
        Enfoque diferencial:
      </div>
      <div class="info-value">
        Aplicación de <strong>enfoque de género, etario y diferencial</strong> en todas las etapas del proceso.
      </div>

      <div class="info-label">
        <i class="fas fa-users"></i>
        Casos atendidos:
      </div>
      <div class="info-value">
        Atención a casos <strong>familiares</strong> y <strong>no familiares</strong> de violencia física, psicológica, sexual, negligencia o abandono.
      </div>

      <div class="info-label">
        <i class="fas fa-balance-scale"></i>
        Derechos garantizados:
      </div>
      <div class="info-value">
        Protección del derecho a la <em>verdad, justicia y reparación integral</em> para todas las víctimas.
      </div>
    </div>
  </div>
</section>

                        <a href="assets/pdf/asuntosGenero/ruta/Ruta%20NNA.pdf"
                            class="pdf-link btn btn-institucional mt-2"
                            data-title="Ruta de atención en violencias">
                            <i class="fas fa-file-pdf"></i> Ver Infografía ruta completa
                        </a>
                        </div>
                    </div>

                    <!-- Acordeón: Guías y Rutas Defensoría -->
                    <div class="accordion-item">
                        <button class="accordion-button" onclick="toggleAccordion(this)" aria-expanded="false">
                        <i class="fas fa-map-marked-alt me-2" style="color:#006666;"></i> Guías y rutas en asuntos de género por la defensoría del pueblo
                        </button>
                        <div class="accordion-content justicia">
                        <div class="seguimiento-block">
                            <div class="seguimiento-title"><span class="icon">🛡️</span> Guías y rutas defensoriales para la atención integral</div>

                            <div style="font-size: 16px;">
                            <p style="text-align: justify;">
                                En esta sección se presentan <strong>guías operativas y rutas institucionales</strong> diseñadas por la <strong>Defensoría del Pueblo de Colombia</strong>, orientadas a garantizar una atención integral, oportuna y con enfoque diferencial para víctimas de violencia, discriminación y otras vulneraciones de derechos.
                            </p>
                            <p style="text-align: justify;">
                                Estas herramientas están dirigidas a profesionales de salud, funcionarios públicos, organizaciones sociales, defensorías regionales y ciudadanía en general, facilitando el acceso a la justicia, la protección integral y la prevención de riesgos. Incluyen procedimientos para atender casos de violencia sexual, trata de personas, rectificación de identidad, derechos sexuales y reproductivos, entre otros.
                            </p>
                            </div>

                            <p>Consulta y descarga las guías defensoriales disponibles:</p>

                            <!-- Grid de tarjetas -->
                            <div class="row">
                            <!-- Guía 1 -->
                            <div class="col-md-4 mb-4">
                                <div class="card tablero-card">
                                <img src="assets/svg/img-genero/RutaAtencion/DPGuias-01.png"
                                    class="card-img-top"
                                    alt="Guía defensorial para la atención integral de víctimas sobrevivientes de violencia sexual"
                                    loading="lazy">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Guía defensorial para la atención integral de víctimas sobrevivientes de violencia sexual</h5>
                                    <a href="assets/pdf/asuntosGenero/ruta/1.%20GUIA%20DEFENSORIAL%20PARA%20LA%20ATENCION%20INTEGRAL%20DE%20VICTIMAS.pdf"
                                    class="pdf-link btn btn-institucional2 mt-2"
                                    data-title="Guía defensorial para la atención integral de víctimas sobrevivientes de violencia sexual">
                                    Ver documento
                                    </a>
                                </div>
                                </div>
                            </div>

                            <!-- Guía 2 -->
                            <div class="col-md-4 mb-4">
                                <div class="card tablero-card">
                                <img src="assets/svg/img-genero/RutaAtencion/DPGuias-02.png"
                                    class="card-img-top"
                                    alt="Guía para definir la situación militar de hombres transgénero"
                                    loading="lazy">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Guía para definir la situación militar de hombres transgénero</h5>
                                    <a href="assets/pdf/asuntosGenero/ruta/2.%20GUIA%20PARA%20DEFINIR%20LA%20SITUACION%20MILITAR%20DE%20HOMBRES%20TRANSGENERO.pdf"
                                    class="pdf-link btn btn-institucional2 mt-2"
                                    data-title="Guía para definir la situación militar de hombres transgénero">
                                    Ver documento
                                    </a>
                                </div>
                                </div>
                            </div>

                            <!-- Guía 3 -->
                            <div class="col-md-4 mb-4">
                                <div class="card tablero-card">
                                <img src="assets/svg/img-genero/RutaAtencion/DPGuias-03.png"
                                    class="card-img-top"
                                    alt="Guía para la garantía del derecho a la interrupción voluntaria del embarazo - IVE"
                                    loading="lazy">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Guía para la garantía del derecho a la interrupción voluntaria del embarazo - IVE</h5>
                                    <a href="assets/pdf/asuntosGenero/ruta/3.%20GUIA%20PARA%20LA%20GARANTIA%20DEL%20DERECHO%20A%20LA%20INTERRUPCI%C3%93N%20VOLUNTARIA%20DEL%20EMBARAZO.pdf"
                                    class="pdf-link btn btn-institucional2 mt-2"
                                    data-title="Guía para la garantía del derecho a la interrupción voluntaria del embarazo - IVE">
                                    Ver documento
                                    </a>
                                </div>
                                </div>
                            </div>

                            <!-- Guía 4 -->
                            <div class="col-md-4 mb-4">
                                <div class="card tablero-card">
                                <img src="assets/svg/img-genero/RutaAtencion/DPGuias-04.png"
                                    class="card-img-top"
                                    alt="Guía para rectificar el componente sexo en el documento de identidad de niñas, niños y adolescentes"
                                    loading="lazy">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Guía para rectificar el componente sexo en el documento de identidad de niñas, niños y adolescentes</h5>
                                    <a href="assets/pdf/asuntosGenero/ruta/4.%20GUIA%20PARA%20RECTIFICAR%20EL%20COMPONENTE%20SEXO%20EN%20EL%20DOCUMENTO%20DE%20IDENTIDAD.pdf"
                                    class="pdf-link btn btn-institucional2 mt-2"
                                    data-title="Guía para rectificar el componente sexo en el documento de identidad de niñas, niños y adolescentes">
                                    Ver documento
                                    </a>
                                </div>
                                </div>
                            </div>

                            <!-- Guía 5 -->
                            <div class="col-md-4 mb-4">
                                <div class="card tablero-card">
                                <img src="assets/svg/img-genero/RutaAtencion/DPGuias-05.png"
                                    class="card-img-top"
                                    alt="Ruta defensorial para la atención de víctimas de violencia intrafamiliar con enfoque de género"
                                    loading="lazy">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Ruta defensorial para la atención de víctimas de violencia intrafamiliar con enfoque de género</h5>
                                    <a href="assets/pdf/asuntosGenero/ruta/5.%20Ruta%20Defensorial%20Atencion%20de%20victimas%20de%20violencia%20intrafamiliar%20con%20enfoque%20de%20genero.pdf"
                                    class="pdf-link btn btn-institucional2 mt-2"
                                    data-title="Ruta defensorial para la atención de víctimas de violencia intrafamiliar con enfoque de género">
                                    Ver documento
                                    </a>
                                </div>
                                </div>
                            </div>

                            <!-- Guía 6 -->
                            <div class="col-md-4 mb-4">
                                <div class="card tablero-card">
                                <img src="assets/svg/img-genero/RutaAtencion/DPGuias-06.png"
                                    class="card-img-top"
                                    alt="Ruta defensorial para la identificación y acompañamiento a víctimas de trata de personas"
                                    loading="lazy">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Ruta defensorial para la identificación y acompañamiento a víctimas de trata de personas</h5>
                                    <a href="assets/pdf/asuntosGenero/ruta/6.%20Ruta%20Defensorial%20para%20la%20identificacion%20y%20acomp%20a%20victimas%20de%20Trata%20de%20Personas_Mujer.pdf"
                                    class="pdf-link btn btn-institucional2 mt-2"
                                    data-title="Ruta defensorial para la identificación y acompañamiento a víctimas de trata de personas">
                                    Ver documento
                                    </a>
                                </div>
                                </div>
                            </div>
                            </div> <!-- Fin Grid -->
                        </div>
                        </div>
                    </div>
                    </div> <!-- Fin .accordion -->
                </div>
                </div>




    </div>

    <div class="tab-pane fade panel-purple" id="panel5">


<div class="indicadores-section mt-4">
                <div style="display: flex; align-items: center;">
                    <img src="assets/svg/img-genero/BARRERAS-ACCESO.svg" alt="Icono Barreras de acceso" style="width: 50px; height: 50px; margin-right: 15px;">
                    
                    <section id="barreras-acceso" class="rich-block">
                    <h3>
                        <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444"></i>
                        Barreras de acceso: identifícalas y actúa
                    </h3>
                    <p style="margin-top:6px">
                        Si no te atienden de forma <strong>oportuna</strong>, <strong>eficaz</strong> y con <strong>calidad</strong>, podrías estar
                        enfrentando una <strong>barrera de acceso</strong>. Aquí te explicamos qué es, qué leyes te protegen, ejemplos comunes
                        y cómo actuar según el canal.
                    </p>

                    <!-- Navegación principal -->
                    <div class="chip-group" role="tablist" aria-label="Secciones de barreras de acceso">
                        <button class="chip is-active" data-target="quees" role="tab" aria-selected="true">
                        <i class="fa-solid fa-circle-question"></i> ¿Qué es?
                        </button>
                        <button class="chip" data-target="leyes" role="tab" aria-selected="false">
                        <i class="fa-solid fa-scale-balanced"></i> Tus derechos (leyes)
                        </button>
                        <button class="chip" data-target="ejemplos" role="tab" aria-selected="false">
                        <i class="fa-solid fa-list"></i> Ejemplos
                        </button>
                        <button class="chip" data-target="quehacer" role="tab" aria-selected="false">
                        <i class="fa-solid fa-life-ring"></i> ¿Qué hacer?
                        </button>
                    </div>

                    <!-- 1) Qué es -->
                    <section class="mod-section" data-sec="quees">
                        <div class="two-col two-col--sticky">
                        <figure class="two-col__media">
                            <img src="assets/svg/img-genero/barreras/barreraAcceso.png" alt="¿Qué es una barrera de acceso?" loading="lazy">
                        </figure>
                        <div class="two-col__text">
                            <h4><i class="fa-solid fa-circle-question" style="color:#7c3aed"></i> ¿Qué es una barrera de acceso?</h4>
                            <p>
                            Es cualquier <strong>obstáculo</strong> que impide o retrasa que recibas atención en salud de forma
                            <strong>oportuna</strong>, <strong>eficaz</strong> y con <strong>calidad</strong>. Puede suceder en
                            urgencias, consultas, exámenes, entrega de medicamentos o en los trámites con tu EPS/IPS.
                            </p>
                            <p>
                            Si te <strong>demoran</strong> citas, te <strong>niegan</strong> servicios sin explicación, te exigen
                            <strong>autorizaciones innecesarias</strong> o te <strong>trasladan</strong> de un lugar a otro sin resolver,
                            podrías estar ante una barrera de acceso.
                            </p>
                            <div class="callout" style="margin-top:10px">
                            <div style="display:grid;grid-template-columns:28px 1fr;gap:10px;align-items:start">
                                <i class="fa-solid fa-hand-holding-heart" style="color:#16a34a"></i>
                                <div>Tu atención debe ser <strong>sin barreras</strong>, con trato digno y respetando tus tiempos.</div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </section>

                    <!-- 2) Leyes -->
                    <section class="mod-section is-hidden" data-sec="leyes">
                        <div class="two-col two-col--sticky">
                        <figure class="two-col__media">
                            <img src="assets/svg/img-genero/barreras/derechos.png" alt="Leyes que te protegen" loading="lazy">
                        </figure>
                        <div class="two-col__text">
                            <h4><i class="fa-solid fa-scale-balanced" style="color:#0ea5e9"></i> Tus derechos (leyes que te respaldan)</h4>
                            <ul class="k-list">
                            <li><strong>Derecho fundamental a la salud</strong>: acceso <em>oportuno, eficaz y de calidad</em>.</li>
                            <li><strong>Atención a violencias</strong> (incluida violencia sexual): <em>prioritaria e inmediata</em>, sin
                                exigir trámites que la demoren.</li>
                            <li><strong>Rutas integrales</strong> (RIAS): la EPS/IPS debe garantizar continuidad y seguimiento (controles,
                                medicamentos, remisiones).</li>
                            <li><strong>Trato digno y enfoque diferencial</strong>: información clara, sin discriminación y con
                                ajustes razonables si los necesitas.</li>
                            </ul>
                            <div class="mini-tip"><i class="fa-solid fa-circle-info"></i> Puedes pedir que citen la norma aplicable en el
                            acto de atención y solicitar copia del soporte.</div>
                            <!-- Espacio para referencias legales locales -->
                            <details class="exp" style="margin-top:10px">
                            <summary><i class="fa-solid fa-book"></i> Referencias legales (edita/ajusta según tu entidad)</summary>
                            <div class="exp-body">
                                <ul>
                                <li>Ley Estatutaria de Salud (derecho fundamental a la salud).</li>
                                <li>Protocolo/Guía de atención a víctimas de violencia sexual (atención inmediata).</li>
                                <li>Resoluciones de Rutas Integrales de Atención en Salud (RIAS).</li>
                                <li>Ley de Violencia contra la mujer y normas complementarias.</li>
                                </ul>
                            </div>
                            </details>
                        </div>
                        </div>
                    </section>

                    <!-- 3) Ejemplos (con sub-chips) -->
                    <section class="mod-section is-hidden" data-sec="ejemplos">
                        <div class="two-col two-col--sticky">
                        <figure class="two-col__media">
                            <img src="assets/svg/img-genero/barreras/ejemplos.png" alt="Ejemplos de barreras" loading="lazy">
                        </figure>
                        <div class="two-col__text">
                            <h4><i class="fa-solid fa-list" style="color:#f59e0b"></i> Ejemplos de barreras</h4>

                            <div class="chip-group k-subchips" role="tablist" aria-label="Tipos de barreras">
                            <button class="chip is-active" data-sub="adm" role="tab" aria-selected="true"><i class="fa-solid fa-file-shield"></i> Administrativas</button>
                            <button class="chip" data-sub="disp" role="tab" aria-selected="false"><i class="fa-solid fa-capsules"></i> Disponibilidad</button>
                            <button class="chip" data-sub="geo" role="tab" aria-selected="false"><i class="fa-solid fa-location-dot"></i> Geográficas/horarios</button>
                            <button class="chip" data-sub="eco" role="tab" aria-selected="false"><i class="fa-solid fa-coins"></i> Económicas</button>
                            <button class="chip" data-sub="info" role="tab" aria-selected="false"><i class="fa-solid fa-comments"></i> Información/Trato</button>
                            </div>

                            <!-- Cards por subcategoría -->
                            <div class="k-cards" data-subsec="adm">
                            <article class="k-card">
                                <h5><i class="fa-solid fa-file-circle-xmark"></i> Autorizaciones innecesarias</h5>
                                <p>Te exigen documentos o pasos que no aplican para tu caso o que ya cumpliste, retrasando la atención.</p>
                            </article>
                            <article class="k-card">
                                <h5><i class="fa-solid fa-person-walking-arrow-loop-left"></i> “Paseo” entre IPS</h5>
                                <p>Te envían de un servicio a otro sin solución ni citas claras.</p>
                            </article>
                            <article class="k-card">
                                <h5><i class="fa-solid fa-clock-rotate-left"></i> Demoras injustificadas</h5>
                                <p>Te dan citas muy lejanas o no responden a remisiones urgentes.</p>
                            </article>
                            </div>

                            <div class="k-cards is-hidden" data-subsec="disp">
                            <article class="k-card">
                                <h5><i class="fa-solid fa-calendar-xmark"></i> Sin agenda disponible</h5>
                                <p>No hay citas en un tiempo razonable para tu necesidad.</p>
                            </article>
                            <article class="k-card">
                                <h5><i class="fa-solid fa-syringe"></i> Falta de medicamentos/insumos</h5>
                                <p>No entregan medicación o no realizan procedimientos por desabastecimiento.</p>
                            </article>
                            </div>

                            <div class="k-cards is-hidden" data-subsec="geo">
                            <article class="k-card">
                                <h5><i class="fa-solid fa-road"></i> Distancia y transporte</h5>
                                <p>Debes viajar largas distancias o no hay transporte seguro.</p>
                            </article>
                            <article class="k-card">
                                <h5><i class="fa-solid fa-business-time"></i> Horarios poco accesibles</h5>
                                <p>El servicio no tiene horarios compatibles con tus posibilidades.</p>
                            </article>
                            </div>

                            <div class="k-cards is-hidden" data-subsec="eco">
                            <article class="k-card">
                                <h5><i class="fa-solid fa-wallet"></i> Costos que frenan</h5>
                                <p>Gastos de traslado, alimentación o copagos que impiden continuar el proceso.</p>
                            </article>
                            </div>

                            <div class="k-cards is-hidden" data-subsec="info">
                            <article class="k-card">
                                <h5><i class="fa-solid fa-language"></i> Información poco clara</h5>
                                <p>Lenguaje técnico o falta de explicaciones sobre tus derechos y pasos a seguir.</p>
                            </article>
                            <article class="k-card">
                                <h5><i class="fa-solid fa-user-slash"></i> Maltrato o discriminación</h5>
                                <p>Comentarios estigmatizantes o trato no digno; faltan intérpretes o ajustes razonables.</p>
                            </article>
                            </div>
                        </div>
                        </div>
                    </section>

                    <!-- 4) Qué hacer -->
                    <section class="mod-section is-hidden" data-sec="quehacer">
                        <div class="two-col two-col--sticky">
                        <figure class="two-col__media">
                            <img src="assets/svg/img-genero/barreras/quehacer.png" alt="Qué hacer si hay barreras" loading="lazy">
                        </figure>
                        <div class="two-col__text">
                            <h4><i class="fa-solid fa-life-ring" style="color:#22c55e"></i> ¿Qué hacer ante una barrera?</h4>

                            <details class="exp" open>
                            <summary><i class="fa-solid fa-hospital"></i> Urgencias y ruta clínica</summary>
                            <div class="exp-body">
                                Si es <strong>violencia sexual</strong>, pide <strong>atención por urgencias</strong> inmediata y la activación de la ruta clínica.
                                No deben exigirte trámites que la demoren.
                            </div>
                            </details>

                            <details class="exp">
                            <summary><i class="fa-solid fa-building"></i> Tu EPS / IPS (PQRD)</summary>
                            <div class="exp-body">
                                Radica una <strong>PQRD</strong> explicando la barrera y solicita solución prioritaria. Pide número de radicado y copia.
                            </div>
                            </details>

                            <details class="exp">
                            <summary><i class="fa-solid fa-shield"></i> Superintendencia de Salud</summary>
                            <div class="exp-body">
                                Reporta el caso. Línea nacional <strong>01 8000 513 700</strong> y canal virtual. Pide atención prioritaria si hay riesgo.
                            </div>
                            </details>

                            <details class="exp">
                            <summary><i class="fa-solid fa-phone"></i> Orientación y apoyo</summary>
                            <div class="exp-body">
                                <ul>
                                <li><strong>155</strong> orientación a mujeres 24/7.</li>
                                <li><strong>141</strong> ICBF (niñas, niños y adolescentes).</li>
                                <li>Orientación ciudadana del Ministerio de Salud.</li>
                                </ul>
                            </div>
                            </details>

                            <div class="actions" style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                            <a class="btn btn-secondary" href="tel:155"><i class="fa-solid fa-phone-volume"></i> Llamar 155</a>
                            </div>
                        </div>
                        </div>
                    </section>
                    </section>


                </div>
                    <!-- iframe de Power BI ajustado al 100% -->
                    <p> Barreras de acceso</p>
                </div>


    </div>

    <div class="tab-pane fade panel-white" id="panel6">
       

<div class="indicadores-section mt-4">
                    
                .m9<div style="display: flex; align-items: center;">
                        <img src="assets/svg/img-genero/ATENCION-INTEGRAL.svg" alt="Icono Atención Integral" style="width: 50px; height: 50px; margin-right: 15px;">
                        <h2 style="margin: 0;" class="indicador-title">Atención Integral</h2>
                    </div>
                    <!-- Información atención integral -->
                    <p></p>
                </div>


                    <div class="seguimiento-block">
                        <div class="seguimiento-title"><span class="icon">🩺</span> ¿Qué es la atención integral?</div>
                        <p>
                            La atención integral incluye la articulación de diferentes servicios para proteger y asistir a las mujeres víctimas de violencia y a sus familias. Generalmente comprende:
                        </p>
                        <ul>
                            <li>Atención médica</li>
                            <li>Atención psicológica</li>
                            <li>Asesoría jurídica</li>
                            <li>Protección temporal en casas de acogida</li>
                            <li>Seguimiento institucional</li>
                        </ul>
                    </div>

                    <div class="seguimiento-block">
                        <div class="seguimiento-title"><span class="icon">📊</span> Resultados de las Medidas de Atención Integral años 2023 y 2024</div>

                        <p style="max-width: 800px; margin: 20px auto; font-size: 16px; line-height: 1.6; text-align: justify;">
                            La siguiente gráfica muestra el resultado de la atención brindada a mujeres víctimas de violencia basada en género y sus familias, en el periodo comprendido entre el <strong>27 de octubre de 2023</strong> y el <strong>26 de agosto de 2024</strong>. 
                            Durante este tiempo se atendieron <strong>18 grupos familiares</strong>, cada uno de ellos conformado por una mujer víctima y sus hijos, hijas o personas acompañantes. 
                            A todos los grupos se les entregaron <strong>kits de aseo</strong> como parte del apoyo inicial, beneficiando directamente a <strong>18 mujeres</strong> y <strong>25 menores o acompañantes</strong>.
                        </p>

                        <!-- CONTENEDOR RESPONSIVO -->
                        <div style="position: relative; width: 100%; max-width: 600px; margin: 0 auto;">
                            <canvas id="graficoGrupos"></canvas>
                        </div>

                        <script>
                            // Plugin simple para mostrar etiquetas sobre las barras
                            const dataLabelPlugin = {
                                id: 'dataLabelPlugin',
                                afterDatasetsDraw(chart, args, pluginOptions) {
                                const { ctx, data, chartArea: { top } } = chart;
                                const isSmall = window.matchMedia('(max-width: 576px)').matches;
                                const ds = data.datasets[0];
                                const meta = chart.getDatasetMeta(0);
                                const total = ds.data.reduce((a, b) => a + b, 0);

                                ctx.save();
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';
                                ctx.font = (pluginOptions && pluginOptions.font) || (isSmall ? '10px sans-serif' : '12px sans-serif');
                                ctx.fillStyle = (pluginOptions && pluginOptions.color) || '#111';

                                meta.data.forEach((bar, i) => {
                                    const value = ds.data[i];
                                    const percent = total ? Math.round((value / total) * 100) : 0;

                                    // Modo de etiqueta: 'valor' | 'porcentaje' | 'ambos'
                                    const mode = (pluginOptions && pluginOptions.mode) || 'ambos';
                                    let label = `${value}`;
                                    if (mode === 'porcentaje') label = `${percent}%`;
                                    if (mode === 'ambos') label = `${value} (${percent}%)`;

                                    // Coordenadas y ajuste para que no se recorte en la parte superior
                                    const x = bar.x;
                                    const yRaw = bar.y - 4;
                                    const y = Math.max(yRaw, top + 12);

                                    ctx.fillText(label, x, y);
                                });

                                ctx.restore();
                                }
                            };

                            const ctx = document.getElementById('graficoGrupos').getContext('2d');

                            const graficoGrupos = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                labels: ['Grupos Familiares', 'Kits Entregados', 'Mujeres Atendidas', 'Hijos/Acompañantes'],
                                datasets: [{
                                    label: 'Cantidad',
                                    data: [18, 18, 18, 25],
                                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#ff5733']
                                }]
                                },
                                options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    title: {
                                    display: true,
                                    text: 'Atención a Grupos Familiares (Oct 2023 – Ago 2024)'
                                    },
                                    // Opciones del plugin de etiquetas
                                    dataLabelPlugin: {
                                    mode: 'ambos',     // 'valor' | 'porcentaje' | 'ambos'
                                    font: '12px sans-serif',
                                    color: '#111'
                                    },
                                    // (Opcional) Desactiva tooltips si ya no los necesitas
                                    tooltip: { enabled: true }
                                },
                                scales: {
                                    y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 5 }
                                    }
                                }
                                },
                                plugins: [dataLabelPlugin]
                            });
                            </script>


                        <div style="max-width: 900px; margin: 20px auto; background: #f0f4f9; padding: 20px; border-radius: 10px; font-family: Arial, sans-serif; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                            <h3 style="color: #003366; margin-bottom: 10px;">📄 Informe Complementario</h3>
                            <p style="font-size: 16px; line-height: 1.6; text-align: justify;">
                            A continuación, podrán ver en detalle un informe elaborado por la <strong>Secretaría de Salud de Boyacá</strong>, que amplía la información presentada en la <strong>gráfica</strong> y el <strong>mapa</strong> sobre la atención integral a mujeres víctimas de violencia basada en género y sus familias.
                            Este documento detalla las <strong>remisiones</strong>, la <strong>entrega de kits</strong>, la <strong>acogida temporal</strong> y el <strong>seguimiento institucional</strong> realizado, con el propósito de garantizar una atención <em>digna, oportuna y articulada</em> en el departamento.
                            </p>
                        </div>

                        <a href="assets/pdf/asuntosGenero/ruta/INFORME%20MEDIDAS_secretaria%20de%20salud.pdf" class="btn btn-link" target="_blank">
                            <span class="icon">📄</span> Descargar Informe de medidas de atención 2023 - 2024 &rarr;
                        </a>
                    </div>

                
                <div class="seguimiento-block">
                    <div class="seguimiento-title"><span class="icon">📊</span> Atención Integral en salud en Boyacá</div>
                    <iframe src="mapaESE.html"
                            title="Mapa de Semaforización Municipal"
                            style="width:100%; height:600px; border:none;">
                    </iframe>
                    <div class="info-grid">
                        <div class="info-label"><i class="fas fa-ambulance"></i> Atención como urgencia médica:</div>
                        <div class="info-value">La víctima debe ser atendida siempre como urgencia médica, sin importar el tiempo transcurrido.</div>
                        <div class="info-label"><i class="fas fa-money-bill-alt"></i> Sin barreras económicas:</div>
                        <div class="info-value">La atención debe ser gratuita y sin copagos ni trámites.</div>
                        <div class="info-label"><i class="fas fa-user-nurse"></i> Apoyo especializado:</div>
                        <div class="info-value">Incluye valoración física y mental, profilaxis ITS/VIH, anticoncepción de emergencia y remisiones según el caso.</div>
                        <div class="info-label"><i class="fas fa-vial"></i> Reporte y seguimiento:</div>
                        <div class="info-value">Notificación a Sivigila, manejo de pruebas y cadena de custodia.</div>
                    </div>

                </div>

                <div class="seguimiento-block">
                    <div class="seguimiento-title"><span class="icon">📊</span> Proyectos Fondo Mujeres Visionarias – Boyacá</div>
                    <iframe src="mapa-fondomujer.html"
                            title="Mapa Fondo Mujeres Visionarias – Boyacá"
                            style="width:100%; height:600px; border:none;">
                    </iframe>

                </div>


                <!-- ====== COMISARÍAS DE FAMILIA (bloque interactivo) ====== -->
                <section class="seguimiento-block" id="mod-comisarias">
                <div class="seguimiento-title"><span class="icon">🏛️</span> Comisarías de Familia – Boyacá</div>

                <!-- Pestañas -->
                <div class="chip-group" id="comi-tabs" role="tablist" aria-label="Comisarías de Familia">
                    <button class="chip is-active" data-tab="que-es"      role="tab" aria-selected="true">¿Qué es?</button>
                    <button class="chip"           data-tab="acciones"    role="tab" aria-selected="false">Acciones</button>
                    <button class="chip"           data-tab="a-quien"     role="tab" aria-selected="false">¿A quiénes atiende?</button>
                    <button class="chip"           data-tab="donde"       role="tab" aria-selected="false">¿Dónde encontrarlas?</button>
                </div>

                <!-- Contenido -->
                <div class="k-panes">
                    <!-- ¿Qué es? -->
                    <div class="k-pane is-active" id="pane-que-es">
                    <div class="two-col">
                        <figure class="two-col__media" aria-hidden="true">
                        <img src="assets/svg/img-genero/Atencion/que_es.png" alt="" />
                        </figure>
                        <div class="two-col__text">
                        <h4 class="section-title">¿Qué es una Comisaría de Familia?</h4>
                        <p>
                            Es una <strong>autoridad administrativa</strong> encargada de <strong>proteger los derechos</strong> de quienes integran la familia,
                            en especial <em>niñas, niños, adolescentes, mujeres y personas mayores</em>, frente a situaciones de
                            <strong>violencia intrafamiliar</strong> o cualquier <strong>vulneración de derechos</strong>.
                        </p>
                        <div class="callout ok">
                            <span>⚠️ Si estás en riesgo inmediato, llama al <strong>123</strong> o acude a la comisaría, policía o la línea local de atención.</span>
                        </div>
                        </div>
                    </div>
                    </div>

                    <!-- Acciones -->
                    <div class="k-pane" id="pane-acciones">
                    <h4 class="section-title">Acciones principales</h4>

                    <details class="exp" open>
                        <summary><i class="fas fa-shield-alt"></i> 1) Atención y protección inmediata</summary>
                        <div class="exp-body">
                        <ul class="k-list">
                            <li>Recepción de denuncias por <strong>violencia intrafamiliar</strong>.</li>
                            <li><strong>Valoración del riesgo</strong> y activación de ruta intersectorial.</li>
                            <li>Orientación inicial y activación de <strong>apoyos psicosociales y legales</strong>.</li>
                        </ul>
                        </div>
                    </details>

                    <details class="exp">
                        <summary><i class="fas fa-user-shield"></i> 2) Medidas de protección</summary>
                        <div class="exp-body">
                        <ul class="k-list">
                            <li>Órdenes de <strong>protección de emergencia</strong> (separación, prohibición de acercamiento, entre otras).</li>
                            <li><strong>Canalización</strong> a salud y justicia; seguimiento a cumplimiento de medidas.</li>
                        </ul>
                        </div>
                    </details>

                    <details class="exp">
                        <summary><i class="fas fa-child"></i> 3) Restablecimiento de derechos (NNA)</summary>
                        <div class="exp-body">
                        <p>Actúa <strong>de inmediato</strong> cuando hay amenaza o vulneración de derechos de niñas, niños y adolescentes,
                            en coordinación con ICBF y demás entidades.</p>
                        </div>
                    </details>

                    <details class="exp">
                        <summary><i class="fas fa-people-arrows"></i> 4) Intervención en conflictos familiares (conciliación)</summary>
                        <div class="exp-body">
                        <p>Brinda <strong>orientación jurídica y psicológica</strong> y puede conciliar en:</p>
                        <ul class="k-list">
                            <li>Custodia y cuidado personal.</li>
                            <li>Régimen de visitas.</li>
                            <li>Obligaciones alimentarias.</li>
                        </ul>
                        </div>
                    </details>

                    <details class="exp">
                        <summary><i class="fas fa-hand-holding-heart"></i> 5) Promoción de convivencia pacífica</summary>
                        <div class="exp-body">
                        <ul class="k-list">
                            <li>Campañas de <strong>prevención de violencias</strong> y de <strong>cultura del cuidado</strong>.</li>
                            <li>Capacitaciones y <strong>talleres comunitarios</strong>.</li>
                        </ul>
                        </div>
                    </details>
                    </div>

                    <!-- ¿A quiénes atiende? -->
                    <div class="k-pane" id="pane-a-quien">
                    <div class="info-grid">
                        <div class="info-label"><i class="fas fa-baby"></i> Niñas, niños y adolescentes</div>
                        <div class="info-value">Prioridad absoluta. Se actúa de inmediato para <strong>restablecer derechos</strong> y activar la ruta con ICBF.</div>

                        <div class="info-label"><i class="fas fa-venus-mars"></i> Víctimas de violencia en el contexto familiar</div>
                        <div class="info-value">Mujeres, personas mayores y cualquier integrante de la familia que sufra <strong>violencia intrafamiliar</strong> o sea
                        víctima de hechos que comprometan su seguridad y dignidad.</div>
                    </div>
                    </div>

                    <!-- ¿Dónde encontrarlas? -->
                    <div class="k-pane" id="pane-donde">
                    <div class="two-col">
                        <figure class="two-col__media" aria-hidden="true">
                        <img src="assets/svg/img-genero/Atencion/donde.png" alt="" />
                        </figure>
                        <div class="two-col__text">
                        <h4 class="section-title">Ubicación y contacto</h4>
                        <p>Hay Comisarías de Familia en los <strong>123 municipios de Boyacá</strong>. Puedes acudir de manera presencial,
                            llamar al <strong>123</strong> o al número local de atención familiar del municipio.</p>
                        <ul class="k-list">
                            <li>Si hay <strong>riesgo inminente</strong>, comunícate al 123 o con la Policía.</li>
                            <li>Para orientación, también puedes acudir a tu <strong>alcaldía</strong> y solicitar el contacto de la comisaría.</li>
                        </ul>
                        <!-- (Opcional) Si luego embebes un mapa/directorio, pon aquí el botón -->
                        <!-- <a class="btn btn-institucional2" href="directorio-comisarias.html">Abrir directorio</a> -->
                        </div>
                    </div>
                    </div>
                </div>
                </section>

                <!-- ====== Estilos mínimos del módulo (usa tus tokens/variables existentes) ====== -->
                <style>
                /* si ya tienes .chip-group/.chip/.exp en tu CSS, puedes omitir esta sección */
                #mod-comisarias .chip-group{ display:flex; flex-wrap:wrap; gap:8px; margin:10px 0 14px; }
                #mod-comisarias .chip{ border:1px solid rgba(0,0,0,.18); background:#fff; padding:6px 10px; border-radius:999px; font-weight:700; cursor:pointer; }
                #mod-comisarias .chip.is-active{ border-color:#7c3aed; background:#f4ebff; color:#3b1e91; }

                #mod-comisarias .k-panes{ margin-top:6px; }
                #mod-comisarias .k-pane{ display:none; }
                #mod-comisarias .k-pane.is-active{ display:block; }

                /* acordeones */
                #mod-comisarias .exp{ background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:12px; padding:10px 12px; margin:10px 0; }
                #mod-comisarias .exp[open]{ box-shadow:0 2px 10px rgba(0,0,0,.06); }
                #mod-comisarias .exp summary{ list-style:none; cursor:pointer; font-weight:800; color:#2c3e50; display:flex; gap:10px; align-items:center; }
                #mod-comisarias .exp summary::-webkit-details-marker{ display:none; }
                #mod-comisarias .exp .exp-body{ padding:8px 2px 2px; line-height:1.6; }
                #mod-comisarias .k-list{ margin:6px 0 0; padding-left:18px; }
                #mod-comisarias .k-list li{ margin:6px 0; }

                /* layout de imagen a la izquierda (reutiliza tu two-col si ya existe) */
                #mod-comisarias .two-col{ --media-w:clamp(240px,28vw,360px); --media-h:clamp(220px,40vh,360px);
                    display:grid; grid-template-columns:var(--media-w) minmax(0,1.6fr); gap:20px; align-items:start;
                    background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:14px; padding:14px; box-shadow:0 2px 10px rgba(0,0,0,.06);
                    margin-bottom:12px;
                }
                #mod-comisarias .two-col__media{ width:var(--media-w); height:var(--media-h); border-radius:12px; overflow:hidden; background:#fff; box-shadow:0 2px 10px rgba(0,0,0,.06); }
                #mod-comisarias .two-col__media img{ width:100%; height:100%; object-fit:cover; object-position:center; display:block; }
                #mod-comisarias .section-title{ margin:0 0 8px; font-weight:800; color:#2c3e50; }
                #mod-comisarias .callout.ok{ background:#ecfdf5; border:1px solid #10b98133; color:#065f46; padding:10px 12px; border-radius:10px; margin-top:8px; }

                /* info-grid ya existe en tu CSS; esta es una versión mínima por si hace falta */
                #mod-comisarias .info-grid{ display:grid; grid-template-columns:clamp(170px,22%,230px) 1fr; column-gap:18px; row-gap:12px; }
                #mod-comisarias .info-label{ display:grid; grid-template-columns:24px 1fr; align-items:start; gap:10px; font-weight:800; color:#2c3e50; }
                #mod-comisarias .info-label i{ color:#7c3aed; margin-top:2px; }
                #mod-comisarias .info-value{ background:#fff; border:1px solid rgba(0,0,0,.06); border-left:4px solid #7c3aed; border-radius:10px; padding:12px 16px; color:#111827; box-shadow:0 2px 10px rgba(0,0,0,.06); line-height:1.6; }
                @media (max-width:900px){
                    #mod-comisarias .two-col{ grid-template-columns:1fr; }
                    #mod-comisarias .two-col__media{ width:100%; height:clamp(200px,42vh,320px); }
                    #mod-comisarias .info-grid{ grid-template-columns:1fr; }
                }
                </style>

                <!-- ====== JS del módulo (tabs) ====== -->
                <script>
                (function comisariasTabs(){
                    const root = document.getElementById('mod-comisarias');
                    if(!root) return;
                    const tabs = root.querySelectorAll('#comi-tabs .chip');
                    const panes = {
                    'que-es':   root.querySelector('#pane-que-es'),
                    'acciones': root.querySelector('#pane-acciones'),
                    'a-quien':  root.querySelector('#pane-a-quien'),
                    'donde':    root.querySelector('#pane-donde')
                    };
                    function activate(key){
                    tabs.forEach(b => b.classList.toggle('is-active', b.dataset.tab===key));
                    Object.entries(panes).forEach(([k,el]) => el.classList.toggle('is-active', k===key));
                    }
                    tabs.forEach(b => b.addEventListener('click', () => activate(b.dataset.tab)));
                    // default
                    activate('que-es');
                })();
                </script>




    </div>

    <div class="tab-pane fade panel-purple" id="panel7">


<div class="indicadores-section mt-4">
                        <div style="display: flex; align-items: center;">
                            <img src="assets/svg/img-genero/POLITICAS-PUBLICAS.svg" alt="Icono Políticas Públicas" style="width: 50px; height: 50px; margin-right: 15px;">
                            <h2 style="margin: 0;" class="indicador-title">Políticas Públicas</h2>
                        </div>
                        <p>
                            En esta sección podrás visualizar información clave sobre la situación de las mujeres en Boyacá, a través de tableros interactivos, datos estadísticos y boletines especializados. Haz clic sobre cada imagen para abrir el tablero respectivo en una ventana emergente.
                        </p>

                        
                        <!-- Grid de imágenes de tableros -->
                        <div class="row">
                                <div class="col-md-4 mb-4">
                                    <div class="card tablero-card" data-iframe="https://app.powerbi.com/view?r=eyJrIjoiYmE3YzFmNmQtNTcwMy00NGVmLTk1NWYtNTI1ZDBhYTU2ZDRlIiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9" data-title="Política Pública Mujer y Género">
                                        <img src="assets/svg/img-genero/PoliticaPublica/PP-Asunto_de_genero.png" class="card-img-top" alt="Tablero Mujer y Género">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Mujer y Género</h5>
                                            <p class="card-text" style="font-size: 0.9rem; color: #555;">Todos los indicadores de esta política están asociados con el Observatorio de Asuntos de Género.</p>
                                            <button class="btn btn-institucional ver-tablero-btn" type="button">Ver tablero</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="card tablero-card" data-iframe="https://app.powerbi.com/view?r=eyJrIjoiZGRjNmJkMzctNjE2ZC00OTg2LTk3ZWMtYTc2YTQ4M2Y5N2U4IiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9" data-title="Política Pública Mujer Campesina y Rural">
                                        <img src="assets/svg/img-genero/PoliticaPublica/PP-Mujer_CampesinayRural.png" class="card-img-top" alt="Tablero Mujer Campesina y Rural">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Mujer Campesina y Rural</h5>
                                            <p class="card-text" style="font-size: 0.9rem; color: #555;">Todos los indicadores de esta política están asociados con el Observatorio de Asuntos de Género.</p>
                                            <button class="btn btn-institucional2 mt-2 ver-tablero-btn" type="button">Ver tablero</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="card tablero-card" data-iframe="https://app.powerbi.com/view?r=eyJrIjoiMzJhMTZjNTktNDM1Yy00YmRmLWJhMDUtZGRjYzhiZDcyM2E4IiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=6d7e0fcb4b49936a08d6" data-title="Política Pública Familia">
                                        <img src="assets/svg/img-genero/PoliticaPublica/PP-Familia.png" class="card-img-top" alt="Tablero Familia">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Familia</h5>
                                            <p class="card-text" style="font-size: 0.9rem; color: #555;">
                                            En esta política podrás ver indicadores seleccionados, únicamente aquellos que están asociados al Observatorio de Asuntos de Género.
                                            </p>
                                            <button class="btn btn-institucional2 mt-2 ver-tablero-btn" type="button">Ver tablero</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="card tablero-card" data-iframe="https://app.powerbi.com/view?r=eyJrIjoiMzU3NzQwOTktNTI5Mi00NWFlLTk2MzItZTIxYzJiYmQ5MWJmIiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=ca6a0c64cd07c4268d7e" data-title="Política Pública Infancia y Adolescencia">
                                        <img src="assets/svg/img-genero/PoliticaPublica/PP-InfanciayAdolescencia.png" class="card-img-top" alt="Tablero Infancia y Adolescencia">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Infancia y Adolescencia</h5>
                                            <p class="card-text" style="font-size: 0.9rem; color: #555;">
                                                En esta política podrás ver indicadores seleccionados, únicamente aquellos que están asociados al Observatorio de Asuntos de Género.
                                            </p>
                                            <button class="btn btn-institucional2 mt-2 ver-tablero-btn" type="button">Ver tablero</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>



    </div>

    <div class="tab-pane fade panel-white" id="panel8">

<div class="indicadores-section mt-4">
                    <div style="display: flex; align-items: center;">
                        <img src="assets/svg/img-genero/CAMPANAS-CONTENIDOS.svg" alt="Icono Campañas y publicaciones" style="width: 50px; height: 50px; margin-right: 15px;">
                        <h2 style="margin: 0;" class="indicador-title">Campañas y publicaciones</h2>
                    </div>
                    <!-- Inicio contenido -->

                    <!-- Acordeón de Indicadores -->
                    <div id="tab" class="indicadores-section-list mt-4">
                                
                        <!-- item accordion 0 -->
                        <div class="accordion-item">
                            <button class="accordion-button" onclick="toggleAccordion(this)"><span class="icon">📢</span>Caja de herramientas - CEPAL Naciones Unidas</span></button>
                            <div class="accordion-content">
                                <!-- Bloque texto  -->                           
                                <div class="seguimiento-block">
                                    <div class="seguimiento-title"><span class="icon">🐞</span> Chivateando, Cuidando: Una estrategia para prevenir el abuso sexual infantil en niñas de Boyacá</div>
                                            <section style="max-width: 900px; margin: 40px auto; font-family: Arial, sans-serif; line-height: 1.7; background: #fefefe; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                <h2 style="color: #004080;">🐞 ¿Qué es?</h2>
                                                <p>
                                                Es un compilado de recursos digitales orientados a fortalecer, mediante el autoaprendizaje, las capacidades de las y los funcionarios públicos para la identificación de brechas de desigualdad en diversos ámbitos y a plantear estrategias de política pública, particularmente de gestión e institucionalidad, que permitan el cierre de dichas brechas de desigualdad en los países de la región.
                                                </p>
                                            </section>

                                        <!-- Tarjeta Observatorio CEPAL -->
                                        <div class="col-md-4 mb-4">
                                            <div class="card tablero-card"

                                                data-title="Observatorio de Igualdad y Género – CEPAL">

                                                <img src="assets/svg/img-genero/Informacion/cepal-observatorio.png"
                                                    class="card-img-top"
                                                    alt="Observatorio de Igualdad de Género – CEPAL">

                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Caja de Herramientas</h5>

                                                    <p class="card-text small text-muted mb-3">
                                                        Es un compilado de recursos digitales orientados a fortalecer, mediante el autoaprendizaje,
                                                         las capacidades de las y los funcionarios públicos para la identificación de brechas de desigualdad
                                                    </p>

                                                    <!-- 🔗 Botón que abre el data-iframe en pestaña nueva -->
                                                    <a href="https://share.google/T6XCe9BF755AbRwkO"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="btn btn-institucional">
                                                        Ver tablero
                                                    </a>
                                                </div>

                                            </div>
                                        </div>

                                    <!-- Fin bloque texto  -->  

                                </div>  

                            </div>
                        </div>
                        
                        
                        <!-- item accordion 1 -->
                        <div class="accordion-item">
                            <button class="accordion-button" onclick="toggleAccordion(this)"><span class="icon">📢</span>Campaña Chivatiando</span></button>
                            <div class="accordion-content">
                                <!-- Bloque texto  -->                           
                                <div class="seguimiento-block">
                                    <div class="seguimiento-title"><span class="icon">🐞</span> Chivateando, Cuidando: Una estrategia para prevenir el abuso sexual infantil en niñas de Boyacá</div>
                                            <section style="max-width: 900px; margin: 40px auto; font-family: Arial, sans-serif; line-height: 1.7; background: #fefefe; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                <h2 style="color: #004080;">🐞 ¿Qué es?</h2>
                                                <p>
                                                    <strong>“Chivateando, Cuidando”</strong> es una campaña educativa liderada por la <strong>Secretaría de Salud de Boyacá</strong>, que busca <strong>prevenir el abuso sexual infantil</strong> y fortalecer el reconocimiento de las niñas como sujetas de derechos, promoviendo entornos protectores en sus familias y comunidades.
                                                </p>

                                                <h2 style="color: #004080;">👨‍👩‍👧 ¿A quién va dirigida?</h2>
                                                <p>
                                                    A grupos de <strong>cuidadores con niñas entre los 8 y 12 años</strong>, con el objetivo de <strong>fortalecer vínculos afectivos</strong>, desarrollar capacidades para el cuidado, y promover el <strong>empoderamiento de las niñas</strong>.
                                                </p>

                                                <h2 style="color: #004080;">🎯 Objetivo</h2>
                                                <p>
                                                    <strong>Empoderar a las niñas</strong> y fortalecer las habilidades de sus cuidadores para <strong>prevenir el abuso sexual infantil</strong>, transformar imaginarios sociales y construir entornos seguros desde el hogar y la escuela.
                                                </p>
                                            </section>

                                            <p>
                                            En esta sección podrás visualizar información clave sobre la situación de las mujeres en Boyacá, a través de indicadores, tableros interactivos y boletines especializados.
                                            </p>

                                            <!-- Grid de tarjetas -->
                                            <div class="row">
                                                <!-- Tarjeta Documento PDF 1 -->
                                                <div class="col-md-4 mb-4">
                                                    <div class="card tablero-card">
                                                        <img src="assets/svg/img-genero/publicaciones/Chivatiando.png"
                                                            class="card-img-top"
                                                            alt="Boletín de violencia dimensión social">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">Chivatiando, cuidando</h5>
                                                            <a href="assets/pdf/asuntosGenero/publicaciones/Chivatiando_Cuidando.pdf"
                                                            class="pdf-link btn btn-institucional2 mt-2"
                                                            data-title="Boletín de violencia dimensión social">
                                                            Ver documento
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <!-- Fin bloque texto  -->  

                                </div>  

                            </div>
                        </div>

                        <!-- item accordion 2 -->
                        <div class="accordion-item">
                            <button class="accordion-button" onclick="toggleAccordion(this)"><span class="icon">📢</span>Campañas de la Defensoría del Pueblo</span></button>
                            <div class="accordion-content">
                                    <div class="seguimiento-block">
                                    <div class="seguimiento-title"><span class="icon">🤝</span> Tu derecho es mi deber </div>
                                            <section style="max-width: 900px; margin: 40px auto; font-family: Arial, sans-serif; line-height: 1.7; background: #fefefe; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                <div class="defensoria-info" style="max-width: 900px; margin: auto; font-family: Arial, sans-serif; line-height: 1.6; font-size: 16px;">
                                                <h2 style="font-size: 22px; color: #003366;">🛡️ ¿Qué hace la Defensoría del Pueblo?</h2>
                                                <p style="text-align: justify;">
                                                    La <strong>Defensoría del Pueblo de Colombia</strong> es una institución encargada de promover, proteger y defender los derechos humanos en todo el territorio nacional, especialmente de las personas y comunidades en situación de vulnerabilidad. A través de investigaciones, acciones de incidencia, acompañamientos institucionales y campañas educativas, busca garantizar el respeto por la dignidad humana, la igualdad, la no discriminación y el acceso efectivo a la justicia y a los servicios del Estado.
                                                </p>

                                                <h2 style="font-size: 22px; color: #003366;">📣 ¿Qué son las campañas educativas de la Defensoría del Pueblo?</h2>
                                                <p style="text-align: justify;">
                                                    Las campañas educativas de la Defensoría, como las contenidas en estas cartillas, tienen un propósito claro: <strong>informar, sensibilizar y empoderar</strong> a la ciudadanía sobre sus derechos fundamentales. Cada cartilla aborda una temática particular de interés social —como los derechos de personas OSIGD-LGBTI, la protección frente a la discriminación, o la promoción de la igualdad de género— y está dirigida especialmente a comunidades históricamente marginadas.
                                                </p>

                                                <p style="text-align: justify;">
                                                    Estas campañas combinan conceptos legales, testimonios, normas nacionales e internacionales, propuestas pedagógicas y actividades interactivas, todo con un lenguaje accesible. Su objetivo es <strong>formar ciudadanía informada</strong>, combatir la discriminación y fomentar espacios seguros, inclusivos y equitativos para todas las personas.
                                                </p>
                                                </div>
                                            </section>

                                            <p>
                                            En esta sección podrás visualizar información clave sobre la situación de las mujeres en Boyacá, a través de indicadores, tableros interactivos y boletines especializados.
                                            </p>

                                            <!-- Grid de tarjetas -->
                                            <div class="row">
                                            <!-- Tarjeta Documento PDF 1 -->
                                                <div class="col-md-4 mb-4">
                                                    <div class="card tablero-card">
                                                        <img src="assets/svg/img-genero/publicaciones/DFCampana01.jpg"
                                                            class="card-img-top"
                                                            alt="Boletín de violencia dimensión social">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">Trans-formando derechos de las personas transgénero en Colombia</h5>
                                                            <a href="assets/pdf/asuntosGenero/publicaciones/1.TRANS-FORMANDO DERECHOS.pdf"
                                                            class="pdf-link btn btn-institucional2 mt-2"
                                                            data-title="Boletín de violencia dimensión social">
                                                            Ver documento
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Tarjeta Documento PDF 2 -->
                                                <div class="col-md-4 mb-4">
                                                    <div class="card tablero-card">
                                                        <img src="assets/svg/img-genero/publicaciones/DFCampana02.jpg"
                                                            class="card-img-top"
                                                            alt="Boletín de violencia dimensión social">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">Protegiendo la diversidad</h5>
                                                            <a href="assets/pdf/asuntosGenero/publicaciones/2.ProtegiendoLaDiversidad.pdf"
                                                            class="pdf-link btn btn-institucional2 mt-2"
                                                            data-title="Boletín de violencia dimensión social">
                                                            Ver documento
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Tarjeta Documento PDF 3 -->
                                                <div class="col-md-4 mb-4">
                                                    <div class="card tablero-card">
                                                        <img src="assets/svg/img-genero/publicaciones/DFCampana04.jpg"
                                                            class="card-img-top"
                                                            alt="Boletín de violencia dimensión social">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">Mujeres - Sujetos de especial protección</h5>
                                                            <a href="assets/pdf/asuntosGenero/publicaciones/3.MUJERES.pdf"
                                                            class="pdf-link btn btn-institucional2 mt-2"
                                                            data-title="Boletín de violencia dimensión social">
                                                            Ver documento
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Tarjeta Documento PDF 4 -->
                                                <div class="col-md-4 mb-4">
                                                    <div class="card tablero-card">
                                                        <img src="assets/svg/img-genero/publicaciones/DFCampana03.jpg"
                                                            class="card-img-top"
                                                            alt="Boletín de violencia dimensión social">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">Personas con Orientación sexual e identidad de género diversa</h5>
                                                            <a href="assets/pdf/asuntosGenero/publicaciones/4.PERSONAS CON ORIENTACION SEXUAL.pdf"
                                                            class="pdf-link btn btn-institucional2 mt-2"
                                                            data-title="Boletín de violencia dimensión social">
                                                            Ver documento
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <!-- Fin bloque texto  -->  

                                </div>  
                            </div>
                        </div>
                        <!-- item accordion 3 -->
                        <div class="accordion-item">
                            <button class="accordion-button" onclick="toggleAccordion(this)"><span class="icon">📢</span>Campaña componente mujer Boyacá - Integración Social</span></button>
                            <div class="accordion-content">

                            <iframe src="demo-campanas.html"
                                    title="Campañas Componente Mujer"
                                    style="width:100%; height:600px; border:none;">
                            </iframe>


                            </div>
                        </div>
                        <!-- item accordion 4 -->
                        <div class="accordion-item">
                        <button class="accordion-button" onclick="toggleAccordion(this)"><span class="icon">📢</span>Campañas de Liderazgo y participación</span></button>
                            <div class="accordion-content">

                            <iframe src="estrategia-liderazgo.html"
                                    title="Campañas Liderazgo y participación"
                                    style="width:100%; height:600px; border:none;">
                            </iframe>
                            
                            </div>
                        </div>
                        <!-- item accordion 5 -->
                        <div class="accordion-item">
                        <button class="accordion-button" onclick="toggleAccordion(this)"><span class="icon">📚</span>Publicaciones</span></button>
                            <div class="accordion-content">
                                <!-- Grid de tarjetas -->
                                <div class="row">
                                    <!-- Tarjeta Power BI -->
                                    <div class="col-md-4 mb-4">
                                        <div class="card tablero-card"
                                            data-iframe="https://uptc.edu.co/sitio/portal/sitios/universidad/vic_aca/vic_acad/edinclus/ogdh.html"
                                            data-title="Indicadores Asuntos de Género">
                                        <img src="assets/svg/img-genero/publicaciones/log_vice_obser.svg"
                                            class="card-img-top"
                                            alt="Tablero indicadores Asuntos de Género">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Observatorio de Género y Derechos Humanos UPTC</h5>
                                            <button class="btn btn-institucional ver-tablero-btn"
                                                    type="button">
                                            Ver página
                                            </button>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>
                </div>


    </div>

    <div class="tab-pane fade panel-purple" id="panel9">



<div class="indicadores-section mt-4">
                    <div style="display: flex; align-items: center;">
                        <img src="assets/svg/img-genero/REPORTE-SOCIAL.svg" alt="Icono Reportes sociales" style="width: 50px; height: 50px; margin-right: 15px;">
                        <h2 style="margin: 0;" class="indicador-title">Reportes sociales</h2>
                    </div>
                    <!-- Texto reportes sociales-->
                   <div class="seguimiento-block">
                                    <div class="seguimiento-title"><span class="icon">🟣</span> Reportes Sociales Participativos: Voces, Datos y Soluciones desde las Comunidades</div>
                                            <p>
                                            La sección Reportes Sociales está diseñada para visibilizar análisis detallados de datos recolectados directamente desde las comunidades, con énfasis en encuestas de percepción social realizadas principalmente relacionadas con asuntos de genero. Su objetivo es identificar problemáticas estructurales, emocionales e institucionales que afectan la seguridad, la equidad de género y la calidad de vida en los territorios. Aquí se publican informes construidos con rigor técnico que permiten comprender cómo se sienten las personas en sus entornos, qué barreras enfrentan y qué propuestas emergen desde la ciudadanía para transformar su realidad.
                                            </p>
 
                                    </div>
                    </div>
                    <!-- Fin bloque texto  --> 
                                    <div class="seguimiento-block">
                                        <div class="seguimiento-title"><span class="icon">🟣</span> Datos y Análisis Comunitarios Generados por Organizaciones Sociales</div>
                                            <p>
                                            El documento "Sobreviviente - Consejo de Seguridad" presenta los resultados de una encuesta aplicada a mujeres del departamento de Boyacá para conocer su percepción sobre la seguridad, los tipos de violencia que enfrentan, y el nivel de respuesta institucional. El análisis revela una profunda desconfianza hacia las rutas de atención actuales, evidencia múltiples formas de violencia, y recopila testimonios que muestran la urgencia de una respuesta efectiva, humanizada y territorializada. Las participantes proponen fortalecer la educación en prevención, mejorar la capacitación institucional, promover la participación de mujeres en liderazgo comunitario y garantizar la difusión efectiva de las rutas de atención.
                                            </p>

                                            <!-- Grid de tarjetas -->
                                            <div class="row">
                                            <!-- Tarjeta Documento PDF 1 -->
                                                <div class="col-md-4 mb-4">
                                                    <div class="card tablero-card">
                                                        <img src="assets/svg/img-genero/sociales/Sobreviviente.png"
                                                            class="card-img-top"
                                                            alt="Boletín de violencia dimensión social">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">Por la seguridad y protección de mujeres y niñas en el departamento de Boyacá</h5>
                                                            <a href="assets/pdf/asuntosGenero/sociales/presentacionSobreviviente.pdf"
                                                            class="pdf-link btn btn-institucional2 mt-2"
                                                            data-title="Boletín de violencia dimensión social">
                                                            Ver documento
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  
 
                                    </div>

                                    <div class="seguimiento-block">
                                        <div class="seguimiento-title"><span class="icon">🟣</span>Términos para la publicación de reportes sociales:</div>
                                    <div class="terminos-condiciones">
                                        <ol>
                                            <li><strong>Rigurosidad metodológica:</strong> Los reportes deben estar basados en procesos sistemáticos de recolección de información, preferiblemente mediante encuestas, entrevistas u otras metodologías participativas aplicadas con consentimiento informado.</li>

                                            <li><strong>Transparencia y trazabilidad:</strong> Se debe adjuntar un documento metodológico breve que incluya el objetivo del estudio, población encuestada, fecha, lugar de aplicación y responsable del análisis. Esto garantizará la trazabilidad de la información.</li>

                                            <li><strong>Protección de datos sensibles:</strong> La información debe ser anonimizada, sin revelar datos personales que puedan identificar a las personas participantes, especialmente si el tema aborda violencias o situaciones de riesgo.</li>

                                            <li><strong>Enfoque ético y de género:</strong> Todos los documentos deben tener enfoque de derechos humanos, perspectiva de género y evitar la revictimización. El lenguaje debe ser respetuoso, inclusivo y no discriminatorio.</li>

                                            <li><strong>Validación previa:</strong> Todo reporte será revisado por el equipo técnico antes de su publicación para verificar que cumpla con los criterios de calidad, seguridad y pertinencia.</li>

                                            <li><strong>Autorización de uso y publicación:</strong> Las organizaciones, fundaciones u ONGs deberán firmar una autorización de publicación, permitiendo el uso del material únicamente para fines educativos, institucionales o de incidencia social.</li>

                                            <li><strong>Compromiso con la verdad:</strong> El contenido debe ser verídico, sustentado y libre de manipulación o alteración con fines propagandísticos o partidistas.</li>

                                            <li><strong>Revisión por la mesa técnica del Observatorio:</strong> La mesa técnica del Observatorio revisará la postulación y la información presentada, y comunicará a la persona de contacto si la publicación es aprobada, requiere ajustes o es rechazada.</li>

                                            <li><strong>Diligenciamiento del formulario:</strong> Para postular un reporte social, se debe diligenciar el formulario disponible en el siguiente enlace: 
                                                <a href="https://forms.gle/tSMzHpEu7gEfdgiJ8" target="_blank">Formulario de Postulación de Reportes Sociales</a>.
                                            </li>
                                        </ol>
                                    </div>

 
                                    </div>
                    </div>
                </div>



    </div>

</div>

</div>


<!-- Modal PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-institucional-soft">
        <h5 class="modal-title" id="pdfModalLabel">Visualizador de documento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <iframe id="pdfIframe" src="" width="100%" height="600px" style="border:none;"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tableros -->
<div class="modal fade" id="tableroModal" tabindex="-1" aria-labelledby="tableroModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-institucional-soft">
        <h5 class="modal-title" id="tableroModalLabel">Visualizador de Tablero</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <iframe id="tableroIframe" src="" width="100%" height="600px" style="border:none;"></iframe>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
    // Función para abrir las pestañas
    function openTab(evt, tabName) {
        const tabPanes = document.getElementsByClassName("tab-pane");
        for (let i = 0; i < tabPanes.length; i++) {
            tabPanes[i].style.display = "none";
        }
        const tabLinks = document.getElementsByClassName("tab-link");
        for (let i = 0; i < tabLinks.length; i++) {
            tabLinks[i].className = tabLinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    var buscador = document.getElementById("buscador");
    if (buscador) {
        buscador.style.display = "block";
}

    // Función para filtrar indicadores
    function filterIndicators() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const items = document.querySelectorAll('.icon-list-items .icon-list-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(input) ? '' : 'none';
        });
    }

    // Función para alternar el acordeón
    function toggleAccordion(button) {
        const content = button.nextElementSibling;
        content.style.display = content.style.display === "block" ? "none" : "block";
        button.classList.toggle("active");
    }

</script>

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



document.querySelectorAll('.ver-tablero-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const card = this.closest('.tablero-card');
        const url = card.getAttribute('data-iframe');
        const title = card.getAttribute('data-title');

        document.getElementById('iframeTitle').innerText = title;
        document.getElementById('iframeViewer').src = url;

        const modal = new bootstrap.Modal(document.getElementById('iframeModal'));
        modal.show();
    });
});
</script>


<style>
   /* =========================
   Tabs genéricos (tuyos)
   ========================= */
.tabs{ display:flex; gap:10px; margin-bottom:20px; }
.tab-link{
  background:#f1f1f1; border:1px solid #ccc; padding:10px 20px;
  cursor:pointer; font-weight:bold;
}
.tab-link.active{ background:#3E2259; color:#fff; border-bottom:none; }
.tab-content{ border:1px solid #ccc; padding:20px; background:#fff; }
.tab-pane{ display:none; }
.tab-pane.active{ display:block; }

/* =========================
   Accordion genérico (tuyo)
   ========================= */
.accordion-item{ border-bottom:1px solid #ddd; margin-top:10px; }
.accordion-button{
  width:100%; text-align:left; background:#f9f9f9; border:none; outline:0;
  padding:10px; font-size:16px; cursor:pointer; font-weight:bold;
}
.accordion-button.active{ background:#ddd; }
.accordion-content{ display:none; padding:10px; background:#f1f1f1; }

/* Iframe genérico */
.iframe-full{ width:100%; height:80vh; border:none; }

/* Search */
.search-bar-container{ margin-bottom:20px; text-align:center; }
.search-bar{
  width:100%; max-width:500px; padding:10px; font-size:16px;
  border:1px solid #ccc; border-radius:5px;
}
.search-bar-container select{
  max-width:500px; width:100%; padding:10px; font-size:16px;
  border:1px solid #ccc; border-radius:5px;
}

/* =========================
   Two-column (imagen + texto)
   ========================= */
.two-col{
  --media-w: clamp(240px, 28vw, 360px);
  --media-h: clamp(220px, 40vh, 400px);
  display:grid;
  grid-template-columns: var(--media-w) minmax(0, 1.6fr);
  gap:24px; align-items:start;
  width:min(1100px, 92%); margin:0 auto;
}
@media (min-width:1200px){
  .two-col{
    --media-w: clamp(240px, 24vw, 340px);
    grid-template-columns: var(--media-w) minmax(0, 2fr);
  }
}
.two-col__media{
  width:var(--media-w); height:var(--media-h); margin:0;
  border-radius:14px; overflow:hidden; background:#fff;
  box-shadow: var(--card-shadow, 0 2px 10px rgba(0,0,0,.08));
}
.two-col__media img{
  width:100%; height:100%; object-fit:cover; object-position:center;
  display:block; transform:translateZ(0);
}
.two-col__text{ align-self:start; }

/* Imagen “sticky” opcional */
.two-col.two-col--sticky .two-col__media{ position:sticky; top:12px; }

/* Móvil */
@media (max-width:900px){
  .two-col{ grid-template-columns:1fr; }
  .two-col__media{ width:100%; height: clamp(200px, 42vh, 380px); }
  .two-col.two-col--sticky .two-col__media{ position:static; }
}

/* =========================
   Info grid (etiqueta + valor)
   ========================= */
.info-grid{
  display:grid;
  grid-template-columns: clamp(170px, 22%, 230px) 1fr;
  column-gap:18px; row-gap:12px;
}
@media (max-width:900px){ .info-grid{ grid-template-columns:1fr; } }
.info-label{
  display:grid; grid-template-columns:24px 1fr; align-items:start; gap:10px;
  font-weight:800; color: var(--title, #2c3e50);
}
.info-label i{ color: var(--brand, #7c3aed); margin-top:2px; }
.info-value{
  background:#fff; border:1px solid rgba(0,0,0,.06);
  border-left:4px solid var(--brand, #7c3aed);
  border-radius:10px; padding:12px 16px;
  color: var(--text, #111827);
  box-shadow: var(--card-shadow, 0 2px 10px rgba(0,0,0,.06));
  line-height:1.6; hyphens:auto; text-wrap:pretty;
}

/* =========================
   Chips (pestañas de Barreras)
   ========================= */
.is-hidden{ display:none !important; }

.chip-group{ display:flex; flex-wrap:wrap; gap:8px; margin:10px 0; }
.chip{
  border:1px solid rgba(0,0,0,.18); background:#fff;
  padding:6px 10px; border-radius:999px; font-weight:700;
  cursor:pointer; display:inline-flex; align-items:center; gap:6px;
  transition:all .15s ease; user-select:none;
}
.chip:hover{ transform:translateY(-1px); box-shadow:0 2px 8px rgba(0,0,0,.06); }
.chip.is-active{
  border-color: var(--brand, #7c3aed);
  background:#f4ebff; color:#3b1e91;
}
/* accesibilidad */
.chip:focus-visible{
  outline:2px solid var(--brand, #7c3aed);
  outline-offset:2px;
}

/* =========================
   Tarjetas de ejemplos
   ========================= */
.k-cards{
  display:grid; grid-template-columns: repeat(auto-fit, minmax(230px,1fr));
  gap:12px; margin-top:10px;
}
.k-card{
  background:#fff; border:1px solid rgba(0,0,0,.06);
  border-radius:12px; padding:12px;
  box-shadow: var(--card-shadow, 0 2px 10px rgba(0,0,0,.06));
}
.k-card h5{
  margin:0 0 6px; font-size:1rem; display:flex; gap:8px; align-items:center;
  color: var(--title, #2c3e50);
}
.k-card p{ margin:0; line-height:1.55; color: var(--text, #111827); }
.k-subchips{ margin-top:6px; }

/* =========================
   Acordeones "exp" (Barreras)
   ========================= */
.exp{
  background:#fff; border:1px solid rgba(0,0,0,.06);
  border-radius:12px; padding:10px 12px; margin-bottom:12px;
}
.exp[open]{ box-shadow:0 2px 10px rgba(0,0,0,.06); }
.exp summary{
  list-style:none; cursor:pointer; font-weight:800; color: var(--title, #2c3e50);
  display:grid; grid-template-columns:24px 1fr; gap:10px; align-items:center;
}
.exp summary::-webkit-details-marker{ display:none; }
.exp summary:focus-visible{ outline:2px solid var(--brand, #7c3aed); outline-offset:2px; }
.exp .exp-body{ padding:8px 2px 2px; line-height:1.6; }

/* =========================
   Listado legal
   ========================= */
.k-list{ margin:6px 0 0; padding-left:18px; }
.k-list li{ margin:6px 0; }



</style>


<?php include 'include/footer.php'; ?>
