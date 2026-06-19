<?php

/**
 * Sincroniza publicaciones de Instagram con el hashtag al home.
 * Ejecutar por CRON / Programador de tareas:
 *   C:\xampp\php\php.exe C:\xampp\htdocs\Observatorio2026\scripts\sync_instagram.php
 */

require_once __DIR__ . '/../website/config/database.php';
$cfg = require __DIR__ . '/../website/config/instagram.php';
require_once __DIR__ . '/../website/lib/instagram_sync.php';

if (empty($cfg['enabled'])) {
    fwrite(STDERR, "Conector deshabilitado. Active 'enabled' en config/instagram.local.php\n");
    exit(2);
}

$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "Sin conexión a la base de datos.\n");
    exit(1);
}

$r = ig_sync_hashtag_posts($pdo, $cfg);
if (!$r['ok']) {
    fwrite(STDERR, "ERROR: " . ($r['error'] ?? 'desconocido') . "\n");
    exit(1);
}

echo sprintf(
    "Sincronización OK · %d publicaciones con #%s · %d nuevas · %d actualizadas\n",
    $r['encontrados'], $cfg['hashtag'], $r['insertados'], $r['actualizados']
);
