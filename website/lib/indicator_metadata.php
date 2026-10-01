<?php

/**
 * Lectura/escritura de metadatos enriquecidos de indicadores (hoja de vida).
 */

function im_fields(): array
{
    return [
        'id','observatory_id','title','category_1','category_2','tags','unit',
        'thematic_breakdown','geographic_breakdown','definition','calculation_formula',
        'periodicity','baseline_date','delivery_form','source','source_link',
        'actors','responsible_entity','observations','availability_status',
    ];
}

function im_field_labels(): array
{
    return [
        'id' => 'Código indicador',
        'observatory_id' => 'Observatorio',
        'title' => 'Nombre del indicador',
        'category_1' => 'Categoría primer orden',
        'category_2' => 'Categoría segundo orden',
        'tags' => 'Etiquetas / categorías',
        'unit' => 'Unidad de medida',
        'thematic_breakdown' => 'Desagregación temática',
        'geographic_breakdown' => 'Desagregación geográfica',
        'definition' => 'Definición del indicador',
        'calculation_formula' => 'Cálculo / fórmula',
        'periodicity' => 'Periodicidad',
        'baseline_date' => 'Fecha línea base',
        'delivery_form' => 'Forma de entrega',
        'source' => 'Fuentes de información',
        'source_link' => 'Enlace de la fuente',
        'actors' => 'Actores involucrados',
        'responsible_entity' => 'Entidad responsable',
        'observations' => 'Observaciones',
        'availability_status' => 'Disponibilidad',
    ];
}

function im_upsert(PDO $pdo, array $row): string
{
    $fields = im_fields();
    $data = [];
    foreach ($fields as $f) {
        $data[$f] = array_key_exists($f, $row) ? trim((string) $row[$f]) : '';
    }
    if ($data['id'] === '' || !ctype_digit($data['id']) || (int) $data['observatory_id'] < 1) {
        return 'skip:invalid-key';
    }
    // Ensure title (NOT NULL in schema)
    if ($data['title'] === '') $data['title'] = 'Indicador ' . $data['id'];

    // Check existence
    $st = $pdo->prepare('SELECT id FROM indicators WHERE id = ?');
    $st->execute([(int) $data['id']]);
    $exists = (bool) $st->fetch();

    if ($exists) {
        $cols = array_diff($fields, ['id']);
        $sets = implode(', ', array_map(fn($c) => "$c = ?", $cols));
        $sql = "UPDATE indicators SET $sets WHERE id = ?";
        $vals = [];
        foreach ($cols as $c) $vals[] = $data[$c] === '' ? null : $data[$c];
        $vals[] = (int) $data['id'];
        $pdo->prepare($sql)->execute($vals);
        return 'updated';
    }
    $cols = implode(', ', $fields);
    $marks = implode(', ', array_fill(0, count($fields), '?'));
    $sql = "INSERT INTO indicators ($cols) VALUES ($marks)";
    $vals = [];
    foreach ($fields as $c) {
        if ($c === 'id' || $c === 'observatory_id') $vals[] = (int) $data[$c];
        else $vals[] = $data[$c] === '' ? null : $data[$c];
    }
    $pdo->prepare($sql)->execute($vals);
    return 'inserted';
}

function im_import_csv(PDO $pdo, string $filePath): array
{
    $h = fopen($filePath, 'r');
    if (!$h) return ['error' => 'No se pudo abrir el archivo.'];
    // Detect BOM
    $firstBytes = fread($h, 3);
    if ($firstBytes !== "\xEF\xBB\xBF") rewind($h);

    $headers = fgetcsv($h);
    if (!$headers) { fclose($h); return ['error' => 'CSV vacío o sin cabecera.']; }
    $headers = array_map(fn($s) => trim(strtolower((string) $s)), $headers);

    $stats = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
    $line = 1;
    while (($r = fgetcsv($h)) !== false) {
        $line++;
        if (count($r) < 2) { $stats['skipped']++; continue; }
        $row = [];
        foreach ($headers as $i => $k) {
            $row[$k] = $r[$i] ?? '';
        }
        try {
            $res = im_upsert($pdo, $row);
            if (strpos($res, 'skip') === 0) $stats['skipped']++;
            elseif ($res === 'inserted') $stats['inserted']++;
            elseif ($res === 'updated') $stats['updated']++;
        } catch (Throwable $e) {
            $stats['errors'][] = "Línea $line: " . $e->getMessage();
            $stats['skipped']++;
        }
    }
    fclose($h);
    return $stats;
}

