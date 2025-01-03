<div class="card text-center">
  
    <div class="card-body">
        <table id="tableReparaciones" class="table table-hover table-bordered table-lg table-responsive">
            <thead class="thead-dark">
                <tr class="align-middle">
                    <th class="w-20"><i class="fa-solid fa-qrcode" title="Código"></i> Código</th>
                    <th style="width: 100px;"><i class="fa-solid fa-calendar-days" title="Fecha"></i> Fecha</th>
                    <th style="width: 300px;">Propietario</th>
                    <th style="width: 300px;">Dirección</th>
                    <th style="width: 200px;">Teléfono Propietario</th>
                    <th style="width: 300px;">Inquilino</th>
                    <th style="width: 200px;">Teléfono Inquilino</th>
                    <th style="width: 100px;"><i class="fa-solid fa-dollar-sign"></i> Valor factura</th>
                    <th style="width: 100px;"><i class="fa-solid fa-dollar-sign"></i> Valor Servicio</th>
                    <th style="width: 100px;"><i class="fa-solid fa-dollar-sign"></i> Total</th>
                    <th style="width: 100px;"><i class="fa-solid fa-triangle-exclamation"></i> Estado</th>
                    <th style="width: 70px;"><i class="bi bi-eye-fill"></i></th>
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
                            INNER JOIN repairmen ON report.id_reparador = repairmen.identificacion 
                            WHERE EstadoReporte = 'SIN ATENDER'";
                $sql = mysqli_query($conn, $sqlQuery);

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
                                <td><a href="verReparador.php?nik=' . $row['id_reparador'] . '" class="btn bg-magenta-dark text-white btn-sm"><i class="bi bi-eye-fill"></i></a></td>
                                <td><a href="printReporteReparaciones.php?nik=' . $row['id'] . '&code=' . $row['codigo_propietario'] . '&repairmen=' . $row['id_reparador'] . '&reparacion=' . $row['codigoReporte'] . '" class="btn bg-indigo-dark text-white btn-sm"><span class="fa fa-print"></span></a></td>
                                <td><a href="updateReparacion.php?niks=' . $row['codigoReporte'] . '" class="btn btn-warning btn-sm"><span class="fa fa-edit"></span></a></td>
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
