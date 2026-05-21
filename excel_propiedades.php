<?php
/**
 * Exportador de Propiedades - Somos Propiedad
 * Hoja 1 : Todos los registros de `proprieter` (columnas coloreadas por grupo)
 *           Celdas con dato duplicado resaltadas en amarillo
 *           Celdas con discrepancia vs base_somos.xls resaltadas en rojo claro
 * Hoja 2 : Detalle de datos repetidos/duplicados detectados
 * Hoja 3 : Resumen comparativo con base_somos.xls
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

// ─────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────
function hdrStyle(string $bgARGB, string $fgARGB = 'FFFFFFFF'): array
{
    return [
        'font'      => ['bold' => true, 'color' => ['argb' => $fgARGB]],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgARGB]],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
            'wrapText'   => true,
        ],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF999999']]],
    ];
}

function thinBorder(): array
{
    return ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFDDDDDD']]]];
}

function autoSize(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, int $cols): void
{
    for ($c = 1; $c <= $cols; $c++) {
        $ws->getColumnDimensionByColumn($c)->setAutoSize(true);
    }
}

// ─────────────────────────────────────────────────
// CONSULTA PRINCIPAL
// ─────────────────────────────────────────────────
$result = $conn->query("SELECT * FROM proprieter ORDER BY codigo ASC");
if (!$result) {
    die("Error BD: " . $conn->error);
}
$rows     = $result->fetch_all(MYSQLI_ASSOC);
$totalBD  = count($rows);

// ─────────────────────────────────────────────────
// DEFINICIÓN DE COLUMNAS  [campo_bd, etiqueta, grupo]
// ─────────────────────────────────────────────────
$columns = [
    ['id',                     'ID',                      'Sistema'],
    ['codigo',                 'Código Inmueble',         'Sistema'],
    ['condicion',              'Condición',               'Sistema'],
    ['estadoPropietario',      'Estado Propietario',      'Sistema'],
    ['url_foto_principal',     'URL Foto Principal',      'Sistema'],
    ['fecha_creacion',         'Fecha Creación',          'Sistema'],

    ['tipoInmueble',           'Tipo Inmueble',           'Inmueble'],
    ['nivel_piso',             'Nivel / Piso',            'Inmueble'],
    ['area',                   'Área (m²)',               'Inmueble'],
    ['estrato',                'Estrato',                 'Inmueble'],
    ['departamento',           'Departamento',            'Inmueble'],
    ['Municipio',              'Municipio',               'Inmueble'],
    ['direccion',              'Dirección',               'Inmueble'],
    ['latitud',                'Latitud',                 'Inmueble'],
    ['longitud',               'Longitud',                'Inmueble'],
    ['TelefonoInmueble',       'Teléfono Inmueble',       'Inmueble'],

    ['terraza',                'Terraza',                 'Características'],
    ['ascensor',               'Ascensor',                'Características'],
    ['patio',                  'Patio',                   'Características'],
    ['parqueadero',            'Parqueadero',             'Características'],
    ['cuarto_util',            'Cuarto Útil',             'Características'],
    ['alcobas',                'Alcobas',                 'Características'],
    ['closet',                 'Closet',                  'Características'],
    ['sala',                   'Sala',                    'Características'],
    ['sala_comedor',           'Sala-Comedor',            'Características'],
    ['comedor',                'Comedor',                 'Características'],
    ['cocina',                 'Cocina',                  'Características'],
    ['servicios',              'Baños Servicio',          'Características'],
    ['CuartoServicios',        'Cuarto Servicios',        'Características'],
    ['ZonaRopa',               'Zona Ropa',               'Características'],
    ['vista',                  'Vista',                   'Características'],
    ['servicios_publicos',     'Servicios Públicos',      'Características'],
    ['otras_caracteristicas',  'Otras Características',   'Características'],
    ['contrato_EPM',           'Contrato EPM',            'Características'],

    ['doc_propietario',        'Doc. Propietario',        'Propietario'],
    ['nombre_propietario',     'Nombre Propietario',      'Propietario'],
    ['telefono_propietario',   'Teléfono Propietario',    'Propietario'],
    ['email_propietario',      'Email Propietario',       'Propietario'],
    ['banco',                  'Banco',                   'Propietario'],
    ['tipoCuenta',             'Tipo Cuenta',             'Propietario'],
    ['numeroCuenta',           'Número Cuenta',           'Propietario'],
    ['diaPago',                'Día de Pago',             'Propietario'],

    ['doc_inquilino',          'Doc. Inquilino',          'Inquilino'],
    ['nombre_inquilino',       'Nombre Inquilino',        'Inquilino'],
    ['telefono_inquilino',     'Teléfono Inquilino',      'Inquilino'],
    ['email_inquilino',        'Email Inquilino',         'Inquilino'],

    ['valor_canon',            'Valor Canon',             'Contrato'],
    ['vigenciaContrato',       'Vigencia Contrato',       'Contrato'],
    ['fecha',                  'Fecha Contrato',          'Contrato'],
    ['descuento',              'Descuento (%)',           'Contrato'],
    ['iva',                    'IVA (%)',                 'Contrato'],
    ['comision',               'Comisión',                'Contrato'],
    ['aval_catastro',          'Aval Catastro',           'Contrato'],
    ['asistencia',             'Asistencia',              'Contrato'],
    ['ipc',                    'IPC (%)',                 'Contrato'],

    ['cc_codeudor_uno',        'CC Codeudor 1',           'Codeudores'],
    ['nombre_codeudor_uno',    'Nombre Codeudor 1',       'Codeudores'],
    ['email_codeudor_uno',     'Email Codeudor 1',        'Codeudores'],
    ['telefono_codeudor_uno',  'Teléfono Codeudor 1',    'Codeudores'],
    ['direccion_codeudor_uno', 'Dirección Codeudor 1',   'Codeudores'],
    ['cc_codeudor_dos',        'CC Codeudor 2',           'Codeudores'],
    ['nombre_codeudor_dos',    'Nombre Codeudor 2',       'Codeudores'],
    ['email_codeudor_dos',     'Email Codeudor 2',        'Codeudores'],
    ['telefono_codeudor_dos',  'Teléfono Codeudor 2',    'Codeudores'],
    ['direccion_codeudor_dos', 'Dirección Codeudor 2',   'Codeudores'],
    ['cc_codeudor_tres',       'CC Codeudor 3',           'Codeudores'],
    ['nombre_codeudor_tres',   'Nombre Codeudor 3',       'Codeudores'],
    ['email_codeudor_tres',    'Email Codeudor 3',        'Codeudores'],
    ['telefono_codeudor_tres', 'Teléfono Codeudor 3',    'Codeudores'],
    ['direccion_codeudor_tres','Dirección Codeudor 3',   'Codeudores'],
];

// Color por grupo (ARGB)
$groupColor = [
    'Sistema'        => 'FF37474F',
    'Inmueble'       => 'FF1565C0',
    'Características'=> 'FF6A1B9A',
    'Propietario'    => 'FF2E7D32',
    'Inquilino'      => 'FFE65100',
    'Contrato'       => 'FF880E4F',
    'Codeudores'     => 'FF00695C',
];

// ─────────────────────────────────────────────────
// DETECCIÓN DE DUPLICADOS
// ─────────────────────────────────────────────────
// Campos a auditar por duplicados
$auditFields = [
    'doc_propietario'      => 'Documento Propietario',
    'email_propietario'    => 'Email Propietario',
    'telefono_propietario' => 'Teléfono Propietario',
    'numeroCuenta'         => 'Número de Cuenta Bancaria',
    'doc_inquilino'        => 'Documento Inquilino',
    'email_inquilino'      => 'Email Inquilino',
    'telefono_inquilino'   => 'Teléfono Inquilino',
    'TelefonoInmueble'     => 'Teléfono Inmueble',
    'cc_codeudor_uno'      => 'CC Codeudor 1',
    'cc_codeudor_dos'      => 'CC Codeudor 2',
    'cc_codeudor_tres'     => 'CC Codeudor 3',
    'email_codeudor_uno'   => 'Email Codeudor 1',
    'email_codeudor_dos'   => 'Email Codeudor 2',
    'email_codeudor_tres'  => 'Email Codeudor 3',
];

// dupReport[campo][] = ['label', 'valor', 'codigos', 'veces']
$dupReport = [];
foreach ($auditFields as $field => $label) {
    $map = [];
    foreach ($rows as $r) {
        $val = trim((string)($r[$field] ?? ''));
        if ($val === '' || $val === '0') {
            continue;
        }
        $map[$val][] = $r['codigo'];
    }
    foreach ($map as $val => $codes) {
        if (count($codes) > 1) {
            $dupReport[$field][] = [
                'label'   => $label,
                'valor'   => $val,
                'codigos' => $codes,
                'veces'   => count($codes),
            ];
        }
    }
}

// Mapa rápido: codigo => [campo => true]  para resaltar en hoja 1
$dupCellMap = [];
foreach ($dupReport as $field => $entries) {
    foreach ($entries as $entry) {
        foreach ($entry['codigos'] as $cod) {
            $dupCellMap[$cod][$field] = true;
        }
    }
}

// ─────────────────────────────────────────────────
// LEER base_somos.xls
// ─────────────────────────────────────────────────
$baseFile      = __DIR__ . '/base_somos.xls';
$baseRows      = [];   // [noInm => ['dir','doc_prop','doc_arre']]
$baseTotal     = 0;
$baseLoadError = '';

if (file_exists($baseFile)) {
    try {
        $rdr = IOFactory::createReaderForFile($baseFile);
        $rdr->setReadDataOnly(true);
        $baseData = $rdr->load($baseFile)->getActiveSheet()->toArray(null, true, true, false);

        $bHeaders = array_map('trim', $baseData[0] ?? []);
        unset($baseData[0]);

        $iNoInm   = array_search('No. Inm',           $bHeaders);
        $iDir     = array_search('Dirección',          $bHeaders);
        $iDocProp = array_search('Doc. Propietario',   $bHeaders);
        $iDocArre = array_search('Doc. Arrendatario',  $bHeaders);

        foreach ($baseData as $bRow) {
            $noInm = trim((string)($bRow[$iNoInm] ?? ''));
            if ($noInm === '') {
                continue;
            }
            $baseRows[$noInm] = [
                'dir'      => trim((string)($bRow[$iDir]     ?? '')),
                'doc_prop' => trim((string)($bRow[$iDocProp] ?? '')),
                'doc_arre' => trim((string)($bRow[$iDocArre] ?? '')),
            ];
        }
        $baseTotal = count($baseRows);
    } catch (\Exception $e) {
        $baseLoadError = $e->getMessage();
    }
} else {
    $baseLoadError = 'Archivo base_somos.xls no encontrado en la raíz del proyecto.';
}

$bdCodigos  = array_column($rows, 'codigo');
$soloEnBD   = array_values(array_filter($bdCodigos, fn($c) => !array_key_exists((string)$c, $baseRows)));
$soloEnBase = array_values(array_filter(array_keys($baseRows), fn($c) => !in_array((int)$c, $bdCodigos)));

// Discrepancias en propiedades que existen en ambos
$discMap = [
    'direccion'       => 'dir',
    'doc_propietario' => 'doc_prop',
    'doc_inquilino'   => 'doc_arre',
];
$discrepancias = [];
$discCellMap   = [];   // [codigo][campo_bd] = true
foreach ($rows as $r) {
    $cod = (string)$r['codigo'];
    if (!isset($baseRows[$cod])) {
        continue;
    }
    $b = $baseRows[$cod];
    foreach ($discMap as $bdField => $baseKey) {
        $vBD   = strtolower(trim((string)$r[$bdField]));
        $vBase = strtolower(trim((string)$b[$baseKey]));
        if ($vBD === '' || $vBase === '' || $vBD === $vBase) {
            continue;
        }
        $discrepancias[$cod][] = [
            'campo' => $bdField,
            'bd'    => $r[$bdField],
            'base'  => $b[$baseKey],
        ];
        $discCellMap[$r['codigo']][$bdField] = true;
    }
}

// ─────────────────────────────────────────────────
// LIBRO EXCEL
// ─────────────────────────────────────────────────
$book = new Spreadsheet();
$book->getProperties()
    ->setTitle('Propiedades Somos Propiedad')
    ->setCreator('Sistema Admin')
    ->setDescription('Exportación proprieter + duplicados + comparativa base_somos.xls');

// ══════════════════════════════════════════════════
// HOJA 1 — PROPIEDADES
// ══════════════════════════════════════════════════
$ws1 = $book->getActiveSheet()->setTitle('Propiedades BD');
$totalCols = count($columns);

// --- Fila 1: encabezados de GRUPO (con merge) ---
$colNum    = 1;
$prevGroup = null;
$gStartCol = 1;
foreach ($columns as $i => [$key, $lbl, $grp]) {
    if ($grp !== $prevGroup && $prevGroup !== null) {
        $gEndCol = $colNum - 1;
        if ($gEndCol > $gStartCol) {
            $ws1->mergeCells([$gStartCol, 1, $gEndCol, 1]);
        }
        $ws1->setCellValue([$gStartCol, 1], $prevGroup);
        $ws1->getStyle([$gStartCol, 1, $gEndCol, 1])
            ->applyFromArray(hdrStyle($groupColor[$prevGroup] ?? 'FF333333'));
        $gStartCol = $colNum;
    } elseif ($prevGroup === null) {
        $gStartCol = 1;
    }
    $prevGroup = $grp;
    $colNum++;
}
// último grupo
$gEndCol = $colNum - 1;
if ($gEndCol > $gStartCol) {
    $ws1->mergeCells([$gStartCol, 1, $gEndCol, 1]);
}
$ws1->setCellValue([$gStartCol, 1], $prevGroup);
$ws1->getStyle([$gStartCol, 1, $gEndCol, 1])
    ->applyFromArray(hdrStyle($groupColor[$prevGroup] ?? 'FF333333'));

// --- Fila 2: nombres de columna ---
foreach ($columns as $i => [$key, $lbl, $grp]) {
    $c = $i + 1;
    $ws1->setCellValue([$c, 2], $lbl);
    $ws1->getStyle([$c, 2])->applyFromArray(hdrStyle($groupColor[$grp] ?? 'FF333333'));
}
$ws1->getRowDimension(1)->setRowHeight(22);
$ws1->getRowDimension(2)->setRowHeight(38);

// --- Filas de datos ---
$dataRow = 3;
foreach ($rows as $r) {
    $cod     = $r['codigo'];
    $hasDup  = isset($dupCellMap[$cod]);
    $hasDisc = isset($discCellMap[$cod]);

    foreach ($columns as $i => [$key, $lbl, $grp]) {
        $c   = $i + 1;
        $val = $r[$key] ?? '';
        $ws1->setCellValue([$c, $dataRow], $val);

        // Fondo base (fila alternada)
        $bg = ($dataRow % 2 === 0) ? 'FFF5F5F5' : 'FFFFFFFF';

        // Prioridad alta: discrepancia con base_somos.xls → rojo claro
        if ($hasDisc && isset($discCellMap[$cod][$key])) {
            $bg = 'FFFFCDD2';
        }
        // Prioridad media: duplicado → amarillo
        elseif ($hasDup && isset($dupCellMap[$cod][$key])) {
            $bg = 'FFFFF59D';
        }

        $ws1->getStyle([$c, $dataRow])->applyFromArray(array_merge(
            thinBorder(),
            ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]]]
        ));
    }
    $dataRow++;
}

// Freeze + autosize
$ws1->freezePane([1, 3]);
autoSize($ws1, $totalCols);

// --- Leyenda al pie ---
$lr = $dataRow + 1;
$ws1->mergeCells([1, $lr, 3, $lr]);
$ws1->setCellValue([1, $lr], 'LEYENDA DE COLORES');
$ws1->getStyle([1, $lr, 3, $lr])->applyFromArray(hdrStyle('FF37474F'));
$leyenda = [
    ['FFFFF59D', 'Amarillo',    'Dato duplicado en ese campo (mismo valor en varios inmuebles)'],
    ['FFFFCDD2', 'Rojo claro',  'Discrepancia con base_somos.xls en ese campo'],
    ['FFF5F5F5', 'Gris claro',  'Fila par (sin alerta)'],
];
foreach ($leyenda as [$color, $nombre, $desc]) {
    $lr++;
    $ws1->setCellValue([1, $lr], $nombre);
    $ws1->setCellValue([2, $lr], $desc);
    $ws1->getStyle([1, $lr])->applyFromArray([
        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF999999']]],
        'font'    => ['bold' => true],
    ]);
    $ws1->getStyle([2, $lr])->applyFromArray(thinBorder());
}

// ══════════════════════════════════════════════════
// HOJA 2 — DATOS DUPLICADOS
// ══════════════════════════════════════════════════
$ws2 = $book->createSheet()->setTitle('Datos Duplicados');

$ws2->mergeCells('A1:H1');
$ws2->setCellValue('A1', 'REPORTE DE DATOS REPETIDOS / DUPLICADOS');
$ws2->getStyle('A1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFB71C1C']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$ws2->getRowDimension(1)->setRowHeight(28);

$h2 = ['#', 'Campo BD', 'Etiqueta', 'Valor Repetido', 'Veces', 'Códigos de Inmueble Afectados', '# Inmuebles', 'Observación'];
foreach ($h2 as $i => $h) {
    $ws2->setCellValue([$i + 1, 2], $h);
    $ws2->getStyle([$i + 1, 2])->applyFromArray(hdrStyle('FF880E4F'));
}
$ws2->getRowDimension(2)->setRowHeight(28);

$dr2 = 3;
$seq = 1;
if (empty($dupReport)) {
    $ws2->mergeCells('A3:H3');
    $ws2->setCellValue('A3', 'No se encontraron datos duplicados.');
    $ws2->getStyle('A3')->getFont()->setItalic(true);
} else {
    foreach ($dupReport as $field => $entries) {
        foreach ($entries as $entry) {
            $obs = match (true) {
                str_contains($field, 'doc')       => 'Mismo documento en múltiples inmuebles — verificar si es propietario con varios o error',
                str_contains($field, 'email')     => 'Mismo correo compartido — puede ser válido si es el mismo titular',
                str_contains($field, 'telefono') || str_contains($field, 'Telefono')
                                                  => 'Mismo teléfono en varios inmuebles',
                str_contains($field, 'Cuenta')    => '⚠️ MISMA CUENTA BANCARIA — revisar pagos duplicados',
                str_contains($field, 'cc_code')   => 'Codeudor aparece en varios inmuebles — puede ser válido',
                default                           => 'Valor repetido',
            };

            // Color por gravedad
            $rowBg = match (true) {
                $entry['veces'] >= 5 => 'FFFFCDD2',
                $entry['veces'] >= 3 => 'FFFFF9C4',
                default              => 'FFF3E5F5',
            };

            $ws2->setCellValue([1, $dr2], $seq++);
            $ws2->setCellValue([2, $dr2], $field);
            $ws2->setCellValue([3, $dr2], $entry['label']);
            $ws2->setCellValue([4, $dr2], $entry['valor']);
            $ws2->setCellValue([5, $dr2], $entry['veces']);
            $ws2->setCellValue([6, $dr2], implode(', ', $entry['codigos']));
            $ws2->setCellValue([7, $dr2], count($entry['codigos']));
            $ws2->setCellValue([8, $dr2], $obs);
            $ws2->getStyle([1, $dr2, 8, $dr2])->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ]);
            $dr2++;
        }
    }

    // Mini resumen al pie de hoja 2
    $dr2++;
    $ws2->mergeCells([1, $dr2, 8, $dr2]);
    $totalDupEntries = array_sum(array_map('count', $dupReport));
    $ws2->setCellValue([1, $dr2], "Total tipos de campo con duplicados: " . count($dupReport) . "   |   Total entradas duplicadas: " . $totalDupEntries);
    $ws2->getStyle([1, $dr2, 8, $dr2])->applyFromArray(hdrStyle('FF37474F'));
}
autoSize($ws2, 8);

// ══════════════════════════════════════════════════
// HOJA 3 — COMPARATIVA BASE SOMOS
// ══════════════════════════════════════════════════
$ws3 = $book->createSheet()->setTitle('Comparativa Base Somos');

$ws3->mergeCells('A1:D1');
$ws3->setCellValue('A1', 'COMPARATIVA: BD proprieter  vs  base_somos.xls');
$ws3->getStyle('A1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D47A1']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$ws3->getRowDimension(1)->setRowHeight(28);

// Bloque KPIs
$kpis = [
    ['Total propiedades en BD (proprieter)',   $totalBD,              'FF1565C0'],
    ['Total propiedades en base_somos.xls',    $baseTotal,            'FF1565C0'],
    ['Diferencia numérica',                    $totalBD - $baseTotal, 'FF6A1B9A'],
    ['Solo en BD (no en Excel base)',           count($soloEnBD),      'FFE65100'],
    ['Solo en Excel base (no en BD)',           count($soloEnBase),    'FFE65100'],
    ['Propiedades con discrepancias de datos',  count($discrepancias), 'FFC62828'],
    ['Campos con valores duplicados',           count($dupReport),     'FF880E4F'],
];

$ws3->setCellValue('A3', 'Indicador');
$ws3->setCellValue('B3', 'Valor');
$ws3->getStyle('A3:B3')->applyFromArray(hdrStyle('FF263238'));
$ws3->getRowDimension(3)->setRowHeight(22);

$sr = 4;
foreach ($kpis as [$lbl, $val, $color]) {
    $ws3->setCellValue([1, $sr], $lbl);
    $ws3->setCellValue([2, $sr], $val);
    $ws3->getStyle([1, $sr, 2, $sr])->applyFromArray([
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
        'font'      => ['bold' => ($val != 0), 'color' => ['argb' => 'FFFFFFFF']],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFAAAAAA']]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
    ]);
    $ws3->getStyle([2, $sr])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sr++;
}

if ($baseLoadError) {
    $sr++;
    $ws3->mergeCells([1, $sr, 4, $sr]);
    $ws3->setCellValue([1, $sr], '⚠ Advertencia: ' . $baseLoadError);
    $ws3->getStyle([1, $sr])->applyFromArray([
        'font' => ['italic' => true, 'color' => ['argb' => 'FFCC0000']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF9C4']],
    ]);
}

// ---- Solo en BD ----
$sr += 2;
$ws3->mergeCells([1, $sr, 4, $sr]);
$ws3->setCellValue([1, $sr], 'PROPIEDADES SOLO EN BD — ausentes en base_somos.xls (' . count($soloEnBD) . ')');
$ws3->getStyle([1, $sr, 4, $sr])->applyFromArray(hdrStyle('FFE65100'));
$sr++;

if (empty($soloEnBD)) {
    $ws3->setCellValue([1, $sr], 'Ninguna');
    $ws3->getStyle([1, $sr])->getFont()->setItalic(true);
    $sr++;
} else {
    foreach (['Código', 'Dirección', 'Propietario', 'Estado'] as $ci => $ch) {
        $ws3->setCellValue([$ci + 1, $sr], $ch);
        $ws3->getStyle([$ci + 1, $sr])->applyFromArray(hdrStyle('FFF4511E'));
    }
    $sr++;
    $byCode = array_column($rows, null, 'codigo');
    foreach ($soloEnBD as $cod) {
        $pr = $byCode[$cod] ?? [];
        $ws3->setCellValue([1, $sr], $cod);
        $ws3->setCellValue([2, $sr], $pr['direccion'] ?? '');
        $ws3->setCellValue([3, $sr], $pr['nombre_propietario'] ?? '');
        $ws3->setCellValue([4, $sr], $pr['estadoPropietario'] ?? '');
        $ws3->getStyle([1, $sr, 4, $sr])->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF3E0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ]);
        $sr++;
    }
}

// ---- Solo en Excel base ----
$sr++;
$ws3->mergeCells([1, $sr, 4, $sr]);
$ws3->setCellValue([1, $sr], 'PROPIEDADES SOLO EN base_somos.xls — ausentes en BD (' . count($soloEnBase) . ')');
$ws3->getStyle([1, $sr, 4, $sr])->applyFromArray(hdrStyle('FF1565C0'));
$sr++;

if (empty($soloEnBase)) {
    $ws3->setCellValue([1, $sr], 'Ninguna');
    $ws3->getStyle([1, $sr])->getFont()->setItalic(true);
    $sr++;
} else {
    foreach (['No. Inm (Excel)', 'Dirección (Excel)', 'Doc. Propietario', 'Doc. Arrendatario'] as $ci => $ch) {
        $ws3->setCellValue([$ci + 1, $sr], $ch);
        $ws3->getStyle([$ci + 1, $sr])->applyFromArray(hdrStyle('FF1976D2'));
    }
    $sr++;
    foreach ($soloEnBase as $cod) {
        $b = $baseRows[$cod];
        $ws3->setCellValue([1, $sr], $cod);
        $ws3->setCellValue([2, $sr], $b['dir']);
        $ws3->setCellValue([3, $sr], $b['doc_prop']);
        $ws3->setCellValue([4, $sr], $b['doc_arre']);
        $ws3->getStyle([1, $sr, 4, $sr])->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE3F2FD']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ]);
        $sr++;
    }
}

// ---- Discrepancias ----
$sr++;
$ws3->mergeCells([1, $sr, 4, $sr]);
$ws3->setCellValue([1, $sr], 'DISCREPANCIAS DE DATOS — mismo código, valores distintos (' . count($discrepancias) . ')');
$ws3->getStyle([1, $sr, 4, $sr])->applyFromArray(hdrStyle('FFC62828'));
$sr++;

if (empty($discrepancias)) {
    $ws3->setCellValue([1, $sr], 'No se detectaron discrepancias en los campos comparados.');
    $ws3->getStyle([1, $sr])->getFont()->setItalic(true);
} else {
    foreach (['Código', 'Campo', 'Valor en BD', 'Valor en base_somos.xls'] as $ci => $ch) {
        $ws3->setCellValue([$ci + 1, $sr], $ch);
        $ws3->getStyle([$ci + 1, $sr])->applyFromArray(hdrStyle('FFD32F2F'));
    }
    $sr++;
    foreach ($discrepancias as $cod => $diffs) {
        foreach ($diffs as $d) {
            $ws3->setCellValue([1, $sr], $cod);
            $ws3->setCellValue([2, $sr], $d['campo']);
            $ws3->setCellValue([3, $sr], $d['bd']);
            $ws3->setCellValue([4, $sr], $d['base']);
            $ws3->getStyle([1, $sr, 4, $sr])->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFEBEE']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
            ]);
            $sr++;
        }
    }
}

autoSize($ws3, 4);

// ─────────────────────────────────────────────────
// ENVIAR AL NAVEGADOR
// ─────────────────────────────────────────────────
$book->setActiveSheetIndex(0);
$filename = 'propiedades_somos_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

(new Xlsx($book))->save('php://output');
exit;
