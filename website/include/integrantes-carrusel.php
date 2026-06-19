<?php
/**
 * Carrusel de logos de entidades integrantes (antes del pie de página).
 *
 * Lee la sección 'widget-integrantes' del CMS (cms_microsite_sections);
 * cada hijo activo es una entidad: title = nombre, image_url = logo
 * (opcional: si no hay logo se muestra una tarjeta con ícono y nombre).
 * Se administra en CMS → Pestañas.
 *
 * Requiere $msWidgetSections (calculado en observatorio.php).
 */
$intSection = $msWidgetSections['widget-integrantes'] ?? null;
$intItems = [];
foreach (($intSection['children'] ?? []) as $intChild) {
    $intItems[] = [
        'name' => (string) ($intChild['title'] ?? ''),
        'logo' => trim((string) ($intChild['image_url'] ?? '')),
    ];
}
?>
<?php if ($intItems !== []): ?>
<style>
    .integrantes-strip{background:#fff;border-top:1px solid #e8edf5;padding:1.6rem 0 1.8rem;overflow:hidden}
    .integrantes-strip h2{font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;font-weight:700;text-align:center;margin:0 0 1.2rem}
    .integrantes-strip h2 i{color:var(--obs-color,#0d6efd);margin-right:.45rem}
    .logo-marquee{overflow:hidden;position:relative}
    .logo-marquee::before,.logo-marquee::after{content:"";position:absolute;top:0;bottom:0;width:70px;z-index:2;pointer-events:none}
    .logo-marquee::before{left:0;background:linear-gradient(90deg,#fff,transparent)}
    .logo-marquee::after{right:0;background:linear-gradient(-90deg,#fff,transparent)}
    .logo-track{display:flex;align-items:center;gap:2.6rem;width:max-content;animation:logosScroll 38s linear infinite}
    .logo-marquee:hover .logo-track{animation-play-state:paused}
    @keyframes logosScroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
    .logo-item{display:flex;flex-direction:column;align-items:center;gap:.45rem;min-width:130px;text-align:center}
    .logo-item img{height:64px;width:auto;max-width:170px;object-fit:contain;filter:grayscale(.15);transition:filter .2s ease,transform .2s ease}
    .logo-item:hover img{filter:none;transform:scale(1.06)}
    .logo-item .logo-fallback{width:64px;height:64px;border-radius:14px;background:rgba(var(--obs-color-rgb,13,110,253),.1);color:var(--obs-color,#0d6efd);display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem}
    .logo-item span{font-size:.7rem;color:#6b7280;font-weight:600;max-width:160px;line-height:1.25}
    @media (prefers-reduced-motion: reduce){.logo-track{animation:none;flex-wrap:wrap;justify-content:center;width:100%}}
</style>
<section class="integrantes-strip" aria-label="Entidades integrantes del observatorio">
    <div class="container-fluid px-0">
        <h2><i class="fa-solid fa-handshake" aria-hidden="true"></i><?= htmlspecialchars($integrantesHeading ?? 'Entidades que participan') ?></h2>
        <div class="logo-marquee">
            <div class="logo-track">
                <?php for ($rep = 0; $rep < 2; $rep++): /* pista duplicada para bucle continuo */ ?>
                    <?php foreach ($intItems as $it): ?>
                        <div class="logo-item" <?= $rep === 1 ? 'aria-hidden="true"' : '' ?>>
                            <?php if ($it['logo'] !== ''): ?>
                                <img src="<?= htmlspecialchars($it['logo']) ?>" alt="<?= $rep === 0 ? htmlspecialchars($it['name']) : '' ?>" loading="lazy">
                            <?php else: ?>
                                <span class="logo-fallback"><i class="fa-solid fa-building-columns" aria-hidden="true"></i></span>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($it['name']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
