<?php
    include("vistaBase.php");
        if (isset($_GET['id']) && isset($_GET['tabla'])) {
        $registroId = $_GET['id'];
        $tabla = $_GET['tabla'];
        $registro = obtenerRegistroPorId($tabla, $registroId);
        } else {
        header('Location: index.php');
        exit();
        }

        echo generarHead("Detalle del registro");
        echo generarHeader();

    ?>
    <div id="mt-3">
            <div class="mt-3 mb-3">
                <br><br>
                <div id="dashboard">
                    <div class="position-relative">
                        <h2 class="position-absolute top-0 start-0 "><i class="bi bi-info-circle-fill"></i> Detalles del Registro</h2>
                    </div>
                    <h6 class="text-aling-rigth"></h6>
                    <hr>
                    <div class="row">
                        <div class="col col-sm-12 col-md-12 col-lg-12">
                            <?php if ($registro) : ?>
                                 <div class="table-responsive">
                                    <table class="table table-bordered">
                                    <?php foreach ($registro as $key => $value): ?>
                                            <tr>
                                                <th class="bg-light"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?></th>
                                                <td><?php 
                                                
                                                        if ($key == 'fecha' || $key == 'fecha_creacion'){
                                                            echo date("d/m/Y", strtotime($value));
                                                        }else{
                                                            echo htmlspecialchars($value);
                                                        }
                                                
                                                    ?></td>
                                            </tr>
                                    <?php endforeach; ?>
                                    </table>
                                 </div>
                            <?php else: ?>
                                <p>Registro no encontrado.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-3">
                         <a href="propiedadesDisponibles.php" class="btn btn-secondary">Volver</a>
                    </div>
                 </div>
            </div>
    </div>
    <?php
        echo generarFooter();
    ?>