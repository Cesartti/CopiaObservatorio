<?php
header("Content-Type: application/json");

$pdo = new PDO("mysql:host=127.0.0.1;dbname=observatorio_boyaca;charset=utf8mb4",
               "observa_user", "Observa2025*",
               [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// 1. agrupar por país
$paises = $pdo->query("SELECT pais, COUNT(*) as total 
                       FROM accesos_observatorio 
                       WHERE pais <> '' 
                       GROUP BY pais")->fetchAll(PDO::FETCH_ASSOC);

// 2. agrupar por ciudad
$ciudades = $pdo->query("SELECT ciudad, latitud, longitud, COUNT(*) as total
                         FROM accesos_observatorio
                         WHERE ciudad <> '' 
                         GROUP BY ciudad")->fetchAll(PDO::FETCH_ASSOC);

// 3. heatmap
$heat = $pdo->query("SELECT latitud, longitud, 1 as intensity
                     FROM accesos_observatorio
                     WHERE latitud <> '' AND longitud <> ''")->fetchAll(PDO::FETCH_ASSOC);

// salida
echo json_encode([
    "paises"   => $paises,
    "ciudades" => $ciudades,
    "heat"     => $heat
]);

