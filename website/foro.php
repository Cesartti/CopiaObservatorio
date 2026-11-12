<?php
/**
 * Formulario de aportes del Primer Foro Anual de la Red de Observatorios
 * (Universidad de los Llanos, 12 de noviembre de 2026).
 *
 * Página suelta y ligera a propósito: se abre desde un código QR proyectado en
 * el auditorio, con la red del campus y en el teléfono de cada quien. No carga
 * el encabezado del portal ni librerías externas; todo va en este archivo.
 */
$titulo = 'Aportes · Primer Foro Anual de la Red de Observatorios';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($titulo) ?></title>
<meta name="description" content="Formulario de aportes de los asistentes al Primer Foro Anual de la Red de Observatorios.">
<style>
  :root{
    --tinta:#0B2744; --verde:#14494C; --rojo:#C8102E;
    --gris:#5D6B80; --linea:#DBE3EC; --fondo:#F4F7FA;
  }
  *{box-sizing:border-box}
  body{margin:0;font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
       background:var(--fondo);color:var(--tinta);line-height:1.5}
  .cab{background:var(--verde);color:#fff;padding:26px 20px 30px}
  .caja{max-width:720px;margin:0 auto;padding:0 16px}
  .cab h1{margin:0 0 6px;font-size:1.45rem;letter-spacing:-.01em}
  .cab p{margin:0;opacity:.85;font-size:.95rem}
  .et{display:inline-block;background:var(--rojo);color:#fff;font-size:.72rem;
      font-weight:700;letter-spacing:.06em;padding:4px 10px;border-radius:3px;margin-bottom:12px}
  main{max-width:720px;margin:-18px auto 0;padding:0 16px 60px}
  .tarjeta{background:#fff;border:1px solid var(--linea);border-radius:12px;
           padding:22px;margin-bottom:16px;box-shadow:0 1px 3px rgba(11,39,68,.06)}
  .intro{font-size:.95rem;color:var(--gris);margin:0 0 4px}
  label{display:block;font-weight:600;margin:0 0 6px;font-size:.95rem}
  .ayuda{font-weight:400;color:var(--gris);font-size:.85rem;display:block;margin-top:2px}
  input[type=text],input[type=email],select,textarea{
    width:100%;padding:10px 12px;border:1px solid var(--linea);border-radius:8px;
    font:inherit;color:inherit;background:#fff}
  textarea{min-height:92px;resize:vertical}
  input:focus,select:focus,textarea:focus{outline:2px solid var(--verde);outline-offset:1px;border-color:var(--verde)}
  .campo{margin-bottom:18px}
  .campo:last-child{margin-bottom:0}
  .ops{display:grid;gap:8px}
  .op{display:flex;gap:10px;align-items:flex-start;border:1px solid var(--linea);
      border-radius:8px;padding:10px 12px;cursor:pointer;background:#fff}
  .op:hover{border-color:var(--verde)}
  .op input{margin:3px 0 0;flex:0 0 auto}
  .op span{font-size:.93rem}
  .fila{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  @media(max-width:560px){.fila{grid-template-columns:1fr}}
  button{background:var(--verde);color:#fff;border:0;border-radius:8px;
         padding:14px 22px;font:inherit;font-weight:700;cursor:pointer;width:100%}
  button:hover{background:#0f3a3c}
  button[disabled]{opacity:.6;cursor:default}
  .aviso{font-size:.85rem;color:var(--gris);margin-top:14px}
  .ok{display:none;text-align:center;padding:34px 20px}
  .ok h2{color:var(--verde);margin:0 0 10px}
  .err{display:none;background:#FDECEA;border:1px solid #F5C2BD;color:#8A1C13;
       border-radius:8px;padding:12px;margin-bottom:14px;font-size:.9rem}
  .pie{text-align:center;font-size:.82rem;color:var(--gris);padding:0 16px 40px}
  .pie a{color:var(--verde)}
</style>
</head>
<body>

<header class="cab">
  <div class="caja">
    <span class="et">12 DE NOVIEMBRE DE 2026</span>
    <h1>Primer Foro Anual de la Red de Observatorios</h1>
    <p>Contextualización del territorio a través de los observatorios · Universidad de los Llanos</p>
  </div>
</header>

<main>
  <div class="tarjeta">
    <p class="intro">
      Lo que usted escriba aquí alimenta un artículo que la Red va a publicar después
      del foro. Son seis preguntas y toma dos minutos. Puede dejar en blanco las que
      no quiera responder.
    </p>
  </div>

  <form id="f" novalidate>
    <div class="tarjeta">
      <div class="err" id="err"></div>

      <div class="fila">
        <div class="campo">
          <label for="institucion">Institución u observatorio</label>
          <input type="text" id="institucion" maxlength="160" autocomplete="organization">
        </div>
        <div class="campo">
          <label for="territorio">Territorio donde trabaja
            <span class="ayuda">Municipio, departamento o región</span></label>
          <input type="text" id="territorio" maxlength="120">
        </div>
      </div>

      <div class="campo">
        <label for="rol">¿Desde dónde participa?</label>
        <select id="rol">
          <option value="">Seleccione…</option>
          <option value="academia">Academia o investigación</option>
          <option value="entidad-publica">Entidad pública</option>
          <option value="sector-productivo">Sector productivo</option>
          <option value="organizacion-social">Organización social o comunitaria</option>
          <option value="estudiante">Estudiante</option>
          <option value="otro">Otro</option>
        </select>
      </div>
    </div>

    <div class="tarjeta">
      <div class="campo">
        <label for="falta_info">¿Qué información de su territorio necesita y hoy no encuentra?
          <span class="ayuda">Es la pregunta que más nos sirve. Sea concreto.</span></label>
        <textarea id="falta_info" maxlength="1200"></textarea>
      </div>

      <div class="campo">
        <label for="barrera">¿Cuál es la principal barrera para usar datos del territorio?</label>
        <select id="barrera">
          <option value="">Seleccione…</option>
          <option value="no-existen">El dato no existe</option>
          <option value="dispersos">Está disperso en muchas entidades</option>
          <option value="desactualizados">Está desactualizado</option>
          <option value="no-comparables">No es comparable entre municipios o años</option>
          <option value="no-se-donde">No sé dónde buscarlo</option>
          <option value="otra">Otra</option>
        </select>
      </div>

      <div class="campo">
        <label>¿Qué debería priorizar una red de observatorios?
          <span class="ayuda">Puede marcar varias</span></label>
        <div class="ops">
          <label class="op"><input type="checkbox" name="pr" value="estandarizar"><span>Estandarizar indicadores entre observatorios</span></label>
          <label class="op"><input type="checkbox" name="pr" value="datos-abiertos"><span>Publicar los datos abiertos y descargables</span></label>
          <label class="op"><input type="checkbox" name="pr" value="formacion"><span>Formar a quienes usan los datos</span></label>
          <label class="op"><input type="checkbox" name="pr" value="articulacion"><span>Articularse con las entidades que producen el dato</span></label>
          <label class="op"><input type="checkbox" name="pr" value="publicacion"><span>Publicar con periodicidad fija</span></label>
          <label class="op"><input type="checkbox" name="pr" value="visualizacion"><span>Mejorar la visualización y los tableros</span></label>
        </div>
      </div>

      <div class="campo">
        <label for="aporte">Su aporte para el artículo
          <span class="ayuda">Una idea, una experiencia, una advertencia. Lo que quiera dejar.</span></label>
        <textarea id="aporte" maxlength="1800"></textarea>
      </div>
    </div>

    <div class="tarjeta">
      <div class="campo">
        <label>¿Podemos usar su aporte en el artículo?</label>
        <div class="ops">
          <label class="op"><input type="radio" name="cons" value="anonimo" checked><span>Sí, de forma anónima</span></label>
          <label class="op"><input type="radio" name="cons" value="con-credito"><span>Sí, y pueden citar mi nombre</span></label>
          <label class="op"><input type="radio" name="cons" value="no"><span>No, es solo para ustedes</span></label>
        </div>
      </div>

      <div class="fila">
        <div class="campo" id="campoNombre" style="display:none">
          <label for="nombre">Nombre para la cita</label>
          <input type="text" id="nombre" maxlength="120" autocomplete="name">
        </div>
        <div class="campo">
          <label for="correo">Correo <span class="ayuda">Opcional. Solo para enviarle el artículo.</span></label>
          <input type="email" id="correo" maxlength="160" autocomplete="email">
        </div>
      </div>

      <button type="submit" id="btn">Enviar mi aporte</button>
      <p class="aviso">
        No se recogen más datos que los que usted escriba. El correo, si lo deja,
        se usa únicamente para enviarle la publicación.
      </p>
    </div>
  </form>

  <div class="tarjeta ok" id="ok">
    <h2>Gracias</h2>
    <p>Su aporte quedó registrado y entra al material del artículo.</p>
    <p class="intro">
      Mientras tanto puede explorar los cinco observatorios de Boyacá en
      <a href="https://observatorios.boyaca.gov.co">observatorios.boyaca.gov.co</a>
    </p>
  </div>
</main>

<p class="pie">
  Red de Observatorios de Boyacá · Secretaría de Planeación<br>
  <a href="https://observatorios.boyaca.gov.co">observatorios.boyaca.gov.co</a>
</p>

<script>
(function () {
  var f = document.getElementById('f');
  var btn = document.getElementById('btn');
  var err = document.getElementById('err');
  var campoNombre = document.getElementById('campoNombre');

  // El nombre solo se pide a quien acepta que lo citen.
  f.addEventListener('change', function (e) {
    if (e.target.name === 'cons') {
      campoNombre.style.display = e.target.value === 'con-credito' ? '' : 'none';
    }
  });

  function val(id) { return (document.getElementById(id).value || '').trim(); }

  f.addEventListener('submit', function (e) {
    e.preventDefault();
    err.style.display = 'none';

    var prioridades = Array.prototype.slice
      .call(document.querySelectorAll('input[name=pr]:checked'))
      .map(function (c) { return c.value; });
    var cons = (document.querySelector('input[name=cons]:checked') || {}).value || 'no';

    var cuerpo = {
      evento: 'foro-unillanos-2026',
      institucion: val('institucion'),
      territorio: val('territorio'),
      rol: val('rol'),
      falta_info: val('falta_info'),
      barrera: val('barrera'),
      prioridades: prioridades,
      aporte: val('aporte'),
      consentimiento: cons,
      nombre: val('nombre'),
      correo: val('correo')
    };

    if (!cuerpo.falta_info && !cuerpo.aporte && !prioridades.length && !cuerpo.barrera) {
      err.textContent = 'Responda al menos una pregunta antes de enviar.';
      err.style.display = 'block';
      return;
    }

    btn.disabled = true;
    btn.textContent = 'Enviando…';

    fetch('api/foro.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(cuerpo)
    }).then(function (r) {
      return r.json().catch(function () { return { ok: false }; });
    }).then(function (j) {
      if (j && j.ok) {
        f.style.display = 'none';
        document.getElementById('ok').style.display = 'block';
        window.scrollTo(0, 0);
      } else {
        throw new Error((j && j.error) || 'No se pudo enviar');
      }
    }).catch(function (ex) {
      err.textContent = ex.message + '. Intente de nuevo.';
      err.style.display = 'block';
      btn.disabled = false;
      btn.textContent = 'Enviar mi aporte';
    });
  });
})();
</script>
</body>
</html>
