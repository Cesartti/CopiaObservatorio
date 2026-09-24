<?php

/**
 * Estado del agua en Boyacá para la pestaña "Fenómenos en Boyacá".
 *
 * Los datos se toman del visor FEWS del IDEAM (Flood Early Warning System),
 * que publica sus capas como GeoJSON estático en
 *   https://fews.ideam.gov.co/visorfews/data/
 * y del operador del mercado eléctrico XM, que es la fuente del volumen de
 * los embalses (el propio IDEAM lo acredita así en su visor).
 *
 * Capas que se usan:
 *   ReporteTablaEmbalsesVolUtil.json  volumen útil de los embalses (fuente XM)
 *   ReporteTablaEstaciones.json       nivel de los ríos, con umbrales de alerta
 *   ReporteTablaEstacionesQ.json      caudal de los ríos
 *   ReporteTablaEstacionesPobs.json   precipitación observada
 *   Desabastecimiento.json            municipios con riesgo de desabastecimiento
 *
 * Nota sobre el certificado: fews.ideam.gov.co entrega la cadena TLS
 * incompleta (no envía el intermedio de Sectigo). Los navegadores lo resuelven
 * solos, pero cURL no. En vez de desactivar la verificación se acompaña el
 * intermedio en data/fenomenos/certs/ideam_ca_bundle.pem y se verifica contra
 * ese paquete.
 *
 * Ninguna función lanza excepciones: si la fuente falla se devuelve la copia
 * en caché y, si tampoco la hay, un arreglo vacío.
 */

require_once __DIR__ . '/fenomenos.php';

const AGUA_FEWS_BASE = 'https://fews.ideam.gov.co/visorfews/data/';
const AGUA_VISOR = 'https://fews.ideam.gov.co/visorfews/nacional';
const AGUA_CA = __DIR__ . '/../data/fenomenos/certs/ideam_ca_bundle.pem';

/** Ficha de cada fuente, para el bloque "de dónde salen estos datos". */
function agua_fuentes(): array
{
    return [
        [
            'nombre' => 'IDEAM · Visor FEWS Colombia',
            'detalle' => 'Nivel y caudal de los ríos, precipitación y riesgo de desabastecimiento.',
            'url' => AGUA_VISOR,
        ],
        [
            'nombre' => 'XM · Operador del Mercado Eléctrico',
            'detalle' => 'Volumen útil diario de los embalses del Sistema Interconectado Nacional. '
                . 'Es la fuente que el propio visor del IDEAM acredita para esta capa.',
            'url' => 'https://www.xm.com.co/',
        ],
        [
            'nombre' => 'NOAA STAR · Vegetation Health Product',
            'detalle' => 'Índice VHI de sequía agrícola, producto satelital semanal de 4 km. '
                . 'El portal descarga el mosaico global y promedia las celdas de cada municipio.',
            'url' => 'https://www.star.nesdis.noaa.gov/smcd/emb/vci/VH/index.php',
        ],
    ];
}

/**
 * Descarga una capa del visor FEWS con caché en disco.
 *
 * @param string $archivo nombre del GeoJSON dentro de /visorfews/data/
 * @param int    $ttl     segundos de vigencia de la caché
 */
function agua_fews(string $archivo, int $ttl = 3600): array
{
    if (!preg_match('/^[A-Za-z0-9_.-]+\.json$/', $archivo)) {
        return [];
    }
    if (!is_dir(FEN_CACHE_DIR)) {
        @mkdir(FEN_CACHE_DIR, 0775, true);
    }
    $cache = FEN_CACHE_DIR . '/fews_' . $archivo;
    if (is_file($cache) && (time() - (int) @filemtime($cache)) < $ttl) {
        $d = json_decode((string) @file_get_contents($cache), true);
        if (is_array($d)) {
            return $d;
        }
    }

    $crudo = agua_descargar(AGUA_FEWS_BASE . $archivo);
    if ($crudo !== null) {
        $d = json_decode($crudo, true);
        if (is_array($d) && !empty($d['features'])) {
            @file_put_contents($cache, $crudo);

            return $d;
        }
    }
    // Sin red o respuesta inválida: se sirve la copia vieja si existe.
    $d = is_file($cache) ? json_decode((string) @file_get_contents($cache), true) : null;

    return is_array($d) ? $d : [];
}

