<?php

if (isset($_POST['agregar'])) {
    $tipo = trim($_POST['tipo']);
    list($mensaje, $tipo_mensaje) = agregarNuevoTipoDePropiedad($tipo);
}
?>
<!-- Toast de Bootstrap con color dinámico -->
<!-- Toast de Bootstrap con color dinámico -->
<div class="toast-container top-0 bottom-0 end-0 p-3">
    <div id="liveToasts"
        class="toast <?php echo isset($tipo_mensaje) && $tipo_mensaje === 'success' ? 'bg-lime-light' : 'bg-amber-light'; ?>"
        role="alert" aria-live="assertive" aria-atomic="true"
        style="display: <?php echo !empty($mensaje) ? 'block' : 'none'; ?>;">

        <div class="toast-header">
            <strong class="me-auto">
                <i class="bi bi-exclamation-square-fill"></i>
                <?php echo isset($tipo_mensaje) && $tipo_mensaje === 'success' ? 'Éxito' : 'Error'; ?>
            </strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>

        <div class="toast-body">
            <?php echo htmlspecialchars($mensaje ?? ''); ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-magenta-dark ">
                <h1 class="modal-title fs-5 text-white" id="exampleModalToggleLabel">
                    <i class="fas fa-plus-circle"></i> Agregar Nuevo Tipo de Propiedad
                </h1>
                <button type="button" class="btn-close bg-gray-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="nuevo-tipo-propiedad">
                    <img src="img/somosLogo.png" alt="logo" width="120px">
                    <div class="box-nuevo-tipo">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo de Propiedad</label>
                                <input type="text" name="tipo" id="tipo" class="form-control" placeholder="Ejemplo: Apartamento" required>
                            </div>
                            <button type="submit" name="agregar" class="btn bg-magenta-dark text-white w-100"><i class="bi bi-floppy2-fill"></i> Agregar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn bg-gray-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php if (isset($_POST['agregar'])) : ?>
            const toastElement = document.getElementById("liveToasts");
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        <?php endif; ?>
    });
</script>