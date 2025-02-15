<?php
return [
    'include_fields' => [
        'codigo',
        'tipoInmueble',
        'nivel_piso',
        'area',
        'estrato',
        'terraza',
        'ascensor',
        'patio',
        'parqueadero',
        'cuarto_util',
        'alcobas',
        'closet',
        'sala',
        'sala_comedor',
        'comedor',
        'cocina',
        'servicios',
        'CuartoServicios',
        'ZonaRopa',
        'vista',
        'servicios_publicos',
        'otras_caracteristicas',
        'direccion',
        'TelefonoInmueble',
        'valor_canon',
        'doc_propietario',
        'nombre_propietario',
        'telefono_propietario',
        'email_propietario',
        'banco',
        'tipoCuenta',
        'numeroCuenta',
        'diaPago',
        'contrato_EPM',
        'url_foto_principal',
        'condicion',
        'doc_inquilino',
        'nombre_inquilino',
        'telefono_inquilino',
        'email_inquilino',
    ],

    'tipoInmueble' => [
        'type' => 'select',
        'options_table' => 'tipos',
        'value_column' => 'id',
        'label_column' => 'Tipo de propiedad: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "tipoInmueble"
        ]
    ],

    'codigo' => [
        'type' => 'text',
        'attributes' => [
            'class' => 'form-control',
            'label_column' => 'Código de la propiedad: ',
            'placeholder' => 'Ingrese el código de la propiedad',
            'required' => true,
            'name' => "codigo"
        ]
    ],

    'nivel_piso' => [
        'type' => 'number',
        'attributes' => [
            'class' => 'form-control',
            'label_column' => 'Nivel de piso: ',
            'placeholder' => 'Ingrese el nivel del piso',
            'required' => true,
            'min' => 0,
            'max' => 40
        ]
    ],

    'area' => [
        'type' => 'number',
        'attributes' => [
            'class' => 'form-control',
            'label_column' => 'Área m²: ',
            'placeholder' => 'Ingrese el área de la propiedad',
            'required' => true,
            'min' => 0
        ]
    ],

    // Completar con los campos restantes...
];
