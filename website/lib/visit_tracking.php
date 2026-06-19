<?php

/**
 * Contadores de visitantes únicos (cookie anónima) y constantes de la encuesta opcional.
 */

const CMS_VISITOR_COOKIE = 'obs_unique_vid';
const CMS_VISITOR_COOKIE_LIFETIME = 365 * 24 * 60 * 60;

function cms_visitor_cookie_path(): string
{
    $override = getenv('OBS_VISITOR_COOKIE_PATH');
    if (is_string($override) && $override !== '') {
        return $override;
    }
    // Por defecto '/' para que la misma cookie sirva en todo el host (localhost, subcarpetas XAMPP).
    // Si en un mismo dominio hay varias apps PHP y no deben compartir visitante, defina OBS_VISITOR_COOKIE_PATH=/su/carpeta/
    return '/';
}

/**
 * ID estable por navegador (32 hex). Define cookie en la primera petición.
 */
function cms_ensure_unique_visitor_id(): string
{
    $existing = $_COOKIE[CMS_VISITOR_COOKIE] ?? '';
    if (is_string($existing) && preg_match('/^[a-f0-9]{32}$/', $existing)) {
        return $existing;
    }

    $id = bin2hex(random_bytes(16));
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    setcookie(CMS_VISITOR_COOKIE, $id, [
        'expires' => time() + CMS_VISITOR_COOKIE_LIFETIME,
        'path' => cms_visitor_cookie_path(),
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[CMS_VISITOR_COOKIE] = $id;

    return $id;
}

function cms_survey_age_ranges(): array
{
    return [
        '0-17' => 'Menor de 18 años',
        '18-24' => '18 a 24 años',
        '25-34' => '25 a 34 años',
        '35-44' => '35 a 44 años',
        '45-54' => '45 a 54 años',
        '55-64' => '55 a 64 años',
        '65+' => '65 años o más',
    ];
}

function cms_survey_sectors(): array
{
    return [
        'estado' => 'Estado',
        'academico_docente' => 'Académico — docente',
        'academico_estudiante' => 'Académico — estudiante',
        'sociedad_civil' => 'Sociedad civil',
        'empresa' => 'Empresa',
    ];
}

/** Tercera pregunta: frecuencia de uso (la encuesta es opcional en conjunto). */
function cms_survey_visit_frequency(): array
{
    return [
        'primera_vez' => 'Es mi primera visita',
        'ocasional' => 'Ocasionalmente',
        'frecuente' => 'Con frecuencia',
        'prefiero_no_decir' => 'Prefiero no decir',
    ];
}

/**
 * Registra un visitante único para esta página (sin incrementar en recargas del mismo navegador).
 * Devuelve el total de visitantes únicos para page_key o null si falla la BD.
 */
function cms_track_page_visit(?PDO $pdo, string $pageKey): ?int
{
    if (!$pdo || $pageKey === '') {
        return null;
    }
    if (strlen($pageKey) > 64) {
        $pageKey = substr($pageKey, 0, 64);
    }

    $visitorId = cms_ensure_unique_visitor_id();
    if (strlen($visitorId) !== 32) {
        return null;
    }

    try {
        $ins = $pdo->prepare(
            'INSERT IGNORE INTO cms_unique_visitors (page_key, visitor_id) VALUES (?, ?)'
        );
        $ins->execute([$pageKey, $visitorId]);

        // Solo la PRIMERA vez de este navegador en esta página: geolocalizar.
        if ($ins->rowCount() === 1) {
            cms_track_visit_geo($pdo, $pageKey, $visitorId);
        }

        return cms_get_visit_count($pdo, $pageKey);
    } catch (Throwable $e) {
        return null;
    }
}

/** Geolocaliza (aproximada por IP) la fila recién creada; tolerante a fallos. */
function cms_track_visit_geo(PDO $pdo, string $pageKey, string $visitorId): void
{
    $geoFile = __DIR__ . '/geo_lookup.php';
    if (!is_readable($geoFile)) {
        return;
    }
    require_once $geoFile;
    try {
        $geo = cms_geo_lookup($pdo, cms_client_ip());
        if (!$geo) {
            return;
        }
        $upd = $pdo->prepare(
            'UPDATE cms_unique_visitors SET country=?, region=?, city=?, lat=?, lng=? WHERE page_key=? AND visitor_id=?'
        );
        $upd->execute([$geo['country'], $geo['region'], $geo['city'], $geo['lat'], $geo['lng'], $pageKey, $visitorId]);
    } catch (Throwable $e) {
        // no bloquear el render por la geolocalización
    }
}

function cms_get_visit_count(?PDO $pdo, string $pageKey): int
{
    if (!$pdo || $pageKey === '') {
        return 0;
    }
    if (strlen($pageKey) > 64) {
        $pageKey = substr($pageKey, 0, 64);
    }
    try {
        $sel = $pdo->prepare(
            'SELECT COUNT(*) FROM cms_unique_visitors WHERE page_key = ?'
        );
        $sel->execute([$pageKey]);
        $n = $sel->fetchColumn();

        return $n !== false ? (int) $n : 0;
    } catch (Throwable $e) {
        return 0;
    }
}

function cms_portal_visit_page_key(): string
{
    return 'portal';
}

function cms_microsite_visit_page_key(string $slug): string
{
    return 'microsite_' . $slug;
}
