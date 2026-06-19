<?php
require_once __DIR__ . '/config/database.php';

$pdo = cms_pdo();
$id = (int) ($_GET['id'] ?? 0);
if (!$pdo || $id < 1) {
    http_response_code(404);
    echo 'Gráfico no encontrado.';
    exit;
}

$stmt = $pdo->prepare(
    'SELECT c.title, c.chart_type, c.options_json, c.data_json, c.is_active, d.is_active AS dash_active
     FROM cms_charts c
     INNER JOIN cms_dashboards d ON d.id = c.dashboard_id
     WHERE c.id = ?'
);
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row || !$row['is_active'] || !$row['dash_active']) {
    http_response_code(404);
    echo 'No disponible.';
    exit;
}

$data = json_decode($row['data_json'], true);
if (!is_array($data)) {
    http_response_code(500);
    echo 'Datos inválidos.';
    exit;
}
$optsRaw = $row['options_json'] ?? '{}';
if (is_string($optsRaw)) {
    $options = json_decode($optsRaw, true);
} else {
    $options = is_array($optsRaw) ? $optsRaw : [];
}
if (!is_array($options)) {
    $options = [];
}

$title = $row['title'];
$chartType = $row['chart_type'] ?: 'ColumnChart';
$dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);
$optionsJson = json_encode($options, JSON_UNESCAPED_UNICODE);
?>
<!doctype html>
<html lang="es">
<head>
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png">
    <link rel="apple-touch-icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-180x180.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 1rem; background: #f4f7fc; }
        #chart { min-height: 420px; background: #fff; border-radius: 12px; padding: 8px; box-shadow: 0 4px 20px rgba(0,0,0,.06); }
        h1 { font-size: 1.15rem; color: #0f1f32; }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($title) ?></h1>
    <div id="chart"></div>
    <p class="small text-muted">Visualización con Google Charts (<a href="https://developers.google.com/chart/interactive/docs/gallery" target="_blank" rel="noopener">documentación</a>).</p>
    <script>
        const chartType = <?= json_encode($chartType, JSON_UNESCAPED_UNICODE) ?>;
        const dataArr = <?= $dataJson ?>;
        const options = <?= $optionsJson ?> || {};

        google.charts.load('current', { packages: ['corechart', 'geochart', 'table'] });
        google.charts.setOnLoadCallback(function () {
            const data = google.visualization.arrayToDataTable(dataArr);
            const ctor = google.visualization[chartType] || google.visualization.ColumnChart;
            const chart = new ctor(document.getElementById('chart'));
            chart.draw(data, options);
        });
    </script>
</body>
</html>
