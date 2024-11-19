<div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel"><i class="bi bi-boxes"></i> SIVP Aplicaciones</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="container-fluid">
            <fieldset class="checkbox-group">
                <legend class="checkbox-group-legend">
                    <?php
                    foreach ($empresas as $empresa) {
                        //echo '<label class="card-text">' . $empresa['nombre'] . '</label><br>'; // Mostrar el nombre
                        echo '<img src="img/' . $empresa['logo'] . '" alt="Logo de ' . $empresa['nombre'] . '" width="100px" >'; // Mostrar el logo
                    }
                    ?>
                </legend>
                <div class="checkbox">
                    <label class="checkbox-wrapper" data-bs-target="#exampleModalNuevoAdmin" data-bs-toggle="modal">

                        <span class="checkbox-tile">
                            <span class="checkbox-icon">
                                <i class="bi bi-person-add icono"></i>
                            </span>
                            <span class="checkbox-label">Añadir</span>
                        </span>
                    </label>
                </div>
                <div class="checkbox">
                    <label class="checkbox-wrapper">
                        <span class="checkbox-tile">
                            <span class="checkbox-icon">
                                <i class="bi bi-bug icono"></i>
                            </span>
                            <span class="checkbox-label ">Añadir</span>
                        </span>
                    </label>
                </div>
                <div class="checkbox">
                    <label class="checkbox-wrapper">
                        <span class="checkbox-tile">
                            <span class="checkbox-icon">
                                <i class="bi bi-tools icono"></i>
                            </span>
                            <span class="checkbox-label">Añadir</span>
                        </span>
                    </label>
                </div>
                <div class="checkbox">

                    <label class="checkbox-wrapper">
                        <span class="checkbox-tile">
                            <span class="checkbox-icon">
                                <i class="bi bi-pencil icono"></i>
                            </span>
                            <span class="checkbox-label">Registrar</span>
                        </span>
                    </label>

                </div>
                <div class="checkbox">

                    <a href="carusel.php">
                        <label class="checkbox-wrapper">
                            <span class="checkbox-tile">
                                <span class="checkbox-icon">
                                    <i class="bi bi-images icono"></i>
                                </span>
                                <span class="checkbox-label ">Carusel</span>
                            </span>
                        </label>
                    </a>
                </div>
                <div class="checkbox">
                    <a href="registrarRetiro.php">
                        <label class="checkbox-wrapper">
                            <span class="checkbox-tile">
                                <span class="checkbox-icon">
                                    <i class="bi bi-door-open icono"></i>
                                </span>
                                <span class="checkbox-label">Retiros</span>
                            </span>
                        </label>
                    </a>
                </div>
                <div class="checkbox">
                    <a href="perfil.php"> <label class="checkbox-wrapper">
                            <span class="checkbox-tile">
                                <span class="checkbox-icon">
                                    <i class="bi bi-person-circle icono"></i>
                                </span>
                                <span class="checkbox-label">Perfil</span>
                            </span>
                        </label>
                    </a>
                </div>
                <div class="checkbox">
                    <a href="actualizar-smtp.php"><label class="checkbox-wrapper">
                            <span class="checkbox-tile">
                                <span class="checkbox-icon">
                                    <i class="bi bi-gear-wide-connected icono"></i>
                                </span>
                                <span class="checkbox-label">Configuración</span>
                            </span>
                        </label>
                    </a>
                </div>
                <div class="checkbox">
                    <label class="checkbox-wrapper" data-bs-target="#exampleModalToggle" data-bs-toggle="modal">
                        <span class="checkbox-tile">
                            <span class="checkbox-icon">
                                <i class="bi bi-database-fill-add icono"></i>
                            </span>
                            <span class="checkbox-label">Añadir</span>
                        </span>
                    </label>
                </div>
                <div class="checkbox">
                    <a href="https://agenciaeaglesoftware.com/" target="_blank">
                        <label class="checkbox-wrapper">
                            <span class="checkbox-tile">
                                <span class="checkbox-icon">
                                    <img src="img/icons/eagle.png" alt="LogoEagle" width="60px">
                                </span>
                            </span>
                        </label>
                    </a>

                </div>
            </fieldset>
        </div>
    </div>
    <?php include("footer.php"); ?>
</div>