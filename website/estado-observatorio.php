<?php
require_once 'tracker.php';
$pageTitle = 'Estado de datos · Red de Observatorios de Boyacá';
$pageDescription = 'Catálogo de indicadores disponibles en la Red de Observatorios de Boyacá, filtrable por dimensión y búsqueda.';
$themeColor = '#0b2744';
$themeAccent = '#23a9b8';
include 'include/site-header.php';
?>
<style>
    .estado{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;color:#0f172a}
    .estado-hero{background:radial-gradient(900px 440px at 12% -20%,rgba(35,169,184,.16),transparent),linear-gradient(180deg,#0b2744,#0f3557);color:#fff;padding:2.75rem 0 2.25rem}
    .estado-hero h1{font-weight:800;letter-spacing:-.02em;font-size:clamp(1.6rem,3.2vw,2.2rem);margin-bottom:.5rem}
    .estado-hero p{color:#cfe0ee;max-width:720px;margin:0}
    .estado-wrap{padding:1.75rem 0 3rem}
    .stat-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:.85rem;margin-bottom:1.5rem}
    .stat-card{background:#fff;border:1px solid #e7ecf3;border-radius:14px;padding:.9rem 1rem;box-shadow:0 6px 18px rgba(2,6,23,.05);border-top:4px solid var(--c,#23a9b8)}
    .stat-card .n{font-size:1.6rem;font-weight:800;color:#0b2744;line-height:1}
    .stat-card .l{font-size:.78rem;color:#5d6b80;font-weight:600;margin-top:.25rem}
    @media (max-width:992px){.stat-grid{grid-template-columns:repeat(3,1fr)}}
    @media (max-width:560px){.stat-grid{grid-template-columns:repeat(2,1fr)}}
    .estado-controls{background:#fff;border:1px solid #e7ecf3;border-radius:16px;padding:1.1rem;box-shadow:0 8px 22px rgba(2,6,23,.05);margin-bottom:1.5rem}
    .dim-filter{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.9rem}
    .dim-chip{display:inline-flex;align-items:center;gap:.45rem;border:1px solid #e2e8f0;background:#f8fafc;color:#0f172a;border-radius:999px;padding:.45rem .85rem;font-size:.86rem;font-weight:600;cursor:pointer;transition:all .15s ease;user-select:none}
    .dim-chip:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(2,6,23,.08)}
    .dim-chip .dot{width:.65rem;height:.65rem;border-radius:999px;background:var(--c,#94a3b8)}
    .dim-chip.active{color:#fff;border-color:transparent;background:var(--c,#0b2744)}
    .dim-chip .badge-n{background:rgba(0,0,0,.08);border-radius:999px;padding:0 .45rem;font-size:.75rem;font-weight:700}
    .dim-chip.active .badge-n{background:rgba(255,255,255,.25)}
    .ind-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem}
    .ind-card{display:block;background:#fff;border:1px solid #e7ecf3;border-radius:14px;padding:1rem 1.1rem;text-decoration:none;color:inherit;transition:all .2s ease;border-left:5px solid var(--c,#23a9b8);height:100%}
    .ind-card:hover{transform:translateY(-3px);box-shadow:0 14px 30px rgba(2,6,23,.10);color:inherit}
    .ind-card .ic-top{display:flex;align-items:center;gap:.5rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--c,#23a9b8);margin-bottom:.4rem}
    .ind-card h3{font-size:1rem;font-weight:700;color:#0b2744;margin:0 0 .35rem;line-height:1.3}
    .ind-card .ic-cat{font-size:.83rem;color:#5d6b80;line-height:1.4}
    .ind-card .ic-id{margin-left:auto;background:#f1f5f9;color:#475569;border-radius:6px;padding:.05rem .4rem;font-size:.7rem}
    .estado-empty{grid-column:1/-1;text-align:center;color:#64748b;border:1px dashed #e2e8f0;background:#fafafa;border-radius:14px;padding:2.5rem}
    .estado-count{color:#5d6b80;font-size:.9rem;margin-bottom:.75rem}
</style>

<div class="estado">
    <section class="estado-hero">
        <div class="container">
            <h1>Estado de los datos</h1>
            <p>Explore el catálogo de indicadores de la Red de Observatorios. Filtre por dimensión y busque por tema para encontrar rápidamente lo que necesita.</p>
        </div>
    </section>

    <div class="estado-wrap">
        <div class="container">
            <div class="stat-grid" id="statGrid">
                <div class="stat-card" style="--c:#0b2744"><div class="n" id="stTotal">—</div><div class="l">Total indicadores</div></div>
                <div class="stat-card" style="--c:#0f3557"><div class="n" id="stEco">—</div><div class="l">Económica</div></div>
                <div class="stat-card" style="--c:#5f2a8a"><div class="n" id="stSoc">—</div><div class="l">Social</div></div>
                <div class="stat-card" style="--c:#1f6b45"><div class="n" id="stAmb">—</div><div class="l">Ambiental</div></div>
                <div class="stat-card" style="--c:#1847b7"><div class="n" id="stCti">—</div><div class="l">CTI</div></div>
                <div class="stat-card" style="--c:#7d2d91"><div class="n" id="stGen">—</div><div class="l">Género</div></div>
            </div>

            <div class="estado-controls">
                <div class="dim-filter" id="dimFilter" role="group" aria-label="Filtrar por dimensión">
                    <span class="dim-chip active" data-dim="" style="--c:#0b2744"><span class="dot"></span> Todas <span class="badge-n" data-count="">0</span></span>
                    <span class="dim-chip" data-dim="económica" style="--c:#0f3557"><span class="dot"></span> Económica <span class="badge-n" data-count="económica">0</span></span>
                    <span class="dim-chip" data-dim="social" style="--c:#5f2a8a"><span class="dot"></span> Social <span class="badge-n" data-count="social">0</span></span>
                    <span class="dim-chip" data-dim="ambiental" style="--c:#1f6b45"><span class="dot"></span> Ambiental <span class="badge-n" data-count="ambiental">0</span></span>
                    <span class="dim-chip" data-dim="tecnología" style="--c:#1847b7"><span class="dot"></span> CTI <span class="badge-n" data-count="tecnología">0</span></span>
                    <span class="dim-chip" data-dim="género" style="--c:#7d2d91"><span class="dot"></span> Género <span class="badge-n" data-count="género">0</span></span>
                </div>
                <input id="searchInput" class="form-control" type="search" placeholder="Buscar por tema, indicador o categoría… (ej: pobreza, violencia, salud)" aria-label="Buscar indicador">
            </div>

            <p class="estado-count" id="resultCount"></p>
            <div class="ind-grid" id="indicatorsGrid">
                <div class="estado-empty">Cargando indicadores…</div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var DIM = {
        'económica':  {label:'Económica', color:'#0f3557', icon:'fa-chart-line'},
        'social':     {label:'Social',    color:'#5f2a8a', icon:'fa-people-group'},
        'ambiental':  {label:'Ambiental', color:'#1f6b45', icon:'fa-leaf'},
        'tecnología': {label:'CTI',       color:'#1847b7', icon:'fa-microchip'},
        'género':     {label:'Género',    color:'#7d2d91', icon:'fa-venus-mars'}
    };
    var all = [], activeDim = '', term = '';
    var grid = document.getElementById('indicatorsGrid');
    var search = document.getElementById('searchInput');
    var countEl = document.getElementById('resultCount');

    function esc(s){ return (s==null?'':String(s)).replace(/[&<>"]/g, function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];}); }

    function renderStats() {
        var by = {};
        all.forEach(function(i){ by[i.dimension] = (by[i.dimension]||0)+1; });
        document.getElementById('stTotal').textContent = all.length;
        document.getElementById('stEco').textContent = by['económica']||0;
        document.getElementById('stSoc').textContent = by['social']||0;
        document.getElementById('stAmb').textContent = by['ambiental']||0;
        document.getElementById('stCti').textContent = by['tecnología']||0;
        document.getElementById('stGen').textContent = by['género']||0;
        // contadores en los chips
        document.querySelectorAll('.badge-n').forEach(function(b){
            var d = b.getAttribute('data-count');
            b.textContent = d === '' ? all.length : (by[d]||0);
        });
    }

    function render() {
        var items = all.filter(function(i){
            if (activeDim && i.dimension !== activeDim) return false;
            if (term) {
                var h = (i.titulo+' '+i.categoria+' '+i.subcategoria+' '+i.dimension).toLowerCase();
                if (h.indexOf(term) === -1) return false;
            }
            return true;
        });
        countEl.textContent = items.length + ' indicador(es)' + (activeDim ? ' · ' + (DIM[activeDim]?DIM[activeDim].label:activeDim) : '') + (term ? ' · "' + term + '"' : '');
        if (!items.length) {
            grid.innerHTML = '<div class="estado-empty"><i class="fa-regular fa-folder-open" style="font-size:1.6rem;opacity:.4;display:block;margin-bottom:.5rem"></i>No se encontraron indicadores con ese filtro.</div>';
            return;
        }
        grid.innerHTML = items.map(function(i){
            var d = DIM[i.dimension] || {label:i.dimension, color:'#23a9b8', icon:'fa-database'};
            return '<a class="ind-card" style="--c:'+d.color+'" href="'+esc(i.url)+'">'
                + '<div class="ic-top"><i class="fa-solid '+d.icon+'"></i> '+esc(d.label)+'<span class="ic-id">#'+esc(i.id)+'</span></div>'
                + '<h3>'+esc(i.titulo)+'</h3>'
                + '<div class="ic-cat">'+esc(i.categoria||'Sin categoría')+(i.subcategoria?(' · '+esc(i.subcategoria)):'')+'</div>'
                + '</a>';
        }).join('');
    }

    document.getElementById('dimFilter').addEventListener('click', function(e){
        var chip = e.target.closest('.dim-chip');
        if (!chip) return;
        activeDim = chip.getAttribute('data-dim');
        document.querySelectorAll('.dim-chip').forEach(function(c){ c.classList.remove('active'); });
        chip.classList.add('active');
        render();
    });
    search.addEventListener('input', function(){ term = (search.value||'').toLowerCase().trim(); render(); });

    fetch('api/indicators.php').then(function(r){ return r.json(); }).then(function(json){
        all = json.items || [];
        renderStats();
        render();
    }).catch(function(err){
        grid.innerHTML = '<div class="estado-empty">No fue posible cargar los indicadores.</div>';
        console.error(err);
    });
})();
</script>

<?php require __DIR__ . '/include/site-footer.php'; ?>
