<?php
require_once __DIR__ . '/admin/auth/bootstrap.php';
auth_require_login();
require_once __DIR__ . '/config/database.php';

$pdo = cms_pdo();
if (!$pdo) {
    http_response_code(503);
    exit('Base de datos no disponible: ' . htmlspecialchars((string) cms_last_db_error()));
}

// Todas las consultas de analítica dependen de la tabla accesos_observatorio
// (migración 015). Si falta o la BD falla, degradar a un estado vacío sin
// pantalla blanca.
$dashboardError = null;
$paises = $paginas = $dias = $dispositivos = $navegadores = $geo = [];
try {
    $paises = $pdo->query("
        SELECT pais, COUNT(*) AS total
        FROM accesos_observatorio
        WHERE pais <> ''
        GROUP BY pais
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $paginas = $pdo->query("
        SELECT pagina, COUNT(*) AS total
        FROM accesos_observatorio
        GROUP BY pagina
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $dias = $pdo->query("
        SELECT DATE(fecha) AS dia, COUNT(*) AS total
        FROM accesos_observatorio
        GROUP BY DATE(fecha)
        ORDER BY dia ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $dispositivos = $pdo->query("
        SELECT dispositivo, COUNT(*) AS total
        FROM accesos_observatorio
        GROUP BY dispositivo
    ")->fetchAll(PDO::FETCH_ASSOC);

    $navegadores = $pdo->query("
        SELECT navegador, COUNT(*) AS total
        FROM accesos_observatorio
        GROUP BY navegador
    ")->fetchAll(PDO::FETCH_ASSOC);

    $geo = $pdo->query("
        SELECT ciudad, pais, latitud, longitud, COUNT(*) AS total
        FROM accesos_observatorio
        WHERE latitud <> '' AND longitud <> ''
        GROUP BY ciudad, pais, latitud, longitud
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $dashboardError = 'Aún no hay datos de analítica disponibles. (La tabla de accesos no está creada en este entorno; ejecute la migración 015.)';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-192x192.png">
    <link rel="apple-touch-icon" href="assets/favicon/cropped-cropped-cropped-cropped-Logo-red-de-obdervatorios_Sin-fondo-1-180x180.png">
    <meta charset="UTF-8">
    <title>Dashboard de Accesos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial; margin: 20px; }
        canvas { margin: 40px 0; }
        h1 { text-align: center; }
    </style>
</head>
<body>

<h1>Dashboard de Accesos – Observatorio de Boyacá</h1>

<?php if ($dashboardError): ?>
<div style="max-width:760px;margin:24px auto;padding:16px 20px;background:#fff3cd;border:1px solid #ffe69c;border-radius:8px;color:#664d03;text-align:center">
    <?= htmlspecialchars($dashboardError) ?>
</div>
<?php endif; ?>

<h2>Accesos por país</h2>
<canvas id="chartPais"></canvas>

<h2>Accesos por página</h2>
<canvas id="chartPaginas"></canvas>

<h2>Accesos por día</h2>
<canvas id="chartDias"></canvas>

<h2>Dispositivos utilizados</h2>
<canvas id="chartDispositivos"></canvas>

<h2>Navegadores</h2>
<canvas id="chartNavegadores"></canvas>

<script>
// ------ Gráfica Países ------
new Chart(document.getElementById('chartPais'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($paises, 'pais')) ?>,
        datasets: [{
            label: 'Visitas',
            data: <?= json_encode(array_column($paises, 'total')) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)'
        }]
    }
});

// ------ Gráfica Páginas ------
new Chart(document.getElementById('chartPaginas'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($paginas, 'pagina')) ?>,
        datasets: [{
            label: 'Visitas',
            data: <?= json_encode(array_column($paginas, 'total')) ?>,
            backgroundColor: 'rgba(255, 159, 64, 0.5)'
        }]
    }
});

// ------ Gráfica por Día ------
new Chart(document.getElementById('chartDias'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($dias, 'dia')) ?>,
        datasets: [{
            label: 'Visitas por día',
            data: <?= json_encode(array_column($dias, 'total')) ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: false
        }]
    }
});

// ------ Dispositivos ------
new Chart(document.getElementById('chartDispositivos'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($dispositivos, 'dispositivo')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($dispositivos, 'total')) ?>,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
        }]
    }
});

// ------ Navegadores ------
new Chart(document.getElementById('chartNavegadores'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($navegadores, 'navegador')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($navegadores, 'total')) ?>,
            backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FF9F40', '#9966FF']
        }]
    }
});
</script>

</body>
</html>
