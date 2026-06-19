<?php

/**
 * Conexión PDO compartida (CMS, dashboard, APIs).
 *
 * Opciones:
 * - Variables de entorno: OBS_DB_HOST, OBS_DB_NAME, OBS_DB_USER, OBS_DB_PASS
 * - Archivo opcional `database.local.php` (no versionar) con: host, name, user, pass
 * - En localhost, si falla el usuario configurado, se intenta `root` sin contraseña (XAMPP por defecto).
 */

/** @var string|null */
$GLOBALS['cms_last_db_error'] = null;

function cms_set_last_db_error(?string $message): void
{
    $GLOBALS['cms_last_db_error'] = $message;
}

function cms_last_db_error(): ?string
{
    return $GLOBALS['cms_last_db_error'] ?? null;
}

function cms_pdo(): ?PDO
{
    static $pdo = null;
    static $failed = false;

    if ($failed) {
        return null;
    }
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('OBS_DB_HOST') ?: '127.0.0.1';
    $name = getenv('OBS_DB_NAME') ?: 'observatorio_boyaca';
    $user = getenv('OBS_DB_USER') ?: 'observa_user';
    $pass = getenv('OBS_DB_PASS') ?: '';

    $localFile = __DIR__ . '/database.local.php';
    if (is_readable($localFile)) {
        $local = require $localFile;
        if (is_array($local)) {
            $host = $local['host'] ?? $host;
            $name = $local['name'] ?? $name;
            $user = $local['user'] ?? $user;
            $pass = $local['pass'] ?? $pass;
        }
    }

    $attempts = [];
    $attempts[] = [$user, $pass, 'configuración principal'];
    if ($host === '127.0.0.1' || $host === 'localhost') {
        $tryRoot = getenv('OBS_DB_TRY_ROOT');
        if ($tryRoot !== '0' && $user !== 'root') {
            $attempts[] = ['root', '', 'XAMPP (root sin contraseña)'];
        }
    }

    $seen = [];
    $lastMessage = null;
    foreach ($attempts as $item) {
        $u = $item[0];
        $p = $item[1];
        $key = $u . "\0" . $p;
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        try {
            $pdo = new PDO(
                "mysql:host={$host};dbname={$name};charset=utf8mb4",
                $u,
                $p,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            cms_set_last_db_error(null);
            return $pdo;
        } catch (PDOException $e) {
            $lastMessage = $e->getMessage();
        }
    }

    cms_set_last_db_error($lastMessage);
    $failed = true;

    return null;
}
