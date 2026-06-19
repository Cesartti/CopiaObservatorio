<?php

require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('rag', false);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/cms_rag.php';

$cmsTitle = 'Conocimiento para el asistente (RAG)';
$cmsNav = 'rag';
$pdo = cms_pdo();
$message = '';
$error = '';
$syncDetails = '';
$canWrite = auth_can('rag', true);

if (empty($_SESSION['rag_sync_csrf'])) {
    $_SESSION['rag_sync_csrf'] = bin2hex(random_bytes(16));
}
$ragCsrf = $_SESSION['rag_sync_csrf'];

$obsFilter = isset($_GET['obs']) ? (int) $_GET['obs'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $tok = $_POST['csrf'] ?? '';
    if (!is_string($tok) || !hash_equals($ragCsrf, $tok)) {
        $error = 'Formulario expirado o inválido. Recargue la página e intente de nuevo.';
    } elseif (!$canWrite) {
        $error = 'No tiene permiso para guardar en esta sección.';
    } else {
        $action = $_POST['action'] ?? '';
        try {
        if ($action === 'add') {
            auth_require_permission('rag', true);
            $obsId = (int) ($_POST['observatory_id'] ?? 0);
            $dim = trim($_POST['dimension'] ?? '');
            $body = trim($_POST['body_text'] ?? '');
            if ($obsId < 1 || $body === '') {
                throw new InvalidArgumentException('Observatorio y texto son obligatorios.');
            }
            if (!in_array($dim, cms_rag_dimension_options(), true)) {
                $dim = cms_rag_suggested_dimension_for_observatory_id($obsId);
            }
            $uid = auth_user()['id'] ?? null;
            $stmt = $pdo->prepare(
                'INSERT INTO cms_rag_chunks (observatory_id, dimension, title, sector, anio, tipo_precio, valor, fuente, departamento, body_text, status, created_by)
                 VALUES (?,?,?,?,?,?,?,?,?,?,\'pending\',?)'
            );
            $stmt->execute([
                $obsId,
                $dim,
                trim($_POST['title'] ?? '') ?: null,
                trim($_POST['sector'] ?? '') ?: null,
                ($_POST['anio'] === '' || $_POST['anio'] === null) ? null : (int) $_POST['anio'],
                trim($_POST['tipo_precio'] ?? '') ?: null,
                ($_POST['valor'] === '' || $_POST['valor'] === null) ? null : (float) str_replace(',', '.', (string) $_POST['valor']),
                trim($_POST['fuente'] ?? '') ?: null,
                trim($_POST['departamento'] ?? '') ?: 'Boyacá',
                $body,
                $uid,
            ]);
            $message = 'Ficha guardada (pendiente de sincronizar a la base del asistente).';
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            auth_require_permission('rag', true);
            $pdo->prepare('DELETE FROM cms_rag_chunks WHERE id = ?')->execute([(int) $_POST['id']]);
            $message = 'Ficha eliminada del CMS. Si ya estaba en PostgreSQL, el registro allí no se borra automáticamente.';
        } elseif ($action === 'requeue' && isset($_POST['id'])) {
            auth_require_permission('rag', true);
            $pdo->prepare(
                "UPDATE cms_rag_chunks SET status = 'pending', sync_error = NULL, synced_at = NULL WHERE id = ?"
            )->execute([(int) $_POST['id']]);
            $message = 'Marcada de nuevo como pendiente de sincronización.';
        } elseif ($action === 'csv' && !empty($_FILES['csv_file']['tmp_name'])) {
            auth_require_permission('rag', true);
            $obsId = (int) ($_POST['csv_observatory_id'] ?? 0);
            if ($obsId < 1) {
                throw new InvalidArgumentException('Seleccione observatorio para la carga CSV.');
            }
            $defaultDim = trim($_POST['csv_dimension'] ?? '');
            if (!in_array($defaultDim, cms_rag_dimension_options(), true)) {
                $defaultDim = cms_rag_suggested_dimension_for_observatory_id($obsId);
            }
            $path = $_FILES['csv_file']['tmp_name'];
            $fh = fopen($path, 'rb');
            if (!$fh) {
                throw new RuntimeException('No se pudo leer el archivo.');
            }
            $firstRow = fgetcsv($fh, 0, ',');
            if ($firstRow === false) {
                fclose($fh);
                throw new RuntimeException('CSV vacío.');
            }
            $lowerFirst = array_map(static fn ($c) => strtolower(trim((string) $c)), $firstRow);
            $hasHeader = in_array('texto', $lowerFirst, true) || in_array('body_text', $lowerFirst, true);
            $colMap = [];
            if ($hasHeader) {
                foreach ($lowerFirst as $idx => $name) {
                    $colMap[$name] = $idx;
                }
            } else {
                rewind($fh);
            }

            $ins = $pdo->prepare(
                'INSERT INTO cms_rag_chunks (observatory_id, dimension, title, sector, anio, tipo_precio, valor, fuente, departamento, body_text, status, created_by)
                 VALUES (?,?,?,?,?,?,?,?,?,?,\'pending\',?)'
            );
            $uid = auth_user()['id'] ?? null;
            $n = 0;
            $max = 400;
            $cell = static function (array $row, array $map, string ...$keys): string {
                foreach ($keys as $k) {
                    $i = $map[$k] ?? null;
                    if ($i !== null && array_key_exists($i, $row)) {
                        $t = trim((string) $row[$i]);
                        if ($t !== '') {
                            return $t;
                        }
                    }
                }

                return '';
            };

            while (($row = fgetcsv($fh, 0, ',')) !== false && $n < $max) {
                if ($hasHeader) {
                    $texto = $cell($row, $colMap, 'texto', 'body_text');
                    if ($texto === '') {
                        continue;
                    }
                    $dim = $cell($row, $colMap, 'dimension');
                    if ($dim === '' || !in_array($dim, cms_rag_dimension_options(), true)) {
                        $dim = $defaultDim;
                    }
                    $titleCell = $cell($row, $colMap, 'actividad', 'titulo');
                    $title = $titleCell !== '' ? $titleCell : null;
                    $sectorCell = $cell($row, $colMap, 'sector');
                    $sector = $sectorCell !== '' ? $sectorCell : null;
                    $anioS = $cell($row, $colMap, 'anio');
                    $anio = $anioS !== '' ? (int) $anioS : null;
                    $tipoCell = $cell($row, $colMap, 'tipo_precio');
                    $tipo = $tipoCell !== '' ? $tipoCell : null;
                    $valS = $cell($row, $colMap, 'valor');
                    $valor = $valS !== '' ? (float) str_replace(',', '.', $valS) : null;
                    $fuenteCell = $cell($row, $colMap, 'fuente');
                    $fuente = $fuenteCell !== '' ? $fuenteCell : null;
                    $deptoCell = $cell($row, $colMap, 'departamento');
                    $depto = $deptoCell !== '' ? $deptoCell : 'Boyacá';
                } else {
                    if (count($row) < 1 || trim((string) $row[0]) === '') {
                        continue;
                    }
                    $texto = trim((string) $row[0]);
                    $dim = $defaultDim;
                    $title = isset($row[2]) && trim((string) $row[2]) !== '' ? trim((string) $row[2]) : null;
                    $sector = isset($row[3]) && trim((string) $row[3]) !== '' ? trim((string) $row[3]) : null;
                    $anio = isset($row[1]) && trim((string) $row[1]) !== '' ? (int) $row[1] : null;
                    $tipo = isset($row[4]) && trim((string) $row[4]) !== '' ? trim((string) $row[4]) : null;
                    $valor = isset($row[5]) && trim((string) $row[5]) !== '' ? (float) str_replace(',', '.', (string) $row[5]) : null;
                    $fuente = isset($row[6]) && trim((string) $row[6]) !== '' ? trim((string) $row[6]) : null;
                    $depto = 'Boyacá';
                }
                $ins->execute([
                    $obsId,
                    $dim,
                    $title,
                    $sector,
                    $anio,
                    $tipo,
                    $valor,
                    $fuente,
                    $depto,
                    $texto,
                    $uid,
                ]);
                $n++;
            }
            fclose($fh);
            $message = "Se importaron {$n} filas (pendientes de sincronizar).";
        } elseif ($action === 'sync_now') {
            auth_require_permission('rag', true);
            require_once __DIR__ . '/../lib/rag_sync_runner.php';
            $res = cms_rag_sync_execute();
            $syncDetails = trim($res['stdout'] . (trim((string) $res['stderr']) !== '' ? "\n--- stderr ---\n" . $res['stderr'] : ''));
            if ($res['ok']) {
                $message = $res['message'];
            } else {
                $error = $res['message'];
            }
        }
        $_SESSION['rag_sync_csrf'] = bin2hex(random_bytes(16));
        $ragCsrf = $_SESSION['rag_sync_csrf'];
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'Error al procesar.';
    }
    }
}

$observatories = [];
$rows = [];
if ($pdo) {
    try {
        $observatories = $pdo->query('SELECT id, slug, name FROM observatories ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
        $sql = 'SELECT c.*, o.name AS obs_name FROM cms_rag_chunks c INNER JOIN observatories o ON o.id = c.observatory_id';
        if ($obsFilter > 0) {
            $sql .= ' WHERE c.observatory_id = ' . (int) $obsFilter;
        }
        $sql .= ' ORDER BY c.id DESC LIMIT 200';
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = $error ?: 'Ejecute la migración database/migrations/005_cms_rag_chunks.sql en MySQL.';
    }
}

$pendingCount = 0;
if ($pdo) {
    try {
        $pendingCount = (int) $pdo->query("SELECT COUNT(*) FROM cms_rag_chunks WHERE status = 'pending'")->fetchColumn();
    } catch (Throwable $e) {
    }
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-2">Conocimiento para el asistente (RAG)</h1>
            <p class="text-muted small mb-3">
                Organice textos por <strong>observatorio</strong> y <strong>dimensión</strong>. Las fichas en <code>pending</code> se vectorizan y pasan a PostgreSQL
                con el botón <strong>Sincronizar ahora</strong> o con <code class="small">app_asistente/sync_cms_chunks_to_pg.py</code>.
                En <code class="small">app_asistente/.env</code> debe existir <code class="small">SUPABASE_DB_URL</code>; MySQL puede tomarse de la config del CMS o de <code class="small">MYSQL_*</code> en ese <code>.env</code>.
            </p>
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($syncDetails !== ''): ?>
                <details class="mb-3" open>
                    <summary class="small fw-semibold">Salida del último proceso de sincronización</summary>
                    <pre class="small bg-light border rounded p-2 mt-2 mb-0" style="max-height:280px;overflow:auto;white-space:pre-wrap;"><?= htmlspecialchars($syncDetails) ?></pre>
                </details>
            <?php endif; ?>

            <?php if ($pendingCount > 0): ?>
                <div class="alert alert-warning py-2">
                    Hay <strong><?= (int) $pendingCount ?></strong> ficha(s) en estado <code>pending</code>.
                </div>
            <?php endif; ?>

            <?php if ($canWrite): ?>
            <div class="card shadow-sm border-primary border-opacity-25 mb-4">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h2 class="h6 mb-1">Sincronizar con PostgreSQL (asistente)</h2>
                        <p class="small text-muted mb-0">Ejecuta <code>sync_cms_chunks_to_pg.py</code> en el servidor. Requiere Python, dependencias (<code>pymysql</code>, etc.) y que <code>proc_open</code> no esté deshabilitado en PHP.</p>
                    </div>
                    <form method="post" class="m-0" onsubmit="this.querySelector('button').disabled=true;">
                        <input type="hidden" name="action" value="sync_now">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($ragCsrf) ?>">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up me-1"></i>Sincronizar ahora</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Filtrar listado</h2>
                    <form method="get" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Observatorio</label>
                            <select name="obs" class="form-select" onchange="this.form.submit()">
                                <option value="0">Todos</option>
                                <?php foreach ($observatories as $o): ?>
                                    <option value="<?= (int) $o['id'] ?>" <?= $obsFilter === (int) $o['id'] ? 'selected' : '' ?>><?= htmlspecialchars($o['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($canWrite): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Nueva ficha</h2>
                    <form method="post" class="row g-2">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($ragCsrf) ?>">
                        <div class="col-md-4">
                            <label class="form-label">Observatorio</label>
                            <select name="observatory_id" class="form-select" required id="ragObs">
                                <?php foreach ($observatories as $o): ?>
                                    <option value="<?= (int) $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dimensión (filtro del chat)</label>
                            <select name="dimension" class="form-select" id="ragDim">
                                <?php foreach (cms_rag_dimension_options() as $d): ?>
                                    <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Año (opcional)</label>
                            <input type="number" name="anio" class="form-control" placeholder="2024" min="1990" max="2100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Título / actividad (opcional)</label>
                            <input type="text" name="title" class="form-control" placeholder="Ej: Empleo municipal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sector (opcional)</label>
                            <input type="text" name="sector" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipo precio / tipo dato (opcional)</label>
                            <input type="text" name="tipo_precio" class="form-control" placeholder="Ej: Indicador CMS">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Valor numérico (opcional)</label>
                            <input type="text" name="valor" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fuente (opcional)</label>
                            <input type="text" name="fuente" class="form-control" placeholder="Secretaría / enlace">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Texto para el asistente <span class="text-danger">*</span></label>
                            <textarea name="body_text" class="form-control" rows="6" required placeholder="Redacte párrafos claros con cifras, año y contexto. Esto es lo que se buscará por similitud."></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Guardar ficha</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Carga masiva CSV</h2>
                    <p class="small text-muted mb-2">
                        Con cabecera: columnas <code>texto</code> (obligatoria), opcionales <code>dimension</code>, <code>anio</code>, <code>actividad</code> o <code>titulo</code>, <code>sector</code>, <code>tipo_precio</code>, <code>valor</code>, <code>fuente</code>, <code>departamento</code>.
                        Sin cabecera: <code>texto,anio,actividad,sector,tipo_precio,valor,fuente</code>.
                    </p>
                    <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
                        <input type="hidden" name="action" value="csv">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($ragCsrf) ?>">
                        <div class="col-md-3">
                            <label class="form-label">Observatorio</label>
                            <select name="csv_observatory_id" class="form-select" required>
                                <?php foreach ($observatories as $o): ?>
                                    <option value="<?= (int) $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dimensión por defecto</label>
                            <select name="csv_dimension" class="form-select">
                                <?php foreach (cms_rag_dimension_options() as $d): ?>
                                    <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Archivo .csv</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">Importar</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-sm align-middle bg-white shadow-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Observatorio</th>
                            <th>Dimensión</th>
                            <th>Estado</th>
                            <th>Texto (inicio)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= (int) $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['obs_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($r['dimension']) ?></td>
                            <td>
                                <span class="badge <?= $r['status'] === 'synced' ? 'text-bg-success' : ($r['status'] === 'error' ? 'text-bg-danger' : 'text-bg-warning') ?>">
                                    <?= htmlspecialchars($r['status']) ?>
                                </span>
                                <?php if (!empty($r['sync_error'])): ?>
                                    <br><small class="text-danger"><?php
                                        $se = (string) $r['sync_error'];
                                        echo htmlspecialchars(function_exists('mb_substr') ? mb_substr($se, 0, 120) : substr($se, 0, 120));
                                    ?></small>
                                <?php endif; ?>
                            </td>
                            <td><small><?php
                                $snippet = (string) $r['body_text'];
                                $cut = function_exists('mb_substr') ? mb_substr($snippet, 0, 140) : substr($snippet, 0, 140);
                                echo htmlspecialchars($cut);
                                echo (strlen($snippet) > 140 ? '…' : '');
                            ?></small></td>
                            <td class="text-end">
                                <?php if ($canWrite): ?>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="requeue">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($ragCsrf) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Reintentar sync</button>
                                </form>
                                <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta ficha del CMS?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($ragCsrf) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($rows === [] && $error === ''): ?>
                        <tr><td colspan="6" class="text-muted">Sin fichas.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <script>
            (function () {
                var obs = document.getElementById('ragObs');
                var dim = document.getElementById('ragDim');
                var map = <?= json_encode([
                    1 => 'Económica',
                    2 => 'Social',
                    3 => 'Ambiental',
                    4 => 'CTI',
                    5 => 'Género',
                ], JSON_UNESCAPED_UNICODE) ?>;
                function syncDim() {
                    if (!obs || !dim) return;
                    var id = parseInt(obs.value, 10);
                    if (map[id]) {
                        for (var i = 0; i < dim.options.length; i++) {
                            if (dim.options[i].value === map[id]) { dim.selectedIndex = i; break; }
                        }
                    }
                }
                if (obs) obs.addEventListener('change', syncDim);
                syncDim();
            })();
            </script>
<?php require __DIR__ . '/includes/footer.php'; ?>
