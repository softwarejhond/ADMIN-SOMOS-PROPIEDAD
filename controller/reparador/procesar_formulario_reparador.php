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
        'options' => ['Plomero', 'Electricista', 'Carpintero', 'Doctor', 'Otro'],
        'attributes' => [
            'class' => 'form-select',
            'required' => true,
            'placeholder' => 'Seleccione una opción',
            'name' => 'profesion',
        ]
    ],

];
