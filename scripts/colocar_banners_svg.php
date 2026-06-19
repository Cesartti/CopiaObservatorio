<?php

/**
 * Coloca los banners definitivos (rasterizados de los SVG del paquete gráfico)
 * en los carruseles widget-carrusel:
 *  - economico / social: reemplaza la infografía temporal.
 *  - genero: agrega su banner como primera diapositiva.
 * Ambiente y CTI conservan la imagen temporal hasta que entreguen su SVG.
 */
require_once __DIR__ . '/../website/config/database.php';

$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "Sin conexión a BD\n");
    exit(1);
}

function obs_id(PDO $pdo, string $slug): int
{
    $st = $pdo->prepare('SELECT id FROM observatories WHERE slug = ?');
    $st->execute([$slug]);
    return (int) $st->fetchColumn();
}

function carrusel_root(PDO $pdo, int $oid): int
{
    $st = $pdo->prepare('SELECT id FROM cms_microsite_sections WHERE observatory_id = ? AND section_key = "widget-carrusel" AND parent_id IS NULL');
    $st->execute([$oid]);
    return (int) $st->fetchColumn();
}

/* Reemplazo en económico y social */
$reemplazos = [
    'economico' => ['img' => 'uploads/cms/2026/06/banner-economico.jpg', 'title' => 'Observatorio Económico'],
    'social' => ['img' => 'uploads/cms/2026/06/banner-social.jpg', 'title' => 'Observatorio Social'],
];
foreach ($reemplazos as $slug => $b) {
    $oid = obs_id($pdo, $slug);
    $rootId = carrusel_root($pdo, $oid);
    if (!$rootId) { echo "{$slug}: sin widget-carrusel, omitido\n"; continue; }
    $st = $pdo->prepare('UPDATE cms_microsite_sections SET image_url = ?, title = ? WHERE parent_id = ? AND section_key = "infografia-1"');
    $st->execute([$b['img'], $b['title'], $rootId]);
    echo "{$slug}: banner reemplazado -> {$b['img']} ({$st->rowCount()} fila)\n";
}

/* Género: banner como primera diapositiva (si no existe ya) */
$oid = obs_id($pdo, 'genero');
$rootId = carrusel_root($pdo, $oid);
if ($rootId) {
    $st = $pdo->prepare('SELECT COUNT(*) FROM cms_microsite_sections WHERE parent_id = ? AND section_key = "banner-genero"');
    $st->execute([$rootId]);
    if ((int) $st->fetchColumn() === 0) {
        $st = $pdo->prepare('INSERT INTO cms_microsite_sections (observatory_id, parent_id, section_key, title, image_url, layout, sort_order, is_active) VALUES (?, ?, "banner-genero", "Observatorio de Asuntos de Género", "uploads/cms/2026/06/banner-genero.jpg", "standard", 5, 1)');
        $st->execute([$oid, $rootId]);
        echo "genero: banner agregado como primera diapositiva\n";
    } else {
        echo "genero: el banner ya existía\n";
    }
}

echo "Listo.\n";
