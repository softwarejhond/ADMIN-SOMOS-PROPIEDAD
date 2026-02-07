<header class="bg-white text-black">
    <div class="">
        <div class="row align-items-center">
            <div class="col-auto">
                <button class="btn bg-indigo-dark text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="col text-center">
                <h3 class="mb-0">Arrendatarios</h3>
            </div>
            <div class="col-auto">
                <a href="cerrar-sesion.php" class="btn bg-indigo-dark text-white">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Offcanvas Menu -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header bg-indigo-dark text-white">
        <h5 class="offcanvas-title" id="offcanvasMenuLabel">
            <i class="fas fa-user-circle me-2"></i>Menú Principal
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <nav class="nav flex-column">
            <a class="nav-link d-flex align-items-center py-2 px-4 border-bottom" href="#">
                <i class="fas fa-users me-3 text-indigo-dark"></i>
                <span class="text-indigo-dark">Gestionar Arrendatarios</span>
            </a>
            <a class="nav-link d-flex align-items-center py-2 px-4 border-bottom" href="#">
                <i class="fas fa-plus-circle me-3 text-indigo-dark"></i>
                <span class="text-indigo-dark">Nuevo Arrendatario</span>
            </a>
            <a class="nav-link d-flex align-items-center py-2 px-4 border-bottom" href="#">
                <i class="fas fa-building me-3 text-indigo-dark"></i>
                <span class="text-indigo-dark">Propiedades</span>
            </a>
        </nav>
    </div>
    <div class="offcanvas-footer border-top p-3 bg-light">
        <small class="text-muted d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            Sistema de Gestión v1.0
        </small>
    </div>
</div>