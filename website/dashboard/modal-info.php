<!-- Modal Automático -->
<div class="modal fade" id="modalInfoUsuario" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="formInfoUsuario" method="POST" action="/guardar-info.php">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Queremos conocerte mejor</h5>
        </div>

        <div class="modal-body">

          <p>Por favor selecciona tu perfil y edad. Esto nos ayuda a mejorar la plataforma.</p>

          <!-- Profesión -->
          <label><b>¿Cuál es tu rol?</b></label>
          <select class="form-control" name="profesion" required>
            <option value="">Selecciona...</option>
            <option value="Docente universitario">Docente universitario</option>
            <option value="Docente de colegio">Docente de colegio</option>
            <option value="Estudiante universitario">Estudiante universitario</option>
            <option value="Estudiante de colegio">Estudiante de colegio</option>
            <option value="Servidor público">Servidor público</option>
            <option value="ONG">ONG</option>
            <option value="Sociedad civil">Sociedad civil</option>
            <option value="Otro">Otro</option>
          </select>

          <br>

          <!-- Edad -->
          <label><b>Edad</b></label>
          <input type="number" class="form-control" name="edad" min="11" max="90" required>

          <br>

          <!-- Otro -->
          <label><b>Si seleccionaste "Otro", escribe aquí:</b></label>
          <input type="text" class="form-control" name="otro" placeholder="Opcional">

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>

<script>
// Mostrar modal solo una vez
document.addEventListener("DOMContentLoaded", () => {
    if (!localStorage.getItem("infoUsuarioRegistrada")) {
        setTimeout(() => {
            $('#modalInfoUsuario').modal('show');
        }, 800);
    }
});
</script>
