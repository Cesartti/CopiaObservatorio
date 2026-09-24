<?php
/**
 * Pestaña "Fenómenos en Boyacá" del Observatorio Ambiental.
 *
 * Subpestañas: El Niño · La Niña · Mapa de novedades (con reporte ciudadano)
 * · Estado del agua · Directorio de bomberos. Los datos vienen de
 * api/fenomenos.php y, para el agua, del visor FEWS del IDEAM.
 *
 * Variables del scope (observatorio.php): $obs, $slug.
 */
require_once __DIR__ . '/../lib/fenomenos.php';
require_once __DIR__ . '/../lib/agua.php';

$fenEnso = fen_estado_enso();
$fenHist = fen_historico();
$fenTot = $fenHist['totales'] ?? [];
$fenMun = $fenHist['municipios'] ?? [];

/** Agrega el histórico por fenómeno para las fichas de cada subpestaña. */
$fenResumen = ['nino' => ['eventos' => 0, 'municipios' => 0, 'top' => [], 'tipos' => []],
               'nina' => ['eventos' => 0, 'municipios' => 0, 'top' => [], 'tipos' => []]];
foreach ($fenMun as $dane => $m) {
    foreach (['nino', 'nina'] as $f) {
        $n = (int) ($m['fenomenos'][$f] ?? 0);
        if ($n > 0) {
            $fenResumen[$f]['eventos'] += $n;
            $fenResumen[$f]['municipios']++;
            $fenResumen[$f]['top'][$m['nombre']] = $n;
        }
    }
}
foreach (['nino', 'nina'] as $f) {
    arsort($fenResumen[$f]['top']);
    $fenResumen[$f]['top'] = array_slice($fenResumen[$f]['top'], 0, 8, true);
}
// Tipos de evento por fenómeno (etiquetas fijas del generador de datos).
$fenTiposPorFen = [
    'nino' => ['Incendio de cobertura vegetal', 'Desabastecimiento de agua', 'Sequía', 'Helada'],
    'nina' => ['Inundación', 'Movimiento en masa', 'Vendaval', 'Creciente súbita o avenida torrencial',
               'Lluvia torrencial o temporal', 'Granizada'],
];
foreach (['nino', 'nina'] as $f) {
    foreach ($fenTiposPorFen[$f] as $t) {
        $n = (int) ($fenTot['tipos'][$t] ?? 0);
        if ($n > 0) {
            $fenResumen[$f]['tipos'][$t] = $n;
        }
    }
}
// Estado del agua: embalse, estaciones hidrológicas y riesgo de
// desabastecimiento. Si las fuentes externas no responden, $agua queda con los
// arreglos vacíos y la subpestaña muestra un aviso en vez de romperse.
$agua = agua_resumen();
$aguaEmbalse = $agua['embalses']['boyaca'][0] ?? null;
$aguaPorEstado = [];
foreach ($agua['estaciones_nivel'] as $e) {
    $aguaPorEstado[$e['estado']] = ($aguaPorEstado[$e['estado']] ?? 0) + 1;
}
$aguaCategorias = [];
foreach ($agua['desabastecimiento'] as $d) {
    $aguaCategorias[$d['categoria']] = ($aguaCategorias[$d['categoria']] ?? 0) + 1;
}
arsort($aguaCategorias);

$fenFaseActual = (string) ($fenEnso['fase'] ?? 'neutral');
$fenAniosFen = $fenTot['anios_fenomeno'] ?? [];
$fenPorFuente = $fenTot['por_fuente'] ?? [];
$fenOniAnual = $fenEnso['anual'] ?? [];

/**
 * Gráfica de barras en SVG: eventos por año, con una franja inferior que
 * indica la fase del ENSO de cada año según el promedio anual del ONI.
 * Se dibuja en el servidor para que no dependa de JavaScript.
 */
