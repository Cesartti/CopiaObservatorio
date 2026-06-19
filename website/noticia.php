<?php
require_once 'tracker.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/admin/auth/bootstrap.php';
require_once __DIR__ . '/include/html_sanitizer.php';

$pdo = cms_pdo();
$slug = trim($_GET['slug'] ?? '');
$newsId = (int) ($_GET['id'] ?? 0);
$isEditor = function_exists('auth_user') && auth_user() !== null;

$noticia = null;
$related = [];
if ($pdo && ($slug !== '' || $newsId > 0)) {
    try {
        $where = $slug !== '' ? 'n.slug = ?' : 'n.id = ?';
        $param = $slug !== '' ? $slug : $newsId;
        $st = $pdo->prepare(
            "SELECT n.*, o.name AS obs_name, o.slug AS obs_slug, o.theme_color, o.accent_color
             FROM news n LEFT JOIN observatories o ON o.id = n.observatory_id
             WHERE {$where} LIMIT 1"
        );
        $st->execute([$param]);
        $noticia = $st->fetch(PDO::FETCH_ASSOC) ?: null;

        // Borradores solo visibles para usuarios del CMS (vista previa)
        if ($noticia && $noticia['content_status'] !== 'published' && !$isEditor) {
            $noticia = null;
        }

        if ($noticia) {
            $stR = $pdo->prepare(
                'SELECT n.title, n.slug, n.image_url, n.published_at, o.name AS obs_name
                 FROM news n LEFT JOIN observatories o ON o.id = n.observatory_id
                 WHERE n.content_status = "published" AND n.id <> ?
                   AND ((n.observatory_id IS NULL AND ? IS NULL) OR n.observatory_id = ?)
                 ORDER BY n.published_at DESC LIMIT 3'
            );
            $obsId = $noticia['observatory_id'] !== null ? (int) $noticia['observatory_id'] : null;
            $stR->execute([(int) $noticia['id'], $obsId, $obsId]);
            $related = $stR->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Throwable $e) {
        $noticia = null;
    }
}

if (!$noticia) {
    http_response_code(404);
}

$meses = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
function noticia_fecha(?string $dt, array $meses): string
{
    if (!$dt) {
        return '';
    }
    $ts = strtotime($dt);
    if ($ts === false) {
        return '';
    }

    return date('j', $ts) . ' de ' . $meses[(int) date('n', $ts)] . ' de ' . date('Y', $ts);
}

$color = htmlspecialchars($noticia['theme_color'] ?? '#5b21b6');
$pageTitle = $noticia ? $noticia['title'] . ' · Red de Observatorios' : 'Noticia no encontrada · Red de Observatorios';
$pageDescription = $noticia['summary'] ?? '';
$themeColor = $noticia['theme_color'] ?? '';
$themeAccent = $noticia['accent_color'] ?? '';
include 'include/site-header.php';
?>
<style>
  .noticia-page{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;color:#0f172a}
  .noticia-hero{background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);border-bottom:1px solid #e5e7eb}
  .noticia-badge{display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .75rem;border-radius:999px;font-weight:700;font-size:.8rem;color:#fff;background:<?= $color ?>}
  .noticia-meta{color:#64748b;font-size:.92rem;display:flex;flex-wrap:wrap;gap:1rem;align-items:center}
  .noticia-cover{width:100%;max-height:420px;object-fit:cover;border-radius:1rem;box-shadow:0 14px 40px rgba(2,6,23,.12)}
  .noticia-body{font-size:1.05rem;line-height:1.75;color:#1f2937}
  .noticia-body img{max-width:100%;height:auto;border-radius:.75rem}
  .noticia-body h2,.noticia-body h3{margin-top:1.6rem;font-weight:700}
  .noticia-rel-card{display:flex;gap:.75rem;align-items:flex-start;padding:.75rem;border:1px solid #e5e7eb;border-radius:.85rem;text-decoration:none;color:inherit;transition:all .2s ease;background:#fff}
  .noticia-rel-card:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(2,6,23,.10);color:inherit}
  .noticia-rel-card img,.noticia-rel-card .ph{flex:0 0 84px;width:84px;height:64px;border-radius:.5rem;object-fit:cover;background:linear-gradient(135deg,<?= $color ?>,#94a3b8)}
  .draft-banner{background:#fff7ed;border:1px solid #fdba74;color:#9a3412;border-radius:.75rem}
</style>

<div class="noticia-page">
<?php if (!$noticia): ?>
  <div class="container py-5 text-center">
    <h1 class="h3 mb-3">Noticia no encontrada</h1>
    <p class="text-muted">La noticia que busca no existe o ya no está publicada.</p>
    <a class="btn btn-primary" href="noticias.php">Ver todas las noticias</a>
  </div>
<?php else: ?>
  <section class="noticia-hero py-4">
    <div class="container" style="max-width:880px">
      <nav aria-label="breadcrumb" class="small mb-3">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
          <li class="breadcrumb-item"><a href="noticias.php">Noticias</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars(mb_strimwidth($noticia['title'], 0, 60, '…')) ?></li>
        </ol>
      </nav>
      <?php if ($noticia['content_status'] !== 'published'): ?>
        <div class="draft-banner px-3 py-2 mb-3 small"><strong>Vista previa:</strong> esta noticia está en borrador y no es visible al público.</div>
      <?php endif; ?>
      <div class="mb-2">
        <?php if (!empty($noticia['obs_name'])): ?>
          <a class="noticia-badge text-decoration-none" href="noticias.php?obs=<?= htmlspecialchars($noticia['obs_slug']) ?>"><?= htmlspecialchars($noticia['obs_name']) ?></a>
        <?php else: ?>
          <a class="noticia-badge text-decoration-none" href="noticias.php?obs=general" style="background:#475569">General · Red de Observatorios</a>
        <?php endif; ?>
      </div>
      <h1 class="fw-bold mb-3" style="letter-spacing:-.02em"><?= htmlspecialchars($noticia['title']) ?></h1>
      <?php if (!empty($noticia['summary'])): ?>
        <p class="lead text-muted mb-3"><?= htmlspecialchars($noticia['summary']) ?></p>
      <?php endif; ?>
      <div class="noticia-meta mb-1">
        <?php $fecha = noticia_fecha($noticia['published_at'] ?? null, $meses); ?>
        <?php if ($fecha !== ''): ?><span>📅 <?= htmlspecialchars($fecha) ?></span><?php endif; ?>
        <?php if (!empty($noticia['source'])): ?><span>✍️ <?= htmlspecialchars($noticia['source']) ?></span><?php endif; ?>
      </div>
    </div>
  </section>

  <div class="container py-4" style="max-width:880px">
    <?php if (!empty($noticia['image_url'])): ?>
      <img class="noticia-cover mb-4" src="<?= htmlspecialchars($noticia['image_url']) ?>" alt="<?= htmlspecialchars($noticia['title']) ?>">
    <?php endif; ?>

    <article class="noticia-body mb-5">
      <?php if (trim((string) $noticia['body']) !== ''): ?>
        <?= cms_sanitize_rich_html((string) $noticia['body']) /* HTML de editores: saneado con lista blanca contra XSS almacenado */ ?>
      <?php else: ?>
        <p class="text-muted">Esta noticia no tiene contenido adicional.</p>
      <?php endif; ?>
    </article>

    <div class="d-flex flex-wrap gap-2 mb-5">
      <a class="btn btn-outline-secondary" href="noticias.php">← Todas las noticias</a>
      <?php if (!empty($noticia['obs_slug'])): ?>
        <a class="btn btn-outline-secondary" href="observatorio.php?slug=<?= htmlspecialchars($noticia['obs_slug']) ?>">Ir al observatorio <?= htmlspecialchars($noticia['obs_name']) ?></a>
      <?php endif; ?>
      <?php if ($isEditor): ?>
        <a class="btn btn-outline-primary" href="cms/news.php?edit=<?= (int) $noticia['id'] ?>">✏️ Editar en el CMS</a>
      <?php endif; ?>
    </div>

    <?php if ($related !== []): ?>
      <h2 class="h5 fw-bold mb-3">Noticias relacionadas</h2>
      <div class="row g-3 mb-4">
        <?php foreach ($related as $r): ?>
          <div class="col-md-4">
            <a class="noticia-rel-card" href="noticia.php?slug=<?= htmlspecialchars($r['slug']) ?>">
              <?php if (!empty($r['image_url'])): ?>
                <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="" loading="lazy">
              <?php else: ?>
                <span class="ph"></span>
              <?php endif; ?>
              <span>
                <strong class="d-block small"><?= htmlspecialchars($r['title']) ?></strong>
                <small class="text-muted"><?= htmlspecialchars(noticia_fecha($r['published_at'] ?? null, $meses)) ?></small>
              </span>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
</div>

<?php require __DIR__ . '/include/site-footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php require __DIR__ . '/include/assistant-widget.php'; ?>
</body>
</html>
