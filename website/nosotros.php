<?php
require_once 'tracker.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/cms_microsite_sections.php';
// Entidades integrantes/aliados: se reutiliza el mismo carrusel del observatorio
// de Asuntos de Género (sección CMS widget-integrantes del slug 'genero').
$msWidgetSections = [];
$nosPdo = cms_pdo();
if ($nosPdo) {
    foreach (cms_microsite_sections_tree($nosPdo, 'genero', true) as $msRoot) {
        if (strpos((string) ($msRoot['section_key'] ?? ''), 'widget-') === 0) {
            $msWidgetSections[$msRoot['section_key']] = $msRoot;
        }
    }
}
$pageTitle = 'Nosotros · Red de Observatorios de Boyacá';
$pageDescription = 'Quiénes somos: origen, misión, visión y objetivos de la Red de Observatorios de Boyacá (ROB).';
$themeColor = '#0b2744';
$themeAccent = '#23a9b8';
include 'include/site-header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    .nos{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;color:#0f172a}
    .nos-hero{background:radial-gradient(900px 460px at 12% -20%,rgba(35,169,184,.18),transparent),linear-gradient(180deg,#0b2744,#0f3557);color:#fff;padding:3.25rem 0 2.75rem}
    .nos-hero h1{font-weight:800;letter-spacing:-.02em;font-size:clamp(1.7rem,3.4vw,2.4rem);margin-bottom:.6rem}
    .nos-hero p{color:#cfe0ee;max-width:760px;margin:0;font-size:1.02rem}
    .nos-section{padding:2.5rem 0}
    .nos-section h2{font-weight:800;color:#0b2744;letter-spacing:-.01em;margin-bottom:.35rem}
    .nos-eyebrow{display:inline-flex;align-items:center;gap:.45rem;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#23a9b8;margin-bottom:.5rem}
    .nos-card{background:#fff;border:1px solid #e7ecf3;border-radius:18px;padding:1.5rem;box-shadow:0 10px 30px rgba(2,6,23,.06);height:100%}
    .nos-card h3{font-weight:800;color:#0b2744;font-size:1.1rem;margin-bottom:.6rem;display:flex;align-items:center;gap:.55rem}
    .nos-card p{color:#3a4759;line-height:1.7;margin:0}
    .nos-lead{color:#3a4759;line-height:1.8;font-size:1.02rem}
    .nos-mv{border-left:5px solid var(--c,#23a9b8)}
    .nos-list{list-style:none;padding:0;margin:0;display:grid;gap:.8rem}
    .nos-list li{background:#fff;border:1px solid #e7ecf3;border-radius:12px;padding:.9rem 1.1rem;color:#3a4759;line-height:1.6;display:flex;gap:.75rem;align-items:flex-start}
    .nos-list li i{color:#23a9b8;margin-top:.25rem}
    .nos-soft{background:#f4f7fc}
    .dim-row{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem}
    .dim-item{background:#fff;border:1px solid #e7ecf3;border-radius:16px;padding:1.2rem;border-top:4px solid var(--c)}
    .dim-item h4{font-weight:800;color:#0b2744;font-size:1.02rem;margin-bottom:.4rem;display:flex;align-items:center;gap:.5rem}
    .dim-item p{color:#3a4759;line-height:1.6;margin:0;font-size:.95rem}
    .org-img{width:100%;border-radius:16px;border:1px solid #e7ecf3;box-shadow:0 10px 30px rgba(2,6,23,.08)}
    .founders{display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;align-items:center}
    .founders .f-logo{display:flex;align-items:center;justify-content:center;background:#fff;border:1px solid #eef0f4;border-radius:14px;padding:1rem;height:104px;box-shadow:0 4px 14px rgba(2,6,23,.05)}
    .founders img{max-width:100%;max-height:64px;object-fit:contain}
    @media (max-width:900px){.dim-row{grid-template-columns:1fr}.founders{grid-template-columns:repeat(3,1fr)}}
    @media (max-width:560px){.founders{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="nos">
    <section class="nos-hero">
        <div class="container">
            <nav aria-label="breadcrumb" class="small mb-3"><a href="index.php" style="color:#9fc3da;text-decoration:none">Inicio</a> <span style="color:#5b7390">›</span> <span style="color:#fff">Nosotros</span></nav>
            <h1>Red de Observatorios de Boyacá</h1>
            <p>Una red intelectual y tecnológica que recopila, analiza y divulga información de las dimensiones social, económica, ambiental y de ciencia, tecnología e innovación, para fortalecer la toma de decisiones en el territorio.</p>
        </div>
    </section>

    <!-- Origen -->
    <section class="nos-section">
        <div class="container">
            <span class="nos-eyebrow"><i class="fa-solid fa-flag"></i> Origen</span>
            <h2>¿Cómo nace la ROB?</h2>
            <p class="nos-lead mt-2">
                La iniciativa de creación de la Red de Observatorios Social, Económico, Ambiental y Ciencia, Tecnología e Innovación – CTeI del departamento de Boyacá (ROB) se remonta al acuerdo de voluntades que firmaron las universidades con la Secretaría de Planeación de Boyacá. A través de un ejercicio participativo con entidades académicas, el sector productivo, el gobierno y otros observatorios presentes en Boyacá, se ratificó el acuerdo para dar origen a la ROB, que busca <em>“aunar esfuerzos técnicos, administrativos y financieros entre el departamento de Boyacá y los actores del Sistema Regional de Ciencia, Tecnología e Innovación, para fortalecer las capacidades en la construcción de planes, programas, proyectos y reportes científicos”.</em>
            </p>
        </div>
    </section>

    <!-- Misión / Visión -->
    <section class="nos-section nos-soft">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="nos-card nos-mv" style="--c:#23a9b8">
                        <h3><i class="fa-solid fa-compass" style="color:#23a9b8"></i> Misión</h3>
                        <p>Contribuir con la generación, análisis y apropiación social de información del departamento en las dimensiones social, económica, ambiental y de ciencia, tecnología e innovación, mediante el trabajo colaborativo e interdisciplinario, para fortalecer la gestión estratégica del conocimiento con impacto positivo en proyectos que mejoren las condiciones del territorio.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="nos-card nos-mv" style="--c:#d8a21d">
                        <h3><i class="fa-solid fa-binoculars" style="color:#d8a21d"></i> Visión</h3>
                        <p>Para el año 2030, la ROB será reconocida como una fuente de información líder para la formulación y proyección de proyectos de inversión e investigación, a través de información confiable, periódica y pertinente que permita la toma de decisiones estratégicas en ámbitos gubernamentales y empresariales.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Objetivo general + específicos -->
    <section class="nos-section">
        <div class="container">
            <span class="nos-eyebrow"><i class="fa-solid fa-bullseye"></i> Objetivos</span>
            <h2>Objetivo general</h2>
            <p class="nos-lead mt-2 mb-4">
                Desarrollar una red intelectual y tecnológica que potencialice la gestión del conocimiento en CTeI mediante la recopilación, análisis, interpretación, difusión y divulgación de la información generada sobre los sectores social, económico, ambiental y tecnológico, que permita la articulación e intervención en el territorio con el desarrollo de proyectos, fortaleciendo la Apropiación Social del Conocimiento y contribuyendo a los Objetivos de Desarrollo Sostenible (ODS).
            </p>
            <h3 class="h5 fw-bold" style="color:#0b2744">Objetivos específicos</h3>
            <ul class="nos-list mt-3">
                <li><i class="fa-solid fa-circle-check"></i> Generar y publicar información confiable, periódica y pertinente que permita la toma de decisiones estratégicas sobre iniciativas de impacto social, económico, ambiental y de CTeI.</li>
                <li><i class="fa-solid fa-circle-check"></i> Validar o proponer indicadores estratégicos de acuerdo con las necesidades del ecosistema de CTI en los ámbitos social, ambiental, económico y tecnológico.</li>
                <li><i class="fa-solid fa-circle-check"></i> Garantizar la participación efectiva y continua de los integrantes de la ROB mediante un flujo permanente de comunicación.</li>
                <li><i class="fa-solid fa-circle-check"></i> Promover escenarios de co-creación y co-participación del conocimiento de CTeI en Boyacá para la formulación de políticas públicas y la toma de decisiones.</li>
                <li><i class="fa-solid fa-circle-check"></i> Generar espacios de divulgación y diálogo para comprender las realidades del territorio con la participación incluyente de los actores del ecosistema de CTI.</li>
            </ul>
        </div>
    </section>

    <!-- Dimensiones -->
    <section class="nos-section nos-soft">
        <div class="container">
            <span class="nos-eyebrow"><i class="fa-solid fa-layer-group"></i> Dimensiones temáticas</span>
            <h2 class="mb-4">Las dimensiones de la ROB</h2>
            <div class="dim-row">
                <div class="dim-item" style="--c:#0f3557">
                    <h4><i class="fa-solid fa-chart-line" style="color:#0f3557"></i> Económica</h4>
                    <p>Monitorea los principales indicadores de la realidad económica del departamento: sector agropecuario, minero, desarrollo empresarial y de servicios, y las variables de competitividad, productividad y finanzas públicas.</p>
                </div>
                <div class="dim-item" style="--c:#5f2a8a">
                    <h4><i class="fa-solid fa-people-group" style="color:#5f2a8a"></i> Social</h4>
                    <p>Reporta y monitorea el comportamiento de las principales variables sociales del departamento, para analizar necesidades, problemáticas y potencialidades que afectan la calidad de vida de la población boyacense.</p>
                </div>
                <div class="dim-item" style="--c:#1f6b45">
                    <h4><i class="fa-solid fa-leaf" style="color:#1f6b45"></i> Ambiental</h4>
                    <p>Proporciona indicadores relevantes de las distintas categorías ambientales, para tomar decisiones que salvaguarden los recursos naturales y garanticen un desarrollo sostenible y sustentable en Boyacá.</p>
                </div>
                <div class="dim-item" style="--c:#1847b7">
                    <h4><i class="fa-solid fa-microchip" style="color:#1847b7"></i> Ciencia, Tecnología e Innovación</h4>
                    <p>Monitorea recursos, capacidades, talento joven de investigación, generación de nuevo conocimiento, infraestructura e innovación, según los lineamientos de la política de CTI nacional y departamental.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modelo organizacional -->
    <section class="nos-section">
        <div class="container">
            <span class="nos-eyebrow"><i class="fa-solid fa-sitemap"></i> Modelo organizacional</span>
            <h2 class="mb-3">Cómo nos organizamos</h2>
            <p class="nos-lead mb-4">
                La ROB adopta una estructura matricial con distribución vertical, pertinente para organizaciones basadas en conocimiento, donde equipos especializados trabajan de manera flexible en distintos servicios y proyectos. Cada área funcional reúne personas de diferentes universidades y grupos de interés académico, ambiental y organizacional. La estructura está encabezada por la Junta Directiva de Fundadores, con la Secretaría de Planeación de Boyacá como secretaría técnica, asesorada por un Consejo de Gobernanza.
            </p>
            <img class="org-img" src="assets/pictures/organigrama.png" alt="Organigrama de la Red de Observatorios de Boyacá" loading="lazy">
        </div>
    </section>

    <!-- Miembros fundadores -->
    <section class="nos-section nos-soft">
        <div class="container">
            <span class="nos-eyebrow"><i class="fa-solid fa-handshake"></i> Miembros fundadores</span>
            <h2 class="mb-2">Quiénes conforman la Red</h2>
            <p class="nos-lead mb-4">Podrán pertenecer a la ROB los diferentes actores del Sistema Regional de Ciencia, Tecnología e Innovación que contribuyan con la generación de datos periódicos, sistémicos y confiables.</p>
            <div class="founders">
                <?php
                $fundadores = [
                    ['gobboy.svg', 'Gobernación de Boyacá', 'gobboy.png'],
                    ['uniboy.png', 'Universidad de Boyacá', null],
                    ['uan.svg', 'Universidad Antonio Nariño', null],
                    ['unad.png', 'UNAD', null],
                    ['jdc.png', 'Fundación Universitaria Juan de Castellanos', null],
                    ['santoto.png', 'Universidad Santo Tomás (Tunja)', null],
                    ['uptc.svg', 'UPTC', null],
                    ['crci.png', 'Comisión Regional de Competitividad e Innovación', null],
                    ['esap.png', 'Escuela Superior de Administración Pública', null],
                ];
                foreach ($fundadores as $m):
                    $src = is_file(__DIR__ . '/assets/svg/logos/' . $m[0]) ? $m[0] : ($m[2] ?? $m[0]);
                ?>
                    <div class="f-logo" title="<?= htmlspecialchars($m[1]) ?>">
                        <img src="assets/svg/logos/<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($m[1]) ?>" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Créditos -->
    <section class="nos-section">
        <div class="container text-center">
            <p class="text-muted mb-3" style="max-width:820px;margin:0 auto">Sitio desarrollado por el equipo de Observatorio de Boyacá de la Dirección de Seguimiento de Planeación Territorial de la Secretaría de Planeación de la Gobernación de Boyacá.</p>
            <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap mt-3">
                <img src="assets/svg/logos/gobboy.png" alt="Gobernación de Boyacá" style="height:80px;object-fit:contain">
                <img src="assets/svg/logos/secplan.png" alt="Secretaría de Planeación · Gobernación de Boyacá" style="height:72px;object-fit:contain">
            </div>
        </div>
    </section>
</div>

<!-- Nuestros aliados (mismo carrusel de integrantes del observatorio de género) -->
<?php $integrantesHeading = 'Nuestros aliados'; require __DIR__ . '/include/integrantes-carrusel.php'; ?>

<?php require __DIR__ . '/include/site-footer.php'; ?>
