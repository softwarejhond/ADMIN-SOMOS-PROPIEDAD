<?php
if (!isset($codigo_propiedad) || !isset($propiedad)) {
    echo "<p>Error: No se pudo cargar la información de la propiedad.</p>";
    return;
}

// Obtener el nombre del departamento
$departamento_nombre = 'N/A';
if (!empty($propiedad['departamento'])) {
    $query_dept = "SELECT departamento FROM departamentos WHERE id_departamento = ?";
    $stmt_dept = $conn->prepare($query_dept);
    $stmt_dept->bind_param("i", $propiedad['departamento']);
    $stmt_dept->execute();
    $result_dept = $stmt_dept->get_result();
    if ($row_dept = $result_dept->fetch_assoc()) {
        $departamento_nombre = $row_dept['departamento'];
    }
    $stmt_dept->close();
}

// Obtener el nombre del municipio
$municipio_nombre = 'N/A';
if (!empty($propiedad['Municipio'])) {
    $query_mun = "SELECT municipio FROM municipios WHERE id_municipio = ?";
    $stmt_mun = $conn->prepare($query_mun);
    $stmt_mun->bind_param("i", $propiedad['Municipio']);
    $stmt_mun->execute();
    $result_mun = $stmt_mun->get_result();
    if ($row_mun = $result_mun->fetch_assoc()) {
        $municipio_nombre = $row_mun['municipio'];
    }
    $stmt_mun->close();
}

// Obtener las fotos de la propiedad
$fotos_dir = "fotos/" . $codigo_propiedad;
$fotos = array();
if (is_dir($fotos_dir)) {
    $files = scandir($fotos_dir);
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
            $fotos[] = $file;
        }
    }
}

$valorCanonFormatted = number_format($propiedad['valor_canon'], 0, ',', '.');
?>

