<?php
require_once 'tracker.php';
$observatories = require __DIR__ . '/config/observatories.php';
$puCatalog = require __DIR__ . '/config/publicaciones_universidades.php';
$puUniversities = $puCatalog['universities'] ?? [];
$puItems = $puCatalog['items'] ?? [];

// Dimensiones (mismas que los observatorios) con color para los filtros.
$dimMeta = [
    'economico' => ['label' => 'Económico', 'color' => '#0f3557'],
    'social'    => ['label' => 'Social',    'color' => '#5f2a8a'],
    'ambiente'  => ['label' => 'Ambiental', 'color' => '#1f6b45'],
    'cti'       => ['label' => 'CTI',       'color' => '#1847b7'],
    'genero'    => ['label' => 'Género',    'color' => '#7d2d91'],
];

// Normaliza cada publicación a la forma que consume el front (JS).
$pubs = [];
foreach ($puItems as $it) {
    $uniKey = $it['university'] ?? '';
    $uni = $puUniversities[$uniKey] ?? ['name' => $uniKey, 'full' => $uniKey, 'color' => '#64748b'];
    $obs = $it['obs'] ?? [];
    // '*' = todos los observatorios
    $dims = in_array('*', $obs, true) ? array_keys($dimMeta) : array_values(array_intersect($obs, array_keys($dimMeta)));
    $pubs[] = [
        'title' => $it['title'] ?? 'Publicación',
        'authors' => $it['authors'] ?? '',
        'university' => $uni['name'],
        'universityColor' => $uni['color'],
        'type' => $it['type'] ?? 'Otro',
        'year' => (int) ($it['year'] ?? 0),
        'summary' => $it['summary'] ?? '',
        'url' => $it['url'] ?? '#',
        'line' => $it['line'] ?? '',
        'dims' => $dims,
        'demo' => !empty($it['demo']),
    ];
}
// Más reciente primero
usort($pubs, static fn ($a, $b) => $b['year'] <=> $a['year']);
$hasDemo = (bool) array_filter($pubs, static fn ($p) => $p['demo']);

// Dimensión preseleccionada por query (?dim=economico) para enlazar desde el observatorio.
$preDim = isset($_GET['dim']) && isset($dimMeta[$_GET['dim']]) ? $_GET['dim'] : '';

