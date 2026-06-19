<?php
require_once __DIR__ . '/../admin/auth/bootstrap.php';
auth_require_login();
$authUser = auth_user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tablero exploratorio · Excel / CSV · Observatorio de Boyacá</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .echart-box { width: 100%; height: 360px; min-height: 280px; }
    .preview-table { max-height: 220px; overflow: auto; font-size: 0.85rem; }
    .preview-table th, .preview-table td { white-space: nowrap; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">

  <?php include __DIR__ . '/partials/navbar.php'; ?>
  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <div class="content-wrapper">

    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Tablero exploratorio</h1>
          </div>
          <div class="col-sm-6 text-sm-right">
            <span class="text-muted"><?php echo htmlspecialchars($authUser['email'] ?? ''); ?></span>
          </div>
        </div>
        <p class="text-muted mb-0">
          Cargue un archivo Excel o CSV; elija columnas de categoría y valor, aplique filtros y visualice varias gráficas interactivas (Apache ECharts + lectura local con SheetJS).
        </p>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">

        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title"><i class="fa fa-file-excel"></i> Origen de datos</h3>
          </div>
          <div class="card-body row">
            <div class="col-md-6">
              <label for="archivoDatos">Archivo (.xlsx, .xls, .csv)</label>
              <input type="file" id="archivoDatos" class="form-control-file" accept=".xlsx,.xls,.csv">
            </div>
            <div class="col-md-3">
              <label for="hojaExcel">Hoja (Excel)</label>
              <select id="hojaExcel" class="form-control" disabled>
                <option value="">—</option>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="button" id="btnLimpiar" class="btn btn-outline-secondary btn-block" disabled>
                <i class="fa fa-eraser"></i> Limpiar
              </button>
            </div>
          </div>
        </div>

        <div class="card card-secondary" id="cardMapeo">
          <div class="card-header">
            <h3 class="card-title"><i class="fa fa-sliders-h"></i> Mapeo y filtros</h3>
          </div>
          <div class="card-body row">
            <div class="col-md-3">
              <label for="colCategoria">Columna categoría (eje X)</label>
              <select id="colCategoria" class="form-control" disabled></select>
            </div>
            <div class="col-md-3">
              <label for="colValor">Columna valor (numérica)</label>
              <select id="colValor" class="form-control" disabled></select>
            </div>
            <div class="col-md-3">
              <label for="colFiltro">Columna filtro (opcional)</label>
              <select id="colFiltro" class="form-control" disabled>
                <option value="">— Ninguna —</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="valorFiltro">Valor del filtro</label>
              <select id="valorFiltro" class="form-control" disabled>
                <option value="">—</option>
              </select>
            </div>
          </div>
          <div class="card-footer">
            <button type="button" id="btnAplicar" class="btn btn-success" disabled>
              <i class="fa fa-sync"></i> Aplicar a gráficas
            </button>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="card card-outline card-info">
              <div class="card-header"><h3 class="card-title">Barras agrupadas</h3></div>
              <div class="card-body"><div id="chartBarras" class="echart-box"></div></div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card card-outline card-success">
              <div class="card-header"><h3 class="card-title">Líneas</h3></div>
              <div class="card-body"><div id="chartLineas" class="echart-box"></div></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="card card-outline card-warning">
              <div class="card-header"><h3 class="card-title">Pastel / dona</h3></div>
              <div class="card-body"><div id="chartPastel" class="echart-box"></div></div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card card-outline card-secondary">
              <div class="card-header"><h3 class="card-title">Vista previa (primeras filas)</h3></div>
              <div class="card-body p-0">
                <div id="tablaPreview" class="table-responsive preview-table p-2">
                  <p class="text-muted m-2">Cargue un archivo para ver la tabla.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

  </div>

  <?php include __DIR__ . '/partials/footer.php'; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script src="js/tablero-datos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>
</html>
