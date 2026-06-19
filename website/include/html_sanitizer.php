<?php
/**
 * Saneador de HTML enriquecido para contenido editorial (cuerpo de noticias).
 *
 * El cuerpo de las noticias admite HTML intencional creado por editores del CMS,
 * pero NO debe permitir vectores de XSS almacenado. Esta función aplica una
 * lista blanca de etiquetas/atributos y elimina scripts, manejadores de eventos
 * y URLs peligrosas (javascript:, data:, etc.) usando DOMDocument.
 */

if (!function_exists('cms_sanitize_rich_html')) {

    function cms_sanitize_rich_html(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        // Etiquetas permitidas (formato de texto, listas, enlaces, multimedia básica, tablas).
        $allowedTags = [
            'p', 'br', 'hr', 'span', 'div', 'blockquote', 'pre', 'code',
            'strong', 'b', 'em', 'i', 'u', 's', 'small', 'sub', 'sup', 'mark',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'dl', 'dt', 'dd',
            'a', 'img', 'figure', 'figcaption',
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption',
        ];
        // Atributos permitidos por etiqueta.
        $allowedAttrs = [
            'a'   => ['href', 'title', 'target', 'rel'],
            'img' => ['src', 'alt', 'title', 'width', 'height'],
            '*'   => ['class', 'id', 'style'],
        ];

        $doc = new DOMDocument('1.0', 'UTF-8');
        // Encapsular en UTF-8 para que DOMDocument no rompa los acentos.
        $wrapped = '<?xml encoding="UTF-8"><div id="__cms_root__">' . $html . '</div>';

        $prevErrors = libxml_use_internal_errors(true);
        $loaded = $doc->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prevErrors);

        if (!$loaded) {
            // Si no se puede parsear, devolver versión 100% escapada como salvaguarda.
            return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
        }

        $root = $doc->getElementById('__cms_root__');
        if ($root === null) {
            return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
        }

        $sanitizeNode = function (DOMNode $node) use (&$sanitizeNode, $allowedTags, $allowedAttrs) {
            // Recorrer hijos de atrás hacia adelante para poder eliminar de forma segura.
            for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
                $child = $node->childNodes->item($i);
                if (!($child instanceof DOMElement)) {
                    continue;
                }
                $tag = strtolower($child->nodeName);

                if (!in_array($tag, $allowedTags, true)) {
                    // Etiqueta no permitida (script, style, iframe, etc.): eliminar el nodo completo.
                    $node->removeChild($child);
                    continue;
                }

                // Depurar atributos.
                $permitted = array_merge($allowedAttrs['*'] ?? [], $allowedAttrs[$tag] ?? []);
                for ($a = $child->attributes->length - 1; $a >= 0; $a--) {
                    $attr = $child->attributes->item($a);
                    $name = strtolower($attr->nodeName);
                    $value = $attr->nodeValue;

                    // Eliminar manejadores de eventos y atributos no permitidos.
                    if (strpos($name, 'on') === 0 || !in_array($name, $permitted, true)) {
                        $child->removeAttribute($attr->nodeName);
                        continue;
                    }
                    // Validar esquema en href/src: bloquear javascript:, data:, vbscript:.
                    if ($name === 'href' || $name === 'src') {
                        $clean = preg_replace('/[\s\x00-\x1F]/', '', strtolower($value));
                        if (preg_match('/^(javascript|data|vbscript):/i', $clean)) {
                            $child->removeAttribute($attr->nodeName);
                            continue;
                        }
                    }
                    // En style, eliminar usos de expression()/url(javascript:).
                    if ($name === 'style' && preg_match('/expression\s*\(|javascript:|behaviou?r:/i', $value)) {
                        $child->removeAttribute($attr->nodeName);
                        continue;
                    }
                }

                // Forzar rel seguro en enlaces que abren en pestaña nueva.
                if ($tag === 'a' && strtolower((string) $child->getAttribute('target')) === '_blank') {
                    $child->setAttribute('rel', 'noopener noreferrer');
                }

                $sanitizeNode($child);
            }
        };

        $sanitizeNode($root);

        // Serializar solo el contenido interno del contenedor.
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }
}
