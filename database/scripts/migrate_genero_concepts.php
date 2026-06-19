<?php
/**
 * migrate_genero_concepts.php
 *
 * Carga las definiciones de los 10 conceptos (sub-secciones del chip-group
 * "Información") en cms_microsite_sections. Las definiciones provienen de
 * indic-genero.php (módulo #mod-conceptos). Para 'perspectiva' y 'violencia'
 * (no existían en el archivo viejo) se incluye una definición estándar.
 */
declare(strict_types=1);

$argv = $_SERVER['argv'] ?? [];
$dryRun = in_array('--dry-run', $argv, true);

require_once dirname(__DIR__, 2) . '/website/config/database.php';
$pdo = cms_pdo();
if (!$pdo) { fwrite(STDERR, "NO_DB\n"); exit(1); }

$obsId = (int) $pdo->query("SELECT id FROM observatories WHERE slug='genero' LIMIT 1")->fetchColumn();
if ($obsId < 1) { fwrite(STDERR, "NO_OBS_GENERO\n"); exit(1); }

// Localizar la pestaña padre "informacion"
$st = $pdo->prepare('SELECT id FROM cms_microsite_sections WHERE observatory_id=? AND parent_id IS NULL AND section_key=? LIMIT 1');
$st->execute([$obsId, 'informacion']);
$parentId = (int) $st->fetchColumn();
if ($parentId < 1) { fwrite(STDERR, "NO_PARENT_INFORMACION\n"); exit(1); }

