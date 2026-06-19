<?php
/**
 * Galería de infografías del observatorio (pestaña "Infografías").
 *
 * Requiere $slug (slug del observatorio actual). Lee config/infografias.php,
 * filtra las publicaciones del observatorio y las muestra como una cuadrícula
 * tipo Instagram ordenada de la más reciente (arriba) a la más antigua (abajo).
 * Al hacer clic se abre un lightbox con carrusel, título y nota.
 */
$igPubsAll = require dirname(__DIR__) . '/config/infografias.php';
$igBaseDir = dirname(__DIR__) . '/assets/infografias';

$igMeses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

$igPubs = [];
foreach ($igPubsAll as $pub) {
    $obsList = $pub['obs'] ?? [];
    if (!in_array('*', $obsList, true) && !in_array($slug, $obsList, true)) {
        continue;
    }
    $dir = $igBaseDir . '/' . $pub['folder'];
    if (!is_dir($dir)) {
        continue;
    }
    $files = array_values(array_filter(scandir($dir), static function ($f) use ($dir) {
        return is_file($dir . '/' . $f) && preg_match('/\.(png|jpe?g|webp|gif)$/i', $f);
    }));
    natcasesort($files);
    $files = array_values($files);
    if ($files === []) {
        continue;
    }
    $pub['images'] = array_map(static function ($f) use ($pub) {
        return 'assets/infografias/' . rawurlencode($pub['folder']) . '/' . rawurlencode($f);
    }, $files);
    $ts = strtotime($pub['date'] ?? '');
    $pub['date_ts'] = $ts !== false ? $ts : 0;
    $pub['date_label'] = $ts !== false
        ? date('j', $ts) . ' de ' . $igMeses[(int) date('n', $ts)] . ' de ' . date('Y', $ts)
        : '';
    $igPubs[] = $pub;
}

// Más reciente arriba; la más antigua queda al final (abajo).
usort($igPubs, static fn ($a, $b) => $b['date_ts'] <=> $a['date_ts']);
?>
<?php if ($igPubs === []): ?>
    <article class="content-card">
        <h3>Datos e infografías</h3>
        <p class="text-muted mb-0">Aún no hay infografías publicadas para este observatorio.</p>
    </article>