/** Descarga verificando el certificado contra el paquete propio. */
function agua_descargar(string $url): ?string
{
    $ca = is_file(AGUA_CA) ? AGUA_CA : null;

    if (function_exists('curl_init')) {
        $c = curl_init($url);
        $opt = [
            CURLOPT_RETURNTRANSFER => true,
            // Tiempos cortos a propósito: esto se ejecuta al pintar una página
            // pública, así que si el IDEAM se demora es preferible servir la
            // copia en caché antes que dejar al visitante esperando.
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'RedObservatoriosBoyaca/1.0 (observatorios.boyaca.gov.co)',
        ];
        if ($ca !== null) {
            $opt[CURLOPT_CAINFO] = $ca;
        }
        curl_setopt_array($c, $opt);
        $r = curl_exec($c);
        $ok = ($r !== false && curl_getinfo($c, CURLINFO_HTTP_CODE) === 200);
        curl_close($c);
        if ($ok) {
            return (string) $r;
        }

        return null;
    }

    $ssl = ['verify_peer' => true, 'verify_peer_name' => true];
    if ($ca !== null) {
        $ssl['cafile'] = $ca;
    }
    $ctx = stream_context_create([
        'http' => ['timeout' => 8, 'header' => "User-Agent: RedObservatoriosBoyaca/1.0\r\n"],
        'ssl' => $ssl,
    ]);
    $r = @file_get_contents($url, false, $ctx);

    return ($r === false || $r === '') ? null : $r;
}

/** Recorre los puntos de una capa y devuelve solo los que caen en Boyacá. */
function agua_puntos_boyaca(array $geojson): array
{
    $out = [];
    foreach (($geojson['features'] ?? []) as $f) {
        $p = $f['properties'] ?? null;
        if (!is_array($p) || !isset($p['lng'], $p['lat'])) {
            continue;
        }
        $lon = (float) $p['lng'];
        $lat = (float) $p['lat'];
        if ($lon === 0.0 || !fen_en_boyaca($lon, $lat)) {
            continue;
        }
        $out[] = $p;
    }

    return $out;
}

/**
 * Embalses. Devuelve los de Boyacá y, aparte, el total nacional para poder
 * comparar. El valor viene como fracción (0,93) y se expresa en porcentaje.
 */
function agua_embalses(): array
{
    $j = agua_fews('ReporteTablaEmbalsesVolUtil.json', 3600);
    $boyaca = [];
    $pais = [];
    $fecha = '';
    foreach (($j['features'] ?? []) as $f) {
        $p = $f['properties'] ?? [];
        if (!isset($p['lng'], $p['lat'])) {
            continue;
        }
        $v = $p['ultimovalor'] ?? null;
        $reg = [
            'id' => (string) ($p['id'] ?? ''),
            'nombre' => (string) ($p['nombre'] ?? ''),
            'lon' => (float) $p['lng'],
            'lat' => (float) $p['lat'],
            'region' => (string) ($p['region'] ?? ''),
            'pct' => ($v === null) ? null : round(((float) $v) * 100, 1),
            'fecha' => (string) ($p['fechaultimovalor'] ?? ''),
        ];
        if ($reg['fecha'] !== '' && $fecha === '') {
            $fecha = $reg['fecha'];
        }
        if ($reg['pct'] !== null) {
            $pais[] = $reg;
        }
        if (fen_en_boyaca($reg['lon'], $reg['lat'])) {
            $boyaca[] = $reg;
        }
    }
    usort($pais, static fn ($a, $b) => $b['pct'] <=> $a['pct']);

    $prom = null;
    if ($pais) {
        $prom = round(array_sum(array_column($pais, 'pct')) / count($pais), 1);
    }

    return ['boyaca' => $boyaca, 'pais' => $pais, 'promedio_pais' => $prom, 'fecha' => $fecha];
}

