<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../functions.php';

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$chart = isset($_GET['chart']) ? trim($_GET['chart']) : '1';

if (!ctype_digit($id) || !ctype_digit($chart)) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros inválidos.']);
    exit;
}

$basePath = realpath(__DIR__ . '/../indicador');
if ($basePath === false) {
    http_response_code(500);
    echo json_encode(['error' => 'No se encontró la carpeta de indicadores.']);
    exit;
}

$indicatorPath = $basePath . DIRECTORY_SEPARATOR . $id;
$infoPath = $indicatorPath . DIRECTORY_SEPARATOR . $chart . '.info';
$csvPath = $indicatorPath . DIRECTORY_SEPARATOR . $chart . '.csv';

if (!file_exists($infoPath) || !file_exists($csvPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró información para el indicador solicitado.']);
    exit;
}

$chartInfo = getInfo($infoPath);
$rows = array_map('str_getcsv', file($csvPath));

if (empty($rows)) {
    http_response_code(404);
    echo json_encode(['error' => 'El CSV está vacío.']);
    exit;
}

$headers = array_shift($rows);
$data = [];

foreach ($rows as $row) {
    if (count($row) === 0 || (count($row) === 1 && trim($row[0]) === '')) {
        continue;
    }

    $item = [];
    foreach ($headers as $index => $header) {
        $value = $row[$index] ?? null;

        if (is_string($value) && is_numeric(str_replace(',', '.', $value))) {
            $normalized = str_replace(',', '.', $value);
            $value = (float) $normalized;
        }

        $item[$header] = $value;
    }

    $data[] = $item;
}

echo json_encode([
    'id' => $id,
    'chart' => (int) $chart,
    'meta' => $chartInfo,
    'rows' => $data,
    'total_rows' => count($data),
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
