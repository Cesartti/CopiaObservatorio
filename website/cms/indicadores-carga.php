<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('charts', true);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/indicator_files.php';
require_once __DIR__ . '/../lib/indicator_metadata.php';

$cmsTitle = 'Cargar / actualizar indicadores';
$cmsNav = 'ind_carga';
$pdo = cms_pdo();
$message = '';
$error = '';
$tab = ($_GET['tab'] ?? $_POST['tab'] ?? 'a') === 'b' ? 'b' : 'a';

$CACHE_DIR = __DIR__ . '/../data/cache';
if (!is_dir($CACHE_DIR)) {
    @mkdir($CACHE_DIR, 0775, true);
}

/** Verifica la firma real de un .xlsx (zip). */
function inf_is_xlsx(string $tmp): bool
{
    $fh = @fopen($tmp, 'rb');
    if (!$fh) {
        return false;
    }
    $head = fread($fh, 4);
    fclose($fh);

    return str_starts_with($head, "PK\x03\x04");
}

/**
 * Parsea el archivo subido (xlsx/csv) → lista ordenada de hojas y lo cachea.
 * Devuelve [token, list].
 *
 * @throws RuntimeException
 */
function inf_parse_upload(array $file, int $id, string $cacheDir): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Suba un archivo Excel (.xlsx) o CSV.');
    }
    if (($file['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new RuntimeException('El archivo supera el límite de 8 MB.');
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext === 'xlsx') {
        if (!inf_is_xlsx($file['tmp_name'])) {
            throw new RuntimeException('El archivo no es un Excel válido.');
        }
        $sheets = inf_xlsx_to_sheets($file['tmp_name']);
    } elseif ($ext === 'csv') {
        $sheets = ['Datos' => inf_csv_to_rows($file['tmp_name'])];
    } else {
        throw new RuntimeException('Formato no permitido. Use .xlsx o .csv.');
    }
    $list = [];
    foreach ($sheets as $name => $rows) {
        if (inf_rows_nonempty($rows)) {
            $list[] = ['name' => (string) $name, 'rows' => array_values($rows)];
        }
    }
    if ($list === []) {
        throw new RuntimeException('No se encontraron datos en el archivo.');
    }
    $token = bin2hex(random_bytes(8));
    file_put_contents($cacheDir . '/ind_upload_' . $token . '.json', json_encode([
        'id' => $id,
        'sheets' => $list,
    ], JSON_UNESCAPED_UNICODE));

    return [$token, $list];
}

$parsed = null;      // hojas parseadas para previsualización
$parsedToken = '';
$parsedId = 0;
$existingInfos = []; // N.info existentes para precargar (solo al actualizar)

/* =========================== Paso 1: PARSE (actualizar existente) =========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'parse') {
    $tab = 'a';
    try {
        $id = (int) ($_POST['indicator_id'] ?? 0);
        if (!inf_valid_id($id) || !is_dir(inf_dir($id))) {
            throw new RuntimeException('Seleccione un indicador válido.');
        }
        [$parsedToken, $parsed] = inf_parse_upload($_FILES['data_file'] ?? [], $id, $CACHE_DIR);
        $parsedId = $id;
        for ($i = 1; $i <= count($parsed); $i++) {
            $existingInfos[$i] = @getInfo(inf_dir($id) . '/' . $i . '.info');
        }
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'No se pudo procesar el archivo.';
    }
}

/* =========================== Crear indicador (+ Excel de datos) =========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $tab = 'b';
    try {
        $id = (int) ($_POST['new_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        if (!inf_valid_id($id)) {
            throw new RuntimeException('El ID debe tener 4 dígitos y empezar por 1-5 (observatorio).');
        }
        if (is_dir(inf_dir($id))) {
            throw new RuntimeException('Ya existe un indicador con el ID ' . $id . '.');
        }
        if ($title === '') {
            throw new RuntimeException('El título es obligatorio.');
        }
        $hasFile = !empty($_FILES['data_file']) && ($_FILES['data_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;

        $digit = (int) ((string) $id)[0];
        $dir = inf_dir($id);
        @mkdir($dir, 0775, true);
        inf_write_indicador_info($dir, [
            'Categoría' => trim($_POST['category'] ?? ''),
            'Subcategoría' => trim($_POST['subcategory'] ?? ''),
            'Titulo' => $title,
            'Descripción' => trim($_POST['description'] ?? ''),
            'Etiquetas' => trim($_POST['tags'] ?? '') ?: 'ND',
            'Fuentes' => trim($_POST['source'] ?? ''),
        ]);

        if ($pdo) {
            try {
                im_upsert($pdo, [
                    'id' => $id,
                    'observatory_id' => $digit,
                    'title' => $title,
                    'category_1' => trim($_POST['category'] ?? ''),
                    'category_2' => trim($_POST['subcategory'] ?? ''),
                    'definition' => trim($_POST['description'] ?? ''),
                    'source' => trim($_POST['source'] ?? ''),
                ]);
            } catch (Throwable $e) {
                // El registro en BD es complementario; los archivos ya quedaron creados.
            }
        }

        if ($hasFile) {
            // Parsear el Excel y pasar a la pantalla de previsualización/configuración.
            [$parsedToken, $parsed] = inf_parse_upload($_FILES['data_file'], $id, $CACHE_DIR);
            $parsedId = $id;
            $tab = 'a';
            $message = 'Indicador ' . $id . ' creado. Revise los gráficos detectados y guarde.';
        } else {
            $message = 'Indicador ' . $id . ' creado (sin datos). Puede cargar su Excel en la pestaña «Actualizar datos».';
        }
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'No se pudo crear el indicador.';
    }
}

/* =========================== Paso 2: GUARDAR datos (crear/actualizar) =========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_data') {
    $tab = 'a';
    try {
        $token = preg_replace('/[^a-f0-9]/', '', $_POST['token'] ?? '');
        $cacheFile = $CACHE_DIR . '/ind_upload_' . $token . '.json';
        if ($token === '' || !is_file($cacheFile)) {
            throw new RuntimeException('La sesión de carga expiró. Vuelva a subir el archivo.');
        }
        $data = json_decode((string) file_get_contents($cacheFile), true);
        $id = (int) ($data['id'] ?? 0);
        $sheets = $data['sheets'] ?? [];
        if (!inf_valid_id($id) || !is_dir(inf_dir($id)) || $sheets === []) {
            throw new RuntimeException('Datos de carga inválidos.');
        }
        $types = $_POST['chart_type'] ?? [];
        $titles = $_POST['chart_title'] ?? [];
        $verts = $_POST['chart_vertical'] ?? [];
        $horis = $_POST['chart_horizontal'] ?? [];

        $dir = inf_dir($id);
        $backup = inf_backup_dir($id);

        $count = count($sheets);
        $typeList = [];
        foreach ($sheets as $i => $sheet) {
            $n = $i + 1;
            file_put_contents($dir . '/' . $n . '.csv', inf_rows_to_csv($sheet['rows'] ?? []));
            $type = in_array($types[$i] ?? 'line', array_keys(inf_chart_types()), true) ? $types[$i] : 'line';
            $typeList[] = $type;
            inf_write_chart_info($dir, $n, [
                'Titulo' => trim($titles[$i] ?? ($sheet['name'] ?? ('Gráfico ' . $n))),
                'Descripción' => '',
                'Vertical' => trim($verts[$i] ?? ''),
                'Horizontal' => trim($horis[$i] ?? ''),
            ]);
        }
        inf_clear_extra_charts($dir, $count);
        file_put_contents($dir . '/display.js', inf_build_display_js($typeList));

        @unlink($cacheFile);
        $message = '✓ Datos guardados (' . $count . ' gráfico(s)) en el indicador ' . $id . '. Respaldo: ' . basename((string) $backup) . '.';
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'No se pudieron guardar los datos.';
    }
}

$indicators = inf_list_indicators();
$nextFree = [];
foreach (inf_observatories() as $d => $o) {
    $nextFree[$d] = inf_next_free_id($d);
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-3">Cargar / actualizar indicadores</h1>
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><a class="nav-link <?= $tab === 'a' ? 'active' : '' ?>" href="?tab=a">📊 Actualizar datos (Excel)</a></li>
                <li class="nav-item"><a class="nav-link <?= $tab === 'b' ? 'active' : '' ?>" href="?tab=b">➕ Crear indicador (Excel)</a></li>
            </ul>

            <?php if ($parsed !== null): ?>
                <!-- PASO 2: previsualización y configuración de gráficos (crear o actualizar) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h6 mb-3">Vista previa · Indicador <?= (int) $parsedId ?> <small class="text-muted">(<?= count($parsed) ?> hoja(s) = gráfico(s))</small></h2>
                        <p class="small text-muted">Revise los datos detectados, elija el tipo de gráfico y los títulos de los ejes. Al guardar se escriben los datos del indicador (se crea respaldo de lo anterior).</p>
                        <form method="post">
                            <input type="hidden" name="action" value="save_data">
                            <input type="hidden" name="tab" value="a">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($parsedToken) ?>">
                            <?php foreach ($parsed as $i => $sheet): $n = $i + 1; $ex = $existingInfos[$n] ?? []; ?>
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong>Gráfico <?= $n ?> · hoja «<?= htmlspecialchars($sheet['name']) ?>»</strong>
                                        <span class="badge text-bg-light"><?= count($sheet['rows']) ?> filas</span>
                                    </div>
                                    <div class="table-responsive mb-2" style="max-height:220px;overflow:auto">
                                        <table class="table table-sm table-bordered small mb-0">
                                            <?php foreach (array_slice($sheet['rows'], 0, 8) as $ri => $row): ?>
                                                <tr>
                                                    <?php foreach ($row as $cell): ?>
                                                        <?php if ($ri === 0): ?><th class="bg-light"><?= htmlspecialchars($cell) ?></th><?php else: ?><td><?= htmlspecialchars($cell) ?></td><?php endif; ?>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                        <?php if (count($sheet['rows']) > 8): ?><div class="small text-muted mt-1">… y <?= count($sheet['rows']) - 8 ?> filas más</div><?php endif; ?>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label small mb-0">Tipo de gráfico</label>
                                            <select name="chart_type[]" class="form-select form-select-sm">
                                                <?php foreach (inf_chart_types() as $tk => $tl): ?>
                                                    <option value="<?= $tk ?>" <?= $tk === 'line' ? 'selected' : '' ?>><?= htmlspecialchars($tl) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5"><label class="form-label small mb-0">Título del gráfico</label><input type="text" name="chart_title[]" class="form-control form-control-sm" value="<?= htmlspecialchars($ex['titulo'] ?? $sheet['name']) ?>"></div>
                                        <div class="col-md-2"><label class="form-label small mb-0">Eje vertical</label><input type="text" name="chart_vertical[]" class="form-control form-control-sm" value="<?= htmlspecialchars($ex['vertical'] ?? '') ?>" placeholder="Porcentaje"></div>
                                        <div class="col-md-2"><label class="form-label small mb-0">Eje horizontal</label><input type="text" name="chart_horizontal[]" class="form-control form-control-sm" value="<?= htmlspecialchars($ex['horizontal'] ?? 'Año') ?>" placeholder="Año"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar datos</button>
                            <a class="btn btn-outline-secondary" href="?tab=a">Cancelar</a>
                        </form>
                    </div>
                </div>
            <?php elseif ($tab === 'a'): ?>
                <!-- PASO 1: seleccionar indicador y subir archivo -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h6">Actualizar datos de un indicador existente</h2>
                        <p class="small text-muted mb-3">Suba un <strong>Excel (.xlsx)</strong> donde <strong>cada hoja es un gráfico</strong>: la 1ª fila son los encabezados y la 1ª columna el eje X (Año o categoría). También acepta un <strong>.csv</strong> (un solo gráfico).</p>
                        <form method="post" enctype="multipart/form-data" class="row g-2">
                            <input type="hidden" name="action" value="parse">
                            <input type="hidden" name="tab" value="a">
                            <div class="col-md-6">
                                <label class="form-label">Indicador</label>
                                <select name="indicator_id" class="form-select" required>
                                    <option value="">— seleccione —</option>
                                    <?php foreach ($indicators as $ind): ?>
                                        <option value="<?= (int) $ind['id'] ?>"><?= (int) $ind['id'] ?> · <?= htmlspecialchars($ind['titulo']) ?> (<?= htmlspecialchars($ind['observatory']) ?>, <?= (int) $ind['charts'] ?> gráf.)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Archivo de datos (.xlsx / .csv)</label>
                                <input type="file" name="data_file" class="form-control" accept=".xlsx,.csv" required>
                            </div>
                            <div class="col-12"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Procesar y previsualizar</button></div>
                        </form>
                    </div>
                </div>
                <p class="small text-muted"><?= count($indicators) ?> indicadores disponibles.</p>
            <?php else: ?>
                <!-- TAB B: crear con Excel -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h6">Crear un indicador nuevo</h2>
                        <p class="small text-muted mb-3">Complete los datos del indicador y suba su <strong>Excel</strong> (cada hoja = un gráfico). Tras crear, podrá revisar y configurar los gráficos antes de guardar. El Excel es opcional: si no lo sube, podrá cargarlo después en «Actualizar datos».</p>
                        <form method="post" enctype="multipart/form-data" class="row g-2">
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="tab" value="b">
                            <div class="col-md-5">
                                <label class="form-label">Observatorio</label>
                                <select id="obsSelect" class="form-select">
                                    <?php foreach (inf_observatories() as $d => $o): ?>
                                        <option value="<?= $d ?>" data-next="<?= (int) $nextFree[$d] ?>"><?= htmlspecialchars($o['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID del indicador</label>
                                <input type="number" name="new_id" id="newIdInput" class="form-control" value="<?= (int) $nextFree[1] ?>" required>
                                <small class="text-muted">Sugerido: siguiente libre.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Título</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-4"><label class="form-label">Categoría</label><input type="text" name="category" class="form-control" placeholder="Ej. Pobreza"></div>
                            <div class="col-md-4"><label class="form-label">Subcategoría</label><input type="text" name="subcategory" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label">Etiquetas</label><input type="text" name="tags" class="form-control" placeholder="separadas por |"></div>
                            <div class="col-12"><label class="form-label">Descripción</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                            <div class="col-md-6"><label class="form-label">Fuente</label><input type="text" name="source" class="form-control" placeholder="Ej. DANE / Secretaría de Salud"></div>
                            <div class="col-md-6">
                                <label class="form-label">Excel de datos (.xlsx / .csv) — opcional</label>
                                <input type="file" name="data_file" class="form-control" accept=".xlsx,.csv">
                            </div>
                            <div class="col-12"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-plus me-1"></i> Crear y previsualizar</button></div>
                        </form>
                    </div>
                </div>
                <script>
                    (function () {
                        var sel = document.getElementById('obsSelect');
                        var idInput = document.getElementById('newIdInput');
                        if (sel && idInput) {
                            sel.addEventListener('change', function () {
                                var next = sel.options[sel.selectedIndex].getAttribute('data-next');
                                if (next) idInput.value = next;
                            });
                        }
                    })();
                </script>
            <?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
