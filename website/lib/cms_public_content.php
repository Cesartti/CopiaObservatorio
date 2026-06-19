<?php

/**
 * Une contenido del CMS (MySQL) con fallback a website/data/content.json.
 */
function cms_content_json_path(): string
{
    return dirname(__DIR__) . '/data/content.json';
}

function cms_load_json_file(): array
{
    $path = cms_content_json_path();
    if (!is_readable($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);

    return is_array($data) ? $data : [];
}

function cms_home_from_database(?PDO $pdo): ?array
{
    if (!$pdo) {
        return null;
    }
    try {
        try {
            $banners = $pdo->query('SELECT title, subtitle, tag, image_url, link_url FROM cms_home_banners WHERE is_active = 1 ORDER BY sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            try {
                $banners = $pdo->query('SELECT title, subtitle, tag, image_url FROM cms_home_banners WHERE is_active = 1 ORDER BY sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
            } catch (Throwable $e2) {
                $banners = $pdo->query('SELECT title, subtitle, tag FROM cms_home_banners WHERE is_active = 1 ORDER BY sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        $social = $pdo->query(
            'SELECT network, category, title, byline, excerpt, url, DATE_FORMAT(post_date, "%Y-%m-%d") AS date, image, media_tag, media_caption FROM cms_social_posts WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        )->fetchAll(PDO::FETCH_ASSOC);
        $contactRow = $pdo->query('SELECT institution, address, phone, email, hours, url_facebook AS facebook, url_instagram AS instagram, url_x AS x, url_youtube AS youtube FROM cms_contact WHERE id = 1')->fetch(PDO::FETCH_ASSOC);

        $hasCms = ($banners !== [] || $social !== [] || ($contactRow && array_filter($contactRow)));

        if (!$hasCms) {
            return null;
        }

        $contact = null;
        if ($contactRow && array_filter($contactRow)) {
            $contact = [
                'institution' => $contactRow['institution'],
                'address' => $contactRow['address'],
                'phone' => $contactRow['phone'],
                'email' => $contactRow['email'],
                'hours' => $contactRow['hours'],
                'social' => array_filter([
                    'facebook' => $contactRow['facebook'],
                    'instagram' => $contactRow['instagram'],
                    'x' => $contactRow['x'],
                    'youtube' => $contactRow['youtube'],
                ]),
            ];
        }

        return [
            'home_banners' => array_map(static function ($b) {
                return [
                    'title' => $b['title'],
                    'subtitle' => $b['subtitle'] ?? '',
                    'tag' => $b['tag'] ?? '',
                    'image_url' => $b['image_url'] ?? '',
                    'link_url' => $b['link_url'] ?? '',
                ];
            }, $banners),
            'social_feed' => array_map(static function ($s) {
                return [
                    'network' => $s['network'],
                    'category' => $s['category'] ?? '',
                    'title' => $s['title'],
                    'byline' => $s['byline'] ?? '',
                    'excerpt' => $s['excerpt'] ?? '',
                    'url' => $s['url'],
                    'date' => $s['date'] ?? '',
                    'image' => $s['image'] ?? '',
                    'media_tag' => $s['media_tag'] ?? '',
                    'media_caption' => $s['media_caption'] ?? '',
                ];
            }, $social),
            'contact' => $contact,
        ];
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * Contenido portal inicio: prioriza CMS si hay datos activos; mezcla noticias JSON.
 */
function cms_portal_home_payload(?PDO $pdo): array
{
    $file = cms_load_json_file();
    $dbBlock = cms_home_from_database($pdo);

    $merged = $file;

    if ($dbBlock !== null) {
        if (!empty($dbBlock['home_banners'])) {
            $merged['home_banners'] = $dbBlock['home_banners'];
        }
        if (!empty($dbBlock['social_feed'])) {
            $merged['social_feed'] = $dbBlock['social_feed'];
        }
        if (!empty($dbBlock['contact'])) {
            $merged['contact'] = array_replace_recursive($file['contact'] ?? [], $dbBlock['contact']);
        }
    }

    $defaults = [
        'general_news' => [],
        'dimension_news' => [],
        'featured_indicators' => [],
        'home_banners' => [],
        'social_feed' => [],
        'contact' => [],
    ];

    return array_merge($defaults, $merged);
}

/**
 * Datos de contacto: CMS (cms_contact) con fallback a content.json.
 */
function cms_contact_payload(?PDO $pdo): array
{
    $file = cms_load_json_file();
    $contact = is_array($file['contact'] ?? null) ? $file['contact'] : [];

    if ($pdo) {
        try {
            $row = $pdo->query('SELECT institution, address, phone, email, hours, url_facebook AS facebook, url_instagram AS instagram, url_x AS x, url_youtube AS youtube FROM cms_contact WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
            if ($row && array_filter($row)) {
                $contact = array_replace_recursive($contact, [
                    'institution' => $row['institution'],
                    'address' => $row['address'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'hours' => $row['hours'],
                    'social' => array_filter([
                        'facebook' => $row['facebook'],
                        'instagram' => $row['instagram'],
                        'x' => $row['x'],
                        'youtube' => $row['youtube'],
                    ]),
                ]);
            }
        } catch (Throwable $e) {
            // Tabla aún no migrada: se mantiene el contenido del JSON.
        }
    }

    return $contact;
}

/**
 * Diapositivas hero del micrositio (observatorio.php). Null si no hay tabla o filas.
 *
 * @return list<array{title:string,text:string,image_url:string}>|null
 */
function cms_microsite_hero_slides(?PDO $pdo, string $slug): ?array
{
    if (!$pdo || $slug === '') {
        return null;
    }
    try {
        try {
            $st = $pdo->prepare(
                'SELECT s.title, s.slide_text, s.image_url, s.link_url FROM cms_microsite_hero_slides s INNER JOIN observatories o ON o.id = s.observatory_id WHERE o.slug = ? AND s.is_active = 1 ORDER BY s.sort_order ASC, s.id ASC'
            );
            $st->execute([$slug]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $st = $pdo->prepare(
                'SELECT s.title, s.slide_text, s.image_url FROM cms_microsite_hero_slides s INNER JOIN observatories o ON o.id = s.observatory_id WHERE o.slug = ? AND s.is_active = 1 ORDER BY s.sort_order ASC, s.id ASC'
            );
            $st->execute([$slug]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        }
        if ($rows === []) {
            return null;
        }
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'title' => (string) ($r['title'] ?? ''),
                'text' => (string) ($r['slide_text'] ?? ''),
                'image_url' => (string) ($r['image_url'] ?? ''),
                'link_url' => (string) ($r['link_url'] ?? ''),
            ];
        }

        return $out;
    } catch (Throwable $e) {
        return null;
    }
}
