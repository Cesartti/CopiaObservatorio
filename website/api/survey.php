<?php

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/visit_tracking.php';
require_once __DIR__ . '/../lib/geo_lookup.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'JSON inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$age = isset($data['age_range']) ? (string) $data['age_range'] : '';
$gender = isset($data['gender']) ? (string) $data['gender'] : '';
$sector = isset($data['sector']) ? (string) $data['sector'] : '';
$freq = isset($data['visit_frequency']) ? (string) $data['visit_frequency'] : '';
$ctx = isset($data['page_context']) ? (string) $data['page_context'] : 'portal';
if (strlen($ctx) > 64) {
    $ctx = substr($ctx, 0, 64);
}

$ages = cms_survey_age_ranges();
$genders = cms_survey_genders();
$sectors = cms_survey_sectors();
$freqs = cms_survey_visit_frequency();

if (!isset($ages[$age], $genders[$gender], $sectors[$sector], $freqs[$freq])) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Respuestas incompletas o no válidas'], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo = cms_pdo();
if (!$pdo) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Servicio no disponible'], JSON_UNESCAPED_UNICODE);
    exit;
}

$geo = cms_geo_lookup($pdo, cms_client_ip()) ?: [];
$visitorId = cms_ensure_unique_visitor_id();

try {
    $stmt = $pdo->prepare(
        'INSERT INTO cms_visitor_surveys (page_context, visitor_id, age_range, gender, sector, visit_frequency, country, region, city, lat, lng)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $ctx, $visitorId, $age, $gender, $sector, $freq,
        $geo['country'] ?? null,
        $geo['region'] ?? null,
        $geo['city'] ?? null,
        $geo['lat'] ?? null,
        $geo['lng'] ?? null,
    ]);
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la encuesta'], JSON_UNESCAPED_UNICODE);
}
