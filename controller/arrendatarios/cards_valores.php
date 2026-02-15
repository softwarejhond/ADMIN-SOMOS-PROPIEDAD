<?php
require_once __DIR__ . '/../conexion.php';

$username = $_SESSION['username'] ?? null;
$info = [
    'nombre_inquilino' => '',
    'codigo' => '',
    'fecha_ingreso' => '',
    'ultima_renovacion' => ''
];

if ($username) {
    $sql = "SELECT nombre_inquilino, codigo, fecha AS fecha_ingreso, vigenciaContrato AS ultima_renovacion, valor_canon
            FROM proprieter
            WHERE doc_inquilino = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($nombre, $codigo, $fecha_ingreso, $ultima_renovacion, $valor_canon);
    if ($stmt->fetch()) {
        $info['nombre_inquilino'] = $nombre;
        $info['codigo'] = $codigo;
        $info['fecha_ingreso'] = $fecha_ingreso;
        $info['ultima_renovacion'] = $ultima_renovacion;
        $info['valor_canon'] = $valor_canon;
    }
    $stmt->close();
}

$porcentaje_aumento = null;
$fecha_incremento = null;
$nuevo_valor_canon = null;

// Obtener porcentaje de aumento desde la tabla porcentajeaumento
$sqlPorcentaje = "SELECT porcentaje FROM porcentajeaumento WHERE id = 1";
$resultPorcentaje = $conn->query($sqlPorcentaje);
if ($resultPorcentaje && $rowPorcentaje = $resultPorcentaje->fetch_assoc()) {
    $porcentaje_aumento = floatval($rowPorcentaje['porcentaje']);
}

// Calcular fecha de incremento y nuevo valor canon
if (!empty($info['fecha_ingreso']) && $porcentaje_aumento !== null && isset($info['valor_canon'])) {
    $fecha_incremento = date('d/m/Y', strtotime($info['fecha_ingreso'] . ' +1 year'));
    $nuevo_valor_canon = $info['valor_canon'] * (1 + ($porcentaje_aumento / 100));
}

// Obtener detalles de reportes pendientes
$reportes_pendientes = [];
$suma_reportes_pendientes = 0;
if (!empty($info['codigo'])) {
    $sqlReportes = "SELECT codigoReporte, situacionReportada, totalPagar FROM report WHERE codigo_propietario = ? AND pagado = 0";
    $stmtReportes = $conn->prepare($sqlReportes);
    $stmtReportes->bind_param("i", $info['codigo']);
    $stmtReportes->execute();
    $stmtReportes->bind_result($codigoReporte, $situacion, $totalPagar);
    while ($stmtReportes->fetch()) {
        $reportes_pendientes[] = [
            'codigo' => $codigoReporte,
            'situacion' => $situacion,
            'total' => $totalPagar,
            'total_formateado' => '$' . number_format($totalPagar, 0, ',', '.')
        ];
        $suma_reportes_pendientes += $totalPagar;
    }
    $stmtReportes->close();
}
$saldo_actual = ($info['valor_canon'] ?? 0) + $suma_reportes_pendientes;
?>