/**
 * Estaciones hidrológicas del IDEAM en Boyacá.
 *
 * @param string $tipo 'nivel', 'caudal' o 'lluvia'
 */
function agua_estaciones(string $tipo = 'nivel'): array
{
    $capas = [
        'nivel' => ['ReporteTablaEstaciones.json', 'ultimonivelobs', 'ultimonivelsen', 'm'],
        'caudal' => ['ReporteTablaEstacionesQ.json', 'ultimoqobs', 'ultimoqsen', 'm³/s'],
        'lluvia' => ['ReporteTablaEstacionesPobs.json', 'ultimodatoobs', 'ultimodatosen', 'mm'],
    ];
    if (!isset($capas[$tipo])) {
        return [];
    }
    [$archivo, $campoObs, $campoSen, $unidad] = $capas[$tipo];

    // Recortar el país a Boyacá exige probar cada estación contra los anillos
    // de los 123 municipios, así que el resultado ya filtrado se guarda aparte:
    // de lo contrario ese cálculo se repetiría en cada visita.
    $procesado = FEN_CACHE_DIR . '/agua_' . $tipo . '.json';
    if (is_file($procesado) && (time() - (int) @filemtime($procesado)) < 1800) {
        $d = json_decode((string) @file_get_contents($procesado), true);
        if (is_array($d)) {
            return $d;
        }
    }

    $out = [];
    foreach (agua_puntos_boyaca(agua_fews($archivo, 1800)) as $p) {
        $valor = $p[$campoObs] ?? $p[$campoSen] ?? null;
        $out[] = [
            'id' => (string) ($p['id'] ?? ''),
            'nombre' => trim(preg_replace('/\s*\[\d+\]\s*$/', '', (string) ($p['nombre'] ?? ''))),
            'lon' => (float) $p['lng'],
            'lat' => (float) $p['lat'],
            'municipio' => (string) ($p['municipio'] ?? ''),
            'corriente' => (string) ($p['corriente'] ?? ''),
            'subzona' => (string) ($p['subzona'] ?? ''),
            'valor' => ($valor === null || $valor === '') ? null : (float) $valor,
            'unidad' => $unidad,
            'estado' => agua_estado_normalizado((string) ($p['Estado'] ?? '')),
            'umbral_maximo' => isset($p['umaxhis']) ? (float) $p['umaxhis'] : null,
        ];
    }
    usort($out, static fn ($a, $b) => strcmp($a['municipio'], $b['municipio']));
    if ($out) {
        @file_put_contents($procesado, json_encode($out, JSON_UNESCAPED_UNICODE));
    }

    return $out;
}

/**
 * Normaliza el estado que reporta el IDEAM a las cuatro categorías que usa
 * el mapa. Las estaciones sin lectura reciente quedan como 'sin_dato'.
 */
function agua_estado_normalizado(string $estado): string
{
    $e = mb_strtolower(trim($estado));
    if ($e === '') {
        return 'sin_dato';
    }
    if (str_contains($e, 'roja') || str_contains($e, 'maximo') || str_contains($e, 'máximo')) {
        return 'roja';
    }
    if (str_contains($e, 'naranja')) {
        return 'naranja';
    }
    if (str_contains($e, 'amarilla')) {
        return 'amarilla';
    }
    if (str_contains($e, 'bajo')) {
        return 'bajos';
    }

    return 'normal';
}

