<?php
$observatories = require __DIR__ . '/config/observatories.php';
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (!array_key_exists($slug, $observatories)) {
    http_response_code(404);
    die('Observatorio no encontrado');
}
$obs = $observatories[$slug];
$economicMode = $slug === 'economico';
$genderMode = $slug === 'genero';

$heroSlides = [
    'economico' => [
        ['title' => 'Coyuntura económica territorial', 'text' => 'Monitoree TRM, inflación, empleo y variables macro en una sola vista.'],
        ['title' => 'Indicadores de mercado y finanzas', 'text' => 'Panel ejecutivo para análisis rápido de tendencias y variaciones.'],
        ['title' => 'Noticias y alertas económicas', 'text' => 'Actualidad, boletines y eventos para toma de decisiones.'],
    ],
    'social' => [
        ['title' => 'Bienestar y desarrollo social', 'text' => 'Siga indicadores de salud, educación, empleo y calidad de vida.'],
        ['title' => 'Enfoque territorial y poblacional', 'text' => 'Analice brechas por municipio, grupos etarios y poblaciones priorizadas.'],
        ['title' => 'Información útil para ciudadanía', 'text' => 'Exploración simple con contexto para comprender cada indicador.'],
    ],
    'ambiente' => [
        ['title' => 'Estado ambiental del territorio', 'text' => 'Calidad del aire, agua, residuos y biodiversidad en seguimiento permanente.'],
        ['title' => 'Datos para acción climática', 'text' => 'Indicadores temáticos y trazabilidad para apoyar gestión ambiental.'],
        ['title' => 'Visualización pública y transparente', 'text' => 'Tarjetas, categorías y descargas para uso ciudadano e institucional.'],
    ],
    'cti' => [
        ['title' => 'Ciencia, tecnología e innovación', 'text' => 'Mida capacidades, proyectos, inversión y resultados del ecosistema CTI.'],
        ['title' => 'Monitoreo estratégico de capacidades', 'text' => 'Panel con métricas de investigación, talento y transferencia.'],
        ['title' => 'Conexión entre academia y territorio', 'text' => 'Información para orientar decisiones de política pública e innovación.'],
    ],
    'genero' => [
        ['title' => 'Asuntos de género con enfoque integral', 'text' => 'Brechas, violencias, participación y autonomía con lectura comprensible.'],
        ['title' => 'Rutas, servicios y seguimiento', 'text' => 'Contenidos de interés ciudadano con enfoque diferencial y territorial.'],
        ['title' => 'Información para prevención y decisión', 'text' => 'Datos y recursos para instituciones, organizaciones y comunidad.'],
    ],
];
$slides = $heroSlides[$slug] ?? $heroSlides['social'];

$tableroUrlBySlug = [
    'economico' => 'indic-economico.php',
    'social' => 'indic-social.php',
    'ambiente' => 'indic-ambiental.php',
    'cti' => 'indic-tecnologia.php',
    'genero' => 'indic-genero.php',
];
$tableroUrl = $tableroUrlBySlug[$slug] ?? 'estado-observatorio.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($obs['name']) ?> · Red de Observatorios</title>
    <meta name="description" content="Micrositio de <?= htmlspecialchars($obs['name']) ?> con tablero, hoja de vida, categorías, noticias y descargas.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern/microsite-pro.css">
</head>
<body style="--obs-color: <?= htmlspecialchars($obs['color']) ?>; --obs-accent: <?= htmlspecialchars($obs['accent']) ?>;">
<?php if ($economicMode): ?>
<div class="market-strip market-strip--active" aria-label="Cinta superior de indicadores económicos">
    <div class="market-strip__track" id="marketTickerTrack"></div>
</div>
<?php endif; ?>

<header class="obs-header">
    <div class="container d-flex justify-content-between align-items-center py-3 gap-3 flex-wrap">
        <a href="index.php" class="back-link d-flex align-items-center gap-2"><img src="assets/svg/logo.svg" alt="Logo Red de Observatorios" class="brand-logo"> <span>Inicio Red</span></a>
        <nav class="d-flex gap-2 flex-wrap">
            <a href="#inicio">Inicio</a>
            <a href="#tablero">Tablero</a>
            <a href="#hojavida">Hoja de vida</a>
            <a href="#categorias">Categorías</a>
            <a href="#descargas">Descargas</a>
            <a href="admin/auth/login.php">Administración</a>
        </nav>
    </div>
</header>

