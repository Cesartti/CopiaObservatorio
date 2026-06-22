<?php

/**
 * Boletines de la Red de Observatorios (general + por observatorio).
 * Tabla cms_bulletins (migración 019). observatory_id NULL = general.
 */

/** Slug simple para nombres de archivo. */
function cms_bulletin_slug(string $s): string
{
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $s = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $s));
    return trim($s, '-') ?: 'boletin';
}

/** Observatorios para el selector del CMS (id, slug, name). */
function cms_bulletins_observatories(PDO $pdo): array
{
    try {
        return $pdo->query('SELECT id, slug, name FROM observatories ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

/**
 * Lista boletines.
 * @param string|int $filter 'all' | 'general' | id de observatorio
 */
function cms_bulletins_fetch(?PDO $pdo, $filter = 'all', bool $onlyActive = true): array
{
    if (!$pdo) {
        return [];
    }
    $where = [];
    $params = [];
    if ($filter === 'general') {
        $where[] = 'observatory_id IS NULL';
    } elseif ($filter !== 'all') {
        $where[] = 'observatory_id = ?';
        $params[] = (int) $filter;
    }
    if ($onlyActive) {
        $where[] = 'is_active = 1';
    }
    $sql = 'SELECT * FROM cms_bulletins';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY (observatory_id IS NULL) DESC, observatory_id ASC, sort_order ASC, published_at DESC, id DESC';
    try {
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

/** Procesa el PDF del formulario (archivo o URL). Devuelve ruta/URL o ''. */
function cms_bulletin_pdf_from_request(string $slugBase): string
{
    $url = trim($_POST['pdf_url'] ?? '');
    if (!empty($_FILES['pdf_file']) && ($_FILES['pdf_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $dir = __DIR__ . '/../assets/uploads/bulletins';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            throw new RuntimeException('El boletín debe ser un archivo PDF.');
        }
        if (($_FILES['pdf_file']['size'] ?? 0) > 25 * 1024 * 1024) {
            throw new RuntimeException('El PDF supera el límite de 25 MB.');
        }
        // Verificar firma real %PDF.
        $fh = @fopen($_FILES['pdf_file']['tmp_name'], 'rb');
        $head = $fh ? fread($fh, 5) : '';
        if ($fh) {
            fclose($fh);
        }
        if (strpos((string) $head, '%PDF') !== 0) {
            throw new RuntimeException('El archivo no es un PDF válido.');
        }
        $fname = $slugBase . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.pdf';
        $dest = $dir . DIRECTORY_SEPARATOR . $fname;
        if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $dest)) {
            throw new RuntimeException('No se pudo guardar el PDF.');
        }
        return 'assets/uploads/bulletins/' . $fname;
    }
    return $url;
}

/** Procesa la portada del formulario (imagen ráster o URL). Devuelve ruta/URL o ''. */
function cms_bulletin_cover_from_request(string $slugBase): string
{
    $url = trim($_POST['cover_url'] ?? '');
    if (!empty($_FILES['cover_file']) && ($_FILES['cover_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $dir = __DIR__ . '/../assets/uploads/bulletins';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $ext = strtolower(pathinfo($_FILES['cover_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            throw new RuntimeException('Portada: use jpg/png/gif/webp.');
        }
        if (($_FILES['cover_file']['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new RuntimeException('La portada supera el límite de 5 MB.');
        }
        $info = @getimagesize($_FILES['cover_file']['tmp_name']);
        $valid = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
        if ($info === false || !in_array($info[2] ?? 0, $valid, true)) {
            throw new RuntimeException('La portada no es una imagen válida.');
        }
        $fname = $slugBase . '-cover-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $dir . DIRECTORY_SEPARATOR . $fname;
        if (!move_uploaded_file($_FILES['cover_file']['tmp_name'], $dest)) {
            throw new RuntimeException('No se pudo guardar la portada.');
        }
        return 'assets/uploads/bulletins/' . $fname;
    }
    return $url;
}

/** href seguro para una ruta con espacios (PDF/portada). */
function cms_bulletin_href(string $path): string
{
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return implode('/', array_map('rawurlencode', explode('/', $path)));
}
