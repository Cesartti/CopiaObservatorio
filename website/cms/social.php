<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('social', true);
require_once __DIR__ . '/../config/database.php';

$cmsTitle = 'Redes sociales';
$cmsNav = 'social';
$pdo = cms_pdo();
$message = '';
$error = '';

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare('INSERT INTO cms_social_posts (sort_order, network, category, title, byline, excerpt, url, post_date, image, media_tag, media_caption, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,1)');
            $d = trim($_POST['post_date'] ?? '');
            $stmt->execute([
                (int) ($_POST['sort_order'] ?? 0),
                trim($_POST['network'] ?? 'instagram'),
                trim($_POST['category'] ?? ''),
                trim($_POST['title'] ?? ''),
                trim($_POST['byline'] ?? ''),
                trim($_POST['excerpt'] ?? ''),
                trim($_POST['url'] ?? ''),
                $d !== '' ? $d : null,
                trim($_POST['image'] ?? ''),
                trim($_POST['media_tag'] ?? ''),
                trim($_POST['media_caption'] ?? ''),
            ]);
            $message = 'Publicación agregada.';
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            $pdo->prepare('DELETE FROM cms_social_posts WHERE id = ?')->execute([(int) $_POST['id']]);
            $message = 'Eliminado.';
        }
    } catch (Throwable $e) {
        $error = 'Error al guardar.';
    }
}

$rows = [];
if ($pdo) {
    try {
        $rows = $pdo->query('SELECT *, DATE_FORMAT(post_date, "%Y-%m-%d") AS d FROM cms_social_posts ORDER BY sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $error = 'Ejecute la migración 002_cms_core.sql.';
    }
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-3">Publicaciones (Instagram / Facebook)</h1>
            <div class="alert alert-info small mb-3">
                <strong><i class="fa-solid fa-circle-info me-1"></i> Cómo agregar un post de Instagram:</strong>
                <ol class="mb-1 mt-2">
                    <li>Copia la URL del post desde Instagram (ej. <code>https://www.instagram.com/secplaneacionboyaca/p/<strong>DYAPxmCEfCV</strong>/</code>)</li>
                    <li>Pégala en el campo <em>Enlace</em> y completa al menos el <em>Título</em></li>
                    <li>El sitio detectará automáticamente el <em>shortcode</em> (<code>DYAPxmCEfCV</code>) y mostrará el embed real al hacer clic en la tarjeta</li>
                </ol>
                <span class="text-muted">Funciona con posts (<code>/p/</code>), reels (<code>/reel/</code>) y videos IGTV (<code>/tv/</code>). Los enlaces de perfil (sin shortcode) abren la página externa.</span>
            </div>
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6">Nueva entrada</h2>
                    <form method="post">
                        <input type="hidden" name="action" value="add">
                        <div class="row g-2">
                            <div class="col-md-1"><label class="form-label">Orden</label><input type="number" name="sort_order" class="form-control" value="0"></div>
                            <div class="col-md-2"><label class="form-label">Red</label>
                                <select name="network" class="form-select">
                                    <option value="instagram">instagram</option>
                                    <option value="facebook">facebook</option>
                                </select>
                            </div>
                            <div class="col-md-2"><label class="form-label">Categoría</label><input type="text" name="category" class="form-control"></div>
                            <div class="col-md-3"><label class="form-label">Título</label><input type="text" name="title" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Enlace</label><input type="url" name="url" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Fecha</label><input type="date" name="post_date" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label">Subtítulo / byline</label><input type="text" name="byline" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label">Texto</label><input type="text" name="excerpt" class="form-control"></div>
                            <div class="col-md-2"><label class="form-label">Tag imagen</label><input type="text" name="media_tag" class="form-control"></div>
                            <div class="col-md-2"><label class="form-label">Pie imagen</label><input type="text" name="media_caption" class="form-control"></div>
                            <div class="col-md-12"><label class="form-label">URL imagen (opcional)</label><input type="url" name="image" class="form-control" placeholder="https://..."></div>
                            <div class="col-12"><button class="btn btn-primary" type="submit">Agregar</button></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle bg-white shadow-sm">
                    <thead><tr><th>Red</th><th>Título</th><th>URL</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['network']) ?></td>
                            <td><?= htmlspecialchars($r['title']) ?></td>
                            <td><a href="<?= htmlspecialchars($r['url']) ?>" target="_blank" rel="noopener">abrir</a></td>
                            <td class="text-end">
                                <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
