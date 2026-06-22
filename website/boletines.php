<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/bulletins.php';

$pdo = cms_pdo();
$obsList = $pdo ? cms_bulletins_observatories($pdo) : [];
$obsById = [];
foreach ($obsList as $o) {
    $obsById[(int) $o['id']] = $o;
}
$all = cms_bulletins_fetch($pdo, 'all', true);

// Agrupar: general (null) + por observatorio
$groups = ['general' => []];
foreach ($all as $b) {
    $key = $b['observatory_id'] === null ? 'general' : (int) $b['observatory_id'];
    $groups[$key][] = $b;
}

$pageTitle = 'Boletines · Red de Observatorios de Boyacá';
$pageDescription = 'Boletines oficiales de la Red de Observatorios de Boyacá, generales y por observatorio.';
require __DIR__ . '/include/site-header.php';

/** Tarjeta de un boletín. */
function boletin_card(array $b): void
{
    $pdf = cms_bulletin_href((string) ($b['pdf_url'] ?? ''));
    $cover = cms_bulletin_href((string) ($b['cover_url'] ?? ''));
    $fecha = !empty($b['published_at']) ? date('d/m/Y', strtotime((string) $b['published_at'])) : '';
    ?>
    <div class="col-md-6 col-lg-4">
        <article class="bol-card h-100">
            <div class="bol-cover">
                <?php if ($cover !== ''): ?>
                    <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($b['title']) ?>" loading="lazy">
                <?php else: ?>
                    <span class="bol-cover-fallback"><i class="fa-solid fa-file-pdf"></i></span>
                <?php endif; ?>
            </div>
            <div class="bol-body">
                <?php if (!empty($b['category'])): ?><span class="bol-cat"><?= htmlspecialchars($b['category']) ?></span><?php endif; ?>
                <h3><?= htmlspecialchars($b['title']) ?></h3>
                <?php if (!empty($b['description'])): ?><p><?= htmlspecialchars($b['description']) ?></p><?php endif; ?>
                <div class="bol-meta">
                    <?php if ($fecha !== ''): ?><span><i class="fa-regular fa-calendar me-1"></i><?= htmlspecialchars($fecha) ?></span><?php endif; ?>
                    <?php if ($pdf !== ''): ?><a class="bol-btn" href="<?= htmlspecialchars($pdf) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-download me-1"></i>Descargar PDF</a><?php endif; ?>
                </div>
            </div>
        </article>
    </div>
    <?php
}
?>
<style>
    .bol-hero{background:linear-gradient(120deg,#0b2744,#16406e);color:#fff;padding:2.4rem 0 2rem}
    .bol-hero h1{font-weight:800;margin:0 0 .4rem}
    .bol-hero p{color:#cfe0f2;max-width:760px;margin:0}
    .bol-section{padding:1.6rem 0 .4rem}
    .bol-section h2{font-size:1.15rem;font-weight:800;color:#0b2744;border-left:4px solid var(--obs-accent,#23a9b8);padding-left:.6rem;margin-bottom:1rem}
    .bol-card{display:flex;flex-direction:column;background:#fff;border:1px solid #e8edf5;border-radius:14px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.05);transition:transform .15s,box-shadow .15s}
    .bol-card:hover{transform:translateY(-3px);box-shadow:0 10px 26px rgba(0,0,0,.1)}
    .bol-cover{height:150px;background:#f0eef3;display:flex;align-items:center;justify-content:center;overflow:hidden}
    .bol-cover img{width:100%;height:100%;object-fit:cover}
    .bol-cover-fallback{font-size:3rem;color:#0b2744;opacity:.4}
    .bol-body{padding:1rem 1.1rem 1.2rem;display:flex;flex-direction:column;gap:.4rem;flex:1}
    .bol-cat{align-self:flex-start;background:rgba(35,169,184,.12);color:#0b6b78;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:.15rem .55rem;border-radius:999px}
    .bol-body h3{font-size:1rem;font-weight:700;color:#13243a;margin:.1rem 0 0}
    .bol-body p{font-size:.86rem;color:#5b6b7f;margin:0;flex:1}
    .bol-meta{display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-top:.4rem;font-size:.8rem;color:#6b7280}
    .bol-btn{background:#0b2744;color:#fff!important;border-radius:999px;padding:.35rem .8rem;font-size:.8rem;font-weight:600;text-decoration:none}
    .bol-btn:hover{background:#16406e}
    .bol-filter{display:flex;flex-wrap:wrap;gap:.5rem;margin:.4rem 0 .2rem}
    .bol-filter-btn{border:1px solid #cfdae8;background:#fff;color:#0b2744;border-radius:999px;padding:.4rem 1.05rem;font-size:.85rem;font-weight:600;cursor:pointer;transition:.15s}
    .bol-filter-btn:hover{background:#eef3fa}
    .bol-filter-btn.active{background:#0b2744;color:#fff;border-color:#0b2744}
</style>

<section class="bol-hero">
    <div class="container">
        <h1>Boletines de la Red de Observatorios</h1>
        <p>Espacio oficial donde se publican los boletines con información actualizada, análisis y reportes de las dimensiones y categorías de los observatorios de Boyacá.</p>
    </div>
</section>

<?php
// Dimensiones presentes (para los botones de filtro)
$presentDims = [];
if (!empty($groups['general'])) {
    $presentDims['general'] = 'Generales';
}
foreach ($obsById as $oid => $o) {
    if (!empty($groups[$oid])) {
        $presentDims[$o['slug']] = $o['name'];
    }
}
?>
<main class="container py-3">
    <?php if (empty($all)): ?>
        <div class="alert alert-info my-4">Aún no hay boletines publicados.</div>
    <?php endif; ?>

    <?php if (count($presentDims) > 1): ?>
    <div class="bol-filter" role="tablist" aria-label="Filtrar boletines por dimensión">
        <button class="bol-filter-btn active" data-dim="all">Todos</button>
        <?php foreach ($presentDims as $slug => $label): ?>
            <button class="bol-filter-btn" data-dim="<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($label) ?></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($groups['general'])): ?>
        <section class="bol-section" data-dim="general">
            <h2><i class="fa-solid fa-layer-group me-2"></i>Boletines generales</h2>
            <div class="row g-3"><?php foreach ($groups['general'] as $b) { boletin_card($b); } ?></div>
        </section>
    <?php endif; ?>

    <?php foreach ($obsById as $oid => $o): ?>
        <?php if (!empty($groups[$oid])): ?>
            <section class="bol-section" data-dim="<?= htmlspecialchars($o['slug']) ?>" id="obs-<?= htmlspecialchars($o['slug']) ?>">
                <h2><i class="fa-solid fa-building-columns me-2"></i><?= htmlspecialchars($o['name']) ?></h2>
                <div class="row g-3"><?php foreach ($groups[$oid] as $b) { boletin_card($b); } ?></div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</main>

<?php require __DIR__ . '/include/site-footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
  var btns = document.querySelectorAll('.bol-filter-btn');
  var secs = document.querySelectorAll('.bol-section');
  btns.forEach(function (b) {
    b.addEventListener('click', function () {
      btns.forEach(function (x) { x.classList.remove('active'); });
      b.classList.add('active');
      var d = b.getAttribute('data-dim');
      secs.forEach(function (s) {
        s.style.display = (d === 'all' || s.getAttribute('data-dim') === d) ? '' : 'none';
      });
    });
  });
})();
</script>
</body>
</html>
