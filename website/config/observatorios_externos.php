<?php

/**
 * Observatorios de la Red que están alojados en otra dirección.
 *
 * Se listan aparte de `observatories.php` a propósito. Ese archivo alimenta los
 * micrositios, el menú de navegación, el buscador y la numeración de las
 * carpetas de indicadores: meter aquí un observatorio que no tiene nada de eso
 * crearía un micrositio vacío y una entrada de menú que no lleva a ninguna
 * parte. De estos solo se pinta la tarjeta en la portada y «Entrar» abre su
 * dirección en otra pestaña.
 *
 * La tarjeta se ve igual que las demás a propósito: quién desarrolló la
 * plataforma es un asunto interno y no cambia en nada lo que el visitante va a
 * encontrar. Lo único que se conserva es la flecha del botón, que avisa de que
 * se abre en otra pestaña —eso sí lo necesita saber quien navega con lector de
 * pantalla o con muchas pestañas abiertas—.
 *
 * `url` es el destino y `activo` decide si la tarjeta se muestra. Para
 * publicarla basta con poner `activo => true` y la URL definitiva.
 *
 * Para probarla sin publicarla, cree `observatorios_externos.local.php` al lado
 * de este archivo devolviendo las claves que quiera sobrescribir. Ese archivo no
 * se versiona —igual que `database.local.php`—, así que la tarjeta se ve en la
 * copia local y no en producción.
 */
return [
    'seguridad' => [
        'name' => 'Observatorio de Seguridad y Convivencia',
        'color' => '#1d4ed8',
        'accent' => '#f97316',
        'icon' => 'fa-shield-halved',
        'description' => 'Seguridad ciudadana, convivencia y delitos de alto impacto '
            . 'en el departamento de Boyacá.',
        'categories' => [
            'Delitos de alto impacto',
            'Seguridad vial',
            'Convivencia ciudadana',
            'Violencia intrafamiliar y de género',
            'Capacidad institucional',
        ],
        // Destino del botón «Entrar». Mientras esté vacío la tarjeta no se muestra.
        'url' => '',
        'activo' => false,
    ],
];
