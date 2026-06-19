<?php
/**
 * Carrusel de imágenes administrable desde el CMS.
 *
 * Lee la sección 'widget-carrusel' (cms_microsite_sections, key con prefijo
 * widget- → no es pestaña). Cada hijo activo con imagen es una diapositiva;
 * su título se muestra como leyenda. Se administra en CMS → Pestañas.
 *
 * Al hacer clic en una imagen se abre un lightbox con su propio carrusel
 * (flechas e indicadores) para recorrer todas las imágenes ampliadas.
 *
 * Requiere $msWidgetSections (calculado en observatorio.php).
 */
$cwcSection = $msWidgetSections['widget-carrusel'] ?? null;
$cwcSlides = [];
foreach (($cwcSection['children'] ?? []) as $cwcChild) {
    $img = trim((string) ($cwcChild['image_url'] ?? ''));
    // Omitir el banner infográfico de género: su información (indicadores, fuentes
    // y líneas) ya está en la tarjeta de descripción del observatorio.
    if ($img === '' || stripos($img, 'banner-genero') !== false) {
        continue;
    }
    $cwcSlides[] = ['img' => $img, 'title' => (string) ($cwcChild['title'] ?? '')];
}
?>
<?php if ($cwcSlides !== []): ?>
<style>
    .obs-img-carousel{border-radius:16px;overflow:hidden;background:#fff;border:1px solid #e8edf5;box-shadow:0 4px 18px rgba(0,0,0,.06)}
    .obs-img-carousel .carousel-item{background:#f6f8fc;text-align:center}
    .obs-img-carousel .carousel-item img{width:100%;max-height:560px;object-fit:contain;cursor:zoom-in}
    .obs-img-carousel .carousel-caption{background:rgba(15,23,42,.65);border-radius:10px;padding:.4rem .9rem;left:50%;right:auto;transform:translateX(-50%);bottom:.8rem;max-width:92%}
    .obs-img-carousel .carousel-caption h5{font-size:.9rem;margin:0;font-weight:600}
    .obs-img-carousel .carousel-indicators [data-bs-target]{width:7px;height:7px;border-radius:999px}
    /* Lightbox ampliado con navegación */
    .cwc-lb .modal-content{background:#0f172a;border:none;border-radius:14px;overflow:hidden}
    .cwc-lb .modal-header{background:var(--obs-color,#0d6efd);color:#fff;border-bottom:none;padding:.7rem 1.1rem}
    .cwc-lb .modal-header .modal-title{font-size:.95rem;font-weight:600}
    .cwc-lb .carousel-item{text-align:center;background:#0f172a}
    .cwc-lb .carousel-item img{max-height:82vh;max-width:100%;width:auto;margin:0 auto;object-fit:contain}
    .cwc-lb .carousel-control-prev,.cwc-lb .carousel-control-next{width:9%}
    .cwc-lb .carousel-indicators [data-bs-target]{width:8px;height:8px;border-radius:999px}
</style>
<section class="mb-4" aria-label="Carrusel de imágenes del observatorio">
    <div id="obsWidgetCarousel" class="carousel slide obs-img-carousel" data-bs-ride="carousel" data-bs-interval="5500">
        <div class="carousel-indicators">
            <?php foreach ($cwcSlides as $i => $s): ?>
                <button type="button" data-bs-target="#obsWidgetCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Imagen <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($cwcSlides as $i => $s): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($s['img']) ?>" alt="<?= htmlspecialchars($s['title']) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>" data-cwc-index="<?= $i ?>" role="button" title="Clic para ampliar">
                    <?php if ($s['title'] !== ''): ?>
                        <div class="carousel-caption d-block"><h5><?= htmlspecialchars($s['title']) ?></h5></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#obsWidgetCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span><span class="visually-hidden">Anterior</span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#obsWidgetCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span><span class="visually-hidden">Siguiente</span></button>
    </div>
</section>

<!-- Lightbox del carrusel: imagen ampliada con flechas para recorrer todas -->
<div class="modal fade cwc-lb" id="cwcLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cwcLbTitle">Imagen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0">
                <div id="cwcLbCarousel" class="carousel slide" data-bs-interval="false">
                    <?php if (count($cwcSlides) > 1): ?>
                    <div class="carousel-indicators">
                        <?php foreach ($cwcSlides as $i => $s): ?>
                            <button type="button" data-bs-target="#cwcLbCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Imagen <?= $i + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="carousel-inner">
                        <?php foreach ($cwcSlides as $i => $s): ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" data-cwc-title="<?= htmlspecialchars($s['title']) ?>">
                                <img src="<?= htmlspecialchars($s['img']) ?>" alt="<?= htmlspecialchars($s['title']) ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($cwcSlides) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#cwcLbCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span><span class="visually-hidden">Anterior</span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#cwcLbCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span><span class="visually-hidden">Siguiente</span></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var lbModal = document.getElementById('cwcLightbox');
    var lbCarouselEl = document.getElementById('cwcLbCarousel');
    var lbTitle = document.getElementById('cwcLbTitle');

    function tituloActual() {
        var activo = lbCarouselEl.querySelector('.carousel-item.active');
        lbTitle.textContent = (activo && activo.getAttribute('data-cwc-title')) || 'Imagen';
    }

    // Clic en una imagen del carrusel → abre el lightbox en esa misma imagen
    document.getElementById('obsWidgetCarousel').addEventListener('click', function (e) {
        var img = e.target.closest('img[data-cwc-index]');
        if (!img || !window.bootstrap) return;
        var idx = parseInt(img.getAttribute('data-cwc-index'), 10) || 0;
        var car = bootstrap.Carousel.getOrCreateInstance(lbCarouselEl, { interval: false });
        car.to(idx);
        tituloActual();
        bootstrap.Modal.getOrCreateInstance(lbModal).show();
    });

    // Mantener el título sincronizado al navegar con las flechas
    lbCarouselEl.addEventListener('slid.bs.carousel', tituloActual);

    // Navegación con teclado dentro del lightbox
    lbModal.addEventListener('keydown', function (e) {
        if (!window.bootstrap) return;
        var car = bootstrap.Carousel.getOrCreateInstance(lbCarouselEl);
        if (e.key === 'ArrowRight') car.next();
        if (e.key === 'ArrowLeft') car.prev();
    });
})();
</script>
<?php endif; ?>
