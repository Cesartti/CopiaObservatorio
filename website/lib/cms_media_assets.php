<?php

/**
 * Listado seguro de archivos gráficos bajo website/assets (p. ej. svg) para el CMS.
 */

function cms_website_root(): string
{
    return dirname(__DIR__);
}

/**
 * @return list<string> Rutas relativas al directorio website/, con barras /
 */
function cms_list_relative_assets(string $sub = 'assets/svg'): array
{
    $root = cms_website_root();
    $dir = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sub);
    $base = realpath($root);
    if ($base === false || !is_dir($dir)) {
        return [];
    }
    $out = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        if (!in_array($ext, ['svg', 'png', 'jpg', 'jpeg', 'webp', 'gif'], true)) {
            continue;
        }
        $full = $file->getRealPath();
        if ($full === false || strpos($full, $base) !== 0) {
            continue;
        }
        $rel = str_replace('\\', '/', substr($full, strlen($base) + 1));
        $out[] = $rel;
    }
    sort($out);

    return $out;
}

function cms_normalize_media_url(string $value): string
{
    $value = trim($value);
    $value = str_replace('\\', '/', $value);

    return preg_replace('#^(\./)+#', '', $value);
}

function cms_is_safe_media_path(string $relative): bool
{
    $relative = cms_normalize_media_url($relative);
    if ($relative === '' || strpos($relative, '..') !== false) {
        return false;
    }
    $root = realpath(cms_website_root());
    if ($root === false) {
        return false;
    }
    $full = realpath($root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative));

    return $full !== false && is_file($full) && strpos($full, $root) === 0;
}
