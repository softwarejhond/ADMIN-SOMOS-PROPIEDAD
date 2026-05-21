<?php
require_once __DIR__ . "/../conexion.php";

// Obtener todos los propietarios con inmuebles desde la tabla de contratos
$sqlPropietarios = "SELECT DISTINCT cedula_propietario AS doc_propietario, propietario AS nombre_propietario FROM contratos_somos_propiedad WHERE cedula_propietario != '' ORDER BY propietario";
$resPropietarios = $conn->query($sqlPropietarios);

// Filtros
$filtroPropietario = $_GET['propietario'] ?? '';
$filtroMes = $_GET['mes'] ?? '';
$filtroAnio = $_GET['anio'] ?? date('Y');

// Solo consultar si hay al menos un propietario seleccionado
$hayFiltroActivo = !empty($filtroPropietario);
$movimientosAgrupados = [];
$totalesGenerales = ['debito' => 0, 'credito' => 0];

if ($hayFiltroActivo) {
    $whereCartera = "c.nit_propietario = ?";
    $params = [$filtroPropietario];
    $types = "s";

    if (!empty($filtroMes)) {
        $whereCartera .= " AND c.mes = ?";
        $params[] = $filtroMes;
        $types .= "s";
    }
    if (!empty($filtroAnio)) {
        $whereCartera .= " AND c.anio = ?";
        $params[] = $filtroAnio;
        $types .= "i";
    }

    $sqlMovimientos = "SELECT c.*, csp.direccion, csp.ciudad AS tipoInmueble 
                       FROM cartera_propietario c 
                       LEFT JOIN contratos_somos_propiedad csp ON c.codigo_inmueble = csp.no_contrato 
                       WHERE $whereCartera 
                       ORDER BY c.nit_propietario, c.codigo_inmueble, c.es_giro ASC, c.id ASC";

    $stmtMov = $conn->prepare($sqlMovimientos);
    $stmtMov->bind_param($types, ...$params);
    $stmtMov->execute();
    $resMovimientos = $stmtMov->get_result();

    while ($mov = $resMovimientos->fetch_assoc()) {
        $nit = $mov['nit_propietario'];
        if (!isset($movimientosAgrupados[$nit])) {
            $movimientosAgrupados[$nit] = [
                'nombre' => $mov['nombre_tercero'],
                'inmuebles' => [],
                'totalDebito' => 0,
                'totalCredito' => 0
            ];
        }
        $codigo = $mov['codigo_inmueble'];
        if (!isset($movimientosAgrupados[$nit]['inmuebles'][$codigo])) {
            $movimientosAgrupados[$nit]['inmuebles'][$codigo] = [
                'direccion' => $mov['direccion'] ?? 'N/A',
                'tipo' => $mov['tipoInmueble'] ?? '',
                'movimientos' => [],
                'subtotalDebito' => 0,
                'subtotalCredito' => 0
            ];
        }
        $movimientosAgrupados[$nit]['inmuebles'][$codigo]['movimientos'][] = $mov;
        $movimientosAgrupados[$nit]['inmuebles'][$codigo]['subtotalDebito'] += $mov['debito'];
        $movimientosAgrupados[$nit]['inmuebles'][$codigo]['subtotalCredito'] += $mov['credito'];
        $movimientosAgrupados[$nit]['totalDebito'] += $mov['debito'];
        $movimientosAgrupados[$nit]['totalCredito'] += $mov['credito'];
        $totalesGenerales['debito'] += $mov['debito'];
        $totalesGenerales['credito'] += $mov['credito'];
    }
} // fin if hayFiltroActivo

// Meses para el select
$meses = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];

