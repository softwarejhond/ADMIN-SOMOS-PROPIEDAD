<?php
// Configuración de la localización
$locale = 'es_ES'; // Cambia según tu idioma/región
$dateFormatter = new IntlDateFormatter(
    $locale,
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'UTC', // Cambia la zona horaria según tus necesidades
    IntlDateFormatter::GREGORIAN
);
?>
<div class="card">
    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="text-end p-3">
        <div class="form-group">
            <label for="mes" class="form-label text-end">Seleccionar un mes para buscar</label>
            <div class="d-inline-flex align-items-center">

                <select name="mes" id="mes" class="form-select form-select-lg me-2">
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
                <button type="submit" class="btn bg-lime-dark btn-lg">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </form>

    <div class="card-body">
        <div class="table-responsive">
            <table id="renovacionesTable" class="table table-striped table-hover table-bordered align-middle mt-3">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Código</th>
                        <th>Inmueble</th>
                        <th>Propietario</th>
                        <th>Inquilino</th>
                        <th>E-mail</th>
                        <th>Teléfono</th>
                        <th>Mes</th>
                        <th>Fecha Creación</th>
                        <th>Estado</th>
                        <th>Editar</th>
                        <th>% IPC</th>
                        <th>Recordatorio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $queryICP = mysqli_query($conn, "SELECT * FROM porcentajeAumento WHERE id=1");
                    while ($porcentajeAumentado = mysqli_fetch_array($queryICP)) {
                        $porcentaje = $porcentajeAumentado['porcentaje'];
                    }
                    ?>
                    <?php
                    $mes = $_POST['mes'] ?? null;

                    $sql = $conn->query(
                        "SELECT * FROM proprieter 
                         WHERE MONTH(DATE_ADD(fecha, INTERVAL 31 DAY)) = ($mes + 1) % 12 + 1 
                         AND estadoPropietario = 'ACTIVO' 
                         ORDER BY fecha DESC"
                    );

                    if ($sql->num_rows === 0) {
                        echo '<tr><td colspan="12" class="text-center">No hay datos disponibles.</td></tr>';
                    } else {
                        while ($row = $sql->fetch_assoc()) {
                            $id = $row['id'];
                            $ipc = $row['ipc'];
                            $mes_fecha = $dateFormatter->format(strtotime($row['fecha'])); // Aquí se mueve la asignación dentro del ciclo
                            $activo = $row['estadoPropietario'] === 'ACTIVO'
                                ? '<span class="btn bg-lime-dark" title="Activo"><i class="fa-solid fa-lock-open"></i></span>'
                                : '<span class="btn bg-danger" title="Inactivo"><i class="fa-solid fa-lock"></i></span>';

                            $email = $row['email_inquilino']
                                ? '<a href="enviarRecordatorio.php?nik=' . $id . '" class="btn bg-lime-dark" title="Enviar recordatorio"><i class="fa-solid fa-bell"></i></a>'
                                : '<button class="btn btn-danger" title="Sin email"><i class="fa-solid fa-lock"></i></button>';

                            if ($row['tipoInmueble'] == "LOCAL") {
                                $porcentajes =  '<a href="actualizarPorcentajeLocal.php?parametro=' . $id . '" title="PORCENTAJE IPC"  class="btn bg-indigo-dark text-white">' . $ipc . '</a>';
                                $tipoPropiedad =  '<button  class="btn btn-sm bg-indigo-dark text-white btn-sm w-100"><i class="fa-solid fa-shop"></i> ' . $row['tipoInmueble'] . '</button>';
                            } else {
                                $porcentajes = '<button title="PORCENTAJE IPC" class="btn bg-magenta-dark text-white">' . $ipc . '</button>';
                                $tipoPropiedad =  '<button class="btn btn-sm bg-magenta-dark text-white   w-100"><i class="fa-solid fa-house"></i> ' . $row['tipoInmueble'] . '</button>';
                            }

                            echo "
                                <tr class='text-center'>
                                    <td class='text-nowrap'>{$row['codigo']}</td>
                                    <td>{$tipoPropiedad}</td>
                                    <td>{$row['nombre_propietario']}</td>
                                    <td>{$row['nombre_inquilino']}</td>
                                    <td class='text-nowrap'>{$row['email_inquilino']}</td>
                                    <td class='text-nowrap'>{$row['telefono_inquilino']}</td>
                                    <td><span class='badge bg-warning text-dark'>$mes_fecha</span></td>
                                    <td>{$row['fecha']}</td>
                                    <td>{$activo}</td>
                                    <td><a href='updatePropietario.php?nik=$id' class='btn btn-info btn'><i class='fa fa-edit'></i></a></td>
                                    <td>$porcentajes</td>
                                    <td>{$email}</td>
                                </tr>
                            ";
                        }
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
</div><br>
<br>
<script>
    $(document).ready(function() {
        $('#renovacionesTable').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    });
</script>