/* Rastreador de navegación (analítica de comportamiento, anónimo).
   Detecta automáticamente:
   - page_view: a qué observatorio/página entra el visitante.
   - indicator_view: clic en un indicador (indicador.php?id=...).
   - news_open: clic en una noticia (noticia.php?...).
   - tab_open / powerbi_open: cambios de pestaña (categorías, tableros, Power BI).
   Los observatorios nuevos entran solos (el slug se deriva de la página/URL). */
(function () {
  var SLUG_BY_DIGIT = { '1': 'economico', '2': 'social', '3': 'ambiente', '4': 'cti', '5': 'genero' };

  function qs() { try { return new URLSearchParams(location.search); } catch (e) { return { get: function () { return null; } }; } }

  function currentObservatory() {
    if (window.OBS_SLUG) return String(window.OBS_SLUG);
    var p = qs();
    if (p.get('slug')) return p.get('slug');
    if (p.get('obs')) return p.get('obs');
    if (/indicador\.php/.test(location.pathname) && p.get('id')) {
      var d = String(p.get('id')).charAt(0);
      if (SLUG_BY_DIGIT[d]) return SLUG_BY_DIGIT[d];
    }
    return 'portal';
  }

  function txt(el) { return (el && el.textContent ? el.textContent : '').replace(/\s+/g, ' ').trim().slice(0, 200); }

  function send(payload) {
    payload.path = location.pathname + location.search;
    if (!payload.observatory) payload.observatory = currentObservatory();
    var body = JSON.stringify(payload);
    try {
      if (navigator.sendBeacon) {
        var blob = new Blob([body], { type: 'application/json' });
        if (navigator.sendBeacon('api/track.php', blob)) return;
      }
    } catch (e) {}
    try { fetch('api/track.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: body, keepalive: true }); } catch (e) {}
  }

  // 1) Vista de página
  send({ type: 'page_view', label: document.title });

  // 2) Interacciones (delegación en captura para no perder clics)
  document.addEventListener('click', function (e) {
    var el = e.target.closest('a, [data-bs-toggle="tab"], [data-bs-toggle="pill"], [role="tab"]');
    if (!el) return;
    var href = el.getAttribute('href') || '';

    // Indicador
    var mInd = href.match(/indicador\.php\?id=([^&#]+)/);
    if (mInd) {
      var id = decodeURIComponent(mInd[1]);
      var d = id.charAt(0);
      send({ type: 'indicator_view', object_type: 'indicator', object_id: id, label: txt(el), observatory: SLUG_BY_DIGIT[d] || currentObservatory() });
      return;
    }

    // Noticia
    var mNew = href.match(/noticia\.php\?(?:[^#]*&)?(?:id|slug)=([^&#]+)/);
    if (mNew) {
      send({ type: 'news_open', object_type: 'news', object_id: decodeURIComponent(mNew[1]), label: txt(el) });
      return;
    }

    // Pestañas (categorías, tableros, Power BI, etc.)
    var isTab = el.matches('[data-bs-toggle="tab"],[data-bs-toggle="pill"],[role="tab"]');
    if (isTab) {
      var target = el.getAttribute('data-bs-target') || href || '';
      var label = txt(el);
      var isPbi = /tablero|power\s*bi|explora/i.test(label) || /tablero|powerbi/i.test(target);
      send({
        type: isPbi ? 'powerbi_open' : 'tab_open',
        object_type: isPbi ? 'powerbi' : 'tab',
        object_id: String(target).replace('#', ''),
        label: label
      });
    }
  }, true);
})();
