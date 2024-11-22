<!-- Modal -->
<div class="modal fade" id="exampleModalNuevoReparador" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-magenta-dark">
                <h1 class="modal-title fs-5 text-white" id="exampleModalToggleLabel">
                    <i class="fas fa-plus-circle"></i> Agregar Nuevo Reparador
                </h1>
                <button type="button" class="btn-close bg-gray-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="nuevo-tipo-propiedad">
                    <img src="img/somosLogo.png" alt="logo" width="120px">
                   
                       <?php include './controller/reparador/nuevoReparador.php'; ?>
                   
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn bg-gray-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>