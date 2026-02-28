/* indic-genero.js
   - Modales de PDF y Tableros (original)
   - Sección "Barreras de acceso" (chips/subchips, FIX: delegación robusta)
*/

$(document).ready(function () {
  // =========================
  // ===  PDF MODAL (ORIG) ===
  // =========================
  $('.pdf-link').on('click', function (e) {
    if ($(this).attr('target') === '_blank') return;
    e.preventDefault();
    const pdfUrl = $(this).attr('href');
    const title = $(this).data('title') || "Visualizador de documento";
    $('#pdfIframe').attr('src', pdfUrl);
    $('#pdfModalLabel').text(title);
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('pdfModal'));
    modal.show();
  });

  $('#pdfModal').on('hidden.bs.modal', function () {
    $('#pdfIframe').attr('src', '');
  });

  // ==============================
  // ===  TABLEROS MODAL (ORIG) ===
  // ==============================
  $('.tablero-card, .ver-tablero-btn').on('click', function () {
    var card = $(this).closest('.tablero-card');
    if (!card.length) return;
    const iframeUrl = card.data('iframe');
    const title = card.data('title');
    if (!iframeUrl) return;
    $('#tableroIframe').attr('src', iframeUrl);
    $('#tableroModalLabel').text(title);
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('tableroModal'));
    modal.show();
  });

  $('#tableroModal').on('hidden.bs.modal', function () {
    $('#tableroIframe').attr('src', '');
  });

  // =======================================================
  // ===  BARRERAS DE ACCESO: chips, subchips y hash    ===
  // =======================================================
  (function setupBarrerasAcceso(){
    const $root = $('#barreras-acceso').first();
    if (!$root.length) return;

    // ---------- helpers ----------
    const scrollToRoot = () => {
      const top = $root.offset().top - 12;
      $('html, body').stop(true).animate({ scrollTop: top }, 300);
    };
    const updateHash = (sec) => {
      try {
        const base = window.location.href.split('#')[0];
        history.replaceState(null, '', `${base}#barreras-${sec}`);
      } catch(_) {}
    };

    const switchSection = (raw) => {
      const sec = String(raw || '').trim().toLowerCase();

      // activar chip principal
      $root.find('.chip[data-target]').each(function(){
        const on = String($(this).data('target')).trim().toLowerCase() === sec;
        $(this).toggleClass('is-active', on).attr('aria-selected', on ? 'true' : 'false');
      });

      // mostrar/ocultar secciones
      $root.find('.mod-section').each(function(){
        const isThis = String($(this).data('sec')).trim().toLowerCase() === sec;
        $(this).toggleClass('is-hidden', !isThis);
      });

      // preparar subchips si entramos a "ejemplos"
      if (sec === 'ejemplos') {
        const $wrap = $root.find('[data-sec="ejemplos"] .two-col__text').first();
        if ($wrap.length) {
          const firstSub = String($wrap.find('.k-subchips .chip[data-sub]').first().data('sub') || 'adm').toLowerCase();
          switchSub($wrap, firstSub);
        }
      }

      updateHash(sec);
      scrollToRoot();
    };

    const switchSub = ($scope, rawKey) => {
      const key = String(rawKey || '').trim().toLowerCase();
      $scope.find('.k-subchips .chip[data-sub]').each(function(){
        const on = String($(this).data('sub')).trim().toLowerCase() === key;
        $(this).toggleClass('is-active', on).attr('aria-selected', on ? 'true' : 'false');
      });
      $scope.find('[data-subsec]').each(function(){
        const isThis = String($(this).data('subsec')).trim().toLowerCase() === key;
        $(this).toggleClass('is-hidden', !isThis);
      });
    };

    // ---------- listeners (delegación robusta) ----------
    // Chips principales: cualquier .chip con data-target (excepto si también es subchip)
    $(document).on('click', '#barreras-acceso .chip[data-target]', function(e){
      // ignorar si es un subchip (tiene data-sub)
      if ($(this).is('[data-sub]')) return;
      e.preventDefault(); e.stopPropagation();
      switchSection($(this).data('target'));
    });

    // Subchips (dentro de “Ejemplos”)
    $(document).on('click', '#barreras-acceso .k-subchips .chip[data-sub]', function(e){
      e.preventDefault(); e.stopPropagation();
      const $wrap = $(this).closest('.two-col__text');
      switchSub($wrap, $(this).data('sub'));
    });

    // Accesibilidad: teclado (chips principales)
    $(document).on('keydown', '#barreras-acceso .chip[data-target]', function(e){
      if ($(this).is('[data-sub]')) return; // no para subchips
      if (!['ArrowRight','ArrowLeft','Home','End'].includes(e.key)) return;
      e.preventDefault();
      const $chips = $root.find('.chip[data-target]').filter(function(){ return !$(this).is('[data-sub]'); });
      let i = $chips.index(this);
      if (e.key === 'ArrowRight') i = (i + 1) % $chips.length;
      if (e.key === 'ArrowLeft')  i = (i - 1 + $chips.length) % $chips.length;
      if (e.key === 'Home')       i = 0;
      if (e.key === 'End')        i = $chips.length - 1;
      $chips.eq(i).focus().trigger('click');
    });

    // Accesibilidad: teclado (subchips)
    $(document).on('keydown', '#barreras-acceso .k-subchips .chip[data-sub]', function(e){
      if (!['ArrowRight','ArrowLeft','Home','End'].includes(e.key)) return;
      e.preventDefault();
      const $wrap  = $(this).closest('.two-col__text');
      const $chips = $wrap.find('.k-subchips .chip[data-sub]');
      let i = $chips.index(this);
      if (e.key === 'ArrowRight') i = (i + 1) % $chips.length;
      if (e.key === 'ArrowLeft')  i = (i - 1 + $chips.length) % $chips.length;
      if (e.key === 'Home')       i = 0;
      if (e.key === 'End')        i = $chips.length - 1;
      $chips.eq(i).focus().trigger('click');
    });

    // ---------- estado inicial + hash ----------
    (function initState(){
      // ocultar todo y mostrar "¿Qué es?"
      $root.find('.mod-section').addClass('is-hidden');
      $root.find('[data-sec="quees"]').removeClass('is-hidden');

      // marcar chip activo por defecto
      $root.find('.chip[data-target]').removeClass('is-active').attr('aria-selected','false');
      $root.find('.chip[data-target="quees"]').addClass('is-active').attr('aria-selected','true');

      // subchips por defecto en "Ejemplos"
      const $ejWrap = $root.find('[data-sec="ejemplos"] .two-col__text').first();
      if ($ejWrap.length){
        $ejWrap.find('[data-subsec]').addClass('is-hidden');
        $ejWrap.find('[data-subsec="adm"]').removeClass('is-hidden');
        $ejWrap.find('.k-subchips .chip').removeClass('is-active').attr('aria-selected','false');
        $ejWrap.find('.k-subchips .chip[data-sub="adm"]').addClass('is-active').attr('aria-selected','true');
      }

      // hash inicial: #barreras-leyes / #barreras-ejemplos / #barreras-quehacer
      const m = (location.hash || '').toLowerCase().match(/^#barreras-([a-z0-9\-]+)$/);
      const sec = m && m[1] ? m[1] : 'quees';
      if ($root.find(`.chip[data-target]`).filter(function(){
          return String($(this).data('target')).trim().toLowerCase() === sec;
        }).length){
        switchSection(sec);
      }
    })();
  })();

  // =======================================
// === MÓDULO DE CAMPAÑAS EN ACORDEONES ===
// =======================================
(function setupCampanasModules(){
  const PLACEHOLDER = "https://placehold.co/800x600?text=Imagen";

  $('.campanas-module').each(function init(){
    const $root = $(this);
    if ($root.data('ready')) return;
    $root.data('ready', true);

    // refs
    const $strategies = $root.find('.strategies');
    const $items      = $root.find('.items');
    const $view       = $root.find('.campaign-view');
    const $img        = $view.find('img');
    const $title      = $view.find('.c-title');
    const $body       = $view.find('.c-body');
    const $credit     = $view.find('.c-credit');
    const $pdfBtn     = $view.find('.pdf-link');

    let DATA = [], CUR_S = null, CUR_I = null;

    // renderers
    const renderStrategies = ()=>{
      $strategies.empty();
      DATA.forEach(({id,title})=>{
        $('<button class="chip" role="tab">')
          .attr({'data-strategy': id, 'aria-selected': 'false'})
          .text(title)
          .appendTo($strategies);
      });
    };

    const renderItems = (sid)=>{
      $items.empty();
      const s = DATA.find(x=>x.id===sid);
      if(!s) return;
      s.items.forEach(({id,title})=>{
        $('<button class="chip" role="tab">')
          .attr({'data-item': id, 'aria-selected': 'false'})
          .text(title)
          .appendTo($items);
      });
    };

    const showPiece = (sid, iid)=>{
      const s = DATA.find(x=>x.id===sid);
      if(!s) return;
      const p = s.items.find(x=>x.id===iid) || s.items[0];
      if(!p) return;

      // activar chips
      $strategies.find('.chip').each(function(){
        const on = $(this).data('strategy') === sid;
        $(this).toggleClass('is-active', on).attr('aria-selected', on ? 'true' : 'false');
      });
      $items.find('.chip').each(function(){
        const on = $(this).data('item') === p.id;
        $(this).toggleClass('is-active', on).attr('aria-selected', on ? 'true' : 'false');
      });

      // pintar contenido
      $img.attr({
        src: p.image || PLACEHOLDER,
        alt: p.title || ''
      });
      $title.text(p.title || '');
      $body.html(p.html || '');
      $credit.text(p.credit || '');
      $pdfBtn.attr({
        href: p.pdf || '#',
        'data-title': p.title || 'Documento'
      });

      CUR_S = sid; CUR_I = p.id;
    };

    const switchStrategy = (sid)=>{
      renderItems(sid);
      const first = (DATA.find(x=>x.id===sid)?.items?.[0]?.id) || null;
      showPiece(sid, first);
    };

    // eventos (delegados)
    $(document).on('click', `#${$root.attr('id')} .strategies .chip[data-strategy]`, function(e){
      e.preventDefault(); e.stopPropagation();
      switchStrategy($(this).data('strategy'));
    });
    $(document).on('click', `#${$root.attr('id')} .items .chip[data-item]`, function(e){
      e.preventDefault(); e.stopPropagation();
      showPiece(CUR_S, $(this).data('item'));
    });

    // accesibilidad teclado
    const keyNav = (selector) => {
      $(document).on('keydown', `#${$root.attr('id')} ${selector}`, function(e){
        if(!['ArrowRight','ArrowLeft','Home','End'].includes(e.key)) return;
        e.preventDefault();
        const $chips = $root.find(selector);
        let i = $chips.index(this);
        if(e.key==='ArrowRight') i = (i+1) % $chips.length;
        if(e.key==='ArrowLeft')  i = (i-1+$chips.length) % $chips.length;
        if(e.key==='Home')       i = 0;
        if(e.key==='End')        i = $chips.length - 1;
        $chips.eq(i).focus().trigger('click');
      });
    };
    keyNav('.strategies .chip[data-strategy]');
    keyNav('.items .chip[data-item]');

    // cargar datos
    const src = $root.data('src');
    const initWith = (arr)=>{
      DATA = (arr||[]).filter(s => s && s.id && s.items && s.items.length);
      if(!DATA.length){
        $root.append('<p>No hay piezas cargadas.</p>');
        return;
      }
      renderStrategies();
      switchStrategy(DATA[0].id);
    };

    if (src){
      $.getJSON(src).done(initWith).fail(()=> initWith([]));
    } else {
      // soporte por variable global opcional: window[id.toUpperCase() + '_DATA']
      const key = ($root.attr('id') || '').toUpperCase() + '_DATA';
      initWith(window[key] || []);
    }
  });
})();

});
