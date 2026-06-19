<?php

/**
 * Crea (si no existen) las secciones CMS tipo widget del microsite de género:
 *  - widget-carrusel    : carrusel de imágenes (paquete gráfico Igualdad de Género del Caribe)
 *  - widget-integrantes : carrusel de logos de entidades integrantes
 *
 * Son secciones de cms_microsite_sections con key 'widget-*': no se muestran
 * como pestañas; alimentan widgets del microsite y se administran desde
 * CMS → Pestañas (cms/tabs.php) como cualquier otra sección.
 */
require_once __DIR__ . '/../website/config/database.php';

$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "Sin conexión a BD\n");
    exit(1);
}

$oid = (int) $pdo->query("SELECT id FROM observatories WHERE slug = 'genero'")->fetchColumn();
if (!$oid) {
    fwrite(STDERR, "Observatorio 'genero' no encontrado\n");
    exit(1);
}

function ensure_root(PDO $pdo, int $oid, string $key, string $title, string $subtitle): int
{
    $st = $pdo->prepare('SELECT id FROM cms_microsite_sections WHERE observatory_id = ? AND section_key = ? AND parent_id IS NULL');
    $st->execute([$oid, $key]);
    $id = $st->fetchColumn();
    if ($id) {
        echo "Root {$key} ya existe (id={$id})\n";
        return (int) $id;
    }
    $st = $pdo->prepare('INSERT INTO cms_microsite_sections (observatory_id, parent_id, section_key, title, subtitle, layout, sort_order, is_active) VALUES (?, NULL, ?, ?, ?, "cards", 900, 1)');
    $st->execute([$oid, $key, $title, $subtitle]);
    $id = (int) $pdo->lastInsertId();
    echo "Root {$key} creado (id={$id})\n";
    return $id;
}

function ensure_child(PDO $pdo, int $oid, int $parentId, string $key, string $title, ?string $imageUrl, int $order): void
{
    $st = $pdo->prepare('SELECT id FROM cms_microsite_sections WHERE observatory_id = ? AND parent_id = ? AND section_key = ?');
    $st->execute([$oid, $parentId, $key]);
    if ($st->fetchColumn()) {
        return;
    }
    $st = $pdo->prepare('INSERT INTO cms_microsite_sections (observatory_id, parent_id, section_key, title, image_url, layout, sort_order, is_active) VALUES (?, ?, ?, ?, ?, "standard", ?, 1)');
    $st->execute([$oid, $parentId, $key, $title, $imageUrl, $order]);
    echo "  + {$title}\n";
}

/* ── Widget: carrusel de imágenes ───────────────────────────────────────── */
$carruselId = ensure_root($pdo, $oid, 'widget-carrusel', 'Widget: Carrusel de imágenes',
    'Imágenes del carrusel del micrositio (no es pestaña). Agregue/quite hijos con su imagen para editar el carrusel.');

$grupos = [
    'autonomia-economica' => ['label' => 'Autonomía económica', 'count' => 6],
    'autonomia-fisica' => ['label' => 'Autonomía física', 'count' => 5],
    'autonomia-toma-decisiones' => ['label' => 'Autonomía en la toma de decisiones', 'count' => 5],
    'participacion-politica-alc' => ['label' => 'Participación política de las mujeres en ALC', 'count' => 2],
];
$orden = 10;
foreach ($grupos as $slug => $g) {
    $files = glob(__DIR__ . '/../website/uploads/cms/2026/06/genero-caribe-' . $slug . '-*.jpg');
    natsort($files);
    $i = 1;
    foreach ($files as $f) {
        $rel = 'uploads/cms/2026/06/' . basename($f);
        ensure_child($pdo, $oid, $carruselId, $slug . '-' . $i, $g['label'] . ' (' . $i . ')', $rel, $orden);
        $orden += 10;
        $i++;
    }
}

/* ── Widget: integrantes (logos) ────────────────────────────────────────── */
$integrantesId = ensure_root($pdo, $oid, 'widget-integrantes', 'Widget: Integrantes (logos)',
    'Entidades que participan en el observatorio (carrusel de logos antes del pie de página). Edite los hijos para cambiar logos.');

$entidades = [
    ['planeacion', 'Secretaría de Planeación de Boyacá', 'assets/svg/carruselGenero/SecPlaneacion.png'],
    ['integracion-social', 'Secretaría de Integración Social de Boyacá', 'assets/svg/carruselGenero/SecIntegracion.png'],
    ['salud', 'Secretaría de Salud de Boyacá', 'assets/svg/carruselGenero/SecSalud.png'],
    ['gobierno', 'Secretaría de Gobierno de Boyacá', 'assets/svg/carruselGenero/SecGobierno.png'],
    ['fiscalia', 'Fiscalía General de la Nación', 'assets/svg/carruselGenero/Fiscalia.png'],
    ['medicina-legal', 'Instituto Nacional de Medicina Legal y Ciencias Forenses', 'assets/svg/carruselGenero/MedicinaLegal.png'],
    ['uptc', 'Universidad Pedagógica y Tecnológica de Colombia', 'assets/svg/carruselGenero/UPTC.png'],
    ['defensoria', 'Defensoría del Pueblo', null],
    ['icbf', 'Instituto Colombiano de Bienestar Familiar (ICBF)', null],
];
$orden = 10;
foreach ($entidades as [$key, $nombre, $logo]) {
    ensure_child($pdo, $oid, $integrantesId, $key, $nombre, $logo, $orden);
    $orden += 10;
}

echo "Listo.\n";
