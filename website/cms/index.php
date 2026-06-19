<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_login();
require_once __DIR__ . '/../config/database.php';

$cmsTitle = 'Resumen';
$cmsNav = 'home';
$pdo = cms_pdo();

$banners = 0;
$social = 0;
$news = 0;
$dash = 0;
$charts = 0;
$rag = 0;
$ragPending = 0;

if ($pdo) {
    try {
        $banners = (int) $pdo->query('SELECT COUNT(*) FROM cms_home_banners')->fetchColumn();
        $social = (int) $pdo->query('SELECT COUNT(*) FROM cms_social_posts')->fetchColumn();
        $news = (int) $pdo->query('SELECT COUNT(*) FROM news')->fetchColumn();
        $dash = (int) $pdo->query('SELECT COUNT(*) FROM cms_dashboards')->fetchColumn();
        $charts = (int) $pdo->query('SELECT COUNT(*) FROM cms_charts')->fetchColumn();
    } catch (Throwable $e) {
        // tablas aún no creadas
    }
    try {
        $rag = (int) $pdo->query('SELECT COUNT(*) FROM cms_rag_chunks')->fetchColumn();
        $ragPending = (int) $pdo->query("SELECT COUNT(*) FROM cms_rag_chunks WHERE status = 'pending'")->fetchColumn();
    } catch (Throwable $e) {
    }
}

require __DIR__ . '/includes/header.php';
?>
            <h1 class="h3 mb-3">Panel CMS</h1>
            <p class="text-muted">Gestione contenido del portal, tableros y gráficos (compatible con <a href="https://developers.google.com/chart/interactive/docs/gallery" target="_blank" rel="noopener">Google Charts</a>) sin editar código.</p>

            <?php if ($pdo): ?>
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4 col-lg-2"><div class="card shadow-sm"><div class="card-body"><div class="h4 mb-0"><?= $banners ?></div><small class="text-muted">Banners</small></div></div></div>
                    <div class="col-6 col-md-4 col-lg-2"><div class="card shadow-sm"><div class="card-body"><div class="h4 mb-0"><?= $social ?></div><small class="text-muted">Redes</small></div></div></div>
                    <div class="col-6 col-md-4 col-lg-2"><div class="card shadow-sm"><div class="card-body"><div class="h4 mb-0"><?= $news ?></div><small class="text-muted">Noticias</small></div></div></div>
                    <div class="col-6 col-md-4 col-lg-2"><div class="card shadow-sm"><div class="card-body"><div class="h4 mb-0"><?= $dash ?></div><small class="text-muted">Tableros</small></div></div></div>
                    <div class="col-6 col-md-4 col-lg-2"><div class="card shadow-sm"><div class="card-body"><div class="h4 mb-0"><?= $charts ?></div><small class="text-muted">Gráficos</small></div></div></div>
                    <?php if (auth_can('rag', false)): ?>
                    <div class="col-6 col-md-4 col-lg-2"><div class="card shadow-sm"><div class="card-body"><div class="h4 mb-0"><?= $rag ?></div><small class="text-muted">RAG · fichas</small><?php if ($ragPending > 0): ?><div class="small text-warning"><?= (int) $ragPending ?> pend. sync</div><?php endif; ?></div></div></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Flujo recomendado</h2>
                    <ol class="mb-0">
                        <li>Configure <strong>Contacto</strong> y redes.</li>
                        <li>Publique <strong>Banners</strong> y tarjetas de <strong>Instagram</strong>.</li>
                        <li>Cree <strong>Tableros</strong> por observatorio y añada <strong>gráficos</strong> cargando un Excel exportado como CSV.</li>
                        <li>Asigne <strong>noticias</strong> y revise el portal público.</li>
                        <li>Cargue <strong>conocimiento RAG</strong> por observatorio y sincronice con PostgreSQL (script Python).</li>
                    </ol>
                </div>
            </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
