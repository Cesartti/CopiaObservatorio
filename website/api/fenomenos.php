<?php

/**
 * API pública de la pestaña "Fenómenos en Boyacá".
 *
 *   GET  ?recurso=todo|enso|focos|reportes|bomberos|historico
 *   POST (JSON o formulario) → registra un reporte ciudadano.
 *
 * Devuelve siempre JSON. Los errores de las fuentes externas viajan en la
 * clave "avisos" para que el mapa cargue igual con los datos locales.
 */

declare(strict_types=1);

require_once __DIR__ . '/../lib/fenomenos.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$pdo = cms_pdo();

function fen_ip(): string
{
    foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $k) {
        if (!empty($_SERVER[$k])) {
            $v = explode(',', (string) $_SERVER[$k])[0];

            return trim($v);
        }
    }

    return '0.0.0.0';
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $crudo = file_get_contents('php://input') ?: '';
    $datos = [];
    if ($crudo !== '' && str_starts_with(ltrim($crudo), '{')) {
        $datos = json_decode($crudo, true) ?: [];
    }
    if ($datos === []) {
        $datos = $_POST;
    }
    [$ok, $mensaje] = fen_guardar_reporte($pdo, $datos, fen_ip());
    http_response_code($ok ? 201 : 400);
    echo json_encode(['ok' => $ok, 'mensaje' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

$recurso = (string) ($_GET['recurso'] ?? 'todo');
$salida = ['ok' => true, 'avisos' => []];

if ($recurso === 'enso' || $recurso === 'todo') {
    $salida['enso'] = fen_estado_enso();
    if (($salida['enso']['oni'] ?? null) === null) {
        $salida['avisos'][] = 'No fue posible consultar el índice ONI de la NOAA.';
    }
}
if ($recurso === 'focos' || $recurso === 'todo') {
    $f = fen_focos_calor((int) ($_GET['dias'] ?? 3));
    $salida['focos'] = $f['focos'];
    $salida['focos_fuente'] = $f['fuente'];
    if ($f['aviso'] !== '') {
        $salida['avisos'][] = $f['aviso'];
    }
}
if ($recurso === 'reportes' || $recurso === 'todo') {
    $salida['reportes'] = fen_reportes($pdo);
}
if ($recurso === 'bomberos' || $recurso === 'todo') {
    $salida['bomberos'] = fen_bomberos($pdo);
}
if ($recurso === 'historico' || $recurso === 'todo') {
    $salida['historico'] = fen_historico();
}

echo json_encode($salida, JSON_UNESCAPED_UNICODE);
