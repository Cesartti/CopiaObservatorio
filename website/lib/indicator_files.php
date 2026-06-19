<?php

/**
 * Librería de archivos de indicadores (sistema basado en carpetas website/indicador/NNNN/).
 *
 * Genera/lee los archivos que consume website/indicador.php:
 *   - indicador.info           metadatos del indicador (clave:valor)
 *   - N.csv / N.info           datos y metadatos de cada gráfico
 *   - display.js               clases Chart/Display para Google Charts
 *
 * Convención de IDs: primer dígito = observatorio (1=económico, 2=social,
 * 3=ambiente, 4=cti, 5=género). Cada observatorio usa el rango D000–D999.
 */

require_once __DIR__ . '/../functions.php'; // getInfo()

const INF_BASE_DIR = __DIR__ . '/../indicador';

/** Mapa dígito → slug/carpeta de assets del observatorio. */
function inf_observatories(): array
{
    return [
        1 => ['slug' => 'economico', 'name' => 'Observatorio Económico'],
        2 => ['slug' => 'social',    'name' => 'Observatorio Social'],
        3 => ['slug' => 'ambiente',  'name' => 'Observatorio de Medio Ambiente'],
        4 => ['slug' => 'cti',       'name' => 'Observatorio CTI'],
        5 => ['slug' => 'genero',    'name' => 'Observatorio de Asuntos de Género'],
    ];
}

/** Valida que el ID sea de 4 dígitos y de un observatorio conocido. */
function inf_valid_id($id): bool
{
    $s = (string) $id;
    if (!ctype_digit($s) || strlen($s) !== 4) {
        return false;
    }
    $digit = (int) $s[0];

    return isset(inf_observatories()[$digit]);
}

/** Carpeta absoluta de un indicador (saneada: solo dígitos). */
function inf_dir(int $id): string
{
    return INF_BASE_DIR . '/' . $id;
}

/** Siguiente ID libre dentro del rango de un observatorio (dígito 1..5). */
function inf_next_free_id(int $digit): int
{
    $start = $digit * 1000 + 1; // p.ej. 1001
    $end = $digit * 1000 + 999;
    for ($i = $start; $i <= $end; $i++) {
        if (!is_dir(inf_dir($i))) {
            return $i;
        }
    }

    return $start;
}

/** Lista indicadores existentes agrupados por observatorio. */
function inf_list_indicators(): array
{
    $out = [];
    if (!is_dir(INF_BASE_DIR)) {
        return $out;
    }
    foreach (scandir(INF_BASE_DIR) as $entry) {
        if (!ctype_digit($entry) || strlen($entry) !== 4) {
            continue;
        }
        $info = @getInfo(INF_BASE_DIR . '/' . $entry . '/indicador.info');
        $digit = (int) $entry[0];
        $obs = inf_observatories()[$digit] ?? ['slug' => 'otro', 'name' => 'Otro'];
        $out[] = [
            'id' => (int) $entry,
            'titulo' => $info['titulo'] ?? ('Indicador ' . $entry),
            'categoria' => $info['categoria'] ?? '',
            'observatory' => $obs['name'],
            'observatory_slug' => $obs['slug'],
            'charts' => inf_count_charts((int) $entry),
            'has_pdf' => isset($info['ficha']) && $info['ficha'] !== '',
        ];
    }
    usort($out, fn ($a, $b) => $a['id'] <=> $b['id']);

    return $out;
}

/** Cuenta cuántos gráficos (N.csv) tiene un indicador. */
function inf_count_charts(int $id): int
{
    $dir = inf_dir($id);
    $n = 0;
    for ($i = 1; $i <= 30; $i++) {
        if (is_file($dir . '/' . $i . '.csv')) {
            $n++;
        } else {
            break;
        }
    }

    return $n;
}

/* ------------------------------------------------------------------ *
 *  Lectura de Excel (.xlsx) y CSV
 * ------------------------------------------------------------------ */

/** Quita namespaces y prefijos para poder usar SimpleXML sin dolores. */
function inf_xml_nons(string $raw): ?SimpleXMLElement
{
    $raw = preg_replace('/\sxmlns(:\w+)?="[^"]*"/', '', $raw);
    $raw = preg_replace('/(<\/?)\w+:/', '$1', $raw);     // prefijo en etiquetas
    $raw = preg_replace('/\s\w+:(\w+=)/', ' $1', $raw);   // prefijo en atributos
    $xml = @simplexml_load_string($raw);

    return $xml ?: null;
}

/** Convierte una referencia de celda (p.ej. "C5") a índice de columna 0-based. */
function inf_col_index(string $ref): int
{
    if (!preg_match('/^([A-Z]+)/', $ref, $m)) {
        return 0;
    }
    $letters = $m[1];
    $n = 0;
    for ($i = 0, $len = strlen($letters); $i < $len; $i++) {
        $n = $n * 26 + (ord($letters[$i]) - 64);
    }

    return $n - 1;
}

