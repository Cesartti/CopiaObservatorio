<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('news', true);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/bulletins.php';

$cmsTitle = 'Boletines';
$cmsNav = 'boletines';
$pdo = cms_pdo();
$error = '';
$message = '';

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    try {
        if ($action === 'add' || $action === 'update') {
            $title = trim($_POST['title'] ?? '');
            if ($title === '') {
                throw new RuntimeException('El título es obligatorio.');
            }
            $obsId = ($_POST['observatory_id'] ?? '') === '' ? null : (int) $_POST['observatory_id'];
            $category = trim($_POST['category'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $pub = trim($_POST['published_at'] ?? '');
            $pub = ($pub !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $pub)) ? $pub : null;
            $sort = (int) ($_POST['sort_order'] ?? 0);
            $slugBase = cms_bulletin_slug($title);

            $pdf = cms_bulletin_pdf_from_request($slugBase);
            $cover = cms_bulletin_cover_from_request($slugBase);

            if ($action === 'add') {
                if ($pdf === '') {
                    throw new RuntimeException('Debe subir el PDF del boletín (o indicar una URL).');
                }
                $st = $pdo->prepare('INSERT INTO cms_bulletins (observatory_id, category, title, description, pdf_url, cover_url, published_at, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,1)');
                $st->execute([$obsId, $category ?: null, $title, $desc ?: null, $pdf, $cover ?: null, $pub, $sort]);
                $message = 'Boletín creado.';
            } else {
                // Conservar PDF/portada actuales si no se envían nuevos.
                $cur = $pdo->prepare('SELECT pdf_url, cover_url FROM cms_bulletins WHERE id = ?');
                $cur->execute([$id]);
                $row = $cur->fetch(PDO::FETCH_ASSOC) ?: [];
                $pdfFinal = $pdf !== '' ? $pdf : ($row['pdf_url'] ?? '');
                $coverFinal = $cover !== '' ? $cover : ($row['cover_url'] ?? null);
                $st = $pdo->prepare('UPDATE cms_bulletins SET observatory_id=?, category=?, title=?, description=?, pdf_url=?, cover_url=?, published_at=?, sort_order=? WHERE id=?');
                $st->execute([$obsId, $category ?: null, $title, $desc ?: null, $pdfFinal, $coverFinal ?: null, $pub, $sort, $id]);
                $message = 'Boletín actualizado.';
            }
        } elseif ($action === 'toggle') {
            $pdo->prepare('UPDATE cms_bulletins SET is_active = IF(is_active=1,0,1) WHERE id=?')->execute([$id]);
            $message = 'Estado actualizado.';
        } elseif ($action === 'delete') {
            $pdo->prepare('DELETE FROM cms_bulletins WHERE id=?')->execute([$id]);
            $message = 'Boletín eliminado.';
        }
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'No se pudo completar la operación.';
    }
}

$observatories = $pdo ? cms_bulletins_observatories($pdo) : [];
$obsName = ['' => 'General (toda la Red)'];
foreach ($observatories as $o) {
    $obsName[(string) $o['id']] = $o['name'];
}
$bulletins = cms_bulletins_fetch($pdo, 'all', false);

$editId = (int) ($_GET['edit'] ?? 0);
$editRow = null;
if ($editId && $pdo) {
    $st = $pdo->prepare('SELECT * FROM cms_bulletins WHERE id = ?');
    $st->execute([$editId]);
    $editRow = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

require __DIR__ . '/includes/header.php';
?>
<h1 class="h4 mb-3">Boletines</h1>
<p class="small text-muted">Publica boletines en PDF a nivel <strong>General</strong> o por <strong>observatorio</strong>. Aparecen en la página pública <code>boletines.php</code> y dentro de cada observatorio.</p>
<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card shadow-sm mb-4"><div class="card-body">
    <h2 class="h6"><?= $editRow ? 'Editar boletín' : 'Nuevo boletín' ?></h2>
    <form method="post" enctype="multipart/form-data" class="row g-2">
        <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'add' ?>">
        <?php if ($editRow): ?><input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>"><?php endif; ?>
        <div class="col-md-5"><label class="form-label">Título</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($editRow['title'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Observatorio</label>
            <select name="observatory_id" class="form-select">
                <?php foreach ($obsName as $val => $label): ?>
                    <option value="<?= htmlspecialchars((string) $val) ?>" <?= (string) ($editRow['observatory_id'] ?? '') === (string) $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div class="col-md-3"><label class="form-label">Categoría (opcional)</label>
            <input type="text" name="category" class="form-control" placeholder="Violencia, Salud…" value="<?= htmlspecialchars($editRow['category'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($editRow['description'] ?? '') ?></textarea></div>
        <div class="col-md-3"><label class="form-label">Fecha</label>
            <input type="date" name="published_at" class="form-control" value="<?= htmlspecialchars($editRow['published_at'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Orden</label>
            <input type="number" name="sort_order" class="form-control" value="<?= (int) ($editRow['sort_order'] ?? 0) ?>"></div>
        <div class="col-md-3"><label class="form-label">PDF <?= $editRow ? '(dejar vacío = conservar)' : '' ?></label>
            <input type="file" name="pdf_file" class="form-control" accept="application/pdf"></div>
        <div class="col-md-3"><label class="form-label">Portada (imagen, opcional)</label>
            <input type="file" name="cover_file" class="form-control" accept="image/*"></div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i><?= $editRow ? 'Guardar cambios' : 'Crear boletín' ?></button>
            <?php if ($editRow): ?><a href="boletines.php" class="btn btn-outline-secondary">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div></div>

<div class="table-responsive">
    <table class="table table-sm align-middle bg-white shadow-sm">
        <thead><tr><th>Título</th><th>Ámbito</th><th>Categoría</th><th>Fecha</th><th>Estado</th><th style="min-width:180px">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($bulletins as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['title']) ?><?php if (!empty($b['pdf_url'])): ?> <a href="<?= htmlspecialchars(app_url('website/' . cms_bulletin_href($b['pdf_url']))) ?>" target="_blank" class="small">(PDF)</a><?php endif; ?></td>
                <td><span class="badge text-bg-<?= $b['observatory_id'] === null ? 'secondary' : 'info' ?>"><?= htmlspecialchars($obsName[(string) ($b['observatory_id'] ?? '')] ?? 'Observatorio') ?></span></td>
                <td class="small"><?= htmlspecialchars((string) ($b['category'] ?? '')) ?></td>
                <td class="small text-muted"><?= htmlspecialchars((string) ($b['published_at'] ?? '')) ?></td>
                <td><span class="badge bg-<?= $b['is_active'] ? 'success' : 'secondary' ?>"><?= $b['is_active'] ? 'Activo' : 'Inactivo' ?></span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a class="btn btn-sm btn-outline-primary" href="boletines.php?edit=<?= (int) $b['id'] ?>"><i class="fa-solid fa-pen"></i></a>
                        <form method="post" class="d-inline"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $b['id'] ?>"><button class="btn btn-sm btn-outline-warning" title="Activar/Desactivar"><i class="fa-solid fa-power-off"></i></button></form>
                        <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este boletín?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $b['id'] ?>"><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button></form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($bulletins)): ?><tr><td colspan="6" class="text-center text-muted py-3">No hay boletines.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
