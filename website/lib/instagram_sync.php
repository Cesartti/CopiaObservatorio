<?php

/**
 * Conector Instagram (Graph API) → cms_social_posts.
 *
 * Lee las publicaciones de la cuenta Business/Creator configurada, filtra las
 * que contienen el hashtag indicado en su texto, y las inserta/actualiza en
 * cms_social_posts (network='instagram') para que aparezcan en el home.
 *
 * Diseñado para correr por CRON (scripts/sync_instagram.php) o desde el CMS.
 * No requiere el permiso de "hashtag search": usa el media propio de la cuenta.
 */

/** Normaliza texto para comparar hashtags (minúsculas, sin acentos). */
function ig_norm(string $s): string
{
    $s = mb_strtolower($s, 'UTF-8');
    $tr = ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u'];
    return strtr($s, $tr);
}

/** Extrae el shortcode de un permalink de Instagram. */
function ig_shortcode(string $permalink): string
{
    if (preg_match('#instagram\.com/(?:[^/]+/)?(?:p|reel|tv)/([A-Za-z0-9_\-]+)#', $permalink, $m)) {
        return $m[1];
    }
    return '';
}

/** Llama a la Graph API y devuelve el JSON decodificado (o lanza RuntimeException). */
function ig_graph_get(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        throw new RuntimeException('Error de red al consultar Instagram: ' . $err);
    }
    $json = json_decode($body, true);
    if (!is_array($json)) {
        throw new RuntimeException('Respuesta no válida de Instagram.');
    }
    if (isset($json['error'])) {
        $msg = $json['error']['message'] ?? 'desconocido';
        throw new RuntimeException('Instagram API: ' . $msg . ' (HTTP ' . $code . ')');
    }
    return $json;
}

/**
 * Sincroniza las publicaciones con hashtag. Devuelve un resumen.
 * @return array{ok:bool, encontrados:int, insertados:int, actualizados:int, error?:string}
 */
function ig_sync_hashtag_posts(PDO $pdo, array $cfg): array
{
    $token  = trim((string) ($cfg['access_token'] ?? ''));
    $userId = trim((string) ($cfg['ig_user_id'] ?? ''));
    $hashtag = ig_norm('#' . ltrim((string) ($cfg['hashtag'] ?? ''), '#'));
    $maxPosts = (int) ($cfg['max_posts'] ?? 12);
    $ver = (string) ($cfg['graph_version'] ?? 'v21.0');

    if ($token === '' || $userId === '') {
        return ['ok' => false, 'encontrados' => 0, 'insertados' => 0, 'actualizados' => 0,
                'error' => 'Falta access_token o ig_user_id en config/instagram.local.php'];
    }

    $fields = 'id,caption,permalink,media_type,media_url,thumbnail_url,timestamp';
    $url = "https://graph.facebook.com/{$ver}/{$userId}/media?fields={$fields}&limit=50&access_token=" . urlencode($token);

    $candidatos = [];
    $paginas = 0;
    try {
        while ($url && $paginas < 6) {  // hasta ~300 publicaciones recientes
            $resp = ig_graph_get($url);
            foreach (($resp['data'] ?? []) as $post) {
                $caption = (string) ($post['caption'] ?? '');
                if (strpos(ig_norm($caption), $hashtag) !== false) {
                    $candidatos[] = $post;
                }
            }
            $url = $resp['paging']['next'] ?? '';
            $paginas++;
            if (count($candidatos) >= $maxPosts) break;
        }
    } catch (RuntimeException $e) {
        return ['ok' => false, 'encontrados' => 0, 'insertados' => 0, 'actualizados' => 0, 'error' => $e->getMessage()];
    }

    $candidatos = array_slice($candidatos, 0, $maxPosts);

    $ins = 0; $upd = 0;
    $selable = $pdo->prepare('SELECT id FROM cms_social_posts WHERE url = ? LIMIT 1');
    $insSql = $pdo->prepare(
        'INSERT INTO cms_social_posts (sort_order, network, category, title, byline, excerpt, url, post_date, image, media_tag, media_caption, is_active)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,1)'
    );
    $updSql = $pdo->prepare(
        'UPDATE cms_social_posts SET title=?, excerpt=?, post_date=?, image=?, media_caption=?, is_active=1 WHERE id=?'
    );

    foreach ($candidatos as $i => $post) {
        $permalink = (string) ($post['permalink'] ?? '');
        if ($permalink === '') continue;
        $caption = trim((string) ($post['caption'] ?? ''));
        // Título = primera línea/frase del caption (recortada).
        $titulo = trim(preg_split('/\r\n|\r|\n/', $caption)[0] ?? '');
        $titulo = mb_substr($titulo !== '' ? $titulo : 'Publicación de @secplaneacionboyaca', 0, 180, 'UTF-8');
        $excerpt = mb_substr($caption, 0, 400, 'UTF-8');
        $fecha = !empty($post['timestamp']) ? date('Y-m-d', strtotime($post['timestamp'])) : null;
        $imagen = (string) ($post['media_type'] ?? '') === 'VIDEO'
            ? (string) ($post['thumbnail_url'] ?? '')
            : (string) ($post['media_url'] ?? '');

        $selable->execute([$permalink]);
        $existing = $selable->fetchColumn();
        if ($existing) {
            $updSql->execute([$titulo, $excerpt, $fecha, $imagen, $caption, (int) $existing]);
            $upd++;
        } else {
            $insSql->execute([$i, 'instagram', 'INSTAGRAM', $titulo, '@secplaneacionboyaca',
                              $excerpt, $permalink, $fecha, $imagen, '#' . ($cfg['hashtag'] ?? ''), $caption]);
            $ins++;
        }
    }

    return ['ok' => true, 'encontrados' => count($candidatos), 'insertados' => $ins, 'actualizados' => $upd];
}
