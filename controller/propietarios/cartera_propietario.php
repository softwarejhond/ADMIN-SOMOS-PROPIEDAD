<?php
require_once __DIR__ . "/../conexion.php";

$doc_propietario = $_SESSION['username'] ?? '';

// Filtros del propietario
$filtroMesProp = $_GET['mes_cartera'] ?? '';
$filtroAnioProp = $_GET['anio_cartera'] ?? date('Y');

// Obtener movimientos del propietario
$whereProp = "c.nit_propietario = ?";
$paramsProp = [$doc_propietario];
$typesProp = "s";

if (!empty($filtroMesProp)) {
    $whereProp .= " AND c.mes = ?";
    $paramsProp[] = $filtroMesProp;
    $typesProp .= "s";
}
if (!empty($filtroAnioProp)) {
    $whereProp .= " AND c.anio = ?";
    $paramsProp[] = $filtroAnioProp;
    $typesProp .= "i";
}

$sqlCarteraProp = "SELECT c.*, csp.direccion, csp.ciudad AS tipoInmueble 
                   FROM cartera_propietario c 
                   LEFT JOIN contratos_somos_propiedad csp ON c.codigo_inmueble = csp.no_contrato 
                   WHERE $whereProp 
                   ORDER BY c.codigo_inmueble, c.es_giro ASC, c.id ASC";
$stmtCarteraProp = $conn->prepare($sqlCarteraProp);
$stmtCarteraProp->bind_param($typesProp, ...$paramsProp);
$stmtCarteraProp->execute();
$resCarteraProp = $stmtCarteraProp->get_result();

// Agrupar por inmueble
$carteraAgrupada = [];
$totalesProp = ['debito' => 0, 'credito' => 0];
$nombreTercero = '';

while ($mov = $resCarteraProp->fetch_assoc()) {
    $codigo = $mov['codigo_inmueble'];
    if (empty($nombreTercero)) $nombreTercero = $mov['nombre_tercero'];
    
    if (!isset($carteraAgrupada[$codigo])) {
        $carteraAgrupada[$codigo] = [
            'direccion' => $mov['direccion'] ?? 'N/A',
            'tipo' => $mov['tipoInmueble'] ?? '',
            'movimientos' => [],
            'subtotalDebito' => 0,
            'subtotalCredito' => 0
        ];
    }
    $carteraAgrupada[$codigo]['movimientos'][] = $mov;
    $carteraAgrupada[$codigo]['subtotalDebito'] += $mov['debito'];
    $carteraAgrupada[$codigo]['subtotalCredito'] += $mov['credito'];
    $totalesProp['debito'] += $mov['debito'];
    $totalesProp['credito'] += $mov['credito'];
}

$mesesProp = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
?>

