<?php
/**
 * dedupe_genero_headers.php
 *
 * Elimina el primer <h1|h2|h3|h4> del body_html cuando duplica el título de la
 * sección (case-insensitive, sin tildes), y también las `.indicadores-section`
 * de cabecera que se renderizan de nuevo por el .ms-pane-head del sistema.
 *
 * Uso:
 *   php database/scripts/dedupe_genero_headers.php           # aplica
 *   php database/scripts/dedupe_genero_headers.php --dry-run # simula
 */
declare(strict_types=1);

$argv = $_SERVER['argv'] ?? [];
$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__, 2) . '/website/config/database.php';
$pdo = cms_pdo();
if (!$pdo) { fwrite(STDERR, "NO_DB\n"); exit(1); }

function normalize_es(string $s): string
{
    $s = trim($s);
    $s = strtolower($s);
    $s = strtr($s, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);
    $s = preg_replace('/\s+/', ' ', $s);
    return (string) $s;
}

$st = $pdo->prepare('SELECT s.id, s.title, s.body_html FROM cms_microsite_sections s
                     INNER JOIN observatories o ON o.id = s.observatory_id
                     WHERE o.slug = ? AND s.parent_id IS NULL');
$st->execute(['genero']);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

$updates = 0;
foreach ($rows as $r) {
    $orig = (string) $r['body_html'];
    if ($orig === '') continue;
    $clean = $orig;
    $titleNorm = normalize_es((string) $r['title']);

    // Primer <hN> al inicio del body (puede haber whitespace antes)
    $clean = preg_replace_callback(
        '#^\s*(<h[1-6][^>]*>)(.*?)</h[1-6]>\s*#is',
        function ($m) use ($titleNorm) {
            $inner = strip_tags($m[2]);
            return normalize_es($inner) === $titleNorm ? '' : $m[0];
        },
        $clean,
        1
    ) ?? $clean;

    // .indicadores-section que sólo contiene icono+título duplicado:
    // Estructura típica:
    //   <div class="indicadores-section ..."><div style="display:flex...">
    //     <img...><h2 class="indicador-title">TITLE</h2></div><p>...</p>
    //   </div>
    // Sólo eliminamos la cabecera interna (el primer div con flex), no toda la
    // sección — el resto del contenido del panel sigue debajo.
    $clean = preg_replace_callback(
        '#(<div\b[^>]*class="indicadores-section[^"]*"[^>]*>\s*)(<div\b[^>]*style="[^"]*display:\s*flex[^"]*"[^>]*>[\s\S]*?</div>\s*)#i',
        function ($m) use ($titleNorm) {
            // Verifica si la cabecera interna contiene el título duplicado
            $headerHtml = $m[2];
            if (preg_match('#<h[1-6][^>]*>([\s\S]*?)</h[1-6]>#i', $headerHtml, $h)) {
                $inner = normalize_es(strip_tags($h[1]));
                if ($inner === $titleNorm) {
                    return $m[1]; // mantén el wrapper, quita la cabecera duplicada
                }
            }
            return $m[0];
        },
        $clean,
        1
    ) ?? $clean;

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

echo $dryRun ? "\n[DRY-RUN] $updates cambios pendientes.\n" : "\n[OK] $updates secciones actualizadas.\n";
