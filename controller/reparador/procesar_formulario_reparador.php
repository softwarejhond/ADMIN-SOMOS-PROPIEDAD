<?php
return [
    'include_fields' => [
        'identificacion',
        'nombre',
        'telefono',
        'email',
        'profesion',
    ],

    'identificacion' => [
        'type' => 'text', // Tipo de campo
        'label' => 'Identificación', // Label para el campo
        'attributes' => [
            'class' => 'form-control',
            'placeholder' => 'Ingrese su identificación', // Placeholder para el campo
            'required' => true,
        ]
    ],

    'nombre' => [
        'type' => 'text',
        'label' => 'Nombre Completo', // Label para el campo
        'attributes' => [
            'class' => 'form-control',
            'placeholder' => 'Ingrese su nombre completo',
            'required' => true,
        ]
    ],

    'telefono' => [
        'type' => 'text',
        'label' => 'Teléfono', // Label para el campo
        'attributes' => [
            'class' => 'form-control',
            'placeholder' => 'Ingrese su número de teléfono',
            'required' => true,
        ]
    ],

    'email' => [
        'type' => 'email', // Tipo de campo para email
        'label' => 'Correo Electrónico', // Label para el campo
        'attributes' => [
            'class' => 'form-control',
            'placeholder' => 'Ingrese su correo electrónico',
            'required' => true,
        ]
    ],

    'profesion' => [
        'type' => 'select',
        'label' => 'Profesión', // Label para el campo
        'options' => [
            'Albañil',
            'Aseo',
            'Ayudante de cocina',
            'Carpintero',
            'Camarero',
            'Cerrajero',
            'Cocinero',
            'Conserje',
            'Costurero',
            'Electricista',
            'Electricista industrial',
            'Exterminador de plagas',
            'Fontanero',
            'Guardia de seguridad',
            'Instalador de aire acondicionado',
            'Jardinero',
            'Jardinero paisajista',
            'Limpiador de ventanas',
            'Mecánico',
            'Niñera',
            'Peluquero',
            'Pintor',
            'Plomero',
            'Reparador de computadoras',
            'Reparador de electrodomésticos',
            'Reparador de muebles',
            'Técnico en informática',
            'Técnico en electrodomésticos',
            'Transporte doméstico',
            'Tutor doméstico',
            'Otros'
        ],
        'attributes' => [
            'class' => 'form-select',
            'required' => true,
            'placeholder' => 'Seleccione una opción',
            'name' => 'profesion',
        ]
    ],


];