/** Recupera todos los indicadores enriquecidos de un observatorio */
function im_list_by_observatory(PDO $pdo, int $obsId): array
{
    $st = $pdo->prepare('SELECT * FROM indicators WHERE observatory_id = ? ORDER BY category_1, category_2, id');
    $st->execute([$obsId]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function im_get_one(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT * FROM indicators WHERE id = ?');
    $st->execute([$id]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    return $r ?: null;
}

/** Escanea website/indicador/XXXX/*.csv (archivos de datos descargables). */
function im_list_data_files_for_observatory(int $obsId, string $websiteRoot): array
{
    $prefix = (string) $obsId; // 1=eco, 2=soc, 3=amb, 4=cti
    $base = $websiteRoot . DIRECTORY_SEPARATOR . 'indicador';
    if (!is_dir($base)) return [];
    $out = [];
    $dh = opendir($base);
    while (($f = readdir($dh)) !== false) {
        if ($f === '.' || $f === '..') continue;
        if (!ctype_digit($f) || strlen($f) === 0 || $f[0] !== $prefix) continue;
        $folder = $base . DIRECTORY_SEPARATOR . $f;
        if (!is_dir($folder)) continue;
        $infoFile = $folder . '/indicador.info';
        $title = $f;
        $retirado = false;
        if (is_readable($infoFile)) {
            foreach (file($infoFile) as $line) {
                $l = trim($line);
                if (stripos($l, 'retirado:') === 0) {
                    // Indicador de una estructura anterior: su carpeta sigue en el
                    // servidor porque el despliegue no borra archivos, pero no debe
                    // aparecer en las descargas.
                    $retirado = trim(substr($l, strpos($l, ':') + 1)) !== '';
                    continue;
                }
                if (stripos($l, 'título:') === 0 || stripos($l, 'titulo:') === 0) {
                    $title = trim(substr($l, strpos($l, ':') + 1));
                }
            }
        }
        if ($retirado) {
            continue;
        }
        $csvs = [];
        foreach (glob($folder . '/*.csv') ?: [] as $cf) {
            $csvs[] = basename($cf);
        }
        if ($csvs) {
            $out[] = ['id' => $f, 'title' => $title, 'files' => $csvs];
        }
    }
    closedir($dh);
    usort($out, fn($a, $b) => strcmp($a['id'], $b['id']));
    return $out;
}

/**
 * ¿La ficha de la hoja de vida describe el indicador de esta carpeta?
 *
 * La hoja de vida se pega a la carpeta por el código, dando por hecho que el
 * mismo número nombra al mismo indicador en los dos lados. En Ambiental y
 * Económico así es. En Social y Género no: su hoja de vida es un catálogo por
 * línea temática —donde «Enfermedades transmitidas por vector» aparece tres
 * veces, una por población— mientras las carpetas están numeradas por grupo
 * poblacional. El resultado era que 53 tarjetas del portal mostraban el título
 * de un indicador con la definición, la fórmula, la periodicidad y la fuente de
 * otro.
 *
 * Así que antes de usar la ficha se compara el título. Si no se parece, es de
 * otro indicador y vale más no mostrar nada que mostrar algo que no es.
 */
function im_ficha_corresponde(string $tituloCarpeta, string $tituloFicha): bool
{
    $a = im_titulo_comparable($tituloCarpeta);
    $b = im_titulo_comparable($tituloFicha);
    if ($a === '' || $b === '') {
        return false;
    }
    if ($a === $b) {
        return true;
    }
    // «Mortalidad prematura» y «Casos de mortalidad prematura de las enfermedades
    // …» son el mismo indicador: uno es el otro con una aclaración.
    $corto = strlen($a) <= strlen($b) ? $a : $b;
    $largo = strlen($a) <= strlen($b) ? $b : $a;
    if (strlen($corto) >= 12 && strpos($largo, $corto) !== false) {
        return true;
    }
    similar_text($a, $b, $pct);

    return $pct >= 85.0;
}

/**
 * Título normalizado para comparar: sin tildes, sin signos, en minúsculas.
 *
 * La tabla de reemplazos va escrita a mano a propósito.
 * `iconv('UTF-8','ASCII//TRANSLIT')` no da el mismo resultado en todas partes:
 * con glibc «Título» sale como «T'itulo» y en Windows como «T?tulo», así que
 * una clave con tilde dejaba de reconocerse y la comparación fallaba sin aviso.
 */
function im_titulo_comparable(string $s): string
{
    static $tildes = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
        'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
        'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',
        'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ñ' => 'n', 'ç' => 'c',
        'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u',
        'À' => 'a', 'È' => 'e', 'Ì' => 'i', 'Ò' => 'o', 'Ù' => 'u',
        'Â' => 'a', 'Ê' => 'e', 'Î' => 'i', 'Ô' => 'o', 'Û' => 'u',
        'Ä' => 'a', 'Ë' => 'e', 'Ï' => 'i', 'Ö' => 'o', 'Ñ' => 'n', 'Ç' => 'c',
    ];
    $s = strtr($s, $tildes);
    $s = function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', ' ', $s);

    return trim(preg_replace('/\s+/', ' ', (string) $s));
}

/**
 * Hoja de vida de un observatorio, armada desde los indicadores publicados.
 *
 * Las páginas `indic-*.php` llevaban esta hoja de vida escrita a mano dentro de
 * un objeto de JavaScript. Se quedó atrás dos veces: la numeración ya no era la
 * de las carpetas, y la de «Indicadores Sociales» traía los indicadores del
 * Observatorio Económico, así que elegir «Suicidios» no mostraba nada.
 *
 * Aquí se arma desde la única fuente que no puede desalinearse: las carpetas
 * publicadas, que son las que abre indicador.php. La ficha de la base de datos
 * solo se añade si describe ese mismo indicador.
 *
 * Devuelve un mapa código => campos, ordenado por código.
 */
function im_hoja_vida_publicada(int $obsId, string $dirWebsite, ?PDO $pdo = null): array
{
    $meta = [];
    if ($pdo) {
        try {
            foreach (im_list_by_observatory($pdo, $obsId) as $r) {
                $meta[(string) (int) $r['id']] = $r;
            }
        } catch (Throwable $e) { /* sin tabla de metadatos */ }
    }
    $nombres = [1 => 'Económico', 2 => 'Social', 3 => 'Ambiental', 4 => 'CTeI', 5 => 'Género'];
    $base = rtrim($dirWebsite, '/\\') . '/indicador';
    $out = [];
    foreach (@scandir($base) ?: [] as $entry) {
        if (!ctype_digit($entry) || strlen($entry) !== 4 || $entry[0] !== (string) $obsId) {
            continue;
        }
        $inf = @getInfo($base . '/' . $entry . '/indicador.info');
        if (!$inf || !empty($inf['retirado'])) {
            continue;
        }
        $graficas = 0;
        while (is_file($base . '/' . $entry . '/' . ($graficas + 1) . '.csv')) {
            $graficas++;
        }
        if ($graficas === 0) {
            continue;   // una ficha que lleva a un indicador vacío no sirve de nada
        }
        $titulo = trim((string) ($inf['titulo'] ?? ''));
        $f = $meta[$entry] ?? [];
        if ($f && !im_ficha_corresponde($titulo, (string) ($f['title'] ?? ''))) {
            $f = [];
        }
        $cat = trim((string) ($inf['categoria'] ?? ''));
        $out[$entry] = [
            'codigo' => $entry,
            'titulo' => $titulo !== '' ? $titulo : ('Indicador ' . $entry),
            'observatorio' => $nombres[$obsId] ?? '',
            'categoria1' => $cat !== '' && strcasecmp($cat, 'ND') !== 0 ? $cat : 'Sin categoría',
            'categoria2' => trim((string) ($inf['subcategoria'] ?? '')),
            'etiqueta' => trim((string) ($inf['etiquetas'] ?? '')),
            'unidad' => (string) ($f['unit'] ?? ''),
            'desagregacion' => (string) ($f['thematic_breakdown'] ?? ''),
            'geografia' => (string) ($f['geographic_breakdown'] ?? ''),
            'definicion' => (string) ($f['definition'] ?? ($inf['descripcion'] ?? '')),
            'calculo' => (string) ($f['calculation_formula'] ?? ''),
            'fecha' => (string) ($f['baseline_date'] ?? ''),
            'periodicidad' => (string) ($f['periodicity'] ?? ''),
            'fuente' => (string) ($f['source'] ?? ($inf['fuentes'] ?? '')),
            'entidad_responsable' => (string) ($f['responsible_entity'] ?? ''),
            'actores_involucrados' => (string) ($f['actors'] ?? ''),
            'forma_entrega' => (string) ($f['delivery_form'] ?? ''),
            'observaciones' => (string) ($f['observations'] ?? ''),
            'disponibilidad' => (string) ($f['availability_status'] ?? 'DISPONIBLE'),
            'graficas' => $graficas,
        ];
    }
    ksort($out);

    return $out;
}
