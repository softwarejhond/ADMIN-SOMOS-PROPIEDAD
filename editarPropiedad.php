<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("funciones.php");
include("conexion.php");

if (isset($_GET['id']) && isset($_GET['tabla'])) {
    $id = $_GET['id'];
    $tabla = $_GET['tabla'];

    // Obtener los datos del registro
    $registro = obtenerRegistroPorId($tabla, $id);

    if (!$registro) {
        echo "<div class='alert alert-danger'>No se encontró el registro.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>No se proporcionó el ID o la tabla.</div>";
    exit;
}

// Procesar la actualización del registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'actualizarRegistro') {
    // Validación simple
    $validarCampos = ['codigo', 'tabla'];
    foreach ($validarCampos as $campo) {
        if (empty($_POST[$campo])) {
            echo '<div class="alert alert-danger">Faltan campos obligatorios.</div>';
            exit;
        }
    }

    if ($resultado['success']) {
        echo '<div class="alert alert-success">Registro actualizado correctamente.</div>';
         header("Location: index.php"); 
         exit;
    } else {
        echo '<div class="alert alert-danger">Error al actualizar el registro: '. $resultado['message'] .'</div>';
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Propiedad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Editar Propiedad</h2>
        <form id="formEditar" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?id=" . $id . "&tabla=" . $tabla); ?>">
            <input type="hidden" name="codigo" value="<?php echo $registro['codigo']; ?>">
            <input type="hidden" name="tabla" value="<?php echo $tabla; ?>">
            <input type="hidden" name="action" value="actualizarRegistro">
           
            <?php foreach ($registro as $key => $value): ?>
                <?php if ($key != 'acciones' && $key != 'codigo'): ?>
                    <div class="mb-3">
                        <label for="edit_<?php echo $key; ?>" class="form-label"><?php echo ucfirst(str_replace('_', ' ', $key)); ?></label>
                        <input type="text" class="form-control" id="edit_<?php echo $key; ?>"
                            name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>" required>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>
