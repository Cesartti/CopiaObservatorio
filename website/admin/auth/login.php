<?php
require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

function cms_load_permissions_from_db(PDO $pdo, int $roleId): array
{
    $stmt = $pdo->prepare('SELECT module, can_read, can_write FROM role_permissions WHERE role_id = ?');
    $stmt->execute([$roleId]);
    $out = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $out[$r['module']] = [
            'read' => (bool) $r['can_read'],
            'write' => (bool) $r['can_write'],
        ];
    }

    return $out;
}

function cms_demo_permissions(string $role): array
{
    $modules = ['banners', 'social', 'contact', 'news', 'charts', 'tabs', 'rag', 'users'];
    $out = [];
    foreach ($modules as $m) {
        $out[$m] = ['read' => true, 'write' => $role === 'admin_general' || $m !== 'users'];
    }
    if ($role !== 'admin_general') {
        $out['users'] = ['read' => false, 'write' => false];
    }

    return $out;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = cms_pdo();
    $logged = false;

    if ($pdo && $email !== '' && $password !== '') {
        $stmt = $pdo->prepare('SELECT u.id, u.email, u.password_hash, u.role_id, r.code AS role_code FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE u.email = ? AND u.is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($password, $row['password_hash'])) {
            $perms = cms_load_permissions_from_db($pdo, (int) $row['role_id']);
            if ($perms === []) {
                $perms = cms_demo_permissions($row['role_code']);
            }
            auth_login_with_permissions(
                $row['email'],
                $row['role_code'],
                (int) $row['id'],
                (int) $row['role_id'],
                $perms
            );
            $upd = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?');
            $upd->execute([(int) $row['id']]);
            $logged = true;
        }
    }

    if ($logged) {
        header('Location: ' . app_url('website/cms/index.php'));
        exit;
    }

    $error = $pdo ? 'Credenciales inválidas.' : 'No fue posible conectar con la base de datos. Intente más tarde.';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 480px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Ingreso al CMS · Red de Observatorios</h1>
            <p class="text-muted small">Panel para gestionar banners, redes, noticias, tableros y gráficos sin editar código.</p>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="form-label" for="email">Correo</label>
                    <input type="email" class="form-control" id="email" name="email" required autocomplete="username">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                </div>
                <button class="btn btn-primary w-100" type="submit">Iniciar sesión</button>
            </form>
            <small class="d-block mt-3 text-muted">Acceso solo para usuarios registrados. Contacte al administrador del sistema.</small>
        </div>
    </div>
</div>
</body>
</html>
