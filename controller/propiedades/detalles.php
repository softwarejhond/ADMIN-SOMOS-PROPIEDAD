<?php
if (isset($_GET['id']) && isset($_GET['tabla'])) {
    $registroId = $_GET['id'];
    $tabla = $_GET['tabla'];
    $registro = obtenerRegistroPorId($tabla, $registroId);
} else {
    header('Location: index.php');
    exit();
}
?>
<div id="mt-3">
    <div class="mt-3 mb-3">
        <div id="dashboard">
            <div class="row">
                <div class="col col-sm-12 col-md-12 col-lg-12">
                    <?php if ($registro) : ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <?php foreach ($registro as $key => $value): ?>
                                    <tr>
                                        <th class="bg-light"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?></th>
                                        <td>
                                            <?php
                                            if ($key == 'fecha' || $key == 'fecha_creacion') {
                                                echo date("d/m/Y", strtotime($value));
                                            } elseif ($key == 'url_foto_principal') { // Cambia 'imagen' al nombre real del campo en tu base de datos
                                                echo '<img src="fotos/' . htmlspecialchars($value) . '" alt="Imagen" style="max-width: 200px; max-height: 200px;">';
                                            } else {
                                                echo htmlspecialchars($value);
                                            }
                                            ?>
                                        </td>
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
                <a href="propiedadesDisponibles.php" class="btn bg-magenta-dark text-white">Volver</a>
            </div>
        </div>
    </div>
</div>
