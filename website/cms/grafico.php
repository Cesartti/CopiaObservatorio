<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('charts', true);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/cms_csv.php';

$pdo = cms_pdo();
$dashboardId = (int) ($_GET['dashboard_id'] ?? 0);
$chartId = (int) ($_GET['chart_id'] ?? 0);
$message = '';
$error = '';
if (isset($_GET['saved'])) {
    $message = 'Gráfico creado.';
}

$chartTypes = [
    'ColumnChart' => 'Columnas',
    'BarChart' => 'Barras',
    'LineChart' => 'Líneas',
    'AreaChart' => 'Área',
    'PieChart' => 'Pastel',
    'ScatterChart' => 'Dispersión',
    'ComboChart' => 'Combinado',
    'Histogram' => 'Histograma',
    'GeoChart' => 'Mapa / Geo (regiones)',
    'Table' => 'Tabla',
];

if (!$pdo || $dashboardId < 1) {
    $error = 'Tablero no válido.';
}

$dash = null;
if ($pdo && $dashboardId > 0) {
    $stmt = $pdo->prepare('SELECT d.*, o.slug AS obs_slug FROM cms_dashboards d INNER JOIN observatories o ON o.id = d.observatory_id WHERE d.id = ?');
    $stmt->execute([$dashboardId]);
    $dash = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$dash) {
    $error = 'No se encontró el tablero.';
}

$chart = [
    'title' => '',
    'chart_type' => 'ColumnChart',
    'options_json' => '{}',
    'data_json' => '[["Categoría","Valor"],["A",1],["B",2]]',
    'sort_order' => 0,
    'layout_span' => 12,
    'tile_height_px' => 320,
];

