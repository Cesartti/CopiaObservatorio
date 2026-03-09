<body>
    <header>
        <nav id="navbar" class="d-none d-md-block navbar navbar-expand-md navbar-fixed-top navbar-light">
            <div class="container">
                <a class="navbar-brand logo" href="index.php">
                    <img src="assets/svg/logo.svg" alt="Inicio" class="logo">
                </a>
                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarObservatorio"
                        aria-controls="navbarObservatorio"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>

            <div class="collapse navbar-collapse" id="navbarObservatorio">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php if ($current_url === 'red-home') echo 'active'; ?>" href="red-home.php">
                            <i class="fa-solid fa-layer-group"></i> Nueva Red
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($current_url === 'about') echo 'active'; ?>" href="about.php">
                            <i class="fa-solid fa-users"></i> Nosotros
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php if (in_array($current_url, ['indic-economico', 'indic-social', 'indic-ambiental', 'indic-tecnologia'])) echo 'active'; ?>" href="#" id="navbarDropdownIndicadores" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-chart-simple"></i> Dimensiones
                        </a>
                        <ul class="dropdown-menu shadow rounded" aria-labelledby="navbarDropdownIndicadores">
                            <li>
                                <a class="dropdown-item" href="indic-economico.php">
                                    <i class="fa-solid fa-coins text-warning"></i> Económico
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="indic-social.php">
                                    <i class="fa-solid fa-people-group text-info"></i> Social
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="indic-ambiental.php">
                                    <i class="fa-solid fa-leaf text-success"></i> Ambiental
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="indic-tecnologia.php">
                                    <i class="fa-solid fa-microchip text-primary"></i> Tecnología e innovación
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="indic-genero.php">
                                    <i class="fa-solid fa-venus-mars text-primary"></i> Asuntos de género
                                </a>
                            </li>

                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($current_url === 'indic-boletin') echo 'active'; ?>" href="indic-boletin.php">
                            <i class="fa-solid fa-file-lines"></i> Boletines
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($current_url === 'indic-boletin') echo 'active'; ?>" href="publicaciones.php">
                            <i class="fa-solid fa-file-lines"></i> Publicaciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($current_url === 'estado-observatorio') echo 'active'; ?>" href="estado-observatorio.php">
                            <i class="fa-solid fa-gauge"></i> Estado de datos
                        </a>
                    </li>
                </ul>
            </div>
            
    </div>
</nav>


    <div class="container" style="padding-top: 100px;">
        <!-- Contenido aquí -->
    </div>

    <div id="sidebar-container">
        <div class="mySideNav">
            <ul class="side-menu">
                <li><a href="index.php">Inicio</a></li>
                <li class="<?php if ($current_url === 'red-home') echo 'active-link'; ?>"><a href="red-home.php">Nueva Red</a></li>
                <li class="<?php if ($current_url === 'about') echo 'active-link'; ?>">
                    <a href="about.php">Nosotros</a>
                </li>
                <li class="<?php if (in_array($current_url, ['indic-economico', 'indic-social', 'indic-ambiental', 'indic-tecnologia'])) echo 'active-link'; ?>">
                    <a class="parent-menu collapsed" data-bs-toggle="collapse" href="#collapseMenu1" role="button" aria-expanded="false" aria-controls="collapseMenu1">
                        Indicadores
                    </a>
                    <ul class="submenu collapse" id="collapseMenu1">
                        <li class="<?php if ($current_url === 'indic-economico') echo 'active-link'; ?>"><a href="indic-economico.php">Económico</a></li>
                        <li class="<?php if ($current_url === 'indic-social') echo 'active-link'; ?>"><a href="indic-social.php">Social</a></li>
                        <li class="<?php if ($current_url === 'indic-ambiental') echo 'active-link'; ?>"><a href="indic-ambiental.php">Ambiental</a></li>
                        <li class="<?php if ($current_url === 'indic-tecnologia') echo 'active-link'; ?>"><a href="indic-tecnologia.php">Tecnología e innovación</a></li>
                    </ul>
                </li>
                <li class="<?php if ($current_url === 'estado-observatorio') echo 'active-link'; ?>"><a href="estado-observatorio.php">Estado de datos</a></li>
                <li>
                    <a class="parent-menu collapsed" data-bs-toggle="collapse" href="#collapseMenu2" role="button" aria-expanded="false" aria-controls="collapseMenu2">
                        Boletines
                    </a>
                    <ul class="submenu collapse" id="collapseMenu2">
                        <li><a href="assets/pdf/Boletin 1-04 julio-2023.pdf" target="_blank">Boletín 1</a></li>
                        <li><a href="assets/pdf/Boletin 2-04 julio 2023.pdf" target="_blank">Boletín 2</a></li>
                        <li><a href="assets/pdf/Boletin 3-17 de julio 2023.pdf" target="_blank">Boletín 3</a></li>
                        <li><a href="assets/pdf/Boletin 4-06 julio 2023.pdf" target="_blank">Boletín 4</a></li>
                        <li><a href="assets/pdf/Boletin 5 - 11 julio 2023.pdf" target="_blank">Boletín 5</a></li>
                        <li><a href="assets/pdf/Boletin 6-13 julio 2023.pdf" target="_blank">Boletín 6</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <img src="assets/svg/bg-menu-t.svg" class="bg-menu-top" />
        <img src="assets/svg/bg-menu-b.svg" class="bg-menu-bottom" />
    </div>

    <nav id="navbar-mobile" class="d-block d-md-none navbar navbar-mobile fixed-top">
        <div class="container1" id="nav-icon">
            <button class="ham">
                <div></div>
                <div></div>
                <div></div>
            </button>
        </div>
    </nav>

    <!-- Bootstrap 5 JS necesario aquí al final -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</header>
</body>
