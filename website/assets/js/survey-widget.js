/* Encuesta opcional.
   - Se abre automáticamente la PRIMERA vez que entra el visitante (una sola vez).
   - Al enviar, se cierra y NO vuelve a aparecer (se ocultan también los accesos del menú).
   - Asocia la respuesta al observatorio de origen (page_context oculto). */
(function () {
  var DONE = 'obs_survey_done';            // ya respondió -> nunca más
  var SEEN = 'obs_survey_seen';            // ya se le mostró automáticamente
  var LEGACY = 'obs_portal_survey_thanks'; // compatibilidad con versión anterior

  function get(k) { try { return localStorage.getItem(k) === '1'; } catch (e) { return false; } }
  function set(k) { try { localStorage.setItem(k, '1'); } catch (e) {} }
  function isDone() { return get(DONE) || get(LEGACY); }
  function triggers() { return document.querySelectorAll('[data-bs-target="#portalSurveyModal"]'); }
  function hideTriggers() { triggers().forEach(function (t) { t.style.display = 'none'; }); }

  var modalEl = document.getElementById('portalSurveyModal');
  var form = document.getElementById('portalSurveyForm');
  if (!modalEl) return;

  // Si ya respondió: ocultar accesos a la encuesta y no hacer nada más.
  if (isDone()) { hideTriggers(); return; }

  // Abrir automáticamente en la primera visita (una sola vez por navegador).
  setTimeout(function () {
    if (!window.bootstrap || get(SEEN)) return;
    try { bootstrap.Modal.getOrCreateInstance(modalEl).show(); set(SEEN); } catch (e) {}
  }, 900);

  if (!form) return;
  var submit = document.getElementById('portalSurveySubmit');
  var err = document.getElementById('portalSurveyError');
  var ok = document.getElementById('portalSurveyOk');
  if (!submit) return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    if (err) { err.classList.add('d-none'); err.textContent = ''; }
    if (ok) { ok.classList.add('d-none'); ok.textContent = ''; }

    var fd = new FormData(form);
    var age = fd.get('age_range');
    var gender = fd.get('gender');
    var sector = fd.get('sector');
    var freq = fd.get('visit_frequency');
    var ctx = fd.get('page_context') || 'portal';
    if (!age || !gender || !sector || !freq) {
      if (err) { err.textContent = 'Por favor responda todas las preguntas.'; err.classList.remove('d-none'); }
      return;
    }

    submit.disabled = true;
    try {
      var res = await fetch('api/survey.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ age_range: age, gender: gender, sector: sector, visit_frequency: freq, page_context: ctx })
      });
      var json = await res.json().catch(function () { return {}; });
      if (json && json.ok) {
        set(DONE);
        if (ok) { ok.textContent = '¡Gracias! Sus respuestas fueron registradas.'; ok.classList.remove('d-none'); }
        // Cerrar tras un momento y ocultar definitivamente los accesos.
        setTimeout(function () {
          try { bootstrap.Modal.getOrCreateInstance(modalEl).hide(); } catch (e) {}
          hideTriggers();
        }, 1200);
      } else {
        if (err) { err.textContent = (json && json.error) || 'No se pudo enviar. Intente más tarde.'; err.classList.remove('d-none'); }
        submit.disabled = false;
      }
    } catch (_e) {
      if (err) { err.textContent = 'Error de red. Intente más tarde.'; err.classList.remove('d-none'); }
      submit.disabled = false;
    }
  });
})();
