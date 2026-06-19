<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('tabs', true);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/cms_microsite_sections.php';

$cmsTitle = 'Pestañas micrositios';
$cmsNav = 'tabs';
$pdo = cms_pdo();
$message = '';
$error = '';

/* ── Observatorios ─────────────────────────────────────────────────────── */
$obsRows = [];
if ($pdo) {
    try {
        $obsRows = $pdo->query('SELECT id, slug, name FROM observatories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = 'Base de datos no lista.';
    }
}

/* ── Layouts soportados (sincronizados con observatorio.php) ───────────── */
$layoutOptions = [
    'standard'  => 'Texto libre (WYSIWYG)',
    'chips'     => 'Pestañas internas con chips (sub-secciones)',
    'accordion' => 'Acordeón (sub-secciones desplegables)',
    'cards'     => 'Rejilla de mini-tarjetas (sub-secciones)',
    'split'     => 'Imagen + texto a dos columnas',
];

/* ── Iconos sugeridos (FontAwesome) ────────────────────────────────────── */
$iconHints = [
    'fa-landmark', 'fa-circle-info', 'fa-chart-line', 'fa-route',
    'fa-triangle-exclamation', 'fa-handshake-angle', 'fa-scale-balanced',
    'fa-bullhorn', 'fa-file-lines', 'fa-people-group', 'fa-book',
    'fa-hand-holding-heart', 'fa-shield-halved', 'fa-graduation-cap',
];

/* ── Selección actual del observatorio en la UI ────────────────────────── */
$currentObsId = isset($_GET['obs']) ? (int) $_GET['obs'] : 0;
if (!$currentObsId && $obsRows) {
    foreach ($obsRows as $o) {
        if ($o['slug'] === 'genero') { $currentObsId = (int) $o['id']; break; }
    }
    if (!$currentObsId) {
        $currentObsId = (int) $obsRows[0]['id'];
    }
}
$currentObsRow = null;
foreach ($obsRows as $o) {
    if ((int) $o['id'] === $currentObsId) {
        $currentObsRow = $o;
        break;
    }
}

/* ── Acciones POST ─────────────────────────────────────────────────────── */
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save_section') {
            $oid       = (int) ($_POST['observatory_id'] ?? 0);
            $editId    = (int) ($_POST['id'] ?? 0);
            $parentId  = ($_POST['parent_id'] ?? '') === '' ? null : (int) $_POST['parent_id'];
            $key       = trim((string) ($_POST['section_key'] ?? ''));
            $title     = trim((string) ($_POST['title'] ?? ''));
            $subtitle  = trim((string) ($_POST['subtitle'] ?? ''));
            $body      = (string) ($_POST['body_html'] ?? '');
            $layout    = trim((string) ($_POST['layout'] ?? 'standard'));
            $icon      = trim((string) ($_POST['icon'] ?? ''));
            $imageUrl  = trim((string) ($_POST['image_url'] ?? ''));
            $ctaLabel  = trim((string) ($_POST['cta_label'] ?? ''));
            $ctaUrl    = trim((string) ($_POST['cta_url'] ?? ''));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);
            $isActive  = isset($_POST['is_active']) ? 1 : 0;

            if ($oid < 1) throw new RuntimeException('Seleccione un observatorio.');
            if ($title === '') throw new RuntimeException('El título es obligatorio.');
            if (!array_key_exists($layout, $layoutOptions)) {
                $layout = 'standard';
            }
            if ($key === '') {
                $key = preg_replace('/[^a-z0-9_-]+/i', '-', strtolower($title));
                $key = trim((string) $key, '-');
                if ($key === '') $key = 'sec-' . substr((string) time(), -6);
                $key = substr($key, 0, 60);
            }

            if ($editId > 0) {
                $sql = 'UPDATE cms_microsite_sections SET
                            parent_id = ?, section_key = ?, title = ?, subtitle = ?, body_html = ?,
                            layout = ?, icon = ?, image_url = ?, cta_label = ?, cta_url = ?,
                            sort_order = ?, is_active = ?
                        WHERE id = ? AND observatory_id = ?';
                $st = $pdo->prepare($sql);
                $st->execute([
                    $parentId, $key, $title, $subtitle ?: null, $body !== '' ? $body : null,
                    $layout, $icon ?: null, $imageUrl ?: null, $ctaLabel ?: null, $ctaUrl ?: null,
                    $sortOrder, $isActive, $editId, $oid,
                ]);
                $message = 'Sección actualizada.';
            } else {
                $sql = 'INSERT INTO cms_microsite_sections
                            (observatory_id, parent_id, section_key, title, subtitle, body_html,
                             layout, icon, image_url, cta_label, cta_url, sort_order, is_active)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
                $st = $pdo->prepare($sql);
                $st->execute([
                    $oid, $parentId, $key, $title, $subtitle ?: null, $body !== '' ? $body : null,
                    $layout, $icon ?: null, $imageUrl ?: null, $ctaLabel ?: null, $ctaUrl ?: null,
                    $sortOrder, $isActive,
                ]);
                $newId = (int) $pdo->lastInsertId();
                $message = 'Sección creada (#' . $newId . ').';
                $editId = $newId;
            }
            header('Location: ?obs=' . $oid . '&edit=' . $editId . '&saved=1');
            exit;
        }

        if ($action === 'delete_section') {
            $delId = (int) ($_POST['id'] ?? 0);
            if ($delId > 0) {
                // borrar hijas primero
                $pdo->prepare('DELETE FROM cms_microsite_sections WHERE parent_id = ?')->execute([$delId]);
                $pdo->prepare('DELETE FROM cms_microsite_sections WHERE id = ?')->execute([$delId]);
                $message = 'Sección eliminada.';
            }
        }

        if ($action === 'toggle_section') {
            $tid = (int) ($_POST['id'] ?? 0);
            if ($tid > 0) {
                $pdo->prepare('UPDATE cms_microsite_sections SET is_active = 1 - is_active WHERE id = ?')->execute([$tid]);
                $message = 'Estado actualizado.';
            }
        }

        if ($action === 'move_section') {
            $mid = (int) ($_POST['id'] ?? 0);
            $dir = (string) ($_POST['dir'] ?? 'up');
            if ($mid > 0) {
                $row = $pdo->prepare('SELECT observatory_id, parent_id, sort_order FROM cms_microsite_sections WHERE id = ? LIMIT 1');
                $row->execute([$mid]);
                $r = $row->fetch(PDO::FETCH_ASSOC);
                if ($r) {
                    $delta = $dir === 'down' ? 1 : -1;
                    $newOrder = max(0, (int) $r['sort_order'] + $delta);
                    $pdo->prepare('UPDATE cms_microsite_sections SET sort_order = ? WHERE id = ?')
                        ->execute([$newOrder, $mid]);
                    $message = 'Orden actualizado.';
                }
            }
        }
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'No se pudo guardar.';
    }
}