// Conceptos predefinidos
$conceptos = [
    'Canon de arrendamiento',
    'IVA ARRENDAMIENTO',
    'Comisión/Canon',
    'Iva/Comisión',
    'RETEFUENTE CANON',
    'RETEFUENTE COMISION',
    'Giro Renta'
];
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid px-3">
    <!-- Encabezado -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-journal-text text-primary"></i> Gestión de Cartera - Propietarios</h4>
                <p class="text-muted mb-0 small">Administre los movimientos de cartera, débitos y créditos por propietario e inmueble.</p>
            </div>
            <button class="btn bg-indigo-dark text-white" data-bs-toggle="modal" data-bs-target="#modalNuevoMovimiento">
                <i class="bi bi-plus-circle"></i> Nuevo Movimiento
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="admin_cartera.php" class="row g-2 align-items-end w-100">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Propietario</label>
                    <select name="propietario" id="filtroPropietarioSelect" class="form-select form-select-sm">
                        <option value="">-- Todos --</option>
                        <?php
                        $resPropietarios->data_seek(0);
                        while ($prop = $resPropietarios->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($prop['doc_propietario']) ?>" <?= $filtroPropietario == $prop['doc_propietario'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prop['doc_propietario']) ?> - <?= htmlspecialchars($prop['nombre_propietario']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Mes</label>
                    <select name="mes" class="form-select form-select-sm">
                        <option value="">-- Todos --</option>
                        <?php foreach ($meses as $numMes => $nombreMes): ?>
                        <option value="<?= $numMes ?>" <?= $filtroMes == $numMes ? 'selected' : '' ?>><?= $nombreMes ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Año</label>
                    <select name="anio" class="form-select form-select-sm">
                        <?php for ($a = date('Y'); $a >= 2020; $a--): ?>
                        <option value="<?= $a ?>" <?= $filtroAnio == $a ? 'selected' : '' ?>><?= $a ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm bg-magenta-dark text-white"><i class="bi bi-funnel"></i> Filtrar</button>
                    <a href="admin_cartera.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i> Limpiar</a>
                    <button type="button" class="btn btn-sm bg-teal-dark text-white" data-bs-toggle="modal" data-bs-target="#modalGenerarCartera">
                        <i class="bi bi-gear"></i> Generar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Cartera estilo informe -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <?php if (!$hayFiltroActivo): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-funnel fs-1"></i>
                    <p class="mt-2 mb-0">Seleccione un propietario para ver sus movimientos de cartera.</p>
                    <small>Use los filtros de arriba para buscar y seleccionar el propietario.</small>
                </div>
            <?php elseif (empty($movimientosAgrupados)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">No hay movimientos de cartera registrados para este propietario.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive w-100">
                    <table class="table table-sm table-bordered mb-0 align-middle" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th>Nit</th>
                                <th>Nombre Tercero</th>
                                <th>Fecha</th>
                                <th>Mes</th>
                                <th>Detalle</th>
                                <th class="text-end">Débito</th>
                                <th class="text-end">Crédito</th>
                                <th class="text-center">No. Inm</th>
                                <th>Año</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimientosAgrupados as $nit => $dataProp): ?>
                                <!-- Cabecera agrupación propietario -->
                                <tr class="table-secondary">
                                    <td colspan="10" class="fw-bold small">
                                        <i class="bi bi-person-fill"></i> Cuenta: <?= htmlspecialchars($dataProp['nombre']) ?> (<?= htmlspecialchars($nit) ?>)
                                    </td>
                                </tr>
                                <?php foreach ($dataProp['inmuebles'] as $codInm => $dataInm): ?>
                                    <?php foreach ($dataInm['movimientos'] as $mov): ?>
                                        <tr class="<?= $mov['es_giro'] ? 'table-success fw-bold' : '' ?>">
                                            <td><?= htmlspecialchars($mov['nit_propietario']) ?></td>
                                            <td><?= htmlspecialchars($mov['nombre_tercero']) ?></td>
                                            <td><?= date('j/m/Y', strtotime($mov['fecha'])) ?></td>
                                            <td><?= htmlspecialchars($mov['mes']) ?></td>
                                            <td>
                                                <?php if ($mov['es_giro']): ?>
                                                    <strong><?= htmlspecialchars($mov['detalle']) ?></strong>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($mov['detalle']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end"><?= $mov['debito'] > 0 ? '$ ' . number_format($mov['debito'], 2, ',', '.') : '$ 0,00' ?></td>
                                            <td class="text-end"><?= $mov['credito'] > 0 ? '$ ' . number_format($mov['credito'], 2, ',', '.') : '$ 0,00' ?></td>
                                            <td class="text-center"><?= htmlspecialchars($mov['codigo_inmueble']) ?></td>
                                            <td><?= htmlspecialchars($mov['anio']) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm bg-indigo-dark text-white btn-editar-mov" 
                                                    data-id="<?= $mov['id'] ?>"
                                                    data-nit="<?= htmlspecialchars($mov['nit_propietario']) ?>"
                                                    data-nombre="<?= htmlspecialchars($mov['nombre_tercero']) ?>"
                                                    data-fecha="<?= $mov['fecha'] ?>"
                                                    data-mes="<?= htmlspecialchars($mov['mes']) ?>"
                                                    data-concepto="<?= htmlspecialchars($mov['concepto']) ?>"
                                                    data-detalle="<?= htmlspecialchars($mov['detalle']) ?>"
                                                    data-debito="<?= $mov['debito'] ?>"
                                                    data-credito="<?= $mov['credito'] ?>"
                                                    data-codigo="<?= $mov['codigo_inmueble'] ?>"
                                                    data-anio="<?= $mov['anio'] ?>"
                                                    data-esgiro="<?= $mov['es_giro'] ?>"
                                                    title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm bg-danger text-white btn-eliminar-mov" data-id="<?= $mov['id'] ?>" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                                <!-- Total por propietario -->
                                <tr class="table-warning fw-bold">
                                    <td colspan="5" class="text-end">Total <?= htmlspecialchars($dataProp['nombre']) ?>:</td>
                                    <td class="text-end">$ <?= number_format($dataProp['totalDebito'], 2, ',', '.') ?></td>
                                    <td class="text-end">$ <?= number_format($dataProp['totalCredito'], 2, ',', '.') ?></td>
                                    <td colspan="3"></td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- Total general -->
                            <tr class="table-dark fw-bold">
                                <td colspan="5" class="text-end">TOTAL GENERAL:</td>
                                <td class="text-end">$ <?= number_format($totalesGenerales['debito'], 2, ',', '.') ?></td>
                                <td class="text-end">$ <?= number_format($totalesGenerales['credito'], 2, ',', '.') ?></td>
                                <td colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Nuevo Movimiento -->
<div class="modal fade" id="modalNuevoMovimiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Movimiento de Cartera</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoMovimiento">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Propietario</label>
                            <select name="nit_propietario" id="selectPropietarioNuevo" class="form-select select2-propietario" required>
                                <option value="">Seleccionar...</option>
                                <?php
                                $resPropietarios->data_seek(0);
                                while ($prop = $resPropietarios->fetch_assoc()):
                                ?>
                                <option value="<?= htmlspecialchars($prop['doc_propietario']) ?>" data-nombre="<?= htmlspecialchars($prop['nombre_propietario']) ?>">
                                    <?= htmlspecialchars($prop['doc_propietario']) ?> - <?= htmlspecialchars($prop['nombre_propietario']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Inmueble</label>
                            <select name="codigo_inmueble" id="selectInmuebleNuevo" class="form-select" required>
                                <option value="">Seleccione propietario primero</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mes</label>
                            <select name="mes" class="form-select" required>
                                <?php foreach ($meses as $numMes => $nombreMes): ?>
                                <option value="<?= $numMes ?>" <?= $numMes == date('m') ? 'selected' : '' ?>><?= $numMes ?> - <?= $nombreMes ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Año</label>
                            <input type="number" name="anio" class="form-control" value="<?= date('Y') ?>" required min="2020" max="2099">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Concepto</label>
                            <select name="concepto" id="selectConceptoNuevo" class="form-select" required>
                                <?php foreach ($conceptos as $concepto): ?>
                                <option value="<?= htmlspecialchars($concepto) ?>"><?= htmlspecialchars($concepto) ?></option>
                                <?php endforeach; ?>
                                <option value="Otro">Otro (personalizado)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Detalle</label>
                            <input type="text" name="detalle" id="detalleNuevo" class="form-control" placeholder="Ej: Canon de desde 01/Mar/2026 hasta 31/Mar/2026 CL 74 SUR 45" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Débito ($)</label>
                            <input type="number" name="debito" class="form-control" step="0.01" min="0" value="0">
                            <small class="text-muted">Retenciones / descuentos</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Crédito ($)</label>
                            <input type="number" name="credito" class="form-control" step="0.01" min="0" value="0">
                            <small class="text-muted">Saldos a favor</small>
                        </div>
                        <div class="col-md-4 d-flex align-items-center pt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="es_giro" id="esGiroNuevo" value="1">
                                <label class="form-check-label fw-semibold" for="esGiroNuevo">
                                    <i class="bi bi-cash-coin text-success"></i> Es Giro Renta
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Movimiento -->
<div class="modal fade" id="modalEditarMovimiento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Movimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarMovimiento">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nit Propietario</label>
                            <input type="text" name="nit_propietario" id="editNit" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Tercero</label>
                            <input type="text" name="nombre_tercero" id="editNombre" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Código Inmueble</label>
                            <input type="number" name="codigo_inmueble" id="editCodigo" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha" id="editFecha" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Mes</label>
                            <select name="mes" id="editMes" class="form-select" required>
                                <?php foreach ($meses as $numMes => $nombreMes): ?>
                                <option value="<?= $numMes ?>"><?= $numMes ?> - <?= $nombreMes ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Año</label>
                            <input type="number" name="anio" id="editAnio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Concepto</label>
                            <select name="concepto" id="editConcepto" class="form-select" required>
                                <?php foreach ($conceptos as $concepto): ?>
                                <option value="<?= htmlspecialchars($concepto) ?>"><?= htmlspecialchars($concepto) ?></option>
                                <?php endforeach; ?>
                                <option value="Otro">Otro (personalizado)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Detalle</label>
                            <input type="text" name="detalle" id="editDetalle" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Débito ($)</label>
                            <input type="number" name="debito" id="editDebito" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Crédito ($)</label>
                            <input type="number" name="credito" id="editCredito" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-4 d-flex align-items-center pt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="es_giro" id="editEsGiro" value="1">
                                <label class="form-check-label fw-semibold" for="editEsGiro">
                                    <i class="bi bi-cash-coin text-success"></i> Es Giro Renta
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Generar Cartera Automática -->
<div class="modal fade" id="modalGenerarCartera" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-gear"></i> Generar Cartera Automática</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formGenerarCartera">
                <div class="modal-body">
                    <p class="text-muted">Se generarán los movimientos de cartera (canon, IVA, comisión, iva/comisión y giro renta) para todos los inmuebles ocupados del propietario seleccionado.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Propietario</label>
                        <select name="nit_propietario" id="selectPropietarioGenerar" class="form-select select2-propietario-generar" required>
                            <option value="">Seleccionar...</option>
                            <option value="TODOS">-- Todos los propietarios --</option>
                            <?php
                            $resPropietarios->data_seek(0);
                            while ($prop = $resPropietarios->fetch_assoc()):
                            ?>
                            <option value="<?= htmlspecialchars($prop['doc_propietario']) ?>">
                                <?= htmlspecialchars($prop['doc_propietario']) ?> - <?= htmlspecialchars($prop['nombre_propietario']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mes</label>
                            <select name="mes" class="form-select" required>
                                <?php foreach ($meses as $numMes => $nombreMes): ?>
                                <option value="<?= $numMes ?>" <?= $numMes == date('m') ? 'selected' : '' ?>><?= $nombreMes ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Año</label>
                            <input type="number" name="anio" class="form-control" value="<?= date('Y') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Fecha del giro</label>
                            <input type="date" name="fecha_giro" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-gear"></i> Generar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cargar inmuebles al seleccionar propietario
$('#selectPropietarioNuevo').on('change', function() {
    const nit = $(this).val();
    const selectInmueble = $('#selectInmuebleNuevo');
    selectInmueble.html('<option value="">Cargando...</option>');
    
    if (nit) {
        $.ajax({
            url: 'controller/cartera/obtener_inmuebles.php',
            type: 'GET',
            data: { nit: nit },
            dataType: 'json',
            success: function(data) {
                let options = '<option value="">Seleccionar inmueble...</option>';
                data.forEach(function(inm) {
                    options += `<option value="${inm.codigo}">#${inm.codigo} - ${inm.direccion} (${inm.tipoInmueble})</option>`;
                });
                selectInmueble.html(options);
            },
            error: function() {
                selectInmueble.html('<option value="">Error al cargar</option>');
            }
        });
    } else {
        selectInmueble.html('<option value="">Seleccione propietario primero</option>');
    }
});

// Guardar nuevo movimiento
$('#formNuevoMovimiento').on('submit', function(e) {
    e.preventDefault();
    const nombre = $('#selectPropietarioNuevo option:selected').data('nombre') || '';
    const formData = $(this).serialize() + '&nombre_tercero=' + encodeURIComponent(nombre);
    
    $.ajax({
        url: 'controller/cartera/guardar_movimiento.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                Swal.fire({icon: 'success', title: 'Guardado', text: 'Movimiento registrado correctamente', timer: 1500, showConfirmButton: false}).then(() => location.reload());
            } else {
                Swal.fire({icon: 'error', title: 'Error', text: resp.message});
            }
        },
        error: function() {
            Swal.fire({icon: 'error', title: 'Error', text: 'Error de conexión con el servidor'});
        }
    });
});

// Abrir modal editar
$(document).on('click', '.btn-editar-mov', function() {
    const btn = $(this);
    $('#editId').val(btn.data('id'));
    $('#editNit').val(btn.data('nit'));
    $('#editNombre').val(btn.data('nombre'));
    $('#editFecha').val(btn.data('fecha'));
    $('#editMes').val(btn.data('mes'));
    $('#editConcepto').val(btn.data('concepto'));
    $('#editDetalle').val(btn.data('detalle'));
    $('#editDebito').val(btn.data('debito'));
    $('#editCredito').val(btn.data('credito'));
    $('#editCodigo').val(btn.data('codigo'));
    $('#editAnio').val(btn.data('anio'));
    $('#editEsGiro').prop('checked', btn.data('esgiro') == 1);
    
    new bootstrap.Modal(document.getElementById('modalEditarMovimiento')).show();
});

// Actualizar movimiento
$('#formEditarMovimiento').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'controller/cartera/actualizar_movimiento.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                Swal.fire({icon: 'success', title: 'Actualizado', text: 'Movimiento actualizado correctamente', timer: 1500, showConfirmButton: false}).then(() => location.reload());
            } else {
                Swal.fire({icon: 'error', title: 'Error', text: resp.message});
            }
        },
        error: function() {
            Swal.fire({icon: 'error', title: 'Error', text: 'Error de conexión con el servidor'});
        }
    });
});

