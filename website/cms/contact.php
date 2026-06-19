<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('contact', true);
require_once __DIR__ . '/../config/database.php';

$cmsTitle = 'Contacto';
$cmsNav = 'contact';
$pdo = cms_pdo();
$message = '';
$error = '';

$row = [
    'institution' => '', 'address' => '', 'phone' => '', 'email' => '', 'hours' => '',
    'url_facebook' => '', 'url_instagram' => '', 'url_x' => '', 'url_youtube' => '',
];

if ($pdo) {
    try {
        $st = $pdo->query('SELECT * FROM cms_contact WHERE id = 1');
        $db = $st->fetch(PDO::FETCH_ASSOC);
        if ($db) {
            $row = array_merge($row, $db);
        }
    } catch (Throwable $e) {
        $error = 'Tabla cms_contact no disponible. Ejecute migración 002.';
    }
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = 'INSERT INTO cms_contact (id, institution, address, phone, email, hours, url_facebook, url_instagram, url_x, url_youtube) VALUES (1,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE institution=VALUES(institution), address=VALUES(address), phone=VALUES(phone), email=VALUES(email), hours=VALUES(hours),
                url_facebook=VALUES(url_facebook), url_instagram=VALUES(url_instagram), url_x=VALUES(url_x), url_youtube=VALUES(url_youtube)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            trim($_POST['institution'] ?? ''),
            trim($_POST['address'] ?? ''),
            trim($_POST['phone'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['hours'] ?? ''),
            trim($_POST['url_facebook'] ?? ''),
            trim($_POST['url_instagram'] ?? ''),
            trim($_POST['url_x'] ?? ''),
            trim($_POST['url_youtube'] ?? ''),
        ]);
        $message = 'Contacto guardado.';
        $st = $pdo->query('SELECT * FROM cms_contact WHERE id = 1');
        $row = array_merge($row, $st->fetch(PDO::FETCH_ASSOC) ?: []);
    } catch (Throwable $e) {
        $error = 'No se pudo guardar.';
    }
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h4 mb-3">Datos de contacto y redes</h1>
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <form method="post" class="card shadow-sm">
                <div class="card-body row g-3">
                    <div class="col-12"><label class="form-label">Institución</label><input type="text" name="institution" class="form-control" value="<?= htmlspecialchars($row['institution'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Dirección</label><input type="text" name="address" class="form-control" value="<?= htmlspecialchars($row['address'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Teléfono</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($row['phone'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Horario</label><input type="text" name="hours" class="form-control" value="<?= htmlspecialchars($row['hours'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Facebook</label><input type="url" name="url_facebook" class="form-control" value="<?= htmlspecialchars($row['url_facebook'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Instagram</label><input type="url" name="url_instagram" class="form-control" value="<?= htmlspecialchars($row['url_instagram'] ?? '') ?>" placeholder="https://www.instagram.com/secplaneacionboyaca/"></div>
                    <div class="col-md-6"><label class="form-label">X</label><input type="url" name="url_x" class="form-control" value="<?= htmlspecialchars($row['url_x'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">YouTube</label><input type="url" name="url_youtube" class="form-control" value="<?= htmlspecialchars($row['url_youtube'] ?? '') ?>"></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit">Guardar</button></div>
                </div>
            </form>
<?php require __DIR__ . '/includes/footer.php'; ?>
