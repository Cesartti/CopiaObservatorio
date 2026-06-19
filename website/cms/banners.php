<?php
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('banners', true);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/cms_media_assets.php';

$cmsTitle = 'Banners inicio';
$cmsNav = 'banners';
$pdo = cms_pdo();
$message = '';
$error = '';

/* ── Scan image folders ─────────────────────────────────── */
$uploadDir = 'assets/banners';
$uploadDirFull = cms_website_root() . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'banners';
if (!is_dir($uploadDirFull)) {
    @mkdir($uploadDirFull, 0755, true);
}
$assetListSvg     = cms_list_relative_assets('assets/svg');
$assetListBanners = cms_list_relative_assets('assets/banners');
$assetList = array_merge($assetListBanners, $assetListSvg);

/* ── Handle upload ──────────────────────────────────────── */
$uploadedPath = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['image_file']['name'])) {
    $file = $_FILES['image_file'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
    $maxSize = 5 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Error al subir archivo (código ' . $file['error'] . ').';
    } elseif ($file['size'] > $maxSize) {
        $error = 'Archivo muy grande. Máximo 5 MB.';
    } elseif (!in_array($file['type'], $allowed, true)) {
        $error = 'Formato no permitido. Use JPG, PNG, WebP, GIF o SVG.';
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeName = preg_replace('/[^a-z0-9_\-]/i', '-', pathinfo($file['name'], PATHINFO_FILENAME));
        $safeName = trim($safeName, '-');
        if ($safeName === '') $safeName = 'banner-' . time();
        $destName = $safeName . '.' . $ext;
        $destFull = $uploadDirFull . DIRECTORY_SEPARATOR . $destName;
        $counter = 1;
        while (file_exists($destFull)) {
            $destName = $safeName . '-' . $counter . '.' . $ext;
            $destFull = $uploadDirFull . DIRECTORY_SEPARATOR . $destName;
            $counter++;
        }
        if (move_uploaded_file($file['tmp_name'], $destFull)) {
            $uploadedPath = $uploadDir . '/' . $destName;
            $message = 'Imagen subida: <strong>' . htmlspecialchars($destName) . '</strong>';
            $assetListBanners = cms_list_relative_assets('assets/banners');
            $assetList = array_merge($assetListBanners, $assetListSvg);
        } else {
            $error = 'No se pudo guardar. Verifique permisos de assets/banners.';
        }
    }
}

/* ── Handle CRUD ────────────────────────────────────────── */
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add') {
            $img = $uploadedPath !== '' ? $uploadedPath : cms_normalize_media_url((string) ($_POST['image_url'] ?? ''));
            if ($img !== '' && !cms_is_safe_media_path($img)) {
                throw new RuntimeException('Imagen no válida o inexistente.');
            }
            $title = trim($_POST['title'] ?? '');
            $subtitle = trim($_POST['subtitle'] ?? '');
            $tag = trim($_POST['tag'] ?? '');
            if ($img === '' && $title === '' && $subtitle === '') {
                throw new RuntimeException('Agregue al menos una imagen, título o subtítulo.');
            }
            $linkUrl = trim($_POST['link_url'] ?? '');
            if ($linkUrl !== '' && !preg_match('~^(https?://|/|\.\./|\?|#)~i', $linkUrl)) {
                throw new RuntimeException('El enlace debe iniciar con http://, https://, / o ../');
            }
            try {
                $stmt = $pdo->prepare('INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, image_url, link_url, is_active) VALUES (?,?,?,?,?,?,1)');
                $stmt->execute([(int) ($_POST['sort_order'] ?? 0), $title, $subtitle, $tag, $img === '' ? null : $img, $linkUrl === '' ? null : $linkUrl]);
            } catch (Throwable $e) {
                try {
                    $stmt = $pdo->prepare('INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, image_url, is_active) VALUES (?,?,?,?,?,1)');
                    $stmt->execute([(int) ($_POST['sort_order'] ?? 0), $title, $subtitle, $tag, $img === '' ? null : $img]);
                } catch (Throwable $e2) {
                    $stmt = $pdo->prepare('INSERT INTO cms_home_banners (sort_order, title, subtitle, tag, is_active) VALUES (?,?,?,?,1)');
                    $stmt->execute([(int) ($_POST['sort_order'] ?? 0), $title, $subtitle, $tag]);
                }
            }
            $message .= ($message ? '<br>' : '') . 'Banner creado.';
        } elseif ($action === 'save' && isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            $img = $uploadedPath !== '' ? $uploadedPath : cms_normalize_media_url((string) ($_POST['image_url'] ?? ''));
            if ($img !== '' && !cms_is_safe_media_path($img)) {
                throw new RuntimeException('Imagen no válida o inexistente.');
            }
            $linkUrl = trim($_POST['link_url'] ?? '');
            if ($linkUrl !== '' && !preg_match('~^(https?://|/|\.\./|\?|#)~i', $linkUrl)) {
                throw new RuntimeException('El enlace debe iniciar con http://, https://, / o ../');
            }
            try {
                $stmt = $pdo->prepare('UPDATE cms_home_banners SET sort_order = ?, title = ?, subtitle = ?, tag = ?, image_url = ?, link_url = ? WHERE id = ?');
                $stmt->execute([(int) ($_POST['sort_order'] ?? 0), trim($_POST['title'] ?? ''), trim($_POST['subtitle'] ?? ''), trim($_POST['tag'] ?? ''), $img === '' ? null : $img, $linkUrl === '' ? null : $linkUrl, $id]);
            } catch (Throwable $e) {
                try {
                    $stmt = $pdo->prepare('UPDATE cms_home_banners SET sort_order = ?, title = ?, subtitle = ?, tag = ?, image_url = ? WHERE id = ?');
                    $stmt->execute([(int) ($_POST['sort_order'] ?? 0), trim($_POST['title'] ?? ''), trim($_POST['subtitle'] ?? ''), trim($_POST['tag'] ?? ''), $img === '' ? null : $img, $id]);
                } catch (Throwable $e2) {
                    $stmt = $pdo->prepare('UPDATE cms_home_banners SET sort_order = ?, title = ?, subtitle = ?, tag = ? WHERE id = ?');
                    $stmt->execute([(int) ($_POST['sort_order'] ?? 0), trim($_POST['title'] ?? ''), trim($_POST['subtitle'] ?? ''), trim($_POST['tag'] ?? ''), $id]);
                }
            }
            $message .= ($message ? '<br>' : '') . 'Banner actualizado.';
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            $pdo->prepare('DELETE FROM cms_home_banners WHERE id = ?')->execute([(int) $_POST['id']]);
            $message = 'Banner eliminado.';
        } elseif ($action === 'toggle' && isset($_POST['id'])) {
            $pdo->prepare('UPDATE cms_home_banners SET is_active = 1 - is_active WHERE id = ?')->execute([(int) $_POST['id']]);
            $message = 'Estado actualizado.';
        }
    } catch (Throwable $e) {
        $error .= ($error ? '<br>' : '') . ($e->getMessage() ?: 'Error al guardar.');
    }
}

