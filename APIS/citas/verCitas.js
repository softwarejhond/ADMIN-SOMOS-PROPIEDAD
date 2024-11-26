 $(document).ready(function() {
        $('#citas-table').DataTable({
            "ajax": {
                "url": "citasView.php", // Aquí va la URL de tu script PHP que devuelve los datos de las citas
                "dataSrc": ""
            },
            "columns": [{
                    "data": "fecha"
                },
                {
                    "data": "hora"
                },
                {
                    "data": "nombre"
                },
                {
                    "data": "telefono"
                },
                {
                    "data": "correo"
                },
                {
                    "data": "estado"
                }
            ]
        });
    });