<div class="container-fluid px-3">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 pt-5 justify-content-center align-items-stretch">

        <!-- Card 1: Saldo Actual -->
        <div class="col d-flex">
            <div class="card h-100 w-100">
                <div class="card-header bg-indigo-dark text-white d-flex align-items-center gap-2">
                    <i class="fas fa-wallet fa-lg"></i>
                    <h5 class="mb-0">Saldo Actual</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <p class="h2 fw-bold mb-1" style="cursor: pointer;" onclick="mostrarDesgloseSaldo()">
                        <?= isset($info['codigo']) ? '$' . number_format($saldo_actual, 0, ',', '.') : 'Sin registro' ?>
                    </p>
                    <p class="text-muted small mb-0">En tiempo real</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Valor canon -->
        <div class="col d-flex">
            <div class="card h-100 w-100">
                <div class="card-header bg-indigo-dark text-white d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    <h5 class="mb-0">Valor canon</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <p class="h2 fw-bold mb-1">
                        <?= isset($info['valor_canon']) ? '$' . number_format($info['valor_canon'], 0, ',', '.') : 'Sin registro' ?>
                    </p>
                    <p class="text-muted small mb-0">Canon de arriendo actual</p>
                </div>
            </div>
        </div>

        <!-- Card 3: Próximo Incremento -->
        <div class="col d-flex">
            <div class="card h-100 w-100">
                <div class="card-header bg-indigo-dark text-white d-flex align-items-center gap-2">
                    <i class="fas fa-chart-line fa-lg"></i>
                    <h6 class="mb-0">Próximo Incremento</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <p class="mb-1">
                        <strong>Fecha:</strong>
                        <?= $fecha_incremento ? $fecha_incremento : 'Sin registro' ?>
                    </p>
                    <p class="mb-0">
                        <strong>Nuevo valor:</strong>
                        <?= $nuevo_valor_canon ? '$' . number_format($nuevo_valor_canon, 0, ',', '.') : 'Sin registro' ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 4: Días de Pago -->
        <div class="col d-flex">
            <div class="card h-100 w-100">
                <div class="card-header bg-indigo-dark text-white d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                    <h5 class="mb-0">Días de Pago</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <p class="h4 mb-0">Del <strong>1 al 5</strong></p>
                    <p class="text-muted small mb-0">de cada mes</p>
                </div>
            </div>
        </div>

        <!-- Card 5: FAQ Carousel -->
        <div class="col d-flex">
            <div class="card h-100 w-100">
                <div class="card-header bg-indigo-dark text-white d-flex align-items-center gap-2">
                    <i class="fas fa-question-circle fa-lg"></i>
                    <h5 class="mb-0">Preguntas Frecuentes</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center p-2">
                    <div id="faqCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">


                        <!-- Slides -->
                        <div class="carousel-inner text-center">
                            <div class="carousel-item active">
                                <p class="mb-1 small"><strong>¿Cómo pago mi canon?</strong></p>
                            </div>
                            <div class="carousel-item">
                                <p class="mb-1 small"><strong>¿Cómo reporto una reparación?</strong></p>
                            </div>
                            <div class="carousel-item">
                                <p class="mb-1 small"><strong>¿Cuándo es el incremento?</strong></p>
                            </div>
                            <div class="carousel-item">
                                <p class="mb-1 small"><strong>¿Cómo termino el contrato?</strong></p>
                            </div>
                        </div>

                        <!-- Indicators -->
                        <div class="carousel-indicators position-relative mb-2" style="position: relative; margin-bottom: 0;">
                            <button type="button" data-bs-target="#faqCarousel" data-bs-slide-to="0" class="active bg-primary" style="width: 8px; height: 8px; border-radius: 50%; margin: 0 2px;"></button>
                            <button type="button" data-bs-target="#faqCarousel" data-bs-slide-to="1" class="bg-primary" style="width: 8px; height: 8px; border-radius: 50%; margin: 0 2px;"></button>
                            <button type="button" data-bs-target="#faqCarousel" data-bs-slide-to="2" class="bg-primary" style="width: 8px; height: 8px; border-radius: 50%; margin: 0 2px;"></button>
                            <button type="button" data-bs-target="#faqCarousel" data-bs-slide-to="3" class="bg-primary" style="width: 8px; height: 8px; border-radius: 50%; margin: 0 2px;"></button>
                        </div>

                        <!-- Controls -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#faqCarousel" data-bs-slide="prev" style="width: 20px; left: -10px;">
                            <i class="fas fa-chevron-left text-black" style="font-size: 0.8rem;"></i>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#faqCarousel" data-bs-slide="next" style="width: 20px; right: -10px;">
                            <i class="fas fa-chevron-right text-black" style="font-size: 0.8rem;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Barra de información del inquilino -->
