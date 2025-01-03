<?php
// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'eliminar_usuario.php';

// Consulta para obtener los usuarios
$sql = "SELECT username, nombre, rol, foto, email, genero, telefono, direccion, edad, fechaCreacionUser 
        FROM users
        ORDER BY fechaCreacionUser DESC";

$result = $conn->query($sql);

// Almacenar los datos de los usuarios
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Determinar el nombre del rol
        $rol = '';
        switch ($row['rol']) {
            case 1:
                $rol = 'Administrador';
                break;
            case 2:
                $rol = 'Operario';
                break;
            case 3:
                $rol = 'Aprobador';
                break;
            case 4:
                $rol = 'Editor';
                break;
            default:
                $rol = 'Desconocido';
                break;
        }

        // Añadir la columna de acciones (solo eliminar)
        $row['acciones'] = '
            <td>
                <form method="POST" style="display:inline;" class="delete-form">
                    <input type="hidden" name="username" value="' . htmlspecialchars($row['username']) . '">
                    <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirmDelete();"><i class="bi bi-trash3-fill"></i></button>
                </form>
            </td>';
        $row['rol'] = $rol;  // Asignar el nombre del rol
        $data[] = $row;
    }
}
?>

<!-- Incluir la hoja de estilos de toastr -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

<!-- Mostrar mensajes de éxito o error con toastr -->
<?php if (isset($message)): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.<?php echo $messageType; ?>("<?php echo $message; ?>");
    </script>
<?php endif; ?>

<!-- Incluir DataTables CSS y JS -->
<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<!-- Tabla de usuarios -->
<table id="tablaUsuarios" class="table table-hover table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Username</th>
            <th>Nombre</th>
            <th>Rol</th>
            <th>Foto</th>
            <th>Email</th>
            <th>Género</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Edad</th>
            <th>Eliminar</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['rol']); ?></td> <!-- Mostrar el nombre del rol -->
                <td><img src="<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto" style="width:50px; height:50px;"></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['genero']); ?></td>
                <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                <td><?php echo htmlspecialchars($row['edad']); ?></td>
                <?php echo $row['acciones']; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Confirmación de eliminación -->
<script>
    function confirmDelete() {
        return confirm("¿Estás seguro de que deseas eliminar este usuario?");
    }

 // Inicializar DataTables
$(document).ready(function() {
    $('#tablaUsuarios').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        }
    });
});

</script>
