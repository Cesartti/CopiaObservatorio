<?php
/**
 * Modal de la "Encuesta opcional", compartido por el portal y los micrositios.
 *
 * Define antes de incluir (opcional):
 *   $surveyContext  Identificador del origen (p.ej. 'portal' o 'microsite_economico').
 *                   Se envía como page_context para asociar la respuesta al observatorio.
 *
 * Requiere las funciones cms_survey_* (lib/visit_tracking.php).
 */
if (!function_exists('cms_survey_age_ranges')) {
    require_once __DIR__ . '/../lib/visit_tracking.php';
}
$surveyContext = isset($surveyContext) && $surveyContext !== '' ? $surveyContext : 'portal';
?>
<div class="modal fade" id="portalSurveyModal" tabindex="-1" aria-labelledby="portalSurveyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content portal-survey-modal">
            <div class="modal-header">
                <h2 class="modal-title h5" id="portalSurveyModalLabel">Encuesta opcional</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Sus respuestas son anónimas y nos ayudan a mejorar el portal. Puede cerrar esta ventana en cualquier momento.</p>
                <form id="portalSurveyForm" novalidate>
                    <input type="hidden" name="page_context" value="<?= htmlspecialchars($surveyContext) ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="surveyAge">1. ¿Cuál es su rango de edad?</label>
                        <select class="form-select" id="surveyAge" name="age_range" required>
                            <option value="" selected disabled>Seleccione…</option>
                            <?php foreach (cms_survey_age_ranges() as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="surveySector">2. ¿A qué sector pertenece?</label>
                        <select class="form-select" id="surveySector" name="sector" required>
                            <option value="" selected disabled>Seleccione…</option>
                            <?php foreach (cms_survey_sectors() as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="surveyFreq">3. ¿Con qué frecuencia utiliza este portal?</label>
                        <select class="form-select" id="surveyFreq" name="visit_frequency" required>
                            <option value="" selected disabled>Seleccione…</option>
                            <?php foreach (cms_survey_visit_frequency() as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="small text-danger mb-2 d-none" id="portalSurveyError" role="alert"></p>
                    <p class="small text-success mb-0 d-none" id="portalSurveyOk" role="status"></p>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button type="submit" class="btn btn-primary" id="portalSurveySubmit">Enviar respuestas</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar sin enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
