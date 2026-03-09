<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../functions.php';

$basePath = realpath(__DIR__ . '/../indicador');
if ($basePath === false || !is_dir($basePath)) {
    http_response_code(500);
    echo json_encode(['error' => 'No se encontró la carpeta de indicadores.']);
    exit;
}

$dimensionMap = [
    '1' => 'económica',
    '2' => 'social',
    '3' => 'ambiental',
    '4' => 'tecnología',
];

$indicators = [];
$directories = scandir($basePath);

foreach ($directories as $directory) {
    if ($directory === '.' || $directory === '..') {
        continue;
    }

    $fullPath = $basePath . DIRECTORY_SEPARATOR . $directory;
    if (!is_dir($fullPath) || !ctype_digit($directory)) {
        continue;
    }

    $infoPath = $fullPath . DIRECTORY_SEPARATOR . 'indicador.info';
    if (!file_exists($infoPath)) {
        continue;
    }

    $info = getInfo($infoPath);
    if (empty($info)) {
        continue;
    }

    $dimension = $dimensionMap[$directory[0]] ?? 'sin-clasificar';

    $indicators[] = [
        'id' => $directory,
        'titulo' => $info['titulo'] ?? ('Indicador ' . $directory),
        'descripcion' => $info['descripcion'] ?? '',
        'categoria' => $info['categoria'] ?? '',
        'subcategoria' => $info['subcategoria'] ?? '',
        'dimension' => $dimension,
        'url' => 'indicador.php?id=' . $directory,
    ];
}

usort($indicators, function ($a, $b) {
    if ($a['dimension'] === $b['dimension']) {
        return strcmp($a['titulo'], $b['titulo']);
    }

    return strcmp($a['dimension'], $b['dimension']);
});

$response = [
    'generated_at' => date(DATE_ATOM),
    'total' => count($indicators),
    'items' => $indicators,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