// Eliminar movimiento
$(document).on('click', '.btn-eliminar-mov', function() {
    const id = $(this).data('id');
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Este movimiento será eliminado permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'controller/cartera/eliminar_movimiento.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        Swal.fire({icon: 'success', title: 'Eliminado', text: 'Movimiento eliminado correctamente', timer: 1500, showConfirmButton: false}).then(() => location.reload());
                    } else {
                        Swal.fire({icon: 'error', title: 'Error', text: resp.message});
                    }
                },
                error: function() {
                    Swal.fire({icon: 'error', title: 'Error', text: 'Error de conexión con el servidor'});
                }
            });
        }
    });
});

// Generar cartera automática
$('#formGenerarCartera').on('submit', function(e) {
    e.preventDefault();
    const btn = $(this).find('button[type=submit]');
    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Generando...');
    
    $.ajax({
        url: 'controller/cartera/generar_cartera.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                Swal.fire({icon: 'success', title: 'Cartera Generada', text: resp.message}).then(() => location.reload());
            } else {
                Swal.fire({icon: 'error', title: 'Error', text: resp.message});
            }
        },
        error: function() {
            Swal.fire({icon: 'error', title: 'Error', text: 'Error de conexión con el servidor'});
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-gear"></i> Generar');
        }
    });
});

// Auto-llenar detalle basándose en concepto seleccionado
$('#selectConceptoNuevo').on('change', function() {
    const concepto = $(this).val();
    if (concepto === 'Giro Renta') {
        $('#esGiroNuevo').prop('checked', true);
    }
});

// Inicializar Select2 en selectores de propietarios
$('#filtroPropietarioSelect').select2({
    theme: 'bootstrap-5',
    placeholder: '-- Todos --',
    allowClear: true,
    width: '100%'
});

// Select2 en modal Nuevo Movimiento
$('#modalNuevoMovimiento').on('shown.bs.modal', function() {
    $('#selectPropietarioNuevo').select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar propietario...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalNuevoMovimiento')
    });
});

// Select2 en modal Generar Cartera
$('#modalGenerarCartera').on('shown.bs.modal', function() {
    $('#selectPropietarioGenerar').select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar propietario...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalGenerarCartera')
    });
});
</script>
