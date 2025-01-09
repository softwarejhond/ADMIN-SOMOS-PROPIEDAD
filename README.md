# ADMIN-SOMOS-PROPIEDAD
## MODULOS DE LA APLICACIÓN
1) Agenda
2) Carousel
3) Propiedades
4) SMTP
5) Configuracion
6) Notificaciones

## AGREGAR NUEVA PROPIEDAD
Para agregar una propiedad debemos tener en cuenta 
que los siguiente arvicos de requieren entre si
1) archivo nuevaPropiedadFrom.php
2) procesar_formulario
3) Carpeta microConsultas/tipoViviendas.pho

## MODULO AGENDA
Para que la agenga funcione de manera correcta rquerimos de
la API citas que se encuenrtra en la carpeta APIS

1) CARPETA APIS/citas/
2) Archivo agenda.php
3) CARPETA js/gestionAgenda.js
4) js/fullCalendar.js
5) js/real-time-calendar.js
6) CARPETA controller/agenda/agendaForm.php
7) CARPETA controller/microConsultas/addCitas.php

## MODULO RENOVACIÓN
Este modulo funciona para enviar notificación a los inquilinos de la 
renovacion del contrato y lo encontramos con

1) renovaciones.php
2) CARPETA controller/renovaciones/listaRenovaciones

Estamos implementando allí DataTable lo que ayuda a que se vea correctamente la 
información este modulo esta conectado a 2 hojas más las cuales son:
- actualizacionPropiedad.php
- actualizarPorcentajeLocal.php
- enviarRecordatorio.php

## MODULO GESTIÓN DE PROPIEDADES
Este modulo nos periitra organizar todas las propiedades, editarlas, eliminarlas y visualizar todo
incluso hasta tendremos el modulo de aceptar la propiedad cuando un usuario ingresa
### Propiedades en venta
Estas propiedades cuentan con los siguientes archivos
1) propiedadesVenta.php 
2) La CARPETA controller/propiedades/listaPropiedadesVenta.php

## MODULO CONTADORES
Este es el que nos permite ver los porcentajes de las propiedades en la pantalla inicial
y continiene las siguientes carpetas y archivos

1) contadores.php
2) CARPETA controller/obtener_proporciones.php

## MODULO VER DETALLE DE REGISTROS
Este es el que nos permite ver los detalles de cada registro de manera dinamica y con codigo reutilizable para otras vistas

1) vistaBase.php
2) verDetalle.php
3) funciones.php

## Características principales

- [x] 🔎 Consulta de detalles de registros
- [x] 👮 Codigo reutilizable


