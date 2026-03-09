<?php
require_once __DIR__ . '/bootstrap.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $demoUsers = [
        'admin@observatorio.gov.co' => ['password' => 'Admin123*', 'role' => 'admin_general'],
        'editor.social@observatorio.gov.co' => ['password' => 'Editor123*', 'role' => 'editor_observatorio'],
    ];

    if (array_key_exists($email, $demoUsers) && $demoUsers[$email]['password'] === $password) {
        auth_login($email, $demoUsers[$email]['role']);
        header('Location: ' . app_url('website/dashboard/index.php'));
        exit;
    }

    $error = 'Credenciales inválidas.';
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
            <h1 class="h4 mb-3">Ingreso al panel administrativo</h1>
            <p class="text-muted">Autenticación base para migración a RBAC completo.</p>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="form-label" for="email">Correo</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Iniciar sesión</button>
            </form>
            <small class="d-block mt-3 text-muted">Demo: admin@observatorio.gov.co / Admin123*</small>
        </div>
    </div>
</div>
</body>
</html>
