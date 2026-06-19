<?php
/**
 * Barra lateral derecha del microsite (estilo widgets):
 *  - Líneas temáticas del observatorio (tarjetas compactas → abren el modal de detalle)
 *  - Líneas o dimensiones de análisis (chips)
 *
 * Variables del scope (observatorio.php): $slug, $obs, $lineKeys, $linesInfo, $lineCounts.
 */
$gsDetailsMap = require dirname(__DIR__) . '/config/observatory-details.php';
$gsD = $gsDetailsMap[$slug] ?? [];
?>
<style>
    .obs-side-widget{background:#fff;border:1px solid #e8edf5;border-radius:16px;padding:1.1rem 1.15rem;box-shadow:0 4px 14px rgba(0,0,0,.05);margin-bottom:1rem}
    .obs-side-widget>h3{font-size:.74rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;font-weight:700;margin:0 0 .85rem;display:flex;align-items:center;gap:.5rem}
    .obs-side-widget>h3 i{color:var(--obs-color,#0d6efd)}
    .side-line-card{display:flex;align-items:center;gap:.7rem;width:100%;text-align:left;background:#fff;border:1px solid rgba(var(--c-rgb,13,110,253),.25);border-radius:12px;padding:.55rem .7rem;margin-bottom:.5rem;cursor:pointer;transition:transform .15s ease,box-shadow .15s ease}
    .side-line-card:hover{transform:translateX(3px);box-shadow:0 6px 14px rgba(var(--c-rgb,13,110,253),.22)}
    .side-line-card:last-child{margin-bottom:0}
    .side-line-card__icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--c),rgba(var(--c-rgb),.7));color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.95rem;flex:0 0 auto}
    .side-line-card__title{font-weight:700;font-size:.85rem;color:#1f2937;line-height:1.2;margin:0}
    .side-line-card__count{font-size:.72rem;color:var(--c);font-weight:600}
    .side-line-card__arrow{margin-left:auto;color:var(--c);font-size:.8rem}
</style>
<?php if (empty($lineKeys ?? []) && empty($gsD['extra'])): ?>
<div class="obs-side-widget">
    <h3><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Líneas temáticas</h3>
    <p class="text-muted small mb-0">Las líneas temáticas de este observatorio se mostrarán aquí cuando se cargue el catálogo de indicadores en el CMS.</p>
</div>
<?php endif; ?>
<?php if (!empty($lineKeys ?? []) && function_exists('obs_category_color')): ?>
<div class="obs-side-widget">
    <h3><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Líneas temáticas</h3>
    <?php foreach ($lineKeys as $cat):
        $count = $lineCounts[$cat] ?? 0;
        $cColor = obs_category_color($cat);
        $cRgb = obs_hex_to_rgb($cColor);
        $cIcon = obs_category_icon($cat);
    ?>
        <button type="button" class="side-line-card"
                style="--c: <?= htmlspecialchars($cColor) ?>; --c-rgb: <?= htmlspecialchars($cRgb) ?>;"
                data-line-cat="<?= htmlspecialchars(mb_strtolower($cat)) ?>"
                data-bs-toggle="modal" data-bs-target="#lineModal">
            <span class="side-line-card__icon"><i class="fa-solid <?= $cIcon ?>" aria-hidden="true"></i></span>
            <span>
                <span class="side-line-card__title d-block"><?= htmlspecialchars($cat) ?></span>
                <span class="side-line-card__count"><?= $count ?> indicador<?= $count === 1 ? '' : 'es' ?></span>
            </span>
            <i class="fa-solid fa-chevron-right side-line-card__arrow" aria-hidden="true"></i>
        </button>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($gsD['extra_heading']) && !empty($gsD['extra'])): ?>
<div class="obs-side-widget">
    <h3><i class="fa-solid fa-sitemap" aria-hidden="true"></i> <?= htmlspecialchars($gsD['extra_heading']) ?></h3>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($gsD['extra'] as $item): ?>
            <span class="obs-chip" style="--c-rgb: var(--obs-color-rgb)"><i class="fa-solid fa-tag" aria-hidden="true"></i><?= htmlspecialchars($item) ?></span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
