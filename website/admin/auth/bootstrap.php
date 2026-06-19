<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function app_base_url()
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $pos = strpos($script, '/website/');
    if ($pos !== false) {
        return substr($script, 0, $pos);
    }

    return '';
}

function app_url($path)
{
    $base = rtrim(app_base_url(), '/');
    $clean = ltrim($path, '/');

    if ($base !== '') {
        return $base . '/' . $clean;
    }

    if (str_starts_with($clean, 'website/')) {
        return '/' . substr($clean, strlen('website/'));
    }

    return '/' . $clean;
}

function auth_user()
{
    return $_SESSION['auth_user'] ?? null;
}

function auth_login($email, $role, array $extra = [])
{
    $_SESSION['auth_user'] = array_merge([
        'email' => $email,
        'role' => $role,
        'login_at' => date(DATE_ATOM),
    ], $extra);
}

/**
 * @param array<string, array{read:bool,write:bool}> $permissions
 */
function auth_login_with_permissions($email, $role, ?int $userId, ?int $roleId, array $permissions)
{
    auth_login($email, $role, [
        'id' => $userId,
        'role_id' => $roleId,
        'permissions' => $permissions,
    ]);
}

function auth_can(string $module, bool $needWrite = false): bool
{
    $u = auth_user();
    if (!$u) {
        return false;
    }
    if (($u['role'] ?? '') === 'admin_general' && !isset($u['permissions'])) {
        return true;
    }
    $p = $u['permissions'][$module] ?? null;
    if (!$p) {
        return false;
    }
    if ($needWrite) {
        return !empty($p['write']);
    }

    return !empty($p['read']) || !empty($p['write']);
}

function auth_require_permission(string $module, bool $needWrite = false): void
{
    auth_require_login();
    if (!auth_can($module, $needWrite)) {
        http_response_code(403);
        echo 'No tiene permisos para esta sección.';
        exit;
    }
}

function auth_logout()
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function auth_require_login()
{
    if (!auth_user()) {
        header('Location: ' . app_url('website/admin/auth/login.php'));
        exit;
    }
}
