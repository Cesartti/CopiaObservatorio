<?php

/**
 * Widget flotante de accesibilidad (WCAG): botón en la esquina inferior
 * izquierda con panel de ajustes — tamaño de texto, filtros de color para
 * daltonismo, lectura en voz alta para personas ciegas o con baja visión,
 * guía de lectura y más. Las preferencias persisten entre páginas.
 */
if (defined('OBS_A11Y_WIDGET_RENDERED')) {
    return;
}
define('OBS_A11Y_WIDGET_RENDERED', true);
?>
<link rel="stylesheet" href="assets/css/modern/accessibility-widget.css">
<button type="button" id="a11yFab" class="a11y-fab" aria-expanded="false" aria-controls="a11yPanel" title="Opciones de accesibilidad" aria-label="Abrir opciones de accesibilidad">
    <i class="fa-solid fa-universal-access" aria-hidden="true"></i>
</button>
<div id="a11yPanel" class="a11y-panel" role="dialog" aria-labelledby="a11yTitle">
    <div class="a11y-panel__head">
        <h2 id="a11yTitle"><i class="fa-solid fa-universal-access" aria-hidden="true"></i> Accesibilidad</h2>
        <button type="button" id="a11yClose" aria-label="Cerrar panel de accesibilidad">Cerrar</button>
    </div>
    <div class="a11y-panel__body">
        <div class="a11y-group">
            <h3>Tamaño del texto</h3>
            <div class="a11y-font-row">
                <button type="button" data-a11y-action="font-down" aria-label="Disminuir tamaño del texto">A−</button>
                <span class="a11y-font-val" id="a11yFontVal" aria-live="polite">100%</span>
                <button type="button" data-a11y-action="font-up" aria-label="Aumentar tamaño del texto">A+</button>
            </div>
        </div>

        <div class="a11y-group">
            <h3>Color y contraste (apoyo para daltonismo)</h3>
            <div class="a11y-grid">
                <button type="button" class="a11y-opt" data-a11y="a11y-contrast" aria-pressed="false"><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i>Alto contraste</button>
                <button type="button" class="a11y-opt" data-a11y="a11y-saturate" aria-pressed="false"><i class="fa-solid fa-droplet" aria-hidden="true"></i>Saturación alta</button>
                <button type="button" class="a11y-opt" data-a11y="a11y-grayscale" aria-pressed="false"><i class="fa-solid fa-circle-half-stroke fa-rotate-90" aria-hidden="true"></i>Escala de grises</button>
                <button type="button" class="a11y-opt" data-a11y="a11y-invert" aria-pressed="false"><i class="fa-solid fa-circle-dot" aria-hidden="true"></i>Invertir colores</button>
                <button type="button" class="a11y-opt" data-a11y="a11y-links" aria-pressed="false"><i class="fa-solid fa-link" aria-hidden="true"></i>Resaltar enlaces</button>
                <button type="button" class="a11y-opt" data-a11y="a11y-font" aria-pressed="false"><i class="fa-solid fa-font" aria-hidden="true"></i>Fuente legible</button>
            </div>
        </div>

        <div class="a11y-group">
            <h3>Lectura en voz alta</h3>
            <div class="a11y-grid">
                <button type="button" class="a11y-opt" data-a11y="read-click" aria-pressed="false"><i class="fa-solid fa-hand-pointer" aria-hidden="true"></i>Leer al hacer clic</button>
                <button type="button" class="a11y-opt" data-a11y-action="read-page"><i class="fa-solid fa-volume-high" aria-hidden="true"></i>Leer página</button>
                <button type="button" class="a11y-opt" data-a11y-action="read-stop"><i class="fa-solid fa-volume-xmark" aria-hidden="true"></i>Detener voz</button>
            </div>
        </div>

        <div class="a11y-group">
            <h3>Navegación y lectura</h3>
            <div class="a11y-grid">
                <button type="button" class="a11y-opt" data-a11y="a11y-guide" aria-pressed="false"><i class="fa-solid fa-ruler-horizontal" aria-hidden="true"></i>Guía de lectura</button>
                <button type="button" class="a11y-opt" data-a11y="a11y-cursor" aria-pressed="false"><i class="fa-solid fa-arrow-pointer" aria-hidden="true"></i>Cursor grande</button>
                <button type="button" class="a11y-opt" data-a11y="a11y-noanim" aria-pressed="false"><i class="fa-solid fa-pause" aria-hidden="true"></i>Sin animaciones</button>
            </div>
        </div>

        <button type="button" class="a11y-reset" data-a11y-action="reset"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i> Restablecer todo</button>
        <p class="a11y-note">Las preferencias se guardan en este navegador y aplican en todas las páginas del observatorio.</p>
    </div>
</div>
<div class="a11y-guide-bar" aria-hidden="true"></div>
<script src="assets/js/modern/accessibility-widget.js" defer></script>
