<?php
header('Content-Type: text/html; charset=UTF-8');
$observatories = require __DIR__ . '/config/observatories.php';
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (!array_key_exists($slug, $observatories)) {
    http_response_code(404);
    die('Observatorio no encontrado');
}
$obs = $observatories[$slug];
$assistant_obs_slug = $slug;
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/cms_public_content.php';
require_once __DIR__ . '/lib/visit_tracking.php';
require_once __DIR__ . '/lib/cms_microsite_sections.php';
$pdoVisit = cms_pdo();
require_once __DIR__ . '/lib/cms_dashboard_embed.php';
$cmsTableroDashSlug = $obs['cms_tablero_slug'] ?? 'principal';
// Tablero oficial de Power BI de la dimensión (reemplaza al tablero demo del CMS).
$pbiDashboards = is_file(__DIR__ . '/config/dashboards_powerbi.php') ? (require __DIR__ . '/config/dashboards_powerbi.php') : [];
$pbiUrl = trim((string) ($pbiDashboards[$slug] ?? ''));
$tableroEmbedSrc = $pdoVisit ? cms_microsite_tablero_embed_src($pdoVisit, $slug, $cmsTableroDashSlug) : null;
$tableroCmsFull = $tableroEmbedSrc !== null
    ? cms_microsite_tablero_public_url($slug, $cmsTableroDashSlug)
    : '';

$micrositeVisitKey = cms_microsite_visit_page_key($slug);
$micrositeVisitCount = cms_track_page_visit($pdoVisit, $micrositeVisitKey);
if ($micrositeVisitCount === null) {
    $micrositeVisitCount = cms_get_visit_count($pdoVisit, $micrositeVisitKey);
}
$economicMode = $slug === 'economico';
if ($economicMode) {
    require_once __DIR__ . '/lib/econ_indicators.php';
    $econ = econ_load();
}

/* ── Indicadores enriquecidos (Hoja de vida / Categorías / Descargas) ── */
$slugToObsId = ['economico' => 1, 'social' => 2, 'ambiente' => 3, 'cti' => 4, 'genero' => 5];
$currentObsId = $slugToObsId[$slug] ?? 0;
require_once __DIR__ . '/lib/indicator_metadata.php';
require_once __DIR__ . '/functions.php';
$imIndicators = [];
$imByCategory = [];
// Metadatos de BD indexados por id (para enriquecer; pueden faltar para algunas carpetas).
$dbMetaById = [];
if ($pdoVisit && $currentObsId > 0) {
    try {
        foreach (im_list_by_observatory($pdoVisit, $currentObsId) as $row) {
            $dbMetaById[(int) $row['id']] = $row;
        }
    } catch (Throwable $e) { /* migración pendiente */ }
}
// El listado de Categorías / Hoja de vida se construye desde las CARPETAS reales
// website/indicador/<dígito>NNN/ (las que tienen indicador.info), no desde la BD.
// Así toda tarjeta lleva a un indicador que SÍ existe y abre sus gráficos; antes
// la BD tenía IDs sin carpeta que daban "Indicador no encontrado".
if ($currentObsId > 0) {
    $indBaseDir = __DIR__ . '/indicador';
    foreach (@scandir($indBaseDir) ?: [] as $entry) {
        if (!ctype_digit($entry) || strlen($entry) !== 4 || $entry[0] !== (string) $currentObsId) {
            continue;
        }
        $dir = $indBaseDir . '/' . $entry;
        if (!is_file($dir . '/indicador.info')) {
            continue;
        }
        $inf = @getInfo($dir . '/indicador.info');
        $id = (int) $entry;
        $cat = trim((string) ($inf['categoria'] ?? '')) ?: 'Sin categoría';
        if (strcasecmp($cat, 'ND') === 0) {
            $cat = 'Sin categoría';
        }
        $rec = $dbMetaById[$id] ?? [];
        $rec['id'] = $id;
        if (empty($rec['title'])) {
            $rec['title'] = (string) ($inf['titulo'] ?? ('Indicador ' . $id));
        }
        $rec['category_1'] = $cat;
        $rec['category_2'] = $cat; // agrupar por la categoría principal (coincide con las líneas temáticas)
        $imIndicators[] = $rec;
    }
    usort($imIndicators, static function ($a, $b) {
        $c = strcasecmp((string) ($a['category_1'] ?? ''), (string) ($b['category_1'] ?? ''));
        return $c !== 0 ? $c : (((int) $a['id']) <=> ((int) $b['id']));
    });
}
foreach ($imIndicators as $row) {
    $c1 = $row['category_1'] ?? 'Sin categoría';
    $c2 = $row['category_2'] ?? '';
    $imByCategory[$c1][$c2][] = $row;
}
$imDataFiles = im_list_data_files_for_observatory($currentObsId, __DIR__);
$genderMode = $slug === 'genero';

/**
 * Devuelve un icono FontAwesome representativo para una categoría de 2° orden.
 * Coincidencia por palabras clave (case-insensitive, sin tildes); fallback genérico.
 */
function obs_category_icon(string $name): string
{
    $n = strtolower(strtr($name, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']));
    $rules = [
        // económico
        'agropecuario'                 => 'fa-wheat-awn',
        'calidad de vida campesina'    => 'fa-mountain-sun',
        'calidad de vida'              => 'fa-house-user',
        'mineria'                      => 'fa-mountain',
        'pobreza'                      => 'fa-hand-holding-dollar',
        'politica fiscal'              => 'fa-scale-balanced',
        'turismo'                      => 'fa-suitcase-rolling',
        'variables macroeconomicas'    => 'fa-chart-line',
        // social / juventud / familia / penal
        'demografia y poblacion'       => 'fa-people-group',
        'demografia'                   => 'fa-people-group',
        'discapacidad'                 => 'fa-wheelchair',
        'educacion'                    => 'fa-graduation-cap',
        'familia'                      => 'fa-people-roof',
        'juventud'                     => 'fa-children',
        'responsabilidad penal'        => 'fa-gavel',
        'salud sexual y reproductiva'  => 'fa-baby',
        'salud y mortalidad'           => 'fa-heart-pulse',
        'salud'                        => 'fa-heart-pulse',
        'violencias contra la mujer'   => 'fa-shield-halved',
        'violencia'                    => 'fa-triangle-exclamation',
        // género
        'empoderamiento economico'     => 'fa-hand-holding-dollar',
        'participacion politica'       => 'fa-landmark',
        'reintegracion y conflicto'    => 'fa-handshake',
    ];
    foreach ($rules as $needle => $icon) {
        if (strpos($n, $needle) !== false) {
            return $icon;
        }
    }
    return 'fa-layer-group';
}

/**
 * Devuelve un color hex vibrante por categoría de 2° orden.
 * Cada dimensión tiene una familia cromática propia:
 *  - Económico: amarillos/ámbares/naranjas (+ verde lima para agro, navy para macro)
 *  - Social: azules/teal/violeta (+ rojo para salud/violencia)
 *  - Género: rosas/magentas/púrpuras (+ naranja contraste, rojo para mortalidad)
 */
function obs_category_color(string $name): string
{
    $n = strtolower(strtr($name, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']));
    $rules = [
        // Económico — paleta amarillo/ámbar/naranja
        'agropecuario'                 => '#65a30d', // lime — campo
        'calidad de vida campesina'    => '#ca8a04', // amarillo dorado
        'calidad de vida'              => '#f59e0b', // ámbar
        'mineria'                      => '#92400e', // ocre / marrón mineral
        'pobreza'                      => '#eab308', // amarillo brillante
        'politica fiscal'              => '#d97706', // naranja oscuro
        'turismo'                      => '#f97316', // naranja vivo
        'variables macroeconomicas'    => '#0f3557', // navy del tema
        // Social — paleta azul/teal con rojos para salud/violencia
        'demografia y poblacion'       => '#a855f7', // (también género; cae en social si aplica)
        'demografia'                   => '#3b82f6', // azul
        'discapacidad'                 => '#8b5cf6', // violeta
        'educacion'                    => '#06b6d4', // cian
        'familia'                      => '#14b8a6', // teal
        'juventud'                     => '#f59e0b', // ámbar (energía juvenil)
        'responsabilidad penal'        => '#475569', // gris pizarra
        'salud sexual y reproductiva'  => '#f43f5e', // rosa-rojo
        'salud y mortalidad'           => '#dc2626', // rojo
        'salud'                        => '#ef4444', // rojo salud
        'violencias contra la mujer'   => '#be185d', // magenta oscuro
        'violencia'                    => '#b91c1c', // rojo oscuro
        // Género — paleta rosa/magenta/púrpura
        'empoderamiento economico'     => '#ec4899', // rosa
        'participacion politica'       => '#6366f1', // índigo
        'reintegracion y conflicto'    => '#f59e0b', // ámbar contraste
    ];
    foreach ($rules as $needle => $color) {
        if (strpos($n, $needle) !== false) {
            return $color;
        }
    }
    return '#0d6efd';
}

/** Convierte un hex (#rrggbb) a 'r,g,b' para uso en rgba() inline. */
function obs_hex_to_rgb(string $hex): string
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) !== 6) return '13,110,253';
    return implode(',', [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))]);
}

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
$dbHero = cms_microsite_hero_slides($pdoVisit, $slug);
if ($dbHero !== null && $dbHero !== []) {
    $slides = $dbHero;
}

