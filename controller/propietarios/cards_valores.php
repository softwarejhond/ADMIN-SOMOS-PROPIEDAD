<?php
require_once "conexion.php";


// Obtener documento del propietario logueado
$doc_propietario = $_SESSION['username'] ?? '';

// Consultar inmuebles del propietario desde la tabla de contratos exclusiva
$sqlInmuebles = "SELECT COUNT(*) as total FROM contratos_somos_propiedad WHERE cedula_propietario = ?";
$stmtInm = $conn->prepare($sqlInmuebles);
$stmtInm->bind_param("s", $doc_propietario);
$stmtInm->execute();
$resInmuebles = $stmtInm->get_result()->fetch_assoc();
$totalInmuebles = $resInmuebles['total'] ?? 0;

// Inmuebles ocupados vs desocupados
$sqlOcupados = "SELECT COUNT(*) as total FROM contratos_somos_propiedad WHERE cedula_propietario = ? AND cedula_arrendatario != '' AND cedula_arrendatario IS NOT NULL";
$stmtOc = $conn->prepare($sqlOcupados);
$stmtOc->bind_param("s", $doc_propietario);
$stmtOc->execute();
$resOcupados = $stmtOc->get_result()->fetch_assoc();
$inmueblesOcupados = $resOcupados['total'] ?? 0;
$inmueblesDesocupados = $totalInmuebles - $inmueblesOcupados;

// Total valor canon mensual
$sqlCanon = "SELECT SUM(vr_canon) as totalCanon FROM contratos_somos_propiedad WHERE cedula_propietario = ? AND cedula_arrendatario != '' AND cedula_arrendatario IS NOT NULL";
$stmtCanon = $conn->prepare($sqlCanon);
$stmtCanon->bind_param("s", $doc_propietario);
$stmtCanon->execute();
$resCanon = $stmtCanon->get_result()->fetch_assoc();
$totalCanon = $resCanon['totalCanon'] ?? 0;

// Listado completo de contratos del propietario (uno por fila)
$sqlContratos = "SELECT no_contrato, direccion, ciudad, vr_canon, arrendatario,
                         cedula_arrendatario, aseguradora, no_solicitud, multiple
                  FROM contratos_somos_propiedad
                  WHERE cedula_propietario = ?
                  ORDER BY no_contrato ASC";
$stmtContratos = $conn->prepare($sqlContratos);
$stmtContratos->bind_param("s", $doc_propietario);
$stmtContratos->execute();
$resContratos = $stmtContratos->get_result();

// Nombre del propietario
$sqlNombre = "SELECT nombre FROM users WHERE username = ? LIMIT 1";
$stmtNom = $conn->prepare($sqlNombre);
$stmtNom->bind_param("s", $doc_propietario);
$stmtNom->execute();
$resNom = $stmtNom->get_result()->fetch_assoc();
$nombrePropietario = $resNom['nombre'] ?? 'Propietario';
?>

