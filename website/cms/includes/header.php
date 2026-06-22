<?php
/** @var string $cmsTitle */
if (!function_exists('auth_user')) {
    exit;
}
$u = auth_user();
$email = htmlspecialchars($u['email'] ?? '');
$role = htmlspecialchars($u['role'] ?? '');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($cmsTitle ?? 'CMS') ?> · Red de Observatorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .cms-sidebar { min-height: 100vh; background: #0f1f32; }
        .cms-sidebar a { color: #c8d7ea; text-decoration: none; display: block; padding: .45rem .75rem; border-radius: 8px; }
        .cms-sidebar a:hover, .cms-sidebar a.active { background: #1a3352; color: #fff; }
        .cms-main { background: #f4f7fc; min-height: 100vh; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row g-0">
        <nav class="col-md-3 col-lg-2 cms-sidebar p-3">
            <div class="text-white fw-bold mb-3"><i class="fa-solid fa-layer-group me-2"></i> CMS</div>
            <small class="text-white-50 d-block mb-3"><?= $email ?><br><span class="badge text-bg-secondary"><?= $role ?></span></small>
            <a href="<?= htmlspecialchars(app_url('website/cms/index.php')) ?>" class="<?= ($cmsNav ?? '') === 'home' ? 'active' : '' ?>"><i class="fa-solid fa-house me-2"></i>Inicio</a>
            <?php if (auth_can('banners', false)): ?>
                <a href="<?= htmlspecialchars(app_url('website/cms/banners.php')) ?>" class="<?= ($cmsNav ?? '') === 'banners' ? 'active' : '' ?>"><i class="fa-solid fa-panorama me-2"></i>Banners inicio</a>
                <a href="<?= htmlspecialchars(app_url('website/cms/microsite-hero.php')) ?>" class="<?= ($cmsNav ?? '') === 'microsite_hero' ? 'active' : '' ?>"><i class="fa-solid fa-images me-2"></i>Carrusel micrositios</a>
            <?php endif; ?>
            <?php if (auth_can('social', false)): ?>
                <a href="<?= htmlspecialchars(app_url('website/cms/social.php')) ?>" class="<?= ($cmsNav ?? '') === 'social' ? 'active' : '' ?>"><i class="fa-brands fa-instagram me-2"></i>Redes / Instagram</a>
            <?php endif; ?>
            <?php if (auth_can('contact', false)): ?>
                <a href="<?= htmlspecialchars(app_url('website/cms/contact.php')) ?>" class="<?= ($cmsNav ?? '') === 'contact' ? 'active' : '' ?>"><i class="fa-solid fa-address-book me-2"></i>Contacto</a>
            <?php endif; ?>
            <?php if (auth_can('news', false)): ?>
                <a href="<?= htmlspecialchars(app_url('website/cms/news.php')) ?>" class="<?= ($cmsNav ?? '') === 'news' ? 'active' : '' ?>"><i class="fa-solid fa-newspaper me-2"></i>Noticias</a>
                <a href="<?= htmlspecialchars(app_url('website/cms/boletines.php')) ?>" class="<?= ($cmsNav ?? '') === 'boletines' ? 'active' : '' ?>"><i class="fa-solid fa-file-pdf me-2"></i>Boletines</a>
            <?php endif; ?>
            <?php if (auth_can('charts', false)): ?>
                <a href="<?= htmlspecialchars(app_url('website/cms/tableros.php')) ?>" class="<?= ($cmsNav ?? '') === 'charts' ? 'active' : '' ?>"><i class="fa-solid fa-chart-column me-2"></i>Tableros y gráficos</a>
                <a href="<?= htmlspecialchars(app_url('website/cms/indicators.php')) ?>" class="<?= ($cmsNav ?? '') === 'indicators' ? 'active' : '' ?>"><i class="fa-solid fa-list-check me-2"></i>Indicadores · Hoja de vida</a>
                <a href="<?= htmlspecialchars(app_url('website/cms/indicadores-carga.php')) ?>" class="<?= ($cmsNav ?? '') === 'ind_carga' ? 'active' : '' ?>"><i class="fa-solid fa-file-arrow-up me-2"></i>Cargar / actualizar indicadores</a>
            <?php endif; ?>
            <?php if (auth_can('tabs', false)): ?>
                <a href="<?= htmlspecialchars(app_url('website/cms/tabs.php')) ?>" class="<?= ($cmsNav ?? '') === 'tabs' ? 'active' : '' ?>"><i class="fa-solid fa-table-columns me-2"></i>Pestañas micrositios</a>
            <?php endif; ?>
            <?php if (auth_can('rag', false)): ?>
                <a href="<?= htmlspecialchars(app_url('website/cms/rag_knowledge.php')) ?>" class="<?= ($cmsNav ?? '') === 'rag' ? 'active' : '' ?>"><i class="fa-solid fa-robot me-2"></i>Asistente · conocimiento RAG</a>
            <?php endif; ?>
            <?php if (auth_can('users', false)): ?>
                <a href="<?= htmlspecialchars(app_url('website/cms/users.php')) ?>" class="<?= ($cmsNav ?? '') === 'users' ? 'active' : '' ?>"><i class="fa-solid fa-users me-2"></i>Usuarios</a>
            <?php endif; ?>
            <hr class="border-secondary">
            <a href="<?= htmlspecialchars(app_url('website/index.php')) ?>"><i class="fa-solid fa-globe me-2"></i>Ver portal</a>
            <a href="<?= htmlspecialchars(app_url('website/cms/estadisticas.php')) ?>" class="<?= ($cmsNav ?? '') === 'estadisticas' ? 'active' : '' ?>"><i class="fa-solid fa-chart-pie me-2"></i>Estadísticas y encuesta</a>
            <a href="<?= htmlspecialchars(app_url('website/dashboard/index.php')) ?>"><i class="fa-solid fa-chart-line me-2"></i>Dashboard visitas</a>
            <a href="<?= htmlspecialchars(app_url('website/admin/content/index.php')) ?>"><i class="fa-solid fa-code me-2"></i>JSON legacy</a>
            <a href="<?= htmlspecialchars(app_url('website/admin/logout.php')) ?>"><i class="fa-solid fa-right-from-bracket me-2"></i>Salir</a>
        </nav>
        <main class="col-md-9 col-lg-10 cms-main p-4">
            <?php
            if (!function_exists('cms_pdo')) {
                require_once __DIR__ . '/../../config/database.php';
            }
            if (!cms_pdo()) {
                $det = cms_last_db_error();
                ?>
                <div class="alert alert-warning border-0 shadow-sm mb-3">
                    <strong>No hay conexión a MySQL.</strong>
                    <?php if ($det): ?>
                        <br><small class="font-monospace text-break d-block mt-1"><?= htmlspecialchars($det) ?></small>
                    <?php endif; ?>
                    <hr class="my-2">
                    <p class="small mb-1"><strong>Qué hacer (XAMPP):</strong></p>
                    <ol class="small mb-0 ps-3">
                        <li>En <strong>phpMyAdmin</strong>, pestaña SQL: ejecute <code>database/setup_xampp.sql</code> (crea la base <code>observatorio_boyaca</code>).</li>
                        <li>Importe <code>database/schema.sql</code> y, si aún no lo hizo, <code>database/migrations/002_cms_core.sql</code>.</li>
                        <li>Si usa <code>root</code> sin contraseña, puede copiar <code>website/config/database.local.php.example</code> a <code>database.local.php</code> y ajustar.</li>
                    </ol>
                </div>
                <?php
            }
            ?>
