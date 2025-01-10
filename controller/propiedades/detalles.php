<?php
if (isset($_GET['id']) && isset($_GET['tabla'])) {
    $registroId = $_GET['id'];
    $tabla = $_GET['tabla'];
    $registro = obtenerRegistroPorId($tabla, $registroId);
} else {
    header('Location: index.php');
    exit();
}

// Lista de campos permitidos organizados por categorías
$camposPermitidos = [
    'codigo',
    'tipoInmueble',
    'nivel_piso',
    'area',
    'estrato', // Características generales
    'departamento',
    'municipio',
    'direccion',                // Ubicación
    'terraza',
    'ascensor',
    'patio',
    'parqueadero',
    'cuarto_util', // Áreas comunes y adicionales
    'habitaciones',
    'closet',
    'sala',
    'sala_comedor',
    'comedor',  // Espacios interiores
    'cocina',
    'CuartoServicios',
    'ZonaRopa',                     // Áreas funcionales
    'vista',
    'servicios',
    'servicios_publicos',
    'otras_caracteristicas', // Detalles adicionales
    'TelefonoInmueble',
    'valor_canon',                           // Información de contacto y precio
    'doc_propietario',
    'nombre_propietario',
    'telefono_propietario',
    'email_propietario', // Propietario
    'banco',
    'tipoCuenta',
    'numeroCuenta',
    'diaPago',           // Información bancaria
    'fecha',
    'contrato_EPM',                                    // Otros
];

// Etiquetas personalizadas para los campos
$labelsCampos = [
    'codigo' => 'Código del Inmueble',
    'tipoInmueble' => 'Tipo de Inmueble',
    'nivel_piso' => 'Nivel/Piso',
    'area' => 'Área Total (m²)',
    'estrato' => 'Estrato',
    'departamento' => 'Departamento',
    'municipio' => 'Municipio',
    'direccion' => 'Dirección',
    'terraza' => 'Terraza',
    'ascensor' => 'Ascensor',
    'patio' => 'Patio',
    'parqueadero' => 'Parqueadero',
    'cuarto_util' => 'Cuarto Útil',
    'habitaciones' => 'Habitaciones',
    'closet' => 'Closet',
    'sala' => 'Sala',
    'sala_comedor' => 'Sala-Comedor',
    'comedor' => 'Comedor',
    'cocina' => 'Cocina',
    'CuartoServicios' => 'Cuarto de Servicios',
    'ZonaRopa' => 'Zona de Ropa',
    'vista' => 'Vista',
    'servicios' => 'Servicios',
    'servicios_publicos' => 'Servicios Públicos',
    'otras_caracteristicas' => 'Otras Características',
    'TelefonoInmueble' => 'Teléfono del Inmueble',
    'valor_canon' => 'Valor del Canon',
    'doc_propietario' => 'Documento del Propietario',
    'nombre_propietario' => 'Nombre del Propietario',
    'telefono_propietario' => 'Teléfono del Propietario',
    'email_propietario' => 'Correo Electrónico del Propietario',
    'banco' => 'Banco',
    'tipoCuenta' => 'Tipo de Cuenta',
    'numeroCuenta' => 'Número de Cuenta',
    'diaPago' => 'Día de Pago',
    'fecha' => 'Fecha de Registro',
    'contrato_EPM' => 'Contrato con EPM'
];
?>

<div class="container">
    <?php if ($registro) : ?>
        <div class="card mb-3">
            <img src="<?php echo isset($registro['url_foto_principal']) ? 'fotos/' . htmlspecialchars($registro['url_foto_principal']) : 'https://via.placeholder.com/500'; ?>" class="card-img-top" alt="Imagen del Inmueble">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <?php
                        // Dividir los campos permitidos en dos mitades
                        $totalCampos = count($camposPermitidos);
                        $mitad = ceil($totalCampos / 2);
                        $primerosCampos = array_slice($camposPermitidos, 0, $mitad);
                        ?>
                        <?php foreach ($primerosCampos as $key) : ?>
                            <?php if (isset($registro[$key])) : ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($labelsCampos[$key] ?? ucfirst(str_replace('_', ' ', $key))); ?>:</strong>
                                    <?php
                                    if ($key == 'fecha' || $key == 'fecha_creacion') {
                                        echo date("d/m/Y", strtotime($registro[$key]));
                                    } else {
                                        echo htmlspecialchars($registro[$key]);
                                    }
                                    ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <?php
                        // Obtener la segunda mitad de los campos
                        $segundosCampos = array_slice($camposPermitidos, $mitad);
                        ?>
                        <?php foreach ($segundosCampos as $key) : ?>
                            <?php if (isset($registro[$key])) : ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($labelsCampos[$key] ?? ucfirst(str_replace('_', ' ', $key))); ?>:</strong>
                                    <?php
                                    if ($key == 'fecha' || $key == 'fecha_creacion') {
                                        echo date("d/m/Y", strtotime($registro[$key]));
                                    } else {
                                        echo htmlspecialchars($registro[$key]);
                                    }
                                    ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-danger" role="alert">
            Registro no encontrado.
        </div>
    <?php endif; ?>
</div>
<br><br>