<div class="container-fluid px-4 mb-4 pt-5 pb-5">
    <!-- Saludo -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark">
                <i class="bi bi-house-heart-fill text-primary"></i> 
                ¡Bienvenido, <?= htmlspecialchars($nombrePropietario) ?>!
            </h3>
            <p class="text-muted">Aquí tienes un resumen de tus propiedades e inversiones.</p>
        </div>
    </div>

    <!-- Cards principales -->
    <div class="row g-3 mb-4">
        <!-- Total Inmuebles -->
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #001f3f !important; border-left-style: solid !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Mis Inmuebles</p>
                        <h2 class="fw-bold mb-0" style="color:#001f3f"><?= $totalInmuebles ?></h2>
                        <small class="text-muted">Propiedades registradas</small>
                    </div>
                    <div class="ms-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px;background-color:rgba(0,31,63,0.1)">
                            <i class="bi bi-buildings fs-3" style="color:#001f3f"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inmuebles Ocupados -->
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #66cc00 !important; border-left-style: solid !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Ocupados</p>
                        <h2 class="fw-bold mb-0" style="color:#66cc00"><?= $inmueblesOcupados ?></h2>
                        <small class="text-muted">Con inquilino activo</small>
                    </div>
                    <div class="ms-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px;background-color:rgba(102,204,0,0.1)">
                            <i class="bi bi-person-check-fill fs-3" style="color:#66cc00"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inmuebles Desocupados -->
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #bf6900 !important; border-left-style: solid !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Desocupados</p>
                        <h2 class="fw-bold mb-0" style="color:#bf6900"><?= $inmueblesDesocupados ?></h2>
                        <small class="text-muted">Sin inquilino</small>
                    </div>
                    <div class="ms-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px;background-color:rgba(191,105,0,0.1)">
                            <i class="bi bi-house-exclamation fs-3" style="color:#bf6900"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingreso Mensual -->
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #006d68 !important; border-left-style: solid !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Canon Mensual</p>
                        <h2 class="fw-bold mb-0" style="color:#006d68">$<?= number_format($totalCanon, 0, ',', '.') ?></h2>
                        <small class="text-muted">Ingreso estimado</small>
                    </div>
                    <div class="ms-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px;background-color:rgba(0,109,104,0.1)">
                            <i class="bi bi-cash-stack fs-3" style="color:#006d68"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Listado de Inmuebles del Propietario -->
    <div class="row mb-4 w-100">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-list-ul text-primary"></i> Mis Inmuebles</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive w-100">
                        <table class="table table-hover mb-0 align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Contrato</th>
                                    <th>Dirección</th>
                                    <th>Ciudad</th>
                                    <th>Arrendatario</th>
                                    <th class="text-end">Vr Canon</th>
                                    <th>Aseguradora</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resContratos->num_rows > 0): ?>
                                <?php while ($contrato = $resContratos->fetch_assoc()):
                                    $ocupado = !empty($contrato['cedula_arrendatario']);
                                    $estadoBadge = $ocupado
                                        ? '<span class="badge bg-success rounded-pill">Ocupado</span>'
                                        : '<span class="badge bg-warning text-dark rounded-pill">Desocupado</span>';
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($contrato['no_contrato']) ?></strong></td>
                                    <td><?= htmlspecialchars($contrato['direccion']) ?></td>
                                    <td><?= htmlspecialchars($contrato['ciudad']) ?></td>
                                    <td>
                                        <?php if ($ocupado): ?>
                                            <?= htmlspecialchars($contrato['arrendatario']) ?><br>
                                            <small class="text-muted"><?= htmlspecialchars($contrato['cedula_arrendatario']) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end"><strong>$ <?= number_format((float)$contrato['vr_canon'], 0, ',', '.') ?></strong></td>
                                    <td><?= htmlspecialchars($contrato['aseguradora']) ?></td>
                                    <td><?= $estadoBadge ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1"></i><br>
                                        No tienes inmuebles registrados
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes recientes -->
    <!-- <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-wrench-adjustable text-danger"></i> Reportes Recientes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive w-100">
                        <table class="table table-hover mb-0 align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Inmueble</th>
                                    <th>Situación</th>
                                    <th>Valor Factura</th>
                                    <th>Estado</th>
                                    <th>Pagado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlRepRecientes = "SELECT r.*, p.direccion, p.codigo as cod_inmueble 
                                                    FROM report r 
                                                    INNER JOIN proprieter p ON r.codigo_propietario = p.codigo 
                                                    WHERE p.doc_propietario = ? 
                                                    ORDER BY r.fechaCreacion DESC LIMIT 10";
                                $stmtRepR = $conn->prepare($sqlRepRecientes);
                                $stmtRepR->bind_param("s", $doc_propietario);
                                $stmtRepR->execute();
                                $resRepRecientes = $stmtRepR->get_result();
                                
                                if ($resRepRecientes->num_rows > 0):
                                    while ($reporte = $resRepRecientes->fetch_assoc()):
                                        // Determinar color del estado
                                        $estadoColor = match(strtolower($reporte['EstadoReporte'])) {
                                            'finalizado' => 'success',
                                            'en proceso', 'en curso' => 'warning',
                                            'pendiente' => 'danger',
                                            default => 'secondary'
                                        };
                                        $pagadoBadge = $reporte['pagado'] == 1 
                                            ? '<span class="badge bg-success">Sí</span>' 
                                            : '<span class="badge bg-danger">No</span>';
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($reporte['codigoReporte']) ?></strong></td>
                                    <td>#<?= htmlspecialchars($reporte['cod_inmueble']) ?> - <?= htmlspecialchars($reporte['direccion']) ?></td>
                                    <td><?= htmlspecialchars(mb_strimwidth($reporte['situacionReportada'], 0, 50, '...')) ?></td>
                                    <td>$<?= number_format($reporte['valorFactura'], 0, ',', '.') ?></td>
                                    <td><span class="badge bg-<?= $estadoColor ?> rounded-pill"><?= htmlspecialchars($reporte['EstadoReporte']) ?></span></td>
                                    <td><?= $pagadoBadge ?></td>
                                    <td><?= date('d/m/Y', strtotime($reporte['fechaCreacion'])) ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else: 
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-clipboard-check fs-1"></i><br>
                                        No hay reportes registrados
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!--
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge text-warning"></i> Accesos Rápidos</h5>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalCertificado">
                <div class="card border-0 shadow-sm text-center py-4 h-100 card-hover">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                        <p class="mt-2 mb-0 fw-semibold text-dark">Generar Certificado</p>
                        <small class="text-muted">Paz y salvo / Retención</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalReporte">
                <div class="card border-0 shadow-sm text-center py-4 h-100 card-hover">
                    <div class="card-body">
                        <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                        <p class="mt-2 mb-0 fw-semibold text-dark">Reportar Novedad</p>
                        <small class="text-muted">Daños o reparaciones</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalPerfil">
                <div class="card border-0 shadow-sm text-center py-4 h-100 card-hover">
                    <div class="card-body">
                        <i class="bi bi-person-gear fs-1 text-primary"></i>
                        <p class="mt-2 mb-0 fw-semibold text-dark">Mi Perfil</p>
                        <small class="text-muted">Datos personales y bancarios</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalHistorial">
                <div class="card border-0 shadow-sm text-center py-4 h-100 card-hover">
                    <div class="card-body">
                        <i class="bi bi-clock-history fs-1 text-info"></i>
                        <p class="mt-2 mb-0 fw-semibold text-dark">Historial de Pagos</p>
                        <small class="text-muted">Consultar movimientos</small>
                    </div>
                </div>
            </a>
        </div>
    </div> -->
</div>

<style>
.card-hover:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.card {
    transition: all 0.3s ease;
}
</style>