  <div class="col col-lg-4 col-md-12 col-sm-12 px-2 mt-1 p-3">
      <h2><i class="bi bi-bookmark-plus-fill"></i> Añadir nueva cita</h2>
      <p>Complete este formulario para registrar una cita, tenga en cuenta que todos los campos son
          obligatorios.
      </p>
      <form method="post" class="was-validated"
          action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <div class="form-group">
              <label for="tipoCita">Tipo de cita:</label>
              <select class="form-select mb-3 " id="tipoCita"
                  name="tipoCita" required>
                  <option value="">SELECCIONAR</option>
                  <option value="RECIBIR">RECIBIR</option>
                  <option value="ENTREGAR">ENTREGAR</option>
                  <option value="MOSTRAR">MOSTRAR</option>
                  <option value="REPARACIONES">REPARACIONES</option>
              </select>
          </div>
          <div class="form-group">
              <label for="nombre">Código de propiedad:</label>
              <input type="text" class="form-control is-invalid" id="propiedad"
                  name="propiedad" placeholder="Ingrese el codigo de la propiedad"
                  required>
          </div>
          <div class="form-group">
              <label>Nombre:</label>
              <input type="text" class="form-control is-invalid" name="nombre"
                  placeholder="Ingrese su nombre" required>
          </div>
          <div class="form-group">
              <label>Teléfono:</label>
              <input type="tel" class="form-control is-invalid"
                  placeholder="Ingrese su teléfono" name="telefono" minlength="7"
                  maxlength="15" required>
          </div>
          <div class="form-group">
              <label for="fecha">Fecha:</label>
              <input type="date" class="form-control is-invalid" id="fecha" name="fecha"
                  placeholder="Ingrese la fecha de la cita" required>
          </div>
          <div class="form-group">
              <label for="hora">Hora:</label>
              <input type="time" class="form-control is-invalid" id="hora" name="hora"
                  placeholder="Ingrese la hora de la cita" required>
          </div>
          <br>
          <input type="submit" class="btn bg-magenta-dark text-white" value="Programar">
          <input type="reset" class="btn bg-indigo-dark text-white" value="Cancelar">
      </form>
  </div>