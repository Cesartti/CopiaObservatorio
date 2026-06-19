<?php

/**
 * Agrega la campaña "Casas de acogida — Secretaría de Salud" al final del
 * contenido de la sección CMS "Campañas y publicaciones" del observatorio
 * de género (id 22). Idempotente: no la duplica si ya existe.
 */
require_once __DIR__ . '/../website/config/database.php';

$pdo = cms_pdo();
if (!$pdo) {
    fwrite(STDERR, "Sin conexión a BD\n");
    exit(1);
}

$body = (string) $pdo->query('SELECT body_html FROM cms_microsite_sections WHERE id = 22')->fetchColumn();
if (strpos($body, 'saludygenerosecretariasaludboy@gmail.com') !== false) {
    echo "La campaña ya existe; sin cambios.\n";
    exit(0);
}

$campana = <<<HTML

<div class="card" style="border-left:6px solid #7d2d91;border-radius:14px;margin-top:1.6rem;box-shadow:0 8px 22px rgba(0,0,0,.09)">
  <div class="card-body" style="padding:1.4rem 1.5rem">
    <h4 style="color:#7d2d91;font-weight:700;display:flex;align-items:center;gap:.6rem"><i class="fa-solid fa-house-chimney-medical"></i> Casas de acogida — Secretaría de Salud</h4>
    <p style="margin-bottom:.7rem">La Secretaría de Salud brinda fortalecimiento institucional para atender a mujeres víctimas de violencias basadas en género, a sus hijos e hijas y personas dependientes.</p>
    <p style="margin-bottom:.7rem"><strong>¿Quiere certificar su casa de acogida?</strong> Realice su solicitud a la Secretaría de Salud al correo <a href="mailto:saludygenerosecretariasaludboy@gmail.com">saludygenerosecretariasaludboy@gmail.com</a>.</p>
    <p style="margin-bottom:0;color:#6b7280"><i class="fa-solid fa-chalkboard-user"></i> Se brindan capacitaciones y asistencia técnica a los diferentes municipios del departamento.</p>
  </div>
</div>
HTML;

$st = $pdo->prepare('UPDATE cms_microsite_sections SET body_html = CONCAT(COALESCE(body_html, ""), ?) WHERE id = 22');
$st->execute([$campana]);
echo "Campaña agregada a la sección Campañas y publicaciones (id 22).\n";