function fen_grafica(array $serie, string $color, array $oniAnual, string $titulo): string
{
    $serie = array_filter($serie, static fn ($v) => $v > 0);
    if ($serie === []) {
        return '';
    }
    $anios = array_keys($serie);
    $max = max($serie);
    $n = count($serie);
    $w = 640; $h = 240; $pl = 46; $pr = 12; $pt = 18; $pb = 54;
    $gw = $w - $pl - $pr; $gh = $h - $pt - $pb;
    $bw = min(58, ($gw / $n) * 0.62);
    $paso = $gw / $n;

    $svg = '<svg viewBox="0 0 ' . $w . ' ' . $h . '" role="img" class="fen-svg" '
         . 'aria-label="' . htmlspecialchars($titulo) . '">';
    // rejilla y eje Y
    for ($i = 0; $i <= 4; $i++) {
        $v = $max * $i / 4;
        $y = $pt + $gh - ($gh * $i / 4);
        $svg .= '<line x1="' . $pl . '" y1="' . round($y, 1) . '" x2="' . ($w - $pr) . '" y2="' . round($y, 1)
              . '" stroke="#e6ecf6" stroke-width="1"/>'
              . '<text x="' . ($pl - 8) . '" y="' . round($y + 4, 1) . '" text-anchor="end" font-size="11" fill="#6b7280">'
              . number_format($v, 0, ',', '.') . '</text>';
    }
    $i = 0;
    foreach ($serie as $anio => $val) {
        $x = $pl + $paso * $i + ($paso - $bw) / 2;
        $bh = $max > 0 ? ($gh * $val / $max) : 0;
        $y = $pt + $gh - $bh;
        $svg .= '<rect x="' . round($x, 1) . '" y="' . round($y, 1) . '" width="' . round($bw, 1)
              . '" height="' . round($bh, 1) . '" rx="4" fill="' . $color . '">'
              . '<title>' . htmlspecialchars((string) $anio) . ': ' . number_format($val, 0, ',', '.') . ' eventos</title></rect>'
              . '<text x="' . round($x + $bw / 2, 1) . '" y="' . round($y - 5, 1) . '" text-anchor="middle" '
              . 'font-size="11" font-weight="700" fill="#374151">' . number_format($val, 0, ',', '.') . '</text>'
              . '<text x="' . round($x + $bw / 2, 1) . '" y="' . ($pt + $gh + 16) . '" text-anchor="middle" '
              . 'font-size="11" fill="#4b5768">' . htmlspecialchars((string) $anio) . '</text>';
        // franja del ENSO
        $oni = $oniAnual[(int) $anio] ?? null;
        if ($oni !== null) {
            $c = $oni >= 0.5 ? '#ea580c' : ($oni <= -0.5 ? '#0891b2' : '#cbd5e1');
            $et = $oni >= 0.5 ? 'Niño' : ($oni <= -0.5 ? 'Niña' : 'neutral');
            $svg .= '<rect x="' . round($pl + $paso * $i + 3, 1) . '" y="' . ($pt + $gh + 24) . '" '
                  . 'width="' . round($paso - 6, 1) . '" height="9" rx="4" fill="' . $c . '">'
                  . '<title>ONI ' . number_format($oni, 2, ',', '.') . ' · ' . $et . '</title></rect>'
                  . '<text x="' . round($pl + $paso * $i + $paso / 2, 1) . '" y="' . ($pt + $gh + 46) . '" '
                  . 'text-anchor="middle" font-size="10" fill="#6b7280">' . $et . '</text>';
        }
        $i++;
    }
    $svg .= '</svg>';

    return $svg;
}
?>
<style>
    .fen-estado{border-radius:16px;padding:1.1rem 1.25rem;color:#fff;display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between;margin-bottom:1rem}
    .fen-estado--nino{background:linear-gradient(135deg,#9a3412,#ea580c)}
    .fen-estado--nina{background:linear-gradient(135deg,#1e3a8a,#0891b2)}
    .fen-estado--neutral{background:linear-gradient(135deg,#334155,#64748b)}
    .fen-estado h3{font-size:1.15rem;font-weight:800;margin:0 0 .2rem}
    .fen-estado small{opacity:.9;font-size:.82rem}
    .fen-estado__oni{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.28);border-radius:14px;padding:.6rem 1rem;text-align:center;min-width:132px}
    .fen-estado__oni b{display:block;font-size:1.75rem;line-height:1.1;font-weight:800}
    .fen-estado__oni span{font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;opacity:.9}
    .fen-subnav{display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1rem}
    .fen-subnav button{border:1px solid #dce4f2;background:#fff;color:#2f3b50;padding:.5rem .95rem;border-radius:10px;font-size:.85rem;font-weight:600;cursor:pointer;transition:all .18s}
    .fen-subnav button:hover{border-color:var(--obs-color,#1f6b45);color:var(--obs-color,#1f6b45)}
    .fen-subnav button.active{background:var(--obs-color,#1f6b45);border-color:var(--obs-color,#1f6b45);color:#fff}
    .fen-pane{display:none}
    .fen-pane.active{display:block}
    .fen-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:.75rem;margin:.9rem 0}
    .fen-kpi{background:#f8fafc;border:1px solid #e6ecf6;border-radius:12px;padding:.85rem .95rem}
    .fen-kpi b{display:block;font-size:1.5rem;font-weight:800;color:var(--obs-color,#1f6b45);line-height:1.1}
    .fen-kpi span{font-size:.78rem;color:#5d6b80}
    .fen-lista{list-style:none;padding:0;margin:.4rem 0 0}
    .fen-lista li{display:flex;justify-content:space-between;gap:.75rem;padding:.35rem 0;border-bottom:1px dashed #e6ecf6;font-size:.87rem}
    .fen-lista li:last-child{border-bottom:none}
    .fen-lista li b{color:var(--obs-color,#1f6b45)}
    #fenMapa,#fenMapaAgua{width:100%;height:540px;border-radius:14px;border:1px solid #e6ecf6;background:#eef2f7;z-index:0}
    .fen-map-tools{display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;margin-bottom:.6rem}
    .fen-map-tools select,.fen-map-tools input{font-size:.85rem;padding:.35rem .55rem;border:1px solid #dce4f2;border-radius:9px}
    .fen-leyenda{display:flex;flex-wrap:wrap;gap:.9rem;margin-top:.6rem;font-size:.8rem;color:#4b5768}
    .fen-leyenda i{width:12px;height:12px;border-radius:50%;display:inline-block;margin-right:.3rem;vertical-align:-1px}
    .fen-form{background:#f8fafc;border:1px solid #e6ecf6;border-radius:14px;padding:1rem}
    .fen-form label{font-size:.8rem;font-weight:600;color:#3b4759;margin-bottom:.2rem;display:block}
    .fen-form .form-control,.fen-form .form-select{font-size:.88rem}
    .fen-dir-tools{display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;margin-bottom:.5rem}
    .fen-dir-busca{position:relative;flex:1 1 320px;max-width:440px}
    .fen-dir-busca i{position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.85rem}
    .fen-dir-busca input{padding-left:2.1rem;font-size:.9rem}
    .fen-dir-tools .form-select{width:auto;min-width:190px;font-size:.88rem}
    .fen-dir-lista{display:grid;grid-template-columns:repeat(auto-fill,minmax(330px,1fr));gap:.75rem}
    .fen-dir-card{background:#fff;border:1px solid #e6ecf6;border-radius:14px;padding:.9rem 1rem;box-shadow:0 3px 12px rgba(2,6,23,.05)}
    .fen-dir-card h6{font-size:1rem;font-weight:800;color:#132033;margin:0 0 .15rem;display:flex;align-items:center;gap:.45rem;flex-wrap:wrap}
    .fen-dir-prov{font-size:.76rem;color:#5d6b80;margin-bottom:.6rem}
    .fen-badge{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-radius:999px;padding:.15rem .5rem}
    .fen-badge--propia{background:#dcfce7;color:#166534}
    .fen-badge--apoyo{background:#fef3c7;color:#92400e}
    .fen-op{display:flex;gap:.6rem;align-items:flex-start;padding:.45rem 0;border-top:1px dashed #eef2f7}
    .fen-op:first-of-type{border-top:none}
    .fen-op__n{width:22px;height:22px;border-radius:50%;background:rgba(220,38,38,.12);color:#b91c1c;font-size:.72rem;font-weight:800;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;margin-top:.1rem}
    .fen-op__cuerpo{font-weight:600;font-size:.88rem;color:#1f2937;line-height:1.25}
    .fen-op__sede{font-size:.76rem;color:#6b7280}
    .fen-op__tel{display:inline-flex;flex-wrap:wrap;gap:.35rem;margin-top:.15rem}
    .fen-op__tel a{font-size:.82rem;font-weight:700;color:#b91c1c;text-decoration:none;background:rgba(220,38,38,.08);border-radius:8px;padding:.1rem .45rem}
    .fen-op__tel a:hover{background:rgba(220,38,38,.16)}
    .fen-bomberos-item{display:flex;gap:.75rem;align-items:flex-start;padding:.7rem .2rem;border-bottom:1px solid #eef2f7}
    .fen-bomberos-item:last-child{border-bottom:none}
    .fen-bomberos-item .fb-ico{width:38px;height:38px;border-radius:10px;background:rgba(220,38,38,.1);color:#dc2626;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto}
    .fen-figura{margin:1.1rem 0 0;background:#fff;border:1px solid #e6ecf6;border-radius:14px;padding:.9rem 1rem}
    .fen-figura figcaption{margin-bottom:.5rem}
    .fen-figura figcaption strong{display:block;font-size:.9rem;color:#1f2937}
    .fen-figura figcaption span{font-size:.78rem;color:#6b7280}
    .fen-svg{width:100%;height:auto;display:block}
    .fen-fuente{margin-top:1.1rem;background:#f8fafc;border:1px solid #e6ecf6;border-left:4px solid var(--obs-color,#1f6b45);border-radius:12px;padding:.9rem 1.05rem}
    .fen-fuente h5{font-size:.85rem;font-weight:700;color:#1f2937;margin:0 0 .5rem}
    .fen-fuente p{font-size:.82rem;color:#4b5768;margin-bottom:.5rem}
    .fen-fuente ul{margin:0 0 .6rem;padding-left:1.1rem}
    .fen-fuente li{font-size:.82rem;color:#4b5768;margin-bottom:.3rem}
    .fen-sin-focos{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;border-radius:10px;padding:.55rem .8rem;font-size:.84rem;margin-bottom:.6rem}
    .fen-aviso{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:12px;padding:.7rem .9rem;font-size:.84rem}
    @media (max-width:575.98px){#fenMapa,#fenMapaAgua{height:420px}}
</style>

<article class="content-card">
    <h3>Fenómenos en Boyacá</h3>
    <p class="small text-muted mb-3">
        Seguimiento a la variabilidad climática de El Niño y La Niña, y a las novedades
        que estos fenómenos dejan en el territorio. Incluye un mapa de calor con las
        emergencias registradas, el reporte ciudadano de alertas y el directorio de
        bomberos por municipio.
    </p>

    <div class="fen-estado fen-estado--<?= htmlspecialchars($fenFaseActual) ?>">
        <div>
            <h3><?= htmlspecialchars((string) $fenEnso['estado']) ?></h3>
            <small>
                <?php if ($fenEnso['periodo']): ?>
                    Último trimestre calculado: <strong><?= htmlspecialchars((string) $fenEnso['periodo']) ?></strong> ·
                <?php endif; ?>
                <?= htmlspecialchars((string) $fenEnso['fuente']) ?>
            </small>
        </div>
        <div class="fen-estado__oni">
            <b><?= $fenEnso['oni'] !== null ? number_format((float) $fenEnso['oni'], 2, ',', '.') : '—' ?></b>
            <span>Índice ONI (°C)</span>
        </div>
    </div>
    <p class="small text-muted">
        El índice ONI mide la diferencia de temperatura del océano Pacífico frente a su promedio.
        Por encima de <strong>+0,5&nbsp;°C</strong> se declara El Niño y por debajo de <strong>−0,5&nbsp;°C</strong>, La Niña.
        <a href="<?= htmlspecialchars((string) $fenEnso['url']) ?>" target="_blank" rel="noopener">Ver la fuente oficial</a>.
    </p>

    <div class="fen-subnav" role="tablist">
        <button type="button" class="active" data-fen="nino"><i class="fa-solid fa-sun me-1" aria-hidden="true"></i> Fenómeno de El Niño</button>
        <button type="button" data-fen="nina"><i class="fa-solid fa-cloud-showers-heavy me-1" aria-hidden="true"></i> Fenómeno de La Niña</button>
        <button type="button" data-fen="mapa"><i class="fa-solid fa-fire me-1" aria-hidden="true"></i> Mapa de novedades</button>
        <button type="button" data-fen="agua"><i class="fa-solid fa-droplet me-1" aria-hidden="true"></i> Estado del agua</button>
        <button type="button" data-fen="bomberos"><i class="fa-solid fa-truck-medical me-1" aria-hidden="true"></i> Directorio de bomberos</button>
    </div>

    <?php
    /* ---------------------------------------------------------------- El Niño */
    $r = $fenResumen['nino'];
    ?>
    <section class="fen-pane active" id="fen-nino">
        <h4 class="h6 fw-bold">¿Qué es el fenómeno de El Niño?</h4>
        <p class="small">
            El Niño es la fase cálida de la variabilidad climática del océano Pacífico tropical.
            Cuando ocurre, en la mayor parte del territorio colombiano <strong>llueve menos de lo
            normal y sube la temperatura</strong>. En Boyacá eso se traduce en temporadas secas más
            largas e intensas, con mayor riesgo de incendios de la cobertura vegetal, disminución
            de los caudales, desabastecimiento de agua en acueductos rurales y afectación de
            cultivos y pastos. Las heladas de la madrugada también se hacen más frecuentes en el
            altiplano, porque los cielos despejados dejan escapar el calor del suelo.
        </p>
        <div class="fen-grid">
            <div class="fen-kpi"><b><?= number_format($r['eventos'], 0, ',', '.') ?></b><span>Emergencias registradas asociadas a condiciones secas</span></div>
            <div class="fen-kpi"><b><?= (int) $r['municipios'] ?></b><span>Municipios con al menos un evento</span></div>
            <div class="fen-kpi"><b><?= number_format((int) ($fenTot['tipos']['Incendio de cobertura vegetal'] ?? 0), 0, ',', '.') ?></b><span>Incendios de la cobertura vegetal</span></div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <h5 class="h6 fw-bold">Municipios con más eventos</h5>
                <ul class="fen-lista">
                    <?php foreach ($r['top'] as $mun => $n): ?>
                        <li><span><?= htmlspecialchars((string) $mun) ?></span> <b><?= (int) $n ?></b></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-6">
                <h5 class="h6 fw-bold">Tipo de novedad</h5>
                <ul class="fen-lista">
                    <?php foreach ($r['tipos'] as $t => $n): ?>
                        <li><span><?= htmlspecialchars((string) $t) ?></span> <b><?= number_format((int) $n, 0, ',', '.') ?></b></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php $g = fen_grafica($fenAniosFen['nino'] ?? [], '#ea580c', $fenOniAnual, 'Emergencias asociadas a condiciones secas por año'); ?>
        <?php if ($g !== ''): ?>
        <figure class="fen-figura">
            <figcaption>
                <strong>Emergencias asociadas a condiciones secas, por año</strong>
                <span>La franja inferior indica la fase del ENSO de cada año, según el promedio anual del índice ONI.</span>
            </figcaption>
            <?= $g ?>
        </figure>
        <?php endif; ?>
        <div class="fen-fuente">
            <h5><i class="fa-solid fa-database me-1" aria-hidden="true"></i> De dónde salen estos datos</h5>
            <p>
                Las cifras de esta sección no son estimaciones: son el conteo de los eventos
                efectivamente registrados por las entidades oficiales entre 2019 y 2025, descargados
                de sus portales de datos abiertos y clasificados por tipo de evento.
            </p>
            <ul>
                <?php foreach (($fenHist['fuentes'] ?? []) as $fu):
                    $clave = strpos($fu['nombre'], 'UNGRD') !== false ? 'UNGRD'
                           : (strpos($fu['nombre'], 'CORPOBOYAC') !== false ? 'CORPOBOYACÁ' : 'CDGRD Boyacá');
                    $n = (int) ($fenPorFuente[$clave] ?? 0);
                ?>
                    <li>
                        <a href="<?= htmlspecialchars((string) $fu['url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars((string) $fu['nombre']) ?></a>
                        — <?= htmlspecialchars((string) $fu['detalle']) ?><?php if ($n > 0): ?>
                            · <strong><?= number_format($n, 0, ',', '.') ?></strong> eventos<?php endif; ?>
                    </li>
                <?php endforeach; ?>
                <li>
                    <a href="<?= htmlspecialchars((string) $fenEnso['url']) ?>" target="_blank" rel="noopener">NOAA · Climate Prediction Center</a>
                    — índice ONI, usado para clasificar la fase del ENSO de cada año.
                </li>
            </ul>
            <p class="mb-0">
                La asignación de cada evento a El Niño o a La Niña se hace por el <em>tipo</em> de evento
                (los incendios, la sequía, el desabastecimiento y las heladas se asocian a condiciones secas;
                las inundaciones, los movimientos en masa, los vendavales y las crecientes, al exceso de lluvia),
                no por la fecha. Es una clasificación temática, no una atribución causal:
                un incendio puede ocurrir en un año de La Niña.
                <?php if (!empty($fenHist['generado'])): ?>
                    Datos descargados el <?= htmlspecialchars((string) $fenHist['generado']) ?>.
                <?php endif; ?>
            </p>
        </div>
        <p class="small text-muted mt-3 mb-0">
            <i class="fa-solid fa-circle-info me-1" aria-hidden="true"></i>
            Qué hacer: evitar quemas abiertas, reportar cualquier conato en el mapa de novedades o
            a la línea <strong>119</strong>, y seguir las recomendaciones de ahorro de agua del
            prestador del servicio en su municipio.
        </p>
    </section>

    <?php
    /* ---------------------------------------------------------------- La Niña */
    $r = $fenResumen['nina'];
    ?>
    <section class="fen-pane" id="fen-nina">
        <h4 class="h6 fw-bold">¿Qué es el fenómeno de La Niña?</h4>
        <p class="small">
            La Niña es la fase fría del mismo ciclo y produce el efecto contrario:
            <strong>lluvias por encima de lo normal</strong> durante varios meses. En Boyacá aumentan
            las inundaciones en las zonas bajas y riberas, los movimientos en masa y deslizamientos
            en las vías de montaña, las crecientes súbitas de quebradas y los vendavales que
            destechan viviendas. El suelo saturado sostiene el riesgo durante semanas, incluso
            después de que cesan las lluvias fuertes.
        </p>
        <div class="fen-grid">
            <div class="fen-kpi"><b><?= number_format($r['eventos'], 0, ',', '.') ?></b><span>Emergencias registradas asociadas a exceso de lluvia</span></div>
            <div class="fen-kpi"><b><?= (int) $r['municipios'] ?></b><span>Municipios con al menos un evento</span></div>
            <div class="fen-kpi"><b><?= number_format((int) ($fenTot['tipos']['Movimiento en masa'] ?? 0), 0, ',', '.') ?></b><span>Movimientos en masa y deslizamientos</span></div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <h5 class="h6 fw-bold">Municipios con más eventos</h5>
                <ul class="fen-lista">
                    <?php foreach ($r['top'] as $mun => $n): ?>
                        <li><span><?= htmlspecialchars((string) $mun) ?></span> <b><?= (int) $n ?></b></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-6">
                <h5 class="h6 fw-bold">Tipo de novedad</h5>
                <ul class="fen-lista">
                    <?php foreach ($r['tipos'] as $t => $n): ?>
                        <li><span><?= htmlspecialchars((string) $t) ?></span> <b><?= number_format((int) $n, 0, ',', '.') ?></b></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php $g = fen_grafica($fenAniosFen['nina'] ?? [], '#0891b2', $fenOniAnual, 'Emergencias asociadas a lluvias por año'); ?>
        <?php if ($g !== ''): ?>
        <figure class="fen-figura">
            <figcaption>
                <strong>Emergencias asociadas a exceso de lluvias, por año</strong>
                <span>La franja inferior indica la fase del ENSO de cada año, según el promedio anual del índice ONI.</span>
            </figcaption>
            <?= $g ?>
        </figure>
        <?php endif; ?>
        <div class="fen-fuente">
            <h5><i class="fa-solid fa-database me-1" aria-hidden="true"></i> De dónde salen estos datos</h5>
            <p>
                Las cifras de esta sección no son estimaciones: son el conteo de los eventos
                efectivamente registrados por las entidades oficiales entre 2019 y 2025, descargados
                de sus portales de datos abiertos y clasificados por tipo de evento.
            </p>
            <ul>
                <?php foreach (($fenHist['fuentes'] ?? []) as $fu):
                    $clave = strpos($fu['nombre'], 'UNGRD') !== false ? 'UNGRD'
                           : (strpos($fu['nombre'], 'CORPOBOYAC') !== false ? 'CORPOBOYACÁ' : 'CDGRD Boyacá');
                    $n = (int) ($fenPorFuente[$clave] ?? 0);
                ?>
                    <li>
                        <a href="<?= htmlspecialchars((string) $fu['url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars((string) $fu['nombre']) ?></a>
                        — <?= htmlspecialchars((string) $fu['detalle']) ?><?php if ($n > 0): ?>
                            · <strong><?= number_format($n, 0, ',', '.') ?></strong> eventos<?php endif; ?>
                    </li>
                <?php endforeach; ?>
                <li>
                    <a href="<?= htmlspecialchars((string) $fenEnso['url']) ?>" target="_blank" rel="noopener">NOAA · Climate Prediction Center</a>
                    — índice ONI, usado para clasificar la fase del ENSO de cada año.
                </li>
            </ul>
            <p class="mb-0">
                La asignación de cada evento a El Niño o a La Niña se hace por el <em>tipo</em> de evento
                (los incendios, la sequía, el desabastecimiento y las heladas se asocian a condiciones secas;
                las inundaciones, los movimientos en masa, los vendavales y las crecientes, al exceso de lluvia),
                no por la fecha. Es una clasificación temática, no una atribución causal:
                un incendio puede ocurrir en un año de La Niña.
                <?php if (!empty($fenHist['generado'])): ?>
                    Datos descargados el <?= htmlspecialchars((string) $fenHist['generado']) ?>.
                <?php endif; ?>
            </p>
        </div>
        <p class="small text-muted mt-3 mb-0">
            <i class="fa-solid fa-circle-info me-1" aria-hidden="true"></i>
            Qué hacer: no cruzar corrientes crecidas, atender los cierres viales, reportar
            deslizamientos en el mapa de novedades y llamar a la línea <strong>123</strong> ante
            una emergencia en curso.
        </p>
    </section>

    <?php /* ------------------------------------------------------ Mapa de calor */ ?>
    <section class="fen-pane" id="fen-mapa">
        <div class="fen-map-tools">
            <label class="mb-0 small fw-semibold" for="fenFiltroFen">Fenómeno</label>
            <select id="fenFiltroFen" class="form-select form-select-sm" style="width:auto">
                <option value="todos">Todos</option>
                <option value="nino">El Niño (sequía e incendios)</option>
                <option value="nina">La Niña (lluvias e inundaciones)</option>
            </select>
            <label class="mb-0 small fw-semibold" for="fenFiltroAnio">Año</label>
            <select id="fenFiltroAnio" class="form-select form-select-sm" style="width:auto">
                <option value="todos">Todos</option>
            </select>
            <button type="button" class="btn btn-sm btn-danger" id="fenBtnReportar">
                <i class="fa-solid fa-triangle-exclamation me-1" aria-hidden="true"></i> Reportar una alerta
            </button>
        </div>

        <p class="fen-sin-focos" id="fenSinFocos" style="display:none">
            <i class="fa-solid fa-circle-check me-1" aria-hidden="true"></i>
            Hoy el satélite no detecta focos de calor activos dentro de Boyacá.
            El mapa sigue mostrando el histórico de emergencias y, en gris, los focos
            detectados en departamentos vecinos.
        </p>
        <div id="fenMapa" role="application" aria-label="Mapa de novedades ambientales de Boyacá"></div>

        <div class="fen-leyenda">
            <span><i style="background:linear-gradient(90deg,#22c55e,#eab308,#ef4444)"></i> Intensidad de emergencias históricas</span>
            <span><i style="background:#dc2626"></i> Foco de calor en Boyacá (satélite)</span>
            <span><i style="background:#d1d5db"></i> Foco en departamento vecino</span>
            <span><i style="background:#7c3aed"></i> Reporte ciudadano</span>
            <span><i style="background:#0ea5e9"></i> Cuerpo de bomberos</span>
        </div>
        <p class="small text-muted mt-2 mb-0" id="fenFuentes"></p>

        <div class="mt-3" id="fenFormWrap" hidden>
            <div class="fen-form">
                <h5 class="h6 fw-bold mb-2"><i class="fa-solid fa-location-dot me-1" aria-hidden="true"></i> Reportar una alerta ambiental</h5>
                <p class="small text-muted">
                    Marque el punto en el mapa o use su ubicación. El reporte llega al equipo de
                    gestión del riesgo para verificación; <strong>no reemplaza la llamada a la línea
                    de emergencias</strong>. Si hay riesgo para la vida llame al <strong>123</strong>
                    o al <strong>119</strong>.
                </p>
                <form id="fenForm" class="row g-2">
                    <div class="col-md-4">
                        <label for="fenTipo">Tipo de alerta</label>
                        <select id="fenTipo" name="tipo" class="form-select form-select-sm" required>
                            <option value="incendio">Incendio de cobertura vegetal</option>
                            <option value="inundacion">Inundación</option>
                            <option value="deslizamiento">Deslizamiento o movimiento en masa</option>
                            <option value="vendaval">Vendaval</option>
                            <option value="desabastecimiento">Desabastecimiento de agua</option>
                            <option value="otro">Otra novedad ambiental</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fenMunicipio">Municipio</label>
                        <input type="text" id="fenMunicipio" name="municipio" class="form-control form-control-sm" placeholder="Se completa al marcar el punto" readonly>
                        <input type="hidden" id="fenDane" name="municipio_dane">
                        <input type="hidden" id="fenLat" name="lat">
                        <input type="hidden" id="fenLon" name="lon">
                    </div>
                    <div class="col-md-4">
                        <label for="fenReferencia">Vereda o punto de referencia</label>
                        <input type="text" id="fenReferencia" name="referencia" class="form-control form-control-sm" maxlength="200" placeholder="Ej: vereda El Salitre, km 3 vía Tunja">
                    </div>
                    <div class="col-md-8">
                        <label for="fenDescripcion">¿Qué está ocurriendo?</label>
                        <textarea id="fenDescripcion" name="descripcion" class="form-control form-control-sm" rows="2" maxlength="800" required placeholder="Describa lo que observa: extensión aproximada, si hay viviendas cerca, hace cuánto empezó…"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="fenContacto">Contacto (opcional)</label>
                        <input type="text" id="fenContacto" name="contacto" class="form-control form-control-sm" maxlength="120" placeholder="Teléfono o correo">
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2 align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="fenBtnUbicacion">
                            <i class="fa-solid fa-crosshairs me-1" aria-hidden="true"></i> Usar mi ubicación
                        </button>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-paper-plane me-1" aria-hidden="true"></i> Enviar reporte
                        </button>
                        <span class="small text-muted" id="fenFormMsg"></span>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php /* ----------------------------------------------------- Estado del agua */ ?>
    <section class="fen-pane" id="fen-agua">
        <h4 class="h6 fw-bold">¿Cuánta agua tiene hoy Boyacá?</h4>
        <p class="small">
            El agua es el termómetro de El Niño y de La Niña: cuando el fenómeno cálido se
            instala bajan los caudales y los acueductos rurales empiezan a racionar, y cuando
            llega La Niña los ríos suben y se desbordan. Esta sección toma en tiempo real el
            <strong>volumen del embalse</strong>, el <strong>nivel de los ríos</strong> y el
            <strong>riesgo de desabastecimiento de cada municipio</strong>, para saber dónde hay
            capacidad de agua y dónde conviene anticipar medidas.
        </p>

        <?php if (!$agua['hay_datos']): ?>
            <div class="fen-aviso mb-3">
                <strong>Las fuentes del IDEAM no respondieron.</strong>
                La sección vuelve a mostrar los datos apenas se restablezca la conexión con el
                visor FEWS. Consulte mientras tanto
                <a href="<?= htmlspecialchars(AGUA_VISOR) ?>" target="_blank" rel="noopener">el visor oficial</a>.
            </div>
        <?php else: ?>

        <div class="fen-grid">
            <?php if ($aguaEmbalse !== null && $aguaEmbalse['pct'] !== null): ?>
                <div class="fen-kpi">
                    <b><?= number_format((float) $aguaEmbalse['pct'], 1, ',', '.') ?>&nbsp;%</b>
                    <span>Volumen útil del embalse La Esmeralda (Chivor), el único de Boyacá
                        en el sistema nacional<?= $agua['embalses']['promedio_pais'] !== null
                            ? '. El promedio del país está en ' . number_format((float) $agua['embalses']['promedio_pais'], 1, ',', '.') . ' %'
                            : '' ?></span>
                </div>
            <?php endif; ?>
            <div class="fen-kpi">
                <b><?= (int) $agua['estaciones_alerta'] ?></b>
                <span>Estaciones de nivel en alerta, de
                    <?= (int) $agua['estaciones_con_dato'] ?> con lectura reciente en Boyacá</span>
            </div>
            <div class="fen-kpi">
                <b><?= (int) $agua['municipios_seco'] ?></b>
                <span>Municipios con riesgo de desabastecimiento en temporada seca,
                    de <?= (int) $agua['municipios_total'] ?></span>
            </div>
            <?php if ($agua['vhi']['municipios']): ?>
                <div class="fen-kpi">
                    <b><?= (int) $agua['vhi_estres'] ?></b>
                    <span>Municipios con estrés de la vegetación por sequía (índice VHI
                        de la NOAA, semana del <?= htmlspecialchars($agua['vhi']['corte']) ?>)</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="fen-map-tools">
            <label class="mb-0 small fw-semibold" for="aguaCapa">Ver en el mapa</label>
            <select id="aguaCapa" class="form-select form-select-sm" style="width:auto">
                <option value="desabastecimiento">Riesgo de desabastecimiento de agua</option>
                <?php if ($agua['vhi']['municipios']): ?>
                    <option value="vhi">Sequía agrícola · índice VHI (satélite)</option>
                <?php endif; ?>
            </select>

            <label class="mb-0 small fw-semibold" for="aguaProvincia">Provincia</label>
            <select id="aguaProvincia" class="form-select form-select-sm" style="width:auto">
                <option value="">Todas</option>
            </select>

            <label class="mb-0 small fw-semibold" for="aguaSeveridad">Mostrar</label>
            <select id="aguaSeveridad" class="form-select form-select-sm" style="width:auto">
                <option value="">Todos los municipios</option>
                <option value="alerta">Solo los que están en alerta</option>
            </select>

            <div class="form-check form-switch mb-0 ms-1">
                <input class="form-check-input" type="checkbox" id="aguaVerEstaciones" checked>
                <label class="form-check-label small" for="aguaVerEstaciones">Estaciones</label>
            </div>
        </div>

        <div class="fen-map-tools" id="aguaTiempoWrap" hidden>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="aguaPlay"
                    aria-label="Reproducir la secuencia en el tiempo">
                <i class="fa-solid fa-play" aria-hidden="true"></i>
            </button>
            <input type="range" class="form-range flex-grow-1" id="aguaTiempo"
                   min="0" max="0" value="0" style="min-width:180px"
                   aria-label="Semana que se muestra en el mapa">
            <span class="small fw-semibold" id="aguaFecha" style="min-width:9rem"></span>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="aguaHoy">Hoy</button>
        </div>
        <p class="small text-muted mb-2" id="aguaCargando" hidden>
            <i class="fa-solid fa-circle-notch fa-spin me-1" aria-hidden="true"></i>
            Cargando el histórico…
        </p>

        <div id="fenMapaAgua" role="application"
             aria-label="Mapa del estado del agua en Boyacá"
             data-agua="<?= htmlspecialchars(json_encode([
                 'estaciones' => $agua['estaciones_nivel'],
                 'embalses' => $agua['embalses']['boyaca'],
                 'municipios' => $agua['desabastecimiento'],
                 'vhi' => $agua['vhi']['municipios'],
                 'provincias' => agua_provincias(),
             ], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>"></div>

        <div class="fen-leyenda" id="aguaLeyDes">
            <span><i style="background:#b45309"></i> Riesgo en temporada seca y húmeda</span>
            <span><i style="background:#f59e0b"></i> Riesgo en temporada seca</span>
            <span><i style="background:#60a5fa"></i> Riesgo en temporada húmeda</span>
            <span><i style="background:#d1fae5"></i> Sin afectación</span>
            <span><i style="background:#16a34a;border-radius:50%"></i> Estación con nivel normal</span>
            <span><i style="background:#dc2626;border-radius:50%"></i> Estación en alerta</span>
            <span><i style="background:#0ea5e9;border-radius:50%"></i> Embalse</span>
        </div>
        <div class="fen-leyenda" id="aguaLeyVhi" style="display:none">
            <span><i style="background:#b91c1c"></i> VHI menor que 20 · sequía severa</span>
            <span><i style="background:#f59e0b"></i> 20 a 40 · estrés moderado</span>
            <span><i style="background:#86efac"></i> 40 a 60 · condición favorable</span>
            <span><i style="background:#15803d"></i> Mayor que 60 · vegetación vigorosa</span>
        </div>
        <p class="small text-muted mt-2" id="aguaPie">
            El color del municipio indica en qué temporada el IDEAM identificó riesgo de
            desabastecimiento; los puntos son estaciones hidrológicas con su última lectura.
        </p>

        <?php
        // Evolución del embalse: se dibuja en el servidor, como las demás
        // gráficas de la pestaña, para que no dependa de JavaScript.
        $aguaSerie = agua_serie_embalse();
        if (count($aguaSerie['valores']) > 30):
            $sv = $aguaSerie['valores'];
            $sf = $aguaSerie['fechas'];
            $n = count($sv);
            $w = 720;
            $h = 170;
            $mi = max(0.0, floor(((float) $aguaSerie['min'] - 5) / 10) * 10);
            $ma = min(100.0, ceil(((float) $aguaSerie['max'] + 5) / 10) * 10);
            $rango = max($ma - $mi, 1);
            $x = static fn ($i) => round(40 + ($i / max($n - 1, 1)) * ($w - 55), 1);
            $y = static fn ($val) => round($h - 26 - (($val - $mi) / $rango) * ($h - 46), 1);
            $linea = '';
            foreach ($sv as $i => $val) {
                $linea .= ($i ? ' L' : 'M') . $x($i) . ' ' . $y((float) $val);
            }
        ?>
            <figure class="fen-figura mt-4">
                <figcaption class="h6 fw-bold">
                    Cómo ha cambiado el embalse La Esmeralda
                    <span class="text-muted fw-normal small">
                        (<?= htmlspecialchars($sf[0]) ?> a <?= htmlspecialchars($sf[$n - 1]) ?>)
                    </span>
                </figcaption>
                <svg viewBox="0 0 <?= $w ?> <?= $h ?>" class="fen-svg" role="img"
                     aria-label="Volumen útil diario del embalse La Esmeralda entre <?= htmlspecialchars($sf[0]) ?> y <?= htmlspecialchars($sf[$n - 1]) ?>">
                    <?php for ($g = 0; $g <= 4; $g++):
                        $val = $mi + $rango * $g / 4; ?>
                        <line x1="40" x2="<?= $w - 15 ?>" y1="<?= $y($val) ?>" y2="<?= $y($val) ?>"
                              stroke="#e6ecf6" stroke-width="1"/>
                        <text x="34" y="<?= $y($val) + 4 ?>" text-anchor="end"
                              font-size="10" fill="#6b7280"><?= (int) round($val) ?>%</text>
                    <?php endfor; ?>
                    <path d="<?= $linea ?>" fill="none" stroke="#0ea5e9" stroke-width="2"
                          stroke-linejoin="round"/>
                    <circle cx="<?= $x($n - 1) ?>" cy="<?= $y((float) $sv[$n - 1]) ?>" r="4" fill="#0ea5e9"/>
                    <?php foreach ([0, intdiv($n - 1, 2), $n - 1] as $i): ?>
                        <text x="<?= $x($i) ?>" y="<?= $h - 6 ?>"
                              text-anchor="<?= $i === 0 ? 'start' : ($i === $n - 1 ? 'end' : 'middle') ?>"
                              font-size="10" fill="#6b7280"><?= htmlspecialchars(substr($sf[$i], 0, 7)) ?></text>
                    <?php endforeach; ?>
                </svg>
                <p class="small text-muted mb-0">
                    Volumen útil diario. En el periodo el embalse se movió entre
                    <strong><?= number_format((float) $aguaSerie['min'], 1, ',', '.') ?>&nbsp;%</strong> y
                    <strong><?= number_format((float) $aguaSerie['max'], 1, ',', '.') ?>&nbsp;%</strong>.
                    Fuente: XM.
                </p>
            </figure>
        <?php endif; ?>

        <?php
        $aguaAlertas = array_values(array_filter(
            $agua['estaciones_nivel'],
            static fn ($e) => in_array($e['estado'], ['roja', 'naranja', 'amarilla'], true)
        ));
        ?>
        <?php if ($aguaAlertas): ?>
            <h5 class="h6 fw-bold mt-4">Estaciones con alerta hoy</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Estación</th>
                            <th scope="col">Municipio</th>
                            <th scope="col">Corriente</th>
                            <th scope="col" class="text-end">Último nivel</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aguaAlertas as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['nombre']) ?></td>
                                <td><?= htmlspecialchars($e['municipio']) ?></td>
                                <td><?= htmlspecialchars($e['corriente']) ?></td>
                                <td class="text-end">
                                    <?= $e['valor'] === null ? '—'
                                        : number_format((float) $e['valor'], 2, ',', '.') . ' ' . $e['unidad'] ?>
                                </td>
                                <td><?= htmlspecialchars(agua_etiqueta_estado($e['estado'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="small mt-3">
                <i class="fa-solid fa-circle-check me-1" aria-hidden="true"></i>
                Ninguna estación de Boyacá reporta alerta en este momento.
            </p>
        <?php endif; ?>

        <?php if ($aguaCategorias): ?>
            <h5 class="h6 fw-bold mt-4">Municipios según el riesgo de desabastecimiento</h5>
            <ul class="fen-lista">
                <?php foreach ($aguaCategorias as $cat => $n): ?>
                    <li><span><?= htmlspecialchars($cat) ?></span><b><?= (int) $n ?></b></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php endif; ?>

        <div class="fen-fuente">
            <h5><i class="fa-solid fa-database me-1" aria-hidden="true"></i> De dónde salen estos datos</h5>
            <p>
                Esta sección no calcula nada por su cuenta: consulta directamente las capas que
                el IDEAM publica en su visor FEWS y las presenta recortadas a Boyacá.
                <?php if ($aguaEmbalse !== null && $aguaEmbalse['fecha'] !== ''): ?>
                    Último corte del embalse: <strong><?= htmlspecialchars($aguaEmbalse['fecha']) ?></strong>.
                <?php endif; ?>
            </p>
            <ul>
                <?php foreach (agua_fuentes() as $fu): ?>
                    <li>
                        <a href="<?= htmlspecialchars($fu['url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($fu['nombre']) ?></a>
                        — <?= htmlspecialchars($fu['detalle']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="small mb-0">
                Boyacá tiene un solo embalse dentro del sistema eléctrico nacional, que es el que
                se reporta a diario. Los embalses administrados por CORPOBOYACÁ —La Copa, Sochagota,
                Teatinos y Gachaneca, entre otros— no se publican por API todavía, así que no
                aparecen aquí.
            </p>
        </div>
    </section>

    <?php /* ------------------------------------------------- Directorio bomberos */ ?>
    <section class="fen-pane" id="fen-bomberos">
        <div class="fen-aviso mb-3">
            <strong>Ante una emergencia en curso, llame primero.</strong>
            Línea única de emergencias <a href="tel:123">123</a> ·
            Línea nacional de bomberos <a href="tel:119">119</a>.
            Busque abajo el cuerpo de bomberos que atiende su municipio.
        </div>

        <div class="fen-dir-tools">
            <div class="fen-dir-busca">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="fenBuscaBomberos" class="form-control"
                       placeholder="Escriba su municipio (por ejemplo: Almeida, Tunja, Chiquinquirá)"
                       aria-label="Buscar municipio o cuerpo de bomberos" autocomplete="off">
            </div>
            <select id="fenFiltroProv" class="form-select" aria-label="Filtrar por provincia">
                <option value="">Todas las provincias</option>
            </select>
            <select id="fenFiltroPropia" class="form-select" aria-label="Filtrar por estación propia">
                <option value="">Con y sin estación propia</option>
                <option value="si">Solo con estación propia</option>
                <option value="no">Solo sin estación propia</option>
            </select>
            <button type="button" id="fenLimpiaDir" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-rotate-left me-1" aria-hidden="true"></i> Limpiar
            </button>
        </div>
        <p class="small text-muted mb-2" id="fenDirResumen" aria-live="polite"></p>

        <div id="fenBomberosLista" class="fen-dir-lista"><p class="text-muted small">Cargando directorio…</p></div>

        <div class="fen-fuente mt-3">
            <h5><i class="fa-solid fa-database me-1" aria-hidden="true"></i> Sobre este directorio</h5>
            <p class="mb-0">
                Boyacá tiene <strong>51 cuerpos de bomberos</strong> que cubren los <strong>123 municipios</strong>:
                50 cuentan con estación propia y los 73 restantes son atendidos por el cuerpo más
                cercano, en el orden de respuesta definido por la Secretaría de Planeación.
                Fuente: <a href="https://dnbc.gov.co/directorio-nacional-de-bomberos/" target="_blank" rel="noopener">Directorio Nacional de Bomberos</a>
                y <a href="https://bomberos.boyaca.gov.co/estaciones/" target="_blank" rel="noopener">Cuerpos de Bomberos de Boyacá</a>.
                Si detecta un dato desactualizado, escríbanos para corregirlo.
            </p>
        </div>
    </section>
</article>

<script>
(function () {
    var API = 'api/fenomenos.php';
    var estado = { mapa: null, capaCalor: null, capaFocos: null, capaReportes: null,
                   capaBomberos: null, marcadorReporte: null, historico: null, bomberos: [],
                   cobertura: [], porCuerpo: {} };

    /* ---- subpestañas ---- */
    document.querySelectorAll('.fen-subnav button').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.fen-subnav button').forEach(function (x) { x.classList.remove('active'); });
            document.querySelectorAll('.fen-pane').forEach(function (p) { p.classList.remove('active'); });
            b.classList.add('active');
            var pane = document.getElementById('fen-' + b.dataset.fen);
            if (pane) { pane.classList.add('active'); }
            if (b.dataset.fen === 'mapa') {
                iniciarMapa();
                if (estado.mapa) {
                    setTimeout(function () { estado.mapa.invalidateSize(); pintarCalor(); }, 180);
                }
            }
            if (b.dataset.fen === 'agua') { iniciarMapaAgua(); }
            if (b.dataset.fen === 'bomberos') { cargarBomberos(); }
        });
    });

    function cargarScript(src, cb) {
        var s = document.createElement('script'); s.src = src; s.onload = cb; document.head.appendChild(s);
    }

    /* ---- mapa del estado del agua ---- */
    var agua = { mapa: null };

    // Color del municipio según la temporada en que el IDEAM identificó riesgo.
    function aguaColor(categoria) {
        if (/seca\s*-\s*h/i.test(categoria)) { return '#b45309'; }
        if (/seca/i.test(categoria)) { return '#f59e0b'; }
        if (/h[uú]meda/i.test(categoria)) { return '#60a5fa'; }
        return '#d1fae5';
    }

    // Escala del índice VHI, con los mismos cortes que usa la NOAA.
    function aguaColorVhi(v) {
        if (v === null || v === undefined) { return '#e5e7eb'; }
        if (v < 20) { return '#b91c1c'; }
        if (v < 40) { return '#f59e0b'; }
        if (v < 60) { return '#86efac'; }
        return '#15803d';
    }

    function iniciarMapaAgua() {
        if (agua.mapa) { setTimeout(function () { agua.mapa.invalidateSize(); }, 60); return; }
        if (typeof L === 'undefined') {
            var css = document.createElement('link');
            css.rel = 'stylesheet'; css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(css);
            cargarScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', cargarLimites);
        } else {
            cargarLimites();
        }
    }

    // Los límites municipales ya existen en el sitio: se cargan solo cuando
    // se abre esta subpestaña, porque pesan medio mega.
    function cargarLimites() {
        if (typeof boyacaData !== 'undefined') { construirMapaAgua(); return; }
        cargarScript('assets/js/boyaca_low.js', construirMapaAgua);
    }

    function construirMapaAgua(intento) {
        var cont = document.getElementById('fenMapaAgua');
        if (!cont) { return; }
        // Leaflet necesita que el contenedor ya tenga ancho.
        if (cont.clientWidth === 0) {
            intento = (intento || 0) + 1;
            if (intento <= 20) { setTimeout(function () { construirMapaAgua(intento); }, 120); }
            return;
        }
        if (agua.mapa) { agua.mapa.invalidateSize(); return; }

        var datos;
        try { datos = JSON.parse(cont.dataset.agua || '{}'); } catch (e) { datos = {}; }
        var municipios = datos.municipios || {};

        agua.mapa = L.map('fenMapaAgua', { scrollWheelZoom: false }).setView([5.62, -73.35], 8);
        var ESRI = 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/';
        var fondo = L.tileLayer(ESRI + 'World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 16, attribution: 'Esri, HERE, Garmin, &copy; OpenStreetMap'
        });
        var respaldo = false;
        fondo.on('tileerror', function () {
            if (respaldo) { return; }
            respaldo = true;
            agua.mapa.removeLayer(fondo);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18, attribution: '&copy; OpenStreetMap'
            }).addTo(agua.mapa);
        });
        fondo.addTo(agua.mapa);

        var vhi = datos.vhi || {};
        var provincias = datos.provincias || {};
        agua.vista = 'desabastecimiento';
        agua.provincia = '';
        agua.soloAlerta = false;
        agua.semana = null;   // índice en el histórico; null = la foto de hoy

        // Valor del VHI que debe pintarse: el de hoy o el de la semana elegida.
        function vhiDe(dane) {
            if (agua.semana !== null && agua.hist && agua.hist.vhi) {
                var s = agua.hist.vhi.municipios[dane];
                return (s && s[agua.semana] !== undefined) ? s[agua.semana] : null;
            }
            var v = vhi[dane];
            return v ? v.vhi : null;
        }

        function visible(dane) {
            if (agua.provincia) {
                var p = provincias[dane];
                if (!p || p.provincia !== agua.provincia) { return false; }
            }
            if (agua.soloAlerta) {
                if (agua.vista === 'vhi') {
                    var v = vhiDe(dane);
                    if (v === null || v >= 40) { return false; }
                } else {
                    var m = municipios[dane];
                    if (!m || !m.seco) { return false; }
                }
            }
            return true;
        }

        function estiloMunicipio(f) {
            var dane = f.properties.id;
            if (!visible(dane)) {
                // Se atenúa en vez de ocultarse, para no perder la silueta del mapa.
                return { fillColor: '#f1f5f9', fillOpacity: 0.35, color: '#ffffff', weight: 0.6 };
            }
            var color;
            if (agua.vista === 'vhi') {
                color = aguaColorVhi(vhiDe(dane));
            } else {
                var m = municipios[dane];
                color = aguaColor(m ? m.categoria : '');
            }
            return { fillColor: color, fillOpacity: 0.78, color: '#ffffff', weight: 1 };
        }

        if (typeof boyacaData !== 'undefined') {
            agua.capaMun = L.geoJSON(boyacaData, {
                style: estiloMunicipio,
                onEachFeature: function (f, capa) {
                    capa.on('popupopen', function () { capa.setPopupContent(ficha(f)); });
                    capa.bindPopup(ficha(f));
                }
            }).addTo(agua.mapa);
        }

        function ficha(f) {
            var m = municipios[f.properties.id];
            var v = vhiDe(f.properties.id);
            var p = provincias[f.properties.id];
            // La ficha muestra siempre las dos lecturas: sirve para cruzar
            // sequía con disponibilidad de agua sin cambiar de capa.
            var html = '<strong>' + f.properties.name + '</strong>';
            if (p) { html += '<br><span class="text-muted">Provincia de ' + p.provincia + '</span>'; }
            html += '<br>Agua: ' + (m ? m.categoria : 'sin información');
            if (v !== null && v !== undefined) {
                html += '<br>Vegetación: VHI ' + v
                    + (v < 20 ? ' · sequía severa' : (v < 40 ? ' · estrés moderado' : ' · favorable'));
                if (agua.semana !== null && agua.hist) {
                    html += '<br><span class="text-muted">Semana del '
                        + agua.hist.vhi.fechas[agua.semana] + '</span>';
                }
            }
            return html;
        }

        function repintar() {
            if (agua.capaMun) { agua.capaMun.setStyle(estiloMunicipio); }
            if (agua.capaEst) {
                agua.capaEst.eachLayer(function (c) {
                    var dentro = (!agua.provincia || c.options.provincia === agua.provincia);
                    c.setStyle({ opacity: dentro ? 1 : 0.15, fillOpacity: dentro ? 0.95 : 0.15 });
                });
            }
        }

        /* ---- filtros ---- */
        var listaProv = [];
        Object.keys(provincias).forEach(function (d) {
            if (listaProv.indexOf(provincias[d].provincia) === -1) {
                listaProv.push(provincias[d].provincia);
            }
        });
        var selProv = document.getElementById('aguaProvincia');
        listaProv.sort().forEach(function (p) {
            var o = document.createElement('option');
            o.value = p; o.textContent = p;
            selProv.appendChild(o);
        });
        selProv.addEventListener('change', function () {
            agua.provincia = selProv.value;
            repintar();
        });

        document.getElementById('aguaSeveridad').addEventListener('change', function () {
            agua.soloAlerta = (this.value === 'alerta');
            repintar();
        });

        document.getElementById('aguaVerEstaciones').addEventListener('change', function () {
            if (!agua.capaEst) { return; }
            if (this.checked) { agua.capaEst.addTo(agua.mapa); }
            else { agua.mapa.removeLayer(agua.capaEst); }
        });

        var sel = document.getElementById('aguaCapa');
        if (sel) {
            sel.addEventListener('change', function () {
                agua.vista = sel.value;
                var esVhi = (agua.vista === 'vhi');
                document.getElementById('aguaLeyDes').style.display = esVhi ? 'none' : '';
                document.getElementById('aguaLeyVhi').style.display = esVhi ? '' : 'none';
                document.getElementById('aguaPie').textContent = esVhi
                    ? 'El índice VHI combina el estrés hídrico y el térmico de la vegetación frente a su serie histórica: por debajo de 40 el cultivo está sufriendo. Se calcula con el producto satelital semanal de la NOAA.'
                    : 'El color del municipio indica en qué temporada el IDEAM identificó riesgo de desabastecimiento; los puntos son estaciones hidrológicas con su última lectura.';
                // El riesgo de desabastecimiento es un estudio fijo: no tiene
                // línea de tiempo, así que el control solo aparece con el VHI.
                document.getElementById('aguaTiempoWrap').hidden = !esVhi;
                if (esVhi) { cargarHistorico(); } else { detenerReproduccion(); agua.semana = null; }
                repintar();
            });
        }

        /* ---- línea de tiempo ---- */
        function cargarHistorico() {
            if (agua.hist || agua.cargando) { prepararTiempo(); return; }
            agua.cargando = true;
            document.getElementById('aguaCargando').hidden = false;
            fetch('api/agua.php?recurso=historico')
                .then(function (r) { return r.json(); })
                .then(function (j) {
                    agua.hist = (j && j.vhi && j.vhi.fechas && j.vhi.fechas.length) ? j : null;
                    prepararTiempo();
                })
                .catch(function () { agua.hist = null; })
                .finally(function () {
                    agua.cargando = false;
                    document.getElementById('aguaCargando').hidden = true;
                });
        }

        function prepararTiempo() {
            var wrap = document.getElementById('aguaTiempoWrap');
            if (!agua.hist) { wrap.hidden = true; return; }
            var r = document.getElementById('aguaTiempo');
            r.max = agua.hist.vhi.fechas.length - 1;
            r.value = r.max;
            agua.semana = null;           // al abrir se muestra la foto de hoy
            document.getElementById('aguaFecha').textContent =
                'Hoy · ' + agua.hist.vhi.fechas[r.max];
            wrap.hidden = false;
        }

        document.getElementById('aguaTiempo').addEventListener('input', function () {
            if (!agua.hist) { return; }
            agua.semana = parseInt(this.value, 10);
            document.getElementById('aguaFecha').textContent =
                'Semana del ' + agua.hist.vhi.fechas[agua.semana];
            repintar();
        });

        document.getElementById('aguaHoy').addEventListener('click', function () {
            detenerReproduccion();
            agua.semana = null;
            if (agua.hist) {
                var r = document.getElementById('aguaTiempo');
                r.value = r.max;
                document.getElementById('aguaFecha').textContent =
                    'Hoy · ' + agua.hist.vhi.fechas[r.max];
            }
            repintar();
        });

        function detenerReproduccion() {
            if (agua.timer) { clearInterval(agua.timer); agua.timer = null; }
            var b = document.getElementById('aguaPlay');
            if (b) { b.innerHTML = '<i class="fa-solid fa-play" aria-hidden="true"></i>'; }
        }

        document.getElementById('aguaPlay').addEventListener('click', function () {
            if (!agua.hist) { return; }
            if (agua.timer) { detenerReproduccion(); return; }
            var r = document.getElementById('aguaTiempo');
            this.innerHTML = '<i class="fa-solid fa-pause" aria-hidden="true"></i>';
            agua.semana = 0;
            r.value = 0;
            agua.timer = setInterval(function () {
                agua.semana++;
                if (agua.semana > parseInt(r.max, 10)) { detenerReproduccion(); return; }
                r.value = agua.semana;
                document.getElementById('aguaFecha').textContent =
                    'Semana del ' + agua.hist.vhi.fechas[agua.semana];
                repintar();
            }, 550);
        });

        // Las estaciones van en su propia capa para poder filtrarlas y ocultarlas.
        agua.capaEst = L.layerGroup();
        (datos.estaciones || []).forEach(function (e) {
            var alerta = (e.estado === 'roja' || e.estado === 'naranja' || e.estado === 'amarilla');
            // El nombre del municipio viene del IDEAM, así que se busca la
            // provincia por coincidencia de nombre, no por código.
            var prov = '';
            Object.keys(provincias).forEach(function (d) {
                if (!prov && provincias[d].municipio
                    && provincias[d].municipio.toUpperCase() === (e.municipio || '').toUpperCase()) {
                    prov = provincias[d].provincia;
                }
            });
            L.circleMarker([e.lat, e.lon], {
                radius: alerta ? 7 : 5,
                fillColor: alerta ? '#dc2626' : (e.estado === 'sin_dato' ? '#9ca3af' : '#16a34a'),
                color: '#ffffff', weight: 1.5, fillOpacity: 0.95, provincia: prov
            }).bindPopup('<strong>' + e.nombre + '</strong><br>'
                + (e.municipio || '') + (e.corriente ? ' · río ' + e.corriente : '') + '<br>'
                + (e.valor === null ? 'Sin lectura reciente'
                    : 'Nivel ' + e.valor + ' ' + e.unidad)).addTo(agua.capaEst);
        });
        agua.capaEst.addTo(agua.mapa);

        (datos.embalses || []).forEach(function (b) {
            L.circleMarker([b.lat, b.lon], {
                radius: 11, fillColor: '#0ea5e9', color: '#ffffff', weight: 2, fillOpacity: 0.95
            }).bindPopup('<strong>' + b.nombre + '</strong><br>Volumen útil: '
                + (b.pct === null ? 'sin dato' : b.pct + ' %')
                + (b.fecha ? '<br>Corte: ' + b.fecha : '')).addTo(agua.mapa);
        });
    }

    /* ---- mapa ---- */
    function iniciarMapa() {
        if (estado.mapa) { setTimeout(function () { estado.mapa.invalidateSize(); }, 60); return; }
        if (typeof L === 'undefined') {
            var css = document.createElement('link');
            css.rel = 'stylesheet'; css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(css);
            cargarScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', function () {
                cargarScript('https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js', construirMapa);
            });
        } else if (typeof L.heatLayer === 'undefined') {
            cargarScript('https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js', construirMapa);
        } else {
            construirMapa();
        }
    }

    function construirMapa(intento) {
        // Leaflet y la capa de calor necesitan que el contenedor ya tenga tamaño:
        // si la subpestaña acaba de mostrarse, se espera un instante.
        var cont = document.getElementById('fenMapa');
        if (!cont || cont.clientWidth === 0) {
            intento = (intento || 0) + 1;
            if (intento <= 20) { setTimeout(function () { construirMapa(intento); }, 120); }
            return;
        }
        if (estado.mapa) { estado.mapa.invalidateSize(); return; }
        estado.mapa = L.map('fenMapa', { scrollWheelZoom: false }).setView([5.62, -73.35], 8);
        // Fondo gris claro sin clave de API, para que resalten los focos y el calor.
        var ESRI = 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/';
        var fondo = L.tileLayer(ESRI + 'World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 16, attribution: 'Esri, HERE, Garmin, &copy; OpenStreetMap'
        });
        var respaldo = false;
        fondo.on('tileerror', function () {
            if (respaldo) { return; }
            respaldo = true;
            estado.mapa.removeLayer(fondo);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 17, attribution: '&copy; OpenStreetMap'
            }).addTo(estado.mapa);
        });
        fondo.addTo(estado.mapa);
        L.tileLayer(ESRI + 'World_Light_Gray_Reference/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 16, attribution: 'Esri, HERE, Garmin, &copy; OpenStreetMap'
        }).addTo(estado.mapa);
        estado.capaFocos = L.layerGroup().addTo(estado.mapa);
        estado.capaReportes = L.layerGroup().addTo(estado.mapa);
        estado.capaBomberos = L.layerGroup();
        L.control.layers(null, {
            'Focos de calor activos': estado.capaFocos,
            'Reportes ciudadanos': estado.capaReportes,
            'Cuerpos de bomberos': estado.capaBomberos
        }, { collapsed: false }).addTo(estado.mapa);

        estado.mapa.on('click', function (e) { fijarPunto(e.latlng.lat, e.latlng.lng); });
        setTimeout(function () { estado.mapa.invalidateSize(); }, 200);

        fetch(API + '?recurso=todo').then(function (r) { return r.json(); }).then(function (d) {
            estado.historico = d.historico || {};
            llenarAnios();

            var CONF = { h: 'alta', n: 'nominal', l: 'baja' };
            (d.focos || []).forEach(function (f) {
                var propio = f.en_boyaca !== false;
                L.circleMarker([f.lat, f.lon], {
                    radius: propio ? 8 : 5,
                    color: propio ? '#7f1d1d' : '#9ca3af',
                    weight: 1,
                    fillColor: propio ? '#dc2626' : '#d1d5db',
                    fillOpacity: propio ? .9 : .5
                }).bindPopup('<strong>Foco de calor' + (propio ? '' : ' (fuera de Boyacá)') + '</strong><br>' +
                    (f.fecha || '') +
                    (f.confianza ? '<br>Confianza de la detección: ' + (CONF[f.confianza] || f.confianza) : '') +
                    (f.potencia ? '<br>Potencia radiativa: ' + f.potencia + ' MW' : '') +
                    '<br><em class="text-muted">Detección térmica satelital, no es un incendio confirmado.</em>'
                ).addTo(estado.capaFocos);
            });
            (d.reportes || []).forEach(function (r) {
                L.circleMarker([r.lat, r.lon], {
                    radius: 7, color: '#4c1d95', weight: 1, fillColor: '#7c3aed', fillOpacity: .85
                }).bindPopup('<strong>Reporte ciudadano</strong><br>' + (r.tipo || '') +
                    '<br>' + (r.municipio || '') + '<br><em>' + (r.estado || '') + '</em>'
                ).addTo(estado.capaReportes);
            });
            estado.bomberos = d.bomberos || [];
            estado.bomberos.forEach(function (b) {
                if (!b.lat || !b.lon) { return; }
                L.circleMarker([b.lat, b.lon], {
                    radius: 7, color: '#075985', weight: 1, fillColor: '#0ea5e9', fillOpacity: .85
                }).bindPopup('<strong>' + (b.nombre || '') + '</strong><br>' + (b.municipio || '') +
                    (b.telefono ? '<br>Tel: ' + b.telefono : '')
                ).addTo(estado.capaBomberos);
            });

            var f = document.getElementById('fenFuentes');
            var fuentes = ((estado.historico.fuentes) || []).map(function (x) { return x.nombre; }).join(' · ');
            var resumen = 'Focos activos: ' + (d.focos_fuente || '—');
            if (typeof d.focos_en_boyaca === 'number') {
                resumen += d.focos_en_boyaca === 0
                    ? ' — hoy el satélite no detecta focos dentro de Boyacá' +
                      (d.focos_vecinos ? ', y sí ' + d.focos_vecinos + ' en el área circundante (en gris)' : '') +
                      '. El número cambia cada día.'
                    : ' — ' + d.focos_en_boyaca + ' en Boyacá y ' + (d.focos_vecinos || 0) +
                      ' en el área circundante (en gris).';
                resumen += ' Un foco es una detección térmica del satélite, no un incendio confirmado.';
            }
            var avSin = document.getElementById('fenSinFocos');
            if (avSin) { avSin.style.display = (d.focos_en_boyaca === 0) ? '' : 'none'; }
            f.textContent = resumen + ' Histórico: ' + fuentes + '.' +
                ((d.avisos && d.avisos.length) ? ' ' + d.avisos.join(' ') : '');

            // El mapa de calor va al final: si el lienzo aún no está listo no debe
            // impedir que se vean los focos, los reportes ni los bomberos.
            pintarCalor();
        }).catch(function (err) {
            var f = document.getElementById('fenFuentes');
            if (f) {
                f.textContent = 'No fue posible cargar los datos del mapa. Intente recargar la página.';
            }
            if (window.console) { console.error('Fenómenos:', err); }
        });
    }

    function llenarAnios() {
        var sel = document.getElementById('fenFiltroAnio');
        var anios = Object.keys((estado.historico.totales || {}).anios || {}).sort();
        anios.forEach(function (a) {
            var o = document.createElement('option'); o.value = a; o.textContent = a; sel.appendChild(o);
        });
        sel.addEventListener('change', pintarCalor);
        document.getElementById('fenFiltroFen').addEventListener('change', pintarCalor);
    }

    function pintarCalor() {
        if (!estado.mapa) { return; }
        var fen = document.getElementById('fenFiltroFen').value;
        var anio = document.getElementById('fenFiltroAnio').value;
        var puntos = [], max = 1;
        var municipios = estado.historico.municipios || {};
        Object.keys(municipios).forEach(function (dane) {
            var m = municipios[dane], peso;
            if (anio !== 'todos') {
                peso = (m.anios && m.anios[anio]) ? m.anios[anio] : 0;
                if (fen !== 'todos' && peso > 0) {
                    // Sin desglose año×fenómeno: se pondera por la participación del fenómeno.
                    var tot = m.total || 1;
                    peso = peso * ((m.fenomenos && m.fenomenos[fen]) ? m.fenomenos[fen] / tot : 0);
                }
            } else {
                peso = (fen === 'todos') ? (m.total || 0) : ((m.fenomenos && m.fenomenos[fen]) || 0);
            }
            if (peso > 0) { puntos.push([m.lat, m.lon, peso]); if (peso > max) { max = peso; } }
        });
        var datos = puntos.map(function (p) { return [p[0], p[1], Math.min(1, p[2] / max)]; });
        if (estado.capaCalor) { estado.mapa.removeLayer(estado.capaCalor); estado.capaCalor = null; }
        var cont = document.getElementById('fenMapa');
        if (!cont || cont.clientWidth === 0) {
            // Lienzo sin tamaño (subpestaña oculta): se reintenta al mostrarse.
            setTimeout(pintarCalor, 200);
            return;
        }
        try {
            estado.capaCalor = L.heatLayer(datos, {
                radius: 28, blur: 22, maxZoom: 11,
                gradient: { 0.2: '#22c55e', 0.5: '#eab308', 0.8: '#f97316', 1: '#ef4444' }
            }).addTo(estado.mapa);
        } catch (e) {
            if (window.console) { console.warn('Capa de calor no disponible:', e); }
        }
    }

    /* ---- reporte ciudadano ---- */
    var wrap = document.getElementById('fenFormWrap');
    document.getElementById('fenBtnReportar').addEventListener('click', function () {
        wrap.hidden = !wrap.hidden;
        if (!wrap.hidden) { wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
    });

    function municipioMasCercano(lat, lon) {
        var municipios = (estado.historico || {}).municipios || {}, mejor = null, dm = 1e9;
        Object.keys(municipios).forEach(function (dane) {
            var m = municipios[dane];
            var d = Math.pow(m.lat - lat, 2) + Math.pow(m.lon - lon, 2);
            if (d < dm) { dm = d; mejor = { dane: dane, nombre: m.nombre }; }
        });
        return mejor;
    }

    function fijarPunto(lat, lon) {
        if (!estado.mapa) { return; }
        document.getElementById('fenLat').value = lat.toFixed(6);
        document.getElementById('fenLon').value = lon.toFixed(6);
        var mun = municipioMasCercano(lat, lon);
        if (mun) {
            document.getElementById('fenMunicipio').value = mun.nombre;
            document.getElementById('fenDane').value = mun.dane;
        }
        if (estado.marcadorReporte) { estado.mapa.removeLayer(estado.marcadorReporte); }
        estado.marcadorReporte = L.marker([lat, lon]).addTo(estado.mapa)
            .bindPopup('Punto del reporte').openPopup();
        if (wrap.hidden) { wrap.hidden = false; }
    }

    document.getElementById('fenBtnUbicacion').addEventListener('click', function () {
        var msg = document.getElementById('fenFormMsg');
        if (!navigator.geolocation) { msg.textContent = 'Su navegador no permite compartir la ubicación.'; return; }
        msg.textContent = 'Obteniendo su ubicación…';
        navigator.geolocation.getCurrentPosition(function (pos) {
            msg.textContent = '';
            iniciarMapa();
            var espera = setInterval(function () {
                if (estado.mapa) {
                    clearInterval(espera);
                    estado.mapa.setView([pos.coords.latitude, pos.coords.longitude], 13);
                    fijarPunto(pos.coords.latitude, pos.coords.longitude);
                }
            }, 200);
        }, function () { msg.textContent = 'No fue posible obtener su ubicación; marque el punto en el mapa.'; });
    });

    document.getElementById('fenForm').addEventListener('submit', function (ev) {
        ev.preventDefault();
        var msg = document.getElementById('fenFormMsg');
        var lat = document.getElementById('fenLat').value;
        if (!lat) { msg.textContent = 'Marque primero el punto en el mapa.'; return; }
        var datos = {
            tipo: document.getElementById('fenTipo').value,
            municipio: document.getElementById('fenMunicipio').value,
            municipio_dane: document.getElementById('fenDane').value,
            lat: parseFloat(lat), lon: parseFloat(document.getElementById('fenLon').value),
            descripcion: document.getElementById('fenDescripcion').value,
            referencia: document.getElementById('fenReferencia').value,
            contacto: document.getElementById('fenContacto').value
        };
        msg.textContent = 'Enviando…';
        fetch(API, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(datos) })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                msg.textContent = d.mensaje || '';
                if (d.ok) {
                    document.getElementById('fenForm').reset();
                    if (estado.marcadorReporte) {
                        estado.marcadorReporte.bindPopup('Reporte enviado. Gracias.').openPopup();
                    }
                }
            })
            .catch(function () { msg.textContent = 'No fue posible enviar el reporte.'; });
    });

    /* ---- directorio de bomberos ---- */
    var bomberosCargados = false;
    function cargarBomberos() {
        if (bomberosCargados) { return; }
        bomberosCargados = true;
        fetch(API + '?recurso=bomberos').then(function (r) { return r.json(); }).then(function (d) {
            estado.bomberos = d.bomberos || [];
            estado.cobertura = d.cobertura || [];
            // Índice de estaciones por nombre del cuerpo, para mostrar sede y dirección.
            estado.porCuerpo = {};
            estado.bomberos.forEach(function (b) {
                estado.porCuerpo[clave(b.nombre.replace(/^Cuerpo de Bomberos de\s*/i, ''))] = b;
            });
            llenarProvincias();
            pintarBomberos();
        }).catch(function () {
            document.getElementById('fenBomberosLista').innerHTML =
                '<p class="text-muted small mb-0">No fue posible cargar el directorio. ' +
                'Use las líneas <a href="tel:123">123</a> y <a href="tel:119">119</a>.</p>';
        });
    }

    function clave(t) {
        return (t || '').toString().toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]/g, '');
    }

    function llenarProvincias() {
        var sel = document.getElementById('fenFiltroProv');
        var provs = {};
        estado.cobertura.forEach(function (m) { if (m.provincia) { provs[m.provincia] = 1; } });
        Object.keys(provs).sort().forEach(function (p) {
            var o = document.createElement('option'); o.value = p; o.textContent = p; sel.appendChild(o);
        });
    }

    function telefonosHtml(tel) {
        if (!tel) { return ''; }
        return '<span class="fen-op__tel">' + tel.split('/').map(function (t) {
            var n = t.trim(); if (!n) { return ''; }
            return '<a href="tel:' + n.replace(/\s/g, '') + '"><i class="fa-solid fa-phone me-1"></i>' + n + '</a>';
        }).join('') + '</span>';
    }

    function pintarBomberos() {
        var cont = document.getElementById('fenBomberosLista');
        var resumen = document.getElementById('fenDirResumen');
        var q = clave(document.getElementById('fenBuscaBomberos').value);
        var prov = document.getElementById('fenFiltroProv').value;
        var propia = document.getElementById('fenFiltroPropia').value;

        var lista = estado.cobertura.filter(function (m) {
            if (prov && m.provincia !== prov) { return false; }
            if (propia === 'si' && !m.propia) { return false; }
            if (propia === 'no' && m.propia) { return false; }
            if (!q) { return true; }
            if (clave(m.municipio).indexOf(q) >= 0) { return true; }
            // también encuentra por el nombre del cuerpo que lo atiende
            return (m.opciones || []).some(function (o) { return clave(o.cuerpo).indexOf(q) >= 0; });
        });

        resumen.textContent = lista.length === 0
            ? 'Sin resultados. Pruebe con otro nombre o quite los filtros.'
            : lista.length + (lista.length === 1 ? ' municipio' : ' municipios') +
              ' · ' + lista.filter(function (m) { return m.propia; }).length + ' con estación propia';

        if (!lista.length) {
            cont.innerHTML = '<p class="text-muted small mb-0">' +
                (estado.cobertura.length ? 'No hay resultados para esa búsqueda.' :
                 'El directorio aún no ha sido cargado. Use las líneas <a href="tel:123">123</a> y <a href="tel:119">119</a>.') +
                '</p>';
            return;
        }

        cont.innerHTML = lista.map(function (m) {
            var ops = (m.opciones || []).map(function (o) {
                var est = estado.porCuerpo[clave(o.cuerpo)];
                var sede = [];
                // «en X» solo aporta cuando el cuerpo no se llama igual que su municipio sede.
                if (est && est.municipio && clave(est.municipio) !== clave(m.municipio)
                    && clave(est.municipio) !== clave(o.cuerpo)) {
                    sede.push('en ' + est.municipio);
                }
                if (est && est.direccion) { sede.push(est.direccion); }
                return '<div class="fen-op">' +
                    '<span class="fen-op__n">' + o.orden + '</span>' +
                    '<div><span class="fen-op__cuerpo">' + o.cuerpo + '</span>' +
                    (sede.length ? '<div class="fen-op__sede">' + sede.join(' · ') + '</div>' : '') +
                    telefonosHtml(o.telefono) + '</div></div>';
            }).join('');
            return '<article class="fen-dir-card">' +
                '<h6>' + m.municipio +
                '<span class="fen-badge ' + (m.propia ? 'fen-badge--propia">Estación propia' : 'fen-badge--apoyo">Acude a otro municipio') +
                '</span></h6>' +
                '<div class="fen-dir-prov">Provincia de ' + (m.provincia || '—') + '</div>' +
                ops + '</article>';
        }).join('');
    }

    ['fenBuscaBomberos', 'fenFiltroProv', 'fenFiltroPropia'].forEach(function (id) {
        var el = document.getElementById(id);
        el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', pintarBomberos);
    });
    document.getElementById('fenLimpiaDir').addEventListener('click', function () {
        document.getElementById('fenBuscaBomberos').value = '';
        document.getElementById('fenFiltroProv').value = '';
        document.getElementById('fenFiltroPropia').value = '';
        pintarBomberos();
    });
})();
</script>
