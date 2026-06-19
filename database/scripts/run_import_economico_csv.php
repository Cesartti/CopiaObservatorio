<?php
/**
 * Ejecutar desde la raíz del proyecto:
 *   C:\xampp\php\php.exe database/scripts/run_import_economico_csv.php
 */
require __DIR__ . '/../../website/config/database.php';
require __DIR__ . '/../../website/lib/indicator_metadata.php';

$csv = __DIR__ . '/../seeds/indicators_economico_xlsx_import.csv';
if (!is_readable($csv)) {
    fwrite(STDERR, "No se encuentra: $csv\n");
    exit(1);
}
$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "Sin conexión a BD: " . (cms_last_db_error() ?? '?') . "\n");
    exit(1);
}
$stats = im_import_csv($pdo, $csv);
echo json_encode($stats, JSON_UNESCAPED_UNICODE) . PHP_EOL;
