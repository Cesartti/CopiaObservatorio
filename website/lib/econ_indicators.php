<?php

/**
 * Indicadores económicos en vivo con caché en archivo (1 hora).
 * Fuentes públicas gratuitas:
 *  - TRM oficial Colombia → datos.gov.co (Superfinanciera)
 *  - EUR/USD → open.er-api.com (sin API key)
 *  - Brent, Oro, IPC, Tasa, Desempleo, PIB → valores base configurables por CMS/admin (baja frecuencia)
 */

function econ_cache_dir(): string
{
    $dir = dirname(__DIR__) . '/data/cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

function econ_cache_path(): string
{
    return econ_cache_dir() . '/econ_indicators.json';
}

function econ_http_get(string $url, int $timeout = 4): ?string
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'ObservatoriosBoyaca/1.0',
            CURLOPT_SSL_VERIFYPEER => false, // XAMPP suele no tener CA bundle
        ]);
        $res = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        if ($err === 0 && is_string($res) && $res !== '') return $res;
    }
    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create([
            'http'  => ['timeout' => $timeout, 'header' => "User-Agent: ObservatoriosBoyaca/1.0\r\n"],
            'https' => ['timeout' => $timeout, 'header' => "User-Agent: ObservatoriosBoyaca/1.0\r\n"],
            'ssl'   => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);
        $res = @file_get_contents($url, false, $ctx);
        if ($res !== false) return $res;
    }
    return null;
}

/** Valores por defecto/base — se sobrescriben con los obtenidos en vivo. Actualice trimestralmente. */
function econ_defaults(): array
{
    return [
        'trm'           => ['value' => 3795.55, 'date' => date('Y-m-d'), 'source' => 'default'],
        'usd_open'      => 3790.00,
        'usd_high'      => 3830.00,
        'usd_low'       => 3772.40,
        'usd_prev'      => 3796.50,
        'usd_change'    => 0.42,
        'euro'          => 4394.68,
        'brent'         => 92.40,
        'gold'          => 2153.10,
        'ipc_mensual'   => 1.08,
        'tasa_interv'   => 10.25,
        'desempleo'     => 10.9,
        'pib_anual'     => 2.3,
        'fetched_at'    => null,
    ];
}

function econ_fetch_trm(): ?array
{
    // datos.gov.co — dataset oficial TRM
    $url = 'https://www.datos.gov.co/resource/32sa-8pi3.json?$order=vigenciadesde%20DESC&$limit=2';
    $raw = econ_http_get($url, 5);
    if ($raw === null) return null;
    $j = json_decode($raw, true);
    if (!is_array($j) || empty($j[0]['valor'])) return null;
    $curr = (float) $j[0]['valor'];
    $prev = isset($j[1]['valor']) ? (float) $j[1]['valor'] : $curr;
    $change = $prev > 0 ? (($curr - $prev) / $prev) * 100 : 0;
    return [
        'value'  => $curr,
        'prev'   => $prev,
        'change' => round($change, 2),
        'date'   => substr((string) $j[0]['vigenciadesde'], 0, 10),
        'source' => 'datos.gov.co',
    ];
}

function econ_fetch_euro_from_trm(float $trm): ?float
{
    // open.er-api.com: EUR/USD rate → EUR en COP = TRM * (USD por EUR)
    $raw = econ_http_get('https://open.er-api.com/v6/latest/USD', 4);
    if ($raw === null) return null;
    $j = json_decode($raw, true);
    if (!is_array($j) || empty($j['rates']['EUR'])) return null;
    $usdPerEur = 1 / (float) $j['rates']['EUR']; // USD per 1 EUR
    return round($trm * $usdPerEur, 2);
}

function econ_load(): array
{
    $defaults = econ_defaults();
    $cacheFile = econ_cache_path();

    // Cache hit (1h)
    if (is_readable($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
        $raw = @file_get_contents($cacheFile);
        $j = $raw ? json_decode($raw, true) : null;
        if (is_array($j)) return $j + $defaults;
    }

    $data = $defaults;

    // TRM
    $trm = econ_fetch_trm();
    if ($trm !== null) {
        $data['trm']        = ['value' => $trm['value'], 'date' => $trm['date'], 'source' => $trm['source']];
        $data['usd_change'] = $trm['change'];
        $data['usd_prev']   = $trm['prev'];
        // Aproximaciones razonables para apertura/max/min (±0.3%)
        $data['usd_open']   = round($trm['prev'], 2);
        $data['usd_high']   = round($trm['value'] * 1.004, 2);
        $data['usd_low']    = round($trm['value'] * 0.995, 2);

        // EUR
        $eur = econ_fetch_euro_from_trm($trm['value']);
        if ($eur !== null) $data['euro'] = $eur;
    }

    $data['fetched_at'] = date('Y-m-d H:i');

    @file_put_contents($cacheFile, json_encode($data, JSON_UNESCAPED_UNICODE));
    return $data;
}

function econ_format_cop(float $v): string
{
    return number_format($v, 2, ',', '.');
}

function econ_format_usd(float $v): string
{
    return number_format($v, 2, ',', '.');
}
