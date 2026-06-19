#!/usr/bin/env php
<?php
/**
 * ============================================================================
 *  SCRIPT MAESTRO: CONFIGURACIÓN COMPLETA DE LA RED DE OBSERVATORIOS
 * ============================================================================
 *  Ejecuta en orden todos los pasos para dejar el observatorio operativo:
 *
 *  PASO 1. Schema base (tablas)
 *  PASO 2. Migraciones incrementales (001–009)
 *  PASO 3. Seed base (roles, observatorios, usuario admin)
 *  PASO 4. Contenido mínimo (contacto, banners, noticias, redes, etc.)
 *  PASO 5. Migración de 385 indicadores legacy → CMS
 *  PASO 6. Generación de gráficos para tableros
 *  PASO 7. Verificación final
 *
 *  USO:
 *    php run_full_setup.php
 *
 *  REQUISITOS:
 *    - MySQL/MariaDB corriendo con la BD creada
 *    - config/database.php o database.local.php configurado
 *    - Ejecutar desde la carpeta database/
 * ============================================================================
 */

$startTime = microtime(true);

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║     CONFIGURACIÓN COMPLETA — RED DE OBSERVATORIOS BOYACÁ   ║\n";
echo "╠══════════════════════════════════════════════════════════════╣\n";
echo "║  Este script ejecuta TODOS los pasos para poblar la BD     ║\n";
echo "║  con datos, contenido y gráficos listos para producción.   ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

/* ── Conexión ─────────────────────────────────────────────────────────── */
require_once __DIR__ . '/../website/config/database.php';
$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "❌ ERROR: No se pudo conectar a la base de datos.\n");
    fwrite(STDERR, "   " . cms_last_db_error() . "\n\n");
    fwrite(STDERR, "   Verifique:\n");
    fwrite(STDERR, "   1. Que MySQL/MariaDB esté corriendo\n");
    fwrite(STDERR, "   2. Que la BD 'observatorio_boyaca' exista:\n");
    fwrite(STDERR, "      CREATE DATABASE IF NOT EXISTS observatorio_boyaca CHARACTER SET utf8mb4;\n");
    fwrite(STDERR, "   3. Que config/database.local.php tenga credenciales correctas\n\n");
    exit(1);
}
echo "✓ Conexión a BD exitosa.\n\n";

/* ── Utilidades ───────────────────────────────────────────────────────── */
function runSql(PDO $pdo, string $file, string $label): bool
{
    echo "  ┌─ {$label}\n";
    echo "  │  Archivo: {$file}\n";

    if (!is_readable($file)) {
        echo "  │  ⚠ Archivo no encontrado. Saltando.\n";
        echo "  └─\n\n";
        return false;
    }

    $sql = file_get_contents($file);
    if (trim($sql) === '') {
        echo "  │  ⚠ Archivo vacío. Saltando.\n";
        echo "  └─\n\n";
        return false;
    }

    try {
        // Ejecutar sentencia por sentencia (separadas por ;)
        $statements = preg_split('/;\s*$/m', $sql);
        $executed = 0;
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '' || strpos($stmt, '--') === 0) continue;
            // Omitir líneas que son solo SELECT de verificación
            if (stripos($stmt, 'SELECT') === 0 && stripos($stmt, 'INSERT') === false) continue;
            $pdo->exec($stmt);
            $executed++;
        }
        echo "  │  ✓ {$executed} sentencias ejecutadas.\n";
        echo "  └─\n\n";
        return true;
    } catch (PDOException $e) {
        echo "  │  ⚠ Error (no fatal): " . $e->getMessage() . "\n";
        echo "  └─\n\n";
        return false;
    }
}

function runPhp(string $script, string $label, array $extraArgs = []): bool
{
    echo "  ┌─ {$label}\n";
    echo "  │  Script: {$script}\n";

    if (!is_readable($script)) {
        echo "  │  ⚠ Script no encontrado. Saltando.\n";
        echo "  └─\n\n";
        return false;
    }

    $cmd = PHP_BINARY . ' ' . escapeshellarg($script);
    foreach ($extraArgs as $arg) {
        $cmd .= ' ' . escapeshellarg($arg);
    }
    $cmd .= ' 2>&1';

    $output = [];
    $exitCode = 0;
    exec($cmd, $output, $exitCode);

    // Mostrar resumen (últimas 10 líneas)
    $lines = array_slice($output, -10);
    foreach ($lines as $line) {
        echo "  │  {$line}\n";
    }

    if ($exitCode !== 0) {
        echo "  │  ⚠ Exit code: {$exitCode}\n";
    } else {
        echo "  │  ✓ Completado.\n";
    }
    echo "  └─\n\n";
    return $exitCode === 0;
}

/* ════════════════════════════════════════════════════════════════════════
   PASO 1: SCHEMA BASE
   ════════════════════════════════════════════════════════════════════════ */
echo "━━━ PASO 1/7: Schema base ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
runSql($pdo, __DIR__ . '/schema.sql', 'Creando tablas principales');

/* ════════════════════════════════════════════════════════════════════════
   PASO 2: MIGRACIONES INCREMENTALES
   ════════════════════════════════════════════════════════════════════════ */
