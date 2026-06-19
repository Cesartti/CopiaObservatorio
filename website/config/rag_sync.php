<?php

/**
 * Sincronización RAG (CMS MySQL → PostgreSQL) desde el botón del panel.
 *
 * Opcional: copie rag_sync.local.php.example a rag_sync.local.php
 */

$base = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'app_asistente';

$defaults = [
    /** Ejecutable de Python (Windows: python, py, o ruta completa). */
    'python_binary' => getenv('OBS_PYTHON') ?: 'python',
    /**
     * Si true, usa `py -3 script.py` (Windows Store / Python Launcher).
     */
    'windows_py_launcher' => false,
    /** Carpeta que contiene sync_cms_chunks_to_pg.py y .env */
    'app_asistente_dir' => $base,
    /** Segundos máximos esperando al proceso (0 = sin límite extra en PHP). */
    'php_time_limit' => 600,
    /** Inyectar MYSQL_* al proceso desde la misma config que el CMS (además de .env). */
    'inject_mysql_from_cms_config' => true,
];

$local = __DIR__ . '/rag_sync.local.php';
if (is_readable($local)) {
    $x = require $local;
    if (is_array($x)) {
        $defaults = array_replace($defaults, $x);
    }
}

return $defaults;