$pageTitle = 'Publicaciones · Red de Observatorios de Boyacá';
$pageDescription = 'Publicaciones y productos académicos de las universidades aliadas, por dimensión, universidad y tipo.';
$themeColor = '#0b2744';
$themeAccent = '#23a9b8';
include 'include/site-header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .pub{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;color:#0f172a}
    .pub-hero{background:radial-gradient(900px 440px at 12% -20%,rgba(35,169,184,.16),transparent),linear-gradient(180deg,#0b2744,#0f3557);color:#fff;padding:2.75rem 0 2.25rem}
    .pub-hero h1{font-weight:800;letter-spacing:-.02em;font-size:clamp(1.6rem,3.2vw,2.2rem);margin-bottom:.5rem}
    .pub-hero p{color:#cfe0ee;max-width:720px;margin:0}
    .pub-wrap{padding:1.75rem 0 3rem}
    .pub-controls{background:#fff;border:1px solid #e7ecf3;border-radius:16px;padding:1.1rem;box-shadow:0 8px 22px rgba(2,6,23,.05);margin-bottom:1.5rem}
    .dim-filter{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.9rem}
    .dim-chip{display:inline-flex;align-items:center;gap:.45rem;border:1px solid #e2e8f0;background:#f8fafc;color:#0f172a;border-radius:999px;padding:.45rem .85rem;font-size:.86rem;font-weight:600;cursor:pointer;transition:all .15s ease;user-select:none}
    .dim-chip:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(2,6,23,.08)}
    .dim-chip .dot{width:.65rem;height:.65rem;border-radius:999px;background:var(--c,#94a3b8)}
    .dim-chip.active{color:#fff;border-color:transparent;background:var(--c,#0b2744)}
    .dim-chip .badge-n{background:rgba(0,0,0,.08);border-radius:999px;padding:0 .45rem;font-size:.75rem;font-weight:700}
    .dim-chip.active .badge-n{background:rgba(255,255,255,.25)}
    .pub-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem}
    .pub-card{display:flex;flex-direction:column;gap:.55rem;background:#fff;border:1px solid #e7ecf3;border-radius:16px;padding:1.1rem 1.2rem;height:100%;border-top:4px solid var(--uc,#23a9b8);transition:transform .2s ease,box-shadow .2s ease}
    .pub-card:hover{transform:translateY(-3px);box-shadow:0 14px 30px rgba(2,6,23,.10)}
    .pub-card-top{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap}
    .pub-type{display:inline-flex;align-items:center;gap:.35rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#0b2744;background:#eef2f7;padding:.22rem .55rem;border-radius:999px}
    .pub-uni{display:inline-flex;align-items:center;gap:.4rem;font-size:.75rem;font-weight:700;color:#fff;padding:.22rem .6rem;border-radius:999px}
    .pub-year{margin-left:auto;font-size:.8rem;font-weight:700;color:#64748b}
    .pub-card h3{font-size:1.02rem;font-weight:700;color:#0b2744;margin:0;line-height:1.35}
    .pub-authors{font-size:.82rem;color:#475569;font-weight:600}
    .pub-summary{font-size:.86rem;color:#5d6b80;line-height:1.5;margin:0;flex:1}
    .pub-foot{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-top:.35rem}
    .pub-line{font-size:.72rem;color:#475569;background:#f1f5f9;border:1px solid #e2e8f0;padding:.2rem .55rem;border-radius:999px;font-weight:600}
    .pub-demo{font-size:.68rem;font-weight:700;color:#9a3412;background:#fff7ed;border:1px solid #fdba74;padding:.15rem .5rem;border-radius:999px}
    .pub-dimtags{display:flex;gap:.3rem;flex-wrap:wrap}
    .pub-dimtag{width:.7rem;height:.7rem;border-radius:999px;display:inline-block}
    .pub-empty{grid-column:1/-1;text-align:center;color:#64748b;border:1px dashed #e2e8f0;background:#fafafa;border-radius:14px;padding:2.5rem}
    .pub-count{color:#5d6b80;font-size:.9rem;margin-bottom:.75rem}
    .demo-banner{background:#fff7ed;border:1px solid #fdba74;color:#9a3412;border-radius:12px;padding:.7rem 1rem;font-size:.85rem;margin-bottom:1rem}
</style>

<div class="pub">
    <section class="pub-hero">
        <div class="container">
            <h1>Publicaciones y productos</h1>
            <p>Artículos, informes, tesis y boletines de las universidades aliadas de la Red de Observatorios. Filtre por dimensión, universidad o tipo para encontrar lo que busca.</p>
        </div>
    </section>

    <div class="pub-wrap">
        <div class="container">
            <?php if ($hasDemo): ?>
                <div class="demo-banner"><i class="bi bi-info-circle me-1"></i> Algunas publicaciones son <strong>contenido de ejemplo</strong> mientras se cargan los registros reales de las universidades.</div>
            <?php endif; ?>

            <div class="pub-controls">
                <div class="dim-filter" id="dimFilter" role="group" aria-label="Filtrar por dimensión">
                    <span class="dim-chip" data-dim="" style="--c:#0b2744"><span class="dot"></span> Todas <span class="badge-n" data-count="">0</span></span>
                    <?php foreach ($dimMeta as $slug => $m): ?>
                        <span class="dim-chip" data-dim="<?= htmlspecialchars($slug) ?>" style="--c:<?= htmlspecialchars($m['color']) ?>"><span class="dot"></span> <?= htmlspecialchars($m['label']) ?> <span class="badge-n" data-count="<?= htmlspecialchars($slug) ?>">0</span></span>
                    <?php endforeach; ?>
                </div>
                <div class="row g-2">
                    <div class="col-md-5"><input id="pubSearch" class="form-control" type="search" placeholder="Buscar por título, autor o tema…" aria-label="Buscar publicación"></div>
                    <div class="col-md-4">
                        <select id="pubUni" class="form-select" aria-label="Universidad">
                            <option value="">Todas las universidades</option>
                            <?php foreach ($puUniversities as $u): ?>
                                <option value="<?= htmlspecialchars($u['name']) ?>"><?= htmlspecialchars($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="pubType" class="form-select" aria-label="Tipo">
                            <option value="">Todos los tipos</option>
                            <?php foreach (array_values(array_unique(array_map(static fn ($p) => $p['type'], $pubs))) as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <p class="pub-count" id="pubCount"></p>
            <div class="pub-grid" id="pubGrid"></div>
        </div>
    </div>
</div>

<script>
(function () {
    var PUBS = <?= json_encode($pubs, JSON_UNESCAPED_UNICODE) ?>;
    var DIM = <?= json_encode($dimMeta, JSON_UNESCAPED_UNICODE) ?>;
    var activeDim = <?= json_encode($preDim) ?>, term = '', uni = '', type = '';
    var grid = document.getElementById('pubGrid');
    var countEl = document.getElementById('pubCount');

    function esc(s){ return (s==null?'':String(s)).replace(/[&<>"]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];}); }

    function counts(){
        var by={}; PUBS.forEach(function(p){ p.dims.forEach(function(d){ by[d]=(by[d]||0)+1; }); });
        document.querySelectorAll('.badge-n').forEach(function(b){ var d=b.getAttribute('data-count'); b.textContent = d==='' ? PUBS.length : (by[d]||0); });
    }

    function render(){
        var items = PUBS.filter(function(p){
            if (activeDim && p.dims.indexOf(activeDim) === -1) return false;
            if (uni && p.university !== uni) return false;
            if (type && p.type !== type) return false;
            if (term){ var h=(p.title+' '+p.authors+' '+p.summary+' '+p.line).toLowerCase(); if(h.indexOf(term)===-1) return false; }
            return true;
        });
        countEl.textContent = items.length + ' publicación(es)' + (activeDim ? ' · ' + (DIM[activeDim]?DIM[activeDim].label:activeDim) : '');
        if(!items.length){ grid.innerHTML='<div class="pub-empty"><i class="bi bi-folder2-open" style="font-size:1.6rem;opacity:.4;display:block;margin-bottom:.5rem"></i>No hay publicaciones con esos filtros.</div>'; return; }
        grid.innerHTML = items.map(function(p){
            var dots = p.dims.map(function(d){ var m=DIM[d]; return m?'<span class="pub-dimtag" title="'+esc(m.label)+'" style="background:'+m.color+'"></span>':''; }).join('');
            var link = (p.url && p.url!=='#') ? '<a href="'+esc(p.url)+'" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary mt-1"><i class="bi bi-box-arrow-up-right"></i> Abrir</a>' : '';
            return '<article class="pub-card" style="--uc:'+esc(p.universityColor)+'">'
                + '<div class="pub-card-top"><span class="pub-type"><i class="bi bi-journal-text"></i> '+esc(p.type)+'</span>'
                + '<span class="pub-uni" style="background:'+esc(p.universityColor)+'">'+esc(p.university)+'</span>'
                + (p.demo?'<span class="pub-demo">ejemplo</span>':'')
                + '<span class="pub-year">'+(p.year||'')+'</span></div>'
                + '<h3>'+esc(p.title)+'</h3>'
                + (p.authors?'<div class="pub-authors">'+esc(p.authors)+'</div>':'')
                + '<p class="pub-summary">'+esc(p.summary)+'</p>'
                + '<div class="pub-foot">'+(p.line?'<span class="pub-line">'+esc(p.line)+'</span>':'')+'<span class="pub-dimtags">'+dots+'</span>'+link+'</div>'
                + '</article>';
        }).join('');
    }

    function syncChips(){ document.querySelectorAll('.dim-chip').forEach(function(c){ c.classList.toggle('active', c.getAttribute('data-dim')===activeDim); }); }

    document.getElementById('dimFilter').addEventListener('click', function(e){
        var chip=e.target.closest('.dim-chip'); if(!chip) return;
        activeDim=chip.getAttribute('data-dim'); syncChips(); render();
    });
    document.getElementById('pubSearch').addEventListener('input', function(e){ term=(e.target.value||'').toLowerCase().trim(); render(); });
    document.getElementById('pubUni').addEventListener('change', function(e){ uni=e.target.value; render(); });
    document.getElementById('pubType').addEventListener('change', function(e){ type=e.target.value; render(); });

    counts(); syncChips(); render();
})();
</script>

<?php require __DIR__ . '/include/site-footer.php'; ?>
