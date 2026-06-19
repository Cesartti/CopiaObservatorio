<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../lib/cms_public_content.php';
require_once __DIR__ . '/../config/database.php';

$data = cms_portal_home_payload(cms_pdo());
if (!is_array($data) || $data === []) {
    $data = cms_load_json_file();
}
if (!is_array($data) || $data === []) {
    echo json_encode(['error' => 'No hay contenido configurado.']);
    exit;
}

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$response = [
    'contact' => $data['contact'] ?? [],
    'home_banners' => $data['home_banners'] ?? [],
    'social_feed' => $data['social_feed'] ?? [],
    'general_news' => $data['general_news'] ?? [],
    'dimension_news' => $slug !== '' ? ($data['dimension_news'][$slug] ?? []) : ($data['dimension_news'] ?? []),
    'featured_indicators' => $slug !== '' ? ($data['featured_indicators'][$slug] ?? []) : ($data['featured_indicators'] ?? []),
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
