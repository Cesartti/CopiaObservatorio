<?php

/**
 * Recibe los aportes del formulario del foro (website/foro.php).
 *
 * Guarda poco y lo guarda crudo: el valor de este formulario esta en lo que la
 * gente escribe, no en encasillarlo. Solo se validan las opciones cerradas y se
 * recortan las longitudes para que no entre basura en la tabla.
 *
 * El nombre solo se guarda si la persona pidio credito; si eligio anonimo o no
 * autorizo, se descarta aunque lo haya escrito.
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'JSON inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Recorta y limpia un campo de texto libre. */
function foro_txt($data, $clave, $max)
{
    $v = isset($data[$clave]) ? trim((string) $data[$clave]) : '';
    $v = preg_replace('/\s+/u', ' ', $v);
    if ($v === '') {
        return null;
    }

    return mb_substr($v, 0, $max);
}

$ROLES = ['academia', 'entidad-publica', 'sector-productivo', 'organizacion-social',
          'estudiante', 'otro'];
$BARRERAS = ['no-existen', 'dispersos', 'desactualizados', 'no-comparables',
             'no-se-donde', 'otra'];
$PRIORIDADES = ['estandarizar', 'datos-abiertos', 'formacion', 'articulacion',
                'publicacion', 'visualizacion'];
$CONSENTIMIENTOS = ['anonimo', 'con-credito', 'no'];

$rol = isset($data['rol']) && in_array($data['rol'], $ROLES, true) ? $data['rol'] : null;
$barrera = isset($data['barrera']) && in_array($data['barrera'], $BARRERAS, true)
    ? $data['barrera'] : null;

$prior = [];
if (isset($data['prioridades']) && is_array($data['prioridades'])) {
    foreach ($data['prioridades'] as $p) {
        if (in_array($p, $PRIORIDADES, true) && !in_array($p, $prior, true)) {
            $prior[] = $p;
        }
    }
}

$cons = isset($data['consentimiento']) && in_array($data['consentimiento'], $CONSENTIMIENTOS, true)
    ? $data['consentimiento'] : 'no';

$faltaInfo = foro_txt($data, 'falta_info', 1200);
$aporte = foro_txt($data, 'aporte', 1800);

// Sin contenido util no se guarda una fila vacia.
if ($faltaInfo === null && $aporte === null && !$prior && $barrera === null) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Responda al menos una pregunta'],
        JSON_UNESCAPED_UNICODE);
    exit;
}

// El nombre solo tiene sentido si pidio credito.
$nombre = $cons === 'con-credito' ? foro_txt($data, 'nombre', 120) : null;

$correo = foro_txt($data, 'correo', 160);
if ($correo !== null && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $correo = null;
}

$pdo = cms_pdo();
if (!$pdo) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Servicio no disponible'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO foro_aportes
           (evento, institucion, rol, territorio, falta_info, barrera, prioridades,
            aporte, consentimiento, nombre, correo)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        foro_txt($data, 'evento', 80) ?: 'foro-unillanos-2026',
        foro_txt($data, 'institucion', 160),
        $rol,
        foro_txt($data, 'territorio', 120),
        $faltaInfo,
        $barrera,
        $prior ? implode('|', $prior) : null,
        $aporte,
        $cons,
        $nombre,
        $correo,
    ]);
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el aporte'],
        JSON_UNESCAPED_UNICODE);
}
