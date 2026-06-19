/**
 * genero-content-legacy.js
 *
 * Activa interacciones del HTML migrado desde indic-genero.php que dependían
 * de scripts inline que se eliminaron al sanitizar el contenido en BD:
 *
 *  - Acordeón custom legacy:
 *      <button class="accordion-button">…</button>
 *      <div class="accordion-content">…</div>
 *      (toggle alternado, sólo uno abierto por bloque)
 *
 *  - <details class="exp"> ya funciona nativamente con HTML5, no requiere JS.
 *
 * Las pestañas internas legacy (.chip-group + .k-pane) ya no se usan: las
 * sub-secciones de "Información" y "Barreras de acceso" se renderizan ahora
 * como pestañas Bootstrap mediante el sistema cms_microsite_sections.
 */
(function () {
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.ms-pane-body .accordion-button');
        if (!btn) return;
        // Si es un botón con data-bs-toggle (Bootstrap nativo) no interferir
        if (btn.dataset.bsToggle) return;
        e.preventDefault();
        var item = btn.closest('.accordion-item');
        var content = item ? item.querySelector('.accordion-content') : btn.nextElementSibling;
        if (!content || !content.classList.contains('accordion-content')) return;

        var isOpen = content.classList.contains('is-open');
        // Cerrar todos los del mismo contenedor padre
        var container = item && item.parentElement ? item.parentElement : null;
        if (container) {
            container.querySelectorAll('.accordion-content.is-open').forEach(function (c) {
                c.classList.remove('is-open');
                var sibBtn = c.parentElement && c.parentElement.querySelector('.accordion-button');
                if (sibBtn) {
                    sibBtn.classList.remove('active');
                    sibBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
        if (!isOpen) {
            content.classList.add('is-open');
            btn.classList.add('active');
            btn.setAttribute('aria-expanded', 'true');
        }
    });

    // Marcar atributos ARIA al cargar
    document.querySelectorAll('.ms-pane-body .accordion-button').forEach(function (btn) {
        if (!btn.hasAttribute('aria-expanded')) btn.setAttribute('aria-expanded', 'false');
        btn.setAttribute('type', 'button');
    });

    // Abrir el primer accordion-item del panel ACTIVO solamente (no de todos),
    // y solo si no es uno con iframe (que reserva mucho espacio).
    function openFirstLegacyAccordionInActivePane() {
        var activePane = document.querySelector('.tab-pane.show.active');
        if (!activePane) return;
        var legacyItems = activePane.querySelectorAll('.ms-pane-body .accordion-item');
        for (var i = 0; i < legacyItems.length; i++) {
            var item = legacyItems[i];
            var content = item.querySelector('.accordion-content');
            if (!content) continue;
            var btn = item.querySelector('.accordion-button');
            if (!btn || btn.dataset.bsToggle) continue;
            // Saltar items cuyo contenido principal es un iframe (suelen ser vacíos)
            var firstChild = content.firstElementChild;
            var onlyIframe = content.children.length === 1 && firstChild && firstChild.tagName === 'IFRAME';
            if (onlyIframe) continue;
            // Si ya hay alguno abierto en este pane, no abrir más
            var existing = activePane.querySelector('.accordion-content.is-open');
            if (existing) return;
            content.classList.add('is-open');
            btn.classList.add('active');
            btn.setAttribute('aria-expanded', 'true');
            return;
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', openFirstLegacyAccordionInActivePane);
    } else {
        openFirstLegacyAccordionInActivePane();
    }
    document.addEventListener('shown.bs.tab', openFirstLegacyAccordionInActivePane);
})();
