<?php
require_once 'functions.php';

$title = 'Observatorio';
$error = null;
$indicador = [];
$maps = false;
$charts = false;
$filename = '';
$dimensionName = '';
$dimensionColor = '';
$dimensionAccent = '';
$dimensionIcon = '';
$link = 'index.php';
$newLink = 'index.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $idStart = substr($id, 0, 1);

    // Dimension mapping
    $dimMap = [
        '1' => ['file' => 'economico',  'name' => 'Observatorio Económico',  'color' => '#0f3557', 'accent' => '#d8a21d', 'icon' => 'fa-chart-line',   'slug' => 'economico'],
        '2' => ['file' => 'social',     'name' => 'Observatorio Social',     'color' => '#5f2a8a', 'accent' => '#d74fa6', 'icon' => 'fa-people-group', 'slug' => 'social'],
        '3' => ['file' => 'ambiental',  'name' => 'Obs. Medio Ambiente',     'color' => '#1f6b45', 'accent' => '#23a9b8', 'icon' => 'fa-leaf',         'slug' => 'ambiente'],
        '4' => ['file' => 'tecnologia', 'name' => 'Obs. CTI',               'color' => '#1847b7', 'accent' => '#6d4aff', 'icon' => 'fa-microchip',    'slug' => 'cti'],
        '5' => ['file' => 'genero',     'name' => 'Obs. Asuntos de Género', 'color' => '#7d2d91', 'accent' => '#ef6f8f', 'icon' => 'fa-venus-mars',   'slug' => 'genero'],
    ];

    $dim = $dimMap[$idStart] ?? $dimMap['1'];
    $filename = $dim['file'];
    $dimensionName = $dim['name'];
    $dimensionColor = $dim['color'];
    $dimensionAccent = $dim['accent'];
    $dimensionIcon = $dim['icon'];
    $link = 'indic-' . $dim['file'] . '.php';
    $newLink = 'observatorio.php?slug=' . $dim['slug'];

    if (file_exists('indicador/' . $id . '/indicador.info')) {
        $indicador = getInfo('indicador/' . $id . '/indicador.info');
        $indicador['id'] = $id;
        $title = ($indicador['titulo'] ?? 'Indicador') . ' · Red de Observatorios';

        if (count($indicador) > 0) {
            $i = 1;
            $indicador['charts'] = [];
            while (true) {
                if (file_exists('indicador/' . $id . '/' . $i . '.info')) {
                    $chartInfo = getInfo('indicador/' . $id . '/' . $i . '.info');
                    if (count($chartInfo) > 0) {
                        $tipo = $chartInfo['tipo'] ?? 'None';
                        if (strtolower($tipo) === 'mapa') $maps = true;
                        else $charts = true;
                        array_push($indicador['charts'], $chartInfo);
                    } else { break; }
                    $i++;
                } else { break; }
            }
        } else {
            $error = 'info de indicador sin datos';
        }
    } else {
        $error = 'Indicador no encontrado';
    }
} else {
    $error = 'ID de indicador no proporcionado';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="description" content="Detalle del indicador: <?= htmlspecialchars($indicador['titulo'] ?? '') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png" sizes="192x192">
    <style>
        :root{--obs-color:<?= htmlspecialchars($dimensionColor) ?>;--obs-accent:<?= htmlspecialchars($dimensionAccent) ?>}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Inter,'Segoe UI',system-ui,sans-serif;background:#f4f7fc;color:#1b2a40}
        .ind-header{background:var(--obs-color);color:#fff;padding:0}
        .ind-header a{color:#fff;text-decoration:none}
        .ind-header .back-link{display:inline-flex;align-items:center;gap:.5rem;font-weight:600;font-size:.9rem;opacity:.9;transition:opacity .2s}
        .ind-header .back-link:hover{opacity:1}
        .brand-logo{width:36px;height:36px;object-fit:contain;background:#fff;border-radius:8px;padding:4px}
        .ind-hero{background:linear-gradient(135deg,var(--obs-color),var(--obs-accent));color:#fff;padding:2.5rem 0 2rem;position:relative;overflow:hidden}
        .ind-hero::after{content:'';position:absolute;right:-60px;top:-40px;width:260px;height:260px;background:rgba(255,255,255,.06);border-radius:50%}
        .ind-hero h1{font-size:clamp(1.3rem,3vw,1.8rem);font-weight:800;margin-bottom:.5rem}
        .ind-hero .meta-pills{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.75rem}
        .ind-hero .meta-pill{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);padding:.3rem .6rem;border-radius:999px;font-size:.78rem;font-weight:500}
        .ind-card{background:#fff;border-radius:14px;padding:1.25rem;box-shadow:0 6px 24px rgba(0,0,0,.07);margin-bottom:1.25rem}
        .ind-card h2{font-size:1.05rem;font-weight:700;color:var(--obs-color);margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem}
        .ind-card h2 i{font-size:.85rem;opacity:.7}
        .info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem}
        .info-item{background:#f7f9fe;border:1px solid #e6ecf9;border-radius:10px;padding:.75rem}
        .info-item strong{display:block;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:#5d6b80;margin-bottom:.25rem}
        .info-item span{font-size:.88rem;color:#1e2d46}
        /* Tabs for charts */
        .chart-tabs{display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1rem}
        .chart-tab{border:1px solid #dce4f2;background:#fff;color:#2f3b50;padding:.5rem 1rem;border-radius:10px;font-size:.85rem;font-weight:500;cursor:pointer;transition:all .2s}
        .chart-tab:hover{border-color:var(--obs-color);color:var(--obs-color)}
        .chart-tab.active{background:var(--obs-color);color:#fff;border-color:var(--obs-color)}
        .chart-panel{display:none}
        .chart-panel.active{display:block}
        .chart-container{width:100%;min-height:420px;background:#fafcff;border:1px solid #e6ecf9;border-radius:12px;overflow:hidden;margin-bottom:.75rem;position:relative}
        .chart-container .chart{width:100%;height:420px}
        .chart-container .map{width:100%;height:500px}
        /* Google Charts SVG responsive */
        .chart-container .chart>div{width:100%!important}
        .chart-container .chart svg{max-width:100%}
        /* Leaflet map controls styling inside new card */
        .chart-container .leaflet-container{border-radius:0;background:#eef2f7}
        /* Animación suave de los polígonos al filtrar/seleccionar */
        .chart-container .leaflet-interactive{transition:fill .3s ease,fill-opacity .3s ease,stroke .15s ease,stroke-width .15s ease;cursor:pointer}
        .chart-container .popup{background:#fff;padding:8px 12px;border-radius:10px;box-shadow:0 6px 18px rgba(2,6,23,.18);font-size:.82rem;max-width:240px}
        .chart-container .popup h4{font-size:.78rem;margin:0 0 4px;color:#5d6b80}
        .chart-container .popup .municipio{font-weight:700;color:#0b1b2e;font-size:.95rem;margin-bottom:2px}
        .chart-container .popup .dropdown{margin:.15rem 0 .35rem;max-width:210px}
        .chart-container .legend{max-height:280px;overflow-y:auto;line-height:1}
        .chart-container .legend i{width:16px;height:10px;display:inline-block;margin-right:4px}
        .chart-container .slider{width:100%}
        /* DummyChart (gender icons) */
        .chart-container .dummies{font-size:50px;margin:10px;display:inline-block;vertical-align:middle}
        @media(max-width:768px){.chart-container{min-height:300px}.chart-container .chart{height:300px}.chart-container .map{height:380px}}
        .chart-desc{font-size:.85rem;color:#5d6b80;padding:.5rem 0;margin-bottom:.5rem}
        .chart-footer{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;padding-top:.5rem;border-top:1px solid #edf1f7}
        .chart-footer a,.chart-footer span{font-size:.82rem;color:#5d6b80}
        .chart-footer a{color:var(--obs-color);font-weight:500;text-decoration:none}
        .chart-footer a:hover{text-decoration:underline}
        .source-link{display:inline-flex;align-items:center;gap:.3rem}
        .error-box{background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:2rem;text-align:center;color:#991b1b}
        .error-box i{font-size:2rem;margin-bottom:.5rem;display:block}
    </style>
    <?php if (!is_null($error)): ?>
    <?php else: ?>
    <script type="text/javascript" src="assets/js/colors.js"></script>
    <script type="text/javascript" src="assets/js/general.js"></script>
    <script type="text/javascript" src="assets/js/<?= htmlspecialchars($filename) ?>.js"></script>
    <?php if ($charts): ?>
    <link rel="stylesheet" href="assets/css/charts.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
    <?php endif; ?>
    <?php if ($maps): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@1.6.0/Control.FullScreen.css">
    <link rel="stylesheet" href="assets/css/maps.css">
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.fullscreen@1.6.0/Control.FullScreen.js"></script>
    <script type="text/javascript" src="assets/js/boyaca.js"></script>
    <script type="text/javascript" src="assets/js/mapChart.js"></script>
    <?php endif; ?>
    <script type="text/javascript">
        info=[]; csv=[];
        <?php
        $ci = 1;
        while (file_exists('indicador/' . $id . '/' . $ci . '.info')) {
            $cInfo = getInfo('indicador/' . $id . '/' . $ci . '.info');
            if (!empty($cInfo)) {
                $jsInfo = $cInfo;
                unset($jsInfo['titulo'], $jsInfo['title']);
                unset($jsInfo['descripcion'], $jsInfo['description']);
                unset($jsInfo['fuentes'], $jsInfo['fuente']);
                echo 'info.push(' . json_encode($jsInfo) . ');';
            }
            if (file_exists('indicador/' . $id . '/' . $ci . '.csv')) {
                $csvArr = array_map('str_getcsv', file('indicador/' . $id . '/' . $ci . '.csv'));
                $csvJson = json_encode($csvArr, JSON_NUMERIC_CHECK);
                echo 'csv.push(' . str_replace('\u00ac', ',', $csvJson) . ');';
            }
            $ci++;
        }
        ?>
    </script>
    <?php endif; ?>
</head>
<body>

<!-- Header -->
<header class="ind-header">
    <div class="container d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <a href="index.php" class="back-link">
            <img src="assets/svg/logo.svg" alt="Logo" class="brand-logo">
            <span>Inicio Red</span>
        </a>
        <nav class="d-flex gap-2 flex-wrap align-items-center" style="font-size:.88rem">
            <a href="<?= htmlspecialchars($newLink) ?>" style="color:#fff;text-decoration:none;opacity:.9">
                <i class="fa-solid <?= htmlspecialchars($dimensionIcon) ?>"></i>
                <?= htmlspecialchars($dimensionName) ?>
            </a>
            <span style="opacity:.4">|</span>
            <a href="estado-observatorio.php" style="color:#fff;text-decoration:none;opacity:.9">Catálogo</a>
            <span style="opacity:.4">|</span>
            <a href="<?= htmlspecialchars($link) ?>" style="color:#fff;text-decoration:none;opacity:.9">Vista clásica</a>
        </nav>
    </div>
</header>

<?php if (is_null($error)): ?>

<!-- Hero -->
<section class="ind-hero">
    <div class="container">
        <nav aria-label="breadcrumb" style="margin-bottom:.75rem">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;--bs-breadcrumb-divider:'›'">
                <li class="breadcrumb-item"><a href="index.php" style="color:rgba(255,255,255,.7)">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars($newLink) ?>" style="color:rgba(255,255,255,.7)"><?= htmlspecialchars($dimensionName) ?></a></li>
                <li class="breadcrumb-item active" style="color:#fff" aria-current="page">Indicador <?= htmlspecialchars($id) ?></li>
            </ol>
        </nav>
        <h1><?= htmlspecialchars($indicador['titulo'] ?? 'Indicador') ?></h1>
        <p style="max-width:72ch;opacity:.9;font-size:.95rem"><?= htmlspecialchars($indicador['descripcion'] ?? '') ?></p>
        <div class="meta-pills">
            <?php if (!empty($indicador['categoria'])): ?>
                <span class="meta-pill"><i class="fa-solid fa-folder-open"></i> <?= htmlspecialchars($indicador['categoria']) ?></span>
            <?php endif; ?>
            <?php if (!empty($indicador['subcategoria'])): ?>
                <span class="meta-pill"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($indicador['subcategoria']) ?></span>
            <?php endif; ?>
            <span class="meta-pill"><i class="fa-solid fa-hashtag"></i> ID: <?= htmlspecialchars($id) ?></span>
            <span class="meta-pill"><i class="fa-solid fa-chart-area"></i> <?= count($indicador['charts']) ?> visualizaciones</span>
            <?php if (!empty($indicador['ficha'])): ?>
                <a class="meta-pill" href="<?= htmlspecialchars($indicador['ficha']) ?>" target="_blank" rel="noopener" style="text-decoration:none"><i class="fa-solid fa-file-pdf"></i> Descargar ficha (PDF)</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<main class="container py-4">

    <!-- Ficha técnica -->
    <div class="ind-card">
        <h2><i class="fa-solid fa-id-card"></i> Ficha técnica</h2>
        <div class="info-grid">
            <div class="info-item">
                <strong>Categoría</strong>
                <span><?= htmlspecialchars($indicador['categoria'] ?? '—') ?></span>
            </div>
            <div class="info-item">
                <strong>Subcategoría</strong>
                <span><?= htmlspecialchars($indicador['subcategoria'] ?? '—') ?></span>
            </div>
            <?php if (!empty($indicador['etiquetas'])): ?>
            <div class="info-item">
                <strong>Etiquetas</strong>
                <span><?= htmlspecialchars(str_replace(['|', '/'], ', ', $indicador['etiquetas'])) ?></span>
            </div>
            <?php endif; ?>
            <div class="info-item">
                <strong>Fuentes</strong>
                <span>
                    <?php
                    $fuentes = $indicador['fuentes'] ?? '';
                    if (strtolower(substr($fuentes, 0, 4)) === 'http') {
                        echo '<a href="' . htmlspecialchars($fuentes) . '" target="_blank" rel="noopener" class="source-link"><i class="fa-solid fa-link"></i> Ver fuente</a>';
                    } else {
                        echo htmlspecialchars($fuentes ?: '—');
                    }
                    ?>
                </span>
            </div>
            <div class="info-item">
                <strong>Dimensión</strong>
                <span><i class="fa-solid <?= htmlspecialchars($dimensionIcon) ?>" style="color:var(--obs-color)"></i> <?= htmlspecialchars($dimensionName) ?></span>
            </div>
        </div>
    </div>

    <!-- Visualizaciones -->
    <?php if (count($indicador['charts']) > 0): ?>
    <div class="ind-card">
        <h2><i class="fa-solid fa-chart-column"></i> Visualizaciones</h2>

        <?php if (count($indicador['charts']) > 1): ?>
        <div class="chart-tabs" id="chartTabs">
            <?php foreach ($indicador['charts'] as $ci => $chart): ?>
                <button class="chart-tab <?= $ci === 0 ? 'active' : '' ?>"
                        onclick="switchChart(this, <?= $ci ?>)">
                    <?php
                    $tipo = strtolower($chart['tipo'] ?? '');
                    $icon = $tipo === 'mapa' ? 'fa-map' : 'fa-chart-bar';
                    ?>
                    <i class="fa-solid <?= $icon ?>"></i>
                    <?= htmlspecialchars($chart['titulo'] ?? $chart['title'] ?? "Gráfico " . ($ci + 1)) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php foreach ($indicador['charts'] as $ci => $chart): ?>
            <div class="chart-panel <?= $ci === 0 ? 'active' : '' ?>" id="chartPanel<?= $ci ?>">
                <?php
                $cDesc = $chart['descripcion'] ?? $chart['description'] ?? '';
                if ($cDesc):
                ?>
                    <p class="chart-desc"><?= htmlspecialchars($cDesc) ?></p>
                <?php endif; ?>

                <div class="chart-container">
                    <?php
                    $tipo = strtolower($chart['tipo'] ?? '');
                    $divClass = ($tipo === 'mapa') ? 'map' : 'chart';
                    ?>
                    <div id="chart<?= $ci + 1 ?>" class="<?= $divClass ?>"></div>
                </div>

                <div class="chart-footer">
                    <a href="indicador/<?= htmlspecialchars($id) ?>/<?= $ci + 1 ?>.csv" download>
                        <i class="fa-solid fa-download"></i> Descargar datos CSV
                    </a>
                    <?php
                    $cFuentes = $chart['fuentes'] ?? $chart['fuente'] ?? '';
                    if ($cFuentes && strtolower(substr($cFuentes, 0, 4)) === 'http'):
                    ?>
                        <a href="<?= htmlspecialchars($cFuentes) ?>" target="_blank" rel="noopener">
                            <i class="fa-solid fa-link"></i> Fuente
                        </a>
                    <?php elseif ($cFuentes): ?>
                        <span><i class="fa-solid fa-quote-left"></i> <?= htmlspecialchars($cFuentes) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="ind-card">
        <h2><i class="fa-solid fa-chart-column"></i> Visualizaciones</h2>
        <p class="text-muted mb-0" style="font-size:.9rem">
            <i class="fa-regular fa-circle-question me-1"></i>
            Este indicador aún no tiene datos cargados.
            <?php if (!empty($indicador['ficha'])): ?>
                Puede consultar la <a href="<?= htmlspecialchars($indicador['ficha']) ?>" target="_blank" rel="noopener">ficha técnica en PDF</a>.
            <?php endif; ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="d-flex flex-wrap gap-2 justify-content-between mb-4">
        <a href="<?= htmlspecialchars($newLink) ?>" class="btn btn-dark">
            <i class="fa-solid fa-arrow-left"></i> Volver a <?= htmlspecialchars($dimensionName) ?>
        </a>
        <a href="estado-observatorio.php" class="btn btn-outline-secondary">
            <i class="fa-solid fa-list"></i> Catálogo completo
        </a>
    </div>
</main>

<script>
/* ── Compatibility shim ──────────────────────────────────────────────
   The legacy Display / AbstractDisplay system expects:
     • a #tab element with .tabContent children (display:block/none)
     • a #firstButton element
     • openTabStatic() from tabs.js
   The new markup uses .chart-panel with .active class.
   We bridge them here BEFORE Display() is constructed.
   ─────────────────────────────────────────────────────────────────── */

// 1. Override openTabStatic so the legacy constructor doesn't crash
function openTabStatic(but, div) {
    // no-op: our new markup already handles panel visibility
}

// 2. Create a fake #firstButton so getElementById doesn't return null
(function() {
    var fb = document.createElement('button');
    fb.id = 'firstButton';
    fb.style.display = 'none';
    document.body.appendChild(fb);
})();

// 3. Show ALL panels before Display() runs (maps need visible containers on init)
(function() {
    var panels = document.querySelectorAll('.chart-panel');
    panels.forEach(function(p) { p.style.display = 'block'; p.classList.add('active'); });
})();

// 4. Patch AbstractDisplay.prototype BEFORE any subclass is instantiated
if (typeof AbstractDisplay !== 'undefined') {
    // Override drawCharts: draw charts then restore panel visibility
    AbstractDisplay.prototype.drawCharts = function() {
        for (var i = 0; i < this._charts.length; i++) {
            try { this._charts[i].draw(); } catch(e) {
                console.warn('Chart ' + i + ' draw error:', e);
            }
        }
        // Restore: only first panel visible
        var panels = document.querySelectorAll('.chart-panel');
        panels.forEach(function(p, idx) {
            p.style.display = '';
            if (idx === 0) p.classList.add('active');
            else p.classList.remove('active');
        });
    };

    // Override redrawChart: redraw only the currently-visible chart
    AbstractDisplay.prototype.redrawChart = function() {
        var panels = document.querySelectorAll('.chart-panel');
        for (var i = 0; i < panels.length; i++) {
            if (panels[i].classList.contains('active') && this._charts[i]) {
                var tipo = (info[i] && info[i]['tipo']) ? info[i]['tipo'] : '';
                if (tipo !== 'Mapa') {
                    try { this._charts[i].draw(); } catch(e) {}
                }
            }
        }
    };

    // Override openTab to use new markup
    AbstractDisplay.prototype.openTab = function(but, div, redraw) {
        // no-op in new design; switchChart handles everything
    };
}

// 5. Tab-switching function for the new UI
function switchChart(btn, idx) {
    // Update tab buttons
    document.querySelectorAll('.chart-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');
    // Update panels
    document.querySelectorAll('.chart-panel').forEach(function(p) { p.classList.remove('active'); });
    var panel = document.getElementById('chartPanel' + idx);
    if (panel) panel.classList.add('active');
    // Redraw the target chart after panel is visible
    setTimeout(function() {
        if (typeof display !== 'undefined' && display._charts && display._charts[idx]) {
            var tipo = (info[idx] && info[idx]['tipo']) ? info[idx]['tipo'] : '';
            if (tipo === 'Mapa') {
                // Invalidate Leaflet map via stored reference
                var mapEl = document.getElementById('chart' + (idx + 1));
                if (mapEl && mapEl._leafletMapRef) {
                    mapEl._leafletMapRef.invalidateSize();
                }
            } else {
                try { display._charts[idx].draw(); } catch(e) {}
            }
        }
        // Global resize as fallback for any remaining chart types
        window.dispatchEvent(new Event('resize'));
    }, 150);
}
</script>

<?php if (file_exists('indicador/' . $id . '/display.js')): ?>
<script type="text/javascript" src="indicador/<?= htmlspecialchars($id) ?>/display.js"></script>
<script type="text/javascript">
    var display;
    try {
        display = new Display();
    } catch(e) {
        console.error('Display init error:', e);
        // Show user-friendly message
        document.querySelectorAll('.chart-container').forEach(function(c){
            if (!c.querySelector('svg') && !c.querySelector('canvas') && !c.querySelector('.leaflet-container')) {
                c.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#5d6b80;font-size:.9rem;padding:2rem;text-align:center">' +
                    '<div><i class="fa-solid fa-chart-column" style="font-size:2rem;margin-bottom:.5rem;display:block;opacity:.4"></i>' +
                    'La visualización no pudo cargarse.<br><small>Revise la consola para más detalles.</small></div></div>';
            }
        });
    }
</script>
<?php endif; ?>

<?php else: ?>
<!-- Error state -->
<main class="container py-5">
    <div class="error-box">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <h2>Indicador no encontrado</h2>
        <p><?= htmlspecialchars($error ?? 'El indicador solicitado no existe o no tiene datos disponibles.') ?></p>
        <a href="estado-observatorio.php" class="btn btn-dark mt-3">
            <i class="fa-solid fa-list"></i> Ir al catálogo de indicadores
        </a>
    </div>
</main>
<?php endif; ?>

<?php require __DIR__ . '/include/site-footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php require __DIR__ . '/include/assistant-widget.php'; ?>
</body>
</html>
