<?php
/**
 * Endpoint de subida de imágenes para TinyMCE (cms/tabs.php).
 *
 * TinyMCE espera un POST multipart con el archivo en el campo "file" y debe
 * responder JSON con la forma { "location": "URL pública relativa" } cuando
 * todo salió bien o un código HTTP de error.
 *
 * Las imágenes se guardan bajo website/uploads/cms/YYYY/MM/ con nombre
 * normalizado (slug + timestamp) para evitar colisiones, y se devuelven con
 * ruta relativa al directorio website/ (la usaremos como src="uploads/...").
 */

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_permission('tabs', true);

function ms_upload_error(int $code, string $message): void
{
    http_response_code($code);
    echo json_encode(['error' => ['message' => $message]], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ms_upload_error(405, 'Método no permitido.');
}

if (empty($_FILES['file']) || !is_array($_FILES['file'])) {
    ms_upload_error(400, 'No se recibió el archivo.');
}

$file = $_FILES['file'];
if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    ms_upload_error(400, 'Error al subir (código ' . (int) $file['error'] . ').');
}

$maxSize = 5 * 1024 * 1024; // 5 MB
if (($file['size'] ?? 0) > $maxSize) {
    ms_upload_error(413, 'Archivo muy grande. Máximo 5 MB.');
}

$allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
$allowedMime = [
    'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml',
];

$ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
    ms_upload_error(415, 'Formato no permitido. Use JPG, PNG, WebP, GIF o SVG.');
}

$finfo = @finfo_open(FILEINFO_MIME_TYPE);
$mime = $finfo ? @finfo_file($finfo, $file['tmp_name']) : ($file['type'] ?? '');
if ($finfo) @finfo_close($finfo);
if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
    ms_upload_error(415, 'Tipo MIME no permitido (' . htmlspecialchars((string) $mime) . ').');
}

$websiteRoot = dirname(__DIR__);
$year  = date('Y');
$month = date('m');
$relativeDir = 'uploads/cms/' . $year . '/' . $month;
$targetDir = $websiteRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeDir);
if (!is_dir($targetDir)) {
    if (!@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
        ms_upload_error(500, 'No se pudo crear el directorio de uploads.');
    }
}

$base = pathinfo((string) $file['name'], PATHINFO_FILENAME);
$slug = preg_replace('/[^a-z0-9_\-]/i', '-', $base);
$slug = trim((string) preg_replace('/-+/', '-', (string) $slug), '-');
if ($slug === '') {
    $slug = 'img';
}
$slug = substr($slug, 0, 60);

$timestamp = date('Ymd-His');
$rand = substr(bin2hex(random_bytes(3)), 0, 6);
$finalName = $slug . '-' . $timestamp . '-' . $rand . '.' . $ext;
$destFull = $targetDir . DIRECTORY_SEPARATOR . $finalName;

if (!@move_uploaded_file($file['tmp_name'], $destFull)) {
    ms_upload_error(500, 'No se pudo guardar el archivo.');
}

// SVG: sanitización mínima — bloquear <script>, on*= handlers.
if ($ext === 'svg') {
    $content = (string) @file_get_contents($destFull);
    if ($content !== '') {
        $clean = preg_replace('#<script[^>]*>.*?</script>#is', '', $content);
        $clean = preg_replace('/\son[a-z]+\s*=\s*"[^"]*"/i', '', $clean ?? '');
        $clean = preg_replace("/\son[a-z]+\s*=\s*'[^']*'/i", '', $clean ?? '');
        if ($clean !== null && $clean !== $content) {
            @file_put_contents($destFull, $clean);
        }
    }
}

// Permisos legibles por el servidor web (no fallar si falla chmod).
@chmod($destFull, 0644);

$publicUrl = $relativeDir . '/' . $finalName; // relativa al directorio website/
echo json_encode([
    'location' => $publicUrl,
    'filename' => $finalName,
], JSON_UNESCAPED_UNICODE);
