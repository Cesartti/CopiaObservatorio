<?php

/**
 * Widget flotante: iframe al asistente Streamlit (AsistenteOllama).
 * Opcional: defina $assistant_obs_slug (string) antes del include para pasar contexto al iframe.
 */

$assistantCfg = require __DIR__ . '/../config/assistant.php';
if (empty($assistantCfg['enabled']) || empty($assistantCfg['iframe_url'])) {
    return;
}

$baseUrl = rtrim((string) $assistantCfg['iframe_url'], '/');
$query = isset($assistantCfg['iframe_query']) ? trim((string) $assistantCfg['iframe_query']) : '';
$iframeSrc = $baseUrl;
if ($query !== '') {
    $iframeSrc .= (strpos($baseUrl, '?') !== false ? '&' : '?') . $query;
}

$obsSlug = isset($assistant_obs_slug) ? (string) $assistant_obs_slug : '';
if ($obsSlug !== '' && preg_match('/^[a-z0-9_-]{1,40}$/', $obsSlug)) {
    $iframeSrc .= (strpos($iframeSrc, '?') !== false ? '&' : '?') . 'obs=' . rawurlencode($obsSlug);
}

$newTabUrl = htmlspecialchars($baseUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$iframeSrcEsc = htmlspecialchars($iframeSrc, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$awCssV = @filemtime(__DIR__ . '/../assets/css/modern/assistant-widget.css') ?: 1;
$awJsV  = @filemtime(__DIR__ . '/../assets/js/modern/assistant-widget.js') ?: 1;
?>
<link rel="stylesheet" href="assets/css/modern/assistant-widget.css?v=<?= $awCssV ?>">
<div id="obsAssistantPanel" class="obs-assistant-panel" aria-hidden="true" role="dialog" aria-labelledby="obsAssistantTitle">
    <div class="obs-assistant-panel__head">
        <h2 id="obsAssistantTitle">Asistente del observatorio</h2>
        <div class="obs-assistant-panel__actions">
            <a href="<?= $newTabUrl ?>" target="_blank" rel="noopener noreferrer">Abrir en pestaña</a>
            <button type="button" id="obsAssistantClose" aria-label="Cerrar asistente">Cerrar</button>
        </div>
    </div>
    <div class="obs-assistant-panel__frame-wrap">
        <iframe
            id="obsAssistantFrame"
            class="obs-assistant-panel__frame"
            title="Asistente inteligente — indicadores Boyacá"
            src="<?= $iframeSrcEsc ?>"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
    </div>
    <p class="obs-assistant-panel__note">Si el asistente no aparece, es posible que el servicio esté temporalmente fuera de línea. Vuelva a intentarlo más tarde o use <strong>«Abrir en pestaña»</strong>.</p>
    <?php /* Nota técnica para el operador (no visible para la ciudadanía):
       Si no carga: ejecute AsistenteOllama/run_asistente.bat (o `streamlit run app.py` en esa carpeta)
       y verifique que Ollama esté corriendo con el modelo configurado en AsistenteOllama/.env.
       Si usa otra URL, ajústela en website/config/assistant.local.php (iframe_url). */ ?>
</div>
<div id="obsAssistantCta" class="obs-assistant-cta" role="button" tabindex="0" aria-label="Abrir el asistente del observatorio">
    <span class="obs-assistant-cta__text">¿Tienes dudas? Pregunta a nuestro <strong>asistente virtual</strong></span>
    <button type="button" class="obs-assistant-cta__close" aria-label="Ocultar este mensaje">&times;</button>
</div>
<img src="assets/img/robot-asistente.png" alt="Asistente virtual" class="obs-assistant-robot" id="obsAssistantRobot" title="Abrir asistente" onclick="document.getElementById('obsAssistantFab').click()">
<button type="button" id="obsAssistantFab" class="obs-assistant-fab" aria-expanded="false" aria-controls="obsAssistantPanel" title="Abrir asistente">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>
    <span class="obs-assistant-sr-only">Abrir asistente de chat</span>
</button>
<script src="assets/js/modern/assistant-widget.js?v=<?= $awJsV ?>" defer></script>
