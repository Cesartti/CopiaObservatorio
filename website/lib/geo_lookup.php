<?php

/**
 * Geolocalización aproximada por IP para la analítica de visitas/encuesta.
 *
 * - Resuelve país/región/ciudad/lat/lng a partir de la IP del visitante.
 * - Usa un proveedor gratuito sin clave (ip-api.com) y cachea por hash de IP
 *   en la tabla `cms_geo_cache` para no repetir llamadas ni exceder el límite.
 * - Privacidad: NO se guarda la IP en claro (solo su hash) ni se geolocalizan
 *   IPs privadas/reservadas (red interna).
 * - Tolerante a fallos: si no hay salida a Internet o el proveedor falla,
 *   devuelve null y la analítica sigue funcionando (sin punto en el mapa).
 */

/** IP real del cliente, considerando proxies/balanceadores delante de Apache. */
function cms_client_ip(): string
{
    $candidates = [];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Puede venir una lista "cliente, proxy1, proxy2"; tomamos la primera.
        foreach (explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']) as $part) {
            $candidates[] = trim($part);
        }
    }
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $candidates[] = trim((string) $_SERVER['HTTP_X_REAL_IP']);
    }
    $candidates[] = $_SERVER['REMOTE_ADDR'] ?? '';

    foreach ($candidates as $ip) {
        if ($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '';
}

/** ¿La IP es pública (geolocalizable)? Descarta privadas/reservadas. */
function cms_ip_is_public(string $ip): bool
{
    return (bool) filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    );
}

/**
 * Devuelve ['country','region','city','lat','lng'] para una IP, o null.
 * Cachea el resultado (incluido el "fallo") por hash de IP.
 */
function cms_geo_lookup(?PDO $pdo, string $ip): ?array
{
    if (!$pdo || $ip === '' || !cms_ip_is_public($ip)) {
        return null;
    }

    $hash = hash('sha256', $ip);

    // 1) Caché
    try {
        $sel = $pdo->prepare('SELECT status, country, region, city, lat, lng FROM cms_geo_cache WHERE ip_hash = ? LIMIT 1');
        $sel->execute([$hash]);
        $row = $sel->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if (($row['status'] ?? '') !== 'success') {
                return null;
            }
            return [
                'country' => $row['country'],
                'region' => $row['region'],
                'city' => $row['city'],
                'lat' => $row['lat'] !== null ? (float) $row['lat'] : null,
                'lng' => $row['lng'] !== null ? (float) $row['lng'] : null,
            ];
        }
    } catch (Throwable $e) {
        // Si la tabla no existe aún, no bloquear la página.
        return null;
    }

    // 2) Consulta al proveedor (sin clave). Campos mínimos, en español.
    $geo = cms_geo_fetch_remote($ip);

    // 3) Guardar en caché (éxito o fallo) para no repetir.
    try {
        $ins = $pdo->prepare(
            'INSERT INTO cms_geo_cache (ip_hash, status, country, region, city, lat, lng)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status=VALUES(status), country=VALUES(country),
               region=VALUES(region), city=VALUES(city), lat=VALUES(lat), lng=VALUES(lng), looked_up_at=NOW()'
        );
        $ins->execute([
            $hash,
            $geo ? 'success' : 'fail',
            $geo['country'] ?? null,
            $geo['region'] ?? null,
            $geo['city'] ?? null,
            $geo['lat'] ?? null,
            $geo['lng'] ?? null,
        ]);
    } catch (Throwable $e) {
        // ignorar fallo de caché
    }

    return $geo;
}

/** Llama al proveedor externo. Devuelve geo o null. Timeout corto. */
function cms_geo_fetch_remote(string $ip): ?array
{
    $url = 'http://ip-api.com/json/' . rawurlencode($ip)
        . '?fields=status,country,regionName,city,lat,lon&lang=es';

    $body = null;
    try {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_USERAGENT => 'ObservatoriosBoyaca/1.0',
            ]);
            $body = curl_exec($ch);
            curl_close($ch);
        } else {
            $ctx = stream_context_create(['http' => ['timeout' => 3, 'header' => "User-Agent: ObservatoriosBoyaca/1.0\r\n"]]);
            $body = @file_get_contents($url, false, $ctx);
        }
    } catch (Throwable $e) {
        return null;
    }

    if (!is_string($body) || $body === '') {
        return null;
    }
    $data = json_decode($body, true);
    if (!is_array($data) || ($data['status'] ?? '') !== 'success') {
        return null;
    }

    return [
        'country' => isset($data['country']) ? mb_substr((string) $data['country'], 0, 80) : null,
        'region' => isset($data['regionName']) ? mb_substr((string) $data['regionName'], 0, 120) : null,
        'city' => isset($data['city']) ? mb_substr((string) $data['city'], 0, 120) : null,
        'lat' => isset($data['lat']) ? (float) $data['lat'] : null,
        'lng' => isset($data['lon']) ? (float) $data['lon'] : null,
    ];
}
