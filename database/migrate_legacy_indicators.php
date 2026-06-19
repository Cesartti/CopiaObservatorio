#!/usr/bin/env php
<?php
/**
 * ============================================================================
 *  MIGRACIÓN DE INDICADORES LEGACY → CMS
 * ============================================================================
 *  Lee las 385 carpetas indicador/XXXX/ con sus archivos .info y .csv
 *  y los inserta en las tablas del CMS:
 *    - indicators          (hoja de vida del indicador)
 *    - indicator_charts    (cada gráfico/pestaña del indicador)
 *    - indicator_observations (filas de datos CSV por gráfico)
 *
 *  USO:
 *    php migrate_legacy_indicators.php [--dry-run] [--verbose]
 *
 *  --dry-run   Solo muestra lo que haría, sin escribir en BD
 *  --verbose   Muestra cada indicador procesado
 * ============================================================================
 */

$dryRun  = in_array('--dry-run', $argv ?? []);
$verbose = in_array('--verbose', $argv ?? []);

/* ── Conexión BD ──────────────────────────────────────────────────────── */
require_once __DIR__ . '/../website/config/database.php';
$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "ERROR: No se pudo conectar a la BD. Revise config/database.php\n");
    fwrite(STDERR, "  → " . cms_last_db_error() . "\n");
    exit(1);
}

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  Migración de indicadores legacy → CMS              ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
echo "║  Modo: " . ($dryRun ? 'DRY-RUN (solo lectura)' : 'ESCRITURA EN BD    ') . "            ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

/* ── Mapeo dimensión → observatory_id ─────────────────────────────────── */
$dimensionMap = [
    '1' => ['slug' => 'economico',  'name' => 'Económico'],
    '2' => ['slug' => 'social',     'name' => 'Social'],
    '3' => ['slug' => 'ambiente',   'name' => 'Ambiental'],
    '4' => ['slug' => 'cti',        'name' => 'CTI'],
];

// Resolver observatory_id desde BD
$obsIds = [];
$st = $pdo->query('SELECT id, slug FROM observatories');
foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $obsIds[$row['slug']] = (int) $row['id'];
}
if (empty($obsIds)) {
    fwrite(STDERR, "ERROR: No hay observatorios en la tabla `observatories`.\n");
    fwrite(STDERR, "  → Ejecute primero: mysql < database/seed_example.sql\n");
    exit(1);
}

echo "  Observatorios encontrados en BD:\n";
foreach ($obsIds as $slug => $id) {
    echo "    [{$id}] {$slug}\n";
}
echo "\n";

/* ── Funciones auxiliares ─────────────────────────────────────────────── */
function parseInfoFile(string $path): array
{
    $info = [];
    if (!is_readable($path)) return $info;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $cut = strpos($line, ':');
        if ($cut === false) continue;
        $key = strtolower(trim(substr($line, 0, $cut)));
        // Normalizar acentos en claves
        $key = strtr($key, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
            'ñ'=>'n','ü'=>'u',
        ]);
        $val = trim(substr($line, $cut + 1));
        $info[$key] = $val;
    }
    return $info;
}

function parseCsv(string $path): array
{
    $rows = [];
    if (!is_readable($path)) return $rows;
    if (($handle = fopen($path, 'r')) === false) return $rows;

    // Detectar delimitador
    $firstLine = fgets($handle);
    rewind($handle);
    $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

    $header = null;
    while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
        if ($header === null) {
            $header = $data;
            continue;
        }
        if (count($data) < 2) continue;
        $rows[] = $data;
    }
    fclose($handle);
    return ['header' => $header ?? [], 'rows' => $rows];
}

function resolveObsId(string $indicatorId, array $dimensionMap, array $obsIds): ?int
{
    $prefix = $indicatorId[0];
    if (isset($dimensionMap[$prefix])) {
        $slug = $dimensionMap[$prefix]['slug'];
        return $obsIds[$slug] ?? null;
    }
    return null;
}

/* ── Escanear carpetas de indicadores ─────────────────────────────────── */
$baseDir = __DIR__ . '/../website/indicador';
if (!is_dir($baseDir)) {
    fwrite(STDERR, "ERROR: No se encontró la carpeta website/indicador/\n");
    exit(1);
}

$directories = array_filter(scandir($baseDir), function ($d) use ($baseDir) {
    return $d !== '.' && $d !== '..' && is_dir($baseDir . '/' . $d);
});
sort($directories);

echo "  Carpetas de indicadores encontradas: " . count($directories) . "\n\n";

