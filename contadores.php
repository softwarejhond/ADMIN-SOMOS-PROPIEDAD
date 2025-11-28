

<div class="row">
    <!-- Tarjeta Total de Usuarios -->
    <div class="col-sm-12 col-lg-3 col-md-6 mb-3 mb-sm-0 mb-md-1">
        <div class="card bg-amber-light text-dark shadow">
            <div class="card-body d-flex align-items-center">
                <div class="icon-container me-3">
                    <i class="fas fa-users fa-3x text-gray-dark"></i>
                </div>
                <div class="text-container">
                    <h5 class="card-title">Usuarios verificados</h5>
                    <h2><span id="total_usuarios"><?php echo $total_usuarios ?></span></h2>
                    <a href="listado-usuarios.php" class="btn btn-light btn-sm">Ver detalles</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta Usuarios Masculinos -->
    <div class="col-sm-12 col-lg-3 col-md-6 mb-3 mb-sm-0 mb-md-1">
        <div class="card bg-indigo-light text-dark shadow">
            <div class="card-body d-flex align-items-center">
                <div class="icon-container me-3">
                    <i class="fas fa-male fa-3x text-gray-dark"></i>
                </div>
                <div class="text-container">
                    <h5 class="card-title">Usuarios Masculinos</h5>
                    <h2><span id="masculinos">0</span> | <span id="porc_masculinos">0</span>%</h2>
                    <a href="listado-masculinos.php" class="btn btn-light btn-sm">Ver detalles</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta Usuarios Femeninos -->
    <div class="col-sm-12 col-lg-3 col-md-6 mb-3 mb-sm-0 mb-md-1">
        <div class="card bg-teal-light text-dark shadow">
            <div class="card-body d-flex align-items-center">
                <div class="icon-container me-3">
                    <i class="fas fa-female fa-3x text-gray-dark"></i>
                </div>
                <div class="text-container">
                    <h5 class="card-title">Usuarios Femeninos</h5>
                    <h2><span id="femeninos">0</span> | <span id="porc_femeninos">0</span>%</h2>
                    <a href="listado-femeninos.php" class="btn btn-light btn-sm">Ver detalles</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta Usuarios con Programa -->
    <div class="col-sm-12 col-lg-3 col-md-6 mb-3 mb-sm-0 mb-md-1">
        <div class="card bg-magenta-light text-white shadow">
            <div class="card-body d-flex align-items-center">
                <div class="icon-container me-3">
                    <i class="fas fa-graduation-cap fa-3x text-gray-dark"></i>
                </div>
                <div class="text-container">
                    <h5 class="card-title">Usuarios con Programa</h5>
                    <h2><span id="con_programa">0</span> | <span id="porc_con_programa">0</span>%</h2>
                    <a href="listado-programas.php" class="btn btn-light btn-sm">Ver detalles</a>
                </div>
            </div>
        </div>
    </div>
</div>
