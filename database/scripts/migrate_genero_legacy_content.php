<?php
/**
 * migrate_genero_legacy_content.php
 *
 * Migra el contenido estático de website/indic-genero.php a la tabla
 * cms_microsite_sections (creada en migrations/013_microsite_sections.sql).
 *
 * Estrategia:
 *   1. Para cada uno de los 9 paneles del archivo viejo, crea (o actualiza) una
 *      pestaña raíz con su título, layout, icono e imagen destacada.
 *   2. El HTML del panel se extrae tal cual y se guarda como body_html — el
 *      usuario podrá refinarlo desde el editor visual del CMS.
 *   3. Para los paneles con chips internos (Información y Barreras), se crean
 *      sub-secciones que reflejan los chips originales.
 *
 * Uso:
 *   php database/scripts/migrate_genero_legacy_content.php           # ejecuta
 *   php database/scripts/migrate_genero_legacy_content.php --dry-run # simula
 *   php database/scripts/migrate_genero_legacy_content.php --reset   # borra
 *                                                                       antes
 *
 * Idempotencia: usa (observatory_id, parent_id, section_key) como pista; si la
 * sección ya existe se ACTUALIZA su contenido en vez de duplicarse.
 */

declare(strict_types=1);

$argv = $_SERVER['argv'] ?? [];
$dryRun = in_array('--dry-run', $argv, true);
$reset  = in_array('--reset', $argv, true);

$root = dirname(__DIR__, 2);
require_once $root . '/website/config/database.php';

$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "[ERROR] Sin conexión a MySQL. Revise website/config/database.local.php\n");
    exit(1);
}

$legacyFile = $root . '/website/indic-genero.php';
if (!is_file($legacyFile)) {
    fwrite(STDERR, "[ERROR] No se encontró $legacyFile\n");
    exit(1);
}

$legacy = (string) file_get_contents($legacyFile);
if ($legacy === '') {
    fwrite(STDERR, "[ERROR] Archivo legado vacío.\n");
    exit(1);
}

/* ── Localizar observatorio "genero" ───────────────────────────────────── */
$st = $pdo->prepare('SELECT id FROM observatories WHERE slug = ? LIMIT 1');
$st->execute(['genero']);
$obsId = (int) $st->fetchColumn();
if ($obsId < 1) {
    fwrite(STDERR, "[ERROR] No existe el observatorio con slug='genero' en la tabla observatories.\n");
    exit(1);
}

/* ── Resetear (opcional) ───────────────────────────────────────────────── */
if ($reset) {
    echo "[INFO] --reset: borrando todas las secciones existentes del observatorio género…\n";
    if (!$dryRun) {
        $pdo->prepare('DELETE FROM cms_microsite_sections WHERE observatory_id = ?')->execute([$obsId]);
    }
}

