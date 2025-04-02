<div class="card text-center">
  
    <div class="card-body">
        <table id="tableReparaciones" class="table table-hover table-bordered table-lg table-responsive">
            <thead class="thead-dark">
                <tr class="align-middle">
                    <th class="w-20">Código</th>
                    <th style="width: 100px;">Fecha</th>
                    <th style="width: 300px;">Propietario</th>
                    <th style="width: 300px;">Dirección</th>
                    <th style="width: 200px;">Teléfono Propietario</th>
                    <th style="width: 300px;">Inquilino</th>
                    <th style="width: 200px;">Teléfono Inquilino</th>
                    <th style="width: 100px;">Valor factura</th>
                    <th style="width: 100px;">Valor Servicio</th>
                    <th style="width: 100px;">Total</th>
                    <th style="width: 100px;">Estado</th>
                    <th style="width: 70px;"><i class="fa-solid fa-print" title="Imprimir certificado"></i></th>
                    <th style="width: 70px;"><i class="fa-solid fa-pen-to-square" title="Editar reparación"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Asegurar que $filter tenga un valor por defecto
                $filter = isset($filter) ? $filter : false;

                // Query SQL basada en el filtro
                $sqlQuery = "SELECT * FROM report 
                            INNER JOIN proprieter ON report.codigo_propietario = proprieter.codigo 
                            INNER JOIN repairmen ON report.id_reparador = repairmen.identificacion";
            $sql = mysqli_query($conn, $sqlQuery);
            if (!$sql) {
                die("Error en la consulta SQL: " . mysqli_error($conn));
            }

                // Verificar si hay resultados
                if (mysqli_num_rows($sql) == 0) {
                    echo '<tr><td colspan="14">No hay datos.</td></tr>';
                } else {
                    while ($row = mysqli_fetch_assoc($sql)) {
                        echo '
                            <tr style="font-size:12px">
                                <td>' . htmlspecialchars($row['codigo_propietario'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['fechaCreacion'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['nombre_propietario'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['direccion'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['telefono_propietario'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['nombre_inquilino'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['telefono_inquilino'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['valorFactura'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['valorServicio'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['totalPagar'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['EstadoReporte'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td><a href="printReporteReparaciones.php?nik=' . $row['id'] . '&code=' . $row['codigo_propietario'] . '&repairmen=' . $row['id_reparador'] . '&reparacion=' . $row['codigoReporte'] . '" class="btn bg-indigo-dark text-white btn-sm"><span class="fa fa-print"></span></a></td>
                                <td><a href="actualizarReporte.php?niks=' . $row['codigoReporte'] . '" class="btn btn-warning btn-sm"><span class="fa fa-edit"></span></a></td>
                            </tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#tableReparaciones').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    });
</script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<!-- Incluir Popper.js sin el atributo integrity -->
<script src="https://unpkg.com/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<!-- Incluir Bootstrap JS sin el atributo integrity -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
<!-- Incluir DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- Incluir DataTables con estilos --> 