<!-- dashboard/partials/sidebar.php -->
<?php $dashboardPage = basename($_SERVER['SCRIPT_NAME'] ?? ''); ?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="#" class="brand-link">
    <span class="brand-text font-weight-light ml-2">Observatorio Boyacá</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column">

        <li class="nav-item">
          <a href="index.php" class="nav-link <?= $dashboardPage === 'index.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>Dashboard General</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="tablero-datos.php" class="nav-link <?= $dashboardPage === 'tablero-datos.php' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-table"></i>
            <p>Tablero exploratorio</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../cms/index.php" class="nav-link">
            <i class="nav-icon fas fa-database"></i>
            <p>CMS (contenido)</p>
          </a>
        </li>

        <li class="nav-header">Dimensiones</li>

        <li class="nav-item">
          <a href="../publicaciones.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Página Publicaciones Universidades</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../indic-genero.php" class="nav-link">
            <i class="fas fa-venus nav-icon"></i>
            <p>Dimensión de Género</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../indic-ambiental.php" class="nav-link">
            <i class="fas fa-leaf nav-icon"></i>
            <p>Dimensión Ambiental</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../indic-economico.php" class="nav-link">
            <i class="fas fa-coins nav-icon"></i>
            <p>Dimensión Económica</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../indic-social.php" class="nav-link">
            <i class="fas fa-users nav-icon"></i>
            <p>Dimensión Social</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../indic-tecnologia.php" class="nav-link">
            <i class="fas fa-flask nav-icon"></i>
            <p>Dimensión CTeI</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../indic-boletin.php" class="nav-link">
            <i class="fas fa-newspaper nav-icon"></i>
            <p>Boletines</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
