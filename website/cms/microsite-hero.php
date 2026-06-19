<?php
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('banners', true);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/cms_media_assets.php';

$cmsTitle = 'Carrusel micrositios';
$cmsNav = 'microsite_hero';
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

$obsRows = [];
if ($pdo) {
    try {
        $obsRows = $pdo->query('SELECT id, slug, name FROM observatories ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = 'Base de datos no lista.';
    }
}

/* ── Handle upload ──────────────────────────────────────── */
$uploadedPath = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['image_file']['name'])) {
    $file = $_FILES['image_file'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
    $maxSize = 5 * 1024 * 1024; // 5 MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Error al subir archivo (código ' . $file['error'] . ').';
    } elseif ($file['size'] > $maxSize) {
        $error = 'Archivo muy grande. Máximo 5 MB.';
    } elseif (!in_array($file['type'], $allowed, true)) {
        $error = 'Formato no permitido. Use JPG, PNG, WebP, GIF o SVG.';
    } else {
        // Sanitize filename
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeName = preg_replace('/[^a-z0-9_\-]/i', '-', pathinfo($file['name'], PATHINFO_FILENAME));
        $safeName = trim($safeName, '-');
        if ($safeName === '') $safeName = 'banner-' . time();
        $destName = $safeName . '.' . $ext;

        // Avoid overwriting
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
            // Refresh asset list
            $assetListBanners = cms_list_relative_assets('assets/banners');
            $assetList = array_merge($assetListBanners, $assetListSvg);
        } else {
            $error = 'No se pudo guardar el archivo. Verifique permisos de la carpeta assets/banners.';
        }
    }
}

/* ── Handle CRUD ────────────────────────────────────────── */
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add') {
            $oid = (int) ($_POST['observatory_id'] ?? 0);
            if ($oid < 1) {
                throw new RuntimeException('Seleccione un observatorio.');
            }
            // Use uploaded image if present, otherwise form select
            $img = $uploadedPath !== '' ? $uploadedPath : cms_normalize_media_url((string) ($_POST['image_url'] ?? ''));
            if ($img !== '' && !cms_is_safe_media_path($img)) {
                throw new RuntimeException('Ruta de imagen no permitida o archivo inexistente.');
            }
            $title = trim($_POST['title'] ?? '');
            $text  = trim($_POST['slide_text'] ?? '');
            // At least one of: image, title, or text
            if ($img === '' && $title === '' && $text === '') {
                throw new RuntimeException('Agregue al menos una imagen, título o texto.');
            }
            $linkUrl = trim($_POST['link_url'] ?? '');
            if ($linkUrl !== '' && !preg_match('#^(https?://|/|\.\./|\?|#)#i', $linkUrl)) {
                throw new RuntimeException('El enlace debe iniciar con http://, https://, / o ../');
            }
            $stmt = $pdo->prepare('INSERT INTO cms_microsite_hero_slides (observatory_id, sort_order, title, slide_text, image_url, link_url, is_active) VALUES (?,?,?,?,?,?,1)');
            $stmt->execute([
                $oid,
                (int) ($_POST['sort_order'] ?? 0),
                $title,
                $text,
                $img === '' ? null : $img,
                $linkUrl === '' ? null : $linkUrl,
            ]);
            $message .= ($message ? '<br>' : '') . 'Diapositiva creada.';
        } elseif ($action === 'save' && isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            if ($id < 1) {
                throw new RuntimeException('ID no válido.');
            }
            $img = $uploadedPath !== '' ? $uploadedPath : cms_normalize_media_url((string) ($_POST['image_url'] ?? ''));
            if ($img !== '' && !cms_is_safe_media_path($img)) {
                throw new RuntimeException('Ruta de imagen no permitida o archivo inexistente.');
            }
            $title = trim($_POST['title'] ?? '');
            $text  = trim($_POST['slide_text'] ?? '');
            $linkUrl = trim($_POST['link_url'] ?? '');
            if ($linkUrl !== '' && !preg_match('#^(https?://|/|\.\./|\?|#)#i', $linkUrl)) {
                throw new RuntimeException('El enlace debe iniciar con http://, https://, / o ../');
            }
            $stmt = $pdo->prepare('UPDATE cms_microsite_hero_slides SET sort_order = ?, title = ?, slide_text = ?, image_url = ?, link_url = ? WHERE id = ?');
            $stmt->execute([
                (int) ($_POST['sort_order'] ?? 0),
                $title,
                $text,
                $img === '' ? null : $img,
                $linkUrl === '' ? null : $linkUrl,
                $id,
            ]);
            $message .= ($message ? '<br>' : '') . 'Diapositiva actualizada.';
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            $pdo->prepare('DELETE FROM cms_microsite_hero_slides WHERE id = ?')->execute([(int) $_POST['id']]);
            $message = 'Eliminada.';
        } elseif ($action === 'toggle' && isset($_POST['id'])) {
            $pdo->prepare('UPDATE cms_microsite_hero_slides SET is_active = 1 - is_active WHERE id = ?')->execute([(int) $_POST['id']]);
            $message = 'Estado actualizado.';
        }
    } catch (Throwable $e) {
        $error .= ($error ? '<br>' : '') . ($e->getMessage() ?: 'Error al guardar.');
    }
}

