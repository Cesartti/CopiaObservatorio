<?php
/**
 * Descarga de los CSV de un indicador con BOM UTF-8.
 *
 * Los archivos `indicador/<id>/<n>.csv` se guardan en UTF-8 sin BOM porque el
 * visor de gráficas los lee así. Excel, en cambio, abre los CSV con la
 * codificación regional del equipo (Windows-1252) y muestra «AÃ±o» o
 * «BoyacÃ¡». Este punto de descarga entrega el mismo contenido con el BOM
 * (\xEF\xBB\xBF) al frente y un nombre de archivo legible, sin alterar los
 * archivos que consume el visor.
 *
 * Uso: descargar-csv.php?id=3201&n=1
 */
declare(strict_types=1);

$id = isset($_GET['id']) ? preg_replace('/\D/', '', (string) $_GET['id']) : '';
$n = isset($_GET['n']) ? preg_replace('/\D/', '', (string) $_GET['n']) : '1';

if ($id === '' || strlen($id) > 8 || $n === '' || strlen($n) > 2) {
    http_response_code(400);
    exit('Solicitud no válida.');
}

$ruta = __DIR__ . '/indicador/' . $id . '/' . $n . '.csv';
if (!is_file($ruta)) {
    http_response_code(404);
    exit('Archivo no encontrado.');
}

// Nombre de archivo a partir del título de la gráfica, si existe.
$nombre = 'indicador-' . $id . '-' . $n;
$infoPath = __DIR__ . '/indicador/' . $id . '/' . $n . '.info';
if (is_readable($infoPath)) {
    foreach (file($infoPath) ?: [] as $linea) {
        if (stripos(trim($linea), 'titulo:') === 0 || stripos(trim($linea), 'título:') === 0) {
            $titulo = trim(substr($linea, strpos($linea, ':') + 1));
            $titulo = iconv('UTF-8', 'ASCII//TRANSLIT', $titulo) ?: $titulo;
            $titulo = preg_replace('/[^A-Za-z0-9]+/', '-', $titulo);
            $titulo = trim((string) $titulo, '-');
            if ($titulo !== '') {
                $nombre = substr($id . '-' . $titulo, 0, 80);
            }
            break;
        }
    }
}

$contenido = (string) file_get_contents($ruta);
// Evita duplicar el BOM si algún archivo ya lo trae.
if (strncmp($contenido, "\xEF\xBB\xBF", 3) !== 0) {
    $contenido = "\xEF\xBB\xBF" . $contenido;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nombre . '.csv"');
header('Content-Length: ' . strlen($contenido));
header('X-Content-Type-Options: nosniff');
echo $contenido;
