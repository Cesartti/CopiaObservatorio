<?php

/**
 * Tableros oficiales de Power BI por observatorio (los reales de la Gobernación).
 * Se embeben en la pestaña "Tablero de datos" del micrositio (observatorio.php).
 *
 *   slug => URL de inserción de Power BI ("Publicar en la web" → enlace).
 *   ''   => aún sin tablero: se muestra un aviso de "en preparación" (no datos demo).
 *
 * Nota: el observatorio de Género usa su propio panel de varios tableros Power BI
 * dentro de observatorio.php, por eso no se incluye aquí.
 */

return [
    'economico' => 'https://app.powerbi.com/view?r=eyJrIjoiNTAyNjgyNTYtMWQyNC00Njc3LWJkMzgtMzRiNTBjNTUyODYwIiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=07fb08234b68b1d828a7',
    'social'    => 'https://app.powerbi.com/view?r=eyJrIjoiNGNhNWM1MDEtNzM0Ny00OWRlLWFmNzUtY2RkYzBhMDNjZGQ0IiwidCI6IjYyMDEwNGUyLTEzOTAtNDNjNS1iYTQ1LTg1ZDE4ODNjYzQ4OCJ9&pageName=808e68650d47116cd8ee',
    'ambiente'  => '',
    'cti'       => '',
];