/** Etiquetas legibles de cada estado. */
function agua_etiqueta_estado(string $estado): string
{
    return [
        'roja' => 'Alerta roja',
        'naranja' => 'Alerta naranja',
        'amarilla' => 'Alerta amarilla',
        'bajos' => 'Niveles bajos',
        'normal' => 'Normal',
        'sin_dato' => 'Sin lectura reciente',
    ][$estado] ?? 'Normal';
}

/**
 * Riesgo de desabastecimiento por municipio según el IDEAM.
 *
 * Se lee del archivo local que genera scripts/gen_desabastecimiento_boyaca.py:
 * la capa original del visor pesa 19 MB porque incluye la geometría de todo el
 * país, y como es un estudio fijo (1998-2021) no tiene sentido descargarla en
 * cada visita. Para actualizarla se vuelve a correr ese script.
 *
 * @return array<string,array{nombre:string,categoria:string,seco:bool,humedo:bool}>
 */
function agua_desabastecimiento(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $p = __DIR__ . '/../data/fenomenos/desabastecimiento_boyaca.json';
    $d = is_file($p) ? json_decode((string) @file_get_contents($p), true) : null;
    $m = is_array($d) ? ($d['municipios'] ?? []) : [];

    return $cache = is_array($m) ? $m : [];
}

/**
 * Índice de Salud de la Vegetación (VHI) por municipio: el indicador de sequía
 * agrícola de la NOAA, que combina estrés hídrico y térmico frente a la serie
 * histórica. Lo calcula scripts/gen_vhi_boyaca.py a partir del producto
 * semanal de 4 km; aquí solo se lee el archivo resultante.
 *
 * @return array{municipios:array,corte:string,semana:?int,anio:?int,url:string,archivo:string}
 */
function agua_vhi(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $p = __DIR__ . '/../data/fenomenos/vhi_boyaca.json';
    $d = is_file($p) ? json_decode((string) @file_get_contents($p), true) : null;
    if (!is_array($d) || empty($d['municipios'])) {
        return $cache = ['municipios' => [], 'corte' => '', 'semana' => null,
                         'anio' => null, 'url' => '', 'archivo' => ''];
    }

    return $cache = [
        'municipios' => $d['municipios'],
        'corte' => (string) ($d['corte'] ?? ''),
        'semana' => isset($d['semana']) ? (int) $d['semana'] : null,
        'anio' => isset($d['anio']) ? (int) $d['anio'] : null,
        'url' => (string) ($d['url'] ?? ''),
        'archivo' => (string) ($d['archivo'] ?? ''),
    ];
}

/**
 * Resumen para las fichas de la subpestaña: embalse, estaciones en alerta y
 * municipios con riesgo de desabastecimiento en temporada seca.
 */
function agua_resumen(): array
{
    $emb = agua_embalses();
    $niv = agua_estaciones('nivel');
    $des = agua_desabastecimiento();

    $alerta = 0;
    $conDato = 0;
    foreach ($niv as $e) {
        if ($e['estado'] === 'sin_dato') {
            continue;
        }
        $conDato++;
        if (in_array($e['estado'], ['roja', 'naranja', 'amarilla'], true)) {
            $alerta++;
        }
    }

    $seco = 0;
    foreach ($des as $d) {
        if ($d['seco']) {
            $seco++;
        }
    }

    $vhi = agua_vhi();
    $vhiEstres = 0;
    foreach ($vhi['municipios'] as $m) {
        if ((float) ($m['vhi'] ?? 100) < 40) {
            $vhiEstres++;
        }
    }

    return [
        'vhi' => $vhi,
        'vhi_estres' => $vhiEstres,
        'embalses' => $emb,
        'estaciones_nivel' => $niv,
        'estaciones_total' => count($niv),
        'estaciones_con_dato' => $conDato,
        'estaciones_alerta' => $alerta,
        'desabastecimiento' => $des,
        'municipios_seco' => $seco,
        'municipios_total' => count($des),
        'hay_datos' => ($emb['boyaca'] || $niv || $des || $vhi['municipios']),
    ];
}
