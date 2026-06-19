<?php
/**
 * Pestaña "Publicaciones universidades" del micrositio.
 *
 * Requiere $slug (slug del observatorio actual). Lee el catálogo de
 * config/publicaciones_universidades.php, filtra por observatorio y renderiza
 * buscador + filtros por universidad, tipo y año (filtrado en el navegador,
 * sin recargar). Los registros 'demo' muestran un aviso de contenido de
 * ejemplo hasta que se carguen publicaciones reales.
 */
$puCatalog = require dirname(__DIR__) . '/config/publicaciones_universidades.php';
$puUniversities = $puCatalog['universities'] ?? [];

$puItems = [];
foreach ($puCatalog['items'] ?? [] as $it) {
    $obsList = $it['obs'] ?? [];
    if (in_array('*', $obsList, true) || in_array($slug, $obsList, true)) {
        $puItems[] = $it;
    }
}

// Más reciente primero
usort($puItems, static fn ($a, $b) => ($b['year'] ?? 0) <=> ($a['year'] ?? 0));

$puTypes = array_values(array_unique(array_map(static fn ($i) => $i['type'] ?? 'Otro', $puItems)));
sort($puTypes);
$puLines = array_values(array_unique(array_filter(array_map(static fn ($i) => trim((string) ($i['line'] ?? '')), $puItems))));
sort($puLines);
$puYears = array_values(array_unique(array_map(static fn ($i) => (int) ($i['year'] ?? 0), $puItems)));
rsort($puYears);
$puUsedUnis = array_values(array_unique(array_map(static fn ($i) => $i['university'] ?? '', $puItems)));
$puHasDemo = (bool) array_filter($puItems, static fn ($i) => !empty($i['demo']));

$puTypeIcons = [
    'Artículo' => 'fa-file-lines',
    'Informe' => 'fa-file-contract',
    'Tesis' => 'fa-graduation-cap',
    'Boletín' => 'fa-newspaper',
    'Dataset' => 'fa-database',
];
?>
<?php if ($puItems === []): ?>
    <article class="content-card">
        <h3>Publicaciones universidades</h3>
        <p class="text-muted mb-0">Aún no hay publicaciones académicas registradas para este observatorio.</p>
    </article>
