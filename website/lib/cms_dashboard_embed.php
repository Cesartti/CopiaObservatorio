<?php

/**
 * Tablero CMS embebido en micrositios (observatorio.php → pestaña Tablero).
 * Convención: en `cms_dashboards` un tablero activo por observatorio con slug
 * definido en `observatories.php` → clave `cms_tablero_slug` (por defecto "principal").
 */

function cms_microsite_tablero_public_url(string $obsSlug, string $dashSlug): string
{
    return 'tableros-cms.php?' . http_build_query([
        'obs' => $obsSlug,
        'dash' => $dashSlug,
    ]);
}

/** URL relativa (desde /website/) para iframe; null si no existe el tablero en BD. */
function cms_microsite_tablero_embed_src(PDO $pdo, string $obsSlug, string $dashSlug = 'principal'): ?string
{
    $st = $pdo->prepare(
        'SELECT d.id FROM cms_dashboards d
         INNER JOIN observatories o ON o.id = d.observatory_id
         WHERE o.slug = ? AND d.slug = ? AND d.is_active = 1
         LIMIT 1'
    );
    $st->execute([$obsSlug, $dashSlug]);
    if (!$st->fetchColumn()) {
        return null;
    }

    return 'tableros-cms.php?' . http_build_query([
        'obs' => $obsSlug,
        'dash' => $dashSlug,
        'embed' => '1',
    ]);
}
