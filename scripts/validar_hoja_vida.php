<?php

/**
 * Valida que el código de la hoja de vida sea el que abre el enlace.
 *
 * Por cada ficha comprueba tres cosas:
 *
 *   1. que exista la carpeta website/indicador/<código>/, que es lo que abre
 *      indicador.php?id=<código>;
 *   2. que el título de la ficha describa ese indicador y no otro, con la misma
 *      función que usa el portal (im_ficha_corresponde);
 *   3. que la carpeta tenga al menos una gráfica, porque una ficha que lleva a
 *      un indicador vacío es un enlace roto con otra cara.
 *
 * Lee la tabla `indicators` si hay base de datos; si no, lee los CSV de
 * database/seeds. Así sirve igual en el servidor y en una copia local sin BD.
 *
 * Uso:  php scripts/validar_hoja_vida.php
 *       php scripts/validar_hoja_vida.php --csv      (ignora la BD)
 */
require __DIR__ . '/../website/lib/indicator_metadata.php';

$soloCsv = in_array('--csv', $argv, true);
$raiz = dirname(__DIR__);
$pub = $raiz . '/website/indicador';

/** Título y número de gráficas de una carpeta publicada. */
function carpeta(string $pub, string $cod): ?array
{
    $info = $pub . '/' . $cod . '/indicador.info';
    if (!is_file($info)) {
        return null;
    }
    $titulo = '';
    $retirado = false;
    foreach (file($info, FILE_IGNORE_NEW_LINES) ?: [] as $linea) {
        $pos = strpos($linea, ':');
        if ($pos === false) {
            continue;
        }
        $clave = im_titulo_comparable(substr($linea, 0, $pos));
        $valor = trim(substr($linea, $pos + 1));
        if ($clave === 'titulo') {
            $titulo = $valor;
        }
        if ($clave === 'retirado' && $valor !== '') {
            $retirado = true;
        }
    }
    $graficas = 0;
    while (is_file($pub . '/' . $cod . '/' . ($graficas + 1) . '.csv')) {
        $graficas++;
    }

    return ['titulo' => $titulo, 'graficas' => $graficas, 'retirado' => $retirado];
}

/* ── De dónde salen las fichas ─────────────────────────────────────────── */
$fichas = [];
$fuente = '';
if (!$soloCsv && is_file($raiz . '/website/config/database.php')) {
    require_once $raiz . '/website/config/database.php';
    $pdo = function_exists('cms_pdo') ? cms_pdo() : null;
    if ($pdo) {
        foreach ($pdo->query('SELECT id, title, observatory_id FROM indicators')
                 ->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $fichas[] = $r;
        }
        $fuente = 'tabla `indicators` de la base de datos';
    }
}
if (!$fichas) {
    foreach (glob($raiz . '/database/seeds/indicators_*_realineado.csv') ?: [] as $csv) {
        $fh = fopen($csv, 'r');
        $cab = fgetcsv($fh);
        while (($fila = fgetcsv($fh)) !== false) {
            $r = array_combine($cab, array_pad(array_slice($fila, 0, count($cab)), count($cab), ''));
            $fichas[] = ['id' => $r['id'], 'title' => $r['title'],
                         'observatory_id' => $r['observatory_id']];
        }
        fclose($fh);
    }
    $fuente = $fuente ?: 'los CSV realineados de database/seeds';
}
if (!$fichas) {
    fwrite(STDERR, "No hay fichas que validar: ni base de datos ni CSV realineados.\n");
    exit(2);
}

/* ── Validación ────────────────────────────────────────────────────────── */
$ok = 0;
$problemas = ['sin_carpeta' => [], 'otro_indicador' => [], 'sin_graficas' => [],
              'retirado' => []];
foreach ($fichas as $f) {
    $cod = (string) $f['id'];
    $c = carpeta($pub, $cod);
    if ($c === null) {
        $problemas['sin_carpeta'][] = [$cod, $f['title']];
        continue;
    }
    if ($c['retirado']) {
        $problemas['retirado'][] = [$cod, $f['title']];
        continue;
    }
    if (!im_ficha_corresponde($c['titulo'], (string) $f['title'])) {
        $problemas['otro_indicador'][] = [$cod, $f['title'], $c['titulo']];
        continue;
    }
    if ($c['graficas'] === 0) {
        $problemas['sin_graficas'][] = [$cod, $f['title']];
        continue;
    }
    $ok++;
}

echo "Hoja de vida leída de: $fuente\n";
echo str_repeat('=', 78) . "\n";
printf("fichas revisadas: %d\n", count($fichas));
printf("  el código abre el mismo indicador que describe la ficha: %d\n", $ok);
printf("  el código no tiene carpeta publicada: %d\n", count($problemas['sin_carpeta']));
printf("  el código abre OTRO indicador: %d\n", count($problemas['otro_indicador']));
printf("  el código abre un indicador retirado: %d\n", count($problemas['retirado']));
printf("  el código abre un indicador sin gráficas: %d\n", count($problemas['sin_graficas']));

foreach (['sin_carpeta' => 'Sin carpeta publicada',
          'otro_indicador' => 'La ficha describe otro indicador',
          'retirado' => 'Indicador retirado',
          'sin_graficas' => 'Indicador sin ninguna gráfica'] as $clave => $titulo) {
    if (!$problemas[$clave]) {
        continue;
    }
    echo "\n" . $titulo . ":\n";
    foreach (array_slice($problemas[$clave], 0, 40) as $p) {
        echo '   ' . $p[0] . '  ficha: ' . mb_substr((string) $p[1], 0, 46)
            . (isset($p[2]) ? '  |  carpeta: ' . mb_substr((string) $p[2], 0, 46) : '') . "\n";
    }
}

$fallos = count($fichas) - $ok;
echo "\n" . ($fallos === 0
    ? "Todas las fichas abren el indicador que describen.\n"
    : "$fallos ficha(s) por resolver.\n");
exit($fallos === 0 ? 0 : 1);
