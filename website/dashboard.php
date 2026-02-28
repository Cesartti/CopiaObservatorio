<?php
$pdo = new PDO("mysql:host=localhost;dbname=observatorio_boyaca;charset=utf8mb4", "root", "");

// --- 1. Accesos por país ---
$paises = $pdo->query("
    SELECT pais, COUNT(*) AS total
    FROM accesos_observatorio
    WHERE pais <> ''
    GROUP BY pais
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

// --- 2. Accesos por página ---
$paginas = $pdo->query("
    SELECT pagina, COUNT(*) AS total
    FROM accesos_observatorio
    GROUP BY pagina
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

// --- 3. Accesos por día ---
$dias = $pdo->query("
    SELECT DATE(fecha) AS dia, COUNT(*) AS total
    FROM accesos_observatorio
    GROUP BY DATE(fecha)
    ORDER BY dia ASC
")->fetchAll(PDO::FETCH_ASSOC);

// --- 4. Dispositivos ---
$dispositivos = $pdo->query("
    SELECT dispositivo, COUNT(*) AS total
    FROM accesos_observatorio
    GROUP BY dispositivo
")->fetchAll(PDO::FETCH_ASSOC);

// --- 5. Navegadores ---
$navegadores = $pdo->query("
    SELECT navegador, COUNT(*) AS total
    FROM accesos_observatorio
    GROUP BY navegador
")->fetchAll(PDO::FETCH_ASSOC);

// --- 6. País + Ciudad (Mapa) ---
$geo = $pdo->query("
    SELECT ciudad, pais, latitud, longitud, COUNT(*) AS total
    FROM accesos_observatorio
    WHERE latitud <> '' AND longitud <> ''
    GROUP BY ciudad, pais, latitud, longitud
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
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