if ($pdo && $chartId > 0) {
    $st = $pdo->prepare('SELECT * FROM cms_charts WHERE id = ? AND dashboard_id = ?');
    $st->execute([$chartId, $dashboardId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $chart = array_merge($chart, $row);
        if (isset($chart['options_json']) && !is_string($chart['options_json'])) {
            $chart['options_json'] = json_encode($chart['options_json'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
}

if ($pdo && $dash && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save_chart') {
            $title = trim($_POST['title'] ?? '');
            $ctype = trim($_POST['chart_type'] ?? 'ColumnChart');
            $opts = trim($_POST['options_json'] ?? '{}');
            json_decode($opts);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('JSON de opciones inválido.');
            }
            $dataJson = trim($_POST['data_json'] ?? '');
            if (isset($_FILES['datafile']) && is_uploaded_file($_FILES['datafile']['tmp_name'])) {
                $upName = (string) ($_FILES['datafile']['name'] ?? '');
                $ext = strtolower(pathinfo($upName, PATHINFO_EXTENSION));
                if (in_array($ext, ['xlsx', 'xls'], true)) {
                    throw new RuntimeException('Suba CSV: en Excel use Archivo → Guardar como → CSV UTF-8 (delimitado por comas).');
                }
                $matrix = cms_csv_file_to_matrix($_FILES['datafile']['tmp_name']);
                $dataJson = cms_matrix_to_json_string($matrix);
                $fn = $upName !== '' ? $upName : 'data.csv';
            } else {
                $fn = null;
            }
            if ($dataJson === '') {
                throw new RuntimeException('Indique datos JSON o suba un CSV.');
            }
            json_decode($dataJson);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Datos JSON inválidos.');
            }
            $sortOrder = max(0, min(32767, (int) ($_POST['sort_order'] ?? 0)));
            $layoutSpan = (int) ($_POST['layout_span'] ?? 12);
            if (!in_array($layoutSpan, [3, 4, 6, 8, 12], true)) {
                $layoutSpan = 12;
            }
            $thRaw = trim((string) ($_POST['tile_height_px'] ?? ''));
            $tileHPx = $thRaw === '' ? 320 : max(200, min(800, (int) $thRaw));

            $uid = auth_user()['id'] ?? null;
            if ($chartId > 0) {
                $sql = 'UPDATE cms_charts SET title=?, chart_type=?, options_json=?, data_json=?, source_filename=COALESCE(?, source_filename), sort_order=?, layout_span=?, tile_height_px=?, updated_at=NOW() WHERE id=? AND dashboard_id=?';
                $pdo->prepare($sql)->execute([$title, $ctype, $opts, $dataJson, $fn, $sortOrder, $layoutSpan, $tileHPx, $chartId, $dashboardId]);
                $message = 'Gráfico actualizado.';
            } else {
                $sql = 'INSERT INTO cms_charts (dashboard_id, title, chart_type, options_json, data_json, source_filename, sort_order, layout_span, tile_height_px, is_active, created_by) VALUES (?,?,?,?,?,?,?,?,?,1,?)';
                $pdo->prepare($sql)->execute([$dashboardId, $title, $ctype, $opts, $dataJson, $fn, $sortOrder, $layoutSpan, $tileHPx, $uid]);
                $newId = (int) $pdo->lastInsertId();
                header('Location: ' . app_url('website/cms/grafico.php?dashboard_id=' . $dashboardId . '&chart_id=' . $newId . '&saved=1'));
                exit;
            }
        } elseif ($action === 'delete_chart' && isset($_POST['cid'])) {
            $pdo->prepare('DELETE FROM cms_charts WHERE id = ? AND dashboard_id = ?')->execute([(int) $_POST['cid'], $dashboardId]);
            $message = 'Gráfico eliminado.';
            $chartId = 0;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'Error al guardar.';
    }
}

$charts = [];
if ($pdo && $dashboardId) {
    try {
        try {
            $stc = $pdo->prepare(
                'SELECT id, title, chart_type, sort_order, COALESCE(layout_span,12) AS layout_span FROM cms_charts WHERE dashboard_id = ? ORDER BY sort_order ASC, id ASC'
            );
            $stc->execute([$dashboardId]);
            $charts = $stc->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $stc = $pdo->prepare('SELECT id, title, chart_type, sort_order FROM cms_charts WHERE dashboard_id = ? ORDER BY sort_order ASC, id ASC');
            $stc->execute([$dashboardId]);
            $charts = $stc->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Throwable $e) {
    }
}

if (!$dash) {
    $cmsTitle = 'Error';
    $cmsNav = 'charts';
    require __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-danger">' . htmlspecialchars($error ?: 'Tablero no encontrado.') . '</div>';
    echo '<p><a href="' . htmlspecialchars(app_url('website/cms/tableros.php')) . '">Volver a tableros</a></p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$cmsTitle = 'Gráficos del tablero';
$cmsNav = 'charts';
require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-2">Gráficos · <?= htmlspecialchars($dash['title'] ?? '') ?></h1>
            <p class="small"><a href="<?= htmlspecialchars(app_url('website/cms/tableros.php')) ?>">← Volver a tableros</a>
                · <a target="_blank" rel="noopener" href="<?= htmlspecialchars(app_url('website/tableros-cms.php?obs=' . urlencode($dash['obs_slug'] ?? '') . '&dash=' . urlencode($dash['slug'] ?? ''))) ?>">Vista pública</a></p>
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header">Gráficos en este tablero</div>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($charts as $c): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="<?= htmlspecialchars(app_url('website/cms/grafico.php?dashboard_id=' . $dashboardId . '&chart_id=' . (int) $c['id'])) ?>"><?= htmlspecialchars($c['title']) ?></a>
                                    <small class="text-muted"><?= htmlspecialchars($c['chart_type']) ?><?php if (isset($c['layout_span'])): ?> · <?= (int) $c['layout_span'] ?>/12<?php endif; ?> · ord.<?= (int) ($c['sort_order'] ?? 0) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="h6"><?= $chartId ? 'Editar gráfico' : 'Nuevo gráfico' ?></h2>
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="save_chart">
                                <div class="mb-2">
                                    <label class="form-label">Título</label>
                                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($chart['title']) ?>">
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Orden en tablero</label>
                                        <input type="number" name="sort_order" class="form-control" min="0" max="32767" value="<?= (int) ($chart['sort_order'] ?? 0) ?>">
                                        <small class="text-muted">Menor = primero (estilo Power BI).</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Ancho en rejilla</label>
                                        <?php $sp = (int) ($chart['layout_span'] ?? 12); ?>
                                        <select name="layout_span" class="form-select">
                                            <?php foreach ([12 => 'Completo (12)', 8 => '2/3 (8)', 6 => 'Mitad (6)', 4 => '1/3 (4)', 3 => '1/4 (3)'] as $sv => $sl): ?>
                                                <option value="<?= $sv ?>" <?= $sp === $sv ? 'selected' : '' ?>><?= htmlspecialchars($sl) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Alto tarjeta (px)</label>
                                        <input type="number" name="tile_height_px" class="form-control" min="200" max="800" value="<?= (int) ($chart['tile_height_px'] ?? 320) ?>">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tipo de gráfico</label>
                                    <select name="chart_type" class="form-select">
                                        <?php foreach ($chartTypes as $k => $label): ?>
                                            <option value="<?= htmlspecialchars($k) ?>" <?= ($chart['chart_type'] ?? '') === $k ? 'selected' : '' ?>><?= htmlspecialchars($k . ' — ' . $label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Referencia: <a href="https://developers.google.com/chart/interactive/docs/gallery" target="_blank" rel="noopener">galería Google Charts</a>. Para mapas use GeoChart y códigos de región en la primera columna.</small>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Opciones (JSON)</label>
                                    <textarea name="options_json" class="form-control font-monospace" rows="4"><?= htmlspecialchars($chart['options_json'] ?? '{}') ?></textarea>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Datos (JSON) — matriz tipo <code>[["Col1","Col2"],["a",1]]</code></label>
                                    <textarea name="data_json" class="form-control font-monospace" rows="8"><?= htmlspecialchars($chart['data_json'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">O subir archivo (CSV desde Excel)</label>
                                    <input type="file" name="datafile" class="form-control" accept=".csv,.txt,text/csv">
                                    <small class="text-muted">Excel: <strong>Guardar como</strong> → <em>CSV UTF-8 (delimitado por comas)</em>. La primera fila son encabezados. Los datos se guardan en MySQL como base del tablero.</small>
                                </div>
                                <button class="btn btn-primary" type="submit">Guardar</button>
                                <?php if ($chartId): ?>
                                    <a class="btn btn-outline-secondary" target="_blank" href="<?= htmlspecialchars(app_url('website/chart-view.php?id=' . $chartId)) ?>">Previsualizar</a>
                                <?php endif; ?>
                            </form>
                            <?php if ($chartId): ?>
                                <form method="post" class="mt-3" onsubmit="return confirm('¿Eliminar gráfico?');">
                                    <input type="hidden" name="action" value="delete_chart">
                                    <input type="hidden" name="cid" value="<?= (int) $chartId ?>">
                                    <button class="btn btn-outline-danger btn-sm" type="submit">Eliminar este gráfico</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
