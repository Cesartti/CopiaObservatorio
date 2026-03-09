<?php
require_once __DIR__ . '/../auth/bootstrap.php';
auth_require_login();

$file = __DIR__ . '/../../data/content.json';
$message = '';
$error = '';

if (!file_exists($file)) {
    file_put_contents($file, json_encode(['general_news' => [], 'dimension_news' => [], 'featured_indicators' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$current = json_decode(file_get_contents($file), true);
if (!is_array($current)) {
    $current = ['general_news' => [], 'dimension_news' => [], 'featured_indicators' => []];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = $_POST['content_json'] ?? '';
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        file_put_contents($file, json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $current = $decoded;
        $message = 'Contenido actualizado correctamente.';
    } else {
        $error = 'JSON inválido. Verifique formato.';
    }
}

$pretty = json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de contenido</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Administración de contenido (Noticias/Indicadores)</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(app_url('website/dashboard/index.php')); ?>">Volver al dashboard</a>
  </div>
  <p class="text-muted">Edite el JSON para actualizar noticias generales, noticias por dimensión e indicadores destacados del nuevo frontend.</p>

  <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="post">
    <label class="form-label" for="content_json">Archivo de contenido (JSON)</label>
    <textarea id="content_json" name="content_json" class="form-control" rows="28" spellcheck="false"><?= htmlspecialchars($pretty) ?></textarea>
    <button class="btn btn-primary mt-3" type="submit">Guardar cambios</button>
  </form>
</div>
</body>
</html>
