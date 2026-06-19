<?php
/**
 * Dashboard visual con la información institucional del observatorio.
 * Variables del scope: $slug, $obs, $kpiIndicators (opcional), $imIndicators (opcional)
 */
$detailsMap = require __DIR__ . '/../config/observatory-details.php';
$d = $detailsMap[$slug] ?? null;
if (!$d) {
    return;
}
/* Banderas opcionales: permiten mover bloques a otros lugares del layout
   (p. ej. líneas temáticas a la barra lateral, fuentes al carrusel de
   integrantes) sin duplicar este componente. */
$obsDescHideLines = $obsDescHideLines ?? false;
$obsDescHideExtra = $obsDescHideExtra ?? false;
$obsDescHideFuentes = $obsDescHideFuentes ?? false;
$obsColor  = $obs['color']  ?? '#0d6efd';
$obsAccent = $obs['accent'] ?? '#0d6efd';
$obsIcon   = $obs['icon']   ?? 'fa-chart-line';
// Total real: $imIndicators (catálogo cargado en observatorio.php) tiene preferencia
$totalInd = isset($imIndicators) && is_array($imIndicators) && count($imIndicators) > 0
    ? count($imIndicators)
    : ($kpiIndicators ?? 0);
$totalFuentes = count($d['fuentes'] ?? []);
$totalLineas  = 0;
if (isset($imIndicators) && is_array($imIndicators)) {
    $cats = [];
    foreach ($imIndicators as $r) {
        $c = trim((string) ($r['category_2'] ?? ''));
        if ($c !== '' && strcasecmp($c, 'ND') !== 0) $cats[$c] = true;
    }
    $totalLineas = count($cats);
}

