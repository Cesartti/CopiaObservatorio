<?php include  'include/header.php' ?>

<style>
    /* === ESTILOS ESPECÍFICOS DE INICIO (puedes moverlos a tu CSS principal) === */

    .hero-wrapper {
        position: relative;
    }

    .hero-carousel {
        position: relative;
    }

    .hero-slide {
        min-height: 70vh;
        display: flex;
        align-items: center;
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .hero-slide::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top left, rgba(255,255,255,0.18), transparent 55%),
                    radial-gradient(circle at bottom right, rgba(0,0,0,0.4), transparent 55%);
        mix-blend-mode: soft-light;
        pointer-events: none;
    }

    .hero-slide .container {
        position: relative;
        z-index: 2;
    }

    .hero-slide--boyaca {
        background: linear-gradient(120deg, #0a2540, #145c5c);
    }

    .hero-slide--economica {
        background: linear-gradient(120deg, #103250, #2b7bba);
    }

    .hero-slide--social {
        background: linear-gradient(120deg, #2b3b5a, #4f73c4);
    }

    .hero-slide--ambiental {
        background: linear-gradient(120deg, #0d3b27, #2f7d4b);
    }

    .hero-slide--cti {
        background: linear-gradient(120deg, #311b4f, #7851a9);
    }

    .hero-graphic {
        max-height: 320px;
        object-fit: contain;
        filter: drop-shadow(0 18px 40px rgba(0,0,0,0.35));
    }

    .badge-hero {
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(8px);
        border-radius: 999px;
        padding: 0.45rem 0.9rem;
        font-size: 0.8rem;
        letter-spacing: 0.04em;
    }

    .btn-hero-primary {
        border-radius: 999px;
        padding: 0.75rem 1.6rem;
        font-weight: 500;
    }

    .btn-hero-outline {
        border-radius: 999px;
        padding: 0.75rem 1.6rem;
        border-width: 1px;
    }

    .hero-metrics {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 1.5rem;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .hero-metrics span strong {
        font-size: 1.1rem;
    }

    .carousel-indicators li {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        margin: 0 4px;
        background-color: rgba(255,255,255,0.4);
    }

    .carousel-indicators .active {
        width: 26px;
        background-color: #ffffff;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        filter: drop-shadow(0 0 6px rgba(0,0,0,0.4));
    }

    /* Secciones genéricas */

    .section-title {
        font-weight: 700;
        font-size: 1.9rem;
    }

    .section-subtitle {
        color: #6c757d;
        max-width: 720px;
        margin-left: auto;
        margin-right: auto;
        font-size: 0.98rem;
    }

    /* Dimensiones - tarjetas */

    #dimensiones {
        scroll-margin-top: 90px;
    }

    .dimension-grid {
        margin-top: 2rem;
    }

    .dimension-card {
        display: block;
        background: #ffffff;
        border-radius: 1.25rem;
        padding: 1.5rem 1.25rem;
        text-align: center;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        border: 1px solid rgba(0,0,0,0.04);
        height: 100%;
    }

    .dimension-card__icon img {
        max-width: 72px;
        max-height: 72px;
    }

    .dimension-card__title {
        font-size: 1.05rem;
        margin-top: 0.75rem;
        margin-bottom: 0.4rem;
        font-weight: 600;
    }

    .dimension-card__text {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .dimension-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
        background: #f8fafc;
    }

    .dimension-card--economica .dimension-card__title { color: #4f46e5; }
    .dimension-card--social .dimension-card__title { color: #0ea5e9; }
    .dimension-card--ambiental .dimension-card__title { color: #16a34a; }
    .dimension-card--cti .dimension-card__title { color: #f59e0b; }

    /* Bloques descriptivos de dimensiones (se mantiene tu estructura original, solo se suaviza) */

    .bg-top {
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 18px 45px rgba(15,23,42,0.06);
        margin-top: -3rem;
        position: relative;
        z-index: 5;
    }

    .section-text {
        align-items: center;
    }

    .section-text .desc h3 {
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .section-text .desc p {
        font-size: 0.96rem;
        color: #4b5563;
    }

    .section-text .icon img {
        max-width: 120px;
    }

    /* Boyacá en imágenes */

    .picture-container {
        position: relative;
    }

    .picture-container::before {
        content: "";
        position: absolute;
        inset: 8%;
        border-radius: 36px;
        border: 1px dashed rgba(148,163,184,0.5);
        pointer-events: none;
    }

    .picture-container img {
        border-radius: 20px;
        object-fit: cover;
        width: 100%;
        height: 100%;
    }

    .picture-container .div-1 img {
        min-height: 260px;
    }

    .picture-container .div-2 img,
    .picture-container .div-3 img {
        min-height: 120px;
    }

    /* Noticias */

    .news-section {
        border-radius: 24px;
        background: linear-gradient(135deg, #f9fafb, #eef2ff);
        padding: 2.75rem 2rem;
    }

    .news-card {
        border-radius: 18px;
        border: 1px solid rgba(148,163,184,0.25);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .news-card__tag {
        display: inline-block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6366f1;
        background: rgba(129,140,248,0.12);
        border-radius: 999px;
        padding: 0.25rem 0.7rem;
    }

    .news-card__meta {
        font-size: 0.78rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .news-card__excerpt {
        font-size: 0.9rem;
        color: #4b5563;
    }

    .news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(15,23,42,0.16);
        border-color: rgba(79,70,229,0.4);
    }

    /* Aliados */

    .partners-section .owl-carousel .item img {
        max-width: 120px;
        max-height: 70px;
        margin: 0 auto;
        filter: grayscale(20%);
        transition: transform 0.2s ease, filter 0.2s ease;
    }

    .partners-section .owl-carousel .item img:hover {
        transform: translateY(-2px) scale(1.02);
        filter: grayscale(0%);
    }

    /* Responsivo */

    @media (max-width: 767.98px) {
        .hero-slide {
            min-height: 70vh;
            text-align: left;
            padding: 3.5rem 0 2.5rem;
        }

        .hero-slide h1 {
            font-size: 1.7rem;
        }

        .bg-top {
            margin-top: -2rem;
            padding-top: 2rem !important;
        }

        .picture-container::before {
            inset: 4%;
        }
    }

  .dimension-card {
    display: block;
    background: #ffffff;
    border-radius: 1.25rem;
    padding: 1.5rem 1.25rem;
    text-align: center;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.3s ease;
    border: 1px solid rgba(0,0,0,0.04);
    height: 100%;
  }

  .dimension-card:hover {
    transform: translateY(-4px);
    background-color: #f0f4ff;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
  }

  .dimension-card__title {
    font-size: 1.05rem;
    margin-top: 0.75rem;
    margin-bottom: 0.4rem;
    font-weight: 600;
  }

  .dimension-card__text {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
  }

</style>

<div class="main-body" id="main-body">

    <!-- HERO / CAROUSEL PRINCIPAL -->
    <section class="container-fluid p-0 hero-wrapper">
        <div id="heroCarousel" class="carousel slide hero-carousel" data-ride="carousel" data-interval="8000">
            <ol class="carousel-indicators">
                <li data-target="#heroCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#heroCarousel" data-slide-to="1"></li>
                <li data-target="#heroCarousel" data-slide-to="2"></li>
                <li data-target="#heroCarousel" data-slide-to="3"></li>
                <li data-target="#heroCarousel" data-slide-to="4"></li>
            </ol>

            <div class="carousel-inner">

                <!-- Slide 1 (ACTIVE) - Convocatoria Fondo Mujer -->
                <div class="carousel-item active">
                <div class="hero-slide hero-slide--boyaca">
                    <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                        <span class="badge-hero text-uppercase mb-3">
                            Convocatoria Fondo Mujer
                        </span>

                        <h1 class="mb-3">
                            Ruta Micronegocios: Fondo Mujer Libre y Productiva
                        </h1>

                        <p class="lead mb-4">
                            Impulsar la autonomía económica de las mujeres también implica transformar cómo acceden al financiamiento.
                            En Colombia, muchas empresas lideradas por mujeres aún crecen sin crédito bancario. Un estudio del BID y el
                            Fondo Mujer (encuesta a 1.123 empresas) muestra que solo el 34,2% han accedido a préstamos y más de la mitad
                            se financia con recursos propios, incluso con formación financiera. Este reto requiere acompañamiento,
                            fortalecimiento empresarial, redes de apoyo y servicios financieros con enfoque de género.
                        </p>

                        <div class="d-flex flex-wrap align-items-center">
                            <a
                            href="https://fondomujer.gov.co/portfolio/convocatoria-para-la-seleccion-de-beneficiarias-para-participar-en-la-ruta-micronegocios-del-fondo-mujer-libre-y-productiva/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-primary btn-hero-primary mr-2 mb-2"
                            >
                            Conoce la convocatoria
                            </a>
                        </div>

                        <!-- Si no quieres métricas en este slide, puedes borrar este bloque -->
                        <div class="hero-metrics">
                            <span><strong>Ruta</strong> Micronegocios</span>
                            <span><strong>Enfoque</strong> género</span>
                        </div>
                        </div>

                        <div class="col-lg-5 d-none d-lg-block text-right">
                        <!-- Puedes cambiar la imagen por una propia si la tienes -->
                        <img src="assets/svg/convocatoriamujer.jpg" alt="Convocatoria Fondo Mujer" class="hero-graphic">
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                <!-- Slide 2 - Resumen de la presentación (antes: Lanzamiento / reemplaza la económica) -->
                <div class="carousel-item">
                <div class="hero-slide hero-slide--economica">
                    <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                        <span class="badge-hero text-uppercase mb-3">
                            Red de Observatorios de Boyacá
                        </span>

                        <h1 class="mb-3">
                            Ver resumen de la presentación
                        </h1>

                        <p class="lead mb-4">
                            Consulta los puntos clave, conclusiones y principales mensajes presentados en el lanzamiento del
                            Observatorio: Asuntos de Género, para socializar y dar continuidad a las acciones.
                        </p>

                        <div class="d-flex flex-wrap align-items-center">
                            <!-- Ajusta el destino según tu sitio: ancla, página o PDF -->
                            <a href="assets/pdf/asuntosGenero/lanzamientoObservatorio2025.pdf" class="btn btn-primary btn-hero-primary mr-2 mb-2">
                            Ver resumen
                            </a>
                        </div>

                        <div class="hero-metrics">
                            <span><strong>4</strong> de diciembre 2025</span>
                            <span><strong>2:00 pm</strong> Salón de la Constitución</span>
                        </div>
                        </div>

                        <div class="col-lg-5 d-none d-lg-block text-right">
                        <img src="assets/svg/lanzamiento.jpg" alt="Resumen presentación" class="hero-graphic">
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                <!-- Slide Social -->
                <div class="carousel-item">
                    <div class="hero-slide hero-slide--social">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <span class="badge-hero text-uppercase mb-3">
                                        Dimensión social
                                    </span>
                                    <h1 class="mb-3">
                                        Calidad de vida y tejido social boyacense
                                    </h1>
                                    <p class="lead mb-4">
                                        Monitoreo de brechas, necesidades y potencialidades para comprender el bienestar de la población.
                                    </p>
                                    <a href="indic-social.php" class="btn btn-primary btn-hero-primary mr-2 mb-2">
                                        Ver más de esta dimensión
                                    </a>
                                </div>
                                <div class="col-lg-5 d-none d-lg-block text-right">
                                    <img src="assets/svg/social-color.svg" alt="Dimensión social" class="hero-graphic">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide Ambiental -->
                <div class="carousel-item">
                    <div class="hero-slide hero-slide--ambiental">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <span class="badge-hero text-uppercase mb-3">
                                        Dimensión ambiental
                                    </span>
                                    <h1 class="mb-3">
                                        Sostenibilidad y protección de los ecosistemas
                                    </h1>
                                    <p class="lead mb-4">
                                        Información para salvaguardar los recursos naturales y orientar el desarrollo sostenible del territorio.
                                    </p>
                                    <a href="indic-ambiental.php" class="btn btn-primary btn-hero-primary mr-2 mb-2">
                                        Ver más de esta dimensión
                                    </a>
                                </div>
                                <div class="col-lg-5 d-none d-lg-block text-right">
                                    <img src="assets/svg/ambiental-color.svg" alt="Dimensión ambiental" class="hero-graphic">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide CTI -->
                <div class="carousel-item">
                    <div class="hero-slide hero-slide--cti">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <span class="badge-hero text-uppercase mb-3">
                                        Dimensión CTeI
                                    </span>
                                    <h1 class="mb-3">
                                        Ciencia, Tecnología e Innovación para transformar Boyacá
                                    </h1>
                                    <p class="lead mb-4">
                                        Recursos, capacidades e innovación articulados con las políticas de CTI a nivel nacional y departamental.
                                    </p>
                                    <a href="indic-tecnologia.php" class="btn btn-primary btn-hero-primary mr-2 mb-2">
                                        Ver más de esta dimensión
                                    </a>
                                </div>
                                <div class="col-lg-5 d-none d-lg-block text-right">
                                    <img src="assets/svg/tecnologia-color.svg" alt="Dimensión CTI" class="hero-graphic">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Siguiente</span>
            </a>
        </div>
    </section>

   <section id="dimensiones" class="container py-6">
  <div class="row justify-content-center mb-5">
    <div class="col-lg-8 text-center">
      <h2 class="section-title mb-3">Explora nuestras dimensiones</h2>
      <p class="section-subtitle">
        Accede a los indicadores, análisis y visualizaciones asociados a cada dimensión del desarrollo del departamento.
      </p>
    </div>
  </div>

  <div class="row dimension-grid justify-content-center text-center">
    <div class="col-6 col-md-4 col-lg-2 mb-4">
      <a href="indic-economico.php" class="dimension-card dimension-card--economica shadow-sm h-100">
        <div class="dimension-card__icon mb-3">
          <img src="assets/svg/economico-color.svg" alt="Dimensión económica">
        </div>
        <h3 class="dimension-card__title">Económica</h3>
        <p class="dimension-card__text">Actividad productiva, competitividad y finanzas públicas.</p>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2 mb-4">
      <a href="indic-social.php" class="dimension-card dimension-card--social shadow-sm h-100">
        <div class="dimension-card__icon mb-3">
          <img src="assets/svg/social-color.svg" alt="Dimensión social">
        </div>
        <h3 class="dimension-card__title">Social</h3>
        <p class="dimension-card__text">Calidad de vida, necesidades y potencialidades sociales.</p>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2 mb-4">
      <a href="indic-ambiental.php" class="dimension-card dimension-card--ambiental shadow-sm h-100">
        <div class="dimension-card__icon mb-3">
          <img src="assets/svg/ambiental-color.svg" alt="Dimensión ambiental">
        </div>
        <h3 class="dimension-card__title">Ambiental</h3>
        <p class="dimension-card__text">Recursos naturales, riesgo y sostenibilidad ambiental.</p>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2 mb-4">
      <a href="#dim-ctiindic-tecnologia.php" class="dimension-card dimension-card--cti shadow-sm h-100">
        <div class="dimension-card__icon mb-3">
          <img src="assets/svg/tecnologia-color.svg" alt="Dimensión CTI">
        </div>
        <h3 class="dimension-card__title">CTI</h3>
        <p class="dimension-card__text">Ciencia, Tecnología e Innovación en el territorio.</p>
      </a>
    </div>
    <div class="col-6 col-md-4 col-lg-2 mb-4">
      <a href="indic-genero.php" class="dimension-card dimension-card--cultural shadow-sm h-100">
        <div class="dimension-card__icon mb-3">
          <img src="assets/svg/genero-color.svg" alt="Dimensión cultural">
        </div>
        <h3 class="dimension-card__title">Genero</h3>
        <p class="dimension-card__text">Asuntos de Genero</p>
      </a>
    </div>
  </div>
</section>




    <!-- NOTICIAS Y BOLETINES (nuevo bloque) -->
    <div class="container my-5">
        <div class="news-section">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="section-title mb-1">Noticias y boletines</h2>
                    <p class="section-subtitle mb-0">
                        Mantente al día con las actualizaciones de indicadores, publicaciones y actividades de la Red de Observatorios de Boyacá.
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="indic-boletin.php" class="btn btn-outline-primary btn-sm">
                        Ver todos los boletines
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Tarjeta 1 - ejemplo estático (luego puedes alimentarlo desde BD) -->
                <div class="col-md-4 mb-4">
                    <article class="card h-100 news-card">
                        <div class="card-body">
                            <span class="news-card__tag">Boletín</span>
                            <h3 class="h5 mt-2">
                                Actualización de indicadores económicos y sociales
                            </h3>
                            <p class="news-card__meta">
                                Publicado el 02 de diciembre de 2025
                            </p>
                            <p class="news-card__excerpt">
                                Consulta los nuevos datos sobre empleo, producción agrícola y condiciones sociales en los municipios de Boyacá.
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="indic-boletin.php" class="btn btn-link p-0">
                                Leer más
                            </a>
                        </div>
                    </article>
                </div>

                <!-- Tarjeta 2 -->
                <div class="col-md-4 mb-4">
                    <article class="card h-100 news-card">
                        <div class="card-body">
                            <span class="news-card__tag">Noticia</span>
                            <h3 class="h5 mt-2">
                                Nueva visualización para la dimensión ambiental
                            </h3>
                            <p class="news-card__meta">
                                Publicado el 25 de noviembre de 2025
                            </p>
                            <p class="news-card__excerpt">
                                Se incorpora un tablero interactivo para seguir el estado de los recursos hídricos y la gestión del riesgo en Boyacá.
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="indic-boletin.php" class="btn btn-link p-0">
                                Leer más
                            </a>
                        </div>
                    </article>
                </div>

                <!-- Tarjeta 3 -->
                <div class="col-md-4 mb-4">
                    <article class="card h-100 news-card">
                        <div class="card-body">
                            <span class="news-card__tag">Evento</span>
                            <h3 class="h5 mt-2">
                                Taller de apropiación social del conocimiento
                            </h3>
                            <p class="news-card__meta">
                                Publicado el 18 de noviembre de 2025
                            </p>
                            <p class="news-card__excerpt">
                                Encuentro con actores del territorio para utilizar los indicadores del observatorio en procesos de planeación local.
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="indic-boletin.php" class="btn btn-link p-0">
                                Leer más
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>

    <!-- BOYACÁ EN IMÁGENES (reutilizando tu picture-container) -->
    <div class="container-fluid picture-container my-5 py-4">
        <div class="container mb-4">
            <h2 class="section-title text-center">Boyacá en imágenes</h2>
            <p class="section-subtitle text-center">
                Territorio, comunidades y actividades productivas que inspiran los análisis del observatorio.
            </p>
        </div>
        <div class="row">
            <div class="div-1 col-12 col-sm-6 mb-3 mb-sm-0">
                <img src="./assets/pictures/thumbs-up.jpg" alt="Participación ciudadana">
            </div>
            <div class="picture col-12 col-sm-6">
                <div class="div-2 mb-2">
                    <img src="./assets/pictures/colegio.jpg" alt="Educación en Boyacá">
                </div>
                <div class="div-3">
                    <img src="./assets/pictures/campo.jpg" alt="Campo boyacense">
                </div>
            </div>
        </div>
    </div>


    <!-- PARTICIPANTES (se mantiene tu owl-carousel) -->
    <div class="container mt-4 mb-5 partners-section">
        <h2 class="section-title text-center">
            Aliados de la Red de Observatorios
        </h2>
        <p class="section-subtitle text-center mb-4">
            Articulamos esfuerzos entre la Gobernación de Boyacá, la academia y otras entidades para fortalecer el análisis territorial.
        </p>
        <div class="owl-carousel owl-theme">
            <div class="item">
                <img src="assets/svg/logos/gobboy.png" alt="Gobernación de Boyacá">
            </div>
            <div class="item">
                <img src="assets/svg/logos/secplan.png" alt="Secretaría de Planeación">
            </div>
            <div class="item">
                <img src="assets/svg/carruselGenero/SecIntegracion.png" alt="Secretaría de Integración Social">
            </div>
            <div class="item">
                <img src="assets/svg/carruselGenero/SecGobierno.png" alt="Secretaría de Gobierno de Boyacá">
            </div>
            <div class="item">
                <img src="assets/svg/carruselGenero/Fiscalia.png" alt="Fiscalía">
            </div>
            <div class="item">
                <img src="assets/svg/carruselGenero/MedicinaLegal.png" alt="Instituto Nacional de Medicina Legal y Ciencias Forenses">
            </div>            

            <div class="item">
                <img src="assets/svg/logos/uptc.svg" alt="UPTC">
            </div>
            <div class="item">
                <img src="assets/svg/logos/ociteb.svg" alt="OCITEB">
            </div>
            <div class="item">
                <img src="assets/svg/logos/jdc.png" alt="Juan de Castellanos">
            </div>
            <div class="item">
                <img src="assets/svg/logos/santoto.png" alt="Santo Tomás">
            </div>
            <div class="item">
                <img src="assets/svg/logos/unad.png" alt="UNAD">
            </div>
            <div class="item">
                <img src="assets/svg/logos/uan.svg" alt="UAN">
            </div>
            <div class="item">
                <img src="assets/svg/logos/esap.png" alt="ESAP">
            </div>
        </div>
        <br/><br/>
    </div>

</div>

<?php include  'include/footer.php' ?>