if (isset($_GET['saved'])) {
    $message = $message ?: 'Sección guardada.';
}

/* ── Cargar árbol del observatorio actual ──────────────────────────────── */
$tree = [];
if ($pdo && $currentObsId) {
    try {
        $st = $pdo->prepare('SELECT * FROM cms_microsite_sections WHERE observatory_id = ? ORDER BY parent_id IS NULL DESC, sort_order ASC, id ASC');
        $st->execute([$currentObsId]);
        $all = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $all = [];
        $error = $error ?: 'Ejecute la migración database/migrations/013_microsite_sections.sql';
    }
    $byId = [];
    foreach ($all as $r) {
        $r['children'] = [];
        $byId[(int) $r['id']] = $r;
    }
    foreach ($byId as $id => $row) {
        if ($row['parent_id'] !== null && isset($byId[(int) $row['parent_id']])) {
            $byId[(int) $row['parent_id']]['children'][] = &$byId[$id];
        }
    }
    foreach ($byId as $id => $row) {
        if ($row['parent_id'] === null) {
            $tree[] = &$byId[$id];
        }
    }
}

/* ── Datos para el formulario (modo edición) ───────────────────────────── */
$editId  = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = null;
$parentOptions = [];
if ($pdo && $currentObsId) {
    $parentOptions = cms_microsite_sections_flat($pdo, $currentObsId);
    if ($editId > 0) {
        $st = $pdo->prepare('SELECT * FROM cms_microsite_sections WHERE id = ? AND observatory_id = ? LIMIT 1');
        $st->execute([$editId, $currentObsId]);
        $editRow = $st->fetch(PDO::FETCH_ASSOC) ?: null;
        if ($editRow) {
            // No permitir auto-padre
            $parentOptions = array_values(array_filter($parentOptions, static function ($r) use ($editId) {
                return (int) $r['id'] !== $editId;
            }));
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<style>
    .ms-tree { font-size: .92rem; }
    .ms-tree .ms-root { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:.75rem 1rem; margin-bottom:.65rem; box-shadow:0 1px 3px rgba(15,31,50,.06); }
    .ms-tree .ms-root.is-inactive { background:#f5f7fa; opacity:.7; }
    .ms-tree .ms-root-head { display:flex; align-items:center; gap:.6rem; }
    .ms-tree .ms-root-head .ms-title { font-weight:700; color:#0f1f32; }
    .ms-tree .ms-root-head .ms-key { font-family:'SFMono-Regular',Consolas,monospace; font-size:.72rem; color:#475569; background:#eef2f7; padding:.1rem .45rem; border-radius:6px; }
    .ms-tree .ms-icon { width:32px; height:32px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:linear-gradient(135deg, rgba(190,24,93,.18), rgba(190,24,93,.08)); color:#be185d; flex:0 0 auto; }
    .ms-tree .ms-actions { margin-left:auto; display:flex; gap:.25rem; flex-wrap:wrap; }
    .ms-tree .ms-children { margin:.5rem 0 0 2.6rem; padding-left:.85rem; border-left:2px dashed #d8dee9; display:grid; gap:.35rem; }
    .ms-tree .ms-child { display:flex; align-items:center; gap:.55rem; padding:.35rem .55rem; background:#fafbfc; border:1px solid #e7ecf2; border-radius:8px; }
    .ms-tree .ms-child.is-inactive { opacity:.55; }
    .ms-tree .ms-child .ms-sub-title { color:#374151; font-weight:600; font-size:.86rem; }
    .ms-tree .badge-layout { font-size:.66rem; background:#e0e7ff; color:#3730a3; border-radius:6px; padding:.12rem .45rem; font-family:'SFMono-Regular',Consolas,monospace; }
    .ms-tree .badge-off    { font-size:.66rem; background:#fee2e2; color:#991b1b; border-radius:6px; padding:.12rem .45rem; }
    .ms-tree .ms-add-child { color:#0d6efd; font-size:.78rem; }
    .ms-empty { background:#fff; border:2px dashed #cbd5e1; border-radius:14px; padding:2rem; text-align:center; color:#64748b; }

    .obs-switcher { display:flex; flex-wrap:wrap; gap:.4rem; margin-bottom:1rem; }
    .obs-switcher a { padding:.45rem .9rem; border-radius:999px; background:#fff; border:1.5px solid #e2e8f0; color:#475569; text-decoration:none; font-weight:600; font-size:.82rem; transition:all .15s; }
    .obs-switcher a:hover { transform:translateY(-1px); border-color:#be185d; color:#be185d; }
    .obs-switcher a.active { background:linear-gradient(135deg,#be185d,#9d174d); color:#fff; border-color:transparent; box-shadow:0 4px 12px rgba(190,24,93,.32); }

    .form-help-icon { color:#94a3b8; cursor:help; }
    .preview-image { max-height:120px; border-radius:8px; border:1px solid #dee2e6; }
    .legacy-warn { background:#fff7ed; border-left:4px solid #f59e0b; padding:.75rem 1rem; border-radius:8px; font-size:.85rem; }
</style>

<h1 class="h4 mb-1">Contenido por pestañas <span class="text-muted">· Micrositios</span></h1>
<p class="small text-muted mb-3">
    Edite las pestañas que ve el público en <code>observatorio.php?slug=…</code>. Cada pestaña raíz se muestra como un botón en la barra de pestañas; las sub-secciones aparecen como chips, acordeones o tarjetas según el <em>layout</em> elegido.
    El editor admite imágenes, listas, tablas y vista de código para los avanzados.
</p>

<?php if ($message): ?><div class="alert alert-success py-2"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- Switcher de observatorio -->
<div class="obs-switcher">
    <?php foreach ($obsRows as $o): ?>
        <a href="?obs=<?= (int) $o['id'] ?>" class="<?= (int) $o['id'] === $currentObsId ? 'active' : '' ?>"><?= htmlspecialchars($o['name']) ?></a>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <!-- Columna izquierda: árbol del observatorio actual -->
    <div class="col-lg-5">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 class="h6 mb-0"><i class="fa-solid fa-sitemap me-1"></i> Estructura de pestañas</h2>
            <a class="btn btn-sm btn-primary" href="?obs=<?= (int) $currentObsId ?>&edit=0&new=1"><i class="fa-solid fa-plus"></i> Nueva pestaña</a>
        </div>
        <?php if (!$tree): ?>
            <div class="ms-empty">
                <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                <p class="mb-2">Aún no hay pestañas configuradas para este observatorio.</p>
                <a class="btn btn-sm btn-primary" href="?obs=<?= (int) $currentObsId ?>&edit=0&new=1">Crear la primera pestaña</a>
                <?php if ($currentObsRow && $currentObsRow['slug'] === 'genero'): ?>
                    <hr>
                    <p class="small mb-1"><strong>¿Quieres precargar las 9 pestañas históricas?</strong></p>
                    <p class="small text-muted">Ejecute el script: <code>php database/scripts/migrate_genero_legacy_content.php</code></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="ms-tree">
            <?php foreach ($tree as $root): ?>
                <div class="ms-root <?= empty($root['is_active']) ? 'is-inactive' : '' ?>">
                    <div class="ms-root-head">
                        <span class="ms-icon"><i class="fa-solid <?= htmlspecialchars($root['icon'] ?: 'fa-layer-group') ?>"></i></span>
                        <div>
                            <div class="ms-title"><?= htmlspecialchars($root['title']) ?></div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <span class="ms-key"><?= htmlspecialchars($root['section_key']) ?></span>
                                <span class="badge-layout"><?= htmlspecialchars($root['layout']) ?></span>
                                <?php if (empty($root['is_active'])): ?><span class="badge-off">inactiva</span><?php endif; ?>
                            </div>
                        </div>
                        <div class="ms-actions">
                            <a class="btn btn-sm btn-outline-primary" href="?obs=<?= (int) $currentObsId ?>&edit=<?= (int) $root['id'] ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <a class="btn btn-sm btn-outline-secondary" href="?obs=<?= (int) $currentObsId ?>&new=1&parent=<?= (int) $root['id'] ?>" title="Añadir sub-sección"><i class="fa-solid fa-plus"></i></a>
                            <form method="post" class="d-inline"><input type="hidden" name="action" value="toggle_section"><input type="hidden" name="id" value="<?= (int) $root['id'] ?>"><button class="btn btn-sm btn-outline-secondary" title="Activar/Desactivar"><i class="fa-solid fa-toggle-on"></i></button></form>
                            <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta pestaña y todas sus sub-secciones?');"><input type="hidden" name="action" value="delete_section"><input type="hidden" name="id" value="<?= (int) $root['id'] ?>"><button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button></form>
                        </div>
                    </div>
                    <?php if (!empty($root['children'])): ?>
                        <div class="ms-children">
                            <?php foreach ($root['children'] as $child): ?>
                                <div class="ms-child <?= empty($child['is_active']) ? 'is-inactive' : '' ?>">
                                    <span class="ms-icon" style="background:linear-gradient(135deg,rgba(99,102,241,.18),rgba(99,102,241,.08));color:#4f46e5;width:26px;height:26px;font-size:.78rem"><i class="fa-solid <?= htmlspecialchars($child['icon'] ?: 'fa-circle-dot') ?>"></i></span>
                                    <div>
                                        <div class="ms-sub-title"><?= htmlspecialchars($child['title']) ?></div>
                                        <div class="d-flex gap-1 mt-1"><span class="ms-key" style="font-size:.66rem"><?= htmlspecialchars($child['section_key']) ?></span><?php if (empty($child['is_active'])): ?><span class="badge-off">off</span><?php endif; ?></div>
                                    </div>
                                    <div class="ms-actions">
                                        <a class="btn btn-sm btn-outline-primary py-0 px-2" href="?obs=<?= (int) $currentObsId ?>&edit=<?= (int) $child['id'] ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                        <form method="post" class="d-inline"><input type="hidden" name="action" value="toggle_section"><input type="hidden" name="id" value="<?= (int) $child['id'] ?>"><button class="btn btn-sm btn-outline-secondary py-0 px-2"><i class="fa-solid fa-toggle-on"></i></button></form>
                                        <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta sub-sección?');"><input type="hidden" name="action" value="delete_section"><input type="hidden" name="id" value="<?= (int) $child['id'] ?>"><button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="fa-solid fa-xmark"></i></button></form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <a class="ms-add-child" href="?obs=<?= (int) $currentObsId ?>&new=1&parent=<?= (int) $root['id'] ?>"><i class="fa-solid fa-plus"></i> Añadir sub-sección</a>
                        </div>
                    <?php else: ?>
                        <div class="mt-2">
                            <a class="ms-add-child" href="?obs=<?= (int) $currentObsId ?>&new=1&parent=<?= (int) $root['id'] ?>"><i class="fa-solid fa-plus"></i> Añadir sub-sección</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($currentObsRow): ?>
            <div class="mt-3 small">
                <i class="fa-solid fa-eye me-1"></i>
                Previsualizar en el portal: <a href="<?= htmlspecialchars(app_url('website/observatorio.php?slug=' . $currentObsRow['slug'])) ?>" target="_blank" rel="noopener">observatorio.php?slug=<?= htmlspecialchars($currentObsRow['slug']) ?> <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Columna derecha: formulario -->
    <div class="col-lg-7">
        <?php
        $isEdit = $editRow !== null;
        $isNew  = isset($_GET['new']);
        $showForm = $isEdit || $isNew;
        $defaultParent = isset($_GET['parent']) ? (int) $_GET['parent'] : null;
        ?>
        <?php if (!$showForm): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="fa-solid fa-arrow-left fa-2x mb-3"></i>
                    <p class="mb-1">Seleccione una pestaña a la izquierda para editarla, o cree una nueva.</p>
                    <p class="small mb-0">Cada pestaña se publica al instante cuando esté <strong>activa</strong>.</p>
                </div>
            </div>
        <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $isEdit ? '<i class="fa-solid fa-pen me-1"></i> Editar sección #' . (int) $editRow['id'] : '<i class="fa-solid fa-plus me-1"></i> Nueva sección' ?></h2>
                <form method="post" id="msForm">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="observatory_id" value="<?= (int) $currentObsId ?>">
                    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>"><?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pestaña padre <i class="fa-solid fa-circle-question form-help-icon" title="Déjelo en blanco para crear una pestaña raíz. Seleccione una raíz para crear una sub-sección (chip/accordion/card según el layout del padre)."></i></label>
                            <select name="parent_id" class="form-select">
                                <option value="">— Pestaña raíz —</option>
                                <?php foreach ($parentOptions as $po): if ($po['depth'] > 0) continue; ?>
                                    <?php
                                    $sel = false;
                                    if ($isEdit) $sel = ((int) ($editRow['parent_id'] ?? 0) === (int) $po['id']);
                                    elseif ($defaultParent) $sel = ($defaultParent === (int) $po['id']);
                                    ?>
                                    <option value="<?= (int) $po['id'] ?>" <?= $sel ? 'selected' : '' ?>><?= htmlspecialchars($po['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Layout (cómo se muestra) <i class="fa-solid fa-circle-question form-help-icon" title="Aplica solo a pestañas raíz. Determina cómo se renderizan las sub-secciones."></i></label>
                            <select name="layout" class="form-select">
                                <?php foreach ($layoutOptions as $val => $lab): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= ($isEdit && $editRow['layout'] === $val) ? 'selected' : ($val === 'standard' ? 'selected' : '') ?>><?= htmlspecialchars($lab) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Título *</label>
                            <input type="text" name="title" class="form-control" required maxlength="255" value="<?= $isEdit ? htmlspecialchars((string) $editRow['title']) : '' ?>" placeholder="Ej. Barreras de acceso">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Clave (slug) <i class="fa-solid fa-circle-question form-help-icon" title="Se autogenera del título si lo deja vacío. Use letras, números y guiones."></i></label>
                            <input type="text" name="section_key" class="form-control font-monospace" maxlength="64" value="<?= $isEdit ? htmlspecialchars((string) $editRow['section_key']) : '' ?>" placeholder="barreras">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Subtítulo / introducción corta <span class="text-muted small fw-normal">(opcional)</span></label>
                            <input type="text" name="subtitle" class="form-control" maxlength="512" value="<?= $isEdit ? htmlspecialchars((string) ($editRow['subtitle'] ?? '')) : '' ?>" placeholder="Frase breve que se muestra bajo el título de la pestaña.">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Contenido <span class="text-muted small fw-normal">(editor visual + vista código)</span></label>
                            <textarea name="body_html" id="msEditor" class="form-control" rows="14"><?= $isEdit ? htmlspecialchars((string) ($editRow['body_html'] ?? '')) : '' ?></textarea>
                            <div class="form-text">Use el botón <strong>Imagen</strong> de la barra para subir archivos (van a <code>uploads/cms/AÑO/MES/</code>). Para visualizar el HTML use el botón <strong>&lt;/&gt;</strong>.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Icono <span class="text-muted small fw-normal">(FontAwesome)</span></label>
                            <input type="text" name="icon" class="form-control font-monospace" list="iconHints" maxlength="64" value="<?= $isEdit ? htmlspecialchars((string) ($editRow['icon'] ?? '')) : '' ?>" placeholder="fa-landmark">
                            <datalist id="iconHints">
                                <?php foreach ($iconHints as $ic): ?><option value="<?= htmlspecialchars($ic) ?>"><?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Imagen destacada <span class="text-muted small fw-normal">(URL relativa o https://)</span></label>
                            <div class="d-flex gap-2 align-items-start">
                                <input type="text" name="image_url" id="msImage" class="form-control" maxlength="512" value="<?= $isEdit ? htmlspecialchars((string) ($editRow['image_url'] ?? '')) : '' ?>" placeholder="assets/svg/img-genero/...">
                                <button type="button" class="btn btn-outline-secondary" id="msImageUpload"><i class="fa-solid fa-upload"></i></button>
                            </div>
                            <?php if ($isEdit && !empty($editRow['image_url'])): ?>
                                <img src="../<?= htmlspecialchars((string) $editRow['image_url']) ?>" class="preview-image mt-2" alt="">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Botón (texto) <span class="text-muted small fw-normal">(opcional)</span></label>
                            <input type="text" name="cta_label" class="form-control" maxlength="120" value="<?= $isEdit ? htmlspecialchars((string) ($editRow['cta_label'] ?? '')) : '' ?>" placeholder="Ej. Ver guía completa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Botón (URL)</label>
                            <input type="text" name="cta_url" class="form-control" maxlength="512" value="<?= $isEdit ? htmlspecialchars((string) ($editRow['cta_url'] ?? '')) : '' ?>" placeholder="rutaVictimas.html  o  https://...">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" name="sort_order" class="form-control" min="0" value="<?= $isEdit ? (int) $editRow['sort_order'] : 0 ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="ms_active" value="1" <?= ($isEdit ? (int) $editRow['is_active'] : 1) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="ms_active">Sección activa (visible en el portal)</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar sección</button>
                        <a class="btn btn-outline-secondary" href="?obs=<?= (int) $currentObsId ?>">Cancelar</a>
                        <?php if ($isEdit): ?>
                            <form method="post" class="ms-auto" onsubmit="return confirm('¿Eliminar esta sección y sus hijas?');">
                                <input type="hidden" name="action" value="delete_section">
                                <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
                                <button class="btn btn-outline-danger" type="submit"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($showForm): ?>
<!-- TinyMCE 7 (vía CDN gratuito) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.0/tinymce.min.js" referrerpolicy="origin"></script>
<script>
(function () {
    if (!window.tinymce) return;
    const uploadUrl = '<?= htmlspecialchars(app_url('website/cms/upload-media.php')) ?>';
    const websiteBase = '<?= htmlspecialchars(rtrim((string) app_url('website/'), '/')) ?>/';

    tinymce.init({
        selector: '#msEditor',
        license_key: 'gpl',
        language: 'es',
        language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@25.2.10/langs7/es.js',
        height: 520,
        menubar: 'edit insert view format table',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount autoresize',
        toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat | code preview fullscreen | help',
        toolbar_mode: 'sliding',
        branding: false,
        promotion: false,
        relative_urls: false,
        remove_script_host: true,
        document_base_url: websiteBase,
        content_style: 'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;font-size:15px;color:#1f2937;padding:14px;} h1,h2,h3{color:#0f1f32;} a{color:#be185d;} img{max-width:100%;height:auto;border-radius:6px;}',
        images_upload_url: uploadUrl,
        automatic_uploads: true,
        images_reuse_filename: true,
        file_picker_types: 'image media',
        paste_data_images: true,
        block_formats: 'Párrafo=p;Encabezado 2=h2;Encabezado 3=h3;Encabezado 4=h4;Cita=blockquote;Código=pre',
        // TinyMCE espera una respuesta JSON { location: "..." } — eso es exactamente lo que devuelve upload-media.php
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            const xhr = new XMLHttpRequest();
            xhr.open('POST', uploadUrl);
            xhr.upload.onprogress = (e) => progress(e.total ? (e.loaded / e.total) * 100 : 0);
            xhr.onload = () => {
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject({ message: 'HTTP error: ' + xhr.status, remove: true });
                    return;
                }
                let json;
                try { json = JSON.parse(xhr.responseText); }
                catch (e) { reject({ message: 'Respuesta inválida del servidor.', remove: true }); return; }
                if (!json.location) {
                    reject({ message: (json.error && json.error.message) || 'Error al subir.', remove: true });
                    return;
                }
                resolve(json.location);
            };
            xhr.onerror = () => reject({ message: 'Fallo de red al subir la imagen.', remove: true });
            xhr.send(formData);
        }),
    });

    // Botón para subir imagen destacada (no inline)
    const featuredBtn = document.getElementById('msImageUpload');
    const featuredInput = document.getElementById('msImage');
    if (featuredBtn && featuredInput) {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        document.body.appendChild(fileInput);
        featuredBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            if (!fileInput.files || !fileInput.files[0]) return;
            const fd = new FormData();
            fd.append('file', fileInput.files[0]);
            featuredBtn.disabled = true;
            featuredBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            fetch(uploadUrl, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data && data.location) featuredInput.value = data.location;
                    else alert((data && data.error && data.error.message) || 'No se pudo subir.');
                })
                .catch(() => alert('Error de red al subir.'))
                .finally(() => {
                    featuredBtn.disabled = false;
                    featuredBtn.innerHTML = '<i class="fa-solid fa-upload"></i>';
                });
        });
    }
})();
</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
