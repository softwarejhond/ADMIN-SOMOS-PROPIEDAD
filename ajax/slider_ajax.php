<?php
/* Llamar la Cadena de Conexion*/
include('conexion.php');
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    // Elimino producto
    if (isset($_REQUEST['id'])) {
        $id_slide = intval($_REQUEST['id']);
        if ($delete = mysqli_query($conn, "delete from slider where id='$id_slide'")) {
            $message = "Datos eliminados satisfactoriamente";
        } else {
            $error = "No se pudo eliminar los datos: " . mysqli_error($conn);
        }
    }

    $tables = "slider";
    $sWhere = "order by orden";
    include 'pagination.php'; // include pagination file

    // pagination variables
    $page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page = 12; // how much records you want to show
    $adjacents  = 4; // gap between pages after number of adjacents
    $offset = ($page - 1) * $per_page;

    // Count the total number of row in your table
    $count_query = mysqli_query($conn, "SELECT count(*) AS numrows FROM $tables $sWhere");
    if ($row = mysqli_fetch_array($count_query)) {
        $numrows = $row['numrows'];
    } else {
        echo "Error en la consulta SQL: " . mysqli_error($conn);
    }
    $total_pages = ceil($numrows / $per_page);
    $reload = 'slider_ajax.php';

    // main query to fetch the data
    $query = mysqli_query($conn, "SELECT * FROM $tables $sWhere LIMIT $offset, $per_page");
    if (!$query) {
        die("Error en la consulta SQL: " . mysqli_error($conn));
    }

    if (isset($message)) {
        echo "<div class='alert alert-success alert-dismissible fade in' role='alert'>
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                <strong>Aviso!</strong> $message
              </div>";
    }
    if (isset($error)) {
        echo "<div class='alert alert-danger alert-dismissible fade in' role='alert'>
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
                <strong>Error!</strong> $error
              </div>";
    }

    // loop through fetched data
    if ($numrows > 0) {
        echo "<div class='row'>";
        while ($row = mysqli_fetch_assoc($query)) {
            $url_image = $row['url_image'];
            $titulo = $row['titulo'];
            $descripcion = $row['descripcion'];
            $id_slide = $row['id'];

            echo "<div class='col-sm-6 col-md-4'>
                    <div class='thumbnail'>
                        <img src='./img/carousel/$url_image' alt='...' style='height: auto; width:100%'>
                        <div class='caption'>
                            <h3>$titulo</h3>
                            <p>$descripcion</p>
                            <p class='text-right'>
                                <a href='editarCarousel.php?id=" . intval($id_slide) . "' class='btn bg-magenta-dark text-white' role='button'><i class='fa fa-edit'></i> Editar</a>
                                <button type='button' class='btn bg-gray-light border-magenta-dark' onclick='eliminar_slide(\"$id_slide\");' role='button'><i class='fa fa-trash'></i> Eliminar</button>
                            </p>
                        </div>
                    </div>
                  </div>";
        }
        echo "</div>";

        echo "<div class='table-pagination text-right'>";
        echo paginate($reload, $page, $total_pages, $adjacents);
        echo "</div>";
    }
}
?>