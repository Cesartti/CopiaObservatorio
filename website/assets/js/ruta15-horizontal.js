/* ================= Ruta 15 - Carrusel Horizontal (auto-contenido) ================= */
(() => {
  const $  = (sel, ctx=document) => ctx.querySelector(sel);
  const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

  // Espera a que tu script original pinte los 15 pasos
  const mainOf = () => $('#ruta15') || $('main');
  const ready  = () => $$('.paso', mainOf()).length > 0;
  const waitFor = (cond, cb) => {
    if (cond()) return cb();
    const t = setInterval(() => { if (cond()) { clearInterval(t); cb(); } }, 50);
  };

  waitFor(ready, init);

  function init(){
    const main = mainOf();
    if (!main) return;

    // Evita doble montaje
    if ($('.hslider', main)) return;

    const pasosEls = $$('.paso', main);
    if (!pasosEls.length) return;

    /* 1) Integrar "Ver más" del Paso 1 dentro del contenido
          - Inserta detalles[0] al final de .paso-content
          - Quita el bloque de acciones (que tenía el botón "Ver más")
    */
    if (window.detalles && Array.isArray(window.detalles) && window.detalles[0]) {
      const paso1 = pasosEls[0];
      const content = $('.paso-content', paso1);
      if (content && !content.querySelector('.rich-block')) {
        content.insertAdjacentHTML('beforeend', window.detalles[0]);
      }
      const actions = $('.paso-actions', paso1);
      if (actions) actions.remove();
    }

    /* 2) Quitar navegación vertical (punticos a la derecha) si existe */
    const oldNav = $('nav.scroll-indicator');
    if (oldNav) oldNav.remove();

    /* 3) Calcular offset del header para fijar la altura mínima de cada .paso */
    const setHeaderOffset = () => {
      const header = $('.navbar, header .navbar, .site-header, header.sticky, header#header, .topnav') || $('header');
      const h = header ? header.offsetHeight : 0;
      document.documentElement.style.setProperty('--header-offset', `${h}px`);
    };
    setHeaderOffset();
    window.addEventListener('resize', setHeaderOffset);

    /* 4) Construir el carrusel horizontal sin modificar el contenido de cada paso */
    const slider = document.createElement('section');
    slider.className = 'hslider';

    const track = document.createElement('div');
    track.className = 'hslider__track';

    pasosEls.forEach(sec => {
      const wrap = document.createElement('div');
      wrap.className = 'hslide';
      wrap.appendChild(sec);  // mueve el nodo (conserva eventos/estilos internos)
      track.appendChild(wrap);
    });

    // Controles y dots
    const prev = document.createElement('button');
    prev.className = 'hslider__ctrl hslider__ctrl--prev';
    prev.title = 'Anterior';

    const next = document.createElement('button');
    next.className = 'hslider__ctrl hslider__ctrl--next';
    next.title = 'Siguiente';

    const dots = document.createElement('div');
    dots.className = 'hslider__dots';

    // Montaje
    slider.appendChild(prev);
    slider.appendChild(next);
    slider.appendChild(track);
    slider.appendChild(dots);
    main.replaceChildren(slider);

    /* 5) Navegación */
    const total = track.children.length;
    let index = 0;

    // Dots
    for (let i=0; i<total; i++){
      const d = document.createElement('span');
      d.className = 'hslider__dot' + (i===0 ? ' is-active' : '');
      d.setAttribute('role','button');
      d.setAttribute('aria-label', `Ir al paso ${i+1}`);
      d.addEventListener('click', () => goTo(i));
      dots.appendChild(d);
    }
    const dotEls = $$('.hslider__dot', dots);

    function sync(){
      prev.disabled = index <= 0;
      next.disabled = index >= total - 1;
      dotEls.forEach((d,i)=> d.classList.toggle('is-active', i===index));
    }

    function goTo(i){
      index = Math.max(0, Math.min(total-1, i));
      const target = track.children[index];
      target?.scrollIntoView({ behavior:'smooth', inline:'start', block:'nearest' });
      sync();
    }

    // Flechas
    prev.addEventListener('click', () => goTo(index - 1));
    next.addEventListener('click', () => goTo(index + 1));

    // Actualiza índice por scroll (drag/swipe)
    let raf;
    track.addEventListener('scroll', () => {
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => {
        const gap = parseFloat(getComputedStyle(track).gap || '0');
        const w   = track.clientWidth + gap;
        const i   = Math.round(track.scrollLeft / w);
        if (i !== index){ index = i; sync(); }
      });
    });

    // Convierte rueda vertical en desplazamiento horizontal
    track.addEventListener('wheel', (e) => {
      if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
        e.preventDefault();
        track.scrollBy({ left: e.deltaY, behavior: 'smooth' });
      }
    }, { passive: false });

    // Teclado
    window.addEventListener('keydown', (e) => {
      if (['ArrowRight','PageDown'].includes(e.key)) { e.preventDefault(); goTo(index + 1); }
      if (['ArrowLeft','PageUp'].includes(e.key))   { e.preventDefault(); goTo(index - 1); }
      if (e.key === 'Home') { e.preventDefault(); goTo(0); }
      if (e.key === 'End')  { e.preventDefault(); goTo(total - 1); }
    });

    // Primer render
    sync();
    goTo(0);
  }
})();
