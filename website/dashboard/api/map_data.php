<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';

if (!$pdo) {
    echo json_encode([
        'paises' => [],
        'ciudades' => [],
        'heat' => [],
        'warning' => $db_error ?? 'Sin conexión a base de datos'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$paises = $pdo->query("SELECT pais, COUNT(*) as total FROM accesos_observatorio WHERE pais <> '' GROUP BY pais")->fetchAll(PDO::FETCH_ASSOC);
$ciudades = $pdo->query("SELECT ciudad, latitud, longitud, COUNT(*) as total FROM accesos_observatorio WHERE ciudad <> '' GROUP BY ciudad, latitud, longitud")->fetchAll(PDO::FETCH_ASSOC);
$heat = $pdo->query("SELECT latitud, longitud, 1 as intensity FROM accesos_observatorio WHERE latitud <> '' AND longitud <> ''")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['paises'=>$paises, 'ciudades'=>$ciudades, 'heat'=>$heat], JSON_UNESCAPED_UNICODE);
