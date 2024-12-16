<?php
// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Consulta SQL
$sql = "SELECT proprieter.codigo, proprieter.tipoInmueble, proprieter.nombre_propietario, 
               proprieter.telefono_propietario, municipios.municipio, proprieter.direccion, 
               proprieter.estadoPropietario,  proprieter.condicion
        FROM proprieter
        INNER JOIN municipios ON proprieter.Municipio = municipios.id_municipio
        WHERE proprieter.estadoPropietario = 'ACTIVO' AND  proprieter.condicion='EN ALQUILER'
        ORDER BY proprieter.nombre_propietario ASC";

$result = $conn->query($sql);

// Si la consulta tiene resultados, generar la tabla
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['acciones'] = '
            <td><button class="btn bg-lime-dark btn-sm"><i class="bi bi-eye-fill"></i></button></td>
            <td><button class="btn bg-indigo-dark text-white btn-sm"><i class="bi bi-pencil-fill"></i></button></td>
            <td><button class="btn btn-danger btn-sm"><i class="bi bi-trash3-fill"></i></button></td>';
        $data[] = $row;
    }
}
?>
<!-- Tabla -->
<table id="propiedadesVenta" class="table table-hover table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Código</th>
            <th>Inmueble</th>
            <th>Propietario</th>
            <th>Teléfono</th>
            <th>Municipio</th>
            <th>Dirección</th>
            <th>Estado</th>
            <th>Condición</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                <td><?php echo htmlspecialchars($row['tipoInmueble']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_propietario']); ?></td>
                <td><?php echo htmlspecialchars($row['telefono_propietario']); ?></td>
                <td><?php echo htmlspecialchars($row['municipio']); ?></td>
                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                <td><?php echo htmlspecialchars($row['estadoPropietario']); ?></td>
                <td><?php echo htmlspecialchars($row['condicion']); ?></td>
                <?php echo $row['acciones']; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    $(document).ready(function() {
        $('#propiedadesVenta').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    });
</script>