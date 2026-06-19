<?php
/**
 * Pie de página de contacto compartido por todas las páginas públicas.
 * Usa $contact si la página ya lo cargó; de lo contrario lo obtiene del
 * CMS con fallback a data/content.json.
 */
if (!isset($contact) || !is_array($contact) || $contact === []) {
    require_once dirname(__DIR__) . '/lib/cms_public_content.php';
    $contact = [];
    try {
        require_once dirname(__DIR__) . '/config/database.php';
        $contact = cms_contact_payload(function_exists('cms_pdo') ? cms_pdo() : null);
    } catch (Throwable $e) {
        $contact = cms_contact_payload(null);
    }
}
if (!empty($contact)):
?>
<style>
    .site-footer{background:linear-gradient(120deg,#0b2744,#0f1f32);color:#e8eef5}
    .site-footer p{margin-bottom:.35rem}
    .site-footer .footer-social-btn{width:42px;height:42px;padding:0;display:inline-flex;align-items:center;justify-content:center}
    .site-footer .footer-brand{display:flex;align-items:center;justify-content:center}
    .site-footer .footer-logo{height:118px;width:auto;max-width:100%}
    @media (min-width:992px){.site-footer .footer-brand{justify-content:flex-start;border-right:1px solid rgba(255,255,255,.12);padding-right:1.25rem}}
    @media (max-width:991px){.site-footer .footer-logo{height:96px;margin-bottom:.5rem}}
</style>
<footer class="site-footer py-5 mt-2">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-3 col-md-12 footer-brand">
                <img src="assets/svg/logo-gobernacion-blanco.png" alt="Gobernación de Boyacá · Secretaría de Planeación" class="footer-logo" loading="lazy">
            </div>
            <div class="col-lg-5">
                <h2 class="h5 text-white mb-3">Contáctenos</h2>
                <?php if (!empty($contact['institution'])): ?>
                    <p class="mb-2 text-white-50"><?= htmlspecialchars($contact['institution']) ?></p>
                <?php endif; ?>
                <?php if (!empty($contact['address'])): ?>
                    <p class="mb-2"><i class="fa-solid fa-location-dot me-2" aria-hidden="true"></i><?= htmlspecialchars($contact['address']) ?></p>
                <?php endif; ?>
                <?php if (!empty($contact['phone'])): ?>
                    <p class="mb-2"><i class="fa-solid fa-phone me-2" aria-hidden="true"></i><a class="link-light link-underline-opacity-0" href="tel:<?= preg_replace('/\s+/', '', (string) $contact['phone']) ?>"><?= htmlspecialchars($contact['phone']) ?></a></p>
                <?php endif; ?>
                <?php if (!empty($contact['email'])): ?>
                    <p class="mb-2"><i class="fa-solid fa-envelope me-2" aria-hidden="true"></i><a class="link-light link-underline-opacity-25 link-underline-opacity-100-hover" href="mailto:<?= htmlspecialchars($contact['email']) ?>"><?= htmlspecialchars($contact['email']) ?></a></p>
                <?php endif; ?>
                <?php if (!empty($contact['hours'])): ?>
                    <p class="mb-0 small text-white-50"><i class="fa-regular fa-clock me-2" aria-hidden="true"></i><?= htmlspecialchars($contact['hours']) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <h3 class="h6 text-white mb-3">Síganos</h3>
                <div class="d-flex flex-wrap gap-2 footer-social">
                    <?php
                    $social = is_array($contact['social'] ?? null) ? $contact['social'] : [];
                    $map = [
                        'facebook' => ['fa-brands fa-facebook-f', 'Facebook'],
                        'instagram' => ['fa-brands fa-instagram', 'Instagram'],
                        'x' => ['fa-brands fa-x-twitter', 'X'],
                        'youtube' => ['fa-brands fa-youtube', 'YouTube'],
                    ];
                    foreach ($map as $key => $meta):
                        if (empty($social[$key])) {
                            continue;
                        }
                        $href = $social[$key];
                        ?>
                        <a class="btn btn-outline-light btn-sm rounded-circle footer-social-btn" href="<?= htmlspecialchars($href) ?>" target="_blank" rel="noopener noreferrer" title="<?= htmlspecialchars($meta[1]) ?>" aria-label="<?= htmlspecialchars($meta[1]) ?>">
                            <i class="<?= htmlspecialchars($meta[0]) ?>" aria-hidden="true"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php endif; ?>
<?php require __DIR__ . '/accessibility-widget.php'; ?>
<script src="assets/js/track.js" defer></script>
