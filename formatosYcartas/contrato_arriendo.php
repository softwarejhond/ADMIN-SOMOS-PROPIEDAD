<?php
require '../vendor/autoload.php';
require '../conexion.php';	

// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);


use Dompdf\Dompdf;
use Dompdf\Options;


// Verificar si se ha enviado el código de la propiedad
if (!isset($_GET['codigo'])) {
    die("Error: No se ha proporcionado el código de la propiedad.");
}

// Obtener el código de la propiedad
$codigo = intval($_GET['codigo']);

// Consultar la base de datos para obtener los datos de la propiedad
$sql = "SELECT * FROM proprieter WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $codigo);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró la propiedad
if ($result->num_rows === 0) {
    die("Error: No se encontró la propiedad con el código proporcionado.");
}

// Obtener los datos de la propiedad
$propiedad = $result->fetch_assoc();




// Obtener fecha actual y formatearla 
$fecha = new DateTime();
$fecha_formateada = (string)$fecha->format('d').' de '.((string)array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre')[$fecha->format('m')-1]).' de '.(string)$fecha->format('Y');
// Obtener día, mes y año por separado
$dia = (string)$fecha->format('d');
$mes = (string)array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre')[$fecha->format('m')-1];
$año = (string)$fecha->format('Y'); 

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
            line-height: 1.2;
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
    <p class="bold" style="font-size: 14pt; text-align: center; ">CONTRATO DE ARRENDAMIENTO DE VIVIENDA </p>

    <p><strong></strong></p>

    <p>Entre los suscritos a saber: <strong>SOMOS PROPIEDAD S.A.S.</strong>, representada legalmente por <strong>MARIA NOHELIA BELTRAN MONTOYA,</strong> mayor de edad y vecino de Sabaneta, identificado con la cédula de ciudadanía No. <strong>42.820.973</strong> expedida en Sabaneta, quien en adelante y para efectos de este contrato se denominará <strong>LA ARRENDADORA,</strong> de una parte <strong>'. 
    $propiedad['nombre_inquilino'] .' </strong>mayor (es) de edad, identificado(a)(s) con la(s) cédula(s) de ciudadanía cuyo(s) número(s) aparece(n) al pie de su(s) firma(s), quien(es) en adelante se denominará(n) <strong>EL (LA-LOS) ARRENDATARIO (A-S)</strong>, se ha celebrado un contrato de arrendamiento contenido en las siguientes cláusulas:</p>
    
    <p><strong>PRIMERA. OBJETO.</strong> LA ARRENDADORA entrega a título de mera tenencia bajo la figura de arrendamiento a EL ARRENDATARIO y este lo recibe bajo el mismo título, el inmueble ubicado en la dirección: '. $propiedad['direccion'] .'., cuyos linderos se podrán relacionar en documento separado, el cual formará parte integral de este contrato; y a su turno, EL ARRENDATARIO se obliga consciente, libre y voluntariamente a pagar a EL ARRENDADOR tanto el precio establecido en el presente contrato, como cualquier otro emolumento, indemnización, penalidad, reparación, gestión de cobro y demás que se deriven del uso y goce del inmueble arrendado, así como de la ejecución o terminación del presente contrato. PARAGRAFO. No obstante, el área y linderos que reposen en el documento anexo, el inmueble se entrega como cuerpo cierto y no por cabida, especialmente en aquellos casos en los cuales el mismo forma parte de un predio de mayor extensión.
    </p>
    
    <p><strong>SEGUNDA. DURACIÓN.</strong> El término inicial del presente contrato de arrendamiento es de Doce meses (12), contado (s) a partir del (Ingresar fecha). El presente contrato se prorrogará por el mismo término inicial, siempre que cada una de las partes haya cumplido con las obligaciones a su cargo, en especial las relativas al pago del canon y sus reajustes; todo lo anterior salvo que cualquiera de las partes, manifieste por escrito con una anticipación mínima de tres (3) meses al vencimiento de la vigencia en curso, su intención de no renovarlo o prorrogarlo.</p>

    <p><strong>TERCERA. PRECIO.</strong> El valor mensual del canon de arrendamiento es de $ '. $propiedad['valor_canon'] .' pagaderos en forma anticipada, dentro de los cinco (5) primeros días de cada periodo, los pagos deben hacerse al arrendador a través de los siguientes medios de pago: punto pago CODIGO QR, o consignando en la cuenta a nombre de Somos Propiedad S.A.S en Bancolombia Cuenta Corriente N. 01700624213.<br>
    <strong>PARÁGRAFO PRIMERO. </strong>La mera tolerancia de EL ARRENDADOR en aceptar el pago con posterioridad al plazo previsto para tal fin, o la estipulación de fechas de pago distintas en la factura o documento equivalente, no modificarán ni alterarán de forma tácita ni expresa, las previsiones que al respecto han acordado las partes mediante el presente contrato. Tampoco se considerarán variadas las estipulaciones relativas al precio del arrendamiento por la recepción de pagos parciales, toda vez que su aceptación no invalida los efectos que la mora produzca a cargo de EL ARRENDATARIO.<br>
    <strong>PARÁGRAFO SEGUNDO. </strong>En caso de mora en el pago del canon de arrendamiento o de cualquier otra erogación a cargo de EL ARRENDATARIO, este reconocerá de forma adicional y pagará durante ella a EL ARRENDADOR, o a la entidad que se hubiere designado para el recaudo, el valor correspondiente a la gestión de cobro, incluyendo honorarios y los intereses moratorios calculados a la tasa máxima permitida por la ley, que se hubieren causado con ocasión del incumplimiento; dichos intereses serán calculados a partir del día siguiente al vencimiento del plazo de pago estipulado en el presente contrato. Si el pago se hiciere en cheque y éste resultare impagado, EL ARRENDATARIO pagará, además, la sanción del veinte por ciento (20%) del valor del cheque, de conformidad con lo dispuesto en el artículo 731 del Código de Comercio. Todo lo anterior sin perjuicio de que EL ARRENDADOR pueda iniciar las acciones que por el incumplimiento correspondan, sin necesidad de requerimiento previo alguno.<br>
    <strong>PARÁGRAFO TERCERO. </strong>En Caso, que EL ARRENDATARIO realice el pago a través de medios electrónicos o transferencias que se autoricen para tal efecto, se obliga a pagar el costo de la operación, incluyendo los impuestos financieros. De la misma manera se obliga a hacer llegar la respectiva copia de la operación al correo electrónico cartera@somospropiedad.com a más tardar al día siguiente de haberla efectuado, pues de lo contrario, el pago no podrá ser aplicado, y no se tendrá como efectivamente realizado, situación que deberá ser tenida en cuenta por EL ARRENDATARIO para la mora que pueda generar el no pago dentro de los términos aquí expresados.<br>
    <strong>PARAGRAFO CUARTO. </strong>En ningún evento podrá el arrendatario justificar el no pago del canon, argumentando que no ha recibido la factura por parte del arrendador, pues la factura es un soporte contable que en nada invalida la obligación de pago que asume EL ARRENDATARIO.
    </p>

    <p><strong>CUARTA. INCREMENTOS.</strong> Cada doce (12) meses de ejecución contractual, el canon de arrendamiento será incrementado de manera automática en una proporción igual al tope máximo permitido por las disposiciones vigentes al momento en que tenga lugar el reajuste. A la fecha de suscripción del presente documento, el tope se encuentra fijado en el cien por ciento (100%) del incremento que haya tenido el Índice de Precios al Consumidor (I.P.C) en el año calendario inmediatamente anterior, conforme lo consagra el artículo 20 de la Ley 820 de 2003. En caso tal, de que dicha normatividad sea modificada, las nuevas disposiciones se entenderán como modificatorias del contrato y por ello adheridas al mismo, quedando obligado EL ARRENDATARIO a su pago y reconocimiento. Este reajuste se hace obligatorio, aunque el inmueble se encuentre pendiente de ser restituido por desahucio, por existir sentencia que así lo indique, conciliación o transacción suscrita por las partes en relación con la terminación o en aquellos casos en que EL ARRENDADOR haya comunicado a EL ARRENDATARIO que el contrato no será prorrogado. El incremento obrará desde la fecha en que inicia la respectiva prórroga, lo cual será reflejado en la facturación del respectivo mes. </p>

    <p><strong>QUINTA. DESTINACIÓN. </strong>El bien inmueble que en virtud de este contrato se entrega en arriendo, será destinado: EXCLUSIVAMENTE PARA VIVIENDA.<br>
    <strong>PARAGRAFO.</strong> Queda totalmente prohibido a EL (LA-LOS) ARRENDATARIO (A-S) destinar el bien, así sea parcialmente, para usos o actividades diferentes a las señaladas en este contrato, o contrarias a la moral y las buenas costumbres, o a reglamentos de propiedad horizontal, de vecindad, convivencia, Comunidad o estatutos de parcelación o contrarios a cualquier otra norma legal que regule la destinación del bien.
    </p>

    
    <p><strong>SEXTA. ENTREGA E INVENTARIO.</strong> El ARRENDATARIO declara que ha recibido el inmueble arrendado en el estado visto y negociado para su uso y goce de conformidad con el inventario y/o acta que se firma como anexo, documento que forma parte integral del presente contrato.<br>
    <strong>PARÁGRAFO:</strong> EL ARRENDATARIO, faculta y autoriza de forma expresa a sus DEUDORES SOLIDARIOS, para que suscriban el inventario inicial y/o el acta de entrega final, y reciba(n) y/o entregue el inmueble, esto último, a la finalización del contrato. Materializada la anterior autorización, no podrá EL ARRENDATARIO oponerse con posterioridad, ni discutir la fecha de iniciación del contrato, el estado en que se encontraba el inmueble al momento del inventario inicial o responsabilidades derivadas de la entrega final, si es el caso. De la misma manera operará cuando se trate de un tercero autorizado por El ARRENDATARIO.
    </p>

    

    <p><strong>SEPTIMA. SERVICIOS PÚBLICOS DOMICILIARIOS.</strong> EL ARRENDATARIO se obliga a pagar en todos los casos: 1) Los consumos facturados, así como aquellos dejados de facturar, siempre que los mismos se hayan causado durante la ejecución del contrato, 2) Las sanciones, costos adicionales y multas que las Empresas de Acueducto, Empresas Públicas de Energía Eléctrica, Teléfono, gas, y en general cualquier otra Autoridad o empresa, que se impongan durante la vigencia del presente contrato por las infracciones de los respectivos reglamentos o por no haber pagado oportunamente tales servicios.<br>
     <strong>PARÁGRAFO PRIMERO</strong> - EL ARRENDATARIO se obliga a presentar a EL ARRENDADOR cuando este los requiera, las facturas o recibos de pago de servicios públicos, debidamente cancelados, al día y sin la existencia de acuerdos de pago no autorizados por EL ARRENDADOR. EL ARRENDADOR podrá abstenerse de recibir el canon de arrendamiento cuando EL ARRENDATARIO no presente los recibos o facturas señaladas.<br>
    <strong>PARÁGRAFO SEGUNDO:</strong> EL ARRENDATARIO cancelará a EL ARRENDADOR, la suma correspondiente a cualquier multa, sanción, o ajuste de consumo, señalado por la respectiva empresa prestadora de servicios públicos y que, a él, esto es, a EL ARRENDATARIO le corresponda, dentro de los cinco (5) días hábiles siguientes a que la factura se ponga a su disposición, y le sea avisada por cualquier medio o por interpuesta persona dicha situación. La responsabilidad sobre el pago de lo aquí señalado permanecerá vigente, y exigible, de forma indeterminada; es decir, que, incluso habiéndose recibido el inmueble.<br>
    <strong>PARÁGRAFO TERCERO:</strong> EL ARRENDATARIO no podrá adquirir ningún tipo de crédito, póliza de seguro, que se facture o financie mediante la inclusión de dichos conceptos en los recibos de servicios públicos del acueducto, alcantarillado, aseo, energía, gas combustible y telefonía pública. Lo anterior, en razón, a que no existe autorización escrita de EL ARRENDADOR; ignorar lo anteriormente señalado se tendrá como causal especial y expresa de incumplimiento del contrato de arrendamiento, con lo cual EL ARRENDATARIO se constituirá en deudor de la pena pecuniaria consagrada para ello a favor de EL ARRENDADOR.<br>
    <strong>PARÁGRAFO CUARTO:</strong> Sin perjuicio de que el contrato de condiciones uniformes de las respectivas empresas de servicios públicos, e incluso un análisis extensivo de la Ley 142 de 1994, conceda la facultad de autorizar o solicitar el cambio de los instrumentos de medición por cualquier causa, EL ARRENDATARIO renuncia irrevocablemente a dicho derecho, y por lo tanto deberá solicitar autorización por escrito a EL ARRENDADOR cuando el cambio o reparación se requiera. Los emolumentos derivados de la revisión, reparación o cambio de los equipos de medición serán de cargo exclusivo de EL ARRENDATARIO siempre y cuando autorice los cambios sin contar con EL ARRENDADOR, razón por la cual se prohíbe la utilización de mecanismo de financiación vía facturación mensual alguno. En caso tal de que EL ARRENDATARIO contraviniendo lo descrito en la presente cláusula, omita la solicitud escrita de autorización, o proceda con la financiación referida, deberá cancelar cualquier valor insoluto a la terminación del presente contrato o en el momento en que se tenga noticia de la situación descrita. Así las cosas, EL ARRENDATARIO autoriza a EL ARRENDADOR para descontar del canon pagado, el valor que corresponda y cubra el monto adeudado a la respectiva empresa de servicios, debiendo, reintegrar el monto descontado, dentro de los cinco (5) días hábiles siguientes a la notificación escrita que remita EL ARRENDADOR so pena de incurrir en las penas e indemnizaciones contemplados en el presente contrato, sin perjuicio del respectivo reporte a las centrales de riesgo.<br>
    <strong>PARAGRAFO QUINTO.</strong> Se deja expresa constancia, que el inmueble se arrienda Sin Línea Telefónica. Si durante la vigencia de este contrato se instalare en el inmueble arrendado una o más líneas telefónicas por parte de EL ARRENDATARIO, este se compromete al pago del servicio telefónico correspondiente y a trasladar dichas líneas o empaquetamientos, si es el caso, antes de la entrega definitiva del inmueble. El cumplimiento de este requisito será condición necesaria para que EL ARRENDADOR reciba el inmueble a la finalización del contrato.</p>

    
    <p><strong>OCTAVA. OBLIGACIONES DEL ARRENDATARIO.</strong> Son obligaciones de <strong>EL ARRENDATARIO</strong> adicionales a la de recibir el inmueble en la fecha y términos acordados, las siguientes: 1. Pagar, dentro del plazo previsto para tal efecto, el precio que se ha fijado como canon de arrendamiento, así como sus correspondientes incrementos, y todas aquellas erogaciones que se encuentren a su cargo; 2. Abstenerse de usar el bien para fines distintos a los estipulados en el contrato; 3. Observar y dar cumplimiento al reglamento de propiedad horizontal, manual de convivencia, y a las indicaciones efectuadas por la Asamblea de Copropietarios y la Administración de la Propiedad Horizontal cuando corresponda, obligación que incluye, hacer un uso adecuado de las zonas comunes de la copropiedad si el inmueble se encuentra ubicado dentro de una de ellas, uso que hace referencia, entre otros, a: no utilizar parqueaderos de visitantes para uso personal o privado; acogerse a el reglamento establecido para el uso de zonas húmedas y sus respectivos horarios; no generar ruido exagerado y respetar los horarios para el desarrollo de fiestas y actividades similares, respetar el personal de vigilancia, aseo, portería y administración. Si el arrendatario, sus huéspedes o dependientes incurren en conductas que violen o atenten contra el reglamento y ello genere multas, deberá el arrendatario pagarlas, obligación que deberá cumplir frente a la copropiedad durante la vigencia del contrato. De no cumplir con dicha obligación, podrá el arrendador cargar dicho concepto en el estado de cartera del arrendatario una vez acreditado por la copropiedad y al hacer el pago del arrendamiento, podrá el arrendador descontarse dicho concepto como primera opción en la aplicación de dicho pago. En todo caso, si a la terminación del contrato, <strong>EL ARRENDATARIO</strong> no aporta el paz y salvo por dicho concepto, el arrendador se podría inhibir de recibir el inmueble, pues será condición necesaria que deberá cumplir el arrendatario a la entrega; 4. Conservar, mantener el inmueble en buen estado de conservación; 5. Ejecutar, a su costa y bajo su entera responsabilidad, las reparaciones locativas que requiera el inmueble para su conservación durante la ejecución del contrato, y en especial al momento de su restitución; 6. Informar oportunamente a <strong>EL ARRENDADOR</strong> sobre la ocurrencia de daños que demanden la ejecución de reparaciones necesarias, y asumir aquellas que se hayan hecho necesarias por su culpa, o por la renuencia a permitir que las mismas sean realizadas por <strong>EL ARRENDADOR</strong>; 7. Pagar oportunamente, los servicios públicos domiciliarios que se encuentren a su cargo de conformidad a lo estipulado en este contrato y demás erogaciones derivadas de los mismos que se encuentren a su cargo, o hayan sido solicitadas por él; 8. Pagar a <strong>EL ARRENDADOR</strong> o la entidad que adelante tal fin, la gestión de cobro y los intereses moratorios en caso de mora en el cumplimiento de las obligaciones dinerarias contraídas con la suscripción del presente contrato, es decir, si diere lugar a alguna diligencia de cobro judicial o extrajudicial, EL ARRENDATARIO se obliga a pagar a la entidad encargada de tal gestión, los costos que ello genere. lo anterior sin perjuicio de lo estipulado por la entidad aseguradora o afianzadora en sus manuales de cobranza, y aquello que el juez ordenare acerca de las costas judiciales; 9. Restituir el inmueble a la terminación del contrato en las mismas condiciones que lo recibió de conformidad al inventario inicial. De no existir inventario, la entrega se regirá por lo establecido en la ley. 10. Informar oportunamente a El ARRENDADOR, cuando haya requerimientos de autoridades judiciales, administrativas o cualquiera sea su naturaleza, en general, avisos, denuncias, mandatos, peticiones, de que tenga conocimiento y que afecten el inmueble o a su propietario. Esta obligación la surtirá notificando vía correo electrónico a la dirección indicada por el ARRENDADOR, esto es, al correo administracion@somospropiedad.com, notificación que deberá surtir EL ARRENDATARIO a más tardar al día inmediatamente siguiente a haber sido conocedor de la situación. La omisión de esta obligación generará a cargo a favor de EL ARRENDATARIO los perjuicios ocasionados a EL ARRENDADOR y/o a EL PROPIETARIO DEL INMUEBLE. 11. Permitir al arrendador y/o al propietario del inmueble durante el término inicial o el de sus prorrogas, por intermedio de sus empleados, personas o grupo de personas autorizadas, visitar el inmueble arrendado, tomar fotografías, si el caso, salvaguardando los bienes de EL ARRENDATARIO. Esta visita se programará con previa notificación a EL ARRENDATARIO y en los días y hora hábiles. 12. - Pagar por su cuenta todos los gastos que ocasione el presente contrato, los de su prórroga o renovación, llegado el caso, y en especial, la comisión por arrendamiento que a la celebración del contrato equivale a 20% sobre el valor del canon y los gastos de contrato. 13. De llegar a adquirir el inmueble arrendado bajo cualquier modalidad de negociación, esto es, compraventa, permuta, entre otras, deberá informar a <strong>EL ARRENDADOR</strong> oportunamente en forma escrita. La compraventa generará a favor de <strong>EL ARRENDADOR</strong> una comisión equivalente al tres (3%) sobre el valor de la negociación, pago que realizará a la suscripción del documento contentivo de la negociación. 14. Las demás que se deriven del presente contrato o de la ley.<br>
    <strong>PARÁGRAFO PRIMERO.</strong> Sin perjuicio de lo aquí dispuesto, algunas de las obligaciones de <strong>EL ARRENDATARIO</strong> son objeto de especial regulación en este mismo contrato.<br>
    <strong>PARÁGRAFO SEGUNDO:</strong> Para efecto de los inmuebles que se encuentren bajo el régimen de propiedad horizontal, <strong>EL ARRENDATARIO</strong>, notificara a <strong>EL ARRENDADOR</strong> por cualquier medio, aunque de preferencia al correo electrónico <strong>administracion@somospropiedad.com</strong> de citaciones o convocatorias a la <strong>ASAMBLEA GENERAL</strong>, que tengan como destinatario al propietario del inmueble arrendado, lo anterior, para evitar que el Propietario sea sancionado por la copropiedad por no asistir a la Asamblea.<br>
    <strong>PARAGRAFO TERCERO.</strong> De llegar el arrendatario a ser sancionado por contravenciones al Reglamento y dichas sanciones sean facturadas por la copropiedad, podrá EL ARRENDADOR cargar dichos conceptos en la cartera de EL ARRENDATARIO y de transcurrir un (1) mes contado desde la notificación de la copropiedad, sin que EL ARRENDATARIO haya pagado tal sanción, EL ARRENDADOR la hará efectiva y la descontará del valor del canon que cancele EL ARRENDATARIO en el período en que se venzan el mes de plazo. De la misma manera se procederá, cuando el arrendatario, huéspedes y dependientes, causen daños a las zonas comunes, daños debidamente acreditados por la copropiedad. En todo caso a la terminación del contrato y consecuente entrega material del inmueble, EL ARRENDATARIO deberá estar a paz y salvo por estos conceptos y podrá EL ARRENDADOR abstenerse de recibir el inmueble hasta tanto el arrendatario cumpla con dicha obligación con la correspondiente generación de arrendamientos.<br>
    <strong>PARAGRAFO  CUARTO.  EL  ARRENDATARIO</strong>  deberá  tener  los cuidados básicos  y de mantenimiento propios del tipo de piso del inmueble arrendado. De la misma manera y de conformidad con deber de cuidado que se encuentra en cabeza de <strong>EL ARRENDATARIO</strong>, este se obliga especialmente a conservar la integridad interior y limpieza de las paredes, techos, bajantes, canoas, desagües, cañerías, reponiendo pisos, tejas que durante el arrendamiento se quiebren o se desencajen, como también a reponer los cristales quebrados en las ventanas, puertas y a mantener en estado de servicio las puertas, ventanas y cerraduras y propugnar por el mantenimiento y conservación de la red hidrosanitaria. Si a la fachada o en el exterior del inmueble arrendado, se pintaren emblemas o slogans, estos deberán ser borrados y pintados del color original y a satisfacción de <strong>EL ARRENDADOR</strong> a la entrega del inmueble.<br>
    PARAGRAFO QUINTO. RENUNCIAS. El Arrendatario (a-s) y deudores solidarios, expresamente renunciamos a: <strong>A.</strong> El derecho de retención que en algunos casos consagran las leyes que rigen este tipo de contratos. <strong>B.</strong> Exigir el pago de indemnizaciones por las mejoras y/o reparaciones que hayamos realizado sobre el bien, contrariando los términos de este contrato. <strong>C.</strong> Que se nos requiera previamente, de forma judicial o extrajudicial, para ser constituidos en mora de cualquiera de las obligaciones generadas del contrato o cuando a ello haya lugar.
    </p>

    <p><strong>NOVENA. PROHIBICIONES ESPECIALES PARA EL ARRENDATARIO:</strong> Al arrendatario le estará prohibido: 1. Ceder el contrato y/o el goce del inmueble total o parcialmente y subarrendarlo total o parcialmente. 2. Cambiar total o parcialmente la destinación del inmueble. 3. Efectuar mejoras de cualquier clase en el inmueble cuando no medie autorización expresa y por escrito de EL ARRENDADOR para tal efecto. 4. Hacerse sustituir por otras personas en la relación tenencial bien sea mediante cesión de este contrato o por otro medio cualquiera que tenga como efecto la mutación de las personas que ocuparán el inmueble. 5.Las demás que estipule la ley o el presente contrato. <strong>PARAGRAFO. FIJACIÓN DE AVISOS. EL ARRENDATARIO</strong> no podrá fijar ni pintar en los muros, puertas, ventanas del inmueble, avisos de ninguna naturaleza, sin autorización escrita de <strong>EL ARRENDADOR</strong> y la previa autorización de la autoridad competente incluyendo la administración de la copropiedad, cuando el inmueble se encuentre sometido el Régimen de Propiedad Horizontal. Con todo, <strong>EL ARRENDATARIO</strong> mantendrá indemne a <strong>EL ARRENDADOR</strong> y al propietario del inmueble, frente a las acciones judiciales, administrativas, policivas o similares, que la desatención de lo aquí establecido, puedan derivar; y más aun teniendo en cuenta que el presente contrato tiene como destinación exclusiva <strong>VIVIENDA FAMILIAR</strong>. 4. Las demás estipuladas en el presente contrato y/o en la ley.
    </p>

    <p><strong>DECIMA. CLÁUSULA PENAL.</strong> El incumplimiento o, cumplimiento tardío de cualquiera de las obligaciones que por este contrato y por la ley asume <strong>EL ARRENDATARIO</strong>, lo constituirán en deudor de <strong>EL ARRENDADOR</strong>, en una suma equivalente a tres (3) cánones de arrendamiento vigentes al momento del incumplimiento o del cumplimiento tardío, suma exigible sin necesidad de los requerimientos previos ni constitución en mora, derechos estos a los que renuncia expresamente <strong>EL ARRENDATARIO</strong> así como a cualquier otro que establezcan las normas de carácter procesal o sustancial sin perjuicio de que el arrendador pueda exigir por separado la restitución del inmueble, el cumplimiento de la obligación principal y la indemnización de los perjuicios sufridos como consecuencia del incumplimiento.<br>
    <strong>PARAGRAFO.</strong> Esta pena se pacta por el simple incumplimiento y no será necesario requerimiento alguno para su exigibilidad. Bastará que se presente la prueba del incumplimiento para que la suma señalada sea exigible ejecutivamente.</p>

    <p><strong>DECIMA PRIMERA. INCUMPLIMIENTO DEL CONTRATO.</strong> El incumplimiento en el pago del canon de arrendamiento en la forma convenida en el contrato y/o la violación de cualquiera de las cláusulas por parte de EL (LA-LOS) ARRENDATARIO (A-S), faculta a la arrendadora para exigir de inmediato la entrega del inmueble, sin necesidad de desahucio o requerimiento alguno, a los cuales renuncia(n) expresamente EL (LA-LOS) ARRENDATARIO (A-S), si a ello hubiere lugar.</p>

    <p><strong>DECIMA SEGUNDA. SOLIDARIDAD.</strong> Los suscritos:</p>

    <p><strong>INGRESAR NOMBRE Y TELÉFONO DE LOS DEUDORES</strong></p>
    
    <br>
    

    <p><strong>NOMBRE: '. $propiedad['nombre_codeudor_uno'] .' </strong></p>

    <p><strong>CC: '. $propiedad['cc_codeudor_uno'] .'</strong></p>

    <p><strong>TELEFONO: '. $propiedad['telefono_codeudor_uno'] .'</strong></p>

    <br>
    

    <p><strong>NOMBRE: '. $propiedad['nombre_codeudor_dos'] .' </strong></p>

    <p><strong>CC: '. $propiedad['cc_codeudor_dos'] .'</strong></p>

    <p><strong>TELEFONO: '. $propiedad['telefono_codeudor_dos'] .'</strong></p>

    <br>
    <br>

    <p>Por medio del presente documento nos declaramos deudores de <strong>EL ARRENDADOR</strong> en forma solidaria e indivisible junto con <strong>EL ARRENDATARIO (INGRESAR NOMBRE)</strong>, de todas las cargas y obligaciones contenidas en el presente contrato, tanto durante el término inicialmente pactado como durante sus prórrogas o renovaciones expresas o tácitas, y hasta que se produzca la entrega material del inmueble a <strong>EL ARRENDADOR</strong>, por concepto de cánones de arrendamiento, servicios públicos, indemnizaciones, daños en el inmueble, cuotas de administración, intereses moratorios, gestiones de cobro y honorarios derivados de la mora por las gestiones de cobranza extrajudicial o judicial, y las costas procesales a que sea condenado <strong>EL ARRENDATARIO</strong> en la cuantía señalada por el respectivo juzgado en el caso del proceso de restitución y/o ejecutivo, cláusulas penales, entre otros.; las cuales podrán ser exigidas por <strong>EL ARRENDADOR</strong> a cualquiera de los obligados por la vía ejecutiva, sin necesidad de requerimientos privados o judiciales a los cuales renunciamos expresamente, y sin que por razón de esta solidaridad asumamos el carácter de fiadores, ni arrendatarios del inmueble objeto del presente contrato, pues tal calidad la asume <strong>(INGRESAR NOMBRE) y sus respectivos causahabientes. Todo lo anterior sin perjuicio de que en caso de abandono</strong> del inmueble cualquiera de los deudores solidarios pueda hacer entrega válidamente del inmueble a <strong>EL ARRENDADOR</strong> o a quien este señale, bien sea judicial o extrajudicialmente, y para este exclusivo efecto <strong>EL ARRENDATARIO</strong> otorga poder amplio y suficiente a <strong>LOS DEUDORES SOLIDARIOS</strong> en este mismo acto mediante la suscripción del presente contrato. <strong>EL ARRENDATARIO, y LOS DEUDORES SOLIDARIOS</strong> Autorizamos a <strong>EL ARRENDADOR</strong> o a quien represente sus derechos u ostente en el futuro la calidad de acreedor, para que, en los mismos términos señalados en la cláusula vigésima de este contrato, consulten, suministren, reporten, procesen, y divulguen toda nuestra información, que se refiera al comportamiento crediticio, financiero, comercial a cualquier otra entidad encargada del manejo de datos comerciales, personales o económicos. <strong>PARAGRAFO.</strong> A la muerte de EL (LA-LOS) ARRENDATARIO (A-S) LA ARRENDADORA podrá acogerse al artículo. 1.434 del Código Civil., respecto a uno cualquiera de sus herederos, iniciando y/o continuando el juicio con él sin necesidad de notificar o demandar a los demás.
    </p>



    <p><strong>DECIMA TERCERA. ACEPTACIÓN DE CESIÓN FUTURA POR PARTE DE EL ARRENDADOR. EL ARRENDATARIO, y LOS DEUDORES SOLIDARIOS</strong> consentimos desde ahora cualquier cesión que <strong>EL ARRENDADOR</strong> haga respecto del presente contrato y aceptamos expresamente, que la notificación de que trata el artículo 1960 del Código Civil se surta con el envío por correo certificado y a la dirección que registramos al pie de nuestra firma, de la copia de la respectiva nota de cesión acompañada de la copia simple del contrato, y las direcciones donde se recibirán todas las notificaciones relacionadas directamente o indirectamente con este contrato. La presente notificación de cesión no podrá ser objeto de oposición alguna por parte de <strong>EL ARRENDATARIO</strong>, ni de sus <strong>DEUDORES SOLIDARIOS</strong>.</p>

    <p><strong>DECIMA CUARTA. CAUSALES DE TERMINACIÓN DEL CONTRATO POR PARTE DEL ARRENDADOR. EL ARRENDADOR</strong> podrá dar por terminado el presente contrato por los siguientes motivos: 1. Cuando EL ARRENDATARIO no realice el pago del canon de arrendamiento dentro de los términos acordados en el presente contrato. 2. Cuando el no pago de los servicios públicos cause la suspensión, desconexión o pérdida del servicio. 3. Cuando subarriende total o parcialmente el inmueble. 4. Cuando ceda la tenencia del inmueble y/o el contrato. 5. Cuando de al inmueble un uso y/o destinación diferente a la pactada en el contrato. 5. Cuando <strong>EL ARRENDATARIO</strong> reiteradamente afecte la tranquilidad de los vecinos o destine el inmueble para actos delictivos o que impliquen contravención alguna al régimen de convivencia y/o al reglamento de propiedad horizontal o normas que lo complementen o modifiquen. 6. Cuando <strong>EL ARRENDATARIO</strong> realice mejoras, adiciones, cambios o ampliaciones en el inmueble, o lo destruya total o parcialmente; 7. Cuando <strong>EL ARRENDATARIO</strong> no realice el pago de las expensas comunes cuando el dicho pago se encuentre a su cargo. 8. Cuando <strong>EL PROPIETARIO o POSEEDOR</strong> necesite el inmueble para ocuparlo, o cuando el inmueble haya de demolerse para efectuar una nueva construcción, o cuando se requiere desocupado con el fin de ejecutar obras indispensables para su reparación o el inmueble haya de entregarse en cumplimiento de las obligaciones originadas en un contrato de compraventa. 9. Cuando el (los) Arrendatario(s), el(los) deudor(es) solidario(s), garante(s) o avalista(s), se encuentren vinculados por parte de las autoridades competentes a cualquier tipo de investigación por delitos de narcotráfico, terrorismo, secuestro, lavado de activos, financiación del terrorismo, administración de recursos relacionados con dichas actividades o en cualquier tipo de proceso judicial relacionado con la comisión de los anteriores delitos. También dará terminación cuando sean incluidos en listas para el control de lavados de activos y financiación del terrorismo administradas por cualquier autoridad nacional o extranjera y/o condenados por parte de las autoridades competentes en cualquier tipo de proceso judicial relacionado con la comisión de los anteriores delitos. 10. El incumplimiento de las normas ambientales o requerimiento de autoridades competentes, especialmente en relación con las actividades desarrolladas en o con el inmueble, así como ser sancionados por dicho incumplimiento. 11. Las demás estipuladas en la Ley o en el presente contrato.<br>
    <strong>PARAGRAFO.</strong> Cuando el predio arrendado o sector donde se encuentra ubicado el bien arrendado, sea requerido por parte de alguna entidad gubernamental bien sea del orden Nacional, Departamental o Municipal para el desarrollo de un proyecto urbanístico concebido dentro de un Plan de Ordenamiento Territorial o en general para desarrollar una obra de carácter público, este hecho, generará una justa causa de terminación del contrato de arrendamiento y por lo tanto, una vez EL ARRENDADOR sea informado de tal situación dará aviso al ARRENDATARIO para proceder con la terminación del contrato de arrendamiento y la consecuente restitución del inmueble, entrega que se dará de conformidad al requerimiento presentado por el ente competente. Ahora bien, por tratarse de un evento de fuerza mayor donde el interés público debe prevalecer ante el interés particular, la terminación anticipada del contrato no dará lugar al cobro de perjuicios y pago de indemnización de ninguna naturaleza a ninguna de las partes. Si es EL ARRENDATARIO quien es notificado de tal decisión por parte de las autoridades competentes, estará obligado a notificar al ARRENDADOR de manera inmediata, allegando el escrito mediante el cual la autoridad lo notificó, de obviar esta información, deberá asumir las sanciones o cargas contractuales derivadas de tal comportamiento.
    </p>

    <p><strong>DECIMA QUINTA. DEVOLUCIÓN SATISFACTORIA DEL INMUEBLE</strong> A la terminación del contrato, <strong>EL ARRENDATARIO</strong> deberá restituir el inmueble en las mismas condiciones en que lo recibió. El contrato deberá restituirse a paz y salvo por concepto todo concepto derivado, del mismo.<br>
    <strong>PARAGRAFO PRIMERO.</strong> En caso de que existan obligaciones pendientes de pago a cargo de <strong>EL ARRENDATARIO</strong> o que el inmueble no esté en las condiciones pactadas para su restitución, <strong>EL ARRENDADOR</strong> podrá negarse a recibir el inmueble; y en este caso <strong>EL ARRENDATARIO y LOS DEUDORES SOLIDARIOS</strong> mantendrán a su cargo las obligaciones contraídas en virtud de este contrato, hasta tanto se logre la devolución a entera satisfacción.<br>
    <strong>PARAGRAFO SEGUNDO.</strong> Si la entrega del inmueble procede de una terminación invocada por cualquiera de las partes o un mutuo acuerdo y llegada la fecha de vencimiento del contrato, EL ARRENDATARIO no cumple con la entrega material, las obligaciones inherentes al contrato se seguirán causando hasta la fecha en que se surta la entrega sin que ello represente prórroga o renovación alguna del contrato, pues tal decisión deberá ser concertada entre las partes.<br>
    <strong>PARÁGRAFO TERCERO:</strong> En caso de incumplimiento de cualquiera de las obligaciones del presente contrato, y en especial, aquellas relacionadas con el pago a tiempo de los cánones de arrendamiento y la destinación del inmueble arrendado, <strong>EL ARRENDATARIO</strong> autoriza y otorga poder especial y expreso a sus <strong>DEUDORES SOLIDARIOS</strong>, para efectuar los trámites tendientes a la terminación del contrato y/o entrega del inmueble; entendiéndose por ello que podrán: 1. Enviar a <strong>EL ARRENDADOR</strong> el aviso de desahucio dentro de los términos y con la antelación consagrada en el presente contrato o en la ley. 2. Efectuar la entrega material del inmueble y suscribir el correspondiente inventario. Se precisa que por el hecho de que <strong>EL ARRENDADOR</strong> reciba sus comunicaciones y acceda a cualquier negociación tendiente a restituir el inmueble arrendado, en ningún evento podrá entenderse la presente autorización como una subrogación, condonación, novación, o cualquier otra figura que modifique la esencia del presente contrato.<br>
    <strong>PARAGRAFO CUARTO: ABANDONO DEL INMUEBLE.</strong> Al suscribir este contrato EL ARRENDATARIO faculta expresamente a <strong>EL ARRENDADOR</strong> para ingresar en el inmueble y recuperar su tenencia con el solo requisito de la presencia de dos testigos, en procura de evitar el deterioro o el desmantelamiento del mismo, siempre que por cualquier circunstancia el inmueble permanezca abandonado y/o desocupado por el término de treinta (30) días y que la exposición al riesgo sea tal que amenace la integridad física del bien o la seguridad del vecindario o los residentes vecinos.
    </p>

    <p><strong>DECIMA SEXTA. AUTORIZACIÓN PARA REGISTRO EN BANCOS DE DATOS.</strong> El arrendatario y los deudores solidarios autorizan expresamente, de manera libre y desde el momento mismo de la firma del contrato a la <strong>INMOBILIARIA SOMOS PROPIEDAD SAS</strong> y a la compañía AFIANZADORA Y/O ASEGURADORA elegida por el arrendador, para incorporar, reportar, procesar, consultar y divulgar en Bancos de Datos la información que se relacione con este contrato o que de él se derive, especialmente cualquier incumplimiento relativo a las obligaciones contraídas. Para efectos de esta autorización el arrendador puede actuar directamente o a través de sus asesores jurídicos o de la AFIANZADORA Y/O ASEGURADORA. La misma autorización se extiende a cualquier eventual cesionario ó subrogatario de las obligaciones derivadas del contrato. Declaramos que hemos leído y comprendido a cabalidad el contenido de la presente autorización, y aceptamos la finalidad en ella descrita y las consecuencias que se derivan de ella <strong>(LEY 1266 de 2008)</strong>.<br>
    <strong>PARAGRAFO. TRATAMIENTO DE DATOS PERSONALES:</strong> De acuerdo al artículo 10 del decreto 137 de 2013, que reglamenta la ley 1581 de 2012, como derecho constitucional que tienen todas las personas a conocer, actualizar y rectificar las informaciones que se hayan recogido sobre ellas en las bases de datos o archivos, y los demás derechos libertades y garantías constitucionales a que se refiere el artículo 15 de la constitución política; así como el derecho a la información consagrado en el artículo 20 de la misma. El Arrendatario y Deudores Solidarios, autorizamos expresamente a Somos Propiedad SAS para utilizar sus datos para enviar correos electrónicos de manera informativa, relacionados con nuevos productos y/o servicios, además de las notificaciones para dar cumplimiento a obligaciones contraídas, respuestas a solicitudes realizadas y las demás inherentes a la relación contractual. El tratamiento de los datos personales y los medios a través de los cuales <strong>SOMOS PROPIEDAD SAS</strong> hace el almacenamiento y uso, de los mismos son seguros y confidenciales, dado que contamos con las herramientas tecnológicas y el recurso humano idóneo para evitar el acceso no autorizado de terceras personas.
    </p>

    <p><strong>DECIMA SEPTIMA. SUBRROGACION A FAVOR DE TERCEROS.</strong> EL ARRENDATARIO y los deudores solidarios aceptan desde ahora, la FIANZA que la compañía AFIANZADORA Y/O ASEGURADORA elegida por el arrendador, llegue eventualmente a otorgar sobre las sumas que se causen con ocasión del presente contrato de Arrendamiento. En el evento en que la AFIANZADORA Y/O ASEGURADORA, afiance el cumplimiento de las obligaciones dinerarias derivadas del presente contrato, El Arrendatario y los deudores solidarios se obligan a: (1) Pagar y reconocer a favor de la compañía AFIANZADORA Y/O ASEGURADORA, las sumas que éste último llegue a pagar a favor de EL ARRENDADOR como resultado del incumplimiento. (2) Pagar y reconocer a favor de la compañía AFIANZADORA Y/O ASEGURADORA, intereses a la tasa más alta permitida por la ley así, como todos los gastos, honorarios de abogado, costos judiciales o extrajudiciales que se generen en la cobranza judicial y extrajudicial de las sumas adeudadas, así como en el proceso de restitución del inmueble que se adelante en contra de LOS ARRENDATARIOS.<br>
    <strong>PARAGRAFO.</strong> Si como resultado del incumplimiento de las obligaciones adquiridas por EL ARRENDATARIO, o sus deudores solidarios, la compañía AFIANZADORA Y/O ASEGURADORA paga al ARRENDADOR el valor total de las obligaciones dinerarias afianzadas, entonces la compañía AFIANZADORA Y/O ASEGURADORA se subrogará por el valor pagado y tendrá el derecho a recuperar las sumas pagadas incluidos sus intereses y gastos de cobranza. El pago que llegare a realizar la compañía AFIANZADORA Y/O ASEGURADORA no extingue parcial, ni totalmente la obligación de LOS ARRENDATARIOS ni de sus deudores solidarios.
    </p>

    <p><strong>DECIMA OCTAVA. ENAJENACIÓN DEL BIEN.</strong> LA ARRENDADORA no se hace responsable de los perjuicios que pueda sufrir EL (LA-LOS) ARRENDATARIO (A-S), generados en la venta o enajenación del bien arrendado, en todos aquellos casos en que tal circunstancia sea causa legal para la terminación contrato.</p>

    <p><strong>DECIMA NOVENA. PREVENCIÓN LAVADO DE ACTIVOS Y FINANCIACIÓN DEL TERRORISMO. EL ARRENDATARIO, LOS DEUDORES SOLIDARIOS</strong> y demás suscribientes de este contrato, declaramos que los recursos que percibimos provienen de actividades lícitas, de conformidad con la ley colombiana. De la misma manera declaramos que los recursos que lleguemos a entregar, sea a <strong>EL ARRENDADOR</strong>, y/o a sus cesionarios, endosatarios o subrrogatarios, no provienen de ninguna actividad ilícita de las contempladas en el Código Penal Colombiano o en cualquier otra norma que lo modifique o adicione.
    </p>

    <p><strong>VIGESIMA. EXENCIÓN DE RESPONSABILIDAD. EL ARRENDADOR Y/O EL PROPIETARIO</strong> no asumen responsabilidad alguna por daños, perjuicios o costos que <strong>EL ARRENDATARIO</strong> pueda sufrir o deba asumir por caso fortuito, fuerza mayor, causas atribuibles a terceros, la correcta o deficiente prestación de los servicios públicos; ni por la imposibilidad, indisponibilidad o dificultad técnica, de cobertura, física o estructural frente a la instalación de servicios adicionales o empaquetados tales como internet (Banda ancha, fibra óptica, entre otras), telefonía (Línea básica), televisión (Satelital, coaxial, entre otras) y similares. En este orden de ideas, estará a cargo de <strong>EL ARRENDATARIO</strong> efectuar las indagaciones frente a las copropiedades y las empresas relacionadas en la presente cláusula, sobre aspectos relacionados con la cobertura, viabilidad de instalación y cualquier otro concerniente a la prestación de dichos servicios, especialmente cuando no se haya indicado expresamente que el inmueble objeto del presente contrato cuenta con el mismo.<br>
    <strong>PARAGRAFO.</strong> El ARRENDADOR Tampoco se hace responsable por los deterioros que puedan sufrir los bienes y/o mercancías que se hallen dentro del inmueble, originados en causas no imputables a la voluntad de estos. De otro lado, en ningún caso LA ARRENDADORA se hace responsable de la solvencia moral y/o legal o de cualquier otra circunstancia subjetiva que no esté obligada a conocer con respecto al PROPIETARIO del inmueble que arrienda.
    </p>

    <p><strong>VIGÉSIMA PRIMERA. DIRECCIÓN PARA NOTIFICACIONES.</strong> Se deja expresa constancia que las direcciones consignadas en documento y/o los correos electrónicos, son los autorizados por los contratantes y garantes para cualquier notificación, sea judicial o extrajudicial, relacionada directa o indirectamente con el presente contrato de arrendamiento. PARAGRAFO. Los contratantes serán responsables de informar los cambios de la información aquí registrada.</p>

    <br>
    <br>
    <br>
    <br>
    <br>

    <p>
    LINDEROS<br>
    Derecha:<br>
    Izquierda:<br>
    Frente:<br>
    Atras:<br>
    </p>

    <br>
    <br>
    
    <p>_____________________________</p>
    <strong>
    SOMOS PROPIEDAD SAS.<br>
    NIT. 811008756-8<br>
    Dirección de notificación: Calle 73 Sur N. 45 A 60 LC 09<br>
    Correo electrónico: administracion@somospropiedad.com
    </strong>

    <br>
    <br>
    <br>

    <strong>ARRENDATARIO:</strong><br>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid black;">
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Nombre: '.$propiedad['nombre_inquilino'].'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Identificación: '.$propiedad['doc_inquilino'].'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Dirección Residencia: '. $propiedad['direccion'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Dirección laboral:</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Correo electrónico: '. $propiedad['email_inquilino'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Tels. fijos y móviles: '. $propiedad['telefono_inquilino'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;"><strong>FIRMA:</strong></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;"><strong>HUELLA:</strong></td>
        </tr>
        
    </table>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <strong>DEUDORES SOLIDARIOS:</strong><br>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid black;">
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Nombre: '. $propiedad['nombre_codeudor_uno'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Identificación: '. $propiedad['cc_codeudor_uno'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Dirección Residencia: '. $propiedad['direccion_codeudor_uno'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Dirección laboral:</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Correo electrónico: '. $propiedad['email_codeudor_uno'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Tels. fijos y móviles: '. $propiedad['telefono_codeudor_uno'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;"><strong>FIRMA:</strong></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;"><strong>HUELLA:</strong></td>
        </tr>
        
    </table>

    <br>
    <br>
    <br>
    

    <strong>DEUDORES SOLIDARIOS:</strong><br>

    <table style="width: 100%; border-collapse: collapse; border: 1px solid black;">
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Nombre: '. $propiedad['nombre_codeudor_dos'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Identificación: '. $propiedad['cc_codeudor_dos'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Dirección Residencia: '. $propiedad['direccion_codeudor_dos'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Dirección laboral:</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Correo electrónico: '. $propiedad['email_codeudor_dos'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;">Tels. fijos y móviles: '. $propiedad['telefono_codeudor_dos'] .'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;"><strong>FIRMA:</strong></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black; padding: 8px;"><strong>HUELLA:</strong></td>
        </tr>
        
    </table>

    <br>
    <br>
    <br>
    <p>Elaboro: ___________________________</p>

    <p>Reviso: ____________________________</p>

    <p>CONTRATO:</p>


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
$dompdf->stream('Contrato_de_Arriendo.pdf', ['Attachment' => true]);