/* ── Definir las 9 pestañas raíz con su metadata ───────────────────────── */
$tabs = [
    1 => [
        'key' => 'marco',     'title' => 'Marco institucional',  'icon' => 'fa-landmark',
        'layout' => 'standard',  'image' => 'assets/svg/img-genero/MARCO-INSTITUCIONAL.svg',
        'subtitle' => 'Normativa, misión, visión, propósito, objetivo e integrantes del Observatorio.',
        'children' => [],
    ],
    2 => [
        'key' => 'informacion', 'title' => 'Información',         'icon' => 'fa-circle-info',
        'layout' => 'chips',    'image' => 'assets/svg/img-genero/INFORMACION.svg',
        'subtitle' => 'Conceptos clave de género y enfoque diferencial.',
        'children' => [
            ['key' => 'sexo',              'title' => 'Sexo',                          'icon' => 'fa-venus-mars'],
            ['key' => 'genero',            'title' => 'Género',                        'icon' => 'fa-transgender'],
            ['key' => 'identidad',         'title' => 'Identidad de género',           'icon' => 'fa-id-card'],
            ['key' => 'expresion',         'title' => 'Expresión de género',           'icon' => 'fa-masks-theater'],
            ['key' => 'orientacion',       'title' => 'Orientación sexual',            'icon' => 'fa-heart'],
            ['key' => 'masculinidades',    'title' => 'Masculinidades y feminidades',  'icon' => 'fa-people-arrows'],
            ['key' => 'perspectiva',       'title' => 'Perspectiva de género',         'icon' => 'fa-eye'],
            ['key' => 'discriminacion',    'title' => 'Discriminación',                'icon' => 'fa-ban'],
            ['key' => 'interseccionalidad','title' => 'Interseccionalidad',            'icon' => 'fa-circle-nodes'],
            ['key' => 'violencia',         'title' => 'Violencia basada en género',    'icon' => 'fa-triangle-exclamation'],
        ],
    ],
    3 => [
        'key' => 'seguimiento', 'title' => 'Seguimiento',         'icon' => 'fa-chart-line',
        'layout' => 'standard', 'image' => 'assets/svg/img-genero/SEGUIMIENTO.svg',
        'subtitle' => 'Monitoreo y análisis de brechas de género en el territorio.',
        'children' => [],
    ],
    4 => [
        'key' => 'ruta',        'title' => 'Ruta de atención',    'icon' => 'fa-route',
        'layout' => 'standard', 'image' => 'assets/svg/img-genero/RUTA-ATENCION.svg',
        'subtitle' => 'Flujo de 15 pasos para activación de la ruta de atención integral.',
        'children' => [],
    ],
    5 => [
        'key' => 'barreras',    'title' => 'Barreras de acceso',  'icon' => 'fa-triangle-exclamation',
        'layout' => 'chips',    'image' => 'assets/svg/img-genero/BARRERAS-ACCESO.svg',
        'subtitle' => 'Identificación de barreras y rutas para superarlas.',
        'children' => [
            ['key' => 'quees',     'title' => '¿Qué es una barrera?', 'icon' => 'fa-circle-question',
                'image' => 'assets/svg/img-genero/barreras/barreraAcceso.png',
                'body' => '<p>Es un obstáculo que <strong>retrasa o impide la atención oportuna, eficaz y de calidad</strong> en el sistema de salud, justicia o protección.</p>
<ul>
  <li>Puede aparecer en urgencias, citas, exámenes, medicamentos o trámites EPS/IPS.</li>
  <li>Si te exigen autorizaciones innecesarias o te remiten sin resolver, hay barrera.</li>
  <li>Aplica también a barreras geográficas, administrativas o de información.</li>
</ul>'],
            ['key' => 'leyes',     'title' => 'Tus derechos',          'icon' => 'fa-scale-balanced',
                'image' => 'assets/svg/img-genero/barreras/derechos.png',
                'body' => '<h4>Tus derechos (leyes)</h4>
<ul>
  <li>Derecho fundamental a la salud: acceso oportuno y con calidad.</li>
  <li>Atención prioritaria e inmediata en violencias basadas en género.</li>
  <li>Trato digno, sin discriminación y con enfoque diferencial.</li>
  <li>Continuidad de ruta: controles, medicamentos y remisiones.</li>
</ul>'],
            ['key' => 'ejemplos',  'title' => 'Ejemplos',              'icon' => 'fa-list-check',
                'image' => 'assets/svg/img-genero/barreras/ejemplos.png',
                'body' => '<h4>Ejemplos de barreras</h4>
<div class="row g-2">
  <div class="col-md-6"><div class="p-3 border rounded h-100"><strong>Demoras excesivas</strong><br><span class="text-muted small">Citas o procedimientos fuera de tiempos razonables.</span></div></div>
  <div class="col-md-6"><div class="p-3 border rounded h-100"><strong>Trámites innecesarios</strong><br><span class="text-muted small">Autorizaciones que no deberían bloquear la atención.</span></div></div>
  <div class="col-md-6"><div class="p-3 border rounded h-100"><strong>Sin agenda disponible</strong><br><span class="text-muted small">No asignan cita acorde con urgencia.</span></div></div>
  <div class="col-md-6"><div class="p-3 border rounded h-100"><strong>Barreras geográficas</strong><br><span class="text-muted small">Distancia, transporte u horarios imposibles.</span></div></div>
</div>'],
            ['key' => 'quehacer',  'title' => '¿Qué hacer?',           'icon' => 'fa-handshake-angle',
                'image' => 'assets/svg/img-genero/barreras/quehacer.png',
                'cta_label' => 'Llamar a la línea 155',
                'cta_url'   => 'tel:155',
                'body' => '<h4>¿Qué hacer ante una barrera?</h4>
<ul>
  <li>Solicite atención por urgencias y activación de ruta clínica cuando aplique.</li>
  <li>Radique <strong>PQRD</strong> en EPS/IPS y guarde número de radicado.</li>
  <li>Escale a SuperSalud: <strong>01 8000 513 700</strong>.</li>
  <li>Líneas de orientación: <strong>155</strong> (mujeres) y <strong>141</strong> (NNA).</li>
</ul>'],
        ],
    ],
    6 => [
        'key' => 'atencion',   'title' => 'Atención integral',    'icon' => 'fa-hand-holding-heart',
        'layout' => 'standard','image' => 'assets/svg/img-genero/ATENCION-INTEGRAL.svg',
        'subtitle' => 'Servicios y acciones de respuesta oportuna con enfoque de derechos.',
        'children' => [],
    ],
    7 => [
        'key' => 'politica',   'title' => 'Política pública',     'icon' => 'fa-file-contract',
        'layout' => 'standard','image' => 'assets/svg/img-genero/POLITICAS-PUBLICAS.svg',
        'subtitle' => 'Marco programático y compromisos de política pública en género.',
        'children' => [],
    ],
    8 => [
        'key' => 'campanas',   'title' => 'Campañas y publicaciones', 'icon' => 'fa-bullhorn',
        'layout' => 'standard','image' => 'assets/svg/img-genero/CAMPANAS-CONTENIDOS.svg',
        'subtitle' => 'Estrategias de comunicación, campañas y material pedagógico.',
        'children' => [],
    ],
    9 => [
        'key' => 'reportes',   'title' => 'Reportes sociales',    'icon' => 'fa-file-lines',
        'layout' => 'standard','image' => 'assets/svg/img-genero/REPORTE-SOCIAL.svg',
        'subtitle' => 'Encuestas y reportes participativos desde las comunidades.',
        'children' => [],
    ],
];

/* ── Extraer HTML de cada panel del archivo viejo ──────────────────────── */
$panelBodies = [];
$panelDivStart = []; // offset donde comienza '<div...' del panel
$panelDivEnd   = []; // offset justo después de '>' (inicio del cuerpo)
if (preg_match_all('#<div\s+class="tab-pane[^"]*"\s+id="panel(\d+)"[^>]*>#i', $legacy, $m, PREG_OFFSET_CAPTURE)) {
    foreach ($m[0] as $i => $match) {
        $pid = (int) $m[1][$i][0];
        $panelDivStart[$pid] = $match[1];                    // donde inicia <div
        $panelDivEnd[$pid]   = $match[1] + strlen($match[0]); // justo tras el '>'
    }
}
if ($panelDivStart) {
    $sortedKeys = array_keys($panelDivStart);
    sort($sortedKeys);
    $textLength = strlen($legacy);
    foreach ($sortedKeys as $idx => $pid) {
        $contentStart = $panelDivEnd[$pid];
        $next = $sortedKeys[$idx + 1] ?? null;
        $contentEnd = $next !== null ? $panelDivStart[$next] : $textLength;
        if ($contentEnd <= $contentStart) continue;

        $raw = substr($legacy, $contentStart, $contentEnd - $contentStart);
        // Recortar el último </div> que cierra el tab-pane abierto.
        $raw = preg_replace('#</div>\s*$#', '', $raw, 1);
        $panelBodies[$pid] = trim((string) $raw);
    }
}

/* ── Helper: upsert por (observatory_id, parent_id, section_key) ──────── */
function ms_upsert(PDO $pdo, int $obsId, ?int $parentId, array $row, bool $dryRun): int
{
    if ($dryRun) {
        echo sprintf("  · upsert dry: parent=%s key=%s title=%s\n", $parentId ?? 'NULL', $row['section_key'], $row['title']);
        return 0;
    }
    // ¿existe?
    if ($parentId === null) {
        $find = $pdo->prepare('SELECT id FROM cms_microsite_sections WHERE observatory_id=? AND parent_id IS NULL AND section_key=? LIMIT 1');
        $find->execute([$obsId, $row['section_key']]);
    } else {
        $find = $pdo->prepare('SELECT id FROM cms_microsite_sections WHERE observatory_id=? AND parent_id=? AND section_key=? LIMIT 1');
        $find->execute([$obsId, $parentId, $row['section_key']]);
    }
    $existingId = (int) $find->fetchColumn();

    if ($existingId > 0) {
        $sql = 'UPDATE cms_microsite_sections SET
                    title=?, subtitle=?, body_html=?, layout=?, icon=?, image_url=?,
                    cta_label=?, cta_url=?, sort_order=?, is_active=1
                WHERE id=?';
        $pdo->prepare($sql)->execute([
            $row['title'], $row['subtitle'] ?? null, $row['body_html'] ?? null,
            $row['layout'] ?? 'standard', $row['icon'] ?? null, $row['image_url'] ?? null,
            $row['cta_label'] ?? null, $row['cta_url'] ?? null,
            (int) ($row['sort_order'] ?? 0), $existingId,
        ]);
        return $existingId;
    }
    $sql = 'INSERT INTO cms_microsite_sections
                (observatory_id, parent_id, section_key, title, subtitle, body_html, layout,
                 icon, image_url, cta_label, cta_url, sort_order, is_active)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1)';
    $pdo->prepare($sql)->execute([
        $obsId, $parentId, $row['section_key'], $row['title'],
        $row['subtitle'] ?? null, $row['body_html'] ?? null, $row['layout'] ?? 'standard',
        $row['icon'] ?? null, $row['image_url'] ?? null,
        $row['cta_label'] ?? null, $row['cta_url'] ?? null,
        (int) ($row['sort_order'] ?? 0),
    ]);
    return (int) $pdo->lastInsertId();
}

/* ── Aplicar migración ─────────────────────────────────────────────────── */
echo "[INFO] Migrando " . count($tabs) . " pestañas para observatorio género (id=$obsId)…\n";
echo $dryRun ? "[INFO] MODO DRY-RUN: no se escribirá en la BD.\n" : "";

$sort = 0;
foreach ($tabs as $pid => $tab) {
    $sort += 10;
    $body = $panelBodies[$pid] ?? '';
    // Quitar PHP tags inadvertidos (no debería haber, pero seguridad ante mal recorte)
    $body = preg_replace('/<\?(php|=).*?\?>/s', '', $body) ?? '';

    $row = [
        'section_key' => $tab['key'],
        'title'       => $tab['title'],
        'subtitle'    => $tab['subtitle'] ?? null,
        'body_html'   => $body !== '' ? $body : ('<p class="text-muted">Contenido pendiente — edite desde el CMS.</p>'),
        'layout'      => $tab['layout'] ?? 'standard',
        'icon'        => $tab['icon'] ?? null,
        'image_url'   => $tab['image'] ?? null,
        'sort_order'  => $sort,
    ];
    $rootId = ms_upsert($pdo, $obsId, null, $row, $dryRun);
    echo "  ✓ Pestaña '{$tab['title']}' (key={$tab['key']}, panel#$pid, " . strlen($body) . " chars)\n";

    /* Sub-secciones */
    if (!empty($tab['children'])) {
        $cSort = 0;
        foreach ($tab['children'] as $child) {
            $cSort += 10;
            $cRow = [
                'section_key' => $child['key'],
                'title'       => $child['title'],
                'subtitle'    => null,
                'body_html'   => $child['body'] ?? null,
                'layout'      => 'standard',
                'icon'        => $child['icon'] ?? null,
                'image_url'   => $child['image'] ?? null,
                'cta_label'   => $child['cta_label'] ?? null,
                'cta_url'     => $child['cta_url'] ?? null,
                'sort_order'  => $cSort,
            ];
            ms_upsert($pdo, $obsId, $rootId, $cRow, $dryRun);
            echo "      ↳ chip '{$child['title']}' (key={$child['key']})\n";
        }
    }
}

echo "[OK] Migración finalizada. Verifique en: website/cms/tabs.php?obs=$obsId\n";
echo "[OK] Página pública: website/observatorio.php?slug=genero\n";