<div class="card shadow-lg border-0 rounded-3 overflow-hidden">
    <!-- Carrusel de Imágenes -->
    <?php if (!empty($fotos)): ?>
        <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($fotos as $index => $foto): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo $fotos_dir . '/' . $foto; ?>" class="d-block w-100" alt="Foto de la propiedad" style="max-height: 550px; object-fit: contain;">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($fotos) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                    <i class="bi bi-arrow-left-circle text-indigo-dark" style="font-size: 3rem;" aria-hidden="true"></i>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                    <i class="bi bi-arrow-right-circle text-indigo-dark" style="font-size: 3rem;" aria-hidden="true"></i>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="text-center p-5 bg-light">
            <i class="bi bi-image-alt display-1 text-muted"></i>
            <p class="mt-3">No hay fotos disponibles para esta propiedad.</p>
        </div>
    <?php endif; ?>

    <!-- Cuerpo de la tarjeta con detalles -->
    <div class="card-body p-4 p-md-5 d-flex flex-column">
        <!-- Detalles y Características -->
        <div>
            <h2 class="card-title text-indigo-dark fw-bold mb-1"><?php echo htmlspecialchars(ucwords(strtolower($propiedad['tipoInmueble']))); ?></h2>
            <p class="text-muted fs-5">Código: <?php echo htmlspecialchars($propiedad['codigo']); ?></p>
            <span class="text-muted fs-5">Valor:</span>
            <span class="badge bg-indigo-dark text-white fs-5 animate__animated animate__bounce my-3">$<?php echo $valorCanonFormatted; ?></span>

            <div class="d-flex align-items-center my-3">
                <i class="bi bi-geo-alt-fill fs-4 text-danger me-2"></i>
                <span class="fs-5"><?php echo htmlspecialchars(ucwords(strtolower($propiedad['direccion'])) . ', ' . $municipio_nombre); ?></span>
            </div>

            <hr class="my-4">

            <h4 class="mb-4"><i class="bi bi-list-check"></i> Características Principales</h4>
            <div class="row row-cols-2 row-cols-sm-3 g-4">
                <div class="col d-flex align-items-start">
                    <i class="bi bi-bounding-box fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Área:</strong><br><?php echo htmlspecialchars($propiedad['area']); ?> m²</div>
                </div>
                <div class="col d-flex align-items-start">
                    <i class="bi bi-building fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Nivel:</strong><br><?php echo htmlspecialchars($propiedad['nivel_piso']); ?></div>
                </div>
                <div class="col d-flex align-items-start">
                    <i class="bi bi-door-closed fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Alcobas:</strong><br><?php echo htmlspecialchars($propiedad['alcobas']); ?></div>
                </div>
                <div class="col d-flex align-items-start">
                    <i class="bi bi-droplet fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Baños:</strong><br><?php echo htmlspecialchars($propiedad['servicios']); ?></div>
                </div>
                <div class="col d-flex align-items-start">
                    <i class="bi bi-car-front fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Parqueadero:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['parqueadero']))); ?></div>
                </div>
                <div class="col d-flex align-items-start">
                    <i class="bi bi-tree fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Patio:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['patio']))); ?></div>
                </div>
                <div class="col d-flex align-items-start">
                    <i class="bi bi-fire fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Cocina:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['cocina']))); ?></div>
                </div>
                <div class="col d-flex align-items-start">
                    <i class="bi bi-person-standing-dress fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Closets:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['closet']))); ?></div>
                </div>
            </div>

            <hr class="my-4">

            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Características Adicionales</h4>
            <div class="row row-cols-2 row-cols-sm-3 g-4">
                <div class="col d-flex align-items-start">
                    <i class="bi bi-layers fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Estrato:</strong><br><?php echo htmlspecialchars($propiedad['estrato']); ?></div>
                </div>

                <div class="col d-flex align-items-start">
                    <i class="bi bi-map fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Departamento:</strong><br><?php echo htmlspecialchars($departamento_nombre); ?></div>
                </div>

                <div class="col d-flex align-items-start">
                    <i class="bi bi-geo-alt fs-4 text-indigo-dark me-3"></i>
                    <div><strong>Municipio:</strong><br><?php echo htmlspecialchars($municipio_nombre); ?></div>
                </div>

                <?php if (!empty($propiedad['terraza']) && $propiedad['terraza'] !== 'no'): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-sun fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Terraza:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['terraza']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['ascensor']) && $propiedad['ascensor'] !== 'no'): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-arrow-up-square fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Ascensor:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['ascensor']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['cuarto_util']) && $propiedad['cuarto_util'] !== 'no'): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-box fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Cuarto Útil:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['cuarto_util']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['CuartoServicios']) && $propiedad['CuartoServicios'] !== 'no'): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-house-gear fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Cuarto de Servicios:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['CuartoServicios']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['ZonaRopa']) && $propiedad['ZonaRopa'] !== 'no'): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-basket fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Zona de Ropa:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['ZonaRopa']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['vista'])): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-eye fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Vista:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['vista']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['sala']) && $propiedad['sala'] !== 'no'): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-house fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Sala:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['sala']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['sala_comedor']) && $propiedad['sala_comedor'] !== 'no'): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-house-heart fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Sala Comedor:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['sala_comedor']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['comedor']) && $propiedad['comedor'] !== 'no'): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-table fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Comedor:</strong><br><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['comedor']))); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['TelefonoInmueble'])): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-telephone fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Teléfono Inmueble:</strong><br><?php echo htmlspecialchars($propiedad['TelefonoInmueble']); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($propiedad['servicios_publicos'])): ?>
                    <div class="col d-flex align-items-start">
                        <i class="bi bi-lightning-charge fs-4 text-indigo-dark me-3"></i>
                        <div><strong>Servicios Públicos:</strong><br><?php echo htmlspecialchars(ucwords(strtolower($propiedad['servicios_publicos']))); ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($propiedad['otras_caracteristicas'])): ?>
                <hr class="my-4">
                <h4 class="mb-3"><i class="bi bi-info-circle"></i> Otras Características</h4>
                <p class="text-muted fs-6 lh-lg bg-light p-3 rounded"><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['otras_caracteristicas']))); ?></p>
            <?php endif; ?>
        </div>

        <hr class="my-5">

        <!-- Sección de Precio y Acciones -->
        <div class="text-center">
            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-2 fs-6 mb-3"><?php echo htmlspecialchars(ucfirst(strtolower($propiedad['condicion']))); ?></span>

            <div class="d-flex justify-content-center flex-wrap gap-3 mt-4 pt-2">
                <button class="btn bg-lime-dark btn-lg fw-bold px-5" onclick="contactarAsesor()">
                    <i class="bi bi-whatsapp me-2"></i>Whatsapp
                </button>
                <button class="btn bg-indigo-dark text-white btn-lg fw-bold px-5" onclick="compartirPropiedad()">
                    <i class="bi bi-share me-2"></i> Compartir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function compartirPropiedad() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo htmlspecialchars($propiedad['tipoInmueble'] . ' - ' . $propiedad['codigo']); ?>',
                text: '¡Mira esta increíble propiedad que encontré!',
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('¡Enlace de la propiedad copiado al portapapeles!');
            });
        }
    }

    function contactarAsesor() {
        const mensaje = `Hola, estoy interesado en la propiedad código ${<?php echo json_encode($propiedad['codigo']); ?>} (<?php echo htmlspecialchars($propiedad['tipoInmueble']); ?>) ubicada en <?php echo htmlspecialchars($propiedad['direccion']); ?>.`;
        const whatsappUrl = `https://wa.me/573206716990?text=${encodeURIComponent(mensaje)}`;
        window.open(whatsappUrl, '_blank');
    }
</script>