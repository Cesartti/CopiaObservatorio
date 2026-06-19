<?php
/**
 * Helpers para leer y administrar el árbol de pestañas y sub-secciones del
 * micrositio público (observatorio.php). Apoya la tabla cms_microsite_sections
 * creada en migrations/013_microsite_sections.sql.
 *
 * Convención:
 *   - Pestaña raíz: parent_id IS NULL
 *   - Sub-sección (chip/accordion item/card): parent_id = id de la pestaña raíz
 *
 * El consumidor público sólo carga lo activo; el CMS muestra todo.
 */

if (!function_exists('cms_pdo')) {
    require_once __DIR__ . '/../config/database.php';
}

/**
 * Devuelve el id numérico del observatorio por slug, o null si no existe.
 */
function cms_obs_id_by_slug(PDO $pdo, string $slug): ?int
{
    static $cache = [];
    if (array_key_exists($slug, $cache)) {
        return $cache[$slug];
    }
    try {
        $st = $pdo->prepare('SELECT id FROM observatories WHERE slug = ? LIMIT 1');
        $st->execute([$slug]);
        $id = $st->fetchColumn();
        $cache[$slug] = ($id === false || $id === null) ? null : (int) $id;
    } catch (Throwable $e) {
        $cache[$slug] = null;
    }
    return $cache[$slug];
}

/**
 * Devuelve el árbol de pestañas (raíces + hijos) para un observatorio.
 *
 * Estructura de cada nodo raíz:
 *   [
 *     'id' => int, 'section_key' => string, 'title' => string,
 *     'subtitle' => ?string, 'body_html' => ?string, 'layout' => string,
 *     'icon' => ?string, 'image_url' => ?string,
 *     'cta_label' => ?string, 'cta_url' => ?string,
 *     'sort_order' => int, 'is_active' => bool,
 *     'children' => [...nodos hijos en el mismo formato]
 *   ]
 *
 * @return list<array<string,mixed>>
 */
