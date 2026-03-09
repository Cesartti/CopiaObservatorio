<?php
session_start();

function auth_user()
{
    return $_SESSION['auth_user'] ?? null;
}

function auth_login($email, $role)
{
    $_SESSION['auth_user'] = [
        'email' => $email,
        'role' => $role,
        'login_at' => date(DATE_ATOM),
    ];
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
        header('Location: /admin/auth/login.php');
        exit;
    }
}
