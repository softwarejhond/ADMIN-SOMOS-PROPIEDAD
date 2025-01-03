<?php
$ipcLocales = mysqli_query($conn, "SELECT * FROM porcentajeAumento WHERE id=2");
while ($ipcActual = mysqli_fetch_array($ipcLocales)) {
    $ipcActuals = $ipcActual['porcentaje'];
}

if (isset($_POST['updateIPC_Locales'])) {
    // Verificar si el campo existe y no está vacío
    if (!empty($_POST['nuevoIPCLocales'])) {
        $porcentajeNuevoLocales = htmlspecialchars($_POST['nuevoIPCLocales'], ENT_QUOTES, 'UTF-8');

        // Preparar y ejecutar la consulta
        $sqlPorcentajeLocales = "UPDATE porcentajeAumento SET porcentaje = ? WHERE id = 2";
        $stmt = $conn->prepare($sqlPorcentajeLocales);
        $stmt->bind_param("s", $porcentajeNuevoLocales);

        if ($stmt->execute()) {
            // Mostrar modal de éxito
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var ipcUpdateLocalesModal = new bootstrap.Modal(document.getElementById("ipcUpdateLocales"));
                    ipcUpdateLocalesModal.show();
                });
            </script>';
        } else {
            // Mostrar modal de error
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var ipcUpdateLocalesErrorModal = new bootstrap.Modal(document.getElementById("ipcUpdateLocalesError"));
                    ipcUpdateLocalesErrorModal.show();
                });
            </script>';
        }
        $stmt->close();
    } else {
        // Campo vacío
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                alert("El campo IPC no puede estar vacío.");
            });
        </script>';
    }
}


?>
<form method="post" class="was-validated">

    <!-- Modal register barrios -->
    <div class="modal fade" id="actualizarIPCLocales" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 1055;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="mocal-header text-white" style="background:#ec008c;">
                    <h6 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-percent"></i> ACTUALIZACIÓN DE IPC DE AUMENTO</h6>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label style="color:#000" class="text-left">IPC actual <i class="fa-solid fa-percent"></i></label><br>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-percent"></i></span>
                        <input type="text" class="form-control text-center" name="nuevoIPCLocales" value="<?php echo $ipcActuals; ?>" maxlength="5" required>
                    </div>
                    <input type="submit" name="updateIPC_Locales" class="btn bg-magenta-dark text-white" value="Actualizar porcentaje">
                    <input type="reset" class="btn bg-indigo-dark text-white" value="Cancelar">
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal satisfactorio -->
<div class="modal fade" id="ipcUpdateLocales" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#ec008c;">
                <h5 class="modal-title">Notificación</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close" onclick="reiniciar()"></button>
            </div>
            <div class="modal-body text-center">
                <h1><i class="fa fa-refresh w-100"></i><br>Porcentaje actualizado con éxito.</h1>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-indigo-dark text-white" data-bs-dismiss="modal" onclick="reiniciar()">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal error -->
<div class="modal fade" id="ipcUpdateLocalesError" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#ec008c;">
                <h5 class="modal-title">Aviso <i class="fa-solid fa-triangle-exclamation"></i></h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close" onclick="reiniciar()"></button>
            </div>
            <div class="modal-body text-center">
                <h1><i class="fa-solid fa-bug"></i><br>Error al actualizar <?php echo htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'); ?></h1>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-indigo-dark text-white" data-bs-dismiss="modal" onclick="reiniciar()">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function reiniciar() {
    window.location.href = window.location.href;
}
</script>
