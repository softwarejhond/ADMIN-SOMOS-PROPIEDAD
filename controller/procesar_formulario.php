<?php
return [
    'include_fields' => [
        'codigo',
        'tipoInmueble',
        'nivel_piso',
        'area',
        'estrato',
        'departamento',
        'municipios',
        'barrio',
        'terraza',
        'ascensor',
        'patio',
        'parqueadero',
        'cuarto_util',
        'habitaciones',
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
        'doc_inquilino',
        'nombre_inquilino',
        'telefono_inquilino',
        'email_inquilino',
        'vigenciaContrato',
        'fecha',
        'descuento',
        'iva',
        'contrato_EPM',
        'comision',
        'aval_catastro',
        'asistencia',
        'cc_codeudor_uno',
        'nombre_codeudor_uno',
        'email_codeudor_uno',
        'telefono_codeudor_uno',
        'direccion_codeudor_uno',
        'cc_codeudor_dos',
        'nombre_codeudor_dos',
        'email_codeudor_dos',
        'telefono_codeudor_dos',
        'direccion_codeudor_dos',
        'cc_codeudor_tres',
        'nombre_codeudor_tres',
        'email_codeudor_tres',
        'telefono_codeudor_tres',
        'direccion_codeudor_tres',
        'estadoPropietario',
        'url_foto_principal',
        'condicion',
        'ipc',
        'fecha_creacion'
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
            'placeholder' => '',
            'required' => true,
            'min' => 0
        ]
    ],

    'estrato' => [
        'type' => 'radio',
        'attributes' => [
            'class' => 'form-control',
            'label_column' => 'Estrato: ',
            'required' => true,
            'min' => 1,
            'max' => 7
        ]
    ],

    'departamento' => [
        'type' => 'select',
        'value_column' => 'id',
        'label_column' => 'Seleccione el departamento: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "departamento",
            'id' => "lista_departamento"
        ]
    ],

    'municipios' => [
        'type' => 'select',
        'value_column' => 'id',
        'label_column' => 'Seleccione el municipio: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "municipios"
        ]
    ],

    'barrio' => [
        'type' => 'select',
        'value_column' => 'id',
        'label_column' => 'Seleccione el barrio: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "Barrio"
        ]
    ],

    'terraza' => [
        'type' => 'radio',
        'value_column' => 'id',
        'label_column' => 'Terraza: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "terraza"
        ]
    ],

    'parqueadero' => [
        'type' => 'select',
        'value_column' => 'id',
        'label_column' => 'Parqueadero: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "parqueadero"
        ],
        'options' => [
            '1' => 'Privado',
            '2' => 'Público',
            '3' => 'Sin parqueadero'
        ]
    ],

    'ascensor' => [
        'type' => 'select',
        'value_column' => 'id',
        'label_column' => 'Ascensor: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "ascensor"
        ],
        'options' => [
            '1' => 'Sí',
            '2' => 'No'
        ]
    ],

    'patio' => [
        'type' => 'radio',
        'value_column' => 'id',
        'label_column' => 'Patio: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "patio"
        ]
    ],

    'cuarto_util' => [
        'type' => 'radio',
        'value_column' => 'id',
        'label_column' => 'Cuarto útil: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "cuarto_util"
        ]
    ],

    'habitaciones' => [
        'type' => 'radio',
        'attributes' => [
            'class' => 'form-control',
            'label_column' => 'Habitaciones: ',
            'required' => true,
            'min' => 0,
            'max' => 10
        ]
    ],

    'closet' => [
        'type' => 'radio',
        'label_column' => 'Closet: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "closet"
        ]
    ],

    'sala' => [
        'type' => 'radio',
        'label_column' => 'Sala: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "sala"
        ]
    ],

    'sala_comedor' => [
        'type' => 'radio',
        'label_column' => 'Sala comedor: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "sala_comedor"
        ]
    ],

    'comedor' => [
        'type' => 'radio',
        'label_column' => 'Comedor: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "comedor"
        ]
    ],

    'cocina' => [
        'type' => 'radio',
        'label_column' => 'Cocina: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "cocina"
        ]
    ],

    'servicios' => [
        'type' => 'text',
        'label_column' => 'Servicios: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "servicios"
        ]
    ],

    'CuartoServicios' => [
        'type' => 'text',
        'label_column' => 'Cuarto de Servicios: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "CuartoServicios"
        ]
    ],

    'ZonaRopa' => [
        'type' => 'text',
        'label_column' => 'Zona de Ropa: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "ZonaRopa"
        ]
    ],

    'vista' => [
        'type' => 'select',
        'label_column' => 'Vista: ',
        'options' => [
            '1' => 'Vista al mar',
            '2' => 'Vista panorámica',
            '3' => 'Vista al parque'
        ],
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "vista"
        ]
    ],

    'servicios_publicos' => [
        'type' => 'text',
        'label_column' => 'Servicios Públicos: ',
        'attributes' => [
            'class' => 'form-control',
            'name' => "servicios_publicos",
            'required' => true
        ]
    ],

    'otras_caracteristicas' => [
        'type' => 'text',
        'label_column' => 'Otras Características: ',
        'attributes' => [
            'class' => 'form-control',
            'name' => "otras_caracteristicas",
            'required' => true
        ]
    ],

    'direccion' => [
        'type' => 'text',
        'label_column' => 'Dirección: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "direccion"
        ]
    ],

    'TelefonoInmueble' => [
        'type' => 'text',
        'label_column' => 'Teléfono del inmueble: ',
        'attributes' => [
            'class' => 'form-control',
            'required' => true,
            'name' => "TelefonoInmueble"
        ]
    ],

    // Completar con los campos restantes...
];
