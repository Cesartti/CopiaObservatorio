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

function est_rows_p(?PDO $pdo, string $sql, array $p = []): array
{
    if (!$pdo) {
        return [];
    }
    try {
        $st = $pdo->prepare($sql);
        $st->execute($p);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

function est_scalar_p(?PDO $pdo, string $sql, array $p = []): int
{
    if (!$pdo) {
        return 0;
    }
    try {
        $st = $pdo->prepare($sql);
        $st->execute($p);
        return (int) $st->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

function est_chart_data(array $rows, array $labels, string $keyField = 'k'): array
{
    $out = ['labels' => [], 'data' => []];
    foreach ($rows as $r) {
        $k = (string) ($r[$keyField] ?? '');
        $out['labels'][] = $labels[$k] ?? ($k !== '' ? $k : 'Sin dato');
        $out['data'][] = (int) ($r['c'] ?? 0);
    }
    return $out;
}

// ---------------- Filtros (GET) ----------------
$ageLabels = cms_survey_age_ranges();
$genderLabels = cms_survey_genders();
$sectorLabels = cms_survey_sectors();
$freqLabels = cms_survey_visit_frequency();

$fDesde = trim((string) ($_GET['f_desde'] ?? ''));
$fHasta = trim((string) ($_GET['f_hasta'] ?? ''));
$fGenero = (string) ($_GET['f_genero'] ?? '');
$fEdad = (string) ($_GET['f_edad'] ?? '');
$fSector = (string) ($_GET['f_sector'] ?? '');
$fFrec = (string) ($_GET['f_frecuencia'] ?? '');

// Validar (solo se aceptan valores conocidos; las fechas deben ser YYYY-MM-DD)
$datePat = '/^\d{4}-\d{2}-\d{2}$/';
if (!preg_match($datePat, $fDesde)) { $fDesde = ''; }
if (!preg_match($datePat, $fHasta)) { $fHasta = ''; }
if (!isset($genderLabels[$fGenero])) { $fGenero = ''; }
if (!isset($ageLabels[$fEdad])) { $fEdad = ''; }
if (!isset($sectorLabels[$fSector])) { $fSector = ''; }
if (!isset($freqLabels[$fFrec])) { $fFrec = ''; }

// WHERE encuesta (todos los filtros)
$sv = []; $sp = [];
if ($fDesde !== '') { $sv[] = 'created_at >= ?'; $sp[] = $fDesde . ' 00:00:00'; }
if ($fHasta !== '') { $sv[] = 'created_at <= ?'; $sp[] = $fHasta . ' 23:59:59'; }
if ($fGenero !== '') { $sv[] = 'gender = ?'; $sp[] = $fGenero; }
if ($fEdad !== '') { $sv[] = 'age_range = ?'; $sp[] = $fEdad; }
if ($fSector !== '') { $sv[] = 'sector = ?'; $sp[] = $fSector; }
if ($fFrec !== '') { $sv[] = 'visit_frequency = ?'; $sp[] = $fFrec; }
$sWhere = $sv ? ('WHERE ' . implode(' AND ', $sv)) : '';

// WHERE visitas (solo fecha)
$vv = []; $vp = [];
if ($fDesde !== '') { $vv[] = 'first_seen_at >= ?'; $vp[] = $fDesde . ' 00:00:00'; }
if ($fHasta !== '') { $vv[] = 'first_seen_at <= ?'; $vp[] = $fHasta . ' 23:59:59'; }
$vWhere = $vv ? ('WHERE ' . implode(' AND ', $vv)) : '';

$hasFilters = ($fDesde || $fHasta || $fGenero || $fEdad || $fSector || $fFrec);

// ---------------- Visitas (filtradas por fecha) ----------------
$totalUnique = est_scalar_p($pdo, "SELECT COUNT(DISTINCT visitor_id) FROM cms_unique_visitors $vWhere", $vp);
$visitsByPage = est_rows_p($pdo, "SELECT page_key, COUNT(*) AS c FROM cms_unique_visitors $vWhere GROUP BY page_key ORDER BY c DESC", $vp);
$visitsByDay = est_rows_p($pdo, "SELECT DATE(first_seen_at) AS d, COUNT(*) AS c FROM cms_unique_visitors $vWhere GROUP BY DATE(first_seen_at) ORDER BY d DESC LIMIT 60", $vp);
$visitsByDay = array_reverse($visitsByDay);

// Geolocalización de los VISITANTES (para el mapa de "de dónde son")
$vAndGeo = $vWhere ? ($vWhere . ' AND ') : 'WHERE ';
$visitorGeoKnown = est_scalar_p($pdo, "SELECT COUNT(*) FROM cms_unique_visitors {$vAndGeo}lat IS NOT NULL", $vp);
$visitorPoints = est_rows_p($pdo, "SELECT country, city, lat, lng, COUNT(*) AS c FROM cms_unique_visitors {$vAndGeo}lat IS NOT NULL GROUP BY country, city, lat, lng ORDER BY c DESC LIMIT 500", $vp);
$visitorByCountry = est_rows_p($pdo, "SELECT COALESCE(country,'(desconocido)') AS country, COUNT(*) AS c FROM cms_unique_visitors {$vAndGeo}country IS NOT NULL GROUP BY country ORDER BY c DESC LIMIT 30", $vp);

// ---------------- Encuesta (todos los filtros) ----------------
$totalSurveys = est_scalar_p($pdo, "SELECT COUNT(*) FROM cms_visitor_surveys $sWhere", $sp);
$surveyByGender = est_rows_p($pdo, "SELECT gender AS k, COUNT(*) AS c FROM cms_visitor_surveys $sWhere GROUP BY gender", $sp);
$surveyByAge = est_rows_p($pdo, "SELECT age_range AS k, COUNT(*) AS c FROM cms_visitor_surveys $sWhere GROUP BY age_range", $sp);
$surveyBySector = est_rows_p($pdo, "SELECT sector AS k, COUNT(*) AS c FROM cms_visitor_surveys $sWhere GROUP BY sector", $sp);
$surveyByFreq = est_rows_p($pdo, "SELECT visit_frequency AS k, COUNT(*) AS c FROM cms_visitor_surveys $sWhere GROUP BY visit_frequency", $sp);
$surveyByCtx = est_rows_p($pdo, "SELECT page_context AS k, COUNT(*) AS c FROM cms_visitor_surveys $sWhere GROUP BY page_context ORDER BY c DESC", $sp);

// Desglose geográfico de los ENCUESTADOS filtrados (país -> ciudad)
$surveyGeo = est_rows_p(
    $pdo,
    "SELECT COALESCE(country,'(sin ubicación)') AS country, COALESCE(city,'(sin ciudad)') AS city, COUNT(*) AS c
     FROM cms_visitor_surveys $sWhere GROUP BY country, city ORDER BY c DESC LIMIT 100",
    $sp
);
$sWhereGeo = $sWhere ? ($sWhere . ' AND lat IS NOT NULL') : 'WHERE lat IS NOT NULL';
$surveyPoints = est_rows_p(
    $pdo,
    "SELECT country, city, lat, lng, COUNT(*) AS c FROM cms_visitor_surveys $sWhereGeo GROUP BY country, city, lat, lng ORDER BY c DESC LIMIT 500",
    $sp
);
$surveyGeoKnown = est_scalar_p($pdo, "SELECT COUNT(*) FROM cms_visitor_surveys $sWhereGeo", $sp);

// ---------------- Navegación / eventos (filtrado por fecha) ----------------
$obsCfg = is_file(__DIR__ . '/../config/observatories.php') ? (require __DIR__ . '/../config/observatories.php') : [];
$obsLabels = ['portal' => 'Portal (inicio)'];
foreach ($obsCfg as $oslug => $ometa) {
    $obsLabels[(string) $oslug] = (string) ($ometa['name'] ?? $oslug);
}
$evTypeLabels = [
    'page_view' => 'Vistas de página',
    'indicator_view' => 'Indicadores vistos',
    'news_open' => 'Noticias abiertas',
    'tab_open' => 'Pestañas / categorías',
    'powerbi_open' => 'Power BI / tableros',
    'search' => 'Búsquedas',
];

$ep = [];
$eWhere = '';
if ($fDesde !== '' || $fHasta !== '') {
    $ec = [];
    if ($fDesde !== '') { $ec[] = 'created_at >= ?'; $ep[] = $fDesde . ' 00:00:00'; }
    if ($fHasta !== '') { $ec[] = 'created_at <= ?'; $ep[] = $fHasta . ' 23:59:59'; }
    $eWhere = 'WHERE ' . implode(' AND ', $ec);
}
$evAnd = function (string $extra) use ($eWhere): string {
    return $eWhere ? ($eWhere . ' AND ' . $extra) : ('WHERE ' . $extra);
};

$totalEvents = est_scalar_p($pdo, "SELECT COUNT(*) FROM cms_events $eWhere", $ep);
$powerbiOpens = est_scalar_p($pdo, "SELECT COUNT(*) FROM cms_events " . $evAnd("event_type='powerbi_open'"), $ep);
$eventsByType = est_rows_p($pdo, "SELECT event_type AS k, COUNT(*) AS c FROM cms_events $eWhere GROUP BY event_type ORDER BY c DESC", $ep);
$obsConsulted = est_rows_p($pdo, "SELECT observatory AS k, COUNT(*) AS c FROM cms_events " . $evAnd("event_type='page_view'") . " GROUP BY observatory ORDER BY c DESC", $ep);
$topIndicators = est_rows_p($pdo, "SELECT object_id, MAX(label) AS label, observatory, COUNT(*) AS c FROM cms_events " . $evAnd("event_type='indicator_view'") . " GROUP BY object_id, observatory ORDER BY c DESC LIMIT 15", $ep);
$topNews = est_rows_p($pdo, "SELECT object_id, MAX(label) AS label, COUNT(*) AS c FROM cms_events " . $evAnd("event_type='news_open'") . " GROUP BY object_id ORDER BY c DESC LIMIT 15", $ep);

$dataObsConsulted = ['labels' => [], 'data' => []];
foreach ($obsConsulted as $r) {
    $dataObsConsulted['labels'][] = $obsLabels[(string) $r['k']] ?? ((string) $r['k'] !== '' ? (string) $r['k'] : 'Sin dato');
    $dataObsConsulted['data'][] = (int) $r['c'];
}

// Datos para gráficos
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
$dataGender = est_chart_data($surveyByGender, $genderLabels);
$dataAge = est_chart_data($surveyByAge, $ageLabels);
$dataSector = est_chart_data($surveyBySector, $sectorLabels);
$dataFreq = est_chart_data($surveyByFreq, $freqLabels);

$mapPoints = [];
foreach ($surveyPoints as $g) {
    $mapPoints[] = [
        'lat' => (float) $g['lat'],
        'lng' => (float) $g['lng'],
        'city' => (string) ($g['city'] ?? ''),
        'country' => (string) ($g['country'] ?? ''),
        'c' => (int) $g['c'],
    ];
}
$visitorMapPoints = [];
foreach ($visitorPoints as $g) {
    $visitorMapPoints[] = [
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
<p class="small text-muted">Filtra por fecha y por las respuestas de la encuesta para ver cuántos son, qué respondieron y desde dónde se conectan (país y ciudad).</p>

<!-- ===== FILTROS ===== -->
<form method="get" class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="f_desde" value="<?= htmlspecialchars($fDesde) ?>" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="f_hasta" value="<?= htmlspecialchars($fHasta) ?>" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Género</label>
                <select name="f_genero" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($genderLabels as $k => $v): ?>
                        <option value="<?= htmlspecialchars($k) ?>" <?= $fGenero === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Edad</label>
                <select name="f_edad" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($ageLabels as $k => $v): ?>
                        <option value="<?= htmlspecialchars($k) ?>" <?= $fEdad === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Sector</label>
                <select name="f_sector" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($sectorLabels as $k => $v): ?>
                        <option value="<?= htmlspecialchars($k) ?>" <?= $fSector === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Frecuencia</label>
                <select name="f_frecuencia" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($freqLabels as $k => $v): ?>
                        <option value="<?= htmlspecialchars($k) ?>" <?= $fFrec === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="fa-solid fa-filter me-1"></i>Filtrar</button>
            <?php if ($hasFilters): ?><a href="estadisticas.php" class="btn btn-outline-secondary btn-sm">Limpiar</a><?php endif; ?>
        </div>
    </div>
</form>

<!-- Tarjetas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="card shadow-sm border-0"><div class="card-body">
        <div class="text-muted small">Visitantes únicos<?= $hasFilters && ($fDesde || $fHasta) ? ' (en fechas)' : '' ?></div>
        <div class="h3 mb-0"><?= number_format($totalUnique, 0, ',', '.') ?></div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card shadow-sm border-0"><div class="card-body">
        <div class="text-muted small">Respuestas de encuesta<?= $hasFilters ? ' (filtradas)' : '' ?></div>
        <div class="h3 mb-0"><?= number_format($totalSurveys, 0, ',', '.') ?></div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card shadow-sm border-0"><div class="card-body">
        <div class="text-muted small">Encuestados ubicados</div>
        <div class="h3 mb-0"><?= number_format($surveyGeoKnown, 0, ',', '.') ?></div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card shadow-sm border-0"><div class="card-body">
        <div class="text-muted small">Lugares distintos</div>
        <div class="h3 mb-0"><?= number_format(count($surveyGeo), 0, ',', '.') ?></div>
    </div></div></div>
</div>

<!-- Visitas -->
<div class="row g-3">
    <div class="col-lg-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h2 class="h6">Visitantes por observatorio</h2>
        <canvas id="chartByPage" height="220"></canvas>
    </div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h2 class="h6">Visitantes por día</h2>
        <canvas id="chartByDay" height="220"></canvas>
    </div></div></div>
</div>

<!-- Mapa de visitantes -->
<div class="row g-3 mt-1">
    <div class="col-lg-7"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h2 class="h6">Mapa de visitantes — ¿de dónde son?</h2>
        <p class="small text-muted mb-2">Ubicación aproximada por IP de los <?= number_format($totalUnique, 0, ',', '.') ?> visitantes únicos. <?= $visitorGeoKnown ? number_format($visitorGeoKnown, 0, ',', '.') . ' ubicados.' : '' ?></p>
        <?php if ($visitorGeoKnown === 0): ?>
            <div class="alert alert-info small mb-2">Aún no hay visitantes con ubicación para estas fechas (la red interna no se geolocaliza).</div>
        <?php endif; ?>
        <div id="mapVisitantes" style="height:380px;border-radius:12px;"></div>
    </div></div></div>
    <div class="col-lg-5"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h2 class="h6">Visitantes por país</h2>
        <div class="table-responsive" style="max-height:380px;overflow:auto">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>País</th><th class="text-end">Visitantes</th></tr></thead>
                <tbody>
                <?php if (empty($visitorByCountry)): ?>
                    <tr><td colspan="2" class="text-muted text-center py-2">Sin datos de ubicación todavía.</td></tr>
                <?php else: foreach ($visitorByCountry as $r): ?>
                    <tr><td><?= htmlspecialchars((string) $r['country']) ?></td><td class="text-end"><?= (int) $r['c'] ?></td></tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div></div></div>
</div>

<!-- Navegación / comportamiento -->
<h2 class="h5 mt-4 mb-2">Navegación — ¿qué consultan?</h2>
<div class="row g-3 mb-1">
    <div class="col-6 col-lg-3"><div class="card shadow-sm border-0"><div class="card-body">
        <div class="text-muted small">Interacciones registradas</div>
        <div class="h3 mb-0"><?= number_format($totalEvents, 0, ',', '.') ?></div>
    </div></div></div>
    <div class="col-6 col-lg-3"><div class="card shadow-sm border-0"><div class="card-body">
        <div class="text-muted small">Aperturas de Power BI / tableros</div>
        <div class="h3 mb-0"><?= number_format($powerbiOpens, 0, ',', '.') ?></div>
    </div></div></div>
</div>
<div class="row g-3">
    <div class="col-lg-7"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Observatorio más consultado</h3>
        <p class="small text-muted mb-2">Vistas de página por observatorio (incluye si solo se quedan en el portal).</p>
        <canvas id="chartObs" height="200"></canvas>
    </div></div></div>
    <div class="col-lg-5"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Eventos por tipo</h3>
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Tipo de interacción</th><th class="text-end">Cantidad</th></tr></thead>
            <tbody>
            <?php if (empty($eventsByType)): ?>
                <tr><td colspan="2" class="text-muted text-center py-2">Aún no hay interacciones (empiezan a registrarse al navegar el sitio).</td></tr>
            <?php else: foreach ($eventsByType as $r): ?>
                <tr><td><?= htmlspecialchars($evTypeLabels[(string) $r['k']] ?? (string) $r['k']) ?></td><td class="text-end"><?= (int) $r['c'] ?></td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div></div></div>
</div>
<div class="row g-3 mt-1">
    <div class="col-lg-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Indicadores más vistos</h3>
        <div class="table-responsive" style="max-height:320px;overflow:auto">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>Indicador</th><th>Observatorio</th><th class="text-end">Vistas</th></tr></thead>
                <tbody>
                <?php if (empty($topIndicators)): ?>
                    <tr><td colspan="3" class="text-muted text-center py-2">Sin datos para estas fechas.</td></tr>
                <?php else: foreach ($topIndicators as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars(($r['label'] !== null && $r['label'] !== '') ? (string) $r['label'] : ('Indicador ' . (string) $r['object_id'])) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($obsLabels[(string) $r['observatory']] ?? (string) $r['observatory']) ?></td>
                        <td class="text-end"><?= (int) $r['c'] ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Noticias más abiertas</h3>
        <div class="table-responsive" style="max-height:320px;overflow:auto">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>Noticia</th><th class="text-end">Aperturas</th></tr></thead>
                <tbody>
                <?php if (empty($topNews)): ?>
                    <tr><td colspan="2" class="text-muted text-center py-2">Sin datos para estas fechas.</td></tr>
                <?php else: foreach ($topNews as $r): ?>
                    <tr><td><?= htmlspecialchars(($r['label'] !== null && $r['label'] !== '') ? (string) $r['label'] : ('Noticia ' . (string) $r['object_id'])) ?></td><td class="text-end"><?= (int) $r['c'] ?></td></tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div></div></div>
</div>

<!-- Encuesta -->
<h2 class="h5 mt-4 mb-2">Encuesta — perfil de quienes respondieron</h2>
<div class="row g-3">
    <div class="col-lg-3 col-md-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Por género</h3><canvas id="chartGender" height="220"></canvas>
    </div></div></div>
    <div class="col-lg-3 col-md-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Por rango de edad</h3><canvas id="chartAge" height="220"></canvas>
    </div></div></div>
    <div class="col-lg-3 col-md-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Por sector</h3><canvas id="chartSector" height="220"></canvas>
    </div></div></div>
    <div class="col-lg-3 col-md-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Por frecuencia</h3><canvas id="chartFreq" height="220"></canvas>
    </div></div></div>
</div>

<!-- Geografía de los encuestados -->
<h2 class="h5 mt-4 mb-2">¿De dónde son los encuestados?</h2>
<div class="row g-3">
    <div class="col-lg-7"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">Mapa de encuestados (según filtros)</h3>
        <?php if ($surveyGeoKnown === 0): ?>
            <div class="alert alert-info small mb-2">No hay encuestados con ubicación para estos filtros. La ubicación se obtiene por IP al responder; las conexiones desde la red interna no se geolocalizan.</div>
        <?php endif; ?>
        <div id="mapEncuestados" style="height:380px;border-radius:12px;"></div>
    </div></div></div>
    <div class="col-lg-5"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h3 class="h6">País y ciudad</h3>
        <div class="table-responsive" style="max-height:380px;overflow:auto">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>País</th><th>Ciudad</th><th class="text-end">Encuestados</th></tr></thead>
                <tbody>
                <?php if (empty($surveyGeo)): ?>
                    <tr><td colspan="3" class="text-muted text-center py-2">Sin respuestas para estos filtros.</td></tr>
                <?php else: foreach ($surveyGeo as $r): ?>
                    <tr><td><?= htmlspecialchars((string) $r['country']) ?></td><td><?= htmlspecialchars((string) $r['city']) ?></td><td class="text-end"><?= (int) $r['c'] ?></td></tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div></div></div>
</div>

<!-- Encuesta por observatorio de origen -->
<div class="card shadow-sm border-0 mt-3"><div class="card-body">
    <h3 class="h6">Respuestas por observatorio de origen</h3>
    <table class="table table-sm mb-0" style="max-width:520px">
        <thead><tr><th>Observatorio / página</th><th class="text-end">Respuestas</th></tr></thead>
        <tbody>
        <?php if (empty($surveyByCtx)): ?>
            <tr><td colspan="2" class="text-muted text-center py-2">Sin respuestas para estos filtros.</td></tr>
        <?php else: foreach ($surveyByCtx as $r): ?>
            <tr><td><?= htmlspecialchars(est_page_label((string) $r['k'])) ?></td><td class="text-end"><?= (int) $r['c'] ?></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>

<script>
const EST = {
  byPage: <?= json_encode($dataVisitsByPage, JSON_UNESCAPED_UNICODE) ?>,
  byDay: <?= json_encode($dataVisitsByDay, JSON_UNESCAPED_UNICODE) ?>,
  gender: <?= json_encode($dataGender, JSON_UNESCAPED_UNICODE) ?>,
  age: <?= json_encode($dataAge, JSON_UNESCAPED_UNICODE) ?>,
  sector: <?= json_encode($dataSector, JSON_UNESCAPED_UNICODE) ?>,
  freq: <?= json_encode($dataFreq, JSON_UNESCAPED_UNICODE) ?>,
  obsConsulted: <?= json_encode($dataObsConsulted, JSON_UNESCAPED_UNICODE) ?>,
  points: <?= json_encode($mapPoints, JSON_UNESCAPED_UNICODE) ?>,
  visitorPoints: <?= json_encode($visitorMapPoints, JSON_UNESCAPED_UNICODE) ?>
};
const PALETTE = ['#0d6efd','#20c997','#fd7e14','#6f42c1','#dc3545','#0dcaf0','#ffc107','#198754'];

function mkBar(id, d){ const el=document.getElementById(id); if(!el)return;
  new Chart(el,{type:'bar',data:{labels:d.labels,datasets:[{data:d.data,backgroundColor:'#0d6efd'}]},
    options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}}); }
function mkLine(id, d){ const el=document.getElementById(id); if(!el)return;
  new Chart(el,{type:'line',data:{labels:d.labels,datasets:[{data:d.data,borderColor:'#20c997',backgroundColor:'rgba(32,201,151,.15)',fill:true,tension:.3}]},
    options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}}); }
function mkDoughnut(id, d){ const el=document.getElementById(id); if(!el)return;
  new Chart(el,{type:'doughnut',data:{labels:d.labels,datasets:[{data:d.data,backgroundColor:PALETTE}]},
    options:{plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:10}}}}}}); }

mkBar('chartByPage', EST.byPage);
mkLine('chartByDay', EST.byDay);
mkBar('chartObs', EST.obsConsulted);
mkDoughnut('chartGender', EST.gender);
mkDoughnut('chartAge', EST.age);
mkDoughnut('chartSector', EST.sector);
mkDoughnut('chartFreq', EST.freq);

function drawMap(elId, pts, color, noun){
  const el=document.getElementById(elId); if(!el||!window.L)return;
  const map=L.map(elId).setView([4.6,-74.1],4);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:18,attribution:'&copy; OpenStreetMap'}).addTo(map);
  const bounds=[];
  (pts||[]).forEach(p=>{
    const r=Math.min(28,6+Math.sqrt(p.c)*4);
    L.circleMarker([p.lat,p.lng],{radius:r,color:color,fillColor:color,fillOpacity:.45,weight:1})
     .addTo(map).bindPopup('<strong>'+(p.city||'')+(p.city&&p.country?', ':'')+(p.country||'')+'</strong><br>'+p.c+' '+noun);
    bounds.push([p.lat,p.lng]);
  });
  if(bounds.length){ try{ map.fitBounds(bounds,{padding:[30,30],maxZoom:8}); }catch(e){} }
  // Si el mapa se crea en un contenedor que cambió de tamaño, recalcular.
  setTimeout(()=>{ try{ map.invalidateSize(); }catch(e){} }, 200);
}
drawMap('mapVisitantes', EST.visitorPoints, '#0d6efd', 'visitante(s)');
drawMap('mapEncuestados', EST.points, '#6f42c1', 'encuestado(s)');
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