<?php else: ?>
<style>
    .ig-pub-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}
    @media (max-width: 991px){.ig-pub-grid{grid-template-columns:repeat(2,1fr)}}
    @media (max-width: 575px){.ig-pub-grid{grid-template-columns:1fr}}
    .ig-pub-card{position:relative;display:flex;flex-direction:column;background:#fff;border:1px solid #e8edf5;border-radius:14px;overflow:hidden;cursor:pointer;transition:transform .2s ease,box-shadow .2s ease;padding:0;text-align:left}
    .ig-pub-card:hover{transform:translateY(-3px);box-shadow:0 14px 32px rgba(2,6,23,.14)}
    .ig-pub-media{position:relative;aspect-ratio:4/5;background:#f1f5f9;overflow:hidden}
    .ig-pub-media img{width:100%;height:100%;object-fit:cover;object-position:top;transition:transform .3s ease}
    .ig-pub-card:hover .ig-pub-media img{transform:scale(1.04)}
    .ig-pub-count{position:absolute;top:.6rem;right:.6rem;background:rgba(15,23,42,.72);color:#fff;font-size:.75rem;font-weight:700;padding:.25rem .6rem;border-radius:999px;display:inline-flex;align-items:center;gap:.35rem}
    .ig-pub-hover{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;gap:.5rem;background:rgba(15,23,42,.45);color:#fff;font-weight:700;opacity:0;transition:opacity .2s ease}
    .ig-pub-card:hover .ig-pub-hover{opacity:1}
    .ig-pub-body{padding:.8rem .95rem .95rem}
    .ig-pub-title{font-weight:700;font-size:.95rem;line-height:1.35;margin:0 0 .25rem;color:#13233c;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .ig-pub-date{font-size:.78rem;color:var(--obs-color,#0d6efd);font-weight:600}
    .ig-lb .modal-content{border:none;border-radius:16px;overflow:hidden;background:#0f172a}
    .ig-lb .modal-header{background:var(--obs-color,#0d6efd);color:#fff;border-bottom:none}
    .ig-lb .carousel-item{background:#0f172a;text-align:center}
    .ig-lb .carousel-item img{max-height:74vh;max-width:100%;width:auto;margin:0 auto;object-fit:contain}
    .ig-lb .carousel-control-prev,.ig-lb .carousel-control-next{width:9%}
    .ig-lb .carousel-indicators [data-bs-target]{width:8px;height:8px;border-radius:999px}
    .ig-lb-note{background:#fff;padding:1rem 1.25rem;color:#334155;font-size:.92rem;line-height:1.55}
    .ig-lb-note .ig-lb-date{color:var(--obs-color,#0d6efd);font-weight:700;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em}
</style>

<article class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="mb-0">Datos e infografías</h3>
        <span class="text-muted small"><?= count($igPubs) ?> publicación<?= count($igPubs) === 1 ? '' : 'es' ?> · de la más reciente a la más antigua</span>
    </div>
    <div class="ig-pub-grid">
        <?php foreach ($igPubs as $i => $pub): ?>
            <button type="button" class="ig-pub-card" data-ig-pub="<?= $i ?>" aria-label="Ver publicación: <?= htmlspecialchars($pub['title']) ?>">
                <span class="ig-pub-media">
                    <img src="<?= htmlspecialchars($pub['images'][0]) ?>" alt="<?= htmlspecialchars($pub['title']) ?>" loading="lazy">
                    <?php if (count($pub['images']) > 1): ?>
                        <span class="ig-pub-count"><i class="fa-regular fa-clone" aria-hidden="true"></i> <?= count($pub['images']) ?></span>
                    <?php endif; ?>
                    <span class="ig-pub-hover"><i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i> Ver publicación</span>
                </span>
                <span class="ig-pub-body">
                    <span class="ig-pub-title d-block"><?= htmlspecialchars($pub['title']) ?></span>
                    <?php if ($pub['date_label'] !== ''): ?>
                        <span class="ig-pub-date"><i class="fa-regular fa-calendar me-1" aria-hidden="true"></i><?= htmlspecialchars($pub['date_label']) ?></span>
                    <?php endif; ?>
                </span>
            </button>
        <?php endforeach; ?>
    </div>
</article>

<!-- Lightbox de publicación con carrusel -->
<div class="modal fade ig-lb" id="igPubModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="igPubModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <div id="igPubCarousel" class="carousel slide">
                    <div class="carousel-indicators" id="igPubIndicators"></div>
                    <div class="carousel-inner" id="igPubInner"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#igPubCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span><span class="visually-hidden">Anterior</span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#igPubCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span><span class="visually-hidden">Siguiente</span></button>
                </div>
                <div class="ig-lb-note">
                    <div class="ig-lb-date mb-1" id="igPubModalDate"></div>
                    <div id="igPubModalNote"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var pubs = <?= json_encode(array_map(static fn ($p) => [
        'title' => $p['title'],
        'note' => $p['note'] ?? '',
        'date' => $p['date_label'],
        'images' => $p['images'],
    ], $igPubs), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    function openPub(idx) {
        var pub = pubs[idx];
        if (!pub) return;
        document.getElementById('igPubModalTitle').textContent = pub.title;
        document.getElementById('igPubModalNote').textContent = pub.note;
        document.getElementById('igPubModalDate').textContent = pub.date ? 'Publicado el ' + pub.date : '';

        var inner = document.getElementById('igPubInner');
        var dots = document.getElementById('igPubIndicators');
        inner.innerHTML = '';
        dots.innerHTML = '';
        pub.images.forEach(function (src, i) {
            var item = document.createElement('div');
            item.className = 'carousel-item' + (i === 0 ? ' active' : '');
            var img = document.createElement('img');
            img.src = src;
            img.alt = pub.title + ' — imagen ' + (i + 1) + ' de ' + pub.images.length;
            img.loading = i === 0 ? 'eager' : 'lazy';
            item.appendChild(img);
            inner.appendChild(item);
            if (pub.images.length > 1) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.setAttribute('data-bs-target', '#igPubCarousel');
                dot.setAttribute('data-bs-slide-to', String(i));
                dot.setAttribute('aria-label', 'Imagen ' + (i + 1));
                if (i === 0) dot.className = 'active';
                dots.appendChild(dot);
            }
        });

        var single = pub.images.length <= 1;
        document.querySelector('#igPubCarousel .carousel-control-prev').style.display = single ? 'none' : '';
        document.querySelector('#igPubCarousel .carousel-control-next').style.display = single ? 'none' : '';

        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('igPubModal')).show();
        }
    }

    document.addEventListener('click', function (e) {
        var card = e.target.closest('[data-ig-pub]');
        if (card) openPub(parseInt(card.getAttribute('data-ig-pub'), 10));
    });
})();
</script>
<?php endif; ?>
