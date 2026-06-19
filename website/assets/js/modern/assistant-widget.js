(function () {
  const fab = document.getElementById('obsAssistantFab');
  const panel = document.getElementById('obsAssistantPanel');
  const closeBtn = document.getElementById('obsAssistantClose');
  const cta = document.getElementById('obsAssistantCta');
  const ctaClose = cta ? cta.querySelector('.obs-assistant-cta__close') : null;
  // sessionStorage: si la persona la cierra, queda oculta solo durante esa
  // visita; en la próxima sesión la invitación vuelve a aparecer.
  const CTA_KEY = 'obs_assistant_cta_dismissed_v2';
  if (!fab || !panel) return;

  function hideCta() {
    if (cta) cta.classList.add('is-hidden');
  }

  function setOpen(open) {
    fab.setAttribute('aria-expanded', open ? 'true' : 'false');
    panel.classList.toggle('is-open', open);
    panel.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (open) hideCta();
  }

  if (cta) {
    try {
      if (sessionStorage.getItem(CTA_KEY) === '1') hideCta();
    } catch (e) { /* ignore */ }

    cta.addEventListener('click', function (e) {
      if (ctaClose && (e.target === ctaClose || ctaClose.contains(e.target))) return;
      setOpen(true);
    });
    cta.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        setOpen(true);
      }
    });
    if (ctaClose) {
      ctaClose.addEventListener('click', function (e) {
        e.stopPropagation();
        hideCta();
        try { sessionStorage.setItem(CTA_KEY, '1'); } catch (err) { /* ignore */ }
      });
    }
  }

  fab.addEventListener('click', function () {
    setOpen(!panel.classList.contains('is-open'));
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', function () {
      setOpen(false);
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && panel.classList.contains('is-open')) {
      setOpen(false);
      fab.focus();
    }
  });
})();