echo "━━━ PASO 2/7: Migraciones ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$migDir = __DIR__ . '/migrations';
if (is_dir($migDir)) {
    $migs = glob($migDir . '/*.sql');
    sort($migs);
    foreach ($migs as $mig) {
        runSql($pdo, $mig, 'Migración: ' . basename($mig));
    }
} else {
    echo "  ⚠ Carpeta migrations/ no encontrada.\n\n";
}

/* ════════════════════════════════════════════════════════════════════════
   PASO 3: SEED BASE (roles, observatorios, admin)
   ════════════════════════════════════════════════════════════════════════ */
echo "━━━ PASO 3/7: Seed base ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
runSql($pdo, __DIR__ . '/seed_example.sql', 'Roles, observatorios y usuario admin');

/* ════════════════════════════════════════════════════════════════════════
   PASO 4: CONTENIDO MÍNIMO VIABLE
   ════════════════════════════════════════════════════════════════════════ */
echo "━━━ PASO 4/7: Contenido mínimo (banners, noticias, contacto) \n";
runSql($pdo, __DIR__ . '/seed_full_content.sql', 'Carga de contenido editorial');

/* ════════════════════════════════════════════════════════════════════════
   PASO 5: MIGRACIÓN DE 385 INDICADORES LEGACY
   ════════════════════════════════════════════════════════════════════════ */
echo "━━━ PASO 5/7: Migración de indicadores legacy ━━━━━━━━━━━━━━\n";
runPhp(__DIR__ . '/migrate_legacy_indicators.php', 'Leyendo 385 carpetas → indicators + observations');

/* ════════════════════════════════════════════════════════════════════════
   PASO 6: GENERACIÓN DE GRÁFICOS PARA TABLEROS
   ════════════════════════════════════════════════════════════════════════ */
echo "━━━ PASO 6/7: Gráficos de tableros CMS ━━━━━━━━━━━━━━━━━━━━━\n";
runPhp(__DIR__ . '/generate_dashboard_charts.php', 'Generando gráficos Google Charts por observatorio');

/* ════════════════════════════════════════════════════════════════════════
   PASO 7: VERIFICACIÓN FINAL
   ════════════════════════════════════════════════════════════════════════ */
echo "━━━ PASO 7/7: Verificación final ━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$checks = [
    ['Observatorios',       'SELECT COUNT(*) FROM observatories WHERE status = "active"',     5],
    ['Roles',               'SELECT COUNT(*) FROM roles',                                      2],
    ['Usuario admin',       'SELECT COUNT(*) FROM users WHERE is_active = 1',                 1],
    ['Banners portal',      'SELECT COUNT(*) FROM cms_home_banners WHERE is_active = 1',      5],
    ['Contacto',            'SELECT COUNT(*) FROM cms_contact',                                1],
    ['Redes sociales',      'SELECT COUNT(*) FROM cms_social_posts WHERE is_active = 1',      4],
    ['Hero slides',         'SELECT COUNT(*) FROM cms_microsite_hero_slides WHERE is_active=1', 10],
    ['Noticias publicadas', 'SELECT COUNT(*) FROM news WHERE content_status = "published"',   10],
    ['Etiquetas',           'SELECT COUNT(*) FROM tags',                                       10],
    ['Tableros activos',    'SELECT COUNT(*) FROM cms_dashboards WHERE is_active = 1',        4],
    ['Gráficos CMS',        'SELECT COUNT(*) FROM cms_charts WHERE is_active = 1',            4],
    ['Indicadores',         'SELECT COUNT(*) FROM indicators WHERE content_status="published"', 100],
    ['Observaciones datos', 'SELECT COUNT(*) FROM indicator_observations',                     500],
];

$allOk = true;
echo "\n  ┌───────────────────────────────────┬──────────┬─────────┐\n";
echo "  │ Módulo                            │ Conteo   │ Estado  │\n";
echo "  ├───────────────────────────────────┼──────────┼─────────┤\n";

foreach ($checks as [$label, $sql, $minExpected]) {
    try {
        $val = (int) $pdo->query($sql)->fetchColumn();
    } catch (Throwable $e) {
        $val = 0;
    }
    $ok = $val >= $minExpected;
    $status = $ok ? '  ✓  ' : '  ✗  ';
    if (!$ok) $allOk = false;
    printf("  │ %-33s │ %8d │ %s  │\n", $label, $val, $status);
}

echo "  └───────────────────────────────────┴──────────┴─────────┘\n\n";

$elapsed = round(microtime(true) - $startTime, 1);

if ($allOk) {
    echo "  ✅ ¡Configuración completa exitosa! ({$elapsed}s)\n";
    echo "     El observatorio está listo para producción.\n\n";
    echo "  Próximos pasos:\n";
    echo "    1. Abra el portal: http://localhost/website/index.php\n";
    echo "    2. Ingrese al CMS: http://localhost/website/admin/auth/login.php\n";
    echo "       (admin@observatorio.gov.co / Admin123*)\n";
    echo "    3. Personalice banners, noticias e indicadores desde el CMS.\n";
} else {
    echo "  ⚠ Configuración completada con advertencias ({$elapsed}s)\n";
    echo "    Algunos módulos tienen menos datos de los esperados.\n";
    echo "    Revise los errores anteriores.\n";
}

echo "\n";
