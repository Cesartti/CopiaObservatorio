<?php
/**
 * Pestaña "Fenómenos en Boyacá" del Observatorio Ambiental.
 *
 * Subpestañas: El Niño · La Niña · Mapa de novedades (con reporte ciudadano)
 * · Directorio de bomberos. Los datos vienen de api/fenomenos.php.
 *
 * Variables del scope (observatorio.php): $obs, $slug.
 */
require_once __DIR__ . '/../lib/fenomenos.php';

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
$fenFaseActual = (string) ($fenEnso['fase'] ?? 'neutral');
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
    #fenMapa{width:100%;height:540px;border-radius:14px;border:1px solid #e6ecf6;background:#eef2f7;z-index:0}
    .fen-map-tools{display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;margin-bottom:.6rem}
    .fen-map-tools select,.fen-map-tools input{font-size:.85rem;padding:.35rem .55rem;border:1px solid #dce4f2;border-radius:9px}
    .fen-leyenda{display:flex;flex-wrap:wrap;gap:.9rem;margin-top:.6rem;font-size:.8rem;color:#4b5768}
    .fen-leyenda i{width:12px;height:12px;border-radius:50%;display:inline-block;margin-right:.3rem;vertical-align:-1px}
    .fen-form{background:#f8fafc;border:1px solid #e6ecf6;border-radius:14px;padding:1rem}
    .fen-form label{font-size:.8rem;font-weight:600;color:#3b4759;margin-bottom:.2rem;display:block}
    .fen-form .form-control,.fen-form .form-select{font-size:.88rem}
    .fen-bomberos-item{display:flex;gap:.75rem;align-items:flex-start;padding:.7rem .2rem;border-bottom:1px solid #eef2f7}
    .fen-bomberos-item:last-child{border-bottom:none}
    .fen-bomberos-item .fb-ico{width:38px;height:38px;border-radius:10px;background:rgba(220,38,38,.1);color:#dc2626;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto}
    .fen-aviso{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:12px;padding:.7rem .9rem;font-size:.84rem}
    @media (max-width:575.98px){#fenMapa{height:420px}}
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

    <?php /* ------------------------------------------------- Directorio bomberos */ ?>
    <section class="fen-pane" id="fen-bomberos">
        <div class="fen-aviso mb-3">
            <strong>Ante una emergencia en curso llame primero.</strong>
            Línea única de emergencias <strong>123</strong> · Línea nacional de bomberos <strong>119</strong>.
            El directorio municipal lo administra la entidad competente y se muestra a continuación.
        </div>
        <div class="fen-map-tools">
            <label class="mb-0 small fw-semibold" for="fenBuscaBomberos">Buscar por municipio</label>
            <input type="search" id="fenBuscaBomberos" class="form-control form-control-sm" style="max-width:280px" placeholder="Escriba el nombre del municipio">
        </div>
        <div id="fenBomberosLista" class="mt-2"><p class="text-muted small">Cargando directorio…</p></div>
    </section>
</article>

<script>
(function () {
    var API = 'api/fenomenos.php';
    var estado = { mapa: null, capaCalor: null, capaFocos: null, capaReportes: null,
                   capaBomberos: null, marcadorReporte: null, historico: null, bomberos: [] };

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
            if (b.dataset.fen === 'bomberos') { cargarBomberos(); }
        });
    });

    function cargarScript(src, cb) {
        var s = document.createElement('script'); s.src = src; s.onload = cb; document.head.appendChild(s);
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
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 17, attribution: '&copy; OpenStreetMap'
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
                resumen += ' — ' + d.focos_en_boyaca + ' en Boyacá y ' + (d.focos_vecinos || 0) +
                    ' en el área circundante (en gris). Un foco es una detección térmica del satélite, ' +
                    'no un incendio confirmado.';
            }
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
            pintarBomberos('');
        });
    }
    function pintarBomberos(filtro) {
        var cont = document.getElementById('fenBomberosLista');
        var f = (filtro || '').toLowerCase().trim();
        var lista = estado.bomberos.filter(function (b) {
            return !f || (b.municipio || '').toLowerCase().indexOf(f) >= 0 || (b.nombre || '').toLowerCase().indexOf(f) >= 0;
        });
        if (!lista.length) {
            cont.innerHTML = '<p class="text-muted small mb-0">' +
                (estado.bomberos.length ? 'No hay resultados para esa búsqueda.' :
                'El directorio municipal aún no ha sido cargado. Use las líneas 123 y 119.') + '</p>';
            return;
        }
        cont.innerHTML = lista.map(function (b) {
            var contacto = [b.telefono, b.celular].filter(Boolean).join(' · ');
            return '<div class="fen-bomberos-item">' +
                '<span class="fb-ico"><i class="fa-solid fa-fire-extinguisher"></i></span>' +
                '<div><strong>' + (b.nombre || '') + '</strong>' +
                '<div class="small text-muted">' + (b.municipio || '') +
                (b.provincia ? ' · ' + b.provincia : '') + (b.tipo ? ' · ' + b.tipo : '') + '</div>' +
                (contacto ? '<div class="small"><i class="fa-solid fa-phone me-1"></i>' + contacto + '</div>' : '') +
                (b.direccion ? '<div class="small text-muted">' + b.direccion + '</div>' : '') +
                '</div></div>';
        }).join('');
    }
    document.getElementById('fenBuscaBomberos').addEventListener('input', function (e) {
        pintarBomberos(e.target.value);
    });
})();
</script>
