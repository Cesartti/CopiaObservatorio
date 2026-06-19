<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('charts', true);
require_once __DIR__ . '/../config/database.php';

$cmsTitle = 'Tableros';
$cmsNav = 'charts';
$pdo = cms_pdo();
$message = '';
$error = '';

$obsRows = [];
if ($pdo) {
    try {
        $obsRows = $pdo->query('SELECT id, slug, name FROM observatories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = 'No existe la tabla observatories. Ejecute schema.sql y datos de observatorios.';
    }
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_dash') {
    try {
        $slug = trim($_POST['slug'] ?? '');
        $oid = (int) ($_POST['observatory_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        if ($slug === '' || $oid < 1 || $title === '') {
            throw new RuntimeException('Complete observatorio, título y slug.');
        }
        $stmt = $pdo->prepare('INSERT INTO cms_dashboards (observatory_id, title, slug, description, sort_order, is_active) VALUES (?,?,?,?,0,1)');
        $stmt->execute([$oid, $title, $slug, trim($_POST['description'] ?? '')]);
        $message = 'Tablero creado. Añada gráficos desde el enlace “Gráficos”.';
    } catch (Throwable $e) {
        $error = 'No se pudo crear (¿slug duplicado en ese observatorio?).';
    }
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_dash') {
    try {
        $pdo->prepare('DELETE FROM cms_dashboards WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
        $message = 'Tablero eliminado.';
    } catch (Throwable $e) {
        $error = 'No se pudo eliminar.';
    }
}

$dashboards = [];
if ($pdo) {
    try {
        $dashboards = $pdo->query(
            'SELECT d.*, o.name AS obs_name, o.slug AS obs_slug FROM cms_dashboards d INNER JOIN observatories o ON o.id = d.observatory_id ORDER BY o.name ASC, d.sort_order ASC, d.id ASC'
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
    }
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-3">Tableros por observatorio</h1>
            <p class="small text-muted">Cada tablero agrupa visualizaciones (estilo <strong>Power BI</strong>: rejilla y tarjetas en la vista pública) con <a href="https://developers.google.com/chart/interactive/docs/gallery" target="_blank" rel="noopener">Google Charts</a>. Los datos se guardan en <strong>MySQL</strong> al subir un <strong>CSV</strong> exportado desde Excel.</p>
            <p class="small text-muted mb-0">Exploración rápida con Excel/CSV en el navegador (ECharts + SheetJS): <a href="<?= htmlspecialchars(app_url('website/dashboard/tablero-datos.php')) ?>">Tablero exploratorio</a> (requiere sesión en el panel de visitas).</p>
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Nuevo tablero</h2>
                    <form method="post" class="row g-2 align-items-end">
                        <input type="hidden" name="action" value="add_dash">
                        <div class="col-md-3">
                            <label class="form-label">Observatorio</label>
                            <select name="observatory_id" class="form-select" required>
                                <?php foreach ($obsRows as $o): ?>
                                    <option value="<?= (int) $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label">Título</label><input type="text" name="title" class="form-control" required></div>
                        <div class="col-md-2"><label class="form-label">Slug URL</label><input type="text" name="slug" class="form-control" required placeholder="ej: coyuntura"></div>
                        <div class="col-md-3"><label class="form-label">Descripción</label><input type="text" name="description" class="form-control"></div>
                        <div class="col-md-1"><button class="btn btn-primary w-100" type="submit">Crear</button></div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm bg-white shadow-sm align-middle">
                    <thead><tr><th>Observatorio</th><th>Tablero</th><th>Slug</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($dashboards as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['obs_name']) ?></td>
                            <td><?= htmlspecialchars($d['title']) ?></td>
                            <td><code><?= htmlspecialchars($d['slug']) ?></code></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-primary" href="<?= htmlspecialchars(app_url('website/cms/grafico.php?dashboard_id=' . (int) $d['id'])) ?>">Gráficos</a>
                                <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars(app_url('website/tableros-cms.php?obs=' . urlencode($d['obs_slug']) . '&dash=' . urlencode($d['slug']))) ?>" target="_blank">Vista pública</a>
                                <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar tablero y sus gráficos?');">
                                    <input type="hidden" name="action" value="delete_dash">
                                    <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
