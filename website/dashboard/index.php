<?php
// dashboard/index.php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_login();
$authUser = auth_user();
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Observatorio de Boyacá</title>

  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Leaflet + Heatmap -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

  <style>
    .chart-container { height: 350px; margin-bottom: 25px; }
    #mapAccesos { width: 100%; height: 460px; border-radius: 12px; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">

  <!-- Navbar -->
  <?php include __DIR__ . "/partials/navbar.php"; ?>

  <!-- Sidebar -->
  <?php include __DIR__ . "/partials/sidebar.php"; ?>

  <!-- CONTENIDO PRINCIPAL -->
  <div class="content-wrapper">

    <section class="content-header text-center">
      <div class="container-fluid">
        <h1>Dashboard de Accesos – Observatorio de Boyacá</h1>
        <p>Sesión: <?php echo htmlspecialchars($authUser['email']); ?> · Rol: <?php echo htmlspecialchars($authUser['role']); ?></p>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">

        <!-- TARJETAS RESUMEN -->
        <div class="row">

          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3 id="cardTotalVisitas">0</h3>
                <p>Visitas totales</p>
              </div>
              <div class="icon"><i class="fas fa-chart-line"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3 id="cardVisitasHoy">0</h3>
                <p>Visitas hoy</p>
              </div>
              <div class="icon"><i class="fas fa-calendar-day"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3 id="cardTotalPaises">0</h3>
                <p>Países distintos</p>
              </div>
              <div class="icon"><i class="fas fa-globe"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3 id="cardTotalPaginas">0</h3>
                <p>Páginas / dimensiones</p>
              </div>
              <div class="icon"><i class="fas fa-layer-group"></i></div>
            </div>
          </div>

        </div>

        <!-- FILTROS -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title"><i class="fa fa-filter"></i> Filtros dinámicos</h3>
          </div>

          <div class="card-body row">

            <div class="col-md-4">
              <label>Dimensión / Página</label>
              <select id="filtroPagina" class="form-control">
                <option value="">Todas</option>
                <option value="publicaciones.php">Página Publicaciones Universidades</option>
                <option value="indic-genero.php">Dimensión de Género</option>
                <option value="indic-ambiental.php">Dimensión Ambiental</option>
                <option value="indic-economico.php">Dimensión Económica</option>
                <option value="indic-social.php">Dimensión Social</option>
                <option value="indic-tecnologia.php">Dimensión CTeI</option>
                <option value="indic-boletin.php">Boletines</option>
              </select>
            </div>

            <div class="col-md-4">
              <label>Fecha inicial</label>
              <input type="date" id="fechaInicio" class="form-control">
            </div>

            <div class="col-md-4">
              <label>Fecha final</label>
              <input type="date" id="fechaFin" class="form-control">
            </div>

          </div>

          <div class="card-footer text-right">
            <button id="btnFiltrar" class="btn btn-success">
              <i class="fa fa-search"></i> Aplicar filtros
            </button>
          </div>
        </div>

        <!-- GRÁFICAS -->
        <div class="row">

          <div class="col-md-6">
            <div class="card card-outline card-info">
              <div class="card-header"><h3 class="card-title">Accesos por país</h3></div>
              <div class="card-body chart-container"><canvas id="chartPais"></canvas></div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card card-outline card-warning">
              <div class="card-header"><h3 class="card-title">Accesos por página / dimensión</h3></div>
              <div class="card-body chart-container"><canvas id="chartPaginas"></canvas></div>
            </div>
          </div>

        </div>

        <div class="row">

          <div class="col-md-6">
            <div class="card card-outline card-success">
              <div class="card-header"><h3 class="card-title">Accesos por día</h3></div>
              <div class="card-body chart-container"><canvas id="chartDias"></canvas></div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card card-outline card-danger">
              <div class="card-header"><h3 class="card-title">Dispositivos</h3></div>
              <div class="card-body chart-container"><canvas id="chartDispositivos"></canvas></div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card card-outline card-primary">
              <div class="card-header"><h3 class="card-title">Navegadores</h3></div>
              <div class="card-body chart-container"><canvas id="chartNavegadores"></canvas></div>
            </div>
          </div>

        </div>

        <!-- MAPA AVANZADO -->
        <div class="card card-outline card-secondary">
          <div class="card-header">
            <h3 class="card-title">Mapa de accesos</h3>
          </div>
          <div class="card-body">
            <div id="mapAccesos"></div>
          </div>
        </div>

      </div>
    </section>

  </div>

  <?php include __DIR__ . "/partials/footer.php"; ?>

</div>

<script src="js/dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<?php include __DIR__ . "/modal-info.php"; ?>

</body>
</html>
