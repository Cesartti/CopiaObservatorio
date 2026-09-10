<?php
/**
 * CMS · Fenómenos ambientales.
 *  - Moderación de los reportes ciudadanos del mapa del Observatorio Ambiental.
 *  - Directorio de cuerpos de bomberos por municipio.
 */
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('news', true);
require_once __DIR__ . '/../config/database.php';

$cmsTitle = 'Fenómenos · Reportes y bomberos';
$cmsNav = 'fenomenos';
$pdo = cms_pdo();
$message = '';
$error = '';
$tab = ($_GET['tab'] ?? 'reportes') === 'bomberos' ? 'bomberos' : 'reportes';

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string) ($_POST['accion'] ?? '');
    try {
        if ($accion === 'estado_reporte') {
            $estados = ['pendiente', 'verificado', 'atendido', 'descartado'];
            $nuevo = in_array($_POST['estado'] ?? '', $estados, true) ? $_POST['estado'] : 'pendiente';
            $st = $pdo->prepare('UPDATE env_reportes_ciudadanos SET estado = ?, nota_revision = ?, revisado_en = NOW() WHERE id = ?');
            $st->execute([$nuevo, mb_substr(trim((string) ($_POST['nota'] ?? '')), 0, 400) ?: null, (int) $_POST['id']]);
            $message = 'Reporte actualizado.';
        } elseif ($accion === 'borrar_reporte') {
            $pdo->prepare('DELETE FROM env_reportes_ciudadanos WHERE id = ?')->execute([(int) $_POST['id']]);
            $message = 'Reporte eliminado.';
        } elseif ($accion === 'guardar_bombero') {
            $id = (int) ($_POST['id'] ?? 0);
            $campos = [
                'nombre' => trim((string) ($_POST['nombre'] ?? '')),
                'municipio' => trim((string) ($_POST['municipio'] ?? '')),
                'municipio_dane' => trim((string) ($_POST['municipio_dane'] ?? '')) ?: null,
                'provincia' => trim((string) ($_POST['provincia'] ?? '')) ?: null,
                'telefono' => trim((string) ($_POST['telefono'] ?? '')) ?: null,
                'celular' => trim((string) ($_POST['celular'] ?? '')) ?: null,
                'direccion' => trim((string) ($_POST['direccion'] ?? '')) ?: null,
                'correo' => trim((string) ($_POST['correo'] ?? '')) ?: null,
                'tipo' => trim((string) ($_POST['tipo'] ?? '')) ?: 'Cuerpo de bomberos voluntarios',
                'lat' => ($_POST['lat'] ?? '') !== '' ? (float) $_POST['lat'] : null,
                'lon' => ($_POST['lon'] ?? '') !== '' ? (float) $_POST['lon'] : null,
                'fuente' => trim((string) ($_POST['fuente'] ?? '')) ?: null,
                'verificado' => isset($_POST['verificado']) ? 1 : 0,
                'activo' => isset($_POST['activo']) ? 1 : 0,
            ];
            if ($campos['nombre'] === '' || $campos['municipio'] === '') {
                throw new RuntimeException('El nombre y el municipio son obligatorios.');
            }
            if ($id > 0) {
                $sets = implode(', ', array_map(fn ($c) => "$c = ?", array_keys($campos)));
                $st = $pdo->prepare("UPDATE env_bomberos SET $sets WHERE id = ?");
                $st->execute([...array_values($campos), $id]);
                $message = 'Cuerpo de bomberos actualizado.';
            } else {
                $cols = implode(', ', array_keys($campos));
                $marks = implode(', ', array_fill(0, count($campos), '?'));
                $pdo->prepare("INSERT INTO env_bomberos ($cols) VALUES ($marks)")->execute(array_values($campos));
                $message = 'Cuerpo de bomberos agregado.';
            }
            $tab = 'bomberos';
        } elseif ($accion === 'borrar_bombero') {
            $pdo->prepare('DELETE FROM env_bomberos WHERE id = ?')->execute([(int) $_POST['id']]);
            $message = 'Registro eliminado.';
            $tab = 'bomberos';
        } elseif ($accion === 'importar_bomberos') {
            if (empty($_FILES['csv']['tmp_name'])) {
                throw new RuntimeException('Seleccione un archivo CSV.');
            }
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');
            $cab = fgetcsv($fh, 0, ',');
            if ($cab === false) {
                throw new RuntimeException('El archivo está vacío.');
            }
            // Quita el BOM del primer encabezado si viene de Excel.
            $cab[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $cab[0]);
            $cab = array_map(static fn ($c) => strtolower(trim((string) $c)), $cab);
            $ix = array_flip($cab);
            $n = 0;
            $st = $pdo->prepare(
                'INSERT INTO env_bomberos (nombre, municipio, municipio_dane, provincia, telefono, celular, direccion, correo, tipo, fuente, verificado, activo)
                 VALUES (?,?,?,?,?,?,?,?,?,?,1,1)
                 ON DUPLICATE KEY UPDATE provincia=VALUES(provincia), telefono=VALUES(telefono), celular=VALUES(celular),
                    direccion=VALUES(direccion), correo=VALUES(correo), tipo=VALUES(tipo), fuente=VALUES(fuente), verificado=1, activo=1'
            );
            $g = static fn (array $f, string $k) => isset($ix[$k]) ? trim((string) ($f[$ix[$k]] ?? '')) : '';
            while (($f = fgetcsv($fh, 0, ',')) !== false) {
                $nombre = $g($f, 'nombre');
                $muni = $g($f, 'municipio');
                if ($nombre === '' || $muni === '') {
                    continue;
                }
                $st->execute([$nombre, $muni, $g($f, 'municipio_dane') ?: null, $g($f, 'provincia') ?: null,
                    $g($f, 'telefono') ?: null, $g($f, 'celular') ?: null, $g($f, 'direccion') ?: null,
                    $g($f, 'correo') ?: null, $g($f, 'tipo') ?: 'Cuerpo de bomberos voluntarios',
                    $g($f, 'fuente') ?: 'Importación CSV']);
                $n++;
            }
            fclose($fh);
            $message = "Se importaron o actualizaron $n registros.";
            $tab = 'bomberos';
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$reportes = [];
$bomberos = [];
$faltaTabla = false;
if ($pdo) {
    try {
        $reportes = $pdo->query(
            'SELECT * FROM env_reportes_ciudadanos ORDER BY (estado = "pendiente") DESC, creado_en DESC LIMIT 300'
        )->fetchAll(PDO::FETCH_ASSOC);
        $bomberos = $pdo->query('SELECT * FROM env_bomberos ORDER BY municipio, nombre')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $faltaTabla = true;
        $error = 'Las tablas del módulo aún no existen. Aplique la migración 026_fenomenos_ambiental.sql.';
    }
}
$editar = null;
if (($_GET['edit'] ?? '') !== '') {
    foreach ($bomberos as $b) {
        if ((int) $b['id'] === (int) $_GET['edit']) {
            $editar = $b;
            $tab = 'bomberos';
        }
    }
}
$pendientes = count(array_filter($reportes, static fn ($r) => $r['estado'] === 'pendiente'));

require __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap gap-2 align-items-center mb-3">
    <a class="btn btn-sm <?= $tab === 'reportes' ? 'btn-dark' : 'btn-outline-secondary' ?>" href="?tab=reportes">
        <i class="fa-solid fa-triangle-exclamation me-1"></i> Reportes ciudadanos
        <?php if ($pendientes > 0): ?><span class="badge bg-danger ms-1"><?= $pendientes ?></span><?php endif; ?>
    </a>
    <a class="btn btn-sm <?= $tab === 'bomberos' ? 'btn-dark' : 'btn-outline-secondary' ?>" href="?tab=bomberos">
        <i class="fa-solid fa-fire-extinguisher me-1"></i> Directorio de bomberos (<?= count($bomberos) ?>)
    </a>
</div>

<?php if ($message !== ''): ?><div class="alert alert-success py-2"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<?php if ($tab === 'reportes'): ?>
    <div class="card">
        <div class="card-body">
            <p class="text-muted small">
                Alertas enviadas por la ciudadanía desde el mapa del Observatorio Ambiental. Los estados
                <strong>pendiente</strong>, <strong>verificado</strong> y <strong>atendido</strong> se ven en el mapa público;
                <strong>descartado</strong> lo oculta.
            </p>
            <?php if (!$reportes): ?>
                <p class="text-muted mb-0">Todavía no hay reportes.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr>
                        <th>Fecha</th><th>Tipo</th><th>Municipio</th><th>Descripción</th>
                        <th>Ubicación</th><th>Contacto</th><th style="min-width:290px">Estado</th><th></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($reportes as $r): ?>
                        <tr>
                            <td class="small"><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $r['creado_en']))) ?></td>
                            <td class="small"><?= htmlspecialchars((string) $r['tipo']) ?></td>
                            <td class="small"><?= htmlspecialchars((string) ($r['municipio'] ?? '')) ?></td>
                            <td class="small" style="max-width:320px"><?= nl2br(htmlspecialchars((string) $r['descripcion'])) ?>
                                <?php if ($r['referencia']): ?><br><em class="text-muted"><?= htmlspecialchars((string) $r['referencia']) ?></em><?php endif; ?>
                            </td>
                            <td class="small">
                                <a href="https://www.openstreetmap.org/?mlat=<?= (float) $r['lat'] ?>&mlon=<?= (float) $r['lon'] ?>#map=15/<?= (float) $r['lat'] ?>/<?= (float) $r['lon'] ?>" target="_blank" rel="noopener">
                                    <?= number_format((float) $r['lat'], 4) ?>, <?= number_format((float) $r['lon'], 4) ?>
                                </a>
                            </td>
                            <td class="small"><?= htmlspecialchars((string) ($r['contacto'] ?? '—')) ?></td>
                            <td>
                                <form method="post" class="d-flex flex-wrap gap-1 align-items-center">
                                    <input type="hidden" name="accion" value="estado_reporte">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <select name="estado" class="form-select form-select-sm" style="width:auto">
                                        <?php foreach (['pendiente', 'verificado', 'atendido', 'descartado'] as $e): ?>
                                            <option value="<?= $e ?>" <?= $r['estado'] === $e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="nota" class="form-control form-control-sm" style="width:120px"
                                           placeholder="Nota" value="<?= htmlspecialchars((string) ($r['nota_revision'] ?? '')) ?>">
                                    <button class="btn btn-sm btn-primary">Guardar</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" onsubmit="return confirm('¿Eliminar este reporte?');">
                                    <input type="hidden" name="accion" value="borrar_reporte">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= $editar ? 'Editar cuerpo de bomberos' : 'Agregar cuerpo de bomberos' ?></h5>
                    <form method="post" class="row g-2">
                        <input type="hidden" name="accion" value="guardar_bombero">
                        <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? 0) ?>">
                        <div class="col-12"><label class="form-label small mb-1">Nombre *</label>
                            <input name="nombre" class="form-control form-control-sm" required value="<?= htmlspecialchars((string) ($editar['nombre'] ?? '')) ?>"></div>
                        <div class="col-7"><label class="form-label small mb-1">Municipio *</label>
                            <input name="municipio" class="form-control form-control-sm" required value="<?= htmlspecialchars((string) ($editar['municipio'] ?? '')) ?>"></div>
                        <div class="col-5"><label class="form-label small mb-1">Código DANE</label>
                            <input name="municipio_dane" class="form-control form-control-sm" maxlength="5" value="<?= htmlspecialchars((string) ($editar['municipio_dane'] ?? '')) ?>"></div>
                        <div class="col-6"><label class="form-label small mb-1">Provincia</label>
                            <input name="provincia" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($editar['provincia'] ?? '')) ?>"></div>
                        <div class="col-6"><label class="form-label small mb-1">Tipo</label>
                            <input name="tipo" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($editar['tipo'] ?? 'Cuerpo de bomberos voluntarios')) ?>"></div>
                        <div class="col-6"><label class="form-label small mb-1">Teléfono</label>
                            <input name="telefono" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($editar['telefono'] ?? '')) ?>"></div>
                        <div class="col-6"><label class="form-label small mb-1">Celular</label>
                            <input name="celular" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($editar['celular'] ?? '')) ?>"></div>
                        <div class="col-12"><label class="form-label small mb-1">Dirección</label>
                            <input name="direccion" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($editar['direccion'] ?? '')) ?>"></div>
                        <div class="col-12"><label class="form-label small mb-1">Correo</label>
                            <input name="correo" type="email" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($editar['correo'] ?? '')) ?>"></div>
                        <div class="col-6"><label class="form-label small mb-1">Latitud</label>
                            <input name="lat" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($editar['lat'] ?? '')) ?>"></div>
                        <div class="col-6"><label class="form-label small mb-1">Longitud</label>
                            <input name="lon" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($editar['lon'] ?? '')) ?>"></div>
                        <div class="col-12"><label class="form-label small mb-1">Fuente del dato</label>
                            <input name="fuente" class="form-control form-control-sm" placeholder="Ej: Delegación Departamental de Bomberos, oficio del 10/09/2026"
                                   value="<?= htmlspecialchars((string) ($editar['fuente'] ?? '')) ?>"></div>
                        <div class="col-12 d-flex gap-3">
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="verificado" id="v1" <?= (int) ($editar['verificado'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="v1">Verificado</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="activo" id="a1" <?= (int) ($editar['activo'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="a1">Visible en el portal</label></div>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-sm btn-primary"><?= $editar ? 'Guardar cambios' : 'Agregar' ?></button>
                            <?php if ($editar): ?><a href="?tab=bomberos" class="btn btn-sm btn-outline-secondary">Cancelar</a><?php endif; ?>
                        </div>
                    </form>
                    <hr>
                    <h6 class="small fw-bold">Importar desde CSV</h6>
                    <p class="small text-muted mb-2">
                        Encabezados admitidos: <code>nombre, municipio, municipio_dane, provincia, telefono,
                        celular, direccion, correo, tipo, fuente</code>. Los registros existentes se actualizan.
                    </p>
                    <form method="post" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="accion" value="importar_bomberos">
                        <input type="file" name="csv" accept=".csv" class="form-control form-control-sm" required>
                        <button class="btn btn-sm btn-outline-primary">Importar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Directorio (<?= count($bomberos) ?>)</h5>
                    <?php if (!$bomberos): ?>
                        <p class="text-muted mb-0">Aún no hay registros.</p>
                    <?php else: ?>
                    <div class="table-responsive" style="max-height:620px">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Municipio</th><th>Nombre</th><th>Contacto</th><th>Estado</th><th></th></tr></thead>
                            <tbody>
                            <?php foreach ($bomberos as $b): ?>
                                <tr>
                                    <td class="small"><?= htmlspecialchars((string) $b['municipio']) ?></td>
                                    <td class="small"><?= htmlspecialchars((string) $b['nombre']) ?></td>
                                    <td class="small"><?= htmlspecialchars(trim(((string) $b['telefono']) . ' ' . ((string) $b['celular']))) ?: '—' ?></td>
                                    <td class="small">
                                        <?= (int) $b['activo'] ? '<span class="badge bg-success">visible</span>' : '<span class="badge bg-secondary">oculto</span>' ?>
                                        <?= (int) $b['verificado'] ? '' : ' <span class="badge bg-warning text-dark">sin verificar</span>' ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="?tab=bomberos&edit=<?= (int) $b['id'] ?>"><i class="fa-solid fa-pen"></i></a>
                                        <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar el registro?');">
                                            <input type="hidden" name="accion" value="borrar_bombero">
                                            <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
