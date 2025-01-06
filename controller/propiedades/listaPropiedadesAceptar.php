<?php
// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegurarse de que la conexión a la base de datos esté configurada
if (!isset($conn) || !$conn) {
    die('Error: La conexión a la base de datos no está configurada.');
}

// Inicializar la variable de mensaje
$mensajeToast = '';

// Actualizar estado de la propiedad si se envió el formulario
if (isset($_POST['btnActualizarEstado'])) {
    $codigo = $_POST['codigo']; // Obtener el código de la propiedad desde el formulario
    $nuevoEstado = $_POST['nuevoEstado']; // Obtener el nuevo estado
    
    // Consulta SQL para actualizar el estado
    $updateSql = "UPDATE proprieter SET estadoPropietario = 'ACTIVO', condicion=? WHERE codigo = ?";
    $stmt = $conn->prepare($updateSql);

    // Usar bind_param correctamente con tipos: 's' para string y 'i' para entero
    $stmt->bind_param('si', $nuevoEstado, $codigo); // Preparar la consulta para ejecutar
    
    if ($stmt->execute()) {
        $mensajeToast = 'Estado actualizado correctamente.';
    } else {
        $mensajeToast = 'Error al actualizar el estado.';
    }
}

// Consulta SQL para obtener los datos
$sql = "SELECT proprieter.codigo, proprieter.tipoInmueble, proprieter.nombre_propietario, 
               proprieter.telefono_propietario, municipios.municipio, proprieter.direccion, 
               proprieter.estadoPropietario, proprieter.condicion
        FROM proprieter
        INNER JOIN municipios ON proprieter.Municipio = municipios.id_municipio
        WHERE proprieter.estadoPropietario = 'NUEVO' AND proprieter.condicion = 'NUEVO'
        ORDER BY proprieter.nombre_propietario ASC";

$result = $conn->query($sql);

// Si la consulta tiene resultados, generar los datos
$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Agregar acciones a cada fila
        $row['acciones'] = '
        <td><button class="btn bg-lime-dark btn-sm"><i class="bi bi-eye-fill"></i></button></td>
        <td>
            <form method="POST" class="d-inline" onsubmit="return confirmarActualizacion();">
                <input type="hidden" name="codigo" value="' . htmlspecialchars($row["codigo"]) . '">
                <select class="form-control" name="nuevoEstado" required>
                    <option value="NUEVO" ' . ($row["estadoPropietario"] == 'NUEVO' ? 'selected' : '') . '>NUEVO</option>
                    <option value="EN ALQUILER" ' . ($row["estadoPropietario"] == 'EN ALQUILER' ? 'selected' : '') . '>EN ALQUILER</option>
                    <option value="EN VENTA" ' . ($row["estadoPropietario"] == 'EN VENTA' ? 'selected' : '') . '>EN VENTA</option>
                    <option value="ALQUILER O VENTA" ' . ($row["estadoPropietario"] == 'ALQUILER O VENTA' ? 'selected' : '') . '>ALQUILER O VENTA</option>
                    <option value="DENEGADO" ' . ($row["estadoPropietario"] == 'DENEGADO' ? 'selected' : '') . '>DENEGADO</option>
                </select>
                <button type="submit" name="btnActualizarEstado" class="btn bg-indigo-dark text-white btn-sm">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </form>
        </td>
        <td><button class="btn btn-danger btn-sm"><i class="bi bi-trash3-fill"></i></button></td>';
        

        // Guardar fila de datos
        $data[] = $row;
    }
} else {
    // Si no hay datos, mostrar mensaje
    echo '<div class="alert alert-info">No hay datos disponibles.</div>';
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

<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Inicialización de la tabla
    $('#propiedadesVenta').DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });

    // Mostrar mensaje de toast si existe
    <?php if ($mensajeToast): ?>
        toastr.success("<?php echo $mensajeToast; ?>");
    <?php endif; ?>
});

// Función de confirmación de actualización
function confirmarActualizacion() {
    return confirm("¿Está seguro de que desea actualizar el estado de esta propiedad?");
}
</script>