$concepts = [
    'sexo' => [
        'image' => 'assets/svg/img-genero/conceptos/sexo.png',
        'body'  => '<p>Características <strong>biológicas</strong> (anatómicas, hormonales, cromosómicas) con las que nacen las personas. No determina por sí mismo la identidad o la expresión de género.</p>'
                 . '<p class="mini-tip"><i class="fa-solid fa-circle-info"></i> El sexo describe el cuerpo. La identidad describe la vivencia interna.</p>',
    ],
    'genero' => [
        'image' => 'assets/svg/img-genero/conceptos/genero.png',
        'body'  => '<p>Construcción <strong>sociocultural</strong> de roles, expectativas y normas asociadas a ser mujer, hombre u otras identidades. Afecta el acceso a recursos, poder y reconocimiento.</p>'
                 . '<p class="mini-tip"><i class="fa-solid fa-circle-info"></i> El género se aprende y varía entre culturas y épocas.</p>',
    ],
    'identidad' => [
        'image' => 'assets/svg/img-genero/conceptos/identidad.png',
        'body'  => '<p>Vivencia <strong>interna y personal</strong> del género que cada persona siente profundamente. Puede corresponder o no con el sexo asignado al nacer.</p>'
                 . '<ul><li>Cisgénero — su identidad coincide con el sexo asignado.</li><li>Transgénero — su identidad no coincide con el sexo asignado.</li><li>No binarie — no se identifica exclusivamente con hombre/mujer.</li></ul>',
    ],
    'expresion' => [
        'image' => 'assets/svg/img-genero/conceptos/expresion.png',
        'body'  => '<p>Forma en que una persona <strong>manifiesta su género</strong> a través de vestimenta, conducta, voz o apariencia. No determina la orientación ni la identidad de género.</p>'
                 . '<p class="mini-tip"><i class="fa-solid fa-triangle-exclamation"></i> Una expresión de género diversa no implica una orientación sexual específica.</p>',
    ],
    'orientacion' => [
        'image' => 'assets/svg/img-genero/conceptos/orientacion.png',
        'body'  => '<p>Atracción <strong>afectiva, emocional y/o sexual</strong> hacia otras personas (por ejemplo, hacia un género, varios o independiente del género).</p>'
                 . '<ul><li>Heterosexual — atracción hacia un género distinto.</li><li>Homosexual — atracción hacia el mismo género.</li><li>Bisexual — atracción hacia más de un género.</li><li>Asexual — sin atracción sexual o muy baja.</li></ul>',
    ],
    'masculinidades' => [
        'image' => 'assets/svg/img-genero/conceptos/masculinidades_y_feminidades.png',
        'body'  => '<p>Propuestas que <strong>cuestionan estereotipos tradicionales</strong> y promueven relaciones igualitarias, cuidado, corresponsabilidad y diversidad de maneras de ser hombre o mujer.</p>'
                 . '<p>Buscan transformar el machismo, la violencia y la rigidez de los roles para construir vínculos más libres y justos.</p>',
    ],
    'perspectiva' => [
        'image' => 'assets/svg/img-genero/conceptos/perspectivade%20genero.png',
        'body'  => '<p>Enfoque analítico que permite <strong>identificar las brechas y desigualdades</strong> que viven mujeres y hombres por razón de género, así como las relaciones de poder entre ellos.</p>'
                 . '<p>Se aplica al diseño de políticas, planes y programas para asegurar equidad en el acceso a derechos, recursos y oportunidades.</p>',
    ],
    'discriminacion' => [
        'image' => 'assets/svg/img-genero/conceptos/discriminacion.png',
        'body'  => '<p>Trato <strong>desigual o desfavorable</strong> basado en características como sexo, género, orientación o identidad. Puede ser directa, indirecta o estructural y vulnera derechos y oportunidades.</p>'
                 . '<ul><li><strong>Directa</strong> — trato distinto explícito por una característica.</li><li><strong>Indirecta</strong> — práctica neutra que afecta a un grupo desproporcionadamente.</li><li><strong>Estructural</strong> — normas e instituciones que perpetúan la desigualdad.</li></ul>',
    ],
    'interseccionalidad' => [
        'image' => 'assets/svg/img-genero/conceptos/interseccionalidad.png',
        'body'  => '<p>Enfoque que reconoce cómo se <strong>cruzan múltiples factores</strong> (género, clase, etnia, discapacidad, edad, territorio, etc.) generando ventajas o desventajas acumuladas.</p>'
                 . '<p class="mini-tip"><i class="fa-solid fa-circle-info"></i> Una mujer rural campesina con discapacidad enfrenta barreras distintas a las de una mujer urbana profesional. La interseccionalidad las hace visibles.</p>',
    ],
    'violencia' => [
        'image' => 'assets/svg/img-genero/conceptos/violencia.png',
        'body'  => '<p>Toda acción u omisión que cause <strong>daño físico, sexual, psicológico, económico o patrimonial</strong> a una persona por razón de su género. Incluye amenazas, coacción y privación arbitraria de la libertad.</p>'
                 . '<ul><li>Violencia física, psicológica, sexual, económica y patrimonial.</li><li>Puede darse en lo privado (pareja, familia) o en lo público (laboral, institucional, comunitario).</li></ul>'
                 . '<p class="callout is-primary"><strong>Líneas de orientación:</strong> 155 (mujeres) · 141 (NNA) · 123 (emergencias)</p>',
    ],
];

$updated = 0;
foreach ($concepts as $key => $data) {
    $st = $pdo->prepare('SELECT id FROM cms_microsite_sections WHERE observatory_id=? AND parent_id=? AND section_key=? LIMIT 1');
    $st->execute([$obsId, $parentId, $key]);
    $id = (int) $st->fetchColumn();
    if ($id < 1) {
        echo "  ⚠ sub-sección '$key' no encontrada en BD, omitida.\n";
        continue;
    }
    echo sprintf("  ✓ '%s' (#%d) → body %d bytes, imagen %s\n", $key, $id, strlen($data['body']), $data['image']);
    if (!$dryRun) {
        $pdo->prepare('UPDATE cms_microsite_sections SET body_html=?, image_url=? WHERE id=?')
            ->execute([$data['body'], $data['image'], $id]);
    }
    $updated++;
}

echo $dryRun ? "\n[DRY-RUN] $updated conceptos pendientes de actualizar.\n" : "\n[OK] $updated conceptos actualizados.\n";
