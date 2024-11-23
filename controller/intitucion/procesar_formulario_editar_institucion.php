<?php
return [
    // Campos a incluir en el formulario
    'include_fields' => [
        
        'nombre',
        'nit',
        'direccion',
        'logo',
        'email',
        'ciudad'
    ],

    // Definición del campo 'codigoReporte'
    'nombre' => [
        'type' => 'text',  // Tipo de campo
        'label' => 'Nombre de la empresa',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',
            'placeholder' => 'Nombre de la empresa',  // Placeholder para el campo
            'required' => true,
            'name' => 'nombre',
        ]
    ],

    // Definición del campo 'codigo_propietario'
    'nit' => [
        'type' => 'text',  // Tipo de campo
        'label' => 'NIT de la empresa',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',
            'placeholder' => 'NIT de la empresa ',
            'required' => true,  // El campo es obligatorio
            'name' => 'nit',
        ]
    ],

    // Definición del campo 'situacionReportada' (campo textarea)
    'direccion' => [
        'type' => 'text',  // Tipo de campo cambiado a textarea
        'label' => 'Dirección de la empresa',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',  // Clase para aplicar estilos
            'required' => true,  // El campo es obligatorio
            'placeholder' => 'Dirección de la empresa',  // Placeholder para guía del usuario
            'name' => 'direccion',
        ]
    ],
    // Definición del campo 'fotoReporte' (campo de carga de archivos)
    'logo' => [
        'type' => 'file',  // Tipo de campo
        'label' => 'Subir logo de la empresa',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',  // Clase para aplicar estilos
            'required' => true,  // El campo es obligatorio
            'accept' => 'image/png, image/jpeg, image/jpg, image/gif',  // Solo aceptar imágenes
            'name' => 'logo',  // Nombre del campo
        ]
    ],

    
    'email' => [
        'type' => 'text',  // Tipo de campo cambiado a textarea
        'label' => 'Correo electrónico de la emprea',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',  // Clase para aplicar estilos
            'required' => true,  // El campo es obligatorio
            'placeholder' => 'Correo electrónico de la emprea',  // Placeholder para guía del usuario
            'name' => 'email',
        ]
    ],
    'ciudad' => [
        'type' => 'text',  // Tipo de campo cambiado a textarea
        'label' => 'Ciudad de la emprea',  // Etiqueta para el campo
        'attributes' => [
            'class' => 'form-control',  // Clase para aplicar estilos
            'required' => true,  // El campo es obligatorio
            'placeholder' => 'Ciudad de la emprea',  // Placeholder para guía del usuario
            'name' => 'ciudad',
        ]
    ],

];