$obsDbId = null;
$kpiIndicators = 0;
$kpiNews = 0;
$kpiDocs = 0;
$latestNewsRows = [];
if ($pdoVisit) {
    try {
        $stObs = $pdoVisit->prepare('SELECT id FROM observatories WHERE slug = ? LIMIT 1');
        $stObs->execute([$slug]);
        $obsDbId = $stObs->fetchColumn();
        if ($obsDbId !== false) {
            $obsDbId = (int) $obsDbId;
            $st = $pdoVisit->prepare('SELECT COUNT(*) FROM indicators WHERE observatory_id = ? AND content_status = "published"');
            $st->execute([$obsDbId]);
            $kpiIndicators = (int) $st->fetchColumn();
            $st = $pdoVisit->prepare('SELECT COUNT(*) FROM news WHERE observatory_id = ? AND content_status = "published"');
            $st->execute([$obsDbId]);
            $kpiNews = (int) $st->fetchColumn();
            $st = $pdoVisit->prepare('SELECT COUNT(*) FROM documents WHERE observatory_id = ? AND content_status = "published"');
            $st->execute([$obsDbId]);
            $kpiDocs = (int) $st->fetchColumn();
            $st = $pdoVisit->prepare('SELECT title, slug, summary, image_url, source, published_at FROM news WHERE observatory_id = ? AND content_status = "published" ORDER BY published_at DESC LIMIT 5');
            $st->execute([$obsDbId]);
            $latestNewsRows = $st->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Throwable $e) {
        // tablas opcionales
    }
}

/* ---------------------------------------------------------------------------
 * Conteo REAL de indicadores desde las carpetas website/indicador/NNNN/
 * (fuente de verdad de lo que se publica), líneas temáticas por categoría y
 * fecha de última actualización (mtime más reciente de los datos del dim).
 * El primer dígito del ID = observatory_id (1=econ, 2=social, 3=ambiente,
 * 4=cti, 5=género).
 * ------------------------------------------------------------------------- */
require_once __DIR__ . '/functions.php';
$dimDigit = $obsDbId ?: (['economico' => 1, 'social' => 2, 'ambiente' => 3, 'cti' => 4, 'genero' => 5][$slug] ?? 0);
$obsFolderCount = 0;
$obsLastUpdated = 0;
$folderLineCounts = [];
if ($dimDigit > 0) {
    $indDir = __DIR__ . '/indicador';
    foreach (@scandir($indDir) ?: [] as $entry) {
        if (!ctype_digit($entry) || strlen($entry) !== 4 || $entry[0] !== (string) $dimDigit) {
            continue;
        }
        $infoPath = $indDir . '/' . $entry . '/indicador.info';
        if (!is_file($infoPath)) {
            continue;
        }
        $obsFolderCount++;
        $obsLastUpdated = max($obsLastUpdated, (int) @filemtime($infoPath));
        foreach (@glob($indDir . '/' . $entry . '/*.csv') ?: [] as $csv) {
            $obsLastUpdated = max($obsLastUpdated, (int) @filemtime($csv));
        }
        $inf = @getInfo($infoPath);
        $cat = trim((string) ($inf['categoria'] ?? ''));
        if ($cat === '' || strcasecmp($cat, 'ND') === 0) {
            $cat = 'Sin categoría';
        }
        $folderLineCounts[$cat] = ($folderLineCounts[$cat] ?? 0) + 1;
    }
}
if ($obsFolderCount > 0) {
    $kpiIndicators = $obsFolderCount; // conteo real
}
// Fecha de última actualización en español (dd de mes de aaaa)
$obsLastUpdatedLabel = '';
if ($obsLastUpdated > 0) {
    $meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $obsLastUpdatedLabel = date('j', $obsLastUpdated) . ' de ' . $meses[(int) date('n', $obsLastUpdated)] . ' de ' . date('Y', $obsLastUpdated);
}

$heroPreload = '';
if (!empty($slides[0]['image_url'])) {
    $heroPreload = (string) $slides[0]['image_url'];
}

$tableroUrlBySlug = [
    'economico' => 'indic-economico.php',
    'social' => 'indic-social.php',
    'ambiente' => 'indic-ambiental.php',
    'cti' => 'indic-tecnologia.php',
    'genero' => 'indic-genero.php',
];
$tableroUrl = $tableroUrlBySlug[$slug] ?? 'estado-observatorio.php';

$contentPath = __DIR__ . '/data/content.json';
$contentData = [];
if (file_exists($contentPath)) {
    $contentData = json_decode(file_get_contents($contentPath), true);
    if (!is_array($contentData)) {
        $contentData = [];
    }
}
$genderExtraTabs = [];
if ($genderMode && isset($contentData['genero_tabs']) && is_array($contentData['genero_tabs'])) {
    $genderExtraTabs = $contentData['genero_tabs'];
}

/* ── Árbol dinámico de pestañas desde el CMS (cms_microsite_sections) ── */
$msSectionsTree = $pdoVisit ? cms_microsite_sections_tree($pdoVisit, $slug, true) : [];

/* Secciones con key 'widget-*' NO son pestañas: alimentan widgets editables
   desde el CMS (carrusel de imágenes, integrantes, etc.). */
$msWidgetSections = [];
foreach ($msSectionsTree as $msIdx => $msRootW) {
    if (strpos((string) ($msRootW['section_key'] ?? ''), 'widget-') === 0) {
        $msWidgetSections[$msRootW['section_key']] = $msRootW;
        unset($msSectionsTree[$msIdx]);
    }
}
$msSectionsTree = array_values($msSectionsTree);
$hasMsSections  = !empty($msSectionsTree);

/* Género: pestañas CMS que van de primeras (después de Tablero de datos y
   Ruta de atención): Barreras de acceso y Atención integral. */
$msFirstKeys = $genderMode ? ['barreras', 'atencion'] : [];
$msTabsFirst = [];
foreach ($msFirstKeys as $msK) {
    foreach ($msSectionsTree as $msRootF) {
        if (($msRootF['section_key'] ?? '') === $msK) { $msTabsFirst[] = $msRootF; break; }
    }
}
$msTabsRest = array_values(array_filter($msSectionsTree, static fn ($r) => !in_array($r['section_key'] ?? '', $msFirstKeys, true)));
if ($hasMsSections) {
    // Si la BD ya provee las pestañas, ignoramos las del content.json y la
    // sección hardcoded de "barreras-quees" para evitar duplicación.
    $genderExtraTabs = [];
}
?>
<!doctype html>
<html lang="es">
<head>
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png">
    <link rel="apple-touch-icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-180x180.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($obs['name']) ?> · Red de Observatorios</title>
    <meta name="description" content="Micrositio de <?= htmlspecialchars($obs['name']) ?> con tablero, hoja de vida, categorías, noticias y descargas.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern/microsite-pro.css">
    <?php if ($genderMode): ?>
    <link rel="stylesheet" href="assets/css/modern/genero-content.css">
    <?php endif; ?>
    <?php if ($heroPreload !== ''): ?>
    <link rel="preload" as="image" href="<?= htmlspecialchars($heroPreload) ?>">
    <?php endif; ?>
</head>
<?php
// Color plano para la pestaña activa: amarillo quemado para económico, color principal para los demás
$tabActiveByObs = [
    'economico' => '#a16207', // amarillo quemado (contraste 4.7:1 con blanco — pasa AA)
    // los demás heredan --obs-color por defecto
];
$obsTabActive = $tabActiveByObs[$slug] ?? $obs['color'];
?>
<body class="observatorio-page" style="--obs-color: <?= htmlspecialchars($obs['color']) ?>; --obs-accent: <?= htmlspecialchars($obs['accent']) ?>; --obs-tab-active: <?= htmlspecialchars($obsTabActive) ?>; --obs-color-rgb: <?= htmlspecialchars(obs_hex_to_rgb($obs['color'])) ?>; --obs-accent-rgb: <?= htmlspecialchars(obs_hex_to_rgb($obs['accent'])) ?>;">
<?php if ($economicMode): ?>
<div class="market-strip market-strip--active" aria-label="Cinta superior de indicadores económicos">
    <div class="market-strip__track" id="marketTickerTrack"></div>
</div>
<?php endif; ?>

<header class="obs-header">
    <div class="container d-flex justify-content-between align-items-center py-3 gap-3 flex-wrap">
        <a href="index.php" class="back-link d-flex align-items-center gap-2"><img src="assets/svg/logo.svg" alt="Logo Red de Observatorios" class="brand-logo"> <span>Inicio Red</span></a>
        <div class="d-flex flex-wrap align-items-center gap-3 ms-md-auto">
            <span class="obs-visit-pill" title="Visitantes únicos en este micrositio (mismo navegador no suma varias veces)">
                <i class="fa-solid fa-chart-simple" aria-hidden="true"></i>
                <?= number_format((int) $micrositeVisitCount, 0, ',', '.') ?> visitantes únicos
            </span>
            <nav class="d-flex gap-2 flex-wrap">
                <a href="#inicio">Inicio</a>
                <a href="index.php">Red de observatorios</a>
                <a href="#tablero">Explora el observatorio</a>
                <a href="noticias.php?obs=<?= urlencode($slug) ?>">Noticias</a>
                <a href="#" role="button" data-bs-toggle="modal" data-bs-target="#portalSurveyModal">Encuesta opcional</a>
            </nav>
        </div>
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
            <?php $hasImg = !empty($slide['image_url'] ?? ''); ?>
            <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                <section class="hero hero-slide-with-media <?= $hasImg ? '' : 'hero-slide-no-img' ?>">
                    <?php if ($hasImg): ?>
                    <div class="hero-slide-media" aria-hidden="true">
                        <img src="<?= htmlspecialchars($slide['image_url']) ?>" alt="" loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>" decoding="async">
                    </div>
                    <div class="hero-slide-overlay"></div>
                    <?php endif; ?>
                    <div class="hero-slide-copy">
                        <span class="hero-tag"><?= htmlspecialchars($obs['name']) ?></span>
                        <?php $hTag = $idx === 0 ? 'h1' : 'h2'; /* un solo h1 por página: las demás diapositivas usan h2 */ ?>
                        <<?= $hTag ?>><?= htmlspecialchars($slide['title']) ?></<?= $hTag ?>>
                        <p class="mb-0"><?= htmlspecialchars($slide['text'] ?? $slide['slide_text'] ?? '') ?></p>
                        <?php if (!empty($slide['link_url'])): ?>
                            <a class="hero-slide-link" href="<?= htmlspecialchars($slide['link_url']) ?>">Ver más <i class="bi bi-arrow-right"></i></a>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    </section>

    <?php
    // Líneas temáticas: datos pre-calculados para que observatory-description.php
    // pueda renderizar las tarjetas en el medio (entre Consulta y Fuentes).
    $categoriesByObs = require __DIR__ . '/config/observatory_categories.php';
    $linesInfo = $categoriesByObs[$slug] ?? [];
    // Conteo de indicadores por línea: usar las carpetas reales si están
    // disponibles (fuente de verdad); si no, caer al catálogo de BD.
    if (!empty($folderLineCounts)) {
        $lineCounts = $folderLineCounts;
    } else {
        $lineCounts = [];
        foreach ($imIndicators as $r0) {
            $c2 = trim((string) ($r0['category_2'] ?? ''));
            if ($c2 === '' || strcasecmp($c2, 'ND') === 0) $c2 = 'Sin categoría';
            $lineCounts[$c2] = ($lineCounts[$c2] ?? 0) + 1;
        }
    }
    $lineKeys = array_unique(array_merge(array_keys($linesInfo), array_keys($lineCounts)));
    ?>
    <?php
    /* Layout común a los 5 observatorios (género es la referencia):
       descripción de borde a borde sin la información general (líneas,
       dimensiones y fuentes salen de la tarjeta), y debajo una fila con el
       carrusel de imágenes (CMS: sección widget-carrusel) a la izquierda y
       las líneas temáticas como widgets verticales a la derecha. */
    $obsDescHideLines = true;
    $obsDescHideExtra = true;
    // En género mostramos las fuentes oficiales dentro de la tarjeta (antes iban en una imagen aparte).
    $obsDescHideFuentes = $genderMode ? false : true;
    ?>
    <?php require __DIR__ . '/include/observatory-description.php'; ?>
    <div class="row g-3 mb-2 align-items-start">
        <div class="col-lg-8">
            <?php require __DIR__ . '/include/cms-widget-carousel.php'; ?>
        </div>
        <aside class="col-lg-4">
            <?php require __DIR__ . '/include/genero-sidebar.php'; ?>
        </aside>
    </div>

    <?php if ($economicMode): ?>
    <section class="econ-dashboard mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2>Panel de coyuntura económica</h2>
            <small>
                <?php if (!empty($econ['trm']['date'])): ?>
                    TRM oficial del <strong><?= htmlspecialchars($econ['trm']['date']) ?></strong> · actualizado <?= htmlspecialchars($econ['fetched_at'] ?? '—') ?>
                    <?php if (($econ['trm']['source'] ?? '') === 'default'): ?>
                        <span class="badge bg-warning text-dark">sin conexión — valor cacheado</span>
                    <?php endif; ?>
                <?php else: ?>
                    Diseño tipo dashboard financiero con variables clave
                <?php endif; ?>
            </small>
        </div>
        <div class="row g-3">
            <div class="col-lg-8">
                <article class="dash-card">
                    <h3>Dólar spot (TRM oficial)</h3>
                    <?php $chg = (float) ($econ['usd_change'] ?? 0); $chgClass = $chg >= 0 ? 'up' : 'down'; $chgArrow = $chg >= 0 ? '▲' : '▼'; ?>
                    <p class="big">$ <span id="fxMain"><?= econ_format_cop((float) $econ['trm']['value']) ?></span> <span class="<?= $chgClass ?>"><?= $chgArrow ?> <?= ($chg >= 0 ? '+' : '') . number_format($chg, 2, ',', '.') ?>%</span></p>
                    <div class="mini-grid">
                        <div><small>Apertura</small><strong>$ <?= econ_format_cop((float) $econ['usd_open']) ?></strong></div>
                        <div><small>Máximo</small><strong>$ <?= econ_format_cop((float) $econ['usd_high']) ?></strong></div>
                        <div><small>Mínimo</small><strong>$ <?= econ_format_cop((float) $econ['usd_low']) ?></strong></div>
                        <div><small>Cierre ant.</small><strong>$ <?= econ_format_cop((float) $econ['usd_prev']) ?></strong></div>
                    </div>
                </article>
            </div>
            <div class="col-lg-4">
                <article class="dash-card h-100">
                    <h3>Tasas e inflación</h3>
                    <ul class="kpi-list">
                        <li><span>IPC mensual</span><strong><?= number_format((float) $econ['ipc_mensual'], 2, ',', '.') ?>%</strong></li>
                        <li><span>Tasa intervención</span><strong><?= number_format((float) $econ['tasa_interv'], 2, ',', '.') ?>%</strong></li>
                        <li><span>Desempleo nacional</span><strong><?= number_format((float) $econ['desempleo'], 1, ',', '.') ?>%</strong></li>
                        <li><span>PIB anual</span><strong><?= number_format((float) $econ['pib_anual'], 1, ',', '.') ?>%</strong></li>
                    </ul>
                </article>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>TRM Hoy</h3><p class="big">$ <?= econ_format_cop((float) $econ['trm']['value']) ?></p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Euro</h3><p class="big">$ <?= econ_format_cop((float) $econ['euro']) ?></p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Petróleo Brent</h3><p class="big">US$ <?= econ_format_usd((float) $econ['brent']) ?></p></article></div>
            <div class="col-md-6 col-xl-3"><article class="dash-card"><h3>Oro</h3><p class="big">US$ <?= econ_format_usd((float) $econ['gold']) ?></p></article></div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!$genderMode): /* en género estos datos ya están en la tarjeta de descripción */ ?>
    <section class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3"><article class="base-card"><h2>Indicadores</h2><p id="kpi-total" data-static-kpi="1"><?= $kpiIndicators ?></p><small>Indicadores disponibles en esta dimensión<?php if ($obsLastUpdatedLabel !== ''): ?><br><span class="kpi-updated"><i class="fa-regular fa-clock" aria-hidden="true"></i> Última actualización: <strong><?= htmlspecialchars($obsLastUpdatedLabel) ?></strong></span><?php endif; ?></small></article></div>
        <div class="col-md-6 col-lg-3"><article class="base-card"><h2>Líneas temáticas</h2><p><?= count($lineKeys ?? $folderLineCounts) ?: count($folderLineCounts) ?></p><small>Categorías de análisis en esta dimensión</small></article></div>
        <div class="col-md-6 col-lg-3"><article class="base-card"><h2>Noticias y eventos</h2><p><?= $kpiNews ?></p><small>Noticias con estado publicado</small></article></div>
        <div class="col-md-6 col-lg-3"><article class="base-card"><h2>Visitas</h2><p><?= number_format((int) $micrositeVisitCount, 0, ',', '.') ?></p><small>Visitantes únicos de este micrositio</small></article></div>
    </section>
    <?php endif; ?>

    <?php if (!empty($lineKeys)): ?>
    <section class="lines-modal-host" aria-hidden="true">
        <style>
            /* Estilos de las tarjetas se mueven al observatory-description.php; aquí solo el modal */
            .line-modal-header{background:linear-gradient(135deg,var(--c),rgba(var(--c-rgb),.7));color:#fff;border-bottom:none;border-top-left-radius:.5rem;border-top-right-radius:.5rem;padding:1.25rem 1.5rem;position:relative;overflow:hidden}
            .line-modal-header::after{content:"";position:absolute;right:-40px;top:-50px;width:170px;height:170px;border-radius:50%;background:rgba(255,255,255,.12);pointer-events:none}
            .line-modal-header h5{font-weight:700;display:flex;align-items:center;gap:.65rem;position:relative;z-index:1}
            .line-modal-header .btn-close{filter:invert(1);position:relative;z-index:1}
            .line-modal-icon{width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,.22);display:inline-flex;align-items:center;justify-content:center;font-size:1.2rem}
            .line-modal-body{padding:1.35rem 1.5rem}
            .line-modal-intro{display:flex;gap:.75rem;align-items:flex-start;background:rgba(var(--c-rgb),.07);border-left:4px solid var(--c);border-radius:10px;padding:.85rem 1rem;color:#374151;font-size:.95rem;line-height:1.55;margin-bottom:1.2rem}
            .line-modal-intro i{color:var(--c);margin-top:.2rem;flex:0 0 auto}
            .line-modal-body h6{font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;margin:1.2rem 0 .65rem;font-weight:700;display:flex;align-items:center;gap:.45rem}
            .line-modal-body h6 i{color:var(--c)}
            .line-modal-body h6:first-of-type{margin-top:.2rem}
            ul#lineModalConsulta{list-style:none;padding:0;margin:0;display:grid;gap:.5rem}
            ul#lineModalConsulta li{display:flex;gap:.65rem;align-items:flex-start;background:rgba(var(--c-rgb),.05);border:1px solid rgba(var(--c-rgb),.14);border-radius:10px;padding:.6rem .85rem;color:#374151;line-height:1.45;font-size:.92rem}
            ul#lineModalConsulta li::before{content:"\f058";font-family:"Font Awesome 6 Free";font-weight:900;color:var(--c);flex:0 0 auto;margin-top:.1rem}
            ul#lineModalFuentes{list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;gap:.5rem}
            ul#lineModalFuentes li{display:inline-flex;align-items:center;gap:.5rem;background:#fff;border:1.5px solid rgba(var(--c-rgb),.3);border-radius:999px;padding:.4rem .85rem;color:#1f2937;font-size:.84rem;font-weight:600}
            ul#lineModalFuentes li::before{content:"\f1c0";font-family:"Font Awesome 6 Free";font-weight:900;color:var(--c);font-size:.8rem}
            .line-modal-body .badge-periodo{display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,var(--c),rgba(var(--c-rgb),.75));color:#fff;font-weight:600;padding:.5rem 1rem;border-radius:999px;font-size:.83rem;box-shadow:0 4px 10px rgba(var(--c-rgb),.3)}
            .line-modal-body .badge-periodo::before{content:"\f2f1";font-family:"Font Awesome 6 Free";font-weight:900;font-size:.8rem}
            .line-modal-footer{border-top:1px solid #eef0f4}
            .line-modal-footer .btn-primary{background:var(--c);border-color:var(--c);font-weight:600}
            .line-modal-footer .btn-primary:hover{background:rgba(var(--c-rgb),.85);border-color:rgba(var(--c-rgb),.85)}
        </style>

        <!-- Modal de línea temática (se llena dinámicamente al click) -->
        <div class="modal fade" id="lineModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content" id="lineModalContent">
                    <div class="line-modal-header modal-header">
                        <h5 class="modal-title">
                            <span class="line-modal-icon"><i class="fa-solid fa-layer-group" id="lineModalIcon"></i></span>
                            <span id="lineModalTitle">Línea temática</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body line-modal-body">
                        <div class="line-modal-intro"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span id="lineModalIntro"></span></div>
                        <h6 id="lineModalConsultaHeading" style="display:none"><i class="fa-solid fa-magnifying-glass-chart" aria-hidden="true"></i> Información que se puede consultar</h6>
                        <ul id="lineModalConsulta" style="display:none"></ul>
                        <h6 id="lineModalFuentesHeading" style="display:none"><i class="fa-solid fa-database" aria-hidden="true"></i> Fuentes de información</h6>
                        <ul id="lineModalFuentes" style="display:none"></ul>
                        <div id="lineModalPeriodicidadWrap" class="mt-3" style="display:none">
                            <h6><i class="fa-solid fa-calendar-days" aria-hidden="true"></i> Periodicidad</h6>
                            <span class="badge-periodo" id="lineModalPeriodicidad"></span>
                        </div>
                    </div>
                    <div class="line-modal-footer modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                        <a href="#" id="lineModalGoCategorias" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-list me-1"></i> Ver indicadores en Categorías
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function(){
            // Diccionario con la info por línea (preparada en PHP)
            var LINES_DATA = <?= json_encode(array_merge(
                ...array_map(function($cat) use ($linesInfo, $lineCounts) {
                    $info = $linesInfo[$cat] ?? [];
                    return [mb_strtolower($cat) => [
                        'title'        => $cat,
                        'intro'        => $info['intro'] ?? 'Línea temática del observatorio. Explora los indicadores asociados desde la pestaña Categorías.',
                        'consulta'     => $info['consulta'] ?? [],
                        'fuentes'      => $info['fuentes'] ?? [],
                        'periodicidad' => $info['periodicidad'] ?? '',
                        'count'        => $lineCounts[$cat] ?? 0,
                    ]];
                }, $lineKeys)
            ), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?: '{}' ?>;

            var modalEl = document.getElementById('lineModal');
            var contentEl = document.getElementById('lineModalContent');
            var btns = document.querySelectorAll('.line-card, .side-line-card');

            function setList(ulEl, headingEl, items){
                if (!items || !items.length){ ulEl.style.display='none'; headingEl.style.display='none'; return; }
                ulEl.innerHTML = items.map(function(i){return '<li>' + i + '</li>';}).join('');
                ulEl.style.display = ''; headingEl.style.display = '';
            }

            btns.forEach(function(b){
                b.addEventListener('click', function(){
                    var key = this.dataset.lineCat;
                    var d = LINES_DATA[key];
                    if (!d) return;
                    // Color e icono propios
                    var c = getComputedStyle(this).getPropertyValue('--c').trim();
                    var cRgb = getComputedStyle(this).getPropertyValue('--c-rgb').trim();
                    contentEl.style.setProperty('--c', c);
                    contentEl.style.setProperty('--c-rgb', cRgb);
                    // Icono del header copia el icono de la tarjeta
                    var sourceIcon = this.querySelector('.line-card__icon i, .side-line-card__icon i');
                    document.getElementById('lineModalIcon').className = sourceIcon ? sourceIcon.className : 'fa-solid fa-layer-group';
                    // Texto
                    document.getElementById('lineModalTitle').textContent = d.title + ' (' + d.count + ')';
                    document.getElementById('lineModalIntro').textContent = d.intro;
                    setList(document.getElementById('lineModalConsulta'), document.getElementById('lineModalConsultaHeading'), d.consulta);
                    setList(document.getElementById('lineModalFuentes'), document.getElementById('lineModalFuentesHeading'), d.fuentes);
                    if (d.periodicidad){
                        document.getElementById('lineModalPeriodicidad').textContent = d.periodicidad;
                        document.getElementById('lineModalPeriodicidadWrap').style.display = '';
                    } else {
                        document.getElementById('lineModalPeriodicidadWrap').style.display = 'none';
                    }
                    // Botón "Ver indicadores en Categorías": atajo a la parte inferior.
                    // El desplazamiento se hace SOLO cuando el modal terminó de cerrarse
                    // (hidden.bs.modal); si se hace antes, Bootstrap restaura el scroll
                    // del body al cerrar y la página "rebota" hacia arriba.
                    var go = document.getElementById('lineModalGoCategorias');
                    go.onclick = function(e){
                        e.preventDefault();
                        if (document.activeElement) document.activeElement.blur();
                        modalEl.addEventListener('hidden.bs.modal', function once(){
                            modalEl.removeEventListener('hidden.bs.modal', once);
                            var tabBtn = document.querySelector('[data-bs-target="#p-categorias"]');
                            if (tabBtn) new bootstrap.Tab(tabBtn).show();
                            var catBtn = document.querySelector('.cat-filters .cat-btn[data-cat="' + key + '"]');
                            if (catBtn) catBtn.click();
                            var tabsPanel = document.getElementById('tablero');
                            if (tabsPanel) {
                                tabsPanel.setAttribute('tabindex', '-1');
                                tabsPanel.focus({ preventScroll: true });
                                requestAnimationFrame(function(){
                                    tabsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                });
                                // Re-anclar al terminar la animación: las imágenes que cargan
                                // tarde mueven el layout y desplazan el objetivo.
                                setTimeout(function(){
                                    tabsPanel.scrollIntoView({ block: 'start' });
                                }, 1400);
                            }
                        });
                        bootstrap.Modal.getInstance(modalEl).hide();
                    };
                });
            });
        })();
        </script>
    </section>
    <?php endif; ?>

    <?php
    /* Pestañas secundarias (consulta/documentación): van como lista vertical a la
       derecha en lugar de la barra horizontal, para no saturar de botones. */
    $exploraSecundarias = [];
    foreach ($msTabsRest as $msRootS) {
        $exploraSecundarias[] = [
            'pane' => '#' . cms_section_pane_id($slug, $msRootS),
            'title' => (string) $msRootS['title'],
            'icon' => !empty($msRootS['icon']) ? (string) $msRootS['icon'] : 'fa-book-open',
        ];
    }
    $exploraSecundarias[] = ['pane' => '#p-pub-uni', 'title' => 'Publicaciones universidades', 'icon' => 'fa-graduation-cap'];
    ?>
    <section class="tabs-panel mb-4" id="tablero">
        <h2>Explora el observatorio</h2>
        <style>
            .explora-side{background:#fff;border:1px solid #e8edf5;border-radius:16px;padding:1rem 1.05rem;box-shadow:0 4px 14px rgba(0,0,0,.05)}
            .explora-side>h3{font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;font-weight:700;margin:0 0 .8rem;display:flex;align-items:center;gap:.5rem}
            .explora-side>h3 i{color:var(--obs-color,#0d6efd)}
            .explora-side-btn{display:flex;align-items:center;gap:.65rem;width:100%;text-align:left;background:#fff;border:1px solid rgba(var(--obs-color-rgb,13,110,253),.22);border-radius:12px;padding:.6rem .75rem;margin-bottom:.5rem;cursor:pointer;font-size:.85rem;font-weight:600;color:#1f2937;transition:transform .15s ease,box-shadow .15s ease,background-color .15s ease}
            .explora-side-btn:hover{transform:translateX(3px);box-shadow:0 6px 14px rgba(var(--obs-color-rgb,13,110,253),.2)}
            .explora-side-btn:last-child{margin-bottom:0}
            .explora-side-btn i:first-child{width:34px;height:34px;border-radius:10px;background:rgba(var(--obs-color-rgb,13,110,253),.1);color:var(--obs-color,#0d6efd);display:inline-flex;align-items:center;justify-content:center;font-size:.9rem;flex:0 0 auto}
            .explora-side-btn .explora-side-arrow{margin-left:auto;color:var(--obs-color,#0d6efd);font-size:.75rem}
            .explora-side-btn.active{background:var(--obs-color,#0d6efd);border-color:var(--obs-color,#0d6efd);color:#fff}
            .explora-side-btn.active i:first-child{background:rgba(255,255,255,.22);color:#fff}
            .explora-side-btn.active .explora-side-arrow{color:#fff}
        </style>
        <div class="row g-3 align-items-start">
        <div class="<?= $exploraSecundarias !== [] ? 'col-lg-9' : 'col-12' ?>">
        <ul class="nav nav-pills mb-3 flex-wrap gap-1" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#p-tablero" type="button">Tablero de datos</button></li>
            <?php if ($genderMode): ?>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-ruta-atencion" type="button">Ruta de atención</button></li>
            <?php endif; ?>
            <?php foreach ($msTabsFirst as $msRoot):
                $msPaneId = cms_section_pane_id($slug, $msRoot);
            ?>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#<?= htmlspecialchars($msPaneId) ?>" type="button">
                        <?php if (!empty($msRoot['icon'])): ?><i class="fa-solid <?= htmlspecialchars((string) $msRoot['icon']) ?> me-1"></i><?php endif; ?>
                        <?= htmlspecialchars((string) $msRoot['title']) ?>
                    </button>
                </li>
            <?php endforeach; ?>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-hojavida" type="button">Hoja de vida</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-categorias" type="button">Categorías</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-descargas" type="button">Descarga de datos</button></li>
            <li class="nav-item d-none"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-pub-uni" type="button">Publicaciones universidades</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-infografias" type="button">Datos e infografías</button></li>
            <?php if ($genderMode): ?>
                <?php foreach ($genderExtraTabs as $extra): ?>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#p-g-<?= htmlspecialchars($extra['id']) ?>" type="button"><?= htmlspecialchars($extra['label']) ?></button></li>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php /* Secundarias: ocultas en la barra (se activan desde la lista vertical) */ ?>
            <?php foreach ($msTabsRest as $msRoot):
                $msPaneId = cms_section_pane_id($slug, $msRoot);
            ?>
                <li class="nav-item d-none">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#<?= htmlspecialchars($msPaneId) ?>" type="button">
                        <?= htmlspecialchars((string) $msRoot['title']) ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="p-tablero">
                <?php if (!$genderMode): /* Género usa solo los tableros Power BI */ ?>
                <article class="content-card">
                    <h3>Tablero de datos</h3>
                    <?php if ($pbiUrl !== ''): ?>
                        <p class="small text-muted mb-2">Tablero oficial en Power BI de la <?= htmlspecialchars($obs['name']) ?>.</p>
                        <div class="obs-tablero-embed-wrap">
                            <iframe class="obs-tablero-iframe" title="Tablero Power BI · <?= htmlspecialchars($obs['name']) ?>" src="<?= htmlspecialchars($pbiUrl) ?>" loading="lazy" allowfullscreen style="min-height:600px"></iframe>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2 align-items-center">
                            <a class="btn btn-dark btn-sm" href="<?= htmlspecialchars($pbiUrl) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square me-1" aria-hidden="true"></i> Abrir en pestaña nueva</a>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="fa-solid fa-screwdriver-wrench mb-2" style="font-size:1.6rem;opacity:.5;display:block"></i>
                            El tablero de datos de esta dimensión estará disponible próximamente.
                        </div>
                    <?php endif; ?>
                </article>
                <?php endif; ?>

                <?php if ($genderMode): ?>
                <?php
                $pbiTableros = [
                    ['title' => 'Indicadores Asuntos de Género', 'desc' => 'Tablero principal con los indicadores del observatorio.', 'url' => 'https://app.powerbi.com/view?r=eyJrIjoiNjFlYTAwMWItOGY1MC00NzAzLWIyNTYtNDg3YzJjNmU1NmQ3IiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=ReportSection'],
                    ['title' => 'Política Pública Mujer y Género', 'desc' => 'Indicadores asociados a la política pública de mujer y género.', 'url' => 'https://app.powerbi.com/view?r=eyJrIjoiYmE3YzFmNmQtNTcwMy00NGVmLTk1NWYtNTI1ZDBhYTU2ZDRlIiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9'],
                    ['title' => 'Política Pública Mujer Campesina y Rural', 'desc' => 'Indicadores de la política pública de mujer campesina y rural.', 'url' => 'https://app.powerbi.com/view?r=eyJrIjoiZGRjNmJkMzctNjE2ZC00OTg2LTk3ZWMtYTc2YTQ4M2Y5N2U4IiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9'],
                    ['title' => 'Política Pública Familia', 'desc' => 'Indicadores seleccionados asociados al observatorio.', 'url' => 'https://app.powerbi.com/view?r=eyJrIjoiMzJhMTZjNTktNDM1Yy00YmRmLWJhMDUtZGRjYzhiZDcyM2E4IiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=6d7e0fcb4b49936a08d6'],
                    ['title' => 'Política Pública Infancia y Adolescencia', 'desc' => 'Indicadores seleccionados asociados al observatorio.', 'url' => 'https://app.powerbi.com/view?r=eyJrIjoiMzU3NzQwOTktNTI5Mi00NWFlLTk2MzItZTIxYzJiYmQ5MWJmIiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=ca6a0c64cd07c4268d7e'],
                ];
                ?>
                <article class="content-card mt-3" id="pbiTablerosCard">
                    <h3>Tableros Power BI</h3>
                    <p class="small text-muted mb-2">Tableros interactivos del observatorio. Seleccione uno para visualizarlo; también puede abrirlo en pantalla completa.</p>
                    <div class="d-flex flex-wrap gap-2 mb-3" id="pbiTabs">
                        <?php foreach ($pbiTableros as $i => $tb): ?>
                            <button type="button" class="btn btn-sm <?= $i === 0 ? 'btn-dark' : 'btn-outline-secondary' ?> pbi-tab-btn" data-pbi-url="<?= htmlspecialchars($tb['url']) ?>" data-pbi-title="<?= htmlspecialchars($tb['title']) ?>" title="<?= htmlspecialchars($tb['desc']) ?>">
                                <i class="fa-solid fa-chart-pie me-1" aria-hidden="true"></i><?= htmlspecialchars($tb['title']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="obs-tablero-embed-wrap" style="position:relative">
                        <div id="pbiPlaceholder" class="d-flex flex-column align-items-center justify-content-center text-muted" style="min-height:320px;border:1px dashed #d8dee9;border-radius:12px;gap:.5rem">
                            <div class="spinner-border" role="status" style="opacity:.45"><span class="visually-hidden">Cargando…</span></div>
                            <span>Cargando tablero… los tableros Power BI requieren conexión a internet</span>
                        </div>
                        <iframe id="pbiFrame" class="obs-tablero-iframe d-none" title="Tablero Power BI" allowfullscreen style="min-height:480px"></iframe>
                    </div>
                    <div class="mt-2">
                        <a id="pbiOpenNew" class="btn btn-outline-secondary btn-sm d-none" href="#" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square me-1" aria-hidden="true"></i>Abrir en pestaña nueva</a>
                    </div>
                    <script>
                        (function () {
                            var frame = document.getElementById('pbiFrame');
                            var ph = document.getElementById('pbiPlaceholder');
                            var openNew = document.getElementById('pbiOpenNew');

                            function cargarTablero(btn) {
                                if (!btn) return;
                                document.querySelectorAll('#pbiTabs .pbi-tab-btn').forEach(function (b) {
                                    b.classList.remove('btn-dark'); b.classList.add('btn-outline-secondary');
                                });
                                btn.classList.add('btn-dark'); btn.classList.remove('btn-outline-secondary');
                                frame.src = btn.getAttribute('data-pbi-url');
                                frame.title = 'Tablero Power BI — ' + btn.getAttribute('data-pbi-title');
                                frame.classList.remove('d-none');
                                ph.classList.add('d-none');
                                openNew.href = btn.getAttribute('data-pbi-url');
                                openNew.classList.remove('d-none');
                            }

                            document.getElementById('pbiTabs').addEventListener('click', function (e) {
                                cargarTablero(e.target.closest('.pbi-tab-btn'));
                            });

                            // Previsualización automática: carga el primer tablero cuando la
                            // sección entra en pantalla (no pesa en la carga inicial de la página).
                            var card = document.getElementById('pbiTablerosCard');
                            if ('IntersectionObserver' in window) {
                                var io = new IntersectionObserver(function (entries) {
                                    entries.forEach(function (en) {
                                        if (en.isIntersecting && !frame.src) {
                                            cargarTablero(document.querySelector('#pbiTabs .pbi-tab-btn'));
                                            io.disconnect();
                                        }
                                    });
                                }, { threshold: 0.15 });
                                io.observe(card);
                            }
                            // Respaldo: si tras unos segundos no se ha cargado ninguno
                            // (p. ej. sin IntersectionObserver), carga el primero.
                            setTimeout(function () {
                                if (!frame.src) cargarTablero(document.querySelector('#pbiTabs .pbi-tab-btn'));
                            }, 5000);
                        })();
                    </script>
                </article>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="p-hojavida">
                <article class="content-card" id="hojavida">
                    <h3>Hoja de vida de indicadores</h3>
                    <p class="small text-muted">Definición, metodología, fórmula, periodicidad, cobertura y fuentes de los <?= count($imIndicators) ?> indicadores de este observatorio.</p>
                    <?php if (!$imIndicators): ?>
                        <div class="alert alert-warning small">Aún no se han cargado hojas de vida. Importe el CSV en <a href="cms/indicators.php">CMS · Indicadores</a>.</div>
                    <?php else: ?>
                        <?php
                        // Recolectar categorías de 2° orden con conteo para los pills de filtro
                        $hvCat2Counts = [];
                        foreach ($imIndicators as $r0) {
                            $c2 = trim((string) ($r0['category_2'] ?? ''));
                            if ($c2 === '' || strcasecmp($c2, 'ND') === 0) {
                                $c2 = 'Sin categoría';
                            }
                            $hvCat2Counts[$c2] = ($hvCat2Counts[$c2] ?? 0) + 1;
                        }
                        ksort($hvCat2Counts, SORT_NATURAL | SORT_FLAG_CASE);
                        ?>
                        <style>
                            /* ── Pills de filtro por categoría ─────────────────── */
                            .hv-cat-filters{ --pill-radius: 999px; }
                            .hv-cat-filters .hv-cat-btn{
                                --c: var(--cat-color, #0d6efd);
                                --c-rgb: var(--cat-color-rgb, 13,110,253);
                                border: 1.5px solid var(--c);
                                background: rgba(var(--c-rgb), .10);
                                color: var(--c);
                                border-radius: var(--pill-radius);
                                padding: .45rem 1rem;
                                font-weight: 600;
                                font-size: .82rem;
                                display: inline-flex;
                                align-items: center;
                                gap: .55rem;
                                transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease, color .15s ease;
                                box-shadow: 0 1px 3px rgba(var(--c-rgb), .12);
                            }
                            .hv-cat-filters .hv-cat-btn:hover{
                                transform: translateY(-2px);
                                background: rgba(var(--c-rgb), .18);
                                box-shadow: 0 6px 14px rgba(var(--c-rgb), .28);
                            }
                            .hv-cat-filters .hv-cat-btn.active{
                                background: linear-gradient(135deg, var(--c) 0%, rgba(var(--c-rgb), .82) 100%);
                                color: #fff;
                                border-color: var(--c);
                                box-shadow: 0 6px 16px rgba(var(--c-rgb), .42);
                            }
                            .hv-cat-filters .hv-cat-btn .hv-icon{
                                width: 24px; height: 24px;
                                display: inline-flex; align-items:center; justify-content:center;
                                border-radius: 50%;
                                background: rgba(var(--c-rgb), .22);
                                color: var(--c);
                                font-size: .78rem;
                                transition: background-color .15s ease, color .15s ease;
                            }
                            .hv-cat-filters .hv-cat-btn.active .hv-icon{
                                background: rgba(255,255,255,.28);
                                color: #fff;
                            }
                            .hv-cat-filters .hv-cat-btn .badge{
                                font-weight: 700;
                                font-size: .70rem;
                                background: rgba(var(--c-rgb), .18) !important;
                                color: var(--c) !important;
                                border-radius: 999px;
                                padding: .15rem .55rem;
                            }
                            .hv-cat-filters .hv-cat-btn.active .badge{
                                background: rgba(255,255,255,.30) !important;
                                color: #fff !important;
                            }
                            /* ── Acordeón ──────────────────────────────────────── */
                            #hvAccordion .accordion-item{
                                --c: var(--cat-color, var(--obs-color, #0d6efd));
                                --c-rgb: var(--cat-color-rgb, 13,110,253);
                                border-radius: 14px !important;
                                margin-bottom: .55rem;
                                border: 1px solid rgba(var(--c-rgb), .25);
                                overflow: hidden;
                                box-shadow: 0 1px 3px rgba(0,0,0,.04);
                                transition: box-shadow .2s ease, transform .2s ease;
                            }
                            #hvAccordion .accordion-item:hover{
                                box-shadow: 0 6px 18px rgba(var(--c-rgb), .15);
                                transform: translateY(-1px);
                            }
                            #hvAccordion .accordion-button{
                                border-radius: 14px !important;
                                gap: .7rem;
                                background: linear-gradient(90deg, rgba(var(--c-rgb), .06) 0%, rgba(var(--c-rgb), 0) 100%);
                            }
                            #hvAccordion .accordion-button:not(.collapsed){
                                background: linear-gradient(135deg, rgba(var(--c-rgb), .14) 0%, rgba(var(--c-rgb), .06) 100%);
                                color: inherit;
                                box-shadow: none;
                            }
                            #hvAccordion .hv-item-icon{
                                width: 38px; height: 38px;
                                display: inline-flex; align-items:center; justify-content:center;
                                border-radius: 12px;
                                background: linear-gradient(135deg, rgba(var(--c-rgb), .22) 0%, rgba(var(--c-rgb), .12) 100%);
                                color: var(--c);
                                flex: 0 0 auto;
                                font-size: 1.05rem;
                                box-shadow: inset 0 0 0 1px rgba(var(--c-rgb), .18);
                            }
                            /* ── Pestañas principales: realzar la activa con el color del observatorio (color plano) ── */
                            .observatorio-page .nav-pills .nav-link{
                                font-weight: 600;
                                border-radius: 999px;
                                padding: .55rem 1.2rem;
                                background: rgba(0,0,0,.03);
                                border: 1px solid rgba(0,0,0,.08);
                                transition: transform .15s ease, box-shadow .15s ease;
                            }
                            .observatorio-page .nav-pills .nav-link:hover{
                                transform: translateY(-1px);
                            }
                            .observatorio-page .nav-pills .nav-link.active{
                                background: var(--obs-tab-active, var(--obs-color, #0d6efd)) !important;
                                color: #fff !important;
                                border-color: transparent;
                                box-shadow: 0 4px 12px rgba(0,0,0,.18);
                            }
                        </style>
                        <?php
                        $todasColor = $obs['accent'] ?? '#0d6efd';
                        $todasRgb   = obs_hex_to_rgb($todasColor);
                        ?>
                        <div class="hv-cat-filters mb-3 d-flex flex-wrap gap-2">
                            <button type="button" class="hv-cat-btn active" data-cat=""
                                    style="--cat-color: <?= htmlspecialchars($todasColor) ?>; --cat-color-rgb: <?= htmlspecialchars($todasRgb) ?>;">
                                <span class="hv-icon"><i class="fa-solid fa-grip"></i></span>
                                Todas <span class="badge ms-1"><?= count($imIndicators) ?></span>
                            </button>
                            <?php foreach ($hvCat2Counts as $c2 => $n):
                                $iconCls = obs_category_icon($c2);
                                $cColor  = obs_category_color($c2);
                                $cRgb    = obs_hex_to_rgb($cColor);
                            ?>
                                <button type="button" class="hv-cat-btn" data-cat="<?= htmlspecialchars(mb_strtolower($c2)) ?>"
                                        style="--cat-color: <?= htmlspecialchars($cColor) ?>; --cat-color-rgb: <?= htmlspecialchars($cRgb) ?>;">
                                    <span class="hv-icon"><i class="fa-solid <?= $iconCls ?>"></i></span>
                                    <?= htmlspecialchars($c2) ?> <span class="badge ms-1"><?= $n ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="mb-2 d-flex gap-2 flex-wrap align-items-center">
                            <input type="search" id="hvSearch" class="form-control form-control-sm" style="max-width:320px" placeholder="Buscar por nombre o código...">
                            <span class="small text-muted" id="hvCount"><?= count($imIndicators) ?> indicadores</span>
                        </div>
                        <div class="accordion" id="hvAccordion">
                        <?php foreach ($imIndicators as $ix => $r):
                            $nid = (int) $r['id'];
                            $itemCat2 = trim((string) ($r['category_2'] ?? ''));
                            if ($itemCat2 === '' || strcasecmp($itemCat2, 'ND') === 0) {
                                $itemCat2 = 'Sin categoría';
                            }
                            $itemColor = obs_category_color($itemCat2);
                            $itemRgb   = obs_hex_to_rgb($itemColor);
                        ?>
                            <div class="accordion-item hv-item"
                                 data-title="<?= htmlspecialchars(mb_strtolower($r['title'] . ' ' . $nid)) ?>"
                                 data-cat="<?= htmlspecialchars(mb_strtolower($itemCat2)) ?>"
                                 style="--cat-color: <?= htmlspecialchars($itemColor) ?>; --cat-color-rgb: <?= htmlspecialchars($itemRgb) ?>;">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hv<?= $nid ?>">
                                        <span class="hv-item-icon" title="<?= htmlspecialchars($itemCat2) ?>">
                                            <i class="fa-solid <?= obs_category_icon($itemCat2) ?>"></i>
                                        </span>
                                        <strong class="me-2">#<?= $nid ?></strong>
                                        <span><?= htmlspecialchars($r['title']) ?></span>
                                        <?php if (($r['availability_status'] ?? '') === 'DISPONIBLE'): ?>
                                            <span class="badge bg-success ms-auto me-2">Disponible</span>
                                        <?php elseif (!empty($r['availability_status'])): ?>
                                            <span class="badge bg-secondary ms-auto me-2"><?= htmlspecialchars($r['availability_status']) ?></span>
                                        <?php endif; ?>
                                    </button>
                                </h2>
                                <div id="hv<?= $nid ?>" class="accordion-collapse collapse" data-bs-parent="#hvAccordion">
                                    <div class="accordion-body small">
                                        <div class="row g-2">
                                            <?php
                                            $fields = [
                                                'Categoría 1er orden' => $r['category_1'] ?? '',
                                                'Categoría 2do orden' => $r['category_2'] ?? '',
                                                'Etiquetas' => $r['tags'] ?? '',
                                                'Unidad de medida' => $r['unit'] ?? '',
                                                'Periodicidad' => $r['periodicity'] ?? '',
                                                'Fecha línea base' => $r['baseline_date'] ?? '',
                                                'Desagregación temática' => $r['thematic_breakdown'] ?? '',
                                                'Desagregación geográfica' => $r['geographic_breakdown'] ?? '',
                                                'Definición' => $r['definition'] ?? '',
                                                'Cálculo / fórmula' => $r['calculation_formula'] ?? '',
                                                'Forma de entrega' => $r['delivery_form'] ?? '',
                                                'Fuentes' => $r['source'] ?? '',
                                                'Entidad responsable' => $r['responsible_entity'] ?? '',
                                                'Actores involucrados' => $r['actors'] ?? '',
                                                'Observaciones' => $r['observations'] ?? '',
                                            ];
                                            foreach ($fields as $lbl => $val):
                                                if ($val === null || trim((string) $val) === '') continue; ?>
                                                <div class="col-md-6"><strong><?= $lbl ?>:</strong><br><span class="text-muted"><?= nl2br(htmlspecialchars((string) $val)) ?></span></div>
                                            <?php endforeach; ?>
                                            <?php if (!empty($r['source_link'])): ?>
                                                <div class="col-md-12"><strong>Enlace fuente:</strong> <a href="<?= htmlspecialchars($r['source_link']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($r['source_link']) ?></a></div>
                                            <?php endif; ?>
                                            <div class="col-md-12 mt-2">
                                                <a class="btn btn-sm btn-outline-primary" href="indicador.php?id=<?= $nid ?>"><i class="fa-solid fa-chart-column"></i> Ver gráficos del indicador</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        <script>
                        (function(){
                            var search   = document.getElementById('hvSearch');
                            var counter  = document.getElementById('hvCount');
                            var items    = document.querySelectorAll('#hvAccordion .hv-item');
                            var catBtns  = document.querySelectorAll('.hv-cat-btn');
                            var activeCat = '';

                            function applyFilter(){
                                var q = search ? search.value.toLowerCase().trim() : '';
                                var n = 0;
                                items.forEach(function(it){
                                    var matchTitle = !q || it.dataset.title.indexOf(q) !== -1;
                                    var matchCat   = !activeCat || it.dataset.cat === activeCat;
                                    var ok = matchTitle && matchCat;
                                    it.style.display = ok ? '' : 'none';
                                    if (ok) n++;
                                });
                                if (counter) counter.textContent = n + ' indicadores';
                            }

                            if (search) search.addEventListener('input', applyFilter);
                            catBtns.forEach(function(b){
                                b.addEventListener('click', function(){
                                    activeCat = this.dataset.cat || '';
                                    catBtns.forEach(function(x){
                                        x.classList.toggle('active', x === b);
                                    });
                                    // Cerrar acordeones abiertos al cambiar de filtro para que no queden mostrando elementos ocultos
                                    document.querySelectorAll('#hvAccordion .accordion-collapse.show').forEach(function(el){
                                        el.classList.remove('show');
                                        var btn = document.querySelector('[data-bs-target="#' + el.id + '"]');
                                        if (btn) btn.classList.add('collapsed');
                                    });
                                    applyFilter();
                                });
                            });
                        })();
                        </script>
                    <?php endif; ?>
                </article>
            </div>
            <div class="tab-pane fade" id="p-categorias">
                <article class="content-card" id="categorias">
                    <h3>Categorías temáticas</h3>
                    <?php if (!$imIndicators): ?>
                        <p class="text-muted small">No hay indicadores categorizados aún.</p>
                        <div class="cards-grid">
                            <div class="mini-card">Salud / Bienestar</div>
                            <div class="mini-card">Economía / Mercado</div>
                            <div class="mini-card">Territorio / Ambiente</div>
                            <div class="mini-card">Innovación / Capacidades</div>
                        </div>
                    <?php else:
                        // Agrupar todo por categoría 2° orden (la categoría 1° suele coincidir con la dimensión)
                        $catGroups = [];
                        foreach ($imIndicators as $r0) {
                            $c2 = trim((string) ($r0['category_2'] ?? ''));
                            if ($c2 === '' || strcasecmp($c2, 'ND') === 0) $c2 = 'Sin categoría';
                            $catGroups[$c2][] = $r0;
                        }
                        ksort($catGroups, SORT_NATURAL | SORT_FLAG_CASE);
                        $todasColorCat = $obs['accent'] ?? '#0d6efd';
                        $todasRgbCat   = obs_hex_to_rgb($todasColorCat);
                    ?>
                        <p class="small text-muted">Indicadores agrupados por categoría temática. Filtra con los botones o busca por nombre/código.</p>
                        <style>
                            /* Pills (mismos estilos visuales que en Hoja de vida) */
                            .cat-filters .cat-btn{
                                --c: var(--cat-color, #0d6efd);
                                --c-rgb: var(--cat-color-rgb, 13,110,253);
                                border: 1.5px solid var(--c);
                                background: rgba(var(--c-rgb), .10);
                                color: var(--c);
                                border-radius: 999px;
                                padding: .42rem 1rem;
                                font-weight: 600;
                                font-size: .80rem;
                                display: inline-flex;
                                align-items: center;
                                gap: .55rem;
                                transition: all .15s ease;
                                box-shadow: 0 1px 3px rgba(var(--c-rgb), .12);
                            }
                            .cat-filters .cat-btn:hover{
                                transform: translateY(-2px);
                                background: rgba(var(--c-rgb), .18);
                                box-shadow: 0 6px 14px rgba(var(--c-rgb), .26);
                            }
                            .cat-filters .cat-btn.active{
                                background: var(--c);
                                color: #fff;
                                box-shadow: 0 4px 12px rgba(var(--c-rgb), .42);
                            }
                            .cat-filters .cat-btn .cat-pill-icon{
                                width: 22px; height: 22px;
                                display: inline-flex; align-items:center; justify-content:center;
                                border-radius: 50%;
                                background: rgba(var(--c-rgb), .22);
                                color: var(--c);
                                font-size: .72rem;
                            }
                            .cat-filters .cat-btn.active .cat-pill-icon{ background: rgba(255,255,255,.28); color: #fff; }
                            .cat-filters .cat-btn .badge{
                                font-weight: 700; font-size: .68rem;
                                background: rgba(var(--c-rgb), .18) !important;
                                color: var(--c) !important;
                                border-radius: 999px;
                                padding: .12rem .5rem;
                            }
                            .cat-filters .cat-btn.active .badge{ background: rgba(255,255,255,.30) !important; color: #fff !important; }

                            /* Secciones por categoría */
                            .cat-section{
                                --c: var(--cat-color, #0d6efd);
                                --c-rgb: var(--cat-color-rgb, 13,110,253);
                                border-radius: 14px;
                                padding: 16px 18px;
                                margin-bottom: 16px;
                                border: 1px solid rgba(var(--c-rgb), .22);
                                background: linear-gradient(180deg, rgba(var(--c-rgb), .055) 0%, rgba(var(--c-rgb), .015) 100%);
                                transition: box-shadow .2s ease;
                            }
                            .cat-section:hover{ box-shadow: 0 4px 14px rgba(var(--c-rgb), .12); }
                            .cat-section .cat-section-head{
                                display: flex; align-items: center; gap: .65rem;
                                margin-bottom: 12px;
                                color: var(--c);
                            }
                            .cat-section .cat-icon-badge{
                                width: 36px; height: 36px;
                                border-radius: 12px;
                                background: linear-gradient(135deg, rgba(var(--c-rgb), .28) 0%, rgba(var(--c-rgb), .12) 100%);
                                color: var(--c);
                                display: inline-flex; align-items: center; justify-content: center;
                                font-size: 1rem;
                                box-shadow: inset 0 0 0 1px rgba(var(--c-rgb), .18);
                                flex: 0 0 auto;
                            }
                            .cat-section .cat-section-title{ font-weight: 700; font-size: 1rem; margin: 0; }
                            .cat-section .cat-section-count{
                                font-weight: 700; font-size: .72rem;
                                background: rgba(var(--c-rgb), .20);
                                color: var(--c);
                                border-radius: 999px;
                                padding: .12rem .6rem;
                            }

                            /* Tarjetas de indicador */
                            .cat-card{
                                background: #fff;
                                border: 1px solid rgba(var(--c-rgb), .25);
                                border-left: 3px solid var(--c);
                                border-radius: 8px;
                                padding: .55rem .75rem;
                                display: flex;
                                flex-direction: column;
                                gap: .15rem;
                                text-decoration: none;
                                color: #2f3b50;
                                font-size: .82rem;
                                line-height: 1.35;
                                transition: all .15s ease;
                                height: 100%;
                            }
                            .cat-card:hover{
                                transform: translateY(-2px);
                                box-shadow: 0 6px 14px rgba(var(--c-rgb), .22);
                                border-color: var(--c);
                                color: var(--c);
                            }
                            .cat-card .cat-card-id{
                                color: var(--c);
                                font-weight: 700;
                                font-size: .72rem;
                                font-family: 'SFMono-Regular', Consolas, monospace;
                                letter-spacing: .02em;
                            }
                        </style>

                        <div class="cat-filters d-flex flex-wrap gap-2 mb-3">
                            <?php foreach ($catGroups as $c2 => $list):
                                $cColorPill = obs_category_color($c2);
                                $cRgbPill   = obs_hex_to_rgb($cColorPill);
                                $cIconPill  = obs_category_icon($c2);
                            ?>
                                <button type="button" class="cat-btn" data-cat="<?= htmlspecialchars(mb_strtolower($c2)) ?>"
                                        style="--cat-color: <?= htmlspecialchars($cColorPill) ?>; --cat-color-rgb: <?= htmlspecialchars($cRgbPill) ?>;">
                                    <span class="cat-pill-icon"><i class="fa-solid <?= $cIconPill ?>"></i></span>
                                    <?= htmlspecialchars($c2) ?> <span class="badge ms-1"><?= count($list) ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <div id="catEmptyHint" class="text-center py-4 text-muted small">
                            <i class="fa-solid fa-hand-pointer me-1"></i> Selecciona una categoría arriba para ver sus indicadores.
                        </div>

                        <div id="catSearchBar" class="d-flex gap-2 flex-wrap align-items-center mb-3" style="display:none !important">
                            <input type="search" id="catSearch" class="form-control form-control-sm" style="max-width:320px" placeholder="Buscar dentro de la categoría…">
                            <span class="small text-muted" id="catCount">0 indicadores</span>
                        </div>

                        <?php foreach ($catGroups as $c2 => $list):
                            $cColor = obs_category_color($c2);
                            $cRgb   = obs_hex_to_rgb($cColor);
                            $cIcon  = obs_category_icon($c2);
                            $catKey = htmlspecialchars(mb_strtolower($c2));
                        ?>
                            <section class="cat-section" data-cat="<?= $catKey ?>"
                                     style="display:none; --cat-color: <?= htmlspecialchars($cColor) ?>; --cat-color-rgb: <?= htmlspecialchars($cRgb) ?>;">
                                <header class="cat-section-head">
                                    <span class="cat-icon-badge"><i class="fa-solid <?= $cIcon ?>"></i></span>
                                    <h4 class="cat-section-title"><?= htmlspecialchars($c2) ?></h4>
                                    <span class="cat-section-count"><?= count($list) ?></span>
                                </header>
                                <div class="row g-2">
                                    <?php foreach ($list as $r): $rid = (int) $r['id']; ?>
                                        <div class="col-md-6 col-lg-4 cat-item"
                                             data-title="<?= htmlspecialchars(mb_strtolower($r['title'] . ' ' . $rid)) ?>"
                                             data-cat="<?= $catKey ?>">
                                            <a href="indicador.php?id=<?= $rid ?>" class="cat-card">
                                                <span class="cat-card-id">#<?= $rid ?></span>
                                                <span><?= htmlspecialchars($r['title']) ?></span>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>

                        <script>
                        (function(){
                            var search    = document.getElementById('catSearch');
                            var searchBar = document.getElementById('catSearchBar');
                            var counter   = document.getElementById('catCount');
                            var hint      = document.getElementById('catEmptyHint');
                            var btns      = document.querySelectorAll('.cat-filters .cat-btn');
                            var sections  = document.querySelectorAll('.cat-section');
                            var activeCat = '';

                            function applyFilter(){
                                if (!activeCat){
                                    // Estado inicial: nada seleccionado → solo se ven los pills + hint
                                    sections.forEach(function(sec){ sec.style.display = 'none'; });
                                    if (searchBar) searchBar.style.display = 'none';
                                    if (hint) hint.style.display = '';
                                    return;
                                }
                                if (hint) hint.style.display = 'none';
                                if (searchBar) searchBar.style.display = '';

                                var q = search ? search.value.toLowerCase().trim() : '';
                                var totalVisible = 0;
                                sections.forEach(function(sec){
                                    if (sec.dataset.cat !== activeCat){
                                        sec.style.display = 'none';
                                        return;
                                    }
                                    var visible = 0;
                                    sec.querySelectorAll('.cat-item').forEach(function(it){
                                        var ok = !q || it.dataset.title.indexOf(q) !== -1;
                                        it.style.display = ok ? '' : 'none';
                                        if (ok) visible++;
                                    });
                                    sec.style.display = visible > 0 ? '' : 'none';
                                    totalVisible += visible;
                                });
                                if (counter) counter.textContent = totalVisible + ' indicadores';
                            }

                            if (search) search.addEventListener('input', applyFilter);
                            btns.forEach(function(b){
                                b.addEventListener('click', function(){
                                    var clicked = this.dataset.cat || '';
                                    // Si se vuelve a hacer click en la categoría activa, se cierra (toggle)
                                    if (clicked === activeCat){
                                        activeCat = '';
                                        btns.forEach(function(x){ x.classList.remove('active'); });
                                        if (search) search.value = '';
                                    } else {
                                        activeCat = clicked;
                                        btns.forEach(function(x){ x.classList.toggle('active', x === b); });
                                        if (search) search.value = '';
                                    }
                                    applyFilter();
                                });
                            });

                            // Estado inicial
                            applyFilter();
                        })();
                        </script>
                    <?php endif; ?>
                </article>
            </div>
            <div class="tab-pane fade" id="p-descargas">
                <article class="content-card" id="descargas">
                    <h3>Centro de descargas</h3>
                    <p class="small text-muted">Archivos CSV con los datos de cada indicador (fuente de sus gráficos y mapas).</p>
                    <?php if (!$imDataFiles): ?>
                        <div class="alert alert-warning small">No se encontraron archivos CSV para los indicadores del observatorio. Los datos se leen de <code>website/indicador/<?= $currentObsId ?>XXX/*.csv</code>.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr><th style="width:80px">Código</th><th>Indicador</th><th>Archivos CSV</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($imDataFiles as $d): ?>
                                    <tr>
                                        <td><code>#<?= htmlspecialchars($d['id']) ?></code></td>
                                        <td class="small"><?= htmlspecialchars($d['title']) ?></td>
                                        <td>
                                            <?php foreach ($d['files'] as $f): ?>
                                                <a class="btn btn-sm btn-outline-primary me-1 mb-1" href="indicador/<?= htmlspecialchars($d['id']) ?>/<?= htmlspecialchars($f) ?>" download><i class="fa-solid fa-file-csv"></i> <?= htmlspecialchars($f) ?></a>
                                            <?php endforeach; ?>
                                            <a class="btn btn-sm btn-outline-secondary mb-1" href="indicador.php?id=<?= htmlspecialchars($d['id']) ?>"><i class="fa-solid fa-chart-column"></i> Ver</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="small text-muted mb-0"><strong><?= count($imDataFiles) ?></strong> indicadores con datos descargables.</p>
                    <?php endif; ?>
                    <hr>
                    <p class="small mb-1"><strong>Enlaces adicionales:</strong></p>
                    <ul class="download-list small">
                        <li><a href="estado-observatorio.php">Catálogo general de indicadores</a></li>
                        <li><a href="<?= htmlspecialchars($obs['legacy_url']) ?>">Repositorio legado de esta dimensión</a></li>
                        <li><a href="api/indicators.php">API catálogo JSON</a></li>
                    </ul>
                </article>
            </div>
            <div class="tab-pane fade" id="p-pub-uni">
                <?php require __DIR__ . '/include/publicaciones-uni-tab.php'; ?>
            </div>
            <div class="tab-pane fade" id="p-infografias">
                <?php require __DIR__ . '/include/infografias-tab.php'; ?>
            </div>
            <?php if ($genderMode): ?>
            <div class="tab-pane fade" id="p-ruta-atencion">
                <article class="content-card">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h3 class="mb-1">Ruta de Atención Integral</h3>
                            <p class="text-muted small mb-0">Recorrido interactivo para víctimas de violencias basadas en sexo y/o género: elija el tipo de ruta (comunitaria o institucional), la persona afectada, el tipo de violencia y el contexto, y avance desplazándose por el camino paso a paso.</p>
                        </div>
                        <a class="btn btn-sm btn-dark" href="ruta-atencion-genero.html" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square me-1" aria-hidden="true"></i> Abrir en pantalla completa</a>
                    </div>
                    <iframe id="rutaAtencionFrame" data-src="ruta-atencion-genero.html" title="Ruta de Atención Integral a víctimas de violencias basadas en sexo y/o género" style="width:100%;height:78vh;border:0;border-radius:14px;display:block;background:#1a1d22"></iframe>
                </article>
                <script>
                    // Carga el iframe solo cuando se abre la pestaña (evita cargar la ruta en cada visita al micrositio)
                    (function () {
                        var tabBtn = document.querySelector('[data-bs-target="#p-ruta-atencion"]');
                        if (!tabBtn) return;
                        tabBtn.addEventListener('shown.bs.tab', function () {
                            var f = document.getElementById('rutaAtencionFrame');
                            if (f && !f.src) f.src = f.getAttribute('data-src');
                        });
                    })();
                </script>
            </div>
            <?php endif; ?>
            <?php if ($genderMode): ?>
                <?php foreach ($genderExtraTabs as $extra): ?>
                    <div class="tab-pane fade" id="p-g-<?= htmlspecialchars($extra['id']) ?>">
                        <article class="content-card">
                            <h3><?= htmlspecialchars($extra['title']) ?></h3>
                            <p><?= htmlspecialchars($extra['description']) ?></p>
                            <?php if (!empty($extra['bullets']) && is_array($extra['bullets'])): ?>
                                <ul class="k-list">
                                    <?php foreach ($extra['bullets'] as $bullet): ?>
                                        <li><?= htmlspecialchars($bullet) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php foreach ($msSectionsTree as $msRoot):
                $msPaneId = cms_section_pane_id($slug, $msRoot);
                /* Contención: el body_html del CMS puede traer divs desbalanceados
                   que cerrarían la columna y la fila del layout. Se compensa la
                   diferencia para que el desbalance no escape del panel. */
                $msHtml = cms_render_microsite_section($msRoot, $slug);
                $msOpenDivs = preg_match_all('/<div\b/i', $msHtml);
                $msCloseDivs = preg_match_all('/<\/div>/i', $msHtml);
                if ($msCloseDivs > $msOpenDivs) {
                    $msHtml = str_repeat('<div>', $msCloseDivs - $msOpenDivs) . $msHtml;
                } elseif ($msOpenDivs > $msCloseDivs) {
                    $msHtml .= str_repeat('</div>', $msOpenDivs - $msCloseDivs);
                }
            ?>
                <div class="tab-pane fade" id="<?= htmlspecialchars($msPaneId) ?>">
                    <?= $msHtml ?>
                </div>
            <?php endforeach; ?>
        </div>
        </div><!-- /col principal -->

        <?php if ($exploraSecundarias !== []): ?>
        <aside class="col-lg-3">
            <div class="explora-side">
                <h3><i class="fa-solid fa-book-open" aria-hidden="true"></i> Consulta y documentación</h3>
                <?php foreach ($exploraSecundarias as $sec): ?>
                    <button type="button" class="explora-side-btn" data-pane="<?= htmlspecialchars($sec['pane']) ?>">
                        <i class="fa-solid <?= htmlspecialchars($sec['icon']) ?>" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($sec['title']) ?></span>
                        <i class="fa-solid fa-chevron-right explora-side-arrow" aria-hidden="true"></i>
                    </button>
                <?php endforeach; ?>
            </div>
        </aside>
        <?php endif; ?>
        </div><!-- /row -->

        <script>
        (function () {
            // Lista vertical "Consulta y documentación": activa la pestaña oculta correspondiente
            document.querySelectorAll('.explora-side-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var navBtn = document.querySelector('.tabs-panel .nav-link[data-bs-target="' + btn.getAttribute('data-pane') + '"]');
                    if (navBtn && window.bootstrap) new bootstrap.Tab(navBtn).show();
                });
            });
            // Sincroniza el estado activo entre la barra horizontal y la lista vertical
            document.querySelectorAll('.tabs-panel .nav-link').forEach(function (nl) {
                nl.addEventListener('shown.bs.tab', function () {
                    var target = nl.getAttribute('data-bs-target');
                    document.querySelectorAll('.explora-side-btn').forEach(function (b) {
                        b.classList.toggle('active', b.getAttribute('data-pane') === target);
                    });
                });
            });
        })();
        </script>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-lg-7">
            <article class="content-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2>Noticias y eventos</h2>
                    <a href="noticias.php?obs=<?= htmlspecialchars($slug) ?>">Ver todas</a>
                </div>
                <style>
                    .news-card-modern{display:flex;gap:.85rem;align-items:flex-start;padding:.85rem;border-radius:12px;border:1px solid #eef0f4;transition:all .2s ease;background:#fff;text-decoration:none;color:inherit;margin-bottom:.7rem}
                    .news-card-modern:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.08);border-color:var(--obs-color,#0d6efd);color:inherit}
                    .news-card-modern__img{flex:0 0 110px;width:110px;height:80px;border-radius:8px;object-fit:cover;background:linear-gradient(135deg,var(--obs-color,#0d6efd),var(--obs-accent,#0d6efd))}
                    .news-card-modern__body{flex:1;min-width:0}
                    .news-card-modern__title{font-weight:700;font-size:.95rem;color:#1f2937;margin:0 0 .3rem;line-height:1.3}
                    .news-card-modern__summary{font-size:.82rem;color:#6b7280;line-height:1.4;margin:0 0 .35rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
                    .news-card-modern__meta{font-size:.72rem;color:var(--obs-color,#0d6efd);font-weight:600;display:flex;gap:.5rem;align-items:center}
                    .news-card-modern__meta i{font-size:.7rem}
                </style>
                <div class="news-list" data-server-news="1">
                    <?php foreach ($latestNewsRows as $n): ?>
                        <a href="noticia.php?slug=<?= htmlspecialchars($n['slug'] ?? '') ?>" class="news-card-modern">
                            <?php if (!empty($n['image_url'])): ?>
                                <img class="news-card-modern__img" src="<?= htmlspecialchars($n['image_url']) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <div class="news-card-modern__img d-flex align-items-center justify-content-center" style="color:#fff">
                                    <i class="fa-solid fa-newspaper" style="font-size:1.6rem;opacity:.7"></i>
                                </div>
                            <?php endif; ?>
                            <div class="news-card-modern__body">
                                <h4 class="news-card-modern__title"><?= htmlspecialchars($n['title'] ?? '') ?></h4>
                                <?php if (!empty($n['summary'])): ?>
                                    <p class="news-card-modern__summary"><?= htmlspecialchars($n['summary']) ?></p>
                                <?php endif; ?>
                                <div class="news-card-modern__meta">
                                    <i class="fa-regular fa-calendar"></i>
                                    <span><?= !empty($n['published_at']) ? date('d M Y', strtotime((string) $n['published_at'])) : '—' ?></span>
                                    <?php if (!empty($n['source'])): ?>
                                        <span class="text-muted">·</span>
                                        <span class="text-muted" style="font-weight:500"><?= htmlspecialchars($n['source']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <?php if ($latestNewsRows === []): ?>
                        <p class="text-muted small mb-0">
                            <i class="fa-regular fa-newspaper me-1"></i>
                            No hay noticias publicadas para este observatorio. <a href="cms/news.php">Crear una en el CMS →</a>
                        </p>
                    <?php endif; ?>
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

    <?php if ($genderMode && !$hasMsSections): ?>
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
                <img src="assets/svg/img-genero/barreras/barreraAcceso.png" alt="¿Qué es barrera de acceso?" class="gender-img" loading="lazy" decoding="async">
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
                <img src="assets/svg/img-genero/barreras/derechos.png" alt="Tus derechos" class="gender-img" loading="lazy" decoding="async">
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
                <img src="assets/svg/img-genero/barreras/ejemplos.png" alt="Ejemplos de barreras" class="gender-img" loading="lazy" decoding="async">
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
                <img src="assets/svg/img-genero/barreras/quehacer.png" alt="Qué hacer" class="gender-img" loading="lazy" decoding="async">
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
            <a class="btn btn-dark btn-sm" href="mapa-fondomujer.html">Explorar recursos relacionados</a>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php if ($genderMode) { require __DIR__ . '/include/integrantes-carrusel.php'; } ?>

<?php require __DIR__ . '/include/site-footer.php'; ?>

<?php $surveyContext = $micrositeVisitKey; require __DIR__ . '/include/survey-modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.OBS_SLUG = <?= json_encode($slug) ?>;</script>
<script src="assets/js/modern/microsite-pro.js" defer></script>
<script src="assets/js/survey-widget.js" defer></script>
<script src="assets/js/modern/genero-lightbox.js" defer></script>
<?php if ($genderMode): ?>
<script src="assets/js/modern/genero-content-legacy.js" defer></script>
<?php endif; ?>
<?php require __DIR__ . '/include/assistant-widget.php'; ?>
</body>
</html>