<div class="container-fluid px-3 mt-4 mb-5 pb-5">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-center p-3 rounded bg-teal-dark" style="min-height: 140px;">
                <!-- Información del inquilino (lado izquierdo) -->
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex flex-column">
                        <div class="mb-1">
                            <span class="fw-bold small me-1 text-white">NOMBRE DEL INQUILINO</span>
                            <span class="small text-white">- <?= htmlspecialchars($info['nombre_inquilino']) ?></span>
                        </div>
                        <div class="mb-1">
                            <span class="fw-bold small me-1 text-white">CÓDIGO DE PROPIEDAD</span>
                            <span class="small text-white">- <?= htmlspecialchars($info['codigo']) ?></span>
                        </div>
                        <div class="mb-1">
                            <span class="fw-bold small me-1 text-white">FECHA DE INGRESO</span>
                            <span class="small text-white">- <?= !empty($info['fecha_ingreso']) ? date('d/m/Y', strtotime($info['fecha_ingreso'])) : 'Sin registro' ?></span>
                        </div>
                        <div>
                            <span class="fw-bold small me-1 text-white">ÚLTIMA FECHA DE RENOVACIÓN</span>
                            <span class="small text-white">- <?= !empty($info['ultima_renovacion']) ? htmlspecialchars($info['ultima_renovacion']) : 'Sin registro' ?></span>
                        </div>
                    </div>
                </div>
                <!-- Botones de acción (lado derecho) -->
                <div class="col-12 col-md-6 d-flex justify-content-center">
                    <div class="d-flex flex-wrap justify-content-center gap-3 w-100">
                        <!-- Botón Certificado -->
                        <button type="button" class="btn p-0 border-0 bg-transparent position-relative"
                            onclick="generarCertificado()"
                            data-bs-toggle="popover"
                            data-bs-trigger="hover focus"
                            data-bs-placement="top"
                            data-bs-content="Descargar certificado de residente">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 bg-magenta-dark" style="width: 75px; height: 75px;">
                                    <i class="fas fa-file-alt fa-2x text-white"></i>
                                </div>
                                <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">CERTIFICADO</span>
                            </div>
                        </button>
                        <!-- Botón Información -->
                        <a href="https://somospropiedad.com/admin/detalle_inmueble.php?codigo=<?= urlencode($info['codigo']) ?>"
                            class="btn p-0 border-0 bg-transparent position-relative"
                            data-bs-toggle="popover"
                            data-bs-trigger="hover focus"
                            data-bs-placement="top"
                            data-bs-content="Ver información de vivienda"
                            target="_blank" rel="noopener">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 bg-magenta-dark" style="width: 75px; height: 75px;">
                                    <i class="fas fa-home fa-2x text-white"></i>
                                </div>
                                <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">INFORMACIÓN</span>
                            </div>
                        </a>
                        <!-- Botón Pagos -->
                        <a href="https://gateway2.tucompra.com.co/sites/somospropiedad/facturacion.xhtml"
                            class="btn p-0 border-0 bg-transparent position-relative"
                            data-bs-toggle="popover"
                            data-bs-trigger="hover focus"
                            data-bs-placement="top"
                            data-bs-content="Gestionar pagos y métodos de pago disponibles"
                            target="_blank" rel="noopener">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 bg-magenta-dark" style="width: 75px; height: 75px;">
                                    <i class="fas fa-dollar-sign fa-2x text-white"></i>
                                </div>
                                <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">PAGOS</span>
                            </div>
                        </a>
                        <!-- Botón Guías -->
                        <button type="button" class="btn p-0 border-0 bg-transparent position-relative"
                            data-bs-toggle="popover"
                            data-bs-trigger="hover focus"
                            data-bs-placement="top"
                            data-bs-content="Videos explicativos y tutoriales">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 bg-magenta-dark" style="width: 75px; height: 75px;">
                                    <i class="fas fa-video fa-2x text-white"></i>
                                </div>
                                <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">GUÍAS</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function(popoverTriggerEl) {
            new bootstrap.Popover(popoverTriggerEl, {
                trigger: 'hover focus',
                placement: 'top',
                container: 'body'
            });
        });
    });

    function mostrarDesgloseSaldo() {
        const valorCanon = <?= json_encode(isset($info['valor_canon']) ? '$' . number_format($info['valor_canon'], 0, ',', '.') : 'Sin registro') ?>;
        const reportes = <?= json_encode($reportes_pendientes) ?>;
        const totalSaldo = <?= json_encode(isset($info['codigo']) ? '$' . number_format($saldo_actual, 0, ',', '.') : 'Sin registro') ?>;

        let tablaHtml = '';
        if (reportes.length > 0) {
            tablaHtml = `
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Código de Reporte</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Situación Reportada</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Total a Pagar</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            reportes.forEach(reporte => {
                tablaHtml += `
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">${reporte.codigo}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">${reporte.situacion}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${reporte.total_formateado}</td>
                    </tr>
                `;
            });
            tablaHtml += `</tbody></table>`;
        } else {
            tablaHtml = '<p style="margin-top: 10px;">No hay deudas por reparaciones pendientes.</p>';
        }

        const htmlContent = `
            <div style="text-align: left;">
                <p><strong>Canon de arriendo:</strong> ${valorCanon}</p>
                <p><strong>Pendientes por reparaciones:</strong></p>
                ${tablaHtml}
                <hr style="margin: 20px 0;">
                <p><strong>Total:</strong> ${totalSaldo}</p>
            </div>
        `;

        Swal.fire({
            title: 'Desglose del Saldo Actual',
            html: htmlContent,
            icon: 'info',
            confirmButtonText: 'Cerrar',
            width: '600px' // Ajusta el ancho para que quepa la tabla
        });
    }

    function generarCertificado() {
        Swal.fire({
            title: 'Generando certificado...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('controller/arrendatarios/generar_certificado.php?codigo=<?= urlencode($info['codigo']) ?>', {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                // Decodificar base64 y crear blob para descarga
                const byteCharacters = atob(data);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], {
                    type: 'application/pdf'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'certificado_residencia_<?= $info['codigo'] ?>.pdf';
                a.click();
                URL.revokeObjectURL(url);

                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Certificado generado y descargado.',
                    icon: 'success'
                });
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo generar el certificado.',
                    icon: 'error'
                });
            });
    }
</script>