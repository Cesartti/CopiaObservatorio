<?php
/**
 * Importa los 3 CSV de hojas de vida (Económico, Social, Asuntos de Género)
 * generados por database/scripts/import_hojas_vida_dynamic_xlsx.py.
 *
 * Ejecutar desde la raíz del proyecto:
 *   C:\xampp\php\php.exe database/scripts/run_import_all_observatorios.php
 *
 * Para importar uno solo, pasar el slug:
 *   C:\xampp\php\php.exe database/scripts/run_import_all_observatorios.php economico
 *   C:\xampp\php\php.exe database/scripts/run_import_all_observatorios.php social
 *   C:\xampp\php\php.exe database/scripts/run_import_all_observatorios.php genero
 */
require __DIR__ . '/../../website/config/database.php';
require __DIR__ . '/../../website/lib/indicator_metadata.php';

$jobs = [
    'economico' => __DIR__ . '/../seeds/indicators_economico_dynamic_import.csv',
    'social'    => __DIR__ . '/../seeds/indicators_social_dynamic_import.csv',
    'genero'    => __DIR__ . '/../seeds/indicators_genero_dynamic_import.csv',
];

$only = $argv[1] ?? null;
if ($only !== null) {
    if (!isset($jobs[$only])) {
        fwrite(STDERR, "Slug inválido: $only. Usa: economico | social | genero | (ninguno=todos)\n");
        exit(2);
    }
    $jobs = [$only => $jobs[$only]];
}

$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "Sin conexión a BD: " . (cms_last_db_error() ?? '?') . "\n");
    exit(1);
}

$grand = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
$results = [];
foreach ($jobs as $slug => $csv) {
    if (!is_readable($csv)) {
        $msg = "[$slug] No se encuentra: $csv";
        fwrite(STDERR, $msg . PHP_EOL);
        $results[$slug] = ['error' => $msg];
        continue;
    }
    $stats = im_import_csv($pdo, $csv);
    $results[$slug] = $stats;
    foreach (['inserted', 'updated', 'skipped'] as $k) {
        $grand[$k] += $stats[$k] ?? 0;
    }
    if (!empty($stats['errors'])) {
        $grand['errors'] = array_merge($grand['errors'], $stats['errors']);
    }
}

echo json_encode([
    'per_observatory' => $results,
    'totals'          => $grand,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
