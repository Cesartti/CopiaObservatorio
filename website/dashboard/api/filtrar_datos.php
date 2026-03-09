<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';

if (!$pdo) {
    echo json_encode([
        'resumen' => [
            'total_visitas' => 0,
            'dias_registrados' => 0,
            'total_paises' => 0,
            'total_paginas' => 0,
            'visitas_hoy' => 0,
        ],
        'paises' => [],
        'paginas' => [],
        'dias' => [],
        'dispositivos' => [],
        'navegadores' => [],
        'geo' => [],
        'warning' => $db_error ?? 'Sin conexión a base de datos'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$pagina      = $_GET['pagina']      ?? '';
$fechaInicio = $_GET['fechaInicio'] ?? '';
$fechaFin    = $_GET['fechaFin']    ?? '';

$condiciones = [];
$params = [];

if ($pagina !== '') {
    $condiciones[] = 'pagina = :pagina';
    $params[':pagina'] = $pagina;
}
if ($fechaInicio !== '') {
    $condiciones[] = 'DATE(fecha) >= :fini';
    $params[':fini'] = $fechaInicio;
}
if ($fechaFin !== '') {
    $condiciones[] = 'DATE(fecha) <= :ffin';
    $params[':ffin'] = $fechaFin;
}

$where = $condiciones ? ('WHERE ' . implode(' AND ', $condiciones)) : '';
$whereNoEmptyPais = $where . ($where ? ' AND ' : ' WHERE ') . "pais <> ''";
$whereNoEmptyGeo = $where . ($where ? ' AND ' : ' WHERE ') . "latitud <> '' AND longitud <> ''";

function runQuery($pdo, $sql, $params) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sqlResumen = "SELECT COUNT(*) AS total_visitas, COUNT(DISTINCT DATE(fecha)) AS dias_registrados, COUNT(DISTINCT pais) AS total_paises, COUNT(DISTINCT pagina) AS total_paginas FROM accesos_observatorio $where";
$resumenRow = runQuery($pdo, $sqlResumen, $params);
$resumen = $resumenRow[0] ?? ['total_visitas'=>0,'dias_registrados'=>0,'total_paises'=>0,'total_paginas'=>0];

$condicionesHoy = $condiciones;
$paramsHoy = $params;
$condicionesHoy[] = 'DATE(fecha) = CURDATE()';
$whereHoy = 'WHERE ' . implode(' AND ', $condicionesHoy);
$hoyRow = runQuery($pdo, "SELECT COUNT(*) AS visitas_hoy FROM accesos_observatorio $whereHoy", $paramsHoy);
$resumen['visitas_hoy'] = $hoyRow[0]['visitas_hoy'] ?? 0;

$paises = runQuery($pdo, "SELECT pais, COUNT(*) AS total FROM accesos_observatorio $whereNoEmptyPais GROUP BY pais ORDER BY total DESC", $params);
$paginas = runQuery($pdo, "SELECT pagina, COUNT(*) AS total FROM accesos_observatorio $where GROUP BY pagina ORDER BY total DESC", $params);
$dias = runQuery($pdo, "SELECT DATE(fecha) AS dia, COUNT(*) AS total FROM accesos_observatorio $where GROUP BY DATE(fecha) ORDER BY dia ASC", $params);
$dispositivos = runQuery($pdo, "SELECT dispositivo, COUNT(*) AS total FROM accesos_observatorio $where GROUP BY dispositivo", $params);
$navegadores = runQuery($pdo, "SELECT navegador, COUNT(*) AS total FROM accesos_observatorio $where GROUP BY navegador", $params);
$geo = runQuery($pdo, "SELECT ciudad, pais, latitud, longitud, COUNT(*) AS total FROM accesos_observatorio $whereNoEmptyGeo GROUP BY ciudad, pais, latitud, longitud", $params);

echo json_encode([
    'resumen'      => $resumen,
    'paises'       => $paises,
    'paginas'      => $paginas,
    'dias'         => $dias,
    'dispositivos' => $dispositivos,
    'navegadores'  => $navegadores,
    'geo'          => $geo,
], JSON_UNESCAPED_UNICODE);
