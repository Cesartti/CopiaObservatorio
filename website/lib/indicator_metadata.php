<?php

/**
 * Lectura/escritura de metadatos enriquecidos de indicadores (hoja de vida).
 */

function im_fields(): array
{
    return [
        'id','observatory_id','title','category_1','category_2','tags','unit',
        'thematic_breakdown','geographic_breakdown','definition','calculation_formula',
        'periodicity','baseline_date','delivery_form','source','source_link',
        'actors','responsible_entity','observations','availability_status',
    ];
}

function im_field_labels(): array
{
    return [
        'id' => 'Código indicador',
        'observatory_id' => 'Observatorio',
        'title' => 'Nombre del indicador',
        'category_1' => 'Categoría primer orden',
        'category_2' => 'Categoría segundo orden',
        'tags' => 'Etiquetas / líneas temáticas',
        'unit' => 'Unidad de medida',
        'thematic_breakdown' => 'Desagregación temática',
        'geographic_breakdown' => 'Desagregación geográfica',
        'definition' => 'Definición del indicador',
        'calculation_formula' => 'Cálculo / fórmula',
        'periodicity' => 'Periodicidad',
        'baseline_date' => 'Fecha línea base',
        'delivery_form' => 'Forma de entrega',
        'source' => 'Fuentes de información',
        'source_link' => 'Enlace de la fuente',
        'actors' => 'Actores involucrados',
        'responsible_entity' => 'Entidad responsable',
        'observations' => 'Observaciones',
        'availability_status' => 'Disponibilidad',
    ];
}

function im_upsert(PDO $pdo, array $row): string
{
    $fields = im_fields();
    $data = [];
    foreach ($fields as $f) {
        $data[$f] = array_key_exists($f, $row) ? trim((string) $row[$f]) : '';
    }
    if ($data['id'] === '' || !ctype_digit($data['id']) || (int) $data['observatory_id'] < 1) {
        return 'skip:invalid-key';
    }
    // Ensure title (NOT NULL in schema)
    if ($data['title'] === '') $data['title'] = 'Indicador ' . $data['id'];

    // Check existence
    $st = $pdo->prepare('SELECT id FROM indicators WHERE id = ?');
    $st->execute([(int) $data['id']]);
    $exists = (bool) $st->fetch();

    if ($exists) {
        $cols = array_diff($fields, ['id']);
        $sets = implode(', ', array_map(fn($c) => "$c = ?", $cols));
        $sql = "UPDATE indicators SET $sets WHERE id = ?";
        $vals = [];
        foreach ($cols as $c) $vals[] = $data[$c] === '' ? null : $data[$c];
        $vals[] = (int) $data['id'];
        $pdo->prepare($sql)->execute($vals);
        return 'updated';
    }
    $cols = implode(', ', $fields);
    $marks = implode(', ', array_fill(0, count($fields), '?'));
    $sql = "INSERT INTO indicators ($cols) VALUES ($marks)";
    $vals = [];
    foreach ($fields as $c) {
        if ($c === 'id' || $c === 'observatory_id') $vals[] = (int) $data[$c];
        else $vals[] = $data[$c] === '' ? null : $data[$c];
    }
    $pdo->prepare($sql)->execute($vals);
    return 'inserted';
}

function im_import_csv(PDO $pdo, string $filePath): array
{
    $h = fopen($filePath, 'r');
    if (!$h) return ['error' => 'No se pudo abrir el archivo.'];
    // Detect BOM
    $firstBytes = fread($h, 3);
    if ($firstBytes !== "\xEF\xBB\xBF") rewind($h);

    $headers = fgetcsv($h);
    if (!$headers) { fclose($h); return ['error' => 'CSV vacío o sin cabecera.']; }
    $headers = array_map(fn($s) => trim(strtolower((string) $s)), $headers);

    $stats = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
    $line = 1;
    while (($r = fgetcsv($h)) !== false) {
        $line++;
        if (count($r) < 2) { $stats['skipped']++; continue; }
        $row = [];
        foreach ($headers as $i => $k) {
            $row[$k] = $r[$i] ?? '';
        }
        try {
            $res = im_upsert($pdo, $row);
            if (strpos($res, 'skip') === 0) $stats['skipped']++;
            elseif ($res === 'inserted') $stats['inserted']++;
            elseif ($res === 'updated') $stats['updated']++;
        } catch (Throwable $e) {
            $stats['errors'][] = "Línea $line: " . $e->getMessage();
            $stats['skipped']++;
        }
    }
    fclose($h);
    return $stats;
}

/** Recupera todos los indicadores enriquecidos de un observatorio */
function im_list_by_observatory(PDO $pdo, int $obsId): array
{
    $st = $pdo->prepare('SELECT * FROM indicators WHERE observatory_id = ? ORDER BY category_1, category_2, id');
    $st->execute([$obsId]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function im_get_one(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT * FROM indicators WHERE id = ?');
    $st->execute([$id]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    return $r ?: null;
}

/** Escanea website/indicador/XXXX/*.csv (archivos de datos descargables). */
function im_list_data_files_for_observatory(int $obsId, string $websiteRoot): array
{
    $prefix = (string) $obsId; // 1=eco, 2=soc, 3=amb, 4=cti
    $base = $websiteRoot . DIRECTORY_SEPARATOR . 'indicador';
    if (!is_dir($base)) return [];
    $out = [];
    $dh = opendir($base);
    while (($f = readdir($dh)) !== false) {
        if ($f === '.' || $f === '..') continue;
        if (!ctype_digit($f) || strlen($f) === 0 || $f[0] !== $prefix) continue;
        $folder = $base . DIRECTORY_SEPARATOR . $f;
        if (!is_dir($folder)) continue;
        $infoFile = $folder . '/indicador.info';
        $title = $f;
        if (is_readable($infoFile)) {
            foreach (file($infoFile) as $line) {
                if (stripos(trim($line), 'título:') === 0 || stripos(trim($line), 'titulo:') === 0) {
                    $title = trim(substr(trim($line), strpos($line, ':') + 1));
                    break;
                }
            }
        }
        $csvs = [];
        foreach (glob($folder . '/*.csv') ?: [] as $cf) {
            $csvs[] = basename($cf);
        }
        if ($csvs) {
            $out[] = ['id' => $f, 'title' => $title, 'files' => $csvs];
        }
    }
    closedir($dh);
    usort($out, fn($a, $b) => strcmp($a['id'], $b['id']));
    return $out;
}
