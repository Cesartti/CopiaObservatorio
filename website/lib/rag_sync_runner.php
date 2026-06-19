<?php

/**
 * Ejecuta sync_cms_chunks_to_pg.py (misma carpeta que .env del asistente).
 */

function cms_rag_mysql_env_from_cms(): array
{
    $host = getenv('OBS_DB_HOST') ?: '127.0.0.1';
    $name = getenv('OBS_DB_NAME') ?: 'observatorio_boyaca';
    $user = getenv('OBS_DB_USER') ?: 'observa_user';
    $pass = getenv('OBS_DB_PASS') ?: '';
    $port = getenv('OBS_DB_PORT') ?: '3306';

    $localFile = __DIR__ . '/../config/database.local.php';
    if (is_readable($localFile)) {
        $local = require $localFile;
        if (is_array($local)) {
            $host = $local['host'] ?? $host;
            $name = $local['name'] ?? $name;
            $user = $local['user'] ?? $user;
            $pass = $local['pass'] ?? $pass;
            $port = isset($local['port']) ? (string) $local['port'] : $port;
        }
    }

    return [
        'MYSQL_HOST' => $host,
        'MYSQL_PORT' => $port,
        'MYSQL_DATABASE' => $name,
        'MYSQL_USER' => $user,
        'MYSQL_PASSWORD' => $pass,
    ];
}

/**
 * @return array{ok:bool,exit_code:int|null,stdout:string,stderr:string,message:string}
 */
function cms_rag_sync_execute(): array
{
    if (!function_exists('proc_open')) {
        return [
            'ok' => false,
            'exit_code' => null,
            'stdout' => '',
            'stderr' => '',
            'message' => 'proc_open no está disponible en este PHP (revisar disable_functions en php.ini).',
        ];
    }

    /** @var array $cfg */
    $cfg = require __DIR__ . '/../config/rag_sync.php';
    $dir = $cfg['app_asistente_dir'];
    $script = $dir . DIRECTORY_SEPARATOR . 'sync_cms_chunks_to_pg.py';

    if (!is_file($script) || !is_readable($script)) {
        return [
            'ok' => false,
            'exit_code' => null,
            'stdout' => '',
            'stderr' => '',
            'message' => 'No se encuentra app_asistente/sync_cms_chunks_to_pg.py',
        ];
    }

    $limit = (int) ($cfg['php_time_limit'] ?? 600);
    if ($limit > 0) {
        @set_time_limit($limit);
    }

    $usePy = !empty($cfg['windows_py_launcher']);
    if ($usePy) {
        $cmd = ['py', '-3', $script];
    } else {
        $bin = (string) ($cfg['python_binary'] ?? 'python');
        $cmd = [$bin, $script];
    }

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $env = null;
    if (!empty($cfg['inject_mysql_from_cms_config'])) {
        $env = [];
        foreach ($_SERVER as $k => $v) {
            if (is_string($v)) {
                $env[$k] = $v;
            }
        }
        foreach (['PATH', 'Path', 'PATHEXT', 'SYSTEMROOT', 'SystemRoot', 'TEMP', 'TMP'] as $ek) {
            $v = getenv($ek);
            if ($v !== false && $v !== '') {
                $env[$ek] = $v;
            }
        }
        foreach (cms_rag_mysql_env_from_cms() as $k => $v) {
            $env[$k] = $v;
        }
        $env['PYTHONUTF8'] = '1';
        $env['PYTHONIOENCODING'] = 'utf-8';
    }

    $proc = @proc_open($cmd, $descriptors, $pipes, $dir, $env);
    if (!is_resource($proc)) {
        return [
            'ok' => false,
            'exit_code' => null,
            'stdout' => '',
            'stderr' => '',
            'message' => 'No se pudo iniciar Python. Compruebe PATH o configure rag_sync.local.php (python_binary).',
        ];
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($proc);

    $stdout = is_string($stdout) ? $stdout : '';
    $stderr = is_string($stderr) ? $stderr : '';

    $ok = $exitCode === 0;
    $msg = $ok
        ? 'Sincronización finalizada.'
        : 'El proceso terminó con error (código ' . (string) $exitCode . ').';

    return [
        'ok' => $ok,
        'exit_code' => $exitCode,
        'stdout' => $stdout,
        'stderr' => $stderr,
        'message' => $msg,
    ];
}
