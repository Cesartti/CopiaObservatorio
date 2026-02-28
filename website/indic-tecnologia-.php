<?php
require_once 'tracker.php';
include 'include/header.php';
?>


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
                    <img src="assets/svg/tecnologia-color.svg" alt="" class="img-fluid">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <h2 class="indicador-title">
                    Dimensión Ciencia, Tecnología e Innovación (CTI)
                </h2>
                <div class="indicador-description">
                    <p class="description">
                        Ciencia, Tecnología e Innovación para monitorear los recursos, capacidades, talento joven de la investigación, generación de nuevo conocimiento, infraestructura e innovación en el departamento de Boyacá por medio de la comprensión e intervención de los actores en las diferentes formas de apropiación social del conocimiento y los lineamientos de lapolítica de CTI Nacional y departamental.
                    </p>
                </div>
            </div>
    </section>

    <div class="container-fluid picture-container mt-4">
        <div class="row full-bg full-bg-right" style="background-image: url('assets/pictures/tec.jpg');">
            <div class="picture col-12 col-sm-6 full-text-left">
                <div class="parr-container text-center">
                    <div class="text-center">
                        <h2 class="indicador-title">
                            Categorías
                        </h2>
                        <div class="indicador-separator"></div>
                    </div>
                    <div id="tab" class="indicadores-section-list mt-4">
                        <button id="firstButton" class="active" onclick="openTabStatic(event.currentTarget,'tab1')">Apropiación social</button>
						<div id="tab1" class="tabContent widget-container" style="display: block;">
                            <ul class="icon-list-items">
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=4001">
                                        <span class="icon-list-text">Número de niños, niñas y jóvenes que participan en el programa Ondas</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab2')">Desarrollo tecnológico</button>
						<div id="tab2" class="tabContent widget-container" style="display: none;">
							<ul class="icon-list-items">
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4002">
                                        <span class="icon-list-text">Diseños industriales presentados y concedidos por la oficina de la superintendencia de industria y comercio -sic</span>
                                    </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=4003">
                                        <span class="icon-list-text">Modelos de utilidad presentados y concedidos por la oficina de la superintendencia de industria y comercio –sic</span>
                                    </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=4004">
                                        <span class="icon-list-text">Número de software registrados</span>
                                    </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=4005">
                                        <span class="icon-list-text">Patentes de invención presentadas y concedidas por la oficina de la superintendencia de industria y comercio –sic</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab3')">Formación en CTeI</button>
						<div id="tab3" class="tabContent widget-container" style="display: none;">
							<ul class="icon-list-items">
						        <li class="icon-list-item">
                                    <a href="./indicador.php?id=4006">
                                        <span class="icon-list-text">Jóvenes investigadores minciencia
                                        </span>
                                    </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=4007">
                                        <span class="icon-list-text">Jóvenes investigadores minciencias por área de la ocde</span>
                                    </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=4008">
                                        <span class="icon-list-text">Programas de maestria y doctorado ofrecidos</span>
                                    </a>
                                </li>
                                <li class="icon-list-item">
                                    <a href="./indicador.php?id=4009">
                                        <span class="icon-list-text">Número de graduados de instituciones de educación superior nivel de formación y por área ocde</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab4')">Generación nuevo conocimiento</button>
						<div id="tab4" class="tabContent widget-container" style="display: none;">
							<ul class="icon-list-items">
						        <li class="icon-list-item">
                                    <a href="./indicador.php?id=4010">
                                        <span class="icon-list-text">Producción científica de autores vinculados a instituciones colombianas en Boyacá en base de datos internacionales</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4011">
                                        <span class="icon-list-text">Revistas indexadas en publindex</span>
                                    </a>
                                </li>
							</ul>
                        </div>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab5')">Innovación</button>
						<div id="tab5" class="tabContent widget-container" style="display: none;">
							<ul class="icon-list-items">
						        <li class="icon-list-item">
                                    <a href="./indicador.php?id=4012">
                                        <span class="icon-list-text">Creación de empresas de base tecnológica</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4013">
                                        <span class="icon-list-text">Número de empresas constituidas según jurisdiccion Cámaras de Comercio en Boyacá</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4014">
                                        <span class="icon-list-text">Empresas de manufactura de acuerdo con su grado de innovación</span>
                                    </a>
								</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4015">
                                        <span class="icon-list-text">Número de personas que participó en la realización de actividades conducentes a la innovación, en empresas de manufactura y servicios.</span>
                                    </a>
								</li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4016">
                                        <span class="icon-list-text">Origen de las ideas de innovación en empresas de manufactura</span>
                                    </a>
								</li>
							</ul>
						</div>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab6')">Inversión en actividades de CTeI</button>
						<div id="tab6" class="tabContent widget-container" style="display: none;">
							<ul class="icon-list-items">
						        <li class="icon-list-item">
                                    <a href="./indicador.php?id=4017">
                                        <span class="icon-list-text">Inversión en I+D y ACTI como proporción del PIB nacional</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4018">
                                        <span class="icon-list-text">Inversión en I+D por tipo de entidad</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4019">
                                        <span class="icon-list-text">Inversión en ACTI por tipo de entidad</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4020">
                                        <span class="icon-list-text">Participación de la Inversión en ACTI por tipo de actividad dentro PIB</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4021">
                                        <span class="icon-list-text">Participación de la Inversión en I+D por actividad dentro del PIB</span>
                                    </a>
                                </li>
							</ul>
						</div>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab7')">TIC</button>
						<div id="tab7" class="tabContent widget-container" style="display: none;">
							<ul class="icon-list-items">
						        <li class="icon-list-item">
                                    <a href="./indicador.php?id=4022">
                                        <span class="icon-list-text">Índice de penetración de internet</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4023">
                                        <span class="icon-list-text">Índice de gobierno digital</span>
                                    </a>
                                </li>
						    	
							</ul>
						</div>
						<button class="tabButton" onclick="openTabStatic(event.currentTarget,'tab8')">Capacidades</button>
						<div id="tab8" class="tabContent widget-container" style="display: none;">
							<ul class="icon-list-items">
						        <li class="icon-list-item">
                                    <a href="./indicador.php?id=4027">
                                        <span class="icon-list-text">Grupos de investigación reconocidos por Colciencias</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4029">
                                        <span class="icon-list-text">Investigadores reconocidos por Colciencias</span>
                                    </a>
                                </li>
								<li class="icon-list-item">
                                    <a href="./indicador.php?id=4030">
                                        <span class="icon-list-text">Investigadores activos vinculados a grupos de investigación</span>
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


<?php include  'include/footer.php' ?>
