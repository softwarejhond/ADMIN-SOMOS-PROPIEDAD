<?php
return [
    // Campos a incluir en el formulario
    'include_fields' => [
        'codigoReporte',
        'codigo_propietario',
        'situacionReportada',
        'fotoReporte'
    ],

    // Definición del campo 'codigoReporte'
    'codigoReporte' => [
        'type' => 'text',  // Tipo de campo
        'label' => 'Código de reporte',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',
            'placeholder' => '',  // Placeholder para el campo
            'required' => true,
            'readonly' => true, // El campo es obligatorio
        ]
    ],

    // Definición del campo 'codigo_propietario'
    'codigo_propietario' => [
        'type' => 'text',  // Tipo de campo
        'label' => 'Código de la propiedad',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',
            'placeholder' => 'Ingrese su nombre completo',
            'required' => true,  // El campo es obligatorio
        ]
    ],

    // Definición del campo 'situacionReportada' (campo textarea)
    'situacionReportada' => [
        'type' => 'textarea',  // Tipo de campo cambiado a textarea
        'label' => 'Situación reportada',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',  // Clase para aplicar estilos
            'required' => true,  // El campo es obligatorio
            'placeholder' => 'Describa su reportada aquí',  // Placeholder para guía del usuario
            'name' => 'profesion',
            'rows' => 4,  // Cantidad de filas visibles en el textarea
            'cols' => 50, // Cantidad de columnas visibles en el textarea (opcional)
        ]
    ],
    // Definición del campo 'fotoReporte' (campo de carga de archivos)
    'fotoReporte' => [
        'type' => 'file',  // Tipo de campo
        'label' => 'Subir foto del reporte',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',  // Clase para aplicar estilos
            'required' => true,  // El campo es obligatorio
            'accept' => 'image/png, image/jpeg, image/jpg, image/gif',  // Solo aceptar imágenes
            'name' => 'fotoReporte',  // Nombre del campo
        ]
    ],


];
