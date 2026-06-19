<?php

/**
 * Vista pública del tablero CMS — diseño tipo Power BI (rejilla + tarjetas).
 * Datos en MySQL (data_json), alimentados desde Excel vía CSV en el CMS.
 */

require_once __DIR__ . '/config/database.php';

$pdo = cms_pdo();
$obsSlug = trim($_GET['obs'] ?? '');
$dashSlug = trim($_GET['dash'] ?? '');
$embed = isset($_GET['embed']) && (string) $_GET['embed'] === '1';

if (!$pdo || $obsSlug === '' || $dashSlug === '') {
    http_response_code(404);
    echo 'Tablero no encontrado.';
    exit;
}

$stmt = $pdo->prepare(
    'SELECT d.id, d.title, d.description, o.name AS obs_name, o.slug AS obs_slug
     FROM cms_dashboards d
     INNER JOIN observatories o ON o.id = d.observatory_id
     WHERE o.slug = ? AND d.slug = ? AND d.is_active = 1'
);
$stmt->execute([$obsSlug, $dashSlug]);
$dash = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$dash) {
    http_response_code(404);
    echo 'Tablero no encontrado.';
    exit;
}

$dashId = (int) $dash['id'];

$sqlFull = 'SELECT id, title, chart_type, data_json, options_json, sort_order, updated_at,
            COALESCE(layout_span, 12) AS layout_span,
            COALESCE(tile_height_px, 320) AS tile_height_px
            FROM cms_charts
            WHERE dashboard_id = ? AND is_active = 1
            ORDER BY sort_order ASC, id ASC';
$sqlLegacy = 'SELECT id, title, chart_type, data_json, options_json, sort_order, updated_at
              FROM cms_charts
              WHERE dashboard_id = ? AND is_active = 1
              ORDER BY sort_order ASC, id ASC';

$chartRows = [];
try {
    $q = $pdo->prepare($sqlFull);
    $q->execute([$dashId]);
    $chartRows = $q->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $q = $pdo->prepare($sqlLegacy);
    $q->execute([$dashId]);
    $chartRows = $q->fetchAll(PDO::FETCH_ASSOC);
    foreach ($chartRows as &$r) {
        $r['layout_span'] = 12;
        $r['tile_height_px'] = 320;
    }
    unset($r);
}

$tiles = [];
$scriptCharts = [];
$lastUpdated = null;

foreach ($chartRows as $c) {
    $ts = $c['updated_at'] ?? null;
    if ($ts && ($lastUpdated === null || $ts > $lastUpdated)) {
        $lastUpdated = $ts;
    }

    $span = (int) ($c['layout_span'] ?? 12);
    if (!in_array($span, [3, 4, 6, 8, 12], true)) {
        $span = 12;
    }
    $h = (int) ($c['tile_height_px'] ?? 320);
    $h = max(200, min(800, $h));

    $cid = (int) $c['id'];
    $domId = 'chart-slot-' . $cid;

    $data = json_decode($c['data_json'] ?? '[]', true);
    if (!is_array($data)) {
        $tiles[] = [
            'domId' => $domId,
            'span' => $span,
            'height' => $h,
            'title' => $c['title'],
            'ctype' => $c['chart_type'] ?? '',
            'error' => 'Datos JSON no válidos.',
        ];
        continue;
    }

    $optsRaw = $c['options_json'] ?? '{}';
    $options = is_string($optsRaw) ? json_decode($optsRaw, true) : (is_array($optsRaw) ? $optsRaw : []);
    if (!is_array($options)) {
        $options = [];
    }

    $tiles[] = [
        'domId' => $domId,
        'span' => $span,
        'height' => $h,
        'title' => $c['title'],
        'ctype' => $c['chart_type'] ?? '',
        'error' => null,
    ];

    $scriptCharts[] = [
        'domId' => $domId,
        'type' => $c['chart_type'] ?: 'ColumnChart',
        'data' => $data,
        'options' => $options,
    ];
}

$jsonPayload = json_encode($scriptCharts, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
if ($jsonPayload === false) {
    $jsonPayload = '[]';
}

$hasDrawableCharts = $scriptCharts !== [];

$lastLabel = $lastUpdated ? date('d/m/Y H:i', strtotime($lastUpdated)) : '—';
?>
<!doctype html>
<html lang="es">
<head>
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png">
    <link rel="apple-touch-icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-180x180.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($dash['title']) ?> · <?= htmlspecialchars($dash['obs_name']) ?></title>
    <?php if ($hasDrawableCharts): ?>
    <link rel="dns-prefetch" href="https://www.gstatic.com">
    <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/modern/tablero-pbi.css">
</head>
<body class="tablero-pbi-body<?= $embed ? ' tablero-pbi-embed' : '' ?>">
<header class="pbi-chrome">
    <div class="pbi-chrome__inner">
        <div>
            <p class="pbi-breadcrumb mb-1">
                <a href="index.php">Red de Observatorios</a>
                <span class="text-secondary"> · </span>
                <a href="observatorio.php?slug=<?= htmlspecialchars(urlencode($obsSlug)) ?>"><?= htmlspecialchars($dash['obs_name']) ?></a>
            </p>
            <h1><?= htmlspecialchars($dash['title']) ?></h1>
            <?php if (!empty($dash['description'])): ?>
                <p class="small mb-0 mt-1" style="color:#c8c6c4;max-width:52rem;"><?= htmlspecialchars($dash['description']) ?></p>
            <?php endif; ?>
        </div>
        <div class="pbi-chrome__meta text-lg-end">
            <div>Actualizado: <strong><?= htmlspecialchars($lastLabel) ?></strong></div>
            <div class="mt-1">Datos: <strong>MySQL (CMS)</strong> · Excel → <em>Guardar como CSV</em></div>
        </div>
    </div>
</header>

<div class="pbi-canvas-wrap">
    <?php if ($tiles === []): ?>
        <div class="pbi-empty">
            <p class="mb-0">Aún no hay visualizaciones publicadas en este tablero.</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($tiles as $t): ?>
                <div class="col-12 col-lg-<?= (int) $t['span'] ?>">
                    <div class="pbi-tile">
                        <div class="pbi-accent-bar" aria-hidden="true"></div>
                        <div class="pbi-tile__head">
                            <h2 class="pbi-tile__title"><?= htmlspecialchars($t['title']) ?></h2>
                            <?php if ($t['ctype'] !== ''): ?>
                                <span class="pbi-tile__badge"><?= htmlspecialchars($t['ctype']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="pbi-tile__body<?= $t['error'] ? ' pbi-tile--error' : '' ?>" style="min-height: <?= (int) $t['height'] ?>px;">
                            <?php if ($t['error']): ?>
                                <?= htmlspecialchars($t['error']) ?>
                            <?php else: ?>
                                <div id="<?= htmlspecialchars($t['domId']) ?>" style="min-height: <?= (int) $t['height'] ?>px;"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($hasDrawableCharts): ?>
<script type="application/json" id="obs-tablero-json"><?= $jsonPayload ?></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script src="assets/js/modern/tablero-pbi.js" defer></script>
<?php endif; ?>

<?php if (!$embed):
$assistant_obs_slug = $obsSlug;
require __DIR__ . '/include/assistant-widget.php';
endif; ?>
</body>
</html>
