<?php
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('charts', true);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/indicator_metadata.php';

$cmsTitle = 'Indicadores · Hoja de vida';
$cmsNav = 'indicators';
$pdo = cms_pdo();
$message = '';
$error = '';

$obsRows = [];
if ($pdo) {
    try {
        $obsRows = $pdo->query('SELECT id, slug, name FROM observatories ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { $error = 'BD no lista.'; }
}

/* ── Detect if migration 012 has been applied ───────────── */
$migrationNeeded = false;
if ($pdo) {
    try {
        $col = $pdo->query("SHOW COLUMNS FROM indicators LIKE 'category_1'")->fetch(PDO::FETCH_ASSOC);
        $migrationNeeded = empty($col);
    } catch (Throwable $e) { $migrationNeeded = true; }
}

/* ── Handle one-click migration ─────────────────────────── */
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'run_migration') {
    $migrationCols = [
        'category_1'           => "ADD COLUMN category_1 VARCHAR(160) NULL AFTER observatory_id",
        'category_2'           => "ADD COLUMN category_2 VARCHAR(160) NULL AFTER category_1",
        'tags'                 => "ADD COLUMN tags VARCHAR(255) NULL AFTER category_2",
        'thematic_breakdown'   => "ADD COLUMN thematic_breakdown TEXT NULL",
        'geographic_breakdown' => "ADD COLUMN geographic_breakdown VARCHAR(160) NULL",
        'definition'           => "ADD COLUMN definition TEXT NULL",
        'calculation_formula'  => "ADD COLUMN calculation_formula TEXT NULL",
        'baseline_date'        => "ADD COLUMN baseline_date VARCHAR(80) NULL",
        'delivery_form'        => "ADD COLUMN delivery_form VARCHAR(255) NULL",
        'source_link'          => "ADD COLUMN source_link VARCHAR(512) NULL",
        'actors'               => "ADD COLUMN actors TEXT NULL",
        'responsible_entity'   => "ADD COLUMN responsible_entity VARCHAR(255) NULL",
        'observations'         => "ADD COLUMN observations TEXT NULL",
        'availability_status'  => "ADD COLUMN availability_status VARCHAR(40) DEFAULT 'DISPONIBLE'",
    ];
    $applied = []; $skipped = []; $failed = [];
    foreach ($migrationCols as $colName => $alterSql) {
        try {
            $exists = $pdo->query("SHOW COLUMNS FROM indicators LIKE " . $pdo->quote($colName))->fetch();
            if ($exists) { $skipped[] = $colName; continue; }
            $pdo->exec("ALTER TABLE indicators $alterSql");
            $applied[] = $colName;
        } catch (Throwable $e) {
            $failed[] = "$colName: " . $e->getMessage();
        }
    }
    try { $pdo->exec("ALTER TABLE indicators CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"); } catch (Throwable $e) {}
    try { $pdo->exec("CREATE INDEX idx_indicators_category_1 ON indicators(category_1)"); } catch (Throwable $e) {}
    try { $pdo->exec("CREATE INDEX idx_indicators_availability ON indicators(availability_status)"); } catch (Throwable $e) {}

    if (!empty($failed)) {
        $error = 'Errores: ' . htmlspecialchars(implode(' | ', $failed));
    } else {
        $message = 'Migración aplicada correctamente. <strong>' . count($applied) . '</strong> columnas nuevas agregadas'
                 . (count($skipped) ? ', ' . count($skipped) . ' ya existían' : '') . '. Ahora puede importar el CSV.';
        $migrationNeeded = false;
    }
}

/* ── Handle CSV bulk import ─────────────────────────────── */
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'import_csv') {
    if (empty($_FILES['csv_file']['tmp_name']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Seleccione un archivo CSV válido.';
    } else {
        $stats = im_import_csv($pdo, $_FILES['csv_file']['tmp_name']);
        if (isset($stats['error'])) {
            $error = $stats['error'];
        } else {
            $message = sprintf(
                'Importación completa: <strong>%d</strong> insertados, <strong>%d</strong> actualizados, <strong>%d</strong> omitidos.',
                $stats['inserted'], $stats['updated'], $stats['skipped']
            );
            if (!empty($stats['errors'])) {
                $message .= '<br><small class="text-danger">' . htmlspecialchars(implode(' | ', array_slice($stats['errors'], 0, 3))) . '</small>';
            }
        }
    }
}

/* ── Handle individual save ─────────────────────────────── */
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    try {
        $row = [];
        foreach (im_fields() as $f) {
            $row[$f] = $_POST[$f] ?? '';
        }
        $res = im_upsert($pdo, $row);
        if (strpos($res, 'skip') === 0) {
            $error = 'Datos inválidos: se requiere código numérico y observatorio.';
        } else {
            $message = 'Indicador ' . htmlspecialchars($row['id']) . ' ' . ($res === 'inserted' ? 'creado' : 'actualizado') . '.';
        }
    } catch (Throwable $e) { $error = $e->getMessage(); }
}

