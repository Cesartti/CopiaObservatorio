<?php
require_once 'tracker.php';
require_once __DIR__ . '/config/database.php';

$pdo = cms_pdo();

$q = trim($_GET['q'] ?? '');
$obsFilter = trim($_GET['obs'] ?? ''); // '' = todas | 'general' | slug de observatorio
$page = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 9;

$observatories = [];
$rows = [];
$total = 0;
if ($pdo) {
    try {
        $observatories = $pdo->query('SELECT id, name, slug, theme_color FROM observatories ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);

        $where = ['n.content_status = "published"'];
        $params = [];
        if ($obsFilter === 'general') {
            $where[] = 'n.observatory_id IS NULL';
        } elseif ($obsFilter !== '') {
            $where[] = 'o.slug = ?';
            $params[] = $obsFilter;
        }
        if ($q !== '') {
            $where[] = '(n.title LIKE ? OR n.summary LIKE ? OR n.source LIKE ?)';
            $like = '%' . $q . '%';
            array_push($params, $like, $like, $like);
        }
        $whereSql = implode(' AND ', $where);

        $stC = $pdo->prepare("SELECT COUNT(*) FROM news n LEFT JOIN observatories o ON o.id = n.observatory_id WHERE {$whereSql}");
        $stC->execute($params);
        $total = (int) $stC->fetchColumn();

        $offset = ($page - 1) * $pageSize;
        $st = $pdo->prepare(
            "SELECT n.title, n.slug, n.summary, n.image_url, n.source, n.published_at,
                    o.name AS obs_name, o.slug AS obs_slug, o.theme_color
             FROM news n LEFT JOIN observatories o ON o.id = n.observatory_id
             WHERE {$whereSql}
             ORDER BY n.published_at DESC, n.id DESC
             LIMIT {$pageSize} OFFSET {$offset}"
        );
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        // BD no disponible: se muestra estado vacío
    }
}

$pages = max(1, (int) ceil($total / $pageSize));

function noticias_link(array $overrides = []): string
{
    $params = array_merge([
        'q' => trim($_GET['q'] ?? ''),
        'obs' => trim($_GET['obs'] ?? ''),
        'page' => 1,
    ], $overrides);

    return 'noticias.php?' . http_build_query(array_filter($params, fn ($v) => $v !== '' && $v !== null));
}

$mesesCortos = [1 => 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
function noticias_fecha(?string $dt, array $meses): string
{
    if (!$dt || ($ts = strtotime($dt)) === false) {
        return '—';
    }

    return date('d', $ts) . ' ' . $meses[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

$pageTitle = 'Noticias y eventos · Red de Observatorios';
$pageDescription = 'Actualidad de la Red de Observatorios de Boyacá: convocatorias, informes, eventos y novedades.';
include 'include/site-header.php';
?>
<style>
  .noticias-page{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;color:#0f172a}
  .noticias-hero{background:radial-gradient(900px 450px at 10% -20%,rgba(124,58,237,.14),transparent),linear-gradient(180deg,#fff 0%,#f8fafc 100%);border-bottom:1px solid #e5e7eb}
  .obs-chip{display:inline-flex;align-items:center;gap:.45rem;padding:.5rem .85rem;border-radius:999px;border:1px solid #e2e8f0;background:#fff;color:#0f172a;text-decoration:none;font-size:.88rem;font-weight:600;transition:all .15s ease}
  .obs-chip:hover{transform:translateY(-1px);box-shadow:0 10px 20px rgba(2,6,23,.08);color:#0f172a}
  .obs-chip.active{background:#f3e8ff;border-color:#c4b5fd;color:#5b21b6}
  .obs-dot{width:.65rem;height:.65rem;border-radius:999px;display:inline-block}
  .noticia-card{background:#fff;border:1px solid #e5e7eb;border-radius:1rem;overflow:hidden;height:100%;display:flex;flex-direction:column;text-decoration:none;color:inherit;transition:all .2s ease}
  .noticia-card:hover{transform:translateY(-3px);box-shadow:0 14px 36px rgba(2,6,23,.12);border-color:var(--c,#c4b5fd);color:inherit}
  .noticia-card__media{position:relative;aspect-ratio:16/9;background:linear-gradient(135deg,var(--c,#5b21b6),#94a3b8);overflow:hidden}
  .noticia-card__media img{width:100%;height:100%;object-fit:cover}
  .noticia-card__tag{position:absolute;top:.6rem;left:.6rem;background:rgba(15,23,42,.72);color:#fff;font-size:.68rem;font-weight:700;padding:.25rem .6rem;border-radius:999px;text-transform:uppercase;letter-spacing:.04em}
  .noticia-card__body{padding:1rem 1.1rem 1.1rem;display:flex;flex-direction:column;gap:.45rem;flex:1}
  .noticia-card__title{font-weight:700;font-size:1rem;line-height:1.35;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
  .noticia-card__excerpt{font-size:.85rem;color:#6b7280;line-height:1.5;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;flex:1}
  .noticia-card__meta{font-size:.75rem;color:var(--c,#5b21b6);font-weight:600;margin-top:auto}
  .noticias-empty{border:1px dashed #e2e8f0;background:#fafafa;border-radius:1rem;color:#64748b}
</style>

<div class="noticias-page">
  <section class="noticias-hero py-5">
    <div class="container">
      <h1 class="fw-bold mb-2" style="letter-spacing:-.02em">Noticias y eventos</h1>
      <p class="text-muted mb-0" style="max-width:680px">Actualidad de la Red de Observatorios de Boyacá: convocatorias, informes, eventos y novedades, tanto generales como de cada observatorio.</p>
    </div>
  </section>

  <div class="container py-4">
    <div class="d-flex flex-wrap gap-2 mb-3">
      <a class="obs-chip <?= $obsFilter === '' ? 'active' : '' ?>" href="<?= htmlspecialchars(noticias_link(['obs' => ''])) ?>">
        <span class="obs-dot" style="background:#94a3b8"></span> Todas
      </a>
      <a class="obs-chip <?= $obsFilter === 'general' ? 'active' : '' ?>" href="<?= htmlspecialchars(noticias_link(['obs' => 'general'])) ?>">
        <span class="obs-dot" style="background:#475569"></span> Generales
      </a>
      <?php foreach ($observatories as $o): ?>
        <a class="obs-chip <?= $obsFilter === $o['slug'] ? 'active' : '' ?>" href="<?= htmlspecialchars(noticias_link(['obs' => $o['slug']])) ?>">
          <span class="obs-dot" style="background:<?= htmlspecialchars($o['theme_color'] ?? '#5b21b6') ?>"></span>
          <?= htmlspecialchars($o['name']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <form class="row g-2 mb-4" method="get" action="noticias.php">
      <?php if ($obsFilter !== ''): ?><input type="hidden" name="obs" value="<?= htmlspecialchars($obsFilter) ?>"><?php endif; ?>
      <div class="col-md-5 col-lg-4">
        <input type="search" name="q" class="form-control" placeholder="Buscar por título, resumen o fuente…" value="<?= htmlspecialchars($q) ?>">
      </div>
      <div class="col-auto"><button class="btn btn-primary" type="submit">Buscar</button></div>
      <?php if ($q !== '' || $obsFilter !== ''): ?>
        <div class="col-auto"><a class="btn btn-outline-secondary" href="noticias.php">Limpiar</a></div>
      <?php endif; ?>
    </form>

    <?php if ($rows === []): ?>
      <div class="noticias-empty p-5 text-center">
        <h2 class="h5 mb-2">Sin noticias</h2>
        <p class="mb-0"><?= $q !== '' || $obsFilter !== '' ? 'No hay resultados con los filtros actuales.' : 'Aún no hay noticias publicadas.' ?></p>
      </div>
    <?php else: ?>
      <p class="text-muted small mb-3">Mostrando <strong><?= count($rows) ?></strong> de <strong><?= $total ?></strong> noticias</p>
      <div class="row g-3">
        <?php foreach ($rows as $n): ?>
          <div class="col-sm-6 col-lg-4">
            <a class="noticia-card" href="noticia.php?slug=<?= htmlspecialchars($n['slug']) ?>" style="--c: <?= htmlspecialchars($n['theme_color'] ?? '#475569') ?>">
              <div class="noticia-card__media">
                <?php if (!empty($n['image_url'])): ?>
                  <img src="<?= htmlspecialchars($n['image_url']) ?>" alt="" loading="lazy">
                <?php endif; ?>
                <span class="noticia-card__tag"><?= htmlspecialchars($n['obs_name'] ?? 'General') ?></span>
              </div>
              <div class="noticia-card__body">
                <h2 class="noticia-card__title"><?= htmlspecialchars($n['title']) ?></h2>
                <?php if (!empty($n['summary'])): ?>
                  <p class="noticia-card__excerpt"><?= htmlspecialchars($n['summary']) ?></p>
                <?php endif; ?>
                <div class="noticia-card__meta">
                  📅 <?= htmlspecialchars(noticias_fecha($n['published_at'] ?? null, $mesesCortos)) ?>
                  <?php if (!empty($n['source'])): ?> · <span class="text-muted"><?= htmlspecialchars($n['source']) ?></span><?php endif; ?>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($pages > 1): ?>
        <nav aria-label="Paginación de noticias" class="mt-4">
          <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= htmlspecialchars(noticias_link(['page' => max(1, $page - 1)])) ?>">Anterior</a>
            </li>
            <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars(noticias_link(['page' => $i])) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= htmlspecialchars(noticias_link(['page' => min($pages, $page + 1)])) ?>">Siguiente</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/include/site-footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php require __DIR__ . '/include/assistant-widget.php'; ?>
</body>
</html>