/**
 * Lee un .xlsx → ['NombreHoja' => [ [c0,c1,...], ... ]].
 * Lector mínimo para grillas simples (encabezados + datos).
 *
 * @throws RuntimeException
 */
function inf_xlsx_to_sheets(string $path): array
{
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('La extensión zip de PHP no está activa; no se puede leer Excel.');
    }
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new RuntimeException('No se pudo abrir el archivo Excel.');
    }

    // Shared strings
    $shared = [];
    if (($s = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
        $sx = inf_xml_nons($s);
        if ($sx) {
            foreach ($sx->si as $si) {
                // Texto plano o rich-text (varios <r><t>)
                $text = '';
                if (isset($si->t)) {
                    $text = (string) $si->t;
                } else {
                    foreach ($si->r as $r) {
                        $text .= (string) $r->t;
                    }
                }
                $shared[] = $text;
            }
        }
    }

    // Mapa rId → archivo de hoja
    $relMap = [];
    if (($rels = $zip->getFromName('xl/_rels/workbook.xml.rels')) !== false) {
        $rx = inf_xml_nons($rels);
        if ($rx) {
            foreach ($rx->Relationship as $rel) {
                $relMap[(string) $rel['Id']] = (string) $rel['Target'];
            }
        }
    }

    // Orden y nombres de hojas
    $sheets = [];
    $wb = inf_xml_nons((string) $zip->getFromName('xl/workbook.xml'));
    if (!$wb || !isset($wb->sheets)) {
        $zip->close();
        throw new RuntimeException('El Excel no tiene hojas legibles.');
    }
    foreach ($wb->sheets->sheet as $sheet) {
        $name = (string) $sheet['name'];
        $rid = (string) $sheet['id'];
        $target = $relMap[$rid] ?? '';
        $target = ltrim($target, '/');
        if ($target === '') {
            continue;
        }
        $entry = str_starts_with($target, 'xl/') ? $target : 'xl/' . $target;
        $raw = $zip->getFromName($entry);
        if ($raw === false) {
            continue;
        }
        $sheets[$name] = inf_parse_sheet_xml($raw, $shared);
    }
    $zip->close();

    // Quitar hojas totalmente vacías
    $sheets = array_filter($sheets, fn ($rows) => inf_rows_nonempty($rows));
    if ($sheets === []) {
        throw new RuntimeException('El Excel no contiene datos legibles.');
    }

    return $sheets;
}

/** Parsea el XML de una hoja → filas (array de arrays). */
function inf_parse_sheet_xml(string $raw, array $shared): array
{
    $sx = inf_xml_nons($raw);
    $rows = [];
    if (!$sx || !isset($sx->sheetData)) {
        return $rows;
    }
    foreach ($sx->sheetData->row as $row) {
        $cells = [];
        $auto = 0;
        foreach ($row->c as $c) {
            $ref = (string) ($c['r'] ?? '');
            $idx = $ref !== '' ? inf_col_index($ref) : $auto;
            $type = (string) ($c['t'] ?? '');
            $val = '';
            if ($type === 's') {
                $val = $shared[(int) $c->v] ?? '';
            } elseif ($type === 'inlineStr') {
                $val = (string) ($c->is->t ?? '');
            } elseif (isset($c->v)) {
                $val = (string) $c->v;
            }
            $cells[$idx] = trim($val);
            $auto = $idx + 1;
        }
        if ($cells === []) {
            $rows[] = [];
            continue;
        }
        // Rellenar huecos de columnas faltantes
        $max = max(array_keys($cells));
        $line = [];
        for ($i = 0; $i <= $max; $i++) {
            $line[] = $cells[$i] ?? '';
        }
        $rows[] = $line;
    }

    // Recortar filas vacías al final
    while ($rows !== [] && inf_row_empty(end($rows))) {
        array_pop($rows);
    }

    return $rows;
}

function inf_row_empty(array $row): bool
{
    foreach ($row as $v) {
        if (trim((string) $v) !== '') {
            return false;
        }
    }

    return true;
}

function inf_rows_nonempty(array $rows): bool
{
    foreach ($rows as $r) {
        if (!inf_row_empty($r)) {
            return true;
        }
    }

    return false;
}

/** Lee un .csv suelto → una "hoja" de filas. */
function inf_csv_to_rows(string $path): array
{
    $rows = [];
    if (($fh = fopen($path, 'r')) === false) {
        throw new RuntimeException('No se pudo abrir el CSV.');
    }
    // Quitar BOM si lo trae
    $bom = fread($fh, 3);
    if ($bom !== "\xEF\xBB\xBF") {
        rewind($fh);
    }
    while (($line = fgetcsv($fh)) !== false) {
        $rows[] = array_map(fn ($v) => trim((string) $v), $line);
    }
    fclose($fh);
    while ($rows !== [] && inf_row_empty(end($rows))) {
        array_pop($rows);
    }

    return $rows;
}