<!-- Sección Cartera del Propietario -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-journal-text text-primary"></i> Estado de Cuenta - Cartera
                    </h5>
                    <!-- Filtros inline -->
                    <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
                        <div>
                            <select name="mes_cartera" class="form-select form-select-sm" style="min-width: 140px;">
                                <option value="">Todos los meses</option>
                                <?php foreach ($mesesProp as $numMes => $nombreMes): ?>
                                <option value="<?= $numMes ?>" <?= $filtroMesProp == $numMes ? 'selected' : '' ?>><?= $nombreMes ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <select name="anio_cartera" class="form-select form-select-sm" style="min-width: 100px;">
                                <?php for ($a = date('Y'); $a >= 2020; $a--): ?>
                                <option value="<?= $a ?>" <?= $filtroAnioProp == $a ? 'selected' : '' ?>><?= $a ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-dark">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body vstack w-100 p-0">
                <?php if (empty($carteraAgrupada)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x fs-1"></i>
                        <p class="mt-2">No hay movimientos de cartera para el período seleccionado.</p>
                    </div>
                <?php else: ?>
                    <!-- Encabezado cuenta -->
                    <div class="px-3 py-2 bg-light border-bottom w-100">
                        <small class="text-muted fw-semibold">
                            <i class="bi bi-bank"></i> Cuenta: 28150501 (Total Agrupación) &mdash; 
                            <?= htmlspecialchars($doc_propietario) ?> - <?= htmlspecialchars($nombreTercero) ?>
                        </small>
                    </div>

                    <div class="table-responsive w-100">
                        <table class="table table-sm table-hover mb-0 align-middle w-100" style="font-size: 0.85rem;">
                            <thead style="background-color: #d4edda;">
                                <tr>
                                    <th style="min-width: 90px;">Nit</th>
                                    <th style="min-width: 200px;">Nombre Tercero</th>
                                    <th style="min-width: 90px;">Fecha</th>
                                    <th style="min-width: 70px;">Mes</th>
                                    <th>Detalle</th>
                                    <th class="text-end" style="min-width: 120px;">Débito</th>
                                    <th class="text-end" style="min-width: 120px;">Crédito</th>
                                    <th class="text-center" style="min-width: 60px;">No. Inm</th>
                                    <th style="min-width: 50px;">Año</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carteraAgrupada as $codInm => $dataInm): ?>
                                    <?php foreach ($dataInm['movimientos'] as $mov): ?>
                                        <?php 
                                            $rowClass = '';
                                            $rowStyle = '';
                                            if ($mov['es_giro']) {
                                                $rowClass = 'fw-bold';
                                                $rowStyle = 'background-color: #c8e6c9;';
                                            }
                                        ?>
                                        <tr class="<?= $rowClass ?>" style="<?= $rowStyle ?>">
                                            <td><?= htmlspecialchars($mov['nit_propietario']) ?></td>
                                            <td><?= htmlspecialchars($mov['nombre_tercero']) ?></td>
                                            <td><?= date('j/m/Y', strtotime($mov['fecha'])) ?></td>
                                            <td><?= $mov['anio'] ?> - <?= $mesesProp[$mov['mes']] ?? $mov['mes'] ?></td>
                                            <td>
                                                <?php if ($mov['es_giro']): ?>
                                                    <strong><i class="bi bi-cash-coin text-success"></i> <?= htmlspecialchars($mov['detalle']) ?></strong>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($mov['detalle']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <?= $mov['debito'] > 0 ? '$ ' . number_format($mov['debito'], 2, ',', '.') : '$ 0,00' ?>
                                            </td>
                                            <td class="text-end">
                                                <?= $mov['credito'] > 0 ? '$ ' . number_format($mov['credito'], 2, ',', '.') : '$ 0,00' ?>
                                            </td>
                                            <td class="text-center"><?= htmlspecialchars($mov['codigo_inmueble']) ?></td>
                                            <td><?= htmlspecialchars($mov['anio']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>

                                <!-- Totales -->
                                <tr class="fw-bold" style="background-color: #fff9c4; border-top: 2px solid #333;">
                                    <td colspan="5" class="text-end">TOTALES:</td>
                                    <td class="text-end">$ <?= number_format($totalesProp['debito'], 2, ',', '.') ?></td>
                                    <td class="text-end">$ <?= number_format($totalesProp['credito'], 2, ',', '.') ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen -->
                    <div class="px-3 py-3 bg-light border-top w-100">
                        <div class="row g-3 w-100">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;background-color:rgba(0,128,0,0.1)">
                                        <i class="bi bi-arrow-up-circle text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Total Créditos (a favor)</small>
                                        <strong class="text-success">$ <?= number_format($totalesProp['credito'], 2, ',', '.') ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;background-color:rgba(255,0,0,0.1)">
                                        <i class="bi bi-arrow-down-circle text-danger"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Total Débitos (retenciones)</small>
                                        <strong class="text-danger">$ <?= number_format($totalesProp['debito'], 2, ',', '.') ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;background-color:rgba(0,0,255,0.1)">
                                        <i class="bi bi-wallet2 text-primary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Neto (Créditos - Débitos)</small>
                                        <?php $neto = $totalesProp['credito'] - $totalesProp['debito']; ?>
                                        <strong class="<?= $neto >= 0 ? 'text-primary' : 'text-danger' ?>">
                                            $ <?= number_format($neto, 2, ',', '.') ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
