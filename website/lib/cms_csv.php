<?php

/**
 * Convierte CSV (exportado desde Excel) en matriz para Google Charts / JSON.
 * Primera fila = encabezados.
 */
function cms_csv_file_to_matrix(string $path): array
{
    $fh = fopen($path, 'rb');
    if (!$fh) {
        throw new RuntimeException('No se pudo abrir el archivo.');
    }
    $rows = [];
    while (($r = fgetcsv($fh)) !== false) {
        $rows[] = $r;
    }
    fclose($fh);
    if ($rows === []) {
        throw new RuntimeException('El archivo está vacío.');
    }

    // Excel “CSV UTF-8” a veces inserta BOM en la primera celda
    if (isset($rows[0][0]) && is_string($rows[0][0]) && str_starts_with($rows[0][0], "\xEF\xBB\xBF")) {
        $rows[0][0] = substr($rows[0][0], 3);
    }

    return $rows;
}

function cms_matrix_to_json_string(array $rows): string
{
    return json_encode($rows, JSON_UNESCAPED_UNICODE);
}