<main class="container py-4" id="inicio">
    <section id="heroCarousel" class="carousel slide hero-carousel mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php foreach ($slides as $idx => $slide): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($slides as $idx => $slide): ?>
            <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                <section class="hero p-4">
                    <span class="badge rounded-pill text-bg-light mb-2"><?= htmlspecialchars($obs['name']) ?></span>
                    <h1><?= htmlspecialchars($slide['title']) ?></h1>
                    <p><?= htmlspecialchars($slide['text']) ?></p>
                </section>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    </section>

    <?php if ($economicMode): ?>
    <section class="econ-dashboard mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2>Panel de coyuntura económica</h2>
            <small>Diseño tipo dashboard financiero con variables clave</small>
        </div>
        <div class="row g-3">
            <div class="col-lg-8">
                <article class="dash-card">
                    <h3>Dólar spot (referencia)</h3>
                    <p class="big">$ <span id="fxMain">3.812,40</span> <span class="up">▲ +0,42%</span></p>
                    <div class="mini-grid">
                        <div><small>Apertura</small><strong>$ 3.790,00</strong></div>
                        <div><small>Máximo</small><strong>$ 3.830,00</strong></div>
                        <div><small>Mínimo</small><strong>$ 3.772,40</strong></div>
                        <div><small>Cierre ant.</small><strong>$ 3.796,50</strong></div>
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <article class="dash-card h-100">
                    <h3>Tasas e inflación</h3>
                    <ul class="kpi-list">
                        <li><span>IPC mensual</span><strong>1,08%</strong></li>
                        <li><span>Tasa intervención</span><strong>10,25%</strong></li>
                        <li><span>Desempleo nacional</span><strong>10,9%</strong></li>
                        <li><span>PIB anual</span><strong>2,3%</strong></li>
                    </ul>
                </article>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>TRM Hoy</h3><p class="big">$ 3.795,55</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Euro</h3><p class="big">$ 4.394,68</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Petróleo Brent</h3><p class="big">US$ 92,40</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Oro</h3><p class="big">US$ 2.153,10</p></article></div>
        </div>
    </section>
    <?php endif; ?>

    <section class="row g-3 mb-4">
        <div class="col-md-4"><article class="base-card"><h2>Indicadores</h2><p id="kpi-total">-</p><small>Disponibles en esta dimensión</small></article></div>
        <div class="col-md-4"><article class="base-card"><h2>Noticias y eventos</h2><p>12</p><small>Actualizaciones y agenda</small></article></div>
        <div class="col-md-4"><article class="base-card"><h2>Documentos / Datasets</h2><p>38</p><small>Descargables y abiertos</small></article></div>
    </section>

    <section class="tabs-panel mb-4" id="tablero">
        <h2>Exploración del observatorio</h2>
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#p-tablero" type="button">Tablero</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-hojavida" type="button">Hoja de vida</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-categorias" type="button">Categorías</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-descargas" type="button">Descarga de datos</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="p-tablero">
                <article class="content-card">
                    <h3>Tablero principal</h3>
                    <p>Acceda al tablero completo de esta dimensión y sus visualizaciones activas.</p>
                    <a class="btn btn-dark btn-sm" href="<?= htmlspecialchars($tableroUrl) ?>">Abrir tablero de la dimensión</a>
                </article>
            </div>
            <div class="tab-pane fade" id="p-hojavida">
                <article class="content-card" id="hojavida">
                    <h3>Hoja de vida de indicadores</h3>
                    <p>Definición, metodología, fórmula, periodicidad, cobertura y fuentes.</p>
                    <div class="mini-grid">
                        <div><strong>Definición</strong><small>Descripción clara y contexto del indicador</small></div>
                        <div><strong>Fórmula</strong><small>Expresión de cálculo y unidad de medida</small></div>
                        <div><strong>Periodicidad</strong><small>Frecuencia de actualización</small></div>
                        <div><strong>Fuente</strong><small>Entidad responsable y trazabilidad</small></div>
                    </div>
                </article>
            </div>
            <div class="tab-pane fade" id="p-categorias">
                <article class="content-card" id="categorias">
                    <h3>Categorías temáticas</h3>
                    <div class="cards-grid">
                        <div class="mini-card">Salud / Bienestar</div>
                        <div class="mini-card">Economía / Mercado</div>
                        <div class="mini-card">Territorio / Ambiente</div>
                        <div class="mini-card">Innovación / Capacidades</div>
                        <div class="mini-card">Género / Participación</div>
                        <div class="mini-card">Violencias / Prevención</div>
                    </div>
                </article>
            </div>
            <div class="tab-pane fade" id="p-descargas">
                <article class="content-card" id="descargas">
                    <h3>Centro de descargas</h3>
                    <p>Archivos abiertos en formatos CSV/XLSX/PDF para uso institucional y ciudadano.</p>
                    <ul class="download-list">
                        <li><a href="estado-observatorio.php">Catálogo general de indicadores</a></li>
                        <li><a href="<?= htmlspecialchars($obs['legacy_url']) ?>">Repositorio de esta dimensión</a></li>
                        <li><a href="api/indicators.php">API catálogo JSON</a></li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-lg-7">
            <article class="content-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2>Noticias y eventos</h2>
                    <a href="#">Ver todas</a>
                </div>
                <div class="news-list">
                    <div><strong>Actualización de serie histórica territorial</strong><span>hace 2 días</span></div>
                    <div><strong>Nuevo boletín temático trimestral</strong><span>hace 5 días</span></div>
                    <div><strong>Informe especial por subregión</strong><span>hace 1 semana</span></div>
                    <div><strong>Convocatoria de participación ciudadana</strong><span>hace 2 semanas</span></div>
                </div>
            </article>
        </div>
        <div class="col-lg-5">
            <article class="content-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2>Indicadores destacados</h2>
                    <a href="estado-observatorio.php">Catálogo</a>
                </div>
                <ul id="featuredIndicators" class="list-group"></ul>
            </article>
        </div>
    </section>

    <?php if ($genderMode): ?>
    <section class="content-card genero-highlight mb-4" id="barreras-quees">
        <h2>Módulo ampliado de Asuntos de Género</h2>
        <p>Se migra y organiza contenido clave del portal de género actual para conservar identidad y mejorar interacción.</p>

        <div class="chip-group mb-3" role="tablist" aria-label="Secciones de barreras de acceso">
            <button class="chip is-active" data-target="quees">¿Qué es una barrera?</button>
            <button class="chip" data-target="leyes">Tus derechos</button>
            <button class="chip" data-target="ejemplos">Ejemplos</button>
            <button class="chip" data-target="quehacer">¿Qué hacer?</button>
        </div>

        <section class="gender-mod" data-sec="quees">
            <div class="two-col">
                <img src="assets/svg/img-genero/barreras/barreraAcceso.png" alt="¿Qué es barrera de acceso?" class="gender-img">
                <div>
                    <h4>¿Qué es una barrera de acceso?</h4>
                    <ul>
                        <li>Es un obstáculo que retrasa o impide atención oportuna, eficaz y de calidad.</li>
                        <li>Puede aparecer en urgencias, citas, exámenes, medicamentos o trámites EPS/IPS.</li>
                        <li>Si te exigen autorizaciones innecesarias o te remiten sin resolver, hay barrera.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="gender-mod d-none" data-sec="leyes">
            <div class="two-col">
                <img src="assets/svg/img-genero/barreras/derechos.png" alt="Tus derechos" class="gender-img">
                <div>
                    <h4>Tus derechos (leyes)</h4>
                    <ul>
                        <li>Derecho fundamental a la salud: acceso oportuno y con calidad.</li>
                        <li>Atención prioritaria e inmediata en violencias basadas en género.</li>
                        <li>Trato digno, sin discriminación y con enfoque diferencial.</li>
                        <li>Continuidad de ruta: controles, medicamentos y remisiones.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="gender-mod d-none" data-sec="ejemplos">
            <div class="two-col">
                <img src="assets/svg/img-genero/barreras/ejemplos.png" alt="Ejemplos de barreras" class="gender-img">
                <div>
                    <h4>Ejemplos de barreras</h4>
                    <div class="cards-grid">
                        <div class="mini-card"><strong>Demoras excesivas</strong><small>Citas o procedimientos fuera de tiempos razonables.</small></div>
                        <div class="mini-card"><strong>Trámites innecesarios</strong><small>Autorizaciones que no deberían bloquear la atención.</small></div>
                        <div class="mini-card"><strong>Sin agenda disponible</strong><small>No asignan cita acorde con urgencia.</small></div>
                        <div class="mini-card"><strong>Barreras geográficas</strong><small>Distancia, transporte o horarios imposibles.</small></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="gender-mod d-none" data-sec="quehacer">
            <div class="two-col">
                <img src="assets/svg/img-genero/barreras/quehacer.png" alt="Qué hacer" class="gender-img">
                <div>
                    <h4>¿Qué hacer ante una barrera?</h4>
                    <ul>
                        <li>Solicita atención por urgencias y activación de ruta clínica cuando aplique.</li>
                        <li>Radica PQRD en EPS/IPS y guarda número de radicado.</li>
                        <li>Escala a SuperSalud: 01 8000 513 700.</li>
                        <li>Líneas de orientación: 155 (mujeres) y 141 (NNA).</li>
                    </ul>
                    <a class="btn btn-secondary btn-sm" href="tel:155">Llamar 155</a>
                </div>
            </div>
        </section>

        <div class="mt-3 d-flex flex-wrap gap-2">
            <a class="btn btn-outline-dark btn-sm" href="indic-genero.php#barreras-quees">Ver versión completa histórica</a>
            <a class="btn btn-dark btn-sm" href="mapa-fondomujer.html">Explorar recursos relacionados</a>
        </div>
    </section>
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.OBS_SLUG = <?= json_encode($slug) ?>;</script>
<script src="assets/js/modern/microsite-pro.js" defer></script>
</body>
</html>
