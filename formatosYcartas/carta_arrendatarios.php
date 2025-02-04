
<?php
require '../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Dompdf\Dompdf;
use Dompdf\Options;

// Configuración de Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Permitir cargar recursos remotos (imágenes, CSS, etc.)
$dompdf = new Dompdf($options);

// Contenido HTML
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            margin: 20mm 30mm 20mm 30mm;
        }
        body { 
            font-family: Arial, sans-serif;
            text-align: justify;
            font-size: 10pt;
            line-height: 1.5;
        }
        h1 { 
            font-weight: bold;
            text-align: center;
        }
        .bold { font-weight: bold; }
        .normal { 
            font-weight: normal;
            text-align: justify;
        }
        
    </style>
</head>
<body>
    <p class="bold" style="font-size: 12pt;">BIENVENIDO A SOMOS PROPIEDAD S.A.S.</p>
    <br>
    <p>Estimado arrendatario:</p>
    
    <p>En nombre de SOMOS PROPIEDAD S.A.S. le damos la más cordial bienvenida a nuestro grupo de clientes y confiamos en que su experiencia con nosotros le brindará la máxima satisfacción y tranquilidad para usted y su familia.</p>

    <p>Nos complace servirle con la ubicación en un inmueble que le brinde el confort y la satisfacción como una experiencias de vida y esperamos lo pueda disfrutar al máximo pudiendo contar con todo nuestro respaldo y apoyo.</p>

    <p>Contamos con mas de treinta años de experiencia en el sector inmobiliario, adicional tenemos un equipo de trabajo dispuesto y atento a solucionar sus inquietudes y dificultades en el goce del inmueble que le hemos entregado. </p>

    <p>Queremos colocar a su disposición nuestro grupo de trabajo para que usted canalice directamente su necesidad y pueda obtener una respuesta oportuna y clara a sus requerimientos:</p>

    <p>Nuestras áreas de apoyo están compuestas así:
    <br>
    <ol style="list-style-type: disc;">
        <li><strong>Contratos:</strong> Diana Alexandra Agudelo Gil: 6044447362 Ext: 103, Cel: 320 6716990</li>
        <li><strong>Reparaciones:</strong> Verónica Valencia Hernandez: 6044447362 Ext: 109, Cel: 300 6662367</li>
        <li><strong>Lider de Arrendamientos:</strong> Sandra Montoya Colorado: 6044447362 Ext: 108, Cel: 3122933978</li>
        <li><strong>Contabilidad:</strong> Andrés Uribe Gallego: 6044447362 Ext: 105</li>
        <li><strong>Cartera:</strong> Daniela Molina Montoya: 6044447362 Ext: 102, Cel: 319 4534526</li>
        <li><strong>Tesorería:</strong> Carlos Andrés Vélez Gómez: 6044447362 Ext: 110 Cel: 311 7921620</li>
        <li><strong>Administración:</strong> Jaime Zuluaga Cardona: 6044447362 Ext: 101 Cel: 3128330857</li>
    </ol></p>

    <p>Gracias nuevamente por elegir a SOMOS PROPIEDAD S.A.S. Estamos agradecidos por darnos la oportunidad de servirle, trabajaremos incansablemente para brindarle todo el apoyo que requiera. </p>

    <p>Cordialmente:</p>
    <br>
    <br>
    <br>

    <p style="font-size: 12pt;"><strong>JAIME ZULUAGA CARDONA</strong></p>
    
    <p>ADMINISTRADOR</p>
    <p>CEL. 3128330857</p>

</body>

</html>
';


// Crear instancia de Dompdf
$dompdf = new Dompdf($options);

// Cargar el contenido HTMLL
$dompdf->loadHtml($html);

// (Opcional) Configurar el tamaño y la orientación del papel
$dompdf->setPaper('letter', 'portrait');

// Renderizar el HTML como PDF
$dompdf->render();

ob_clean();

// Salida del PDF
$dompdf->stream('carta_bienvenida_propietarios.pdf', ['Attachment' => true]);
