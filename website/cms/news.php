<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('news', true);
require_once __DIR__ . '/../config/database.php';

$cmsTitle = 'Noticias';
$cmsNav = 'news';
$pdo = cms_pdo();
$message = '';
$error = '';

function cms_slugify(string $text): string
{
    $t = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    if ($t === false) {
        $t = $text;
    }
    $t = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $t));

    return trim($t, '-') ?: 'noticia';
}

function cms_news_unique_slug(PDO $pdo, string $title, int $excludeId = 0): string
{
    $slug = cms_slugify($title);
    $base = $slug;
    $i = 1;
    while (true) {
        $chk = $pdo->prepare('SELECT COUNT(*) FROM news WHERE slug = ? AND id <> ?');
        $chk->execute([$slug, $excludeId]);
        if ((int) $chk->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
}

/**
 * Procesa la imagen del formulario: prioriza archivo subido; si no, URL provista.
 * Devuelve la ruta/URL final o '' si no se envió nada nuevo.
 */
function cms_news_image_from_request(string $slug): string
{
    $imageUrl = trim($_POST['image_url'] ?? '');
    if (!empty($_FILES['image_file']) && ($_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assets/uploads/news';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        // Solo imágenes ráster: SVG se excluye por ser vector de XSS almacenado.
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            throw new RuntimeException('Formato no permitido. Use jpg/png/gif/webp.');
        }
        // Límite de tamaño 5 MB.
        if (($_FILES['image_file']['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new RuntimeException('La imagen supera el límite de 5 MB.');
        }
        // Verificar que el contenido real sea una imagen válida (no un archivo renombrado).
        $info = @getimagesize($_FILES['image_file']['tmp_name']);
        $validTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
        if ($info === false || !in_array($info[2] ?? 0, $validTypes, true)) {
            throw new RuntimeException('El archivo no es una imagen válida.');
        }
        // Nombre con sufijo aleatorio para evitar rutas predecibles.
        $fname = $slug . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $uploadDir . DIRECTORY_SEPARATOR . $fname;
        if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
            throw new RuntimeException('No se pudo guardar el archivo.');
        }

        return 'assets/uploads/news/' . $fname;
    }

    return $imageUrl;
}

$obsRows = [];
if ($pdo) {
    try {
        $obsRows = $pdo->query('SELECT id, name FROM observatories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = 'Base de datos no lista.';
    }
}

$action = $_POST['action'] ?? '';

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    try {
        $oid = (int) ($_POST['observatory_id'] ?? 0); // 0 = General (NULL)
        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            throw new RuntimeException('El título es obligatorio.');
        }
        $slug = cms_news_unique_slug($pdo, $title);
        $imageUrl = cms_news_image_from_request($slug);
        $status = trim($_POST['status'] ?? 'published');

        $uid = auth_user()['id'] ?? null;
        $stmt = $pdo->prepare('INSERT INTO news (observatory_id, title, slug, summary, body, source, image_url, published_at, content_status, created_by) VALUES (?,?,?,?,?,?,?,NOW(),?,?)');
        $stmt->execute([
            $oid > 0 ? $oid : null,
            $title,
            $slug,
            trim($_POST['summary'] ?? ''),
            trim($_POST['body'] ?? ''),
            trim($_POST['source'] ?? ''),
            $imageUrl !== '' ? $imageUrl : null,
            $status,
            $uid,
        ]);
        $message = 'Noticia creada.';
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'No se pudo guardar.';
    }
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $oid = (int) ($_POST['observatory_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        if ($id < 1 || $title === '') {
            throw new RuntimeException('El título es obligatorio.');
        }
        $cur = $pdo->prepare('SELECT slug, image_url FROM news WHERE id = ?');
        $cur->execute([$id]);
        $curRow = $cur->fetch(PDO::FETCH_ASSOC);
        if (!$curRow) {
            throw new RuntimeException('La noticia no existe.');
        }
        $imageUrl = cms_news_image_from_request($curRow['slug']);
        if ($imageUrl === '') {
            $imageUrl = (string) ($curRow['image_url'] ?? ''); // conservar imagen actual
        }
        $status = trim($_POST['status'] ?? 'published');

        $stmt = $pdo->prepare('UPDATE news SET observatory_id = ?, title = ?, summary = ?, body = ?, source = ?, image_url = ?, content_status = ?, published_at = COALESCE(published_at, NOW()) WHERE id = ?');
        $stmt->execute([
            $oid > 0 ? $oid : null,
            $title,
            trim($_POST['summary'] ?? ''),
            trim($_POST['body'] ?? ''),
            trim($_POST['source'] ?? ''),
            $imageUrl !== '' ? $imageUrl : null,
            $status,
            $id,
        ]);
        $message = 'Noticia actualizada.';
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'No se pudo actualizar.';
    }
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'toggle') {
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE news SET content_status = IF(content_status = "published", "draft", "published"), published_at = COALESCE(published_at, NOW()) WHERE id = ?');
        $stmt->execute([$id]);
        $message = 'Estado actualizado.';
    } catch (Throwable $e) {
        $error = 'No se pudo cambiar el estado.';
    }
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM news WHERE id = ?');
        $stmt->execute([$id]);
        $message = 'Noticia eliminada.';
    } catch (Throwable $e) {
        $error = 'No se pudo eliminar.';
    }
}

