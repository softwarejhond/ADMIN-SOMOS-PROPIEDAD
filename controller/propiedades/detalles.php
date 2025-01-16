<?php
// Incluye la función de conexión a la base de datos (adaptar a tu configuración)
require_once 'funciones.php';
if (isset($_GET['id']) && isset($_GET['tabla'])) {
    $registroId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $tabla = filter_input(INPUT_GET, 'tabla');
   
     if(!$registroId){
        header('Location: index.php?error=id_invalido');
        exit();
      }
      if(!$tabla){
         header('Location: index.php?error=tabla_invalida');
         exit();
       }
       
        // Lista de tablas permitidas para evitar el problema de seguridad
        $tablasPermitidas = ['inmuebles', /* Agrega aqui las tablas que deben mostrar detalles */ ];
        
       
    $registro = obtenerRegistroPorId($tabla, $registroId);
} else {
    header('Location: index.php');
    exit();
}

$camposPermitidos = [
    'codigo',
    'tipoInmueble',
    'nivel_piso',
    'area',
    'estrato',
    'departamento',
    'municipio',
    'direccion',
    'terraza',
    'ascensor',
    'patio',
    'parqueadero',
    'cuarto_util',
    'alcobas',
    'closet',
    'sala',
    'sala_comedor',
    'comedor',
    'cocina',
    'CuartoServicios',
    'ZonaRopa',
    'vista',
    'servicios',
    'servicios_publicos',
    'otras_caracteristicas',
    'TelefonoInmueble',
    'valor_canon',
    'doc_propietario',
    'nombre_propietario',
    'telefono_propietario',
    'email_propietario',
    'banco',
    'tipoCuenta',
    'numeroCuenta',
    'diaPago',
    'fecha',
    'contrato_EPM',
];

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
    'alcobas' => 'Alcobas',
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div class="container mt-4">
    <?php if ($registro) : ?>
        <div class="cardind">
            <div class="count">
               
                <!-- Sección de la imagen -->
                 <div class="text-center mb-4" style="max-width: 50%; margin-left: auto; margin-right: auto;">
                     <div style="width: 100%; padding-bottom: 50%; position: relative; overflow: hidden;">
                        <img src="<?php echo isset($registro['url_foto_principal']) ? 'fotos/' . htmlspecialchars($registro['url_foto_principal']) : 'https://via.placeholder.com/800x400'; ?>"
                            class="img-fluid rounded" alt="Imagen del Inmueble"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                
                <!-- Sección de Iconos y Valor del Canon -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                       <?php if(isset($registro['alcobas'])): ?>
                         <i class="fas fa-bed me-1"></i>
                         <span><?php echo htmlspecialchars($registro['alcobas']); ?></span>
                        <?php endif; ?>
                         <?php if(isset($registro['area'])): ?>
                        <i class="fas fa-ruler-combined ms-2 me-1"></i>
                        <span><?php echo htmlspecialchars($registro['area']); ?> m²</span>
                          <?php endif; ?>
                    </div>
                    <div>
                        <?php if (isset($registro['valor_canon'])) : ?>
                            <span class="h4"><?php echo htmlspecialchars(number_format($registro['valor_canon'], 0, ',', '.')); ?> COP</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sección de Ubicación -->
                <h4 class="mt-4">Ubicación</h4>
                <div class="row mb-3">
                   <?php if (isset($registro['departamento'])) : ?>
                     <div class="col-md-6"><i class="fas fa-map-marker-alt me-2"></i>Departamento: <?php echo htmlspecialchars($registro['departamento']); ?></div>
                   <?php endif; ?>
                   <?php if (isset($registro['municipio'])) : ?>
                    <div class="col-md-6"><i class="fas fa-city me-2"></i>Municipio: <?php echo htmlspecialchars($registro['municipio']); ?></div>
                   <?php endif; ?>
                   <?php if (isset($registro['direccion'])) : ?>
                    <div class="col-md-12"><i class="fas fa-location-arrow me-2"></i>Dirección: <?php echo htmlspecialchars($registro['direccion']); ?></div>
                   <?php endif; ?>
                </div>
                
                  <!-- Sección de Detalles del Inmueble -->
                 <h4 class="mt-4">Detalles del Inmueble</h4>
                  <div class="row mb-3">
                    <?php
                   $detalles = [
                    'codigo',
                    'tipoInmueble',
                    'nivel_piso',
                    'estrato',
                    'terraza',
                    'ascensor',
                    'patio',
                    'parqueadero',
                    'alcobas',
                    'cuarto_util',
                    'habitaciones',
                     'closet',
                     'sala',
                     'sala_comedor',
                     'comedor',
                     'cocina',
                     'CuartoServicios',
                     'ZonaRopa',
                     'vista',
                     'TelefonoInmueble',
                     'diaPago',
                    'fecha',
                    'contrato_EPM',
                    ];
                        $totalDetalles = count($detalles);
                        $mitadDetalles = ceil($totalDetalles / 2);
                        $primerosDetalles = array_slice($detalles, 0, $mitadDetalles);
                        $segundosDetalles = array_slice($detalles, $mitadDetalles);
                    ?>
                       <?php foreach (array_chunk($detalles, 2) as $chunk): ?>
                         <div class="col-md-12">
                           <div class="row">
                                  <?php foreach($chunk as $key): ?>
                                     <?php if(isset($registro[$key])): ?>
                                        <div class="col-md-6">
                                              <strong style="width: 180px; display: inline-block;"><?php echo htmlspecialchars($labelsCampos[$key] ?? ucfirst(str_replace('_', ' ', $key))); ?>:</strong>
                                              <span class="text-muted"><?php echo htmlspecialchars($registro[$key]); ?></span>
                                          </div>
                                     <?php endif; ?>
                                 <?php endforeach; ?>
                             </div>
                        </div>
                     <?php endforeach; ?>
                </div>

                <!-- Sección de Comodidades -->
                 <h4 class="mt-4">Comodidades</h4>
                  <div class="row mb-3">
                     <?php if(isset($registro['servicios'])): ?>
                       <div class="col-md-6"><i class="fas fa-concierge-bell me-2"></i>Servicios: <?php echo htmlspecialchars($registro['servicios']); ?></div>
                    <?php endif; ?>
                    <?php if(isset($registro['servicios_publicos'])): ?>
                      <div class="col-md-6"><i class="fas fa-charging-station me-2"></i>Servicios Públicos: <?php echo htmlspecialchars($registro['servicios_publicos']); ?></div>
                   <?php endif; ?>
                </div>
                

                <!-- Sección de Descripción -->
                <?php if (isset($registro['otras_caracteristicas'])): ?>
                   <h4 class="mt-4">Descripción</h4>
                    <p><?php echo nl2br(htmlspecialchars($registro['otras_caracteristicas'])); ?></p>
                   <?php endif; ?>


                <!-- Sección de Datos del Propietario -->
                  <h4 class="mt-4">Datos del Propietario</h4>
                  <div class="row">
                     <?php if (isset($registro['nombre_propietario'])): ?>
                       <div class="col-md-6"><i class="fas fa-user me-2"></i>Nombre: <?php echo htmlspecialchars($registro['nombre_propietario']); ?></div>
                    <?php endif; ?>
                     <?php if (isset($registro['doc_propietario'])): ?>
                       <div class="col-md-6"><i class="fas fa-id-card me-2"></i>Documento: <?php echo htmlspecialchars($registro['doc_propietario']); ?></div>
                    <?php endif; ?>
                     <?php if (isset($registro['telefono_propietario'])): ?>
                       <div class="col-md-6"><i class="fas fa-phone me-2"></i>Teléfono: <?php echo htmlspecialchars($registro['telefono_propietario']); ?></div>
                     <?php endif; ?>
                      <?php if (isset($registro['email_propietario'])): ?>
                       <div class="col-md-6"><i class="fas fa-envelope me-2"></i>Email: <?php echo htmlspecialchars($registro['email_propietario']); ?></div>
                     <?php endif; ?>
                       <?php if (isset($registro['banco'])): ?>
                       <div class="col-md-6"><i class="fas fa-university me-2"></i>Banco: <?php echo htmlspecialchars($registro['banco']); ?></div>
                       <?php endif; ?>
                        <?php if (isset($registro['tipoCuenta'])): ?>
                           <div class="col-md-6"><i class="fas fa-list-alt me-2"></i>Tipo de Cuenta: <?php echo htmlspecialchars($registro['tipoCuenta']); ?></div>
                          <?php endif; ?>
                          <?php if (isset($registro['numeroCuenta'])): ?>
                              <div class="col-md-6"><i class="fas fa-hashtag me-2"></i>Número de Cuenta: <?php echo htmlspecialchars($registro['numeroCuenta']); ?></div>
                          <?php endif; ?>
                  </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-danger mt-4" role="alert">
            Registro no encontrado.
        </div>
    <?php endif; ?>
</div>
<br><br>
<script>
    <?php
    // Convierte el array de datos en una cadena JSON
    $registro_json = json_encode($registro);
    echo "console.log('Datos del registro:', " . $registro_json . ");";
    ?>
</script>