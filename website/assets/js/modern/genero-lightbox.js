/**
 * genero-lightbox.js — Modal/lightbox unificado para el observatorio de género.
 *
 * Captura:
 *   - <a href="*.pdf">  → abre el PDF en iframe (vista embebida).
 *   - <button class="ver-tablero-btn">  → abre data-iframe de la .tablero-card padre.
 *   - [data-lightbox-iframe="URL" data-lightbox-title="..."]  → abre URL en iframe.
 *   - <a href="https://...power*.com...">  → abre Power BI en iframe.
 *
 * También permite "ver imagen ampliada" al click en <img class="lightbox-img"> o
 * en cualquier <img> dentro de .ms-pane-body si tiene atributo data-zoomable.
 *
 * El HTML del modal se inyecta dinámicamente para no depender del template.
 */
(function () {
    if (window.__gen_lb_init) return;
    window.__gen_lb_init = true;

    // ── HTML del modal ─────────────────────────────────────────────────
    var modalHtml =
        '<div class="modal fade" id="genLightboxModal" tabindex="-1" aria-hidden="true">' +
          '<div class="modal-dialog modal-xl modal-dialog-centered">' +
            '<div class="modal-content gen-lightbox">' +
              '<div class="modal-header gen-lightbox-header">' +
                '<h5 class="modal-title" id="genLightboxTitle">Vista previa</h5>' +
                '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>' +
              '</div>' +
              '<div class="modal-body p-0 position-relative">' +
                '<div id="genLightboxLoader" class="gen-lightbox-loader">' +
                  '<div class="spinner-border text-light" role="status"></div>' +
                  '<p class="mt-3 mb-0">Cargando contenido…</p>' +
                '</div>' +
                '<iframe id="genLightboxFrame" class="gen-lightbox-frame" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>' +
                '<img id="genLightboxImg" class="gen-lightbox-img" alt="" style="display:none">' +
              '</div>' +
              '<div class="modal-footer gen-lightbox-footer">' +
                '<a id="genLightboxOpen" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Abrir en nueva pestaña</a>' +
                '<button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cerrar</button>' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</div>';

    function ensureModal() {
        if (!document.getElementById('genLightboxModal')) {
            var wrap = document.createElement('div');
            wrap.innerHTML = modalHtml;
            document.body.appendChild(wrap.firstChild);
        }
        return document.getElementById('genLightboxModal');
    }

    function openIframe(url, title) {
        var modalEl = ensureModal();
        var frame   = document.getElementById('genLightboxFrame');
        var img     = document.getElementById('genLightboxImg');
        var loader  = document.getElementById('genLightboxLoader');
        var ttl     = document.getElementById('genLightboxTitle');
        var openIn  = document.getElementById('genLightboxOpen');

        ttl.textContent = title || 'Vista previa';
        openIn.href = url;
        img.style.display = 'none'; img.src = '';
        frame.style.display = 'block';
        loader.style.display = 'flex';
        frame.onload = function () { loader.style.display = 'none'; };
        frame.src = url;

        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    function openImage(url, title) {
        var modalEl = ensureModal();
        var frame   = document.getElementById('genLightboxFrame');
        var img     = document.getElementById('genLightboxImg');
        var loader  = document.getElementById('genLightboxLoader');
        var ttl     = document.getElementById('genLightboxTitle');
        var openIn  = document.getElementById('genLightboxOpen');

        ttl.textContent = title || '';
        openIn.href = url;
        frame.style.display = 'none'; frame.src = '';
        img.style.display = 'block';
        loader.style.display = 'flex';
        img.onload = function () { loader.style.display = 'none'; };
        img.src = url;

        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    // ── Delegación de eventos ─────────────────────────────────────────
    document.addEventListener('click', function (e) {
        // 1. Botón "Ver tablero" legacy
        var verBtn = e.target.closest('.ver-tablero-btn');
        if (verBtn) {
            var card = verBtn.closest('.tablero-card');
            var url  = card && card.getAttribute('data-iframe');
            var ttl  = card && (card.getAttribute('data-title') || (card.querySelector('.card-title') && card.querySelector('.card-title').textContent.trim()));
            if (url) { e.preventDefault(); openIframe(url, ttl); return; }
        }

        // 2. Atributo explícito data-lightbox-iframe
        var lbIframe = e.target.closest('[data-lightbox-iframe]');
        if (lbIframe) {
            e.preventDefault();
            openIframe(lbIframe.getAttribute('data-lightbox-iframe'), lbIframe.getAttribute('data-lightbox-title') || lbIframe.textContent.trim());
            return;
        }

        // 3. Click en una .tablero-card que tenga data-iframe (toda la tarjeta es clickeable)
        var card2 = e.target.closest('.ms-pane-body .tablero-card[data-iframe]');
        if (card2) {
            // si el click vino de un <a> que NO es .pdf-link/.ver-tablero-btn dejarlo pasar
            var anchor = e.target.closest('a');
            if (anchor && !anchor.classList.contains('pdf-link') && !anchor.classList.contains('ver-tablero-btn')) return;
            var url2 = card2.getAttribute('data-iframe');
            var ttl2 = card2.getAttribute('data-title') || (card2.querySelector('.card-title') && card2.querySelector('.card-title').textContent.trim());
            if (url2) { e.preventDefault(); openIframe(url2, ttl2); return; }
        }

        // 4. Enlaces a PDF (.pdf-link o cualquier <a href="*.pdf">)
        var pdfA = e.target.closest('.ms-pane-body a.pdf-link, .ms-pane-body a[href$=".pdf"], .ms-pane-body a[href$=".PDF"]');
        if (pdfA) {
            var href = pdfA.getAttribute('href');
            if (!href) return;
            // permitir Ctrl/Meta+click para abrir en nueva pestaña
            if (e.ctrlKey || e.metaKey || pdfA.target === '_blank') return;
            e.preventDefault();
            var pdfTitle = pdfA.getAttribute('data-title') || pdfA.textContent.trim();
            openIframe(href, pdfTitle);
            return;
        }

        // 5. Enlaces a Power BI
        var pbiA = e.target.closest('.ms-pane-body a[href*="app.powerbi.com"]');
        if (pbiA) {
            if (e.ctrlKey || e.metaKey || pbiA.target === '_blank') return;
            // sólo si el enlace tiene texto tipo "Ver tablero/boletín"
            if (/ver |abrir/i.test(pbiA.textContent)) {
                e.preventDefault();
                openIframe(pbiA.href, pbiA.getAttribute('data-title') || pbiA.textContent.trim());
                return;
            }
        }

        // 6. Imágenes ampliables (data-zoomable o clase lightbox-img)
        var zoomImg = e.target.closest('.ms-pane-body img.lightbox-img, .ms-pane-body img[data-zoomable]');
        if (zoomImg) {
            e.preventDefault();
            openImage(zoomImg.getAttribute('data-full') || zoomImg.src, zoomImg.alt || '');
            return;
        }
    });

    // Limpiar iframe al cerrar para detener videos / liberar memoria
    document.addEventListener('hidden.bs.modal', function (e) {
        if (e.target && e.target.id === 'genLightboxModal') {
            var f = document.getElementById('genLightboxFrame');
            var i = document.getElementById('genLightboxImg');
            if (f) f.src = 'about:blank';
            if (i) i.src = '';
        }
    });
})();