$rows = [];
if ($pdo) {
    try {
        $rows = $pdo->query('SELECT * FROM cms_home_banners ORDER BY sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = 'Ejecute la migración 002_cms_core.sql.';
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = null;
if ($editId > 0 && $pdo) {
    $st = $pdo->prepare('SELECT * FROM cms_home_banners WHERE id = ?');
    $st->execute([$editId]);
    $editRow = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-1">Banners del portal</h1>
            <p class="small text-muted mb-3">Carrusel del portal principal (<code>index.php</code>). Suba imágenes o elija de las existentes.</p>

            <div class="alert alert-info py-2 px-3 d-flex align-items-start gap-2" style="font-size:.85rem;border-left:4px solid #0d6efd">
                <i class="bi bi-image" style="font-size:1.1rem;margin-top:2px"></i>
                <div>
                    <strong>Tamaño recomendado:</strong> <code>2280 × 600 px</code> (mín 1140 × 300 px). Aspecto ~3.8:1. JPG/WebP, máx 400 KB.
                    Título, subtítulo y etiqueta son <strong>opcionales</strong>. Si solo carga imagen, el banner mostrará solo la foto.
                </div>
            </div>

            <?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <!-- Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 mb-3"><?= $editRow ? '<i class="bi bi-pencil"></i> Editar banner #' . (int) $editRow['id'] : '<i class="bi bi-plus-circle"></i> Nuevo banner' ?></h2>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?= $editRow ? 'save' : 'add' ?>">
                        <?php if ($editRow): ?>
                            <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
                        <?php endif; ?>

                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Orden</label>
                                <input type="number" name="sort_order" class="form-control" value="<?= $editRow ? (int) $editRow['sort_order'] : 0 ?>" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Título <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="text" name="title" class="form-control" value="<?= $editRow ? htmlspecialchars($editRow['title']) : '' ?>" placeholder="Ej: Red de Observatorios de Boyacá">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Subtítulo <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="text" name="subtitle" class="form-control" value="<?= $editRow ? htmlspecialchars((string) ($editRow['subtitle'] ?? '')) : '' ?>" placeholder="Ej: Datos para la toma de decisiones">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Etiqueta <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="text" name="tag" class="form-control" value="<?= $editRow ? htmlspecialchars((string) ($editRow['tag'] ?? '')) : '' ?>" placeholder="Ej: Nuevo">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Elegir imagen existente</label>
                                <select name="image_url" class="form-select" id="imageSelectHome" onchange="previewImg(this.value)">
                                    <option value="">— Sin imagen —</option>
                                    <?php if ($assetListBanners): ?>
                                        <optgroup label="📁 banners (subidas)">
                                        <?php foreach ($assetListBanners as $path): ?>
                                            <option value="<?= htmlspecialchars($path) ?>" <?= ($editRow && ($editRow['image_url'] ?? '') === $path) ? 'selected' : '' ?>><?= htmlspecialchars(basename($path)) ?></option>
                                        <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <optgroup label="📁 svg (originales)">
                                    <?php foreach ($assetListSvg as $path): ?>
                                        <option value="<?= htmlspecialchars($path) ?>" <?= ($editRow && ($editRow['image_url'] ?? '') === $path) ? 'selected' : '' ?>><?= htmlspecialchars(basename($path)) ?></option>
                                    <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <span class="text-muted fw-semibold" style="padding-bottom:.45rem">ó</span>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Subir nueva imagen</label>
                                <input type="file" name="image_file" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" onchange="previewUploadHome(this)">
                                <div class="form-text">JPG, PNG, WebP, GIF, SVG. Máx 5 MB → <code>assets/banners/</code></div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Enlace "Ver más" <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="text" name="link_url" class="form-control" placeholder="Ej: noticia.php?slug=mi-noticia  o  https://ejemplo.com" value="<?= $editRow ? htmlspecialchars((string) ($editRow['link_url'] ?? '')) : '' ?>">
                                <div class="form-text">URL completa o ruta interna. Si se completa, aparecerá un botón "Ver más" en el banner que dirige a esa página/noticia.</div>
                            </div>
                        </div>

                        <div id="imgPreviewHome" class="mb-3" style="display:none">
                            <label class="form-label fw-semibold small text-muted">Vista previa</label>
                            <div style="max-width:100%;height:110px;border-radius:10px;overflow:hidden;border:1px solid #dee2e6;background:#f8f9fa;position:relative">
                                <img id="previewImgHome" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:block">
                                <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.2) 0%,rgba(0,0,0,.5) 40%,rgba(0,0,0,.7) 100%)"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="bi bi-check-lg"></i> <?= $editRow ? 'Guardar cambios' : 'Agregar banner' ?>
                            </button>
                            <?php if ($editRow): ?>
                                <a class="btn btn-outline-secondary" href="<?= htmlspecialchars(app_url('website/cms/banners.php')) ?>">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-sm align-middle bg-white shadow-sm rounded">
                    <thead class="table-light">
                        <tr><th style="width:60px">Preview</th><th style="width:50px">Orden</th><th>Título</th><th>Subtítulo</th><th>Etiqueta</th><th>Enlace</th><th>Activo</th><th class="text-end">Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay banners. Agregue uno arriba.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td>
                                <?php if (!empty($r['image_url'])): ?>
                                    <img src="../<?= htmlspecialchars(ltrim((string) $r['image_url'], '/')) ?>" alt="" style="width:56px;height:32px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6">
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= (int) $r['sort_order'] ?></td>
                            <td><?= htmlspecialchars($r['title'] ?: '—') ?></td>
                            <td class="small text-muted" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars((string) ($r['subtitle'] ?? '')) ?: '—' ?></td>
                            <td><?= htmlspecialchars((string) ($r['tag'] ?? '')) ?: '—' ?></td>
                            <td class="small">
                                <?php if (!empty($r['link_url'])): ?>
                                    <a href="<?= htmlspecialchars($r['link_url']) ?>" target="_blank" rel="noopener" title="<?= htmlspecialchars($r['link_url']) ?>"><i class="bi bi-link-45deg"></i> Ver</a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= !empty($r['is_active']) ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars(app_url('website/cms/banners.php?edit=' . (int) $r['id'])) ?>"><i class="bi bi-pencil"></i></a>
                                <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                    <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-toggle-on"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
            function previewImg(val) {
                var box = document.getElementById('imgPreviewHome');
                var img = document.getElementById('previewImgHome');
                if (val) { img.src = '../' + val; box.style.display = 'block'; var f = document.querySelector('input[name="image_file"]'); if (f) f.value = ''; }
                else { box.style.display = 'none'; }
            }
            function previewUploadHome(input) {
                if (input.files && input.files[0]) {
                    var r = new FileReader();
                    r.onload = function(e) { document.getElementById('previewImgHome').src = e.target.result; document.getElementById('imgPreviewHome').style.display = 'block'; document.getElementById('imageSelectHome').value = ''; };
                    r.readAsDataURL(input.files[0]);
                }
            }
            <?php if ($editRow && !empty($editRow['image_url'])): ?>
            document.addEventListener('DOMContentLoaded', function() { previewImg('<?= addslashes($editRow['image_url']) ?>'); });
            <?php endif; ?>
            </script>
<?php require __DIR__ . '/includes/footer.php'; ?>
