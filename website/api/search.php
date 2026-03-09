<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../functions.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    echo json_encode(['items' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$qNormalized = mb_strtolower($q);
$basePath = realpath(__DIR__ . '/../indicador');
$items = [];

if ($basePath && is_dir($basePath)) {
    foreach (scandir($basePath) as $dir) {
        if ($dir === '.' || $dir === '..' || !ctype_digit($dir)) {
            continue;
        }

        $infoPath = $basePath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'indicador.info';
        if (!file_exists($infoPath)) {
            continue;
        }

        $info = getInfo($infoPath);
        $title = $info['titulo'] ?? '';
        $category = $info['categoria'] ?? '';
        $description = $info['descripcion'] ?? '';

        $haystack = mb_strtolower($title . ' ' . $category . ' ' . $description . ' ' . $dir);
        if (mb_strpos($haystack, $qNormalized) === false) {
            continue;
        }

        $items[] = [
            'type' => 'indicador',
            'title' => $title !== '' ? $title : ('Indicador ' . $dir),
            'context' => $category,
            'url' => 'indicador.php?id=' . $dir,
        ];

        if (count($items) >= 20) {
            break;
        }
    }
}

echo json_encode(['query' => $q, 'total' => count($items), 'items' => $items], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