$rows = [];
if ($pdo) {
    try {
        $rows = $pdo->query(
            'SELECT s.*, o.slug AS obs_slug, o.name AS obs_name FROM cms_microsite_hero_slides s INNER JOIN observatories o ON o.id = s.observatory_id ORDER BY o.id ASC, s.sort_order ASC, s.id ASC'
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = 'Ejecute la migración database/migrations/009_cms_media_hero.sql';
    }
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = null;
if ($editId > 0 && $pdo) {
    $st = $pdo->prepare('SELECT * FROM cms_microsite_hero_slides WHERE id = ?');
    $st->execute([$editId]);
    $editRow = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-1">Carrusel hero · micrositios</h1>
            <p class="small text-muted mb-3">Gestione las diapositivas del carrusel superior en cada observatorio. Puede subir imágenes o elegir de las existentes.</p>

            <!-- Image size guide -->
            <div class="alert alert-info py-2 px-3 d-flex align-items-start gap-2" style="font-size:.85rem;border-left:4px solid #0d6efd">
                <i class="bi bi-image" style="font-size:1.1rem;margin-top:2px"></i>
                <div>
                    <strong>Tamaño recomendado de imágenes:</strong> <code>2280 × 600 px</code> (mínimo 1140 × 300 px).
                    Relación de aspecto ~3.8:1. Formato JPG o WebP, peso máximo 400 KB ideal.
                    La imagen cubrirá todo el ancho del banner; se oscurece automáticamente para que el texto sea legible.
                    El título y texto son <strong>opcionales</strong>: puede usar solo imagen, solo texto, o ambos.
                </div>
            </div>

            <?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <!-- Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 mb-3"><?= $editRow ? '<i class="bi bi-pencil"></i> Editar diapositiva #' . (int) $editRow['id'] : '<i class="bi bi-plus-circle"></i> Nueva diapositiva' ?></h2>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?= $editRow ? 'save' : 'add' ?>">
                        <?php if ($editRow): ?>
                            <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
                        <?php endif; ?>

                        <!-- Row 1: Observatory + Order -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Observatorio</label>
                                <?php if ($editRow): ?>
                                    <select class="form-select" disabled>
                                        <?php foreach ($obsRows as $o): ?>
                                            <option value="<?= (int) $o['id'] ?>" <?= (int) $editRow['observatory_id'] === (int) $o['id'] ? 'selected' : '' ?>><?= htmlspecialchars($o['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <select name="observatory_id" class="form-select" required>
                                        <?php foreach ($obsRows as $o): ?>
                                            <option value="<?= (int) $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Orden</label>
                                <input type="number" name="sort_order" class="form-control" value="<?= $editRow ? (int) $editRow['sort_order'] : 0 ?>" min="0">
                            </div>
                        </div>

                        <!-- Row 2: Title + Text (OPTIONAL) -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Título <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Ej: Coyuntura económica territorial" value="<?= $editRow ? htmlspecialchars($editRow['title']) : '' ?>">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Texto <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="text" name="slide_text" class="form-control" placeholder="Ej: Monitoree TRM, inflación, empleo y variables macro" value="<?= $editRow ? htmlspecialchars((string) ($editRow['slide_text'] ?? '')) : '' ?>">
                            </div>
                        </div>

                        <!-- Row 3: Image selection -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Elegir imagen existente</label>
                                <select name="image_url" class="form-select" id="imageSelect" onchange="previewImage(this.value)">
                                    <option value="">— Sin imagen —</option>
                                    <?php if ($assetListBanners): ?>
                                        <optgroup label="📁 assets/banners (subidas)">
                                        <?php foreach ($assetListBanners as $path): ?>
                                            <option value="<?= htmlspecialchars($path) ?>" <?= ($editRow && ($editRow['image_url'] ?? '') === $path) ? 'selected' : '' ?>><?= htmlspecialchars(basename($path)) ?></option>
                                        <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <optgroup label="📁 assets/svg (originales)">
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
                                <input type="file" name="image_file" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" onchange="previewUpload(this)">
                                <div class="form-text">JPG, PNG, WebP, GIF o SVG. Máx 5 MB. Se guarda en <code>assets/banners/</code></div>
                            </div>
                        </div>

                        <!-- Row 4: Link URL -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Enlace "Ver más" <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="text" name="link_url" class="form-control" placeholder="Ej: noticia.php?slug=mi-noticia  o  https://ejemplo.com" value="<?= $editRow ? htmlspecialchars((string) ($editRow['link_url'] ?? '')) : '' ?>">
                                <div class="form-text">URL completa (https://...) o ruta interna (noticia.php?slug=...). Si se completa, aparecerá un botón "Ver más" en el banner.</div>
                            </div>
                        </div>

                        <!-- Image preview -->
                        <div id="imagePreview" class="mb-3" style="display:none">
                            <label class="form-label fw-semibold small text-muted">Vista previa</label>
                            <div style="max-width:100%;height:120px;border-radius:10px;overflow:hidden;border:1px solid #dee2e6;background:#f8f9fa;position:relative">
                                <img id="previewImg" src="" alt="Preview" style="width:100%;height:100%;object-fit:cover;display:block">
                                <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.2) 0%,rgba(0,0,0,.5) 40%,rgba(0,0,0,.7) 100%)"></div>
                                <div id="previewText" style="position:absolute;bottom:12px;left:16px;color:#fff;z-index:2">
                                    <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.06em;border:1px solid rgba(255,255,255,.4);border-radius:999px;padding:2px 8px;display:inline-block;margin-bottom:4px">Observatorio</div>
                                    <div style="font-weight:800;font-size:1rem;text-shadow:0 2px 8px rgba(0,0,0,.6)">Título del banner</div>
                                    <div style="font-size:.8rem;opacity:.9;text-shadow:0 1px 4px rgba(0,0,0,.5)">Texto descriptivo</div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="bi bi-check-lg"></i> <?= $editRow ? 'Guardar cambios' : 'Agregar diapositiva' ?>
                            </button>
                            <?php if ($editRow): ?>
                                <a class="btn btn-outline-secondary" href="<?= htmlspecialchars(app_url('website/cms/microsite-hero.php')) ?>">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-sm align-middle bg-white shadow-sm rounded">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">Preview</th>
                            <th>Observatorio</th>
                            <th style="width:50px">Orden</th>
                            <th>Título</th>
                            <th>Texto</th>
                            <th>Enlace</th>
                            <th>Activo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay diapositivas. Agregue una arriba.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td>
                                <?php if (!empty($r['image_url'])): ?>
                                    <img src="../<?= htmlspecialchars($r['image_url']) ?>" alt="" style="width:56px;height:32px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6">
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="small"><?= htmlspecialchars($r['obs_name']) ?></td>
                            <td class="text-center"><?= (int) $r['sort_order'] ?></td>
                            <td><?= htmlspecialchars($r['title'] ?: '—') ?></td>
                            <td class="small text-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars((string) ($r['slide_text'] ?? '')) ?: '—' ?></td>
                            <td class="small">
                                <?php if (!empty($r['link_url'])): ?>
                                    <a href="<?= htmlspecialchars($r['link_url']) ?>" target="_blank" rel="noopener" title="<?= htmlspecialchars($r['link_url']) ?>"><i class="bi bi-link-45deg"></i> Ver</a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($r['is_active'])): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars(app_url('website/cms/microsite-hero.php?edit=' . (int) $r['id'])) ?>" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta diapositiva?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" title="Activar/Desactivar"><i class="bi bi-toggle-on"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- JavaScript for preview -->
            <script>
            function previewImage(val) {
                var box = document.getElementById('imagePreview');
                var img = document.getElementById('previewImg');
                if (val) {
                    img.src = '../' + val;
                    box.style.display = 'block';
                    updatePreviewText();
                    // Clear file upload if selecting existing
                    var fileInput = document.querySelector('input[name="image_file"]');
                    if (fileInput) fileInput.value = '';
                } else {
                    box.style.display = 'none';
                }
            }
            function previewUpload(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = document.getElementById('previewImg');
                        img.src = e.target.result;
                        document.getElementById('imagePreview').style.display = 'block';
                        updatePreviewText();
                        // Clear dropdown if uploading
                        document.getElementById('imageSelect').value = '';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
            function updatePreviewText() {
                var title = document.querySelector('input[name="title"]').value || 'Título del banner';
                var text = document.querySelector('input[name="slide_text"]').value || 'Texto descriptivo';
                var obs = document.querySelector('select[name="observatory_id"]');
                var obsName = obs ? obs.options[obs.selectedIndex].text : 'Observatorio';
                var box = document.getElementById('previewText');
                box.children[0].textContent = obsName;
                box.children[1].textContent = title;
                box.children[2].textContent = text;
            }
            // Live preview on text input
            document.addEventListener('DOMContentLoaded', function() {
                var titleInput = document.querySelector('input[name="title"]');
                var textInput = document.querySelector('input[name="slide_text"]');
                if (titleInput) titleInput.addEventListener('input', updatePreviewText);
                if (textInput) textInput.addEventListener('input', updatePreviewText);
                // Show preview if editing and has image
                <?php if ($editRow && !empty($editRow['image_url'])): ?>
                previewImage('<?= addslashes($editRow['image_url']) ?>');
                <?php endif; ?>
            });
            </script>
<?php require __DIR__ . '/includes/footer.php'; ?>