<?php else: ?>
<style>
    .pu-filter-label{font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#64748b;margin-bottom:.35rem}
    .pu-chip{display:inline-flex;align-items:center;gap:.45rem;padding:.42rem .8rem;border-radius:999px;border:1px solid #e2e8f0;background:#fff;color:#1b2a40;font-size:.85rem;font-weight:600;cursor:pointer;transition:all .15s ease}
    .pu-chip:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(2,6,23,.08)}
    .pu-chip.active{background:var(--obs-color,#0d6efd);border-color:var(--obs-color,#0d6efd);color:#fff}
    .pu-chip .pu-uni-dot{width:.6rem;height:.6rem;border-radius:999px;display:inline-block}
    .pu-chip.active .pu-uni-dot{outline:2px solid rgba(255,255,255,.65)}
    .pu-card{display:flex;flex-direction:column;gap:.5rem;background:#fff;border:1px solid #e8edf5;border-radius:14px;padding:1.05rem 1.15rem;height:100%;transition:transform .2s ease,box-shadow .2s ease}
    .pu-card:hover{transform:translateY(-3px);box-shadow:0 14px 30px rgba(2,6,23,.12)}
    .pu-card-top{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap}
    .pu-type{display:inline-flex;align-items:center;gap:.35rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--obs-color,#0d6efd);background:rgba(var(--obs-color-rgb,13,110,253),.1);padding:.25rem .6rem;border-radius:999px}
    .pu-line{display:inline-flex;align-items:center;gap:.35rem;font-size:.72rem;font-weight:600;color:#475569;background:#f1f5f9;border:1px solid #e2e8f0;padding:.25rem .6rem;border-radius:999px}
    .pu-year{font-size:.78rem;font-weight:700;color:#64748b;margin-left:auto}
    .pu-title{font-size:.98rem;font-weight:700;line-height:1.4;color:#13233c;margin:0}
    .pu-authors{font-size:.82rem;color:#64748b}
    .pu-summary{font-size:.86rem;color:#475569;line-height:1.55;margin:0;flex:1}
    .pu-uni{display:inline-flex;align-items:center;gap:.45rem;font-size:.8rem;font-weight:600;color:#334155}
    .pu-uni .pu-uni-dot{width:.65rem;height:.65rem;border-radius:999px;display:inline-block}
    .pu-link{align-self:flex-start;font-size:.85rem;font-weight:700;color:var(--obs-color,#0d6efd);text-decoration:none}
    .pu-link:hover{text-decoration:underline}
    .pu-demo-note{border:1px dashed #cbd5e1;background:#f8fafc;border-radius:12px;color:#64748b;font-size:.85rem}
    .pu-empty{border:1px dashed #e2e8f0;background:#fafafa;border-radius:12px;color:#64748b}
</style>

<article class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
        <h3 class="mb-0">Publicaciones universidades</h3>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small" id="puCounter"></span>
            <a class="btn btn-sm btn-outline-secondary" href="publicaciones.php?dim=<?= htmlspecialchars($slug) ?>"><i class="fa-solid fa-arrow-up-right-from-square me-1" aria-hidden="true"></i> Ver todas</a>
        </div>
    </div>
    <p class="text-muted small mb-3">Producción académica de las universidades aliadas de la Red relacionada con este observatorio. Combine los filtros o use el buscador para encontrar una publicación.</p>

    <?php if ($puHasDemo): ?>
        <div class="pu-demo-note px-3 py-2 mb-3"><i class="fa-solid fa-circle-info me-2" aria-hidden="true"></i><strong>Propuesta de organización:</strong> los registros que se muestran son ejemplos para visualizar la sección; se reemplazarán por las publicaciones reales de las universidades.</div>
    <?php endif; ?>

    <div class="row g-3 mb-2">
        <div class="col-lg-5">
            <div class="pu-filter-label">Buscar</div>
            <input type="search" id="puSearch" class="form-control" placeholder="Título, autor o palabra clave…">
        </div>
        <div class="col-lg-4 col-sm-7">
            <div class="pu-filter-label">Tipo de publicación</div>
            <div class="d-flex flex-wrap gap-2" id="puTypeChips">
                <button type="button" class="pu-chip active" data-type="">Todos</button>
                <?php foreach ($puTypes as $t): ?>
                    <button type="button" class="pu-chip" data-type="<?= htmlspecialchars($t) ?>"><i class="fa-solid <?= $puTypeIcons[$t] ?? 'fa-file' ?>" aria-hidden="true"></i> <?= htmlspecialchars($t) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-3 col-sm-5">
            <div class="pu-filter-label">Año</div>
            <select id="puYear" class="form-select">
                <option value="">Todos los años</option>
                <?php foreach ($puYears as $y): ?>
                    <option value="<?= $y ?>"><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($puLines !== []): ?>
    <div class="mb-3">
        <div class="pu-filter-label">Línea temática del observatorio</div>
        <div class="d-flex flex-wrap gap-2" id="puLineChips">
            <button type="button" class="pu-chip active" data-line="">Todas las líneas</button>
            <?php foreach ($puLines as $l): ?>
                <button type="button" class="pu-chip" data-line="<?= htmlspecialchars($l) ?>"><i class="fa-solid fa-tag" aria-hidden="true"></i> <?= htmlspecialchars($l) ?></button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="mb-3">
        <div class="pu-filter-label">Universidad</div>
        <div class="d-flex flex-wrap gap-2" id="puUniChips">
            <button type="button" class="pu-chip active" data-uni="">Todas</button>
            <?php foreach ($puUsedUnis as $uKey): $u = $puUniversities[$uKey] ?? null; if (!$u) continue; ?>
                <button type="button" class="pu-chip" data-uni="<?= htmlspecialchars($uKey) ?>" title="<?= htmlspecialchars($u['full']) ?>">
                    <span class="pu-uni-dot" style="background:<?= htmlspecialchars($u['color']) ?>"></span><?= htmlspecialchars($u['name']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row g-3" id="puGrid">
        <?php foreach ($puItems as $it): $u = $puUniversities[$it['university'] ?? ''] ?? null; ?>
            <div class="col-md-6 pu-item"
                 data-type="<?= htmlspecialchars($it['type'] ?? '') ?>"
                 data-uni="<?= htmlspecialchars($it['university'] ?? '') ?>"
                 data-line="<?= htmlspecialchars($it['line'] ?? '') ?>"
                 data-year="<?= (int) ($it['year'] ?? 0) ?>"
                 data-text="<?= htmlspecialchars(mb_strtolower(($it['title'] ?? '') . ' ' . ($it['authors'] ?? '') . ' ' . ($it['summary'] ?? '') . ' ' . ($it['line'] ?? ''))) ?>">
                <div class="pu-card">
                    <div class="pu-card-top">
                        <span class="pu-type"><i class="fa-solid <?= $puTypeIcons[$it['type'] ?? ''] ?? 'fa-file' ?>" aria-hidden="true"></i> <?= htmlspecialchars($it['type'] ?? 'Documento') ?></span>
                        <?php if (!empty($it['line'])): ?>
                            <span class="pu-line"><i class="fa-solid fa-tag" aria-hidden="true"></i> <?= htmlspecialchars($it['line']) ?></span>
                        <?php endif; ?>
                        <span class="pu-year"><i class="fa-regular fa-calendar me-1" aria-hidden="true"></i><?= (int) ($it['year'] ?? 0) ?></span>
                    </div>
                    <h4 class="pu-title"><?= htmlspecialchars($it['title'] ?? '') ?></h4>
                    <?php if (!empty($it['authors'])): ?>
                        <div class="pu-authors"><i class="fa-solid fa-user-pen me-1" aria-hidden="true"></i><?= htmlspecialchars($it['authors']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($it['summary'])): ?>
                        <p class="pu-summary"><?= htmlspecialchars($it['summary']) ?></p>
                    <?php endif; ?>
                    <?php if ($u): ?>
                        <span class="pu-uni"><span class="pu-uni-dot" style="background:<?= htmlspecialchars($u['color']) ?>"></span><?= htmlspecialchars($u['full']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($it['url']) && $it['url'] !== '#'): ?>
                        <a class="pu-link" href="<?= htmlspecialchars($it['url']) ?>" target="_blank" rel="noopener">Ver publicación <i class="fa-solid fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i></a>
                    <?php else: ?>
                        <span class="pu-link text-muted" style="color:#94a3b8 !important">Enlace pendiente</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="pu-empty p-4 text-center d-none" id="puEmpty">
        <strong class="d-block mb-1">Sin resultados</strong>
        No hay publicaciones que coincidan con los filtros seleccionados. <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="puReset">Limpiar filtros</button>
    </div>
</article>

<script>
(function () {
    var state = { q: '', type: '', uni: '', line: '', year: '' };
    var items = Array.prototype.slice.call(document.querySelectorAll('#puGrid .pu-item'));
    var counter = document.getElementById('puCounter');
    var empty = document.getElementById('puEmpty');

    function apply() {
        var visible = 0;
        items.forEach(function (el) {
            var ok = (!state.type || el.dataset.type === state.type)
                && (!state.uni || el.dataset.uni === state.uni)
                && (!state.line || el.dataset.line === state.line)
                && (!state.year || el.dataset.year === state.year)
                && (!state.q || el.dataset.text.indexOf(state.q) !== -1);
            el.classList.toggle('d-none', !ok);
            if (ok) visible++;
        });
        counter.textContent = 'Mostrando ' + visible + ' de ' + items.length + ' publicaciones';
        empty.classList.toggle('d-none', visible !== 0);
    }

    function bindChips(containerId, key) {
        var box = document.getElementById(containerId);
        if (!box) return;
        box.addEventListener('click', function (e) {
            var chip = e.target.closest('.pu-chip');
            if (!chip) return;
            box.querySelectorAll('.pu-chip').forEach(function (c) { c.classList.remove('active'); });
            chip.classList.add('active');
            state[key] = chip.dataset[key] || '';
            apply();
        });
    }

    bindChips('puTypeChips', 'type');
    bindChips('puUniChips', 'uni');
    bindChips('puLineChips', 'line');

    document.getElementById('puSearch').addEventListener('input', function () {
        state.q = this.value.trim().toLowerCase();
        apply();
    });
    document.getElementById('puYear').addEventListener('change', function () {
        state.year = this.value;
        apply();
    });
    document.getElementById('puReset').addEventListener('click', function () {
        state = { q: '', type: '', uni: '', line: '', year: '' };
        document.getElementById('puSearch').value = '';
        document.getElementById('puYear').value = '';
        ['puTypeChips', 'puUniChips', 'puLineChips'].forEach(function (id) {
            var box = document.getElementById(id);
            if (!box) return;
            box.querySelectorAll('.pu-chip').forEach(function (c, i) { c.classList.toggle('active', i === 0); });
        });
        apply();
    });

    apply();
})();
</script>
<?php endif; ?>
