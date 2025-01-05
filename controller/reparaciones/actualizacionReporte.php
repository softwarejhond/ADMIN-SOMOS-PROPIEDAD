<?php
// Validar la conexión a la base de datos
if (!isset($conn)) {
    die('<div class="alert alert-danger">Error: Conexión a la base de datos no establecida.</div>');
}

// Validar la existencia del parámetro 'niks'
$niks = $_GET['niks'] ?? null;
if ($niks !== null) {
    $niks = mysqli_real_escape_string($conn, strip_tags($niks));
} else {
    die('<div class="alert alert-danger">Error: Parámetro inválido.</div>');
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdateReparacion'])) {
    // Recibir y sanitizar los datos del formulario
    $codigoReporte = mysqli_real_escape_string($conn, strip_tags($_POST["codigoReporte"] ?? ''));
    $fechaActualizacion = mysqli_real_escape_string($conn, strip_tags($_POST["fechaActualizacion"] ?? ''));
    $codigo_propietario = mysqli_real_escape_string($conn, strip_tags($_POST["codigo_propietario"] ?? ''));
    $valorFactura = mysqli_real_escape_string($conn, strip_tags($_POST["valorFactura"] ?? ''));
    $valorServicio = mysqli_real_escape_string($conn, strip_tags($_POST["valorServicio"] ?? ''));
    $totalPagar = mysqli_real_escape_string($conn, strip_tags($_POST["totalPagar"] ?? ''));
    $situacionReportada = mysqli_real_escape_string($conn, strip_tags($_POST["situacionReportada"] ?? ''));
    $solucion = mysqli_real_escape_string($conn, strip_tags($_POST["solucion"] ?? ''));
    $EstadoReporte = mysqli_real_escape_string($conn, strip_tags($_POST["EstadoReporte"] ?? ''));
    $id_reparador = mysqli_real_escape_string($conn, strip_tags($_POST["id_reparador"] ?? ''));

    // Manejo del archivo subido
    $directorioDestino = 'fotosReportes/soluciones/';
    $fotoSolucion = null;

    if (isset($_FILES['fotoSolucion']['name']) && $_FILES['fotoSolucion']['name'] !== '') {
        $nombreArchivo = basename($_FILES['fotoSolucion']['name']);
        $rutaTemporal = $_FILES['fotoSolucion']['tmp_name'];
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        // Crear el directorio si no existe
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        // Subir el archivo
        if (move_uploaded_file($rutaTemporal, $rutaCompleta)) {
            $fotoSolucion = $rutaCompleta;
        } else {
            echo '<div class="alert alert-danger">Error: No se pudo subir la foto de la solución.</div>';
        }
    }

    // Actualizar los datos en la base de datos
    $update = mysqli_query($conn, "
        UPDATE report SET 
            codigoReporte='$codigoReporte',
            codigo_propietario='$codigo_propietario', 
            valorFactura='$valorFactura', 
            valorServicio='$valorServicio',
            totalPagar='$totalPagar', 
            situacionReportada='$situacionReportada',
            solucion='$solucion',
            fotoSolucion='$fotoSolucion', 
            EstadoReporte='$EstadoReporte', 
            id_reparador='$id_reparador',
            fechaActualizacion='$fechaActualizacion'
        WHERE codigoReporte='$niks'
    ");

    if ($update) {
        echo '<div class="alert alert-success text-center">Los datos se han actualizado correctamente.</div>';
    } else {
        echo '<div class="alert alert-danger">Error: No se pudo actualizar los datos. ' . mysqli_error($conn) . '</div>';
    }
}

// Obtener los datos actuales del reporte
$query = mysqli_query($conn, "SELECT * FROM report WHERE codigoReporte='$niks'");
if ($query && mysqli_num_rows($query) > 0) {
    $datoReparacionActuralizar = mysqli_fetch_assoc($query);
    $codigoReporte = $datoReparacionActuralizar['codigoReporte'];
    $codigo_propietario = $datoReparacionActuralizar['codigo_propietario'];
    $valorFactura = $datoReparacionActuralizar['valorFactura'];
    $valorServicio = $datoReparacionActuralizar['valorServicio'];
    $totalPagar = $datoReparacionActuralizar['totalPagar'];
    $situacionReportada = $datoReparacionActuralizar['situacionReportada'];
    $solucion = $datoReparacionActuralizar['solucion'];
    $EstadoReporte = $datoReparacionActuralizar['EstadoReporte'];
    $id_reparador = $datoReparacionActuralizar['id_reparador'];
    $fechaActualizacion = $datoReparacionActuralizar['fechaActualizacion'];
    $fotoReporte = $datoReparacionActuralizar['fotoReporte']; // Ruta de la foto reportada
    
} else {
    die('<div class="alert alert-danger">Error: Datos no encontrados.</div>');
}
?>
<!-- Formulario -->
<form method="POST" enctype="multipart/form-data" class="was-validated">
    <div class="modal-body text-left p-3 mb-5 bg-white">
        <div class="row">
            <!-- Columna izquierda -->
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="codigoReporte">Código del reporte *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-qr-code"></i></span>
                        </div>
                        <input type="text" name="codigoReporte" id="codigoReporte" class="form-control" placeholder="Código del reporte" value="<?php echo htmlspecialchars($codigoReporte); ?>" readonly required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fechaActualizacion">Fecha de actualización *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                        </div>
                        <input type="date" name="fechaActualizacion" id="fechaActualizacion" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_reparador">Identificación del reparador *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-person-vcard-fill"></i></span>
                        </div>
                        <input type="number" name="id_reparador" id="id_reparador" class="form-control" placeholder="Identificación del reparador" value="<?php echo htmlspecialchars($id_reparador); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="codigo_propietario">Código propietario *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-regex"></i></span>
                        </div>
                        <input type="number" name="codigo_propietario" id="codigo_propietario" class="form-control" placeholder="Código propietario" value="<?php echo htmlspecialchars($codigo_propietario); ?>" required>
                    </div>
                </div>

                <!-- Campos relacionados con valores -->
                <div class="form-group">
                    <label for="valorFactura">Valor factura *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                        </div>
                        <input type="number" name="valorFactura" id="valorFactura" class="form-control" placeholder="Valor factura" value="<?php echo htmlspecialchars($valorFactura); ?>" required onchange="calcularTotal()">
                    </div>
                </div>

                <div class="form-group">
                    <label for="valorServicio">Valor del servicio *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                        </div>
                        <input type="number" name="valorServicio" id="valorServicio" class="form-control" placeholder="Valor del servicio" value="<?php echo htmlspecialchars($valorServicio); ?>" required onchange="calcularTotal()">
                    </div>
                </div>

                <div class="form-group">
                    <label for="totalPagar">Total a pagar *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                        </div>
                        <input type="number" name="totalPagar" id="totalPagar" class="form-control" value="<?php echo htmlspecialchars($totalPagar); ?>" placeholder="Total a pagar" readonly>
                    </div>
                </div>
            </div>

            <!-- Columna derecha -->
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="situacionReportada">Situación reportada *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-chat-left-text-fill"></i></span>
                        </div>
                        <textarea name="situacionReportada" id="situacionReportada" class="form-control" placeholder="Situación reportada" required><?php echo htmlspecialchars($situacionReportada); ?></textarea>
                    </div>
                </div>

                <div class="form-group">
    <label for="fotoReporte">Foto reportada *</label>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="bi bi-image-fill"></i></span>
        </div>
        <!-- Mostrar la imagen actual si existe -->
        <?php if (!empty($fotoReporte)): ?>
            <div class="mb-2">
                <img src="<?php echo htmlspecialchars($fotoReporte); ?>" alt="Foto reportada" class="img-fluid" style="max-width: 30%; height: auto; border: 1px solid #ccc; padding: 5px;">
            </div>
        <?php else: ?>
            <p class="text-muted">No hay foto reportada disponible.</p>
        <?php endif; ?>
    </div>
</div>

                <div class="form-group">
                    <label for="solucion">Solución *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-chat-left-text-fill"></i></span>
                        </div>
                        <textarea name="solucion" id="solucion" class="form-control" placeholder="Solución" required><?php echo htmlspecialchars($solucion); ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label style="color:#000" class="text-left">Foto de la solución</label><br>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="bi bi-camera-fill"></i>
                            </span>
                        </div>
                        <input type="file" name="fotoSolucion" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="EstadoReporte">Estado del reporte *</label>
                    <select name="EstadoReporte" id="EstadoReporte" class="form-control" required>
                        <option value="<?php echo htmlspecialchars($EstadoReporte); ?>"><?php echo htmlspecialchars($EstadoReporte); ?></option>
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="ATENDIDO">ATENDIDO</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group text-center mt-3">
            <button type="submit" class="btn bg-magenta-dark text-white" name="btnUpdateReparacion">Actualizar reparación</button>
            <a href="viewReparaciones.php" class="btn bg-indigo-dark text-white">Cancelar</a>
        </div>
    </div>
</form>

<script>
    function calcularTotal() {
        let valorFactura = parseFloat(document.getElementById('valorFactura').value) || 0;
        let valorServicio = parseFloat(document.getElementById('valorServicio').value) || 0;
        document.getElementById('totalPagar').value = valorFactura + valorServicio;
    }
</script>