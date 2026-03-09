<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$path = __DIR__ . '/../data/content.json';
if (!file_exists($path)) {
    echo json_encode(['error' => 'No hay contenido configurado.']);
    exit;
}

$data = json_decode(file_get_contents($path), true);
if (!is_array($data)) {
    echo json_encode(['error' => 'Formato inválido de contenido.']);
    exit;
}

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$response = [
    'general_news' => $data['general_news'] ?? [],
    'dimension_news' => $slug !== '' ? ($data['dimension_news'][$slug] ?? []) : ($data['dimension_news'] ?? []),
    'featured_indicators' => $slug !== '' ? ($data['featured_indicators'][$slug] ?? []) : ($data['featured_indicators'] ?? []),
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
