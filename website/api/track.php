<?php

/**
 * Registro de eventos de navegación (analítica de comportamiento).
 *
 * Recibe un evento (page_view, indicator_view, news_open, tab_open, powerbi_open,
 * search) por POST JSON o navigator.sendBeacon, lo asocia al visitante anónimo
 * (cookie obs_unique_vid) y a su geolocalización aproximada, y lo guarda en
 * cms_events. Responde 204 sin cuerpo (fire-and-forget).
 *
 * Anónimo: no guarda nombre/correo ni IP en claro (solo país/ciudad aprox.).
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/visit_tracking.php';
require_once __DIR__ . '/../lib/geo_lookup.php';

function track_end(int $code = 204): void
{
    http_response_code($code);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    track_end(405);
}

// Asegurar/leer el visitante ANTES de cualquier salida (setcookie).
$visitorId = cms_ensure_unique_visitor_id();

$raw = file_get_contents('php://input');
$data = json_decode((string) $raw, true);
if (!is_array($data)) {
    track_end(400);
}

$allowedTypes = ['page_view', 'indicator_view', 'news_open', 'tab_open', 'powerbi_open', 'search'];
$type = (string) ($data['type'] ?? '');
if (!in_array($type, $allowedTypes, true)) {
    track_end(422);
}

/** Recorta y limpia un campo de texto. */
function track_clean($v, int $max): ?string
{
    if ($v === null) {
        return null;
    }
    $s = trim((string) $v);
    if ($s === '') {
        return null;
    }
    $s = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s); // sin caracteres de control
    return mb_substr($s, 0, $max);
}

$observatory = track_clean($data['observatory'] ?? null, 40);
$objectType = track_clean($data['object_type'] ?? null, 40);
$objectId = track_clean($data['object_id'] ?? null, 120);
$label = track_clean($data['label'] ?? null, 200);
$path = track_clean($data['path'] ?? null, 255);

$pdo = cms_pdo();
if (!$pdo) {
    track_end(503);
}

$geo = cms_geo_lookup($pdo, cms_client_ip()) ?: [];

try {
    $stmt = $pdo->prepare(
        'INSERT INTO cms_events (visitor_id, event_type, observatory, object_type, object_id, label, path, country, region, city, lat, lng)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $visitorId, $type, $observatory, $objectType, $objectId, $label, $path,
        $geo['country'] ?? null,
        $geo['region'] ?? null,
        $geo['city'] ?? null,
        $geo['lat'] ?? null,
        $geo['lng'] ?? null,
    ]);
} catch (Throwable $e) {
    track_end(503);
}

track_end(204);
