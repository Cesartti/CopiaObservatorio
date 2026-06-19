<?php
header('Content-Type: text/html; charset=UTF-8');
$observatories = require __DIR__ . '/config/observatories.php';
require_once __DIR__ . '/lib/cms_public_content.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/visit_tracking.php';
$pdoVisit = cms_pdo();
$portalVisitCount = cms_track_page_visit($pdoVisit, cms_portal_visit_page_key());
if ($portalVisitCount === null) {
    $portalVisitCount = cms_get_visit_count($pdoVisit, cms_portal_visit_page_key());
}
$portalContent = cms_portal_home_payload($pdoVisit);
$homeBanners = $portalContent['home_banners'] ?? [];
$socialFeed = $portalContent['social_feed'] ?? [];
$contact = $portalContent['contact'] ?? [];
$instagramProfile = 'https://www.instagram.com/secplaneacionboyaca/';
if (!empty($contact['social']['instagram'])) {
    $instagramProfile = (string) $contact['social']['instagram'];
}
// Configuración del feed de Instagram (widget externo por hashtag).
$igWidget = is_file(__DIR__ . '/config/instagram_widget.php') ? (require __DIR__ . '/config/instagram_widget.php') : [];
$igHashtag = trim((string) ($igWidget['hashtag'] ?? ''));
$igWidgetHtml = trim((string) ($igWidget['embed_html'] ?? ''));
$igHashtagUrl = $igHashtag !== '' ? 'https://www.instagram.com/explore/tags/' . rawurlencode($igHashtag) . '/' : '';
// Publicaciones manuales (Opción B): permalinks canónicos, máx 5, más reciente primero.
$igPosts = [];
foreach ((array) ($igWidget['posts'] ?? []) as $purl) {
    $code = ig_shortcode_from_url((string) $purl);
    if ($code !== '') {
        $igPosts[] = 'https://www.instagram.com/p/' . rawurlencode($code) . '/';
    }
}
$igPosts = array_slice(array_values(array_unique($igPosts)), 0, 4);
$igFeed = array_values(array_filter($socialFeed, static function ($row) {
    return strtolower((string) ($row['network'] ?? '')) === 'instagram';
}));
// Más recientes primero y máximo 5 publicaciones en el carrusel
usort($igFeed, static function ($a, $b) {
    return strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? ''));
});
$igFeed = array_slice($igFeed, 0, 5);
$socialSlides = !empty($igFeed)
    ? array_chunk($igFeed, 2)
    : array_chunk(array_values($socialFeed), 2);

/**
 * Extrae el shortcode de una URL de Instagram, ej:
 *  https://www.instagram.com/p/DYAPxmCEfCV/    → DYAPxmCEfCV
 *  https://www.instagram.com/reel/ABC123/      → ABC123
 *  https://instagram.com/tv/XYZ/                → XYZ
 * Si la URL es un perfil (sin /p/ o /reel/), devuelve '' (no embebible).
 */
