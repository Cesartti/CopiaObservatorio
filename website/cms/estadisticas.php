<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_login();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/visit_tracking.php';

$cmsTitle = 'Estadísticas y encuesta';
$cmsNav = 'estadisticas';
$pdo = cms_pdo();

/** Etiqueta legible para un page_key / page_context. */
function est_page_label(string $key): string
{
    $map = [
        'portal' => 'Portal (inicio)',
        'microsite_economico' => 'Económico',
        'microsite_social' => 'Social',
        'microsite_ambiente' => 'Ambiente',
        'microsite_cti' => 'Ciencia y Tecnología',
        'microsite_genero' => 'Género',
    ];
    return $map[$key] ?? $key;
}

/** Ejecuta una consulta y devuelve filas; [] ante cualquier error (tablas/columnas ausentes). */
function est_rows(?PDO $pdo, string $sql): array
{
    if (!$pdo) {
        return [];
    }
    try {
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

function est_scalar(?PDO $pdo, string $sql): int
{
    if (!$pdo) {
        return 0;
    }
    try {
        return (int) $pdo->query($sql)->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

// ---- Visitas ----
$totalUnique = est_scalar($pdo, 'SELECT COUNT(DISTINCT visitor_id) FROM cms_unique_visitors');
$visitsByPage = est_rows($pdo, 'SELECT page_key, COUNT(*) AS c FROM cms_unique_visitors GROUP BY page_key ORDER BY c DESC');
$visitsByDay = est_rows($pdo, 'SELECT DATE(first_seen_at) AS d, COUNT(*) AS c FROM cms_unique_visitors GROUP BY DATE(first_seen_at) ORDER BY d DESC LIMIT 30');
$visitsByDay = array_reverse($visitsByDay);

// ---- Geo (para mapa y países) ----
$geoPoints = est_rows($pdo, "SELECT country, city, lat, lng, COUNT(*) AS c FROM cms_unique_visitors WHERE lat IS NOT NULL AND lng IS NOT NULL GROUP BY country, city, lat, lng ORDER BY c DESC LIMIT 500");
$byCountry = est_rows($pdo, "SELECT COALESCE(country,'(desconocido)') AS country, COUNT(*) AS c FROM cms_unique_visitors WHERE country IS NOT NULL GROUP BY country ORDER BY c DESC LIMIT 30");
$geoKnown = est_scalar($pdo, 'SELECT COUNT(*) FROM cms_unique_visitors WHERE lat IS NOT NULL');
$countriesCount = est_scalar($pdo, 'SELECT COUNT(DISTINCT country) FROM cms_unique_visitors WHERE country IS NOT NULL');

// ---- Encuesta ----
$totalSurveys = est_scalar($pdo, 'SELECT COUNT(*) FROM cms_visitor_surveys');
$surveyByAge = est_rows($pdo, 'SELECT age_range AS k, COUNT(*) AS c FROM cms_visitor_surveys GROUP BY age_range');
$surveyBySector = est_rows($pdo, 'SELECT sector AS k, COUNT(*) AS c FROM cms_visitor_surveys GROUP BY sector');
$surveyByFreq = est_rows($pdo, 'SELECT visit_frequency AS k, COUNT(*) AS c FROM cms_visitor_surveys GROUP BY visit_frequency');
$surveyByCtx = est_rows($pdo, 'SELECT page_context AS k, COUNT(*) AS c FROM cms_visitor_surveys GROUP BY page_context ORDER BY c DESC');

$ageLabels = cms_survey_age_ranges();
$sectorLabels = cms_survey_sectors();
$freqLabels = cms_survey_visit_frequency();

/** Convierte filas (k,c) a [labels, data] aplicando un diccionario de etiquetas. */
function est_chart_data(array $rows, array $labels, string $keyField = 'k'): array
{
    $out = ['labels' => [], 'data' => []];
    foreach ($rows as $r) {
        $k = (string) ($r[$keyField] ?? '');
        $out['labels'][] = $labels[$k] ?? $k;
        $out['data'][] = (int) ($r['c'] ?? 0);
    }
    return $out;
}

$dataVisitsByPage = ['labels' => [], 'data' => []];
foreach ($visitsByPage as $r) {
    $dataVisitsByPage['labels'][] = est_page_label((string) $r['page_key']);
    $dataVisitsByPage['data'][] = (int) $r['c'];
}
$dataVisitsByDay = ['labels' => [], 'data' => []];
foreach ($visitsByDay as $r) {
    $dataVisitsByDay['labels'][] = (string) $r['d'];
    $dataVisitsByDay['data'][] = (int) $r['c'];
}
$dataAge = est_chart_data($surveyByAge, $ageLabels);
$dataSector = est_chart_data($surveyBySector, $sectorLabels);
$dataFreq = est_chart_data($surveyByFreq, $freqLabels);

$mapPoints = [];
foreach ($geoPoints as $g) {
    $mapPoints[] = [
        'lat' => (float) $g['lat'],
        'lng' => (float) $g['lng'],
        'city' => (string) ($g['city'] ?? ''),
        'country' => (string) ($g['country'] ?? ''),
        'c' => (int) $g['c'],
    ];
}

require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>

<h1 class="h4 mb-1">Estadísticas de visitas y encuesta</h1>
<p class="small text-muted">Visitantes únicos por navegador (general y por observatorio), resultados de la encuesta opcional y ubicación aproximada de los visitantes.</p>

<!-- Tarjetas resumen -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-muted small">Visitantes únicos</div>
            <div class="h3 mb-0"><?= number_format($totalUnique, 0, ',', '.') ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-muted small">Respuestas de encuesta</div>
            <div class="h3 mb-0"><?= number_format($totalSurveys, 0, ',', '.') ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-muted small">Países distintos</div>
            <div class="h3 mb-0"><?= number_format($countriesCount, 0, ',', '.') ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-muted small">Visitantes ubicados</div>
            <div class="h3 mb-0"><?= number_format($geoKnown, 0, ',', '.') ?></div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <!-- Visitas por observatorio -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <h2 class="h6">Visitantes por observatorio</h2>
            <canvas id="chartByPage" height="220"></canvas>
        </div></div>
    </div>
    <!-- Visitas por día -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <h2 class="h6">Visitantes por día (últimos 30)</h2>
            <canvas id="chartByDay" height="220"></canvas>
        </div></div>
    </div>
</div>

<!-- Mapa -->
<div class="card shadow-sm border-0 mt-3"><div class="card-body">
    <h2 class="h6">Mapa de visitantes (ubicación aproximada por IP)</h2>
    <?php if ($geoKnown === 0): ?>
        <div class="alert alert-info small mb-2">Aún no hay ubicaciones registradas. Aparecerán cuando visiten desde Internet (la red interna del gobierno puede no geolocalizarse). Verifique también que el servidor tenga salida a Internet.</div>
    <?php endif; ?>
    <div id="mapVisitas" style="height:420px;border-radius:12px;"></div>
</div></div>

<!-- Encuesta -->
<h2 class="h5 mt-4 mb-2">Encuesta opcional</h2>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <h3 class="h6">Por rango de edad</h3>
            <canvas id="chartAge" height="220"></canvas>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <h3 class="h6">Por sector</h3>
            <canvas id="chartSector" height="220"></canvas>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <h3 class="h6">Por frecuencia de uso</h3>
            <canvas id="chartFreq" height="220"></canvas>
        </div></div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <h3 class="h6">Respuestas por observatorio de origen</h3>
            <table class="table table-sm mb-0">
                <thead><tr><th>Observatorio / página</th><th class="text-end">Respuestas</th></tr></thead>
                <tbody>
                <?php if (empty($surveyByCtx)): ?>
                    <tr><td colspan="2" class="text-muted text-center py-2">Sin respuestas todavía.</td></tr>
                <?php else: foreach ($surveyByCtx as $r): ?>
                    <tr><td><?= htmlspecialchars(est_page_label((string) $r['k'])) ?></td><td class="text-end"><?= (int) $r['c'] ?></td></tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100"><div class="card-body">
            <h3 class="h6">Visitantes por país</h3>
            <table class="table table-sm mb-0">
                <thead><tr><th>País</th><th class="text-end">Visitantes</th></tr></thead>
                <tbody>
                <?php if (empty($byCountry)): ?>
                    <tr><td colspan="2" class="text-muted text-center py-2">Sin datos de ubicación todavía.</td></tr>
                <?php else: foreach ($byCountry as $r): ?>
                    <tr><td><?= htmlspecialchars((string) $r['country']) ?></td><td class="text-end"><?= (int) $r['c'] ?></td></tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
</div>

<script>
const EST = {
  byPage: <?= json_encode($dataVisitsByPage, JSON_UNESCAPED_UNICODE) ?>,
  byDay: <?= json_encode($dataVisitsByDay, JSON_UNESCAPED_UNICODE) ?>,
  age: <?= json_encode($dataAge, JSON_UNESCAPED_UNICODE) ?>,
  sector: <?= json_encode($dataSector, JSON_UNESCAPED_UNICODE) ?>,
  freq: <?= json_encode($dataFreq, JSON_UNESCAPED_UNICODE) ?>,
  points: <?= json_encode($mapPoints, JSON_UNESCAPED_UNICODE) ?>
};
const PALETTE = ['#0d6efd','#20c997','#fd7e14','#6f42c1','#dc3545','#0dcaf0','#ffc107','#198754'];

function mkBar(id, d, label){
  const el = document.getElementById(id); if(!el) return;
  new Chart(el, {type:'bar', data:{labels:d.labels, datasets:[{label:label, data:d.data, backgroundColor:'#0d6efd'}]},
    options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true, ticks:{precision:0}}}}});
}
function mkLine(id, d){
  const el = document.getElementById(id); if(!el) return;
  new Chart(el, {type:'line', data:{labels:d.labels, datasets:[{data:d.data, borderColor:'#20c997', backgroundColor:'rgba(32,201,151,.15)', fill:true, tension:.3}]},
    options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true, ticks:{precision:0}}}}});
}
function mkDoughnut(id, d){
  const el = document.getElementById(id); if(!el) return;
  new Chart(el, {type:'doughnut', data:{labels:d.labels, datasets:[{data:d.data, backgroundColor:PALETTE}]},
    options:{plugins:{legend:{position:'bottom', labels:{boxWidth:12, font:{size:11}}}}}});
}

mkBar('chartByPage', EST.byPage, 'Visitantes');
mkLine('chartByDay', EST.byDay);
mkDoughnut('chartAge', EST.age);
mkDoughnut('chartSector', EST.sector);
mkDoughnut('chartFreq', EST.freq);

// Mapa
(function(){
  const el = document.getElementById('mapVisitas'); if(!el || !window.L) return;
  const map = L.map('mapVisitas').setView([4.6, -74.1], 4);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:18, attribution:'&copy; OpenStreetMap'}).addTo(map);
  const pts = EST.points || [];
  const bounds = [];
  pts.forEach(p=>{
    const r = Math.min(28, 6 + Math.sqrt(p.c)*4);
    L.circleMarker([p.lat, p.lng], {radius:r, color:'#0d6efd', fillColor:'#0d6efd', fillOpacity:.45, weight:1})
      .addTo(map)
      .bindPopup('<strong>'+(p.city||'')+(p.city&&p.country?', ':'')+(p.country||'')+'</strong><br>'+p.c+' visitante(s)');
    bounds.push([p.lat, p.lng]);
  });
  if(bounds.length){ try{ map.fitBounds(bounds, {padding:[30,30], maxZoom:8}); }catch(e){} }
})();
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
