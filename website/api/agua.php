<?php

/**
 * API pública del estado del agua en Boyacá.
 *
 *   GET ?recurso=historico   serie semanal del VHI por municipio y serie
 *                            diaria del volumen del embalse
 *
 * El histórico se sirve aparte y no dentro de la página porque pesa bastante
 * más que la foto del día: así el micrositio carga liviano y el archivo solo
 * viaja cuando el visitante abre la línea de tiempo.
 */

declare(strict_types=1);

require_once __DIR__ . '/../lib/agua.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$recurso = isset($_GET['recurso']) ? (string) $_GET['recurso'] : 'historico';

$archivos = [
    'historico' => 'historico_agua.json',   // VHI semanal + embalse diario
    'calor' => 'historico_calor.json',      // focos de calor diarios
];
if (!isset($archivos[$recurso])) {
    http_response_code(400);
    echo json_encode(['error' => 'Recurso no reconocido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$archivo = __DIR__ . '/../data/fenomenos/' . $archivos[$recurso];
if (!is_file($archivo)) {
    echo json_encode(['aviso' => 'Todavía no se ha construido esta serie.'],
                     JSON_UNESCAPED_UNICODE);
    exit;
}

// El archivo ya es JSON válido: se entrega tal cual, con caché de una hora
// para no releerlo en cada movimiento del control de tiempo.
$mtime = (int) filemtime($archivo);
header('Cache-Control: public, max-age=3600');
header('ETag: "' . md5((string) $mtime) . '"');
if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === '"' . md5((string) $mtime) . '"') {
    http_response_code(304);
    exit;
}
readfile($archivo);