function ig_shortcode_from_url(string $url): string {
    if (preg_match('#instagram\.com/(?:[^/]+/)?(?:p|reel|tv)/([A-Za-z0-9_\-]+)#', $url, $m)) {
        return $m[1];
    }
    return '';
}
function ig_embed_url(string $shortcode): string {
    return 'https://www.instagram.com/p/' . rawurlencode($shortcode) . '/embed/captioned';
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
    <title>Red de Observatorios de Boyacá</title>
    <meta name="description" content="Portal oficial de la Red de Observatorios de Boyacá con indicadores, noticias, documentos y datasets.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern/portal-pro.css">
    <?php
    $firstBannerImg = '';
    if (!empty($homeBanners) && !empty($homeBanners[0]['image_url'] ?? '')) {
        $firstBannerImg = (string) $homeBanners[0]['image_url'];
    }
    ?>
    <?php if ($firstBannerImg !== ''): ?>
    <link rel="preload" as="image" href="<?= htmlspecialchars($firstBannerImg) ?>">
    <?php endif; ?>
</head>
<body>
<header class="portal-header">
    <div class="container d-flex flex-wrap justify-content-between align-items-center py-3 gap-3">
        <div class="d-flex align-items-center gap-2">
            <img src="assets/svg/logo.svg" alt="Logo Red de Observatorios" class="brand-logo">
            <div>
                <p class="m-0 small text-uppercase">Gobernación de Boyacá</p>
                <h1 class="m-0">Red de Observatorios</h1>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3 ms-lg-auto">
            <span class="portal-visit-pill" title="Visitantes únicos (mismo navegador no suma varias veces)">
                <i class="fa-solid fa-chart-simple" aria-hidden="true"></i>
                <?= number_format((int) $portalVisitCount, 0, ',', '.') ?> visitantes únicos
            </span>
            <nav class="portal-nav d-flex flex-wrap gap-2">
                <a href="index.php" class="active">Inicio</a>
                <a href="nosotros.php">Nosotros</a>
                <a href="estado-observatorio.php">Estado de datos</a>
                <a href="noticias.php">Noticias</a>
                <button type="button" class="portal-survey-trigger" data-bs-toggle="modal" data-bs-target="#portalSurveyModal">Encuesta opcional</button>
            </nav>
        </div>
    </div>
</header>

<main>
    <section class="hero container py-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <span class="chip"><i class="fa-solid fa-location-dot me-1" aria-hidden="true"></i> Gobernación de Boyacá · Datos abiertos</span>
                <h2 class="mt-3">Conoce a Boyacá a través de sus datos</h2>
                <p>
                    La Red de Observatorios reúne indicadores, noticias y análisis de las dimensiones
                    económica, social, ambiental, de ciencia y tecnología, y de género del departamento.
                    Información clara y confiable para la ciudadanía, la academia y quienes toman decisiones.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-dark" href="#observatorios"><i class="fa-solid fa-arrow-down me-1" aria-hidden="true"></i> Explorar los 5 observatorios</a>
                    <a class="btn btn-outline-dark" href="nosotros.php">Conócenos</a>
                </div>
            </div>
            <div class="col-lg-5">
                <article class="quick-panel">
                    <h3>Búsqueda global</h3>
                    <label class="form-label" for="globalSearch">Buscar por indicador, tema o palabra</label>
                    <div class="input-group">
                        <input id="globalSearch" type="search" class="form-control" placeholder="Ej: pobreza, violencia, salud, educación">
                        <button class="btn btn-primary" id="searchButton">Buscar</button>
                    </div>
                    <div class="search-hints small text-muted mt-2">
                        Sugerencias:
                        <button type="button" class="btn btn-sm btn-link p-0 search-hint">pobreza</button> ·
                        <button type="button" class="btn btn-sm btn-link p-0 search-hint">violencia</button> ·
                        <button type="button" class="btn btn-sm btn-link p-0 search-hint">salud</button> ·
                        <button type="button" class="btn btn-sm btn-link p-0 search-hint">educación</button>
                    </div>
                    <div id="searchResults" class="search-results mt-3" aria-live="polite"></div>
                </article>
            </div>
        </div>
    </section>

    <?php if (!empty($homeBanners)): ?>
    <section class="home-carousel-section container py-3" aria-label="Banner de noticias e información">
        <div class="section-head">
            <h2 class="h5 mb-1">Información y noticias</h2>
            <p class="text-muted small mb-3">Deslizante con avisos institucionales; el contenido se administra desde datos centralizados.</p>
        </div>
        <div id="homeBannerCarousel" class="carousel slide home-banner-carousel shadow-sm" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($homeBanners as $i => $_b): ?>
                    <button type="button" data-bs-target="#homeBannerCarousel" data-bs-slide-to="<?= (int) $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Diapositiva <?= (int) ($i + 1) ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner rounded-4 overflow-hidden">
                <?php foreach ($homeBanners as $i => $b): ?>
                    <?php $hasBannerImg = !empty($b['image_url'] ?? ''); ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <div class="home-banner-slide">
                            <?php if ($hasBannerImg): ?>
                                <div class="home-banner-slide__fig">
                                    <img src="<?= htmlspecialchars($b['image_url']) ?>" alt="" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>" decoding="async">
                                </div>
                                <div class="home-banner-slide__overlay"></div>
                            <?php endif; ?>
                            <div class="home-banner-slide__text">
                            <?php if (!empty($b['tag'])): ?>
                                <span class="home-banner-tag"><?= htmlspecialchars($b['tag']) ?></span>
                            <?php endif; ?>
                            <?php $bTitle = trim((string) ($b['title'] ?? '')); ?>
                            <?php if ($bTitle !== ''): ?>
                                <h3 class="home-banner-title"><?= htmlspecialchars($bTitle) ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($b['subtitle'])): ?>
                                <p class="home-banner-sub mb-0"><?= htmlspecialchars($b['subtitle']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($b['link_url'])): ?>
                                <a class="home-banner-link" href="<?= htmlspecialchars($b['link_url']) ?>">Ver más <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#homeBannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#homeBannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </section>
    <?php endif; ?>

    <section id="observatorios" class="container py-4">
        <div class="section-head">
            <h2>Micrositios por observatorio</h2>
            <p>Identidad visual propia por dimensión, pero en un ecosistema unificado.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($observatories as $slug => $obs): ?>
                <div class="col-md-6 col-xl-4">
                    <article class="obs-card" style="--obs-color: <?= htmlspecialchars($obs['color']) ?>; --obs-accent: <?= htmlspecialchars($obs['accent']) ?>;">
                        <div class="obs-card__icon"><i class="fa-solid <?= htmlspecialchars($obs['icon']) ?>"></i></div>
                        <h3><?= htmlspecialchars($obs['name']) ?></h3>
                        <p><?= htmlspecialchars($obs['description']) ?></p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a class="btn btn-sm btn-dark" href="observatorio.php?slug=<?= urlencode($slug) ?>">Entrar</a>
                            <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($obs['legacy_url']) ?>">Vista actual</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if ($igWidgetHtml !== '' || !empty($igPosts) || (!empty($socialSlides) && !empty($socialSlides[0]))): ?>
    <style>
        /* Embed oficial de Instagram: se auto-ajusta a la altura del post (no se corta). */
        .ig-blockquote{background:#fff !important;border:0 !important;border-radius:14px !important;box-shadow:0 6px 20px rgba(2,6,23,.08) !important;margin:0 auto !important;width:100% !important;min-width:0 !important;max-width:540px !important}
        .home-ig-grid{display:flex;flex-wrap:wrap;gap:1.25rem;justify-content:center;align-items:flex-start}
        .home-ig-grid>.ig-col{flex:1 1 320px;max-width:540px;min-width:300px}
        .ig-hashtag{display:inline-flex;align-items:center;gap:.3rem;font-weight:700;color:#c13584;text-decoration:none}
        .ig-hashtag:hover{color:#a02d6e}
        .ig-widget-shell iframe{width:100% !important;border:0;overflow:hidden}
        /* Publicaciones compactas alineadas en una sola fila (responsivo) */
        .ig-row{display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;align-items:stretch;padding:.25rem 0 .5rem}
        .ig-row .ig-item{flex:1 1 240px;max-width:320px;min-width:230px}
        .ig-row iframe{width:100% !important;height:440px;border:0;border-radius:14px;background:#fff;box-shadow:0 6px 18px rgba(2,6,23,.10);display:block}
    </style>
    <section class="home-social-section container py-4" aria-label="Publicaciones en Instagram">
        <div class="section-head d-flex flex-column flex-md-row flex-md-wrap justify-content-between align-items-md-end gap-2 mb-3">
            <div>
                <h2 class="h5 mb-1">Síguenos en Instagram</h2>
                <p class="text-muted small mb-0">
                    <a class="ig-profile-link" href="<?= htmlspecialchars($instagramProfile) ?>" target="_blank" rel="noopener noreferrer">@secplaneacionboyaca</a>
                    <?php if ($igHashtag !== ''): ?>
                        <span class="text-muted"> · </span>
                        <a class="ig-hashtag" href="<?= htmlspecialchars($igHashtagUrl) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-hashtag" aria-hidden="true"></i><?= htmlspecialchars($igHashtag) ?></a>
                    <?php endif; ?>
                    <?php if (!empty($contact['social']['facebook'])): ?>
                        <span class="text-muted"> · </span>
                        <a class="ig-profile-link fw-normal" href="<?= htmlspecialchars((string) $contact['social']['facebook']) ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php if ($igWidgetHtml !== ''): ?>
        <!-- Widget externo del feed por hashtag (LightWidget/SnapWidget/Elfsight) -->
        <div class="ig-widget-shell"><?= $igWidgetHtml /* código de inserción confiable del CMS/config */ ?></div>
        <?php elseif (!empty($igPosts)): ?>
        <!-- Últimas publicaciones en fila horizontal (compactas) -->
        <div class="ig-row">
            <?php foreach ($igPosts as $purl): $code = ig_shortcode_from_url($purl); ?>
                <div class="ig-item">
                    <iframe src="https://www.instagram.com/p/<?= htmlspecialchars(rawurlencode($code)) ?>/embed/" title="Publicación de Instagram" loading="lazy" scrolling="no" allowtransparency="true" frameborder="0"></iframe>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-2">
            <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars($instagramProfile) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-instagram me-1" aria-hidden="true"></i> Ver más en Instagram</a>
        </div>
        <?php else: ?>
        <div id="homeIgCarousel" class="carousel slide home-ig-carousel" data-bs-ride="carousel" data-bs-interval="9000">
            <div class="carousel-indicators home-ig-carousel__dots">
                <?php foreach ($socialSlides as $si => $_chunk): ?>
                    <button type="button" data-bs-target="#homeIgCarousel" data-bs-slide-to="<?= (int) $si ?>" class="<?= $si === 0 ? 'active' : '' ?>" aria-label="Grupo <?= (int) ($si + 1) ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="ig-feed-shell position-relative">
                <div class="carousel-inner">
                    <?php foreach ($socialSlides as $si => $pair): ?>
                    <div class="carousel-item <?= $si === 0 ? 'active' : '' ?>">
                        <div class="row g-3 g-lg-4 justify-content-center px-2 px-sm-3 py-3 py-md-4">
                            <?php foreach ($pair as $s): ?>
                                <?php
                                $net = strtolower((string) ($s['network'] ?? ''));
                                $isIg = $net === 'instagram';
                                $href = !empty($s['url']) ? (string) $s['url'] : ($isIg ? $instagramProfile : '#');
                                $category = strtoupper((string) ($s['category'] ?? ($isIg ? 'INSTAGRAM' : 'REDES')));
                                $cardTitle = (string) ($s['title'] ?? 'Publicación');
                                $byline = (string) ($s['byline'] ?? ($isIg ? '@secplaneacionboyaca' : ''));
                                $excerpt = (string) ($s['excerpt'] ?? '');
                                $image = trim((string) ($s['image'] ?? ''));
                                $mediaTag = (string) ($s['media_tag'] ?? $category);
                                $mediaCaption = (string) ($s['media_caption'] ?? $cardTitle);
                                $gradientIdx = (crc32($cardTitle) % 5);
                                // Extraer shortcode de IG: si existe, click abre modal con embed; si no, abre URL externa.
                                $igCode = $isIg ? ig_shortcode_from_url($href) : '';
                                $canEmbed = $igCode !== '';
                                $mediaAttrs = $canEmbed
                                    ? 'href="' . htmlspecialchars($href) . '" data-ig-embed="' . htmlspecialchars($igCode) . '" data-ig-url="' . htmlspecialchars($href) . '" data-ig-title="' . htmlspecialchars($cardTitle) . '" data-bs-toggle="modal" data-bs-target="#igEmbedModal"'
                                    : 'href="' . htmlspecialchars($href) . '" target="_blank" rel="noopener noreferrer"';
                                ?>
                                <div class="col-12 col-md-6 d-flex justify-content-center">
                                    <?php if ($canEmbed): ?>
                                    <!-- Embed oficial de Instagram: se auto-ajusta y muestra el post completo -->
                                    <?php $igPermalink = 'https://www.instagram.com/p/' . rawurlencode($igCode) . '/'; ?>
                                    <blockquote class="instagram-media ig-blockquote" data-instgrm-permalink="<?= htmlspecialchars($igPermalink) ?>?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14">
                                        <a href="<?= htmlspecialchars($igPermalink) ?>" target="_blank" rel="noopener noreferrer">Ver esta publicación en Instagram</a>
                                    </blockquote>
                                    <?php else: ?>
                                    <article class="ig-post-card h-100">
                                        <a class="ig-post-card__media ig-post-card__media--grad-<?= (int) $gradientIdx ?>" <?= $mediaAttrs ?>>
                                            <?php if ($image !== ''): ?>
                                                <img class="ig-post-card__img" src="<?= htmlspecialchars($image) ?>" alt="" loading="lazy">
                                            <?php endif; ?>
                                            <span class="ig-post-card__tag-tl"><?= htmlspecialchars($mediaTag) ?></span>
                                            <span class="ig-post-card__brand" aria-hidden="true">
                                                <?php if ($isIg): ?>
                                                    <i class="fa-brands fa-instagram me-1"></i> Sec. Planeación
                                                <?php else: ?>
                                                    <i class="fa-brands fa-facebook-f me-1"></i> Redes Boyacá
                                                <?php endif; ?>
                                            </span>
                                            <span class="ig-post-card__center-title"><?= htmlspecialchars($cardTitle) ?></span>
                                            <span class="ig-post-card__tag-br"><?= htmlspecialchars($mediaCaption) ?></span>
                                        </a>
                                        <div class="ig-post-card__body">
                                            <p class="ig-post-card__eyebrow"><?= htmlspecialchars($category) ?></p>
                                            <h3 class="ig-post-card__title"><?= htmlspecialchars($cardTitle) ?></h3>
                                            <?php if ($byline !== ''): ?>
                                                <p class="ig-post-card__byline mb-2"><a href="<?= htmlspecialchars($href) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($byline) ?></a></p>
                                            <?php endif; ?>
                                            <?php if ($excerpt !== ''): ?>
                                                <p class="ig-post-card__excerpt mb-0"><?= htmlspecialchars($excerpt) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($s['date'])): ?>
                                                <p class="ig-post-card__date mb-0 mt-2"><small class="text-muted"><?= htmlspecialchars((string) $s['date']) ?></small></p>
                                            <?php endif; ?>
                                        </div>
                                    </article>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($pair) === 1): ?>
                                <div class="col-12 col-md-6 d-none d-md-block" aria-hidden="true"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev home-ig-carousel__ctrl" type="button" data-bs-target="#homeIgCarousel" data-bs-slide="prev" aria-label="Anterior">
                    <span class="home-ig-carousel__chev" aria-hidden="true"><i class="fa-solid fa-chevron-left"></i></span>
                </button>
                <button class="carousel-control-next home-ig-carousel__ctrl" type="button" data-bs-target="#homeIgCarousel" data-bs-slide="next" aria-label="Siguiente">
                    <span class="home-ig-carousel__chev" aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
                </button>
            </div>
        </div>
        <!-- Script oficial de Instagram: convierte los blockquote en embeds con la altura correcta -->
        <script async src="//www.instagram.com/embed.js"></script>
        <script>
            (function () {
                function igProcess() { if (window.instgrm && window.instgrm.Embeds) { window.instgrm.Embeds.process(); } }
                var c = document.getElementById('homeIgCarousel');
                if (c) { c.addEventListener('slid.bs.carousel', igProcess); }
                window.addEventListener('load', function () { setTimeout(igProcess, 600); setTimeout(igProcess, 1800); });
            })();
        </script>
        <?php endif; /* fin del else del widget */ ?>
    </section>
    <?php endif; ?>

    <?php
    // Cargar últimas 8 noticias publicadas con su observatorio
    $homeNews = [];
    if ($pdoVisit) {
        try {
            $stN = $pdoVisit->prepare(
                'SELECT n.title, n.slug, n.summary, n.image_url, n.source, n.published_at, o.name AS obs_name, o.slug AS obs_slug, o.theme_color, o.accent_color
                 FROM news n LEFT JOIN observatories o ON o.id = n.observatory_id
                 WHERE n.content_status = "published"
                 ORDER BY n.published_at DESC LIMIT 8'
            );
            $stN->execute();
            $homeNews = $stN->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) { /* tabla puede no existir */ }
    }
    ?>
    <section class="news-band py-5">
        <div class="container">
            <div class="section-head text-white d-flex flex-wrap justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="mb-1">Actualidad y contenidos destacados</h2>
                    <p class="mb-0">Bloques editoriales para informar con claridad a ciudadanía, academia y tomadores de decisión.</p>
                </div>
                <?php if (!empty($homeNews)): ?>
                    <a href="noticias.php" class="text-white text-decoration-underline small">Ver todas →</a>
                <?php endif; ?>
            </div>
            <style>
                .news-card-home{background:#fff;border-radius:14px;overflow:hidden;height:100%;display:flex;flex-direction:column;box-shadow:0 6px 18px rgba(0,0,0,.10);transition:transform .2s ease,box-shadow .2s ease;text-decoration:none;color:inherit}
                .news-card-home:hover{transform:translateY(-4px);box-shadow:0 12px 28px rgba(0,0,0,.18);color:inherit}
                .news-card-home__media{position:relative;aspect-ratio:16/9;background:linear-gradient(135deg,var(--c,#0d6efd),var(--a,#0d6efd));overflow:hidden}
                .news-card-home__media img{width:100%;height:100%;object-fit:cover;display:block}
                .news-card-home__tag{position:absolute;top:.6rem;left:.6rem;background:rgba(0,0,0,.65);color:#fff;font-size:.68rem;font-weight:700;padding:.25rem .55rem;border-radius:999px;text-transform:uppercase;letter-spacing:.04em}
                .news-card-home__body{padding:.95rem 1.05rem 1.05rem;display:flex;flex-direction:column;gap:.45rem;flex:1}
                .news-card-home__title{font-weight:700;font-size:1rem;color:#1f2937;line-height:1.3;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
                .news-card-home__excerpt{font-size:.83rem;color:#6b7280;line-height:1.45;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;flex:1}
                .news-card-home__date{font-size:.72rem;color:var(--c,#0d6efd);font-weight:600;margin-top:auto;display:flex;align-items:center;gap:.35rem}
                .news-card-home__date i{font-size:.7rem}
            </style>
            <div class="row g-3" id="generalNewsCards">
                <?php if (!empty($homeNews)): ?>
                    <?php foreach ($homeNews as $n): ?>
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <a class="news-card-home"
                               href="noticia.php?slug=<?= htmlspecialchars($n['slug']) ?>"
                               style="--c: <?= htmlspecialchars($n['theme_color'] ?? '#0d6efd') ?>; --a: <?= htmlspecialchars($n['accent_color'] ?? '#0d6efd') ?>;">
                                <div class="news-card-home__media">
                                    <?php if (!empty($n['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($n['image_url']) ?>" alt="" loading="lazy">
                                    <?php endif; ?>
                                    <span class="news-card-home__tag"><?= htmlspecialchars($n['obs_name'] ?? 'General') ?></span>
                                </div>
                                <div class="news-card-home__body">
                                    <h3 class="news-card-home__title"><?= htmlspecialchars($n['title']) ?></h3>
                                    <?php if (!empty($n['summary'])): ?>
                                        <p class="news-card-home__excerpt"><?= htmlspecialchars($n['summary']) ?></p>
                                    <?php endif; ?>
                                    <div class="news-card-home__date">
                                        <i class="fa-regular fa-calendar"></i>
                                        <span><?= !empty($n['published_at']) ? date('d M Y', strtotime((string) $n['published_at'])) : '—' ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-6 col-xl-3"><article class="news-card"><span>Noticia</span><h3>Sin noticias publicadas</h3><p>Cree noticias desde el CMS para que aparezcan aquí.</p></article></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Boyacá en imágenes -->
    <section class="boyaca-images container py-5" aria-label="Boyacá en imágenes">
        <style>
            .boyaca-images .bi-head{text-align:center;margin-bottom:1.5rem}
            .boyaca-images h2{font-weight:800;letter-spacing:-.02em;color:#0b2744;margin-bottom:.4rem}
            .boyaca-images .bi-sub{color:#5d6b80;max-width:640px;margin:0 auto}
            .bi-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:1rem}
            .bi-grid figure{margin:0;border-radius:18px;overflow:hidden;position:relative;box-shadow:0 10px 30px rgba(2,6,23,.10)}
            .bi-grid img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease}
            .bi-grid figure:hover img{transform:scale(1.06)}
            .bi-grid figcaption{position:absolute;left:0;right:0;bottom:0;padding:1rem;color:#fff;font-weight:600;font-size:.95rem;background:linear-gradient(180deg,transparent,rgba(2,6,23,.72))}
            .bi-col{display:flex;flex-direction:column;gap:1rem}
            .bi-grid .bi-tall{min-height:340px}
            .bi-col figure{flex:1;min-height:162px}
            @media (max-width:768px){.bi-grid{grid-template-columns:1fr}.bi-grid .bi-tall{min-height:240px}}
        </style>
        <div class="bi-head">
            <h2>Boyacá en imágenes</h2>
            <p class="bi-sub">Territorio, comunidades y actividades productivas que inspiran los análisis de la Red.</p>
        </div>
        <div class="bi-grid">
            <figure class="bi-tall">
                <img src="assets/pictures/thumbs-up.jpg" alt="Participación ciudadana en Boyacá" loading="lazy">
                <figcaption>Participación ciudadana</figcaption>
            </figure>
            <div class="bi-col">
                <figure>
                    <img src="assets/pictures/colegio.jpg" alt="Educación en Boyacá" loading="lazy">
                    <figcaption>Educación</figcaption>
                </figure>
                <figure>
                    <img src="assets/pictures/campo.jpg" alt="Campo boyacense" loading="lazy">
                    <figcaption>Campo y producción</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <!-- Aliados de la Red -->
    <section class="partners-section container py-5" aria-label="Aliados de la Red de Observatorios">
        <style>
            .partners-section .pt-head{text-align:center;margin-bottom:1.75rem}
            .partners-section h2{font-weight:800;letter-spacing:-.02em;color:#0b2744;margin-bottom:.4rem}
            .partners-section .pt-sub{color:#5d6b80;max-width:680px;margin:0 auto}
            .pt-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:1rem;align-items:center}
            .pt-grid .pt-logo{display:flex;align-items:center;justify-content:center;background:#fff;border:1px solid #eef0f4;border-radius:14px;padding:1rem;height:96px;box-shadow:0 4px 14px rgba(2,6,23,.05);transition:transform .2s ease,box-shadow .2s ease}
            .pt-grid .pt-logo:hover{transform:translateY(-3px);box-shadow:0 12px 26px rgba(2,6,23,.12)}
            .pt-grid img{max-width:100%;max-height:60px;object-fit:contain;filter:grayscale(100%);opacity:.78;transition:filter .25s ease,opacity .25s ease}
            .pt-grid .pt-logo:hover img{filter:grayscale(0);opacity:1}
            @media (max-width:992px){.pt-grid{grid-template-columns:repeat(4,1fr)}}
            @media (max-width:576px){.pt-grid{grid-template-columns:repeat(3,1fr)}.pt-grid .pt-logo{height:78px;padding:.65rem}}
        </style>
        <div class="pt-head">
            <h2>Aliados de la Red de Observatorios</h2>
            <p class="pt-sub">Articulamos esfuerzos entre la Gobernación de Boyacá, la academia y otras entidades para fortalecer el análisis territorial.</p>
        </div>
        <div class="pt-grid">
            <?php
            $aliados = [
                ['gobboy.png', 'Gobernación de Boyacá'],
                ['secplan.png', 'Secretaría de Planeación'],
                ['uptc.svg', 'UPTC'],
                ['bio.svg', 'BIO'],
                ['vie.svg', 'VIE'],
                ['ociteb.svg', 'OCITEB'],
                ['poder.svg', 'PODER'],
                ['jdc.png', 'Juan de Castellanos'],
                ['santoto.png', 'Santo Tomás'],
                ['unad.png', 'UNAD'],
                ['uan.svg', 'UAN'],
                ['esap.png', 'ESAP'],
            ];
            foreach ($aliados as [$file, $name]):
            ?>
                <div class="pt-logo" title="<?= htmlspecialchars($name) ?>">
                    <img src="assets/svg/logos/<?= htmlspecialchars($file) ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy">
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php require __DIR__ . '/include/site-footer.php'; ?>
</main>

<!-- Modal global para embed de Instagram -->
<div class="modal fade" id="igEmbedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px">
        <div class="modal-content" style="border-radius:18px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#833ab4 0%,#fd1d1d 50%,#fcb045 100%);color:#fff;border-bottom:none">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="fa-brands fa-instagram fa-lg"></i>
                    <span id="igEmbedTitle">Publicación de Instagram</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0" style="background:#fafafa;min-height:420px">
                <div id="igEmbedSpinner" class="text-center py-5 text-muted">
                    <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2 small">Cargando publicación…</p>
                </div>
                <iframe id="igEmbedIframe" src="about:blank" frameborder="0" scrolling="no" allowtransparency="true" allow="encrypted-media" style="display:none;width:100%;min-height:600px;border:0"></iframe>
            </div>
            <div class="modal-footer justify-content-between" style="background:#fff">
                <a id="igEmbedOpen" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-dark">
                    <i class="fa-brands fa-instagram me-1"></i> Abrir en Instagram
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    var modal  = document.getElementById('igEmbedModal');
    if (!modal) return;
    var iframe = document.getElementById('igEmbedIframe');
    var spin   = document.getElementById('igEmbedSpinner');
    var openBtn= document.getElementById('igEmbedOpen');
    var title  = document.getElementById('igEmbedTitle');

    modal.addEventListener('show.bs.modal', function(ev){
        var trigger = ev.relatedTarget;
        if (!trigger) return;
        var code = trigger.getAttribute('data-ig-embed');
        var url  = trigger.getAttribute('data-ig-url') || '#';
        var t    = trigger.getAttribute('data-ig-title') || 'Publicación';
        title.textContent = t;
        openBtn.href = url;
        spin.style.display = '';
        iframe.style.display = 'none';
        iframe.onload = function(){
            spin.style.display = 'none';
            iframe.style.display = 'block';
        };
        iframe.src = 'https://www.instagram.com/p/' + encodeURIComponent(code) + '/embed/captioned';
    });
    modal.addEventListener('hidden.bs.modal', function(){
        iframe.src = 'about:blank'; // detener carga al cerrar (src vacío recargaría la propia página)
    });
})();
</script>

<?php $surveyContext = 'portal'; require __DIR__ . '/include/survey-modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/modern/portal-pro.js" defer></script>
<script src="assets/js/survey-widget.js" defer></script>
<?php require __DIR__ . '/include/assistant-widget.php'; ?>
</body>
</html>