function cms_microsite_sections_tree(?PDO $pdo, string $obsSlug, bool $onlyActive = true): array
{
    if (!$pdo) return [];
    $oid = cms_obs_id_by_slug($pdo, $obsSlug);
    if (!$oid) return [];

    try {
        $sql = 'SELECT id, parent_id, section_key, title, subtitle, body_html, layout, icon,
                       image_url, cta_label, cta_url, sort_order, is_active
                FROM cms_microsite_sections
                WHERE observatory_id = ?';
        $params = [$oid];
        if ($onlyActive) {
            $sql .= ' AND is_active = 1';
        }
        $sql .= ' ORDER BY parent_id IS NULL DESC, sort_order ASC, id ASC';
        $st = $pdo->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
    if (!$rows) return [];

    $byId = [];
    foreach ($rows as $r) {
        $r['id']         = (int) $r['id'];
        $r['parent_id']  = $r['parent_id'] === null ? null : (int) $r['parent_id'];
        $r['sort_order'] = (int) $r['sort_order'];
        $r['is_active']  = (int) $r['is_active'] === 1;
        $r['children']   = [];
        $byId[$r['id']] = $r;
    }

    $roots = [];
    foreach ($byId as $id => $row) {
        if ($row['parent_id'] === null) {
            $roots[] = &$byId[$id];
        } else {
            $pid = $row['parent_id'];
            if (isset($byId[$pid])) {
                $byId[$pid]['children'][] = &$byId[$id];
            }
        }
    }

    // Ordenar hijos por sort_order
    foreach ($byId as $id => &$row) {
        if (!empty($row['children'])) {
            usort($row['children'], static function ($a, $b) {
                return ($a['sort_order'] <=> $b['sort_order']) ?: ($a['id'] <=> $b['id']);
            });
        }
    }
    unset($row);

    return $roots;
}

/**
 * Convierte el slug del observatorio + section_key en un anchor de ancla seguro.
 */
function cms_section_anchor(string $obsSlug, string $sectionKey): string
{
    $key = preg_replace('/[^a-z0-9_-]/i', '-', strtolower($sectionKey));
    return sprintf('sec-%s-%s', preg_replace('/[^a-z0-9_-]/i', '-', strtolower($obsSlug)), $key);
}

/**
 * Lista plana ordenada (para selects del CMS) con jerarquía visible.
 *
 * @return list<array{id:int,parent_id:?int,label:string,section_key:string,depth:int}>
 */
function cms_microsite_sections_flat(?PDO $pdo, int $obsId): array
{
    if (!$pdo) return [];
    try {
        $st = $pdo->prepare('SELECT id, parent_id, section_key, title, sort_order
                             FROM cms_microsite_sections
                             WHERE observatory_id = ?
                             ORDER BY parent_id IS NULL DESC, sort_order ASC, id ASC');
        $st->execute([$obsId]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
    $byParent = [];
    foreach ($rows as $r) {
        $byParent[$r['parent_id'] === null ? 0 : (int) $r['parent_id']][] = $r;
    }
    $out = [];
    $walker = function (int $parent, int $depth) use (&$walker, &$byParent, &$out): void {
        if (empty($byParent[$parent])) return;
        foreach ($byParent[$parent] as $r) {
            $out[] = [
                'id'          => (int) $r['id'],
                'parent_id'   => $r['parent_id'] === null ? null : (int) $r['parent_id'],
                'label'       => str_repeat('— ', $depth) . $r['title'],
                'section_key' => (string) $r['section_key'],
                'depth'       => $depth,
            ];
            $walker((int) $r['id'], $depth + 1);
        }
    };
    $walker(0, 0);
    return $out;
}

/**
 * ¿Hay al menos una pestaña raíz publicada para este observatorio?
 */
function cms_microsite_has_sections(?PDO $pdo, string $obsSlug): bool
{
    if (!$pdo) return false;
    $oid = cms_obs_id_by_slug($pdo, $obsSlug);
    if (!$oid) return false;
    try {
        $st = $pdo->prepare('SELECT 1 FROM cms_microsite_sections WHERE observatory_id = ? AND parent_id IS NULL AND is_active = 1 LIMIT 1');
        $st->execute([$oid]);
        return $st->fetchColumn() !== false;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Devuelve un id DOM seguro para usar como anchor de un tab-pane raíz.
 */
function cms_section_pane_id(string $obsSlug, array $rootNode): string
{
    $obs = preg_replace('/[^a-z0-9]/i', '', strtolower($obsSlug));
    $key = preg_replace('/[^a-z0-9_-]/i', '-', strtolower((string) $rootNode['section_key']));
    return 'p-' . $obs . '-' . $key;
}

/**
 * Devuelve el HTML del contenido (sin el wrapper del tab-pane) para una pestaña
 * raíz del árbol de secciones. Soporta 5 layouts:
 *
 *   - standard   → body_html del nodo, opcionalmente imagen destacada.
 *   - split      → imagen + body en 2 columnas.
 *   - chips      → barra de chips internos + paneles con el body de cada hijo.
 *   - accordion  → acordeón Bootstrap con cada hijo.
 *   - cards      → grilla de mini-cards (título + body) por hijo.
 */
/**
 * Inyecta loading="lazy" y decoding="async" en las <img> del HTML de sección
 * que aún no lo declaran. Las pestañas de los micrositios contienen imágenes
 * pesadas en paneles ocultos; sin carga diferida el navegador las descarga
 * todas al inicio (decenas de MB). No altera imágenes que ya definen loading.
 */
if (!function_exists('cms_lazyload_section_imgs')) {
    function cms_lazyload_section_imgs(string $html): string
    {
        if ($html === '' || stripos($html, '<img') === false) {
            return $html;
        }
        return (string) preg_replace_callback(
            '/<img\b(?![^>]*\bloading=)([^>]*)>/i',
            static function ($m) {
                return '<img loading="lazy" decoding="async"' . $m[1] . '>';
            },
            $html
        );
    }
}

function cms_render_microsite_section(array $node, string $obsSlug): string
{
    $title    = htmlspecialchars((string) $node['title']);
    $subtitle = (string) ($node['subtitle'] ?? '');
    $body     = cms_lazyload_section_imgs((string) ($node['body_html'] ?? ''));
    $layout   = (string) ($node['layout'] ?? 'standard');
    $icon     = (string) ($node['icon'] ?? '');
    $img      = (string) ($node['image_url'] ?? '');
    $children = $node['children'] ?? [];
    $paneId   = cms_section_pane_id($obsSlug, $node);
    $ctaLabel = trim((string) ($node['cta_label'] ?? ''));
    $ctaUrl   = trim((string) ($node['cta_url'] ?? ''));

    ob_start();
    ?>
    <article class="content-card ms-pane ms-layout-<?= htmlspecialchars($layout) ?>">
        <header class="ms-pane-head">
            <?php if ($icon !== ''): ?>
                <span class="ms-pane-icon"><i class="fa-solid <?= htmlspecialchars($icon) ?>"></i></span>
            <?php endif; ?>
            <div>
                <h3 class="mb-0"><?= $title ?></h3>
                <?php if ($subtitle !== ''): ?><p class="ms-pane-subtitle"><?= htmlspecialchars($subtitle) ?></p><?php endif; ?>
            </div>
        </header>

        <?php if ($layout === 'split' && $img !== ''): ?>
            <div class="ms-split">
                <div class="ms-split-media"><img src="<?= htmlspecialchars($img) ?>" alt=""></div>
                <div class="ms-split-body"><?= $body ?></div>
            </div>
        <?php else: ?>
            <?php if ($layout === 'standard' && $img !== ''): ?>
                <div class="ms-pane-banner"><img src="<?= htmlspecialchars($img) ?>" alt=""></div>
            <?php endif; ?>
            <?php if ($body !== ''): ?>
                <div class="ms-pane-body"><?= $body ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($layout === 'chips' && $children): ?>
            <ul class="nav nav-pills ms-chips" role="tablist">
                <?php foreach ($children as $i => $c):
                    $cid = $paneId . '-' . preg_replace('/[^a-z0-9_-]/i', '-', strtolower((string) $c['section_key']));
                ?>
                    <li class="nav-item">
                        <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#<?= htmlspecialchars($cid) ?>" type="button">
                            <?php if (!empty($c['icon'])): ?><i class="fa-solid <?= htmlspecialchars((string) $c['icon']) ?> me-1"></i><?php endif; ?>
                            <?= htmlspecialchars((string) $c['title']) ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="tab-content ms-chip-content">
                <?php foreach ($children as $i => $c):
                    $cid = $paneId . '-' . preg_replace('/[^a-z0-9_-]/i', '-', strtolower((string) $c['section_key']));
                    $cImg  = (string) ($c['image_url'] ?? '');
                    $cBody = cms_lazyload_section_imgs((string) ($c['body_html'] ?? ''));
                    $cLab  = trim((string) ($c['cta_label'] ?? ''));
                    $cUrl  = trim((string) ($c['cta_url'] ?? ''));
                ?>
                    <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="<?= htmlspecialchars($cid) ?>">
                        <div class="ms-chip-pane">
                            <?php if ($cImg !== ''): ?>
                                <div class="ms-chip-media"><img src="<?= htmlspecialchars($cImg) ?>" alt=""></div>
                            <?php endif; ?>
                            <div class="ms-chip-body">
                                <h4 class="mb-2"><?= htmlspecialchars((string) $c['title']) ?></h4>
                                <?php if (trim(strip_tags($cBody)) === ''): ?>
                                    <p class="text-muted small mb-0"><i class="fa-solid fa-pen-to-square me-1"></i> Contenido pendiente — edite desde el panel CMS.</p>
                                <?php else: ?>
                                    <?= $cBody ?>
                                <?php endif; ?>
                                <?php if ($cLab !== '' && $cUrl !== ''): ?>
                                    <a class="btn btn-sm btn-primary mt-2" href="<?= htmlspecialchars($cUrl) ?>"><?= htmlspecialchars($cLab) ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($layout === 'accordion' && $children): ?>
            <div class="accordion ms-accordion" id="acc-<?= htmlspecialchars($paneId) ?>">
                <?php foreach ($children as $i => $c):
                    $cid = $paneId . '-' . preg_replace('/[^a-z0-9_-]/i', '-', strtolower((string) $c['section_key']));
                ?>
                    <div class="accordion-item">
                        <h4 class="accordion-header">
                            <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= htmlspecialchars($cid) ?>">
                                <?php if (!empty($c['icon'])): ?><i class="fa-solid <?= htmlspecialchars((string) $c['icon']) ?> me-2"></i><?php endif; ?>
                                <?= htmlspecialchars((string) $c['title']) ?>
                            </button>
                        </h4>
                        <div id="<?= htmlspecialchars($cid) ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#acc-<?= htmlspecialchars($paneId) ?>">
                            <div class="accordion-body"><?= cms_lazyload_section_imgs((string) ($c['body_html'] ?? '')) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($layout === 'cards' && $children): ?>
            <div class="row g-3 ms-cards">
                <?php foreach ($children as $c): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="ms-card h-100">
                            <?php if (!empty($c['image_url'])): ?>
                                <div class="ms-card-media"><img src="<?= htmlspecialchars((string) $c['image_url']) ?>" alt=""></div>
                            <?php endif; ?>
                            <div class="ms-card-body">
                                <h5>
                                    <?php if (!empty($c['icon'])): ?><i class="fa-solid <?= htmlspecialchars((string) $c['icon']) ?> me-1"></i><?php endif; ?>
                                    <?= htmlspecialchars((string) $c['title']) ?>
                                </h5>
                                <div><?= cms_lazyload_section_imgs((string) ($c['body_html'] ?? '')) ?></div>
                                <?php if (!empty($c['cta_url']) && !empty($c['cta_label'])): ?>
                                    <a class="btn btn-sm btn-outline-primary mt-2" href="<?= htmlspecialchars((string) $c['cta_url']) ?>"><?= htmlspecialchars((string) $c['cta_label']) ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($ctaLabel !== '' && $ctaUrl !== ''): ?>
            <div class="ms-pane-cta">
                <a class="btn btn-primary" href="<?= htmlspecialchars($ctaUrl) ?>"><?= htmlspecialchars($ctaLabel) ?></a>
            </div>
        <?php endif; ?>
    </article>
    <?php
    return (string) ob_get_clean();
}
