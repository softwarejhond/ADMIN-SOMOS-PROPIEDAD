<?php


if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["titulo"])) {
	/* Llamar la Cadena de Conexion*/
	include("conexion.php");
	// escaping, additionally removing everything that could be (html/javascript-) code
	$titulo = mysqli_real_escape_string($conn, (strip_tags($_POST['titulo'], ENT_QUOTES)));
	$descripcion = mysqli_real_escape_string($conn, (strip_tags($_POST['descripcion'], ENT_QUOTES)));
	$texto_boton = mysqli_real_escape_string($conn, (strip_tags($_POST['texto_boton'], ENT_QUOTES)));
	$url_boton = mysqli_real_escape_string($conn, ($_POST['url_boton']));
	$estilo = mysqli_real_escape_string($conn, ($_POST['estilo']));
	$orden = intval($_POST['orden']);
	$estado = intval($_POST['estado']);
	$id_slide = intval($_POST['id_slide']);
	$sql = "UPDATE slider SET titulo='$titulo', descripcion='$descripcion', texto_boton='$texto_boton', url_boton='$url_boton', estilo_boton='$estilo', orden='$orden', estado='$estado' WHERE id='$id_slide'";
	$query = mysqli_query($conn, $sql);
	// if user has been added successfully
	if ($query) {
		$messages[] = "Datos  han sido actualizados satisfactoriamente.";
	} else {
		$errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conn);
	}

	if (isset($errors)) {

?>

		<div class="toast-container position-fixed bottom-0 end-0 p-3">
			<div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="toast-header border-magenta-light">
					<strong class="me-auto"><i class="bi bi-exclamation-triangle-fill"></i> Notificación</strong>
					<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
				<div class="toast-body bg-magenta-light">
					<?php
					foreach ($errors as $error) {
						echo $error;
					}
					?>
				</div>
			</div>
		</div>
	<?php
	}
	if (isset($messages)) {

	?>

		<div class="toast-container position-fixed top-0 bottom-0 end-0 p-3">
			<div id="liveToast" class="toast bg-lime-light'" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="toast-header border-lime-light">
					<strong class="me-auto"><i class="bi bi-exclamation-triangle-fill"></i> Notificación</strong>
					<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
				<div class="toast-body bg-lime-light">
					<?php
					foreach ($messages as $message) {
						echo $message;
					}
					?>
				</div>
			</div>
		</div>
<?php
	}
}
?>