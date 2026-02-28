<?php
// dashboard/api/filtrar_datos.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db.php';

$pagina      = $_GET['pagina']      ?? '';
$fechaInicio = $_GET['fechaInicio'] ?? '';
$fechaFin    = $_GET['fechaFin']    ?? '';

$condiciones = [];
$params = [];

// Filtro por página
if ($pagina !== '') {
    $condiciones[] = 'pagina = :pagina';
    $params[':pagina'] = $pagina;
}

// Filtro por rango de fechas
if ($fechaInicio !== '') {
    $condiciones[] = 'DATE(fecha) >= :fini';
    $params[':fini'] = $fechaInicio;
}
if ($fechaFin !== '') {
    $condiciones[] = 'DATE(fecha) <= :ffin';
    $params[':ffin'] = $fechaFin;
}

$where = $condiciones ? ('WHERE ' . implode(' AND ', $condiciones)) : '';

// Helper
function runQuery($pdo, $sql, $params) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 1. Resumen
$sqlResumen = "
    SELECT
        COUNT(*) AS total_visitas,
        COUNT(DISTINCT DATE(fecha)) AS dias_registrados,
        COUNT(DISTINCT pais) AS total_paises,
        COUNT(DISTINCT pagina) AS total_paginas
    FROM accesos_observatorio
    $where
";
$resumenRow = runQuery($pdo, $sqlResumen, $params);
$resumen = $resumenRow[0] ?? [
    'total_visitas'   => 0,
    'dias_registrados'=> 0,
    'total_paises'    => 0,
    'total_paginas'   => 0
];

// Visitas hoy (respecto al filtro + hoy)
$condicionesHoy = $condiciones;
$paramsHoy = $params;
$condicionesHoy[] = 'DATE(fecha) = CURDATE()';
$whereHoy = 'WHERE ' . implode(' AND ', $condicionesHoy);

$sqlHoy = "SELECT COUNT(*) AS visitas_hoy FROM accesos_observatorio $whereHoy";
$hoyRow = runQuery($pdo, $sqlHoy, $paramsHoy);
$resumen['visitas_hoy'] = $hoyRow[0]['visitas_hoy'] ?? 0;

// 2. Países
$sqlPaises = "
    SELECT pais, COUNT(*) AS total
    FROM accesos_observatorio
    $where
    AND pais <> ''
    GROUP BY pais
    ORDER BY total DESC
";
$paises = runQuery($pdo, $sqlPaises, $params);

// 3. Páginas
$sqlPaginas = "
    SELECT pagina, COUNT(*) AS total
    FROM accesos_observatorio
    $where
    GROUP BY pagina
    ORDER BY total DESC
";
$paginas = runQuery($pdo, $sqlPaginas, $params);

// 4. Días
$sqlDias = "
    SELECT DATE(fecha) AS dia, COUNT(*) AS total
    FROM accesos_observatorio
    $where
    GROUP BY DATE(fecha)
    ORDER BY dia ASC
";
$dias = runQuery($pdo, $sqlDias, $params);

// 5. Dispositivos
$sqlDispositivos = "
    SELECT dispositivo, COUNT(*) AS total
    FROM accesos_observatorio
    $where
    GROUP BY dispositivo
";
$dispositivos = runQuery($pdo, $sqlDispositivos, $params);

// 6. Navegadores
$sqlNavegadores = "
    SELECT navegador, COUNT(*) AS total
    FROM accesos_observatorio
    $where
    GROUP BY navegador
";
$navegadores = runQuery($pdo, $sqlNavegadores, $params);

// 7. Datos geográficos para el mapa
$sqlGeo = "
    SELECT ciudad, pais, latitud, longitud, COUNT(*) AS total
    FROM accesos_observatorio
    $where
    AND latitud <> '' AND longitud <> ''
    GROUP BY ciudad, pais, latitud, longitud
";
$geo = runQuery($pdo, $sqlGeo, $params);

echo json_encode([
    'resumen'      => $resumen,
    'paises'       => $paises,
    'paginas'      => $paginas,
    'dias'         => $dias,
    'dispositivos' => $dispositivos,
    'navegadores'  => $navegadores,
    'geo'          => $geo,
], JSON_UNESCAPED_UNICODE);