/* ── Handle delete ───────────────────────────────────────── */
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete' && !empty($_POST['id'])) {
    try {
        $pdo->prepare('DELETE FROM indicators WHERE id = ?')->execute([(int) $_POST['id']]);
        $message = 'Indicador eliminado.';
    } catch (Throwable $e) { $error = $e->getMessage(); }
}

/* ── Filters ─────────────────────────────────────────────── */
$fObs = isset($_GET['obs']) ? (int) $_GET['obs'] : 0;
$fCat = trim($_GET['cat'] ?? '');
$fSearch = trim($_GET['q'] ?? '');

$rows = [];
$categories = [];
if ($pdo) {
    try {
        $sql = 'SELECT i.*, o.name AS obs_name FROM indicators i LEFT JOIN observatories o ON o.id = i.observatory_id WHERE 1=1';
        $p = [];
        if ($fObs > 0)  { $sql .= ' AND i.observatory_id = ?'; $p[] = $fObs; }
        if ($fCat !== '') { $sql .= ' AND i.category_1 = ?'; $p[] = $fCat; }
        if ($fSearch !== '') { $sql .= ' AND (i.title LIKE ? OR CAST(i.id AS CHAR) LIKE ?)'; $p[] = "%$fSearch%"; $p[] = "%$fSearch%"; }
        $sql .= ' ORDER BY i.observatory_id, i.category_1, i.category_2, i.id LIMIT 500';
        $st = $pdo->prepare($sql);
        $st->execute($p);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $categories = $pdo->query('SELECT DISTINCT category_1 FROM indicators WHERE category_1 IS NOT NULL AND category_1 != "" ORDER BY category_1')->fetchAll(PDO::FETCH_COLUMN) ?: [];
    } catch (Throwable $e) { $error = 'Ejecute la migración 012_indicator_metadata.sql — ' . $e->getMessage(); }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = ($editId > 0 && $pdo) ? im_get_one($pdo, $editId) : null;

require __DIR__ . '/includes/header.php';
?>
<h1 class="h4 mb-1">Indicadores · Hoja de vida</h1>
<p class="small text-muted mb-3">Gestione la hoja de vida completa de los indicadores. Puede importar masivamente desde CSV o editar uno por uno.</p>

<?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<?php if ($migrationNeeded): ?>
<div class="card border-warning shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h6 mb-2 text-warning"><i class="fa-solid fa-triangle-exclamation"></i> Migración pendiente</h2>
        <p class="small mb-3">La tabla <code>indicators</code> aún no tiene las 14 columnas de la Hoja de Vida (categorías, definición, fórmula, fuentes, responsables, etc.). Pulse el botón para aplicarlas ahora — es seguro: solo agrega columnas que no existen y no toca datos.</p>
        <form method="post" onsubmit="return confirm('¿Aplicar la migración 012 ahora? Se agregarán 14 columnas a la tabla indicators.');">
            <input type="hidden" name="action" value="run_migration">
            <button class="btn btn-warning fw-semibold" type="submit"><i class="fa-solid fa-database"></i> Aplicar migración 012 ahora</button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Bulk import -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h6 mb-2"><i class="fa-solid fa-file-csv"></i> Carga masiva desde CSV</h2>
        <p class="small text-muted mb-2">
            Columnas esperadas (en este orden o usando los encabezados): <code>id, observatory_id, title, category_1, category_2, tags, unit, thematic_breakdown, geographic_breakdown, definition, calculation_formula, periodicity, baseline_date, delivery_form, source, source_link, actors, responsible_entity, observations, availability_status</code>.
            <strong>observatory_id</strong>: 1=Económico, 2=Social, 3=Ambiente, 4=CTI, 5=Género. Los registros existentes (mismo <code>id</code>) se actualizan; los nuevos se insertan.
        </p>
        <div class="alert alert-info py-2 px-3 small mb-3" style="border-left:4px solid #0dcaf0">
            <i class="fa-solid fa-download"></i> Plantilla precargada con los 238 indicadores de <code>Base_consulta_observatorios_V1.xlsx</code>:
            <a href="<?= htmlspecialchars(app_url('database/seeds/indicators_base_general.csv')) ?>" download><strong>descargar CSV</strong></a>
        </div>
        <form method="post" enctype="multipart/form-data" class="d-flex gap-2 align-items-end flex-wrap">
            <input type="hidden" name="action" value="import_csv">
            <div class="flex-grow-1">
                <label class="form-label fw-semibold small">Archivo CSV UTF-8</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" required>
            </div>
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-upload"></i> Importar</button>
        </form>
    </div>
</div>

<!-- Filters -->
<form method="get" class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Observatorio</label>
                <select name="obs" class="form-select form-select-sm">
                    <option value="0">Todos</option>
                    <?php foreach ($obsRows as $o): ?>
                        <option value="<?= (int) $o['id'] ?>" <?= $fObs === (int) $o['id'] ? 'selected' : '' ?>><?= htmlspecialchars($o['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Categoría 1er orden</label>
                <select name="cat" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= $fCat === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Buscar (nombre o código)</label>
                <input type="search" name="q" value="<?= htmlspecialchars($fSearch) ?>" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm w-100" type="submit"><i class="fa-solid fa-filter"></i> Filtrar</button>
            </div>
        </div>
    </div>
</form>

<!-- Edit form -->
<?php if ($editRow || isset($_GET['new'])): ?>
<div class="card shadow-sm mb-4 border-primary">
    <div class="card-body">
        <h2 class="h6 mb-3"><?= $editRow ? '<i class="fa-solid fa-pencil"></i> Editar indicador #' . (int) $editRow['id'] : '<i class="fa-solid fa-plus"></i> Nuevo indicador' ?></h2>
        <form method="post">
            <input type="hidden" name="action" value="save">
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Código*</label>
                    <input type="number" name="id" class="form-control form-control-sm" value="<?= $editRow ? (int) $editRow['id'] : '' ?>" <?= $editRow ? 'readonly' : 'required' ?>>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Observatorio*</label>
                    <select name="observatory_id" class="form-select form-select-sm" required>
                        <?php foreach ($obsRows as $o): ?>
                            <option value="<?= (int) $o['id'] ?>" <?= $editRow && (int) $editRow['observatory_id'] === (int) $o['id'] ? 'selected' : '' ?>><?= htmlspecialchars($o['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-7">
                    <label class="form-label small fw-semibold">Nombre del indicador*</label>
                    <input type="text" name="title" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars($editRow['title']) : '' ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Categoría 1er orden</label>
                    <input type="text" name="category_1" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['category_1'] ?? '')) : '' ?>" list="catList1">
                    <datalist id="catList1"><?php foreach ($categories as $c): ?><option value="<?= htmlspecialchars($c) ?>"><?php endforeach; ?></datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Categoría 2do orden</label>
                    <input type="text" name="category_2" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['category_2'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Etiquetas</label>
                    <input type="text" name="tags" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['tags'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Unidad de medida</label>
                    <input type="text" name="unit" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['unit'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Periodicidad</label>
                    <input type="text" name="periodicity" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['periodicity'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Fecha línea base</label>
                    <input type="text" name="baseline_date" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['baseline_date'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Disponibilidad</label>
                    <select name="availability_status" class="form-select form-select-sm">
                        <?php foreach (['DISPONIBLE','EN PROCESO','NO DISPONIBLE','PENDIENTE'] as $s): ?>
                            <option value="<?= $s ?>" <?= $editRow && ($editRow['availability_status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Desagregación temática</label>
                    <textarea name="thematic_breakdown" rows="2" class="form-control form-control-sm"><?= $editRow ? htmlspecialchars((string) ($editRow['thematic_breakdown'] ?? '')) : '' ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Desagregación geográfica</label>
                    <input type="text" name="geographic_breakdown" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['geographic_breakdown'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-semibold">Definición del indicador</label>
                    <textarea name="definition" rows="2" class="form-control form-control-sm"><?= $editRow ? htmlspecialchars((string) ($editRow['definition'] ?? '')) : '' ?></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-semibold">Cálculo / fórmula</label>
                    <textarea name="calculation_formula" rows="2" class="form-control form-control-sm"><?= $editRow ? htmlspecialchars((string) ($editRow['calculation_formula'] ?? '')) : '' ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Forma de entrega</label>
                    <input type="text" name="delivery_form" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['delivery_form'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Entidad responsable</label>
                    <input type="text" name="responsible_entity" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['responsible_entity'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Fuentes de información</label>
                    <input type="text" name="source" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['source'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Enlace de la fuente</label>
                    <input type="text" name="source_link" class="form-control form-control-sm" value="<?= $editRow ? htmlspecialchars((string) ($editRow['source_link'] ?? '')) : '' ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-semibold">Actores involucrados</label>
                    <textarea name="actors" rows="2" class="form-control form-control-sm"><?= $editRow ? htmlspecialchars((string) ($editRow['actors'] ?? '')) : '' ?></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-semibold">Observaciones</label>
                    <textarea name="observations" rows="2" class="form-control form-control-sm"><?= $editRow ? htmlspecialchars((string) ($editRow['observations'] ?? '')) : '' ?></textarea>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-check"></i> <?= $editRow ? 'Guardar cambios' : 'Crear indicador' ?></button>
                <a class="btn btn-outline-secondary" href="<?= htmlspecialchars(app_url('website/cms/indicators.php')) ?>">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
    <a class="btn btn-success btn-sm mb-3" href="<?= htmlspecialchars(app_url('website/cms/indicators.php?new=1')) ?>"><i class="fa-solid fa-plus"></i> Nuevo indicador manual</a>
<?php endif; ?>

<!-- Table -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Observatorio</th>
                    <th>Nombre</th>
                    <th>Categoría 1°</th>
                    <th>Categoría 2°</th>
                    <th>Unidad</th>
                    <th>Periodicidad</th>
                    <th>Disponibilidad</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$rows): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Sin indicadores. Importe el CSV arriba o cree uno nuevo.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><code><?= (int) $r['id'] ?></code></td>
                        <td class="small"><?= htmlspecialchars((string) ($r['obs_name'] ?? '')) ?></td>
                        <td class="small" style="max-width:300px"><?= htmlspecialchars($r['title']) ?></td>
                        <td class="small"><?= htmlspecialchars((string) ($r['category_1'] ?? '—')) ?></td>
                        <td class="small"><?= htmlspecialchars((string) ($r['category_2'] ?? '—')) ?></td>
                        <td class="small"><?= htmlspecialchars((string) ($r['unit'] ?? '—')) ?></td>
                        <td class="small"><?= htmlspecialchars((string) ($r['periodicity'] ?? '—')) ?></td>
                        <td>
                            <?php $st = $r['availability_status'] ?? ''; $cls = $st === 'DISPONIBLE' ? 'bg-success' : ($st === 'NO DISPONIBLE' ? 'bg-danger' : 'bg-secondary'); ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($st ?: '—') ?></span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars(app_url('website/cms/indicators.php?edit=' . (int) $r['id'])) ?>"><i class="fa-solid fa-pencil"></i></a>
                            <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar indicador <?= (int) $r['id'] ?>?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<p class="small text-muted mt-2">Se muestran hasta 500 filas. Use los filtros para acotar.</p>

<?php require __DIR__ . '/includes/footer.php'; ?>