// Mapping de iconos por palabra clave en el nombre de la fuente
function obs_source_icon(string $name): string {
    $n = strtolower($name);
    if (strpos($n,'dane') !== false)       return 'fa-chart-pie';
    if (strpos($n,'dnp')  !== false)       return 'fa-building-columns';
    if (strpos($n,'compite') !== false || strpos($n,'competitividad') !== false) return 'fa-ranking-star';
    if (strpos($n,'situr') !== false || strpos($n,'turismo') !== false) return 'fa-suitcase-rolling';
    if (strpos($n,'upra') !== false || strpos($n,'rural') !== false)    return 'fa-wheat-awn';
    if (strpos($n,'cafeteros') !== false || strpos($n,'café') !== false) return 'fa-mug-hot';
    if (strpos($n,'enam')   !== false)     return 'fa-tractor';
    if (strpos($n,'ica')    !== false)     return 'fa-leaf';
    if (strpos($n,'fedegan') !== false || strpos($n,'ganado') !== false) return 'fa-cow';
    if (strpos($n,'upme')   !== false || strpos($n,'minería') !== false || strpos($n,'mineria') !== false) return 'fa-mountain';
    if (strpos($n,'anh')    !== false || strpos($n,'hidrocarburo') !== false || strpos($n,'petró') !== false) return 'fa-oil-well';
    if (strpos($n,'sivigila') !== false || strpos($n,'salud pública') !== false) return 'fa-virus-covid';
    if (strpos($n,'salud')  !== false)     return 'fa-heart-pulse';
    if (strpos($n,'medicina legal') !== false || strpos($n,'forensis') !== false) return 'fa-gavel';
    if (strpos($n,'fiscalía') !== false || strpos($n,'fiscalia') !== false)       return 'fa-scale-balanced';
    if (strpos($n,'icbf')   !== false)     return 'fa-children';
    if (strpos($n,'icfes')  !== false || strpos($n,'educa') !== false)            return 'fa-graduation-cap';
    if (strpos($n,'rlcpd')  !== false || strpos($n,'discapacidad') !== false)     return 'fa-wheelchair';
    if (strpos($n,'sispro') !== false || strpos($n,'minsalud') !== false)         return 'fa-stethoscope';
    if (strpos($n,'arn')    !== false || strpos($n,'reincorpora') !== false)      return 'fa-handshake';
    if (strpos($n,'uariv')  !== false || strpos($n,'víctimas') !== false || strpos($n,'victimas') !== false) return 'fa-people-arrows';
    if (strpos($n,'policía') !== false || strpos($n,'policia') !== false)         return 'fa-shield-halved';
    if (strpos($n,'mujer')  !== false || strpos($n,'género') !== false || strpos($n,'genero') !== false) return 'fa-venus';
    if (strpos($n,'registraduría') !== false || strpos($n,'registraduria') !== false) return 'fa-square-poll-vertical';
    if (strpos($n,'ministerio') !== false || strpos($n,'gobernación') !== false || strpos($n,'gobernacion') !== false) return 'fa-building-columns';
    return 'fa-database';
}
?>
<section class="obs-dashboard-card mb-4" aria-labelledby="obs-desc-title"
         style="--c: <?= htmlspecialchars($obsColor) ?>; --a: <?= htmlspecialchars($obsAccent) ?>;">
    <style>
        .obs-dashboard-card{position:relative;overflow:hidden;border-radius:18px;background:#fff;box-shadow:0 4px 18px rgba(0,0,0,.06);padding:0}
        .obs-dashboard-card__hero{position:relative;padding:1.75rem 1.75rem 1.25rem;background:linear-gradient(135deg,var(--c) 0%,var(--a) 100%);color:#fff;overflow:hidden}
        .obs-dashboard-card__hero::after{content:"";position:absolute;right:-60px;top:-60px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.10);pointer-events:none}
        .obs-dashboard-card__hero::before{content:"";position:absolute;right:60px;bottom:-80px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none}
        .obs-dashboard-card__heading{display:flex;gap:1rem;align-items:center;position:relative;z-index:1}
        .obs-dashboard-card__heading .obs-big-icon{width:64px;height:64px;border-radius:18px;background:rgba(255,255,255,.20);display:inline-flex;align-items:center;justify-content:center;font-size:1.8rem;backdrop-filter:blur(8px);box-shadow:0 4px 12px rgba(0,0,0,.18);flex:0 0 auto}
        .obs-dashboard-card__heading h2{margin:0;font-weight:700;font-size:1.5rem}
        .obs-dashboard-card__heading p{margin:.25rem 0 0;opacity:.92;font-size:.95rem;line-height:1.45}
        .obs-dashboard-card__kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;padding:1.25rem 1.75rem;background:rgba(var(--c-rgb,13,110,253),.02);border-bottom:1px solid #eef0f4}
        .obs-kpi{display:flex;align-items:center;gap:.8rem;background:#fff;padding:.85rem 1rem;border-radius:12px;border:1px solid #eef0f4;transition:transform .15s ease,box-shadow .15s ease}
        .obs-kpi:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.08)}
        .obs-kpi__icon{width:42px;height:42px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--c),var(--a));color:#fff;font-size:1.05rem;flex:0 0 auto;box-shadow:0 4px 10px rgba(0,0,0,.12)}
        .obs-kpi__val{font-weight:700;font-size:1.4rem;line-height:1;color:#1f2937}
        .obs-kpi__lbl{font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7280;margin-top:.2rem}
        .obs-section-block{padding:1.25rem 1.75rem;border-bottom:1px solid #eef0f4}
        .obs-section-block:last-child{border-bottom:none}
        .obs-section-block h3{font-size:.74rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;font-weight:700;margin:0 0 .9rem;display:flex;align-items:center;gap:.5rem}
        .obs-section-block h3 i{color:var(--c)}
        .obs-chip-row{display:flex;flex-wrap:wrap;gap:.5rem}
        .obs-chip{display:inline-flex;align-items:center;gap:.5rem;padding:.45rem .85rem;border-radius:999px;background:rgba(var(--c-rgb,13,110,253),.06);color:#1f2937;border:1px solid rgba(var(--c-rgb,13,110,253),.18);font-size:.84rem;line-height:1.3;transition:transform .15s ease,background-color .15s ease}
        .obs-chip:hover{transform:translateY(-1px);background:rgba(var(--c-rgb,13,110,253),.12)}
        .obs-chip i{color:var(--c);font-size:.85rem}
        .obs-consulta{display:grid;grid-template-columns:1fr;gap:.55rem}
        .obs-consulta li{display:flex;gap:.65rem;padding:.55rem .85rem;background:rgba(var(--c-rgb,13,110,253),.045);border-left:3px solid var(--c);border-radius:8px;list-style:none;color:#374151;line-height:1.45;font-size:.93rem}
        .obs-consulta li i{color:var(--c);flex:0 0 auto;margin-top:.2rem}
        .obs-consulta-list{padding:0;margin:0}
        .obs-period{display:inline-flex;align-items:center;gap:.6rem;padding:.65rem 1rem;border-radius:999px;background:linear-gradient(135deg,var(--c),var(--a));color:#fff;font-weight:600;font-size:.9rem;box-shadow:0 4px 10px rgba(0,0,0,.12)}
        .obs-period i{font-size:.95rem}
    </style>

    <!-- Hero -->
    <div class="obs-dashboard-card__hero">
        <div class="obs-dashboard-card__heading">
            <span class="obs-big-icon"><i class="fa-solid <?= htmlspecialchars($obsIcon) ?>"></i></span>
            <div>
                <h2 id="obs-desc-title"><?= htmlspecialchars($d['title']) ?></h2>
                <p><?= htmlspecialchars($d['intro']) ?></p>
            </div>
        </div>
    </div>

    <!-- KPI bar -->
    <div class="obs-dashboard-card__kpis">
        <div class="obs-kpi">
            <span class="obs-kpi__icon"><i class="fa-solid fa-chart-column"></i></span>
            <div>
                <div class="obs-kpi__val"><?= $totalInd ?></div>
                <div class="obs-kpi__lbl">Indicadores</div>
            </div>
        </div>
        <div class="obs-kpi">
            <span class="obs-kpi__icon"><i class="fa-solid fa-building-columns"></i></span>
            <div>
                <div class="obs-kpi__val"><?= $totalFuentes ?></div>
                <div class="obs-kpi__lbl">Fuentes oficiales</div>
            </div>
        </div>
        <?php if ($totalLineas > 0): ?>
        <div class="obs-kpi">
            <span class="obs-kpi__icon"><i class="fa-solid fa-layer-group"></i></span>
            <div>
                <div class="obs-kpi__val"><?= $totalLineas ?></div>
                <div class="obs-kpi__lbl">Líneas temáticas</div>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($d['periodicidad'])): ?>
        <div class="obs-kpi">
            <span class="obs-kpi__icon"><i class="fa-solid fa-rotate-right"></i></span>
            <div>
                <div class="obs-kpi__val" style="font-size:.85rem;line-height:1.1"><?= htmlspecialchars(explode(',', $d['periodicidad'])[0] ?? 'Actualización periódica') ?></div>
                <div class="obs-kpi__lbl">Actualización</div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Información que se puede consultar -->
    <?php if (!empty($d['consulta'])): ?>
    <div class="obs-section-block">
        <h3><i class="fa-solid fa-circle-info"></i> <?= htmlspecialchars($d['consulta_heading']) ?></h3>
        <ul class="obs-consulta obs-consulta-list">
            <?php foreach ($d['consulta'] as $item): ?>
                <li><i class="fa-solid fa-check-circle"></i><span><?= htmlspecialchars($item) ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Líneas o dimensiones de análisis (extra) -->
    <?php if (!$obsDescHideExtra && !empty($d['extra_heading']) && !empty($d['extra'])): ?>
    <div class="obs-section-block">
        <h3><i class="fa-solid fa-sitemap"></i> <?= htmlspecialchars($d['extra_heading']) ?></h3>
        <div class="obs-chip-row">
            <?php foreach ($d['extra'] as $item): ?>
                <span class="obs-chip"><i class="fa-solid fa-tag"></i><?= htmlspecialchars($item) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Líneas temáticas (tarjetas clickeables) — entre "Consulta" y "Fuentes" -->
    <?php if (!$obsDescHideLines && !empty($lineKeys ?? []) && function_exists('obs_category_color')): ?>
    <div class="obs-section-block">
        <h3><i class="fa-solid fa-layer-group"></i> Líneas temáticas del observatorio
            <span class="text-muted fw-normal" style="text-transform:none;letter-spacing:0;font-size:.85rem;margin-left:auto">
                <i class="fa-solid fa-hand-pointer"></i> Haz clic en una tarjeta para ver el detalle
            </span>
        </h3>
        <style>
            .obs-section-block .lines-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:12px}
            .obs-section-block .line-card{position:relative;overflow:hidden;background:#fff;border-radius:12px;padding:14px 14px 12px;border:1px solid rgba(var(--c-rgb),.22);cursor:pointer;transition:transform .2s ease,box-shadow .2s ease;text-align:left;display:flex;flex-direction:column;gap:8px;min-height:148px}
            .obs-section-block .line-card::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(var(--c-rgb),.10),rgba(var(--c-rgb),.02));z-index:0;pointer-events:none}
            .obs-section-block .line-card::after{content:"";position:absolute;right:-22px;bottom:-22px;width:110px;height:110px;border-radius:50%;background:rgba(var(--c-rgb),.10);z-index:0;transition:transform .35s ease;pointer-events:none}
            .obs-section-block .line-card:hover{transform:translateY(-3px);box-shadow:0 10px 22px rgba(var(--c-rgb),.22)}
            .obs-section-block .line-card:hover::after{transform:scale(1.3)}
            .obs-section-block .line-card>*{position:relative;z-index:1}
            .obs-section-block .line-card__icon{width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,var(--c) 0%,rgba(var(--c-rgb),.7) 100%);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.2rem;box-shadow:0 6px 14px rgba(var(--c-rgb),.32)}
            .obs-section-block .line-card__title{font-weight:700;font-size:.92rem;color:#1f2937;margin:0;line-height:1.25}
            .obs-section-block .line-card__count{font-size:.74rem;color:var(--c);font-weight:600}
            .obs-section-block .line-card__hint{font-size:.7rem;color:#6b7280;margin-top:auto;display:inline-flex;align-items:center;gap:.3rem}
        </style>
        <div class="lines-grid">
            <?php foreach ($lineKeys as $cat):
                $info  = $linesInfo[$cat] ?? null;
                $count = $lineCounts[$cat] ?? 0;
                $cKey  = mb_strtolower($cat);
                $cColor = obs_category_color($cat);
                $cRgb   = obs_hex_to_rgb($cColor);
                $cIcon  = obs_category_icon($cat);
            ?>
                <button type="button" class="line-card"
                        style="--c: <?= htmlspecialchars($cColor) ?>; --c-rgb: <?= htmlspecialchars($cRgb) ?>;"
                        data-line-cat="<?= htmlspecialchars($cKey) ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#lineModal">
                    <span class="line-card__icon"><i class="fa-solid <?= $cIcon ?>"></i></span>
                    <h4 class="line-card__title"><?= htmlspecialchars($cat) ?></h4>
                    <span class="line-card__count"><?= $count ?> indicador<?= $count === 1 ? '' : 'es' ?></span>
                    <span class="line-card__hint">Ver detalle <i class="fa-solid fa-arrow-right"></i></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Fuentes de información como chips con icono -->
    <?php if (!$obsDescHideFuentes && !empty($d['fuentes'])): ?>
    <div class="obs-section-block">
        <h3><i class="fa-solid fa-database"></i> <?= htmlspecialchars($d['fuentes_heading']) ?></h3>
        <div class="obs-chip-row">
            <?php foreach ($d['fuentes'] as $item): ?>
                <span class="obs-chip" title="<?= htmlspecialchars($item) ?>">
                    <i class="fa-solid <?= obs_source_icon($item) ?>"></i><?= htmlspecialchars($item) ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Periodicidad como badge destacado -->
    <?php if (!empty($d['periodicidad'])): ?>
    <div class="obs-section-block">
        <h3><i class="fa-solid fa-calendar-days"></i> Periodicidad</h3>
        <span class="obs-period"><i class="fa-solid fa-rotate-right"></i> <?= htmlspecialchars($d['periodicidad']) ?></span>
        <?php if (!empty($obsLastUpdatedLabel)): ?>
            <p class="small text-muted mt-2 mb-0"><i class="fa-regular fa-clock me-1" aria-hidden="true"></i> Última actualización: <strong><?= htmlspecialchars($obsLastUpdatedLabel) ?></strong></p>
        <?php endif; ?>
        <?php if (!empty($d['footnote'])): ?>
            <p class="small text-muted mt-2 mb-0"><?= htmlspecialchars($d['footnote']) ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>
