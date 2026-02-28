<?php
require_once 'tracker.php';
include 'include/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
  :root{
    --ink:#0f172a;
    --muted:#475569;
    --bg:#ffffff;
    --card:#ffffff;
    --line:#e5e7eb;

    --pri:#5b21b6;
    --pri-600:#7c3aed;

    --social:#0ea5e9;
    --econ:#22c55e;
    --amb:#14b8a6;
    --ctei:#a855f7;
    --gen:#f43f5e;

    --shadow: 0 14px 40px rgba(2,6,23,.10);
  }

  body{
    font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    background:var(--bg);
    color:var(--ink);
  }

  /* HERO */
  .hero{
    background:
      radial-gradient(900px 450px at 10% -20%, rgba(124,58,237,.14), transparent),
      radial-gradient(900px 450px at 95% 10%, rgba(14,165,233,.12), transparent),
      linear-gradient(180deg,#fff 0%, #f8fafc 100%);
    border-bottom:1px solid var(--line);
  }
  .hero h1{ letter-spacing:-.02em; }
  .hero .subtitle{ color:#334155; }
  .hero .quick-pill{
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.45rem .75rem; border-radius:999px;
    background:#fff; border:1px solid #e2e8f0;
    box-shadow: 0 8px 20px rgba(2,6,23,.06);
    color:#0f172a; font-size:.9rem;
  }
  .kbd{
    background:#f8fafc; border:1px solid #e2e8f0; border-bottom-width:3px;
    padding:.15rem .45rem; border-radius:.4rem; color:#0f172a
  }

  /* FILTERS */
  .filter-wrap{
    background:#fff;
    border:1px solid var(--line);
    border-radius:1rem;
    box-shadow: 0 10px 26px rgba(2,6,23,.06);
  }
  .form-control, .form-select{
    background:#ffffff; border-color:#cbd5e1; color:var(--ink)
  }
  .form-control::placeholder{ color:#94a3b8; }

  .chip{
    border:1px solid #e2e8f0;
    background:#f8fafc;
    color:#0f172a;
    padding:.5rem .8rem;
    border-radius:999px;
    display:inline-flex; align-items:center; gap:.45rem;
    font-size:.88rem;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    user-select:none;
    cursor:pointer;
  }
  .chip:hover{ transform: translateY(-1px); box-shadow: 0 10px 20px rgba(2,6,23,.08); }
  .chip input{ display:none; }
  .chip.active{
    background:#f3e8ff;
    border-color:#e9d5ff;
    color:#5b21b6;
  }
  .chip.soft{
    background:#fff;
    border-style:dashed;
  }

  /* Dimension tabs */
  .dim-tabs{
    display:flex; flex-wrap:wrap; gap:.5rem;
  }
  .dim-tab{
    border:1px solid #e2e8f0;
    background:#fff;
    padding:.55rem .85rem;
    border-radius:999px;
    display:inline-flex; align-items:center; gap:.5rem;
    color:#0f172a;
    text-decoration:none;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .dim-tab:hover{ transform: translateY(-1px); box-shadow: 0 10px 20px rgba(2,6,23,.08); }
  .dim-dot{ width:.7rem; height:.7rem; border-radius:999px; }
  .dim-tab.active{ border-color:#c4b5fd; background:#faf5ff; }

  /* Cards */
  .card-pub{
    background:var(--card);
    border:1px solid var(--line);
    border-radius:1rem;
    overflow:hidden;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    height:100%;
  }
  .card-pub:hover{
    border-color:#c4b5fd;
    box-shadow: var(--shadow);
    transform: translateY(-2px);
  }
  .cover{
    position:relative;
    height:140px;
    background: linear-gradient(135deg, rgba(91,33,182,.18), rgba(14,165,233,.16));
  }
  .cover img{
    width:100%; height:100%;
    object-fit:cover;
    filter:saturate(1.05);
  }
  .cover::after{
    content:"";
    position:absolute; inset:0;
    background: linear-gradient(180deg, rgba(2,6,23,0) 30%, rgba(2,6,23,.65) 100%);
  }
  .cover-badges{
    position:absolute; left:14px; bottom:12px;
    z-index:2;
    display:flex; gap:.5rem; flex-wrap:wrap;
  }
  .badge-soft{
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.35rem .6rem;
    border-radius:999px;
    font-weight:600;
    font-size:.78rem;
    color:#0f172a;
    background: rgba(255,255,255,.88);
    border:1px solid rgba(226,232,240,.9);
    backdrop-filter: blur(6px);
  }

  .type-pill{
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.35rem .6rem; border-radius:999px;
    font-weight:700; font-size:.78rem;
    border:1px solid #e2e8f0;
    background:#fff;
  }

  .meta{
    color:#64748b;
    font-size:.9rem;
  }

  .tags .tag{
    display:inline-flex; align-items:center;
    padding:.28rem .55rem;
    border-radius:999px;
    border:1px solid #e2e8f0;
    background:#f8fafc;
    color:#334155;
    font-size:.78rem;
    margin-right:.35rem;
    margin-bottom:.35rem;
  }

  .btn-primary{ background:var(--pri); border-color:var(--pri) }
  .btn-primary:hover{ background:var(--pri-600); border-color:var(--pri-600) }
  .btn-outline-secondary{ border-color:#cbd5e1; color:#0f172a; }
  .btn-outline-secondary:hover{ background:#0f172a; border-color:#0f172a; color:#fff; }

  .empty{
    color:#64748b;
    border:1px dashed #e2e8f0;
    background:#fafafa;
    border-radius:1rem;
  }

  .pagination .page-link{ background:#fff; border-color:#e2e8f0; color:#334155 }
  .pagination .page-item.active .page-link{ background:var(--pri); border-color:var(--pri); color:#fff }

  .guide{
    background: linear-gradient(135deg, rgba(34,197,94,.08), rgba(168,85,247,.10));
    border:1px solid #e2e8f0;
    border-radius:1rem;
    padding:1rem;
  }
  .guide b{ color:#0f172a; }

  details summary{ cursor:pointer; color:#0f172a; }
  pre{ color:#111827; }
</style>

<?php
// --------------------------- Configuración & Datos --------------------------- //
$PAGE_SIZE = max(6, (int)($_GET['page_size'] ?? 12));
$page      = max(1, (int)($_GET['page'] ?? 1));
$q         = trim($_GET['q'] ?? '');
$dim       = strtoupper(trim($_GET['dim'] ?? ''));        // SOCIAL | ECONOMICO | AMBIENTAL | CTEI | GENERO
$cat       = strtoupper(trim($_GET['cat'] ?? ''));        // ARTICLE | BOOK | CHAPTER | THESIS | PROCEEDING | REPORT | TECH
$subtype   = strtoupper(trim($_GET['subtype'] ?? ''));    // A1 | A2 | B | C (solo si article)
$uni       = trim($_GET['uni'] ?? '');

// Cargar JSON
$publicaciones = [];
$jsonPath = __DIR__ . '/data/publicaciones.json';
if (is_file($jsonPath)) {
  $json = file_get_contents($jsonPath);
  $publicaciones = json_decode($json, true) ?: [];
}

// Muestra local si JSON vacío
if (!$publicaciones) {
  $publicaciones = [
    [
      'titulo' => 'Estrategias de resiliencia hídrica en cuencas altoandinas',
      'autores' => 'González P.; Rojas L.',
      'universidad' => 'UPTC',
      'anio' => 2024,
      'dimension' => 'AMBIENTAL',
      'tipo' => 'A2',
      'doi' => '10.1234/example.doi.001',
      'enlace' => 'https://doi.org/10.1234/example.doi.001',
      'resumen' => 'Modelo de balance hídrico y adaptación climática en Boyacá.',
      'etiquetas' => ['agua','clima','Boyacá']
    ],
    [
      'titulo' => 'Economía creativa y desarrollo territorial en provincias de Boyacá',
      'autores' => 'Suárez M.; Díaz N.',
      'universidad' => 'Universidad de Boyacá',
      'anio' => 2023,
      'dimension' => 'ECONOMICO',
      'tipo' => 'B',
      'doi' => '',
      'enlace' => 'https://repositorio.ejemplo/eco-creativa.pdf',
      'resumen' => 'Indicadores de competitividad y empleo en industrias culturales.',
      'etiquetas' => ['economía creativa','competitividad']
    ],
    [
      'titulo' => 'Lineamientos CTeI para gobernanza de datos en observatorios',
      'autores' => 'Monroy C.; Equipo Observatorio',
      'universidad' => 'UNAD',
      'anio' => 2025,
      'dimension' => 'CTEI',
      'tipo' => 'LIBRO',
      'doi' => '',
      'enlace' => 'https://observatorio.boyaca/libro-ctei.pdf',
      'resumen' => 'Manual práctico para gestión de indicadores y tableros.',
      'etiquetas' => ['datos','gobernanza','indicadores']
    ],
    [
      'titulo' => 'Violencia de pareja y rutas de atención en municipios rurales',
      'autores' => 'Páez J.; Romero S.',
      'universidad' => 'UPTC',
      'anio' => 2022,
      'dimension' => 'GENERO',
      'tipo' => 'A1',
      'doi' => '10.5678/example.doi.987',
      'enlace' => 'https://doi.org/10.5678/example.doi.987',
      'resumen' => 'Estudio longitudinal con enfoque diferencial.',
      'etiquetas' => ['género','derechos','ruralidad']
    ],
    [
      'titulo' => 'Cohesión social y participación ciudadana post-pandemia',
      'autores' => 'Lobatón G.; Equipo Territorio',
      'universidad' => 'Santo Tomás',
      'anio' => 2024,
      'dimension' => 'SOCIAL',
      'tipo' => 'C',
      'doi' => '',
      'enlace' => 'https://revista.ejemplo/social-cohesion',
      'resumen' => 'Encuesta departamental y análisis multivariado.',
      'etiquetas' => ['participación','cohesión']
    ],
    [
      'titulo' => 'Dirección de tesis de Maestría: Gemelos Digitales para riego',
      'autores' => 'Directora: Hernández A.',
      'universidad' => 'UNIBOYACÁ',
      'anio' => 2025,
      'dimension' => 'CTEI',
      'tipo' => 'DIRECCION',
      'doi' => '',
      'enlace' => 'https://repositorio.uni/tesis-gemelos-digitales',
      'resumen' => 'Arquitectura IoT + IA para optimización de agua.',
      'etiquetas' => ['IoT','IA','agro']
    ],
  ];
}

// ---- Seguridad (escape) ----
define('ENT_FLAGS', ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
function e(string $s): string { return htmlspecialchars($s, ENT_FLAGS); }

// --------------------------- Catálogo: Dimensiones & Tipos --------------------------- //
$DIM = [
  'SOCIAL'    => ['label'=>'Social',    'icon'=>'bi-people-fill',         'color'=>'var(--social)'],
  'ECONOMICO' => ['label'=>'Económica', 'icon'=>'bi-graph-up-arrow',      'color'=>'var(--econ)'],
  'AMBIENTAL' => ['label'=>'Ambiental', 'icon'=>'bi-tree-fill',           'color'=>'var(--amb)'],
  'CTEI'      => ['label'=>'CTeI',      'icon'=>'bi-cpu-fill',            'color'=>'var(--ctei)'],
  'GENERO'    => ['label'=>'Género',    'icon'=>'bi-gender-ambiguous',    'color'=>'var(--gen)'],
];

// Tipos “amigables” (Minciencias-friendly)
$CATS = [
  'ARTICLE'     => ['label'=>'Artículos científicos',          'icon'=>'bi-journal-text',     'hint'=>'A1/A2/B/C (si aplica)'],
  'BOOK'        => ['label'=>'Libros de investigación',        'icon'=>'bi-book-fill',        'hint'=>'Libro resultado de investigación'],
  'CHAPTER'     => ['label'=>'Capítulos de libro',             'icon'=>'bi-book-half',        'hint'=>'Capítulo en libro de investigación'],
  'THESIS'      => ['label'=>'Tesis / trabajos dirigidos',     'icon'=>'bi-mortarboard-fill', 'hint'=>'Dirección/asesoría de tesis'],
  'PROCEEDING'  => ['label'=>'Ponencias / memorias',           'icon'=>'bi-mic-fill',         'hint'=>'Congresos, simposios, memorias'],
  'REPORT'      => ['label'=>'Informes / documentos técnicos', 'icon'=>'bi-file-earmark-text','hint'=>'Informes, documentos de trabajo'],
  'TECH'        => ['label'=>'Prototipos / tecnología',        'icon'=>'bi-box-seam-fill',    'hint'=>'Desarrollo tecnológico (si aplica)'],
];

// Mapeo desde el campo "tipo" del JSON antiguo (A1/A2/B/C/LIBRO/DIRECCION/...) hacia categoría amigable
function map_tipo_to_cat(string $tipo): array {
  $t = strtoupper(trim($tipo));

  // Artículos (A1/A2/B/C)
  if (in_array($t, ['A1','A2','B','C'], true)) return ['cat'=>'ARTICLE', 'subtype'=>$t];

  // Libros (admite variantes)
  if (in_array($t, ['LIBRO','LIBRO_INV','LIBRO_RESULTADO','BOOK'], true)) return ['cat'=>'BOOK', 'subtype'=>$t];

  // Capítulos
  if (in_array($t, ['CAPITULO','CHAPTER'], true)) return ['cat'=>'CHAPTER', 'subtype'=>$t];

  // Tesis / Dirección
  if (in_array($t, ['DIRECCION','TESIS','MAESTRIA','DOCTORADO','THESIS'], true)) return ['cat'=>'THESIS', 'subtype'=>$t];

  // Ponencias / memorias
  if (in_array($t, ['PONENCIA','MEMORIA','CONGRESO','PROCEEDING'], true)) return ['cat'=>'PROCEEDING', 'subtype'=>$t];

  // Informes
  if (in_array($t, ['INFORME','REPORTE','DOCUMENTO','REPORT'], true)) return ['cat'=>'REPORT', 'subtype'=>$t];

  // Prototipos / tecnología
  if (in_array($t, ['PROTOTIPO','SOFTWARE','TECNOLOGICO','TECH'], true)) return ['cat'=>'TECH', 'subtype'=>$t];

  // Default
  return ['cat'=>'REPORT', 'subtype'=>$t ?: ''];
}

// Normalizar filtros
$validDims = array_keys($DIM);
$validCats = array_keys($CATS);
$validArticleSubs = ['A1','A2','B','C'];

if ($dim && !in_array($dim, $validDims, true)) $dim = '';
if ($cat && !in_array($cat, $validCats, true)) $cat = '';
if ($subtype && !in_array($subtype, $validArticleSubs, true)) $subtype = '';

// Universidades únicas
$universidades = array_values(array_unique(array_map(fn($p) => $p['universidad'] ?? '', $publicaciones)));
$universidades = array_filter($universidades, fn($u) => trim($u) !== '');
sort($universidades);

// --------------------------- Filtrado --------------------------- //
$filtered = array_filter($publicaciones, function($p) use($q,$dim,$cat,$subtype,$uni){
  $titulo = (string)($p['titulo'] ?? '');
  $autores = (string)($p['autores'] ?? '');
  $universidad = (string)($p['universidad'] ?? '');
  $resumen = (string)($p['resumen'] ?? '');
  $dimension = strtoupper((string)($p['dimension'] ?? ''));
  $tipo = (string)($p['tipo'] ?? '');

  $mapped = map_tipo_to_cat($tipo);
  $pCat = $mapped['cat'];
  $pSub = $mapped['subtype'];

  $ok = true;

  if ($q !== '') {
    $needle = mb_strtolower($q);
    $tags = '';
    if (!empty($p['etiquetas']) && is_array($p['etiquetas'])) $tags = implode(' ', $p['etiquetas']);
    $ok = $ok && (
      str_contains(mb_strtolower($titulo), $needle) ||
      str_contains(mb_strtolower($autores), $needle) ||
      str_contains(mb_strtolower($universidad), $needle) ||
      str_contains(mb_strtolower($resumen), $needle) ||
      str_contains(mb_strtolower($tags), $needle)
    );
  }

  if ($dim) $ok = $ok && ($dimension === $dim);

  if ($cat) {
    $ok = $ok && ($pCat === $cat);
    // Si filtra artículos y además un subtipo, aplicar
    if ($cat === 'ARTICLE' && $subtype) {
      $ok = $ok && ($pSub === $subtype);
    }
  }

  if ($uni !== '') $ok = $ok && (mb_strtolower($universidad) === mb_strtolower($uni));

  return $ok;
});

// --------------------------- Paginación --------------------------- //
$total  = count($filtered);
$pages  = max(1, (int)ceil($total / $PAGE_SIZE));
$page   = min($page, $pages);
$offset = ($page - 1) * $PAGE_SIZE;
$items  = array_slice(array_values($filtered), $offset, $PAGE_SIZE);

// --------------------------- Helpers UI --------------------------- //
function dim_meta(array $DIM, string $dim): array {
  $k = strtoupper($dim);
  return $DIM[$k] ?? ['label'=>($dim?:'—'), 'icon'=>'bi-circle', 'color'=>'#94a3b8'];
}

function cat_meta(array $CATS, string $cat): array {
  return $CATS[$cat] ?? ['label'=>($cat?:'Publicación'), 'icon'=>'bi-file-earmark', 'hint'=>''];
}

function cover_image_url(array $p): string {
  // Imagen “bonita” sin API key (Unsplash Source). Cambia o comenta si no deseas externas.
  $tags = [];
  if (!empty($p['etiquetas']) && is_array($p['etiquetas'])) $tags = $p['etiquetas'];
  $dim = strtolower((string)($p['dimension'] ?? ''));
  $q = trim(implode(',', array_slice($tags, 0, 3)));
  if ($q === '') $q = $dim ?: 'research';
  $q = preg_replace('/[^a-zA-Z0-9, \-áéíóúÁÉÍÓÚñÑ]/u', '', $q);
  return 'https://source.unsplash.com/900x600/?' . rawurlencode($q);
}

function linkWith($page,$page_size,$q,$dim,$cat,$subtype,$uni){
  $params = ['page'=>$page, 'page_size'=>$page_size];
  if($q!=='') $params['q']=$q;
  if($dim!=='') $params['dim']=$dim;
  if($cat!=='') $params['cat']=$cat;
  if($subtype!=='') $params['subtype']=$subtype;
  if($uni!=='') $params['uni']=$uni;
  return '?' . http_build_query($params);
}
?>

<section class="hero py-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-8">
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="quick-pill"><i class="bi bi-sparkles"></i> Explora, filtra y encuentra rápido</span>
          <span class="quick-pill"><i class="bi bi-shield-check"></i> Datos desde <code>data/publicaciones.json</code></span>
        </div>
        <h1 class="fw-extrabold mb-2" style="font-weight:800;">Publicaciones y Productos del Observatorio de Boyacá</h1>
        <p class="subtitle mb-0">
          Navega por <b>dimensión</b> (Social, Económica, Ambiental, CTeI y Género) y por <b>tipo de publicación</b>
          (artículos, libros, capítulos, tesis, ponencias, informes…). Todo en un lenguaje más claro para la comunidad.
        </p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <div class="mt-2">
          <span class="kbd">Ctrl</span> + <span class="kbd">K</span> <small class="text-muted">para enfocar búsqueda</small>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container py-4">

  <div class="guide mb-4">
    <div class="d-flex flex-wrap align-items-start gap-3">
      <div style="font-size:1.15rem;">
        <i class="bi bi-info-circle-fill"></i>
      </div>
      <div class="flex-grow-1">
        <b>Guía rápida:</b>
        <div class="text-muted">
          Los <b>Artículos científicos</b> pueden estar clasificados como A1, A2, B o C.
          En esta página lo mostramos de forma amigable (tipo principal) y dejamos la categoría como detalle.
        </div>
      </div>
      <div>
        <a class="btn btn-outline-secondary btn-sm" href="?" title="Quitar filtros">
          <i class="bi bi-arrow-counterclockwise"></i> Limpiar
        </a>
      </div>
    </div>
  </div>

  <!-- Tabs por dimensión -->
  <div class="dim-tabs mb-3">
    <?php
      $baseParams = ['q'=>$q,'cat'=>$cat,'subtype'=>$subtype,'uni'=>$uni,'page_size'=>$PAGE_SIZE];
      $allActive = ($dim==='');
      $allLink = '?' . http_build_query(array_filter($baseParams, fn($v)=>$v!=='' && $v!==null));
    ?>
    <a class="dim-tab <?= $allActive?'active':'' ?>" href="<?= e($allLink) ?>">
      <span class="dim-dot" style="background:#94a3b8"></span>
      <span>Todas</span>
    </a>
    <?php foreach($DIM as $k=>$info):
      $params = $baseParams; $params['dim']=$k; $params['page']=1;
      $link = '?' . http_build_query(array_filter($params, fn($v)=>$v!=='' && $v!==null));
      $active = ($dim===$k);
    ?>
      <a class="dim-tab <?= $active?'active':'' ?>" href="<?= e($link) ?>">
        <span class="dim-dot" style="background:<?= e($info['color']) ?>;"></span>
        <i class="bi <?= e($info['icon']) ?>"></i>
        <span><?= e($info['label']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Barra filtros -->
  <form class="filter-wrap p-3 mb-4" method="get" action="">
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="page_size" value="<?= (int)$PAGE_SIZE ?>" />
    <?php if($dim!==''): ?><input type="hidden" name="dim" value="<?= e($dim) ?>"><?php endif; ?>

    <div class="row g-3 align-items-center">
      <div class="col-lg-4">
        <label for="q" class="form-label mb-1">Buscar</label>
        <input id="q" name="q" type="search" class="form-control" placeholder="Título, autor, etiqueta, palabra clave…" value="<?= e($q) ?>" />
      </div>

      <div class="col-lg-3">
        <label for="cat" class="form-label mb-1">Tipo de publicación</label>
        <select id="cat" name="cat" class="form-select" onchange="toggleArticleSubtype()">
          <option value="">Todos</option>
          <?php foreach($CATS as $k=>$c): ?>
            <option value="<?= e($k) ?>" <?= $cat===$k?'selected':'' ?>>
              <?= e($c['label']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="small text-muted mt-1">
          <i class="bi bi-lightning-charge"></i> Tip: usa “Artículos científicos” y luego filtra A1/A2/B/C si lo necesitas.
        </div>
      </div>

      <div class="col-lg-2" id="articleSubtypeWrap" style="<?= ($cat==='ARTICLE') ? '' : 'display:none;' ?>">
        <label for="subtype" class="form-label mb-1">Categoría</label>
        <select id="subtype" name="subtype" class="form-select">
          <option value="">Todas</option>
          <?php foreach(['A1','A2','B','C'] as $s): ?>
            <option value="<?= $s ?>" <?= $subtype===$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-lg-2">
        <label for="uni" class="form-label mb-1">Universidad</label>
        <select id="uni" name="uni" class="form-select">
          <option value="">Todas</option>
          <?php foreach($universidades as $u): ?>
            <option value="<?= e($u) ?>" <?= ($uni && mb_strtolower($uni)===mb_strtolower($u))? 'selected':'' ?>>
              <?= e($u) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-lg-1 d-grid">
        <label class="form-label mb-1 opacity-0">_</label>
        <button class="btn btn-primary">
          <i class="bi bi-search"></i>
        </button>
      </div>
    </div>

    <!-- Chips de acceso rápido (Tipo) -->
    <div class="mt-3 d-flex flex-wrap gap-2">
      <?php foreach($CATS as $k=>$c):
        $active = ($cat===$k);
      ?>
        <label class="chip <?= $active?'active':'' ?>" title="<?= e($c['hint']) ?>">
          <input type="radio" name="cat" value="<?= e($k) ?>" <?= $active?'checked':'' ?> onchange="this.form.submit()">
          <i class="bi <?= e($c['icon']) ?>"></i>
          <?= e($c['label']) ?>
        </label>
      <?php endforeach; ?>

      <a class="chip soft" href="<?= e($allLink) ?>">
        <i class="bi bi-eraser-fill"></i> Quitar tipo
      </a>
    </div>
  </form>

  <!-- Resultados -->
  <?php if (!$total): ?>
    <div class="empty p-5 text-center">
      <h5 class="mb-2"><i class="bi bi-emoji-frown"></i> Sin resultados</h5>
      <p class="mb-0">Prueba con otra palabra clave o cambia el tipo/dimensión. También puedes quitar filtros con <b>Limpiar</b>.</p>
    </div>
  <?php else: ?>

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
      <div class="text-muted">
        Mostrando <b><?= count($items) ?></b> de <b><?= $total ?></b> resultados
        <?php if($dim!==''): $d=dim_meta($DIM,$dim); ?>
          • Dimensión: <b><?= e($d['label']) ?></b>
        <?php endif; ?>
        <?php if($cat!==''): $c=cat_meta($CATS,$cat); ?>
          • Tipo: <b><?= e($c['label']) ?></b>
        <?php endif; ?>
      </div>
      <div class="small text-muted">
        <i class="bi bi-mouse"></i> Tip: abre con “Abrir” o copia el DOI si aplica.
      </div>
    </div>

    <div class="row g-3">
      <?php foreach ($items as $p):
        $title = (string)($p['titulo'] ?? '');
        $authors = (string)($p['autores'] ?? '');
        $uniName = (string)($p['universidad'] ?? '');
        $year = (int)($p['anio'] ?? 0);
        $dimension = strtoupper((string)($p['dimension'] ?? ''));
        $tipo = (string)($p['tipo'] ?? '');
        $doi = (string)($p['doi'] ?? '');
        $link = (string)($p['enlace'] ?? '#');
        $abs = (string)($p['resumen'] ?? '');
        $tags = (!empty($p['etiquetas']) && is_array($p['etiquetas'])) ? $p['etiquetas'] : [];

        $d = dim_meta($DIM, $dimension);
        $mapped = map_tipo_to_cat($tipo);
        $pCat = $mapped['cat'];
        $pSub = $mapped['subtype'];
        $c = cat_meta($CATS, $pCat);

        $cover = cover_image_url($p);
      ?>
        <div class="col-md-6 col-xl-4">
          <article class="card-pub">
            <div class="cover">
              <img src="<?= e($cover) ?>" alt="Imagen relacionada a la publicación" loading="lazy" />
              <div class="cover-badges">
                <span class="badge-soft">
                  <i class="bi <?= e($d['icon']) ?>"></i>
                  <?= e($d['label']) ?>
                </span>

                <span class="badge-soft">
                  <i class="bi <?= e($c['icon']) ?>"></i>
                  <?= e($c['label']) ?>
                </span>

                <?php if($pCat==='ARTICLE' && in_array($pSub, ['A1','A2','B','C'], true)): ?>
                  <span class="badge-soft" title="Categoría del artículo">
                    <i class="bi bi-award-fill"></i> <?= e($pSub) ?>
                  </span>
                <?php endif; ?>
              </div>
            </div>

            <div class="p-3">
              <h5 class="mb-2" style="font-weight:800;">
                <a href="<?= e($link) ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none" style="color:#0f172a;">
                  <?= e($title) ?>
                </a>
              </h5>

              <div class="meta mb-2">
                <i class="bi bi-person-lines-fill"></i> <?= e($authors ?: 'Autores no especificados') ?>
                <span class="mx-2">•</span>
                <i class="bi bi-building"></i> <?= e($uniName ?: '—') ?>
                <span class="mx-2">•</span>
                <i class="bi bi-calendar3"></i> <?= $year ?: '—' ?>
              </div>

              <p class="mb-3" style="color:#334155;">
                <?= e($abs ?: 'Sin resumen disponible. Abre el enlace para conocer más.') ?>
              </p>

              <?php if($tags): ?>
                <div class="tags mb-3">
                  <?php foreach(array_slice($tags, 0, 6) as $tag): ?>
                    <span class="tag"><i class="bi bi-hash"></i> <?= e((string)$tag) ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="d-flex flex-wrap gap-2">
                <?php if($doi!==''): ?>
                  <a class="btn btn-sm btn-outline-secondary" href="https://doi.org/<?= e($doi) ?>" target="_blank" rel="noopener">
                    <i class="bi bi-link-45deg"></i> DOI
                  </a>
                <?php endif; ?>
                <a class="btn btn-sm btn-primary" href="<?= e($link) ?>" target="_blank" rel="noopener">
                  <i class="bi bi-box-arrow-up-right"></i> Abrir
                </a>
              </div>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Paginación -->
    <nav aria-label="Paginación" class="mt-4">
      <ul class="pagination justify-content-center">
        <li class="page-item <?= $page<=1? 'disabled':'' ?>">
          <a class="page-link" href="<?= e(linkWith(max(1,$page-1),$PAGE_SIZE,$q,$dim,$cat,$subtype,$uni)) ?>">Anterior</a>
        </li>

        <?php for($i=max(1,$page-2); $i<=min($pages,$page+2); $i++): ?>
          <li class="page-item <?= $i===$page? 'active':'' ?>">
            <a class="page-link" href="<?= e(linkWith($i,$PAGE_SIZE,$q,$dim,$cat,$subtype,$uni)) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <li class="page-item <?= $page>=$pages? 'disabled':'' ?>">
          <a class="page-link" href="<?= e(linkWith(min($pages,$page+1),$PAGE_SIZE,$q,$dim,$cat,$subtype,$uni)) ?>">Siguiente</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>

  <!-- Ayuda / Esquema JSON -->
  <div class="mt-5">
    <details>
      <summary class="mb-2">📄 Esquema esperado del archivo <code>data/publicaciones.json</code></summary>
<pre style="white-space:pre-wrap; background:#f8fafc; border:1px solid #e2e8f0; padding:1rem; border-radius:.75rem;">[
  {
    "titulo": "string",
    "autores": "string",
    "universidad": "string",
    "anio": 2025,
    "dimension": "SOCIAL|ECONOMICO|AMBIENTAL|CTEI|GENERO",
    "tipo": "A1|A2|B|C|LIBRO|CAPITULO|DIRECCION|PONENCIA|INFORME|PROTOTIPO|...",
    "doi": "10.xxxx/...",
    "enlace": "https://...",
    "resumen": "string",
    "etiquetas": ["opcional","array","de","tags"]
  }
]</pre>
      <div class="text-muted small mt-2">
        Nota: Si tu JSON sigue usando <code>A1/A2/B/C</code>, <code>LIBRO</code>, <code>DIRECCION</code>, etc., esta página lo convierte automáticamente a tipos amigables.
      </div>
    </details>
  </div>

</div>

<script>
  // UX: Ctrl+K para enfocar búsqueda
  window.addEventListener('keydown', (e)=>{
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase()==='k'){
      e.preventDefault();
      const q=document.getElementById('q'); if(q){ q.focus(); q.select(); }
    }
  });

  function toggleArticleSubtype(){
    const cat = document.getElementById('cat');
    const wrap = document.getElementById('articleSubtypeWrap');
    const sub = document.getElementById('subtype');
    if(!cat || !wrap) return;

    const isArticle = (cat.value === 'ARTICLE');
    wrap.style.display = isArticle ? '' : 'none';
    if(!isArticle && sub){ sub.value = ''; }
  }
</script>

<!-- Modal caracterización (se deja como estaba) -->
<div id="modalCaracterizacion" class="modal is-active">
  <div class="modal-background"></div>
  <div class="modal-content box">
    <h3><b>Ayúdanos a mejorar</b></h3>
    <p>Cuéntanos un poco sobre ti (opcional)</p>

    <form id="formCarac">
      <label>Profesión</label>
      <select class="select" name="profesion">
        <option value="">Seleccione</option>
        <option>Docente Universitario</option>
        <option>Docente Colegio</option>
        <option>Estudiante Universitario</option>
        <option>Estudiante Colegio</option>
        <option>Servidor Público</option>
        <option>ONG</option>
        <option>Sociedad Civil</option>
      </select>

      <label class="mt-3">Edad</label>
      <select class="select" name="edad">
        <option value="">Seleccione</option>
        <option>11-17</option>
        <option>18-24</option>
        <option>25-34</option>
        <option>35-44</option>
        <option>45-54</option>
        <option>55-64</option>
        <option>65+</option>
      </select>

      <label class="mt-3">Otra clasificación</label>
      <input type="text" class="input" name="otro">

      <button class="button is-primary mt-4">Enviar</button>
    </form>
  </div>
</div>

<script>
document.getElementById("formCarac").onsubmit = async (e)=>{
  e.preventDefault();
  let form = new FormData(e.target);

  await fetch("/website/formulario_usuario.php", {
    method:"POST",
    body: form
  });

  document.getElementById("modalCaracterizacion").style.display="none";
};
</script>

<?php include 'include/footer.php'; ?>