/** Serializa filas a CSV (coma, UTF-8 sin BOM), formato de los N.csv actuales. */
function inf_rows_to_csv(array $rows): string
{
    $out = '';
    foreach ($rows as $row) {
        $cells = array_map(function ($v) {
            $v = (string) $v;
            if (preg_match('/[",\n]/', $v)) {
                return '"' . str_replace('"', '""', $v) . '"';
            }

            return $v;
        }, $row);
        $out .= implode(',', $cells) . "\n";
    }

    return $out;
}

/* ------------------------------------------------------------------ *
 *  Escritura de archivos del indicador
 * ------------------------------------------------------------------ */

/** Escribe indicador.info a partir de un mapa de metadatos. */
function inf_write_indicador_info(string $dir, array $meta): void
{
    $order = ['Categoría', 'Subcategoría', 'Titulo', 'Descripción', 'Etiquetas', 'Fuentes', 'Ficha'];
    $lines = [];
    foreach ($order as $key) {
        if (isset($meta[$key]) && $meta[$key] !== '') {
            $lines[] = $key . ':' . str_replace(["\r", "\n"], ' ', (string) $meta[$key]);
        }
    }
    file_put_contents($dir . '/indicador.info', implode("\n", $lines) . "\n");
}

/** Escribe N.info de un gráfico. */
function inf_write_chart_info(string $dir, int $n, array $info): void
{
    $order = ['Titulo', 'Descripción', 'Vertical', 'Horizontal', 'Fuentes'];
    $lines = [];
    foreach ($order as $key) {
        if (isset($info[$key]) && $info[$key] !== '') {
            $lines[] = $key . ':' . str_replace(["\r", "\n"], ' ', (string) $info[$key]);
        }
    }
    file_put_contents($dir . '/' . $n . '.info', implode("\n", $lines) . "\n");
}

/** Tipos de gráfico soportados por el generador. */
function inf_chart_types(): array
{
    return [
        'line' => 'Líneas',
        'column' => 'Barras verticales',
        'bar' => 'Barras horizontales',
        'area' => 'Área',
    ];
}

/**
 * Genera display.js a partir de la lista de tipos (uno por gráfico).
 * Espejo de la plantilla existente (website/indicador/1002/display.js).
 *
 * @param string[] $types p.ej. ['line','column']
 */
function inf_build_display_js(array $types): string
{
    $map = [
        'line' => ['class' => 'LineChart', 'opt' => "curveType: 'function',\n            pointSize: 6,"],
        'column' => ['class' => 'ColumnChart', 'opt' => "bar: {groupWidth: '70%'},"],
        'bar' => ['class' => 'BarChart', 'opt' => "bar: {groupWidth: '70%'},"],
        'area' => ['class' => 'AreaChart', 'opt' => "areaOpacity: 0.25,\n            pointSize: 4,"],
    ];
    $classes = [];
    $names = [];
    foreach (array_values($types) as $i => $type) {
        $t = $map[$type] ?? $map['line'];
        $n = $i + 1;
        $names[] = 'Chart' . $n;
        $classes[] = <<<JS
class Chart{$n} extends AbstractChart{
    format(){
        var f = new google.visualization.NumberFormat({pattern: Patterns.year});
        f.format(this._data, 0);
    }
    getOptions(info){
        return {
            hAxis: {title: info['horizontal'], format: Patterns.year},
            vAxis: {title: info['vertical']},
            {$t['opt']}
            chartArea: {left: 80, right: 30, top: 50, bottom: 50}
        };
    }
    getType(div){ return new google.visualization.{$t['class']}(div); }
}
JS;
    }
    $list = implode(',', $names);
    $body = implode("\n\n", $classes);

    return $body . "\n\nclass Display extends AbstractDisplay{\n    constructor(){ super('corechart',[{$list}]); }\n}\n";
}

/** Copia la carpeta del indicador a _backups/ antes de sobrescribir. */
function inf_backup_dir(int $id): ?string
{
    $src = inf_dir($id);
    if (!is_dir($src)) {
        return null;
    }
    $backupRoot = INF_BASE_DIR . '/_backups';
    if (!is_dir($backupRoot)) {
        @mkdir($backupRoot, 0775, true);
    }
    $dest = $backupRoot . '/' . $id . '_' . date('Ymd_His');
    @mkdir($dest, 0775, true);
    foreach (scandir($src) as $f) {
        if ($f === '.' || $f === '..') {
            continue;
        }
        if (is_file($src . '/' . $f)) {
            @copy($src . '/' . $f, $dest . '/' . $f);
        }
    }

    return $dest;
}

/** Elimina los N.csv/N.info sobrantes si el nuevo set tiene menos gráficos. */
function inf_clear_extra_charts(string $dir, int $keep): void
{
    for ($i = $keep + 1; $i <= 30; $i++) {
        @unlink($dir . '/' . $i . '.csv');
        @unlink($dir . '/' . $i . '.info');
    }
}
