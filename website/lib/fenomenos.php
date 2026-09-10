<?php

/**
 * Soporte de la pestaña "Fenómenos en Boyacá" del Observatorio Ambiental.
 *
 *  - Estado del ENSO (El Niño / La Niña) desde el índice ONI de la NOAA,
 *    con caché en disco y respaldo si no hay salida a internet.
 *  - Focos de calor activos: NASA FIRMS (requiere MAP_KEY gratuita) y, sin
 *    clave, eventos abiertos de NASA EONET.
 *  - Reportes ciudadanos y directorio de bomberos (base de datos).
 *
 * Ninguna función lanza excepciones hacia la página: si una fuente externa
 * falla, se devuelve lo que haya en caché y un aviso, para que el mapa
 * siempre cargue con los datos históricos locales.
 */

require_once __DIR__ . '/../config/database.php';

const FEN_CACHE_DIR = __DIR__ . '/../data/fenomenos/cache';

/** Descarga con caché en disco. $ttl en segundos. */
function fen_http_cache(string $url, string $clave, int $ttl, int $timeout = 8): ?string
{
    if (!is_dir(FEN_CACHE_DIR)) {
        @mkdir(FEN_CACHE_DIR, 0775, true);
    }
    $archivo = FEN_CACHE_DIR . '/' . preg_replace('/[^a-z0-9_.-]/i', '_', $clave);
    if (is_file($archivo) && (time() - (int) @filemtime($archivo)) < $ttl) {
        $c = @file_get_contents($archivo);
        if ($c !== false && $c !== '') {
            return $c;
        }
    }
    $ctx = stream_context_create(['http' => [
        'timeout' => $timeout,
        'header' => "User-Agent: RedObservatoriosBoyaca/1.0 (observatorios.boyaca.gov.co)\r\n",
    ], 'ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);
    $datos = @file_get_contents($url, false, $ctx);
    if ($datos === false || $datos === '') {
        // Sin red: se sirve la copia vieja si existe.
        return is_file($archivo) ? (@file_get_contents($archivo) ?: null) : null;
    }
    @file_put_contents($archivo, $datos);

    return $datos;
}

/**
 * Estado actual del ENSO a partir del índice ONI (Oceanic Niño Index) de la
 * NOAA. Convenio oficial: ONI >= +0,5 °C → El Niño; <= -0,5 °C → La Niña.
 *
 * @return array{estado:string,fase:string,oni:?float,periodo:?string,serie:array,fuente:string,actualizado:?string}
 */
function fen_estado_enso(): array
{
    $txt = fen_http_cache(
        'https://www.cpc.ncep.noaa.gov/data/indices/oni.ascii.txt',
        'oni.txt',
        21600 // 6 horas
    );
    $serie = [];
    if ($txt) {
        foreach (preg_split('/\r?\n/', trim($txt)) as $linea) {
            $p = preg_split('/\s+/', trim($linea));
            if (count($p) < 4 || !ctype_digit((string) $p[1])) {
                continue;
            }
            $serie[] = ['periodo' => $p[0] . ' ' . $p[1], 'anio' => (int) $p[1], 'oni' => (float) $p[3]];
        }
    }
    // Promedio anual del ONI (para relacionar cada año con su fenómeno).
    $anual = [];
    foreach ($serie as $r) {
        $anual[$r['anio']][] = $r['oni'];
    }
    foreach ($anual as $a => $vals) {
        $anual[$a] = round(array_sum($vals) / max(1, count($vals)), 2);
    }
    $anual = array_slice($anual, -12, null, true);

    $serie = array_slice($serie, -36); // tres años
    $ultimo = $serie ? end($serie) : null;
    $oni = $ultimo['oni'] ?? null;

    $fase = 'neutral';
    $estado = 'Condiciones neutrales';
    if ($oni !== null) {
        if ($oni >= 0.5) {
            $fase = 'nino';
            $intensidad = $oni >= 2.0 ? 'muy fuerte' : ($oni >= 1.5 ? 'fuerte' : ($oni >= 1.0 ? 'moderado' : 'débil'));
            $estado = 'Fenómeno de El Niño ' . $intensidad;
        } elseif ($oni <= -0.5) {
            $fase = 'nina';
            $intensidad = $oni <= -2.0 ? 'muy fuerte' : ($oni <= -1.5 ? 'fuerte' : ($oni <= -1.0 ? 'moderado' : 'débil'));
            $estado = 'Fenómeno de La Niña ' . $intensidad;
        }
    }

    return [
        'estado' => $estado,
        'fase' => $fase,
        'oni' => $oni,
        'periodo' => $ultimo['periodo'] ?? null,
        'serie' => $serie,
        'anual' => $anual,
        'fuente' => 'NOAA · Climate Prediction Center, índice ONI (región Niño 3.4)',
        'url' => 'https://www.cpc.ncep.noaa.gov/products/analysis_monitoring/ensostuff/ONI_v5.php',
        'actualizado' => $txt ? date('c', (int) @filemtime(FEN_CACHE_DIR . '/oni.txt')) : null,
    ];
}


/** Anillos exteriores de los municipios de Boyacá (archivo compacto generado). */
function fen_poligonos_boyaca(): array
{
    static $polis = null;
    if ($polis !== null) {
        return $polis;
    }
    $p = __DIR__ . '/../data/fenomenos/boyaca_poligonos.json';
    $d = is_file($p) ? json_decode((string) @file_get_contents($p), true) : null;

    return $polis = is_array($d) ? $d : [];
}

/** ¿El punto cae dentro de Boyacá? (ray casting sobre los anillos). */
function fen_en_boyaca(float $lon, float $lat): bool
{
    foreach (fen_poligonos_boyaca() as $anillo) {
        $dentro = false;
        $n = count($anillo);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $anillo[$i][0]; $yi = $anillo[$i][1];
            $xj = $anillo[$j][0]; $yj = $anillo[$j][1];
            if ((($yi > $lat) !== ($yj > $lat))
                && ($lon < ($xj - $xi) * ($lat - $yi) / (($yj - $yi) ?: 1e-12) + $xi)) {
                $dentro = !$dentro;
            }
        }
        if ($dentro) {
            return true;
        }
    }

    return false;
}

/** Lee un ajuste de la tabla app_settings (vacío si no existe o no hay BD). */
function fen_ajuste(string $clave): string
{
    static $cache = [];
    if (array_key_exists($clave, $cache)) {
        return $cache[$clave];
    }
    $valor = '';
    try {
        $pdo = cms_pdo();
        if ($pdo) {
            $st = $pdo->prepare('SELECT valor FROM app_settings WHERE clave = ?');
            $st->execute([$clave]);
            $valor = trim((string) ($st->fetchColumn() ?: ''));
        }
    } catch (Throwable $e) {
        $valor = '';
    }

    return $cache[$clave] = $valor;
}

/**
 * Clave de NASA FIRMS. Orden de búsqueda: ajuste guardado en el CMS,
 * archivo config/fenomenos.local.php (no versionado) y variable de entorno.
 * Así la clave nunca viaja en el repositorio, que es público.
 */
function fen_firms_key(): string
{
    $key = fen_ajuste('firms_map_key');
    if ($key !== '') {
        return $key;
    }
    $cfg = @include __DIR__ . '/../config/fenomenos.php';
    $key = is_array($cfg) ? trim((string) ($cfg['firms_map_key'] ?? '')) : '';
    if ($key !== '') {
        return $key;
    }

    return trim((string) (getenv('OBS_FIRMS_MAP_KEY') ?: ''));
}

/**
 * Focos de calor activos sobre Boyacá.
 * Con MAP_KEY de NASA FIRMS devuelve detecciones satelitales de los últimos
 * días; sin clave, cae a los eventos abiertos de NASA EONET.
 */
function fen_focos_calor(int $dias = 3): array
{
    $key = fen_firms_key();
    $bbox = '-74.85,4.35,-71.85,7.25'; // Boyacá con margen
    $focos = [];
    $fuente = 'NASA EONET';
    $aviso = '';

    if ($key !== '') {
        $dias = max(1, min(10, $dias));
        $csv = fen_http_cache(
            "https://firms.modaps.eosdis.nasa.gov/api/area/csv/{$key}/VIIRS_NOAA20_NRT/{$bbox}/{$dias}",
            "firms_{$dias}.csv",
            1800, // 30 minutos
            12
        );
        if ($csv && stripos($csv, 'latitude') !== false) {
            $lineas = preg_split('/\r?\n/', trim($csv));
            $cab = str_getcsv((string) array_shift($lineas));
            $ix = array_flip($cab);
            foreach ($lineas as $l) {
                if (trim($l) === '') {
                    continue;
                }
                $c = str_getcsv($l);
                $la = (float) ($c[$ix['latitude']] ?? 0);
                $lo = (float) ($c[$ix['longitude']] ?? 0);
                $focos[] = [
                    'lat' => $la,
                    'lon' => $lo,
                    'fecha' => (string) ($c[$ix['acq_date']] ?? ''),
                    'hora' => (string) ($c[$ix['acq_time']] ?? ''),
                    'confianza' => (string) ($c[$ix['confidence']] ?? ''),
                    'potencia' => (float) ($c[$ix['frp']] ?? 0),
                    'en_boyaca' => fen_en_boyaca($lo, $la),
                ];
            }
            $fuente = 'NASA FIRMS · VIIRS NOAA-20 (tiempo casi real)';
            $enBoyaca = count(array_filter($focos, static fn ($f) => !empty($f['en_boyaca'])));
            $vecinos = count($focos) - $enBoyaca;
        } else {
            $aviso = 'No fue posible consultar NASA FIRMS; se muestran los eventos de NASA EONET.';
        }
    } else {
        $aviso = 'Sin clave de NASA FIRMS: se muestran únicamente los eventos abiertos de NASA EONET. La clave se carga en el CMS, en Fenómenos → Configuración.';
    }

    if ($focos === []) {
        $json = fen_http_cache(
            'https://eonet.gsfc.nasa.gov/api/v3/events/geojson?category=wildfires&status=open&bbox=-74.85,7.25,-71.85,4.35',
            'eonet.json',
            3600
        );
        $g = $json ? json_decode($json, true) : null;
        foreach (($g['features'] ?? []) as $f) {
            $coord = $f['geometry']['coordinates'] ?? null;
            if (!is_array($coord) || count($coord) < 2) {
                continue;
            }
            $focos[] = [
                'lat' => (float) $coord[1],
                'lon' => (float) $coord[0],
                'fecha' => substr((string) ($f['properties']['date'] ?? ''), 0, 10),
                'hora' => '',
                'confianza' => 'evento EONET',
                'potencia' => 0,
                'titulo' => (string) ($f['properties']['title'] ?? ''),
                'en_boyaca' => fen_en_boyaca((float) $coord[0], (float) $coord[1]),
            ];
        }
    }

    $enBoyaca = $enBoyaca ?? count(array_filter($focos, static fn ($f) => !empty($f['en_boyaca'])));
    $vecinos = $vecinos ?? (count($focos) - $enBoyaca);

    return ['focos' => $focos, 'fuente' => $fuente, 'aviso' => $aviso, 'dias' => $dias,
            'en_boyaca' => $enBoyaca, 'vecinos' => $vecinos];
}

/** Reportes ciudadanos publicables (verificados y pendientes recientes). */
function fen_reportes(?PDO $pdo, int $limite = 300): array
{
    if (!$pdo) {
        return [];
    }
    try {
        $st = $pdo->prepare(
            'SELECT id, tipo, municipio, lat, lon, descripcion, referencia, estado, creado_en
               FROM env_reportes_ciudadanos
              WHERE estado IN ("pendiente","verificado","atendido")
                AND creado_en >= DATE_SUB(NOW(), INTERVAL 120 DAY)
              ORDER BY creado_en DESC
              LIMIT ' . (int) $limite
        );
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        return [];
    }
}

/** Directorio de bomberos activo. */
function fen_bomberos(?PDO $pdo): array
{
    if (!$pdo) {
        return [];
    }
    try {
        $st = $pdo->query(
            'SELECT id, nombre, municipio, municipio_dane, provincia, telefono, celular,
                    direccion, correo, tipo, lat, lon, verificado
               FROM env_bomberos
              WHERE activo = 1
              ORDER BY (municipio = "Todo el departamento") DESC, municipio ASC, nombre ASC'
        );

        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        return [];
    }
}

/** Datos históricos de emergencias por municipio (archivo generado por script). */
function fen_historico(): array
{
    $p = __DIR__ . '/../data/fenomenos/emergencias.json';
    if (!is_file($p)) {
        return [];
    }
    $d = json_decode((string) @file_get_contents($p), true);

    return is_array($d) ? $d : [];
}

/** Registra un reporte ciudadano. Devuelve [ok, mensaje]. */
function fen_guardar_reporte(?PDO $pdo, array $datos, string $ip): array
{
    if (!$pdo) {
        return [false, 'El servicio de reportes no está disponible en este momento.'];
    }
    $lat = (float) ($datos['lat'] ?? 0);
    $lon = (float) ($datos['lon'] ?? 0);
    // Boyacá y su entorno inmediato.
    if ($lat < 4.3 || $lat > 7.3 || $lon < -75.0 || $lon > -71.7) {
        return [false, 'La ubicación seleccionada está fuera del departamento de Boyacá.'];
    }
    $tipo = (string) ($datos['tipo'] ?? 'incendio');
    $permitidos = ['incendio', 'inundacion', 'deslizamiento', 'vendaval', 'desabastecimiento', 'otro'];
    if (!in_array($tipo, $permitidos, true)) {
        $tipo = 'otro';
    }
    $desc = trim(mb_substr((string) ($datos['descripcion'] ?? ''), 0, 800));
    if (mb_strlen($desc) < 10) {
        return [false, 'Describa brevemente lo que está ocurriendo (al menos 10 caracteres).'];
    }
    $hash = hash('sha256', $ip . '|observatorios-boyaca');

    try {
        // Límite simple: 5 reportes por hora desde la misma conexión.
        $st = $pdo->prepare('SELECT COUNT(*) FROM env_reportes_ciudadanos WHERE ip_hash = ? AND creado_en >= DATE_SUB(NOW(), INTERVAL 1 HOUR)');
        $st->execute([$hash]);
        if ((int) $st->fetchColumn() >= 5) {
            return [false, 'Ya registró varios reportes en la última hora. Intente más tarde.'];
        }
        $st = $pdo->prepare(
            'INSERT INTO env_reportes_ciudadanos
                (tipo, municipio_dane, municipio, lat, lon, descripcion, referencia, contacto, ip_hash)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([
            $tipo,
            substr(preg_replace('/\D/', '', (string) ($datos['municipio_dane'] ?? '')), 0, 5) ?: null,
            mb_substr(trim((string) ($datos['municipio'] ?? '')), 0, 120) ?: null,
            $lat, $lon, $desc,
            mb_substr(trim((string) ($datos['referencia'] ?? '')), 0, 200) ?: null,
            mb_substr(trim((string) ($datos['contacto'] ?? '')), 0, 120) ?: null,
            $hash,
        ]);

        return [true, 'Reporte recibido. Será revisado por el equipo de gestión del riesgo.'];
    } catch (Throwable $e) {
        return [false, 'No fue posible guardar el reporte.'];
    }
}

/** Guarda o borra un ajuste. Devuelve true si quedó almacenado. */
function fen_guardar_ajuste(?PDO $pdo, string $clave, string $valor): bool
{
    if (!$pdo) {
        return false;
    }
    try {
        $st = $pdo->prepare(
            'INSERT INTO app_settings (clave, valor) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
        );

        return $st->execute([$clave, $valor === '' ? null : $valor]);
    } catch (Throwable $e) {
        return false;
    }
}