/* ── Preparar statements ──────────────────────────────────────────────── */
if (!$dryRun) {
    $stmtInd = $pdo->prepare('
        INSERT INTO indicators (id, observatory_id, title, description, objective, formula_text, unit, source, periodicity, geographic_coverage, content_status)
        VALUES (:id, :obs_id, :title, :description, :objective, :formula, :unit, :source, :periodicity, :geo, :status)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            description = VALUES(description),
            source = VALUES(source),
            content_status = VALUES(content_status)
    ');

    $stmtChart = $pdo->prepare('
        INSERT INTO indicator_charts (indicator_id, chart_order, title, description, chart_type, source)
        VALUES (:ind_id, :chart_order, :title, :desc, :chart_type, :source)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            description = VALUES(description),
            chart_type = VALUES(chart_type)
    ');

    $stmtObs = $pdo->prepare('
        INSERT INTO indicator_observations (indicator_id, chart_order, period_label, geography_code, geography_name, category, value_decimal, value_text)
        VALUES (:ind_id, :chart_order, :period, :geo_code, :geo_name, :category, :val_dec, :val_text)
    ');

    // Limpiar datos previos si se re-ejecuta
    $pdo->exec('DELETE FROM indicator_observations WHERE indicator_id IN (SELECT id FROM indicators WHERE content_status = "published")');
}

/* ── Contadores ───────────────────────────────────────────────────────── */
$stats = [
    'indicators' => 0,
    'charts' => 0,
    'observations' => 0,
    'skipped' => 0,
    'errors' => [],
];

/* ── Procesar cada indicador ──────────────────────────────────────────── */
foreach ($directories as $dirName) {
    $dirPath = $baseDir . '/' . $dirName;
    $infoPath = $dirPath . '/indicador.info';

    if (!is_file($infoPath)) {
        // Carpetas especiales como dataEconomico, dataSocial
        if ($verbose) echo "  SKIP {$dirName} (sin indicador.info)\n";
        $stats['skipped']++;
        continue;
    }

    $info = parseInfoFile($infoPath);
    $title = $info['titulo'] ?? $info['title'] ?? ('Indicador ' . $dirName);
    $desc = $info['descripcion'] ?? $info['description'] ?? '';
    $cat = $info['categoria'] ?? $info['category'] ?? '';
    $subcat = $info['subcategoria'] ?? $info['subcategory'] ?? '';
    $tags = $info['etiquetas'] ?? '';
    $source = $info['fuentes'] ?? $info['fuente'] ?? '';

    $obsId = resolveObsId($dirName, $dimensionMap, $obsIds);
    if ($obsId === null) {
        $stats['errors'][] = "Indicador {$dirName}: no se pudo resolver observatory_id";
        if ($verbose) echo "  ERROR {$dirName}: observatory_id no resuelto\n";
        continue;
    }

    $indicatorId = (int) $dirName;

    if ($verbose) {
        echo "  [{$dirName}] {$title}\n";
        echo "         obs_id={$obsId} cat={$cat}\n";
    }

    // ── Insertar indicador ──
    if (!$dryRun) {
        try {
            $stmtInd->execute([
                ':id'          => $indicatorId,
                ':obs_id'      => $obsId,
                ':title'       => $title,
                ':description' => $desc,
                ':objective'   => $cat . ($subcat ? ' > ' . $subcat : ''),
                ':formula'     => null,
                ':unit'        => null,
                ':source'      => $source,
                ':periodicity' => null,
                ':geo'         => 'Boyacá',
                ':status'      => 'published',
            ]);
        } catch (PDOException $e) {
            $stats['errors'][] = "Indicador {$dirName}: " . $e->getMessage();
            continue;
        }
    }
    $stats['indicators']++;

    // ── Descubrir gráficos (N.info + N.csv) ──
    $chartFiles = glob($dirPath . '/*.info');
    $chartOrder = 0;

    foreach ($chartFiles as $chartInfoFile) {
        $basename = basename($chartInfoFile, '.info');
        if ($basename === 'indicador') continue; // Es la hoja de vida, no un gráfico
        if (!ctype_digit($basename)) continue;

        $chartOrder = (int) $basename;
        $chartInfo = parseInfoFile($chartInfoFile);

        $chartTitle = $chartInfo['titulo'] ?? $chartInfo['title'] ?? ("Gráfico {$chartOrder}");
        $chartDesc = $chartInfo['descripcion'] ?? $chartInfo['description'] ?? '';
        $chartType = $chartInfo['tipo'] ?? $chartInfo['type'] ?? 'LineChart';
        $chartSource = $chartInfo['fuentes'] ?? $chartInfo['fuente'] ?? $source;

        // Mapear tipos legacy
        $chartTypeMap = [
            'mapa' => 'GeoChart',
            'map'  => 'GeoChart',
            'barra' => 'BarChart',
            'bar'   => 'BarChart',
            'pie'   => 'PieChart',
            'torta' => 'PieChart',
            'area'  => 'AreaChart',
            'combo' => 'ComboChart',
        ];
        $normalizedType = strtolower(trim($chartType));
        $chartType = $chartTypeMap[$normalizedType] ?? $chartType;
        if ($chartType === 'LineChart' && !empty($chartInfo['vertical'])) {
            // Inteligencia mínima: si hay eje con actividades, combo
            $v = strtolower($chartInfo['vertical'] ?? '');
            if (strpos($v, 'actividad') !== false || strpos($v, 'lugar') !== false) {
                $chartType = 'BarChart';
            }
        }

        if ($verbose) echo "         chart #{$chartOrder}: {$chartTitle} ({$chartType})\n";

        if (!$dryRun) {
            try {
                $stmtChart->execute([
                    ':ind_id'      => $indicatorId,
                    ':chart_order' => $chartOrder,
                    ':title'       => $chartTitle,
                    ':desc'        => $chartDesc,
                    ':chart_type'  => $chartType,
                    ':source'      => $chartSource,
                ]);
            } catch (PDOException $e) {
                $stats['errors'][] = "Chart {$dirName}/{$chartOrder}: " . $e->getMessage();
                continue;
            }
        }
        $stats['charts']++;

        // ── Cargar datos del CSV correspondiente ──
        $csvPath = $dirPath . '/' . $basename . '.csv';
        if (!is_file($csvPath)) continue;

        $csvData = parseCsv($csvPath);
        if (empty($csvData['rows'])) continue;

        $header = $csvData['header'];
        $numCols = count($header);

        foreach ($csvData['rows'] as $row) {
            // Asegurar que la fila tenga suficientes columnas
            while (count($row) < $numCols) $row[] = '';

            $period = trim($row[0] ?? '');

            // Para CSVs con múltiples columnas de datos (series)
            for ($col = 1; $col < $numCols; $col++) {
                $rawVal = trim($row[$col] ?? '');
                $category = $header[$col] ?? "Serie {$col}";

                // Limpiar valor numérico
                $numVal = str_replace(['.', ','], ['', '.'], $rawVal);
                $numVal = preg_replace('/[^0-9.\-]/', '', $numVal);
                $decVal = is_numeric($numVal) ? (float) $numVal : null;

                if ($rawVal === '' && $decVal === null) continue;

                if (!$dryRun) {
                    try {
                        $stmtObs->execute([
                            ':ind_id'      => $indicatorId,
                            ':chart_order' => $chartOrder,
                            ':period'      => $period,
                            ':geo_code'    => null,
                            ':geo_name'    => null,
                            ':category'    => $category,
                            ':val_dec'     => $decVal,
                            ':val_text'    => $rawVal,
                        ]);
                    } catch (PDOException $e) {
                        // Silently continue for individual row errors
                    }
                }
                $stats['observations']++;
            }
        }
    }
}

/* ── Reporte final ────────────────────────────────────────────────────── */
echo "\n";
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  RESULTADO DE LA MIGRACIÓN                          ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
printf("║  Indicadores migrados:   %6d                     ║\n", $stats['indicators']);
printf("║  Gráficos creados:       %6d                     ║\n", $stats['charts']);
printf("║  Observaciones de datos: %6d                     ║\n", $stats['observations']);
printf("║  Carpetas omitidas:      %6d                     ║\n", $stats['skipped']);
printf("║  Errores:                %6d                     ║\n", count($stats['errors']));
echo "╚══════════════════════════════════════════════════════╝\n";

if (!empty($stats['errors'])) {
    echo "\nErrores:\n";
    foreach (array_slice($stats['errors'], 0, 20) as $err) {
        echo "  • {$err}\n";
    }
    if (count($stats['errors']) > 20) {
        echo "  ... y " . (count($stats['errors']) - 20) . " más\n";
    }
}

if ($dryRun) {
    echo "\n  ⚠  Modo DRY-RUN: no se escribió nada en la base de datos.\n";
    echo "     Ejecute sin --dry-run para aplicar los cambios.\n";
}

echo "\n  ✓ Proceso completado.\n";
