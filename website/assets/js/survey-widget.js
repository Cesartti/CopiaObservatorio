/* Encuesta opcional: envío del formulario asociando el observatorio de origen.
   Funciona en el portal y en los micrositios (lee page_context del input oculto). */
(function () {
  var form = document.getElementById('portalSurveyForm');
  var submit = document.getElementById('portalSurveySubmit');
  if (!form || !submit) return;

  var err = document.getElementById('portalSurveyError');
  var ok = document.getElementById('portalSurveyOk');

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    if (err) { err.classList.add('d-none'); err.textContent = ''; }
    if (ok) { ok.classList.add('d-none'); ok.textContent = ''; }

    var fd = new FormData(form);
    var age = fd.get('age_range');
    var sector = fd.get('sector');
    var freq = fd.get('visit_frequency');
    var ctx = fd.get('page_context') || 'portal';
    if (!age || !sector || !freq) {
      if (err) { err.textContent = 'Por favor responda las tres preguntas.'; err.classList.remove('d-none'); }
      return;
    }

    submit.disabled = true;
    try {
      var res = await fetch('api/survey.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ age_range: age, sector: sector, visit_frequency: freq, page_context: ctx })
      });
      var json = await res.json().catch(function () { return {}; });
      if (json && json.ok) {
        if (ok) { ok.textContent = '¡Gracias! Sus respuestas fueron registradas.'; ok.classList.remove('d-none'); }
        form.reset();
        try { localStorage.setItem('obs_portal_survey_thanks', '1'); } catch (_e) {}
      } else {
        if (err) { err.textContent = (json && json.error) || 'No se pudo enviar. Intente más tarde.'; err.classList.remove('d-none'); }
      }
    } catch (_e) {
      if (err) { err.textContent = 'Error de red. Intente más tarde.'; err.classList.remove('d-none'); }
    }
    submit.disabled = false;
  });
})();
