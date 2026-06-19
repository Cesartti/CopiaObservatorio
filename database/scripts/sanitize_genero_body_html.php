<?php
/**
 * sanitize_genero_body_html.php
 *
 * Limpia el body_html de las secciones migradas desde indic-genero.php:
 *   - elimina <style>...</style> y <script>...</script> inline
 *   - elimina <link rel="stylesheet"> embebidos
 *   - elimina el módulo "mod-conceptos" (panel 2) porque sus contenidos ya
 *     están migrados como sub-secciones (chips) y se renderizan aparte
 *   - normaliza espacios excesivos
 *
 * Uso:
 *   php database/scripts/sanitize_genero_body_html.php           # aplica
 *   php database/scripts/sanitize_genero_body_html.php --dry-run # simula
 */
declare(strict_types=1);

$argv = $_SERVER['argv'] ?? [];
$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__, 2) . '/website/config/database.php';
$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "NO_DB: " . cms_last_db_error() . "\n");
    exit(1);
}

$st = $pdo->prepare('SELECT id FROM observatories WHERE slug = ? LIMIT 1');
$st->execute(['genero']);
$obsId = (int) $st->fetchColumn();
if ($obsId < 1) { fwrite(STDERR, "NO_OBS_GENERO\n"); exit(1); }

$rows = $pdo->prepare('SELECT id, section_key, title, body_html FROM cms_microsite_sections WHERE observatory_id = ? AND parent_id IS NULL');
$rows->execute([$obsId]);
$rows = $rows->fetchAll(PDO::FETCH_ASSOC);

$updates = 0;
foreach ($rows as $r) {
    $orig = (string) ($r['body_html'] ?? '');
    if ($orig === '') continue;
    $clean = $orig;

    // <style>, <script>, <link>
    $clean = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $clean) ?? $clean;
    $clean = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $clean) ?? $clean;
    $clean = preg_replace('#<link\b[^>]*>#is', '', $clean) ?? $clean;
    // tags meta sueltos también
    $clean = preg_replace('#<meta\b[^>]*>#is', '', $clean) ?? $clean;

    // Comentarios HTML largos del legacy (cabeceras decorativas, etc.)
    $clean = preg_replace('#<!--[\s\S]*?-->#', '', $clean) ?? $clean;

    // Panel 2 (informacion): remover módulo conceptos duplicado
    if ($r['section_key'] === 'informacion') {
        // bloque <section ... id="mod-conceptos">...</section> (un solo nivel
        // de section anidado en este caso)
        $clean = preg_replace('#<section\b[^>]*id=["\']mod-conceptos["\'][^>]*>[\s\S]*?</section>\s*#i', '', $clean) ?? $clean;
        // referencias adicionales por si quedaron sueltas
        $clean = preg_replace('#<div\b[^>]*id=["\']mod-conceptos["\'][^>]*>[\s\S]*?</div>\s*#i', '', $clean) ?? $clean;
    }

    // Espacios excesivos
    $clean = preg_replace("/\n\s*\n\s*\n+/", "\n\n", $clean) ?? $clean;
    $clean = trim($clean);

    if ($clean !== $orig) {
        echo sprintf("- '%s' (#%d): %d → %d bytes\n", $r['title'], $r['id'], strlen($orig), strlen($clean));
        if (!$dryRun) {
            $pdo->prepare('UPDATE cms_microsite_sections SET body_html = ? WHERE id = ?')
                ->execute([$clean, $r['id']]);
        }
        $updates++;
    }
}

echo $dryRun
    ? "\n[DRY-RUN] Cambios pendientes en $updates secciones.\n"
    : "\n[OK] $updates secciones actualizadas.\n";
