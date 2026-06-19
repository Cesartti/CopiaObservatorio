<?php

/**
 * Asistente (Streamlit en AsistenteOllama). Ejecutar: AsistenteOllama\run_asistente.bat
 * Requiere además Ollama corriendo (https://ollama.com) con el modelo de AsistenteOllama/.env.
 *
 * Opcional: copie assistant.local.php.example a assistant.local.php y ajuste la URL.
 */

$defaults = [
    'enabled' => true,
    /** URL del servidor Streamlit (sin barra final). Vacío = widget oculto. */
    'iframe_url' => getenv('OBS_ASSISTANT_URL') ?: '',
    /** Parámetros extra en la URL del iframe (opcional; depende de la versión de Streamlit). */
    'iframe_query' => '',
];

$localFile = __DIR__ . '/assistant.local.php';
if (is_readable($localFile)) {
    $local = require $localFile;
    if (is_array($local)) {
        $defaults = array_replace($defaults, $local);
    }
}

if ($defaults['iframe_url'] === '') {
    // Misma forma que suele usarse al abrir XAMPP (localhost) evita bloqueos raros frente a 127.0.0.1
    $defaults['iframe_url'] = 'http://localhost:8501';
}

return $defaults;
