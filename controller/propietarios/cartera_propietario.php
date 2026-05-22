<?php
require_once __DIR__ . "/../conexion.php";

$doc_propietario = $_SESSION['username'] ?? '';

// Filtros del propietario
$filtroMesProp      = $_GET['mes_cartera']      ?? '';
$filtroAnioProp     = $_GET['anio_cartera']     ?? date('Y');
$filtroInmuebleProp = $_GET['inmueble_cartera'] ?? '';

// Consulta de propiedades disponibles para este propietario (para el selector de filtro)
$sqlInmuebles = "SELECT DISTINCT c.codigo_inmueble, csp.direccion
                 FROM cartera_propietario c
                 LEFT JOIN contratos_somos_propiedad csp ON c.codigo_inmueble = csp.no_contrato
                 WHERE c.nit_propietario = ?
                 ORDER BY c.codigo_inmueble ASC";
$stmtInmuebles = $conn->prepare($sqlInmuebles);
$stmtInmuebles->bind_param("s", $doc_propietario);
$stmtInmuebles->execute();
$resInmuebles = $stmtInmuebles->get_result();
$inmueblesList = [];
while ($row = $resInmuebles->fetch_assoc()) {
    $inmueblesList[] = $row;
}

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
if (!empty($filtroInmuebleProp)) {
    $whereProp .= " AND c.codigo_inmueble = ?";
    $paramsProp[] = $filtroInmuebleProp;
    $typesProp .= "s";
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
                            <select name="inmueble_cartera" class="form-select form-select-sm" style="min-width: 160px;">
                                <option value="">Todas las propiedades</option>
                                <?php foreach ($inmueblesList as $inm): ?>
                                <option value="<?= htmlspecialchars($inm['codigo_inmueble']) ?>"
                                    <?= $filtroInmuebleProp == $inm['codigo_inmueble'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($inm['codigo_inmueble']) ?>
                                    <?= !empty($inm['direccion']) ? ' - ' . htmlspecialchars($inm['direccion']) : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
                        <button type="button" class="btn btn-sm btn-danger" id="btnExportarPdfCartera">
                            <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                        </button>
                    </form>

                    <!-- JSON con datos de inmuebles para el modal (escapa correctamente) -->
                    <script>
                    var carterapdfInmuebles = <?= json_encode(array_map(function($i) {
                        return [
                            'codigo'    => $i['codigo_inmueble'],
                            'direccion' => $i['direccion'] ?? ''
                        ];
                    }, $inmueblesList)) ?>;
                    </script>
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

<script>
(function () {
    // Meses para el modal PDF
    var mesesPdf = {
        '01':'Enero','02':'Febrero','03':'Marzo','04':'Abril',
        '05':'Mayo','06':'Junio','07':'Julio','08':'Agosto',
        '09':'Septiembre','10':'Octubre','11':'Noviembre','12':'Diciembre'
    };

    document.getElementById('btnExportarPdfCartera').addEventListener('click', function () {
        // Construir opciones de inmuebles
        var inmueblesOpts = '<option value="todos">Todas las propiedades</option>';
        if (typeof carterapdfInmuebles !== 'undefined') {
            carterapdfInmuebles.forEach(function(inm) {
                var label = inm.codigo + (inm.direccion ? ' - ' + inm.direccion : '');
                inmueblesOpts += '<option value="' + inm.codigo + '">' + label + '</option>';
            });
        }

        // Construir opciones de meses
        var mesesOpts = '<option value="">Todos los meses</option>';
        Object.entries(mesesPdf).forEach(function([num, nom]) {
            mesesOpts += '<option value="' + num + '">' + nom + '</option>';
        });

        // Construir opciones de años
        var anioActual = new Date().getFullYear();
        var aniosOpts = '<option value="">Todos los años</option>';
        for (var a = anioActual; a >= 2020; a--) {
            aniosOpts += '<option value="' + a + '">' + a + '</option>';
        }

        Swal.fire({
            title: '<i class="bi bi-file-earmark-pdf text-danger"></i> Exportar Cartera PDF',
            html: `
                <div class="text-start px-2">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Propiedad</label>
                        <select id="swal-pdf-inmueble" class="form-select form-select-sm">
                            ${inmueblesOpts}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mes</label>
                        <select id="swal-pdf-mes" class="form-select form-select-sm">
                            ${mesesOpts}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Año</label>
                        <select id="swal-pdf-anio" class="form-select form-select-sm">
                            ${aniosOpts}
                        </select>
                    </div>
                </div>`,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-download"></i> Descargar PDF',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            width: 420,
            didOpen: function () {
                // Aplicar estilos Bootstrap al popup de Swal
                document.querySelectorAll('.swal2-popup .form-select').forEach(function(el) {
                    el.style.fontSize = '0.85rem';
                });
            },
            preConfirm: function () {
                return {
                    inmueble: document.getElementById('swal-pdf-inmueble').value,
                    mes:      document.getElementById('swal-pdf-mes').value,
                    anio:     document.getElementById('swal-pdf-anio').value
                };
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                var v = result.value;
                var url = 'controller/propietarios/pdf_cartera_propietario.php?';
                if (v.inmueble && v.inmueble !== 'todos') url += 'cod_inmueble=' + encodeURIComponent(v.inmueble) + '&';
                if (v.mes)   url += 'mes='  + encodeURIComponent(v.mes)  + '&';
                if (v.anio)  url += 'anio=' + encodeURIComponent(v.anio) + '&';

                Swal.fire({
                    title: 'Generando PDF...',
                    text: 'Por favor espere.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: function () { Swal.showLoading(); }
                });

                fetch(url)
                    .then(function (res) {
                        var ct = res.headers.get('content-type') || '';
                        if (ct.indexOf('application/json') !== -1) {
                            return res.json().then(function (json) {
                                throw new Error(json.message || 'Sin movimientos de cartera para los filtros seleccionados.');
                            });
                        }
                        if (!res.ok) throw new Error('Error al generar el PDF');
                        return res.blob();
                    })
                    .then(function (blob) {
                        var blobUrl = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = blobUrl;
                        a.download = 'cartera_' + new Date().toISOString().slice(0, 10) + '.pdf';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        URL.revokeObjectURL(blobUrl);
                        Swal.fire({
                            icon: 'success',
                            title: 'PDF listo',
                            text: 'La descarga ha comenzado.',
                            timer: 2200,
                            showConfirmButton: false
                        });
                    })
                    .catch(function (err) {
                        Swal.fire('Error', err.message || 'No se pudo generar el PDF.', 'error');
                    });
            }
        });
    });
})();
</script>