// Noticia en edición (?edit=ID)
$editRow = null;
$editId = (int) ($_GET['edit'] ?? 0);
if ($pdo && $editId > 0) {
    try {
        $st = $pdo->prepare('SELECT * FROM news WHERE id = ? LIMIT 1');
        $st->execute([$editId]);
        $editRow = $st->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$editRow) {
            $error = 'La noticia a editar no existe.';
        }
    } catch (Throwable $e) {
    }
}

// Listado con filtro
$filterObs = trim($_GET['obs'] ?? ''); // '' | 'general' | id numérico
$rows = [];
if ($pdo) {
    try {
        $where = '1=1';
        $params = [];
        if ($filterObs === 'general') {
            $where = 'n.observatory_id IS NULL';
        } elseif ($filterObs !== '' && ctype_digit($filterObs)) {
            $where = 'n.observatory_id = ?';
            $params[] = (int) $filterObs;
        }
        $st = $pdo->prepare(
            "SELECT n.id, n.title, n.slug, n.image_url, n.published_at, n.content_status, o.name AS obs
             FROM news n LEFT JOIN observatories o ON o.id = n.observatory_id
             WHERE {$where} ORDER BY n.id DESC LIMIT 200"
        );
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
    }
}

require __DIR__ . '/includes/header.php';

