<?php

/**
 * Crea la sección widget-carrusel (con la infografía general como única
 * diapositiva inicial) para los observatorios económico, social, ambiente y
 * CTI. Editable luego desde CMS → Pestañas, igual que el de género.
 * Idempotente.
 */
require_once __DIR__ . '/../website/config/database.php';

$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "Sin conexión a BD\n");
    exit(1);
}

$img = 'uploads/cms/2026/06/infografia-observatorio.jpg';
$slugs = ['economico', 'social', 'ambiente', 'cti'];

foreach ($slugs as $slug) {
    $st = $pdo->prepare('SELECT id FROM observatories WHERE slug = ?');
    $st->execute([$slug]);
    $oid = (int) $st->fetchColumn();
    if (!$oid) {
        echo "Observatorio {$slug} no encontrado, omitido\n";
        continue;
    }

    $st = $pdo->prepare('SELECT id FROM cms_microsite_sections WHERE observatory_id = ? AND section_key = ? AND parent_id IS NULL');
    $st->execute([$oid, 'widget-carrusel']);
    $rootId = $st->fetchColumn();
    if (!$rootId) {
        $st = $pdo->prepare('INSERT INTO cms_microsite_sections (observatory_id, parent_id, section_key, title, subtitle, layout, sort_order, is_active) VALUES (?, NULL, "widget-carrusel", "Widget: Carrusel de imágenes", "Imágenes del carrusel del micrositio (no es pestaña). Agregue/quite hijos con su imagen para editar el carrusel.", "cards", 900, 1)');
        $st->execute([$oid]);
        $rootId = (int) $pdo->lastInsertId();
        echo "{$slug}: root widget-carrusel creado (id={$rootId})\n";
    } else {
        echo "{$slug}: root widget-carrusel ya existía (id={$rootId})\n";
    }

    $st = $pdo->prepare('SELECT COUNT(*) FROM cms_microsite_sections WHERE parent_id = ?');
    $st->execute([$rootId]);
    if ((int) $st->fetchColumn() === 0) {
        $st = $pdo->prepare('INSERT INTO cms_microsite_sections (observatory_id, parent_id, section_key, title, image_url, layout, sort_order, is_active) VALUES (?, ?, "infografia-1", "Red de Observatorios de Boyacá", ?, "standard", 10, 1)');
        $st->execute([$oid, $rootId, $img]);
        echo "{$slug}:   + diapositiva infografía\n";
    }
}

echo "Listo.\n";
