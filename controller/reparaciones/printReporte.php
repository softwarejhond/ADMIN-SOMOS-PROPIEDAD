<?php
require_once 'conexion.php'; // Incluye la conexión a la base de datos

$nik = filter_input(INPUT_GET, 'nik', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$reparacion = filter_input(INPUT_GET, 'reparacion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$idReparador = filter_input(INPUT_GET, 'repairmen', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


// Consultas reutilizables con parámetros preparados
function fetchQuery($conn, $query, $params = []) {
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

?>
  <style>
 @media print {
    #areaImprimir {
        background-size: 100% 100%;
        background-repeat: no-repeat;
        background-color: white !important; /* Forzar color de fondo blanco */
    }
    body {
        visibility: hidden;
    }
    #areaImprimir {
        visibility: visible;
        position: absolute;
        left: 0;
        top: 0;
    }
}

    </style>
<div class="card-body">
    <div class="col-lg-12 col-md-12 col-sm-12 px-2 mt-1">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 px-2 mt-1 border border-dark" id="areaImprimir">
                <div class="text-left">
                    <?php
                    $queryCompany = fetchQuery($conn, "SELECT nombre, nit FROM company");
                    while ($empresaLog = $queryCompany->fetch_assoc()) {
                        echo '<p class="text-right px-5" style="font-size:8px;color:#ffffff">' . htmlspecialchars($empresaLog['nombre']) . '</p>';
                    }
                    ?>
                    <div class="row">
                        <div class="col col-md-6 col-sm-12 text-white ml-3 mt-n2 mr-2">
                            <b>Fecha de impresión:</b> <?= date('d-m-Y'); ?>
                        </div>
                    </div>
                    <br><br><br><br>

                    <div class="row">
                        <?php
                        $queryCompany = fetchQuery($conn, "SELECT * FROM company");
                        while ($empresaLog = $queryCompany->fetch_assoc()) {
                            echo '<div class="col col-md-6 col-sm-12" style="font-size:16px">';
                            echo '<br><label class="card-text ml-3 mt-n2 float-left">' . htmlspecialchars($empresaLog['nombre']) . '</label><br>';
                            echo '<label class="card-text ml-3 mt-n2 float-left">NIT: ' . htmlspecialchars($empresaLog['nit']) . '</label><br>';
                            echo '<label class="card-text ml-3 mt-n2 float-left">Teléfono: ' . htmlspecialchars($empresaLog['telefono']) . '</label><br>';
                            echo '<label class="card-text ml-3 mt-n2 float-left">Correo electrónico: ' . htmlspecialchars($empresaLog['email']) . '</label><br>';
                            echo '<label class="card-text ml-3 mt-n2 float-left">Dirección: ' . htmlspecialchars($empresaLog['direccion']) . '</label><br>';
                            echo '</div>';
                        }
                        ?>

                        <?php
                        $queryReport = fetchQuery($conn, "SELECT * FROM report INNER JOIN proprieter ON proprieter.codigo = report.codigo_propietario WHERE codigoReporte = ?", [$reparacion]);
                        while ($queryReporte = $queryReport->fetch_assoc()) {
                            echo '<div class="col col-md-6 col-sm-12" style="font-size:16px">';
                            echo '<br><label class="card-text ml-3 mt-n2 float-left"># Factura: <b>' . htmlspecialchars($queryReporte['codigoReporte']) . '</b></label><br>';
                            echo '<label class="card-text ml-3 mt-n2 float-left">Código propiedad: ' . htmlspecialchars($queryReporte['codigo_propietario']) . '</label><br>';
                            echo '<label class="card-text ml-3 mt-n2 float-left">Fecha del reporte: ' . htmlspecialchars($queryReporte['fechaCreacion']) . '</label><br>';
                            echo '<label class="card-text ml-3 mt-n2 float-left text-capitalize">Propietario: ' . htmlspecialchars($queryReporte['nombre_propietario']) . '</label><br>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <br>
                <h6 class="text-left ml-3 mt-n2 mr-2">INFORMACIÓN DE FACTURA:</h6>
                <br>
                <div class="ml-3 mt-n2 mr-2">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><h6><i class="bi bi-question-diamond-fill"></i> Daño o reporte</h6></th>
                                <th><h6><i class="bi bi-wrench-adjustable"></i> Solución</h6></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $queryReportePrint = fetchQuery($conn, "SELECT situacionReportada,fotoReporte,solucion,fotoSolucion FROM report WHERE codigoReporte = ?", [$reparacion]);
                            while ($row = $queryReportePrint->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>
                                <label style="font-size:14px">' . htmlspecialchars($row['situacionReportada']) . '</label>
                                <br><br>
                                <img src="' . htmlspecialchars($row['fotoReporte']) . '" alt="Foto del reporte" style="width: 500px; height: 300px;">
                                </td>';                            
                                echo '<td>
                                <label style="font-size:14px">' . htmlspecialchars($row['solucion']) . '</label>
                                 <br><br>
                                <img src="' . htmlspecialchars($row['fotoSolucion']) . '" alt="Foto del reporte" style="width: 500px; height: 300px;">
                               
                                </td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <br><br>

                <div class="text-center">
                    <a onclick="jQuery('#areaImprimir').print()" class="btn bg-indigo-dark text-white" style="border-radius: 0;"><i class="fa fa-print"></i> IMPRIMIR</a>
                    <a href="main.php" class="btn bg-magenta-dark text-white" style="border-radius: 0;"><i class="fa fa-sync"></i> CANCELAR</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/jQuery.print.min.js"></script>
    <!--Muy importante libreria que se encuentra en la carpeta js-->
    <script src="js/jQuery.print.js"></script>
    <!--Muy importante libreria que se encuentra en la carpeta js-->