$f = $editRow ?: [
    'id' => 0,
    'observatory_id' => null,
    'title' => '',
    'summary' => '',
    'body' => '',
    'source' => '',
    'image_url' => '',
    'content_status' => 'published',
];
?>
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Noticias</h1>
                <a class="btn btn-sm btn-outline-secondary" href="../noticias.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Ver página pública</a>
            </div>
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0"><?= $editRow ? 'Editar noticia #' . (int) $editRow['id'] : 'Nueva noticia' ?></h2>
                        <?php if ($editRow): ?><a class="btn btn-sm btn-outline-secondary" href="news.php">+ Crear nueva en su lugar</a><?php endif; ?>
                    </div>
                    <form method="post" enctype="multipart/form-data" class="mt-2">
                        <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'add' ?>">
                        <?php if ($editRow): ?><input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>"><?php endif; ?>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Clasificación</label>
                                <select name="observatory_id" class="form-select">
                                    <option value="0" <?= $f['observatory_id'] === null ? 'selected' : '' ?>>🌐 General (toda la Red)</option>
                                    <?php foreach ($obsRows as $o): ?>
                                        <option value="<?= (int) $o['id'] ?>" <?= (int) ($f['observatory_id'] ?? 0) === (int) $o['id'] ? 'selected' : '' ?>><?= htmlspecialchars($o['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">«General» aparece en el portal y en todas las secciones de noticias.</small>
                            </div>
                            <div class="col-md-8"><label class="form-label">Título</label><input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($f['title']) ?>"></div>
                            <div class="col-12"><label class="form-label">Resumen</label><textarea name="summary" class="form-control" rows="2" placeholder="Breve descripción (1-2 líneas) que aparece en la tarjeta de la noticia"><?= htmlspecialchars((string) $f['summary']) ?></textarea></div>
                            <div class="col-12"><label class="form-label">Cuerpo</label><textarea name="body" class="form-control" rows="8" placeholder="Contenido completo de la noticia (HTML permitido)"><?= htmlspecialchars((string) $f['body']) ?></textarea></div>
                            <div class="col-md-6"><label class="form-label">Fuente (opcional)</label><input type="text" name="source" class="form-control" placeholder="Ej. Secretaría de Planeación de Boyacá" value="<?= htmlspecialchars((string) $f['source']) ?>"></div>
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-select">
                                    <option value="published" <?= $f['content_status'] === 'published' ? 'selected' : '' ?>>Publicada</option>
                                    <option value="draft" <?= $f['content_status'] !== 'published' ? 'selected' : '' ?>>Borrador</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">📤 Subir imagen<?= $editRow && !empty($f['image_url']) ? ' (reemplaza la actual)' : ' (recomendado)' ?></label>
                                <input type="file" name="image_file" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml">
                                <small class="text-muted">JPG, PNG, GIF, WEBP o SVG · Se guarda en <code>assets/uploads/news/</code></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">🔗 …o pegar URL de imagen externa</label>
                                <input type="url" name="image_url" class="form-control" placeholder="https://… (si no subió archivo)">
                                <?php if ($editRow && !empty($f['image_url'])): ?>
                                    <small class="text-muted">Imagen actual: <code><?= htmlspecialchars((string) $f['image_url']) ?></code> (se conserva si no envía otra)</small>
                                <?php else: ?>
                                    <small class="text-muted">También puede usar rutas internas como <code>assets/svg/bg-banner-economico.png</code></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-newspaper me-1"></i> <?= $editRow ? 'Guardar cambios' : 'Guardar noticia' ?></button>
                                <?php if ($editRow): ?><a class="btn btn-outline-secondary" href="news.php">Cancelar</a><?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                <strong class="me-1 small">Historial:</strong>
                <a class="btn btn-sm <?= $filterObs === '' ? 'btn-primary' : 'btn-outline-secondary' ?>" href="news.php">Todas</a>
                <a class="btn btn-sm <?= $filterObs === 'general' ? 'btn-primary' : 'btn-outline-secondary' ?>" href="news.php?obs=general">Generales</a>
                <?php foreach ($obsRows as $o): ?>
                    <a class="btn btn-sm <?= $filterObs === (string) $o['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>" href="news.php?obs=<?= (int) $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></a>
                <?php endforeach; ?>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle bg-white shadow-sm">
                    <thead>
                        <tr>
                            <th style="width:60px">Img</th>
                            <th>Título</th>
                            <th>Clasificación</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th style="width:220px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td>
                                <?php if (!empty($r['image_url'])): ?>
                                    <img src="../<?= htmlspecialchars($r['image_url']) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb">
                                <?php else: ?>
                                    <span class="text-muted small"><i class="fa-regular fa-image"></i></span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r['title']) ?></td>
                            <td>
                                <?php if ($r['obs'] !== null): ?>
                                    <?= htmlspecialchars($r['obs']) ?>
                                <?php else: ?>
                                    <span class="badge bg-dark">🌐 General</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $r['content_status'] === 'published' ? 'success' : 'secondary' ?>">
                                    <?= $r['content_status'] === 'published' ? 'Publicada' : 'Borrador' ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars((string) $r['published_at']) ?></td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a class="btn btn-sm btn-outline-secondary" href="../noticia.php?slug=<?= htmlspecialchars($r['slug']) ?>" target="_blank" title="Ver noticia"><i class="fa-regular fa-eye"></i></a>
                                    <a class="btn btn-sm btn-outline-primary" href="news.php?edit=<?= (int) $r['id'] ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                        <button class="btn btn-sm btn-outline-warning" type="submit" title="<?= $r['content_status'] === 'published' ? 'Pasar a borrador' : 'Publicar' ?>">
                                            <i class="fa-solid <?= $r['content_status'] === 'published' ? 'fa-eye-slash' : 'fa-upload' ?>"></i>
                                        </button>
                                    </form>
                                    <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar definitivamente la noticia «<?= htmlspecialchars(addslashes($r['title'])) ?>»?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">No hay noticias<?= $filterObs !== '' ? ' con este filtro' : ' creadas todavía' ?>.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
