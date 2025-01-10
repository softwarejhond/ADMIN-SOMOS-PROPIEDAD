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
        WHERE proprieter.estadoPropietario = 'ACTIVO' AND proprieter.condicion = 'EN ALQUILER'
        ORDER BY proprieter.nombre_propietario ASC";

$result = $conn->query($sql);

// Si la consulta tiene resultados, generar la tabla
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['acciones'] = '
            <td><a href="verDetalle.php?id=' . $row['codigo'] . '&tabla=proprieter" class="btn bg-lime-dark btn-sm"><i class="bi bi-eye-fill"></i></a></td>
            <td><a href="editarPropiedad.php?id=' . $row['codigo'] . '&tabla=proprieter" class="btn bg-indigo-dark text-white btn-sm"><i class="bi bi-pencil-fill"></i></a></td>
            ';
        $data[] = $row;
    }
}
?>
<!-- Tabla -->
<table id="propiedadesVenta" class="table table-hover table-bordered">
    <thead class="thead-dark">
        <tr>
            <?php if (!empty($data)): ?>
                <?php foreach (array_keys($data[0]) as $key): ?>
                    <?php if ($key != 'acciones'): ?>
                        <th><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
                <th></th>
                <th></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <?php foreach ($row as $key => $value): ?>
                    <?php if ($key != 'acciones'): ?>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>
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
