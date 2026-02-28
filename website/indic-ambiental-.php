<?php include  'include/header.php' ?>

<link rel="stylesheet" type="text/css" href="assets/css/tabs.css">
<script type="text/javascript" src="assets/js/tabs.js"></script>

<div id="main-body" class="main-body">
    <section class="container-fluid section-indicador">
		<section class="section-indicador-top">
			<br/>
		</section>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="indicador-icon">
                    <img src="assets/svg/ambiental-color.svg" alt="" class="img-fluid">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <h2 class="indicador-title">
                    Dimensión ambiental
                </h2>
                <div class="indicador-description">
                    <p class="description">
                        Proporcionar información de indicadores relevantes que muestren análisis pertinentes de las diferentes categorías ambientales, con el fin de tomar decisiones y salvaguardar los recursos naturales y garantizar un desarrollo sostenible y sustentable en el departamento de Boyacá.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid picture-container mt-4">
        <div class="row full-bg full-bg-right" style="background-image: url('assets/pictures/ambiental.jpg');">
            <div class="picture col-12 col-sm-6 full-text-left">
                <div class="parr-container text-center">
                    <div class="text-center">
                        <h2 class="indicador-title">
                            Categorías
                        </h2>
                        <div class="indicador-separator"></div>
                    </div>
                    <div id="tab" class="indicadores-section-list mt-4">
                        <button id="firstButton" class="active" onclick="openTabStatic(event.currentTarget,'tab1')">Servicios públicos</button>
						<div id="tab1" class="tabContent widget-container" style="display: block;">
                            <ul class="icon-list-items">
                                <li class="indicador-subtitle">Acueducto</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3002">
                                        <span class="icon-list-text">Servicio de acueducto urbano</span>
                                    </a>
                                </li>
                                <li class="indicador-subtitle">Alcantarillado</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3003">
                                        <span class="icon-list-text">Sistema de alcantarillado zona urbana</span>
                                    </a>
                                </li>
                                <li class="indicador-subtitle">Aseo</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3004">
                                        <span class="icon-list-text">Aseo en la zona urbana</span>
                                    </a>
                                </li>
								<li class="indicador-subtitle">Hogares</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3001">
                                        <span class="icon-list-text">Porcentaje de edificaciones culminadas que implementaron sistemas de ahorro de energía</span>
                                    </a>
                                </li>
								<li class="indicador-subtitle">Servicios cubiertos hogares</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3008">
                                            <span class="icon-list-text">Porcentaje de tratamiento de aguas residuales urbano</span>
                                     </a>
                                </li>
                            </ul>
                        </div>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab2')">Recursos naturales</button>
						<div id="tab2" class="tabContent widget-container" style="display: none;">
                            <ul class="icon-list-items">
                                <li class="indicador-subtitle">Agua</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3005">
                                            <span class="icon-list-text">Continuidad del servicio De acueducto en horas zona urbana</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3007">
                                            <span class="icon-list-text">Continuidad del servicio De acueducto promedio días por semana zona urbana</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3006">
                                            <span class="icon-list-text">Índice de riesgo de calidad de agua para el consumo humano</span>
                                     </a>
                                </li>
                                <li class="indicador-subtitle">Aire</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3017">
                                            <span class="icon-list-text">Nivel de la calidad del aíre Nobsa - Material particulado PM-10</span>
                                     </a>
                                </li>    
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3018">
                                            <span class="icon-list-text">Calidad del aire Nobsa - PM-2.5 </span>
                                     </a>
                                </li> 
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3019">
                                            <span class="icon-list-text">Nivel de dióxido de azufre en Nobsa SO2</span>
                                     </a>
                                </li> 
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3020">
                                            <span class="icon-list-text">Calidad del aire Nobsa - Ozono O3</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3021">
                                            <span class="icon-list-text">Calidad del aire Nazareth Nobsa PM-10</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3022">
                                            <span class="icon-list-text">Calidad del aire Nazareth Nobsa - PM-2.5</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3023">
                                            <span class="icon-list-text">Calidad del aire Nazareth Nobsa - SO2</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3024">
                                            <span class="icon-list-text">Calidad del aire Nazareth Nobsa - O3</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3025">
                                            <span class="icon-list-text">Calidad del aire en PM-10 en la zona de Paipa</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3026">
                                            <span class="icon-list-text">Calidad del aire Paipa SO2</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3027">
                                            <span class="icon-list-text">Calidad del aire en PM-10 en la zona de Ráquira</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3028">
                                            <span class="icon-list-text">Calidad del aire en PM-2.5 en la zona de Ráquira</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3029">
                                            <span class="icon-list-text">Calidad del aire en PM-10 zona urbana de Ráquira</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3030">
                                            <span class="icon-list-text">Calidad del aire en PM-2.5 zona urbana de Ráquira</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3031">
                                            <span class="icon-list-text">Calidad del aire en SO2 zona urbana de Ráquira</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3032">
                                            <span class="icon-list-text">Calidad del aire en PM-10 zona Sogamoso - Móvil Koika</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3033">
                                            <span class="icon-list-text">Calidad del aire en PM-2.5 zona Sogamoso - Móvil Koika</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3034">
                                            <span class="icon-list-text">Calidad del aire en SO2 zona Sogamoso - Móvil Koika</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3035">
                                            <span class="icon-list-text">Calidad del aire en O3 zona Sogamoso - Móvil Koika</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3036">
                                            <span class="icon-list-text">Calidad del aire en NO2 zona Sogamoso - Móvil Koika</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3037">
                                            <span class="icon-list-text">Calidad del aire en CO zona Sogamoso - Móvil Koika</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3038">
                                            <span class="icon-list-text">Calidad del aire en PM-10 zona Sogamoso Recreo</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3039">
                                            <span class="icon-list-text">Calidad del aire en PM-2.5 zona Sogamoso Recreo</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3040">
                                            <span class="icon-list-text">Calidad del aire en SO2 zona Sogamoso Recreo</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3041">
                                            <span class="icon-list-text">Calidad del aire en O3 zona Sogamoso Recreo</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3042">
                                            <span class="icon-list-text">Calidad del aire en NO2 zona Sogamoso Recreo</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3043">
                                            <span class="icon-list-text">Calidad del aire en CO zona Sogamoso Recreo</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3044">
                                            <span class="icon-list-text">Calidad del aire en PM-10 zona Sogamoso SENA</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3045">
                                            <span class="icon-list-text">Calidad del aire en SO2 zona Sogamoso SENA</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3046">
                                            <span class="icon-list-text">Calidad del aire en O3 zona Sogamoso SENA</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3047">
                                            <span class="icon-list-text">Calidad del aire en PM-10 zona UPTC</span>
                                     </a>
                                </li>
                            </ul>
                        </div>
						<h2 class="indicador-title">Categoría especial</h2>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab3')">Hechos y derechos</button>
						<div id="tab3" class="tabContent widget-container" style="display: none;">
                            <ul class="icon-list-items">
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3002">
                                        <span class="icon-list-text">Servicio de acueducto urbano</span>
                                    </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3003">
                                        <span class="icon-list-text">Sistema de alcantarillado zona urbana</span>
                                    </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3004">
                                        <span class="icon-list-text">Aseo en la zona urbana</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3005">
                                            <span class="icon-list-text">Continuidad del servicio De acueducto en horas zona urbana</span>
                                     </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=3007">
                                            <span class="icon-list-text">Continuidad del servicio De acueducto promedio días por semana zona urbana</span>
                                     </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=3008">
                                            <span class="icon-list-text">Porcentaje de tratamiento de aguas residuales urbano</span>
                                     </a>
                                </li>
							</ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include  'include/footer.php' ?>
