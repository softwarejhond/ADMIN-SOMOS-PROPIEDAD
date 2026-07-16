<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicializar la sesión
session_start();

// Establecer tiempo de vida de la sesión en segundos
$inactividad = 86400;

// Comprobar si $_SESSION["timeout"] está establecida
if (isset($_SESSION["timeout"])) {
    // Calcular el tiempo de vida de la sesión (TTL = Time To Live)
    $sessionTTL = time() - $_SESSION["timeout"];
    if ($sessionTTL > $inactividad) {
        session_unset();
        session_destroy();
        header("location: login.php"); // Redirigir a la página de inicio de sesión
        exit;
    }
}

// Actualizar el tiempo de la sesión
$_SESSION["timeout"] = time();

// Incluir el archivo de conexión
require_once "conexion.php";

// Definir variables y inicializar con valores vacíos
$username = $password = "";
$username_err = $password_err = "";

// Procesar datos del formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validar que el nombre de usuario no esté vacío
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor ingrese su usuario.";
    } elseif (!filter_var(trim($_POST["username"]), FILTER_VALIDATE_INT)) {
        // Validar que el ID de usuario sea un número
        $username_err = "El usuario debe ser un número.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validar que la contraseña no esté vacía
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor ingrese su contraseña.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validar credenciales
    if (empty($username_err) && empty($password_err)) {
        // Preparar una declaración SQL
        $sql = "SELECT id, username, password, nombre, rol, foto FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Vincular variables a la declaración preparada como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;

            // Intentar ejecutar la declaración preparada
            if (mysqli_stmt_execute($stmt)) {
                // Almacenar resultado
                mysqli_stmt_store_result($stmt);

                // Verificar si el nombre de usuario existe, si sí, verificar la contraseña
                if (mysqli_stmt_num_rows($stmt) === 1) {
                    // Vincular variables de resultado
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $nombre, $rol, $foto);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // La contraseña es correcta, iniciar una nueva sesión
                            $_SESSION['loggedin'] = true;
                            $_SESSION['nombre'] = htmlspecialchars($nombre);
                            $_SESSION['rol'] = $rol;
                            $_SESSION['username'] = htmlspecialchars($username);
                            $_SESSION['foto'] = htmlspecialchars($foto);

                            // Redirigir según el rol del usuario
                            if ($rol == 5) { // Nuevo rol para panel de propiedades únicamente
                                header("location: panel_arrendatario.php");
                            } elseif ($rol == 6) { // Propietario: solo panel_propietario.php
                                header("location: panel_propietario.php");
                            } else {
                                header("location: index.php");
                            }
                            exit;
                        } else {
                            $password_err = "Contraseña incorrecta.";
                        }
                    }
                } else {
                    $username_err = "Usuario no existe.";
                }
            } else {
                echo "Algo salió mal, por favor vuelve a intentarlo.";
            }
        }
        // Cerrar la declaración
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilo.css?v=0.9">
    <link rel="stylesheet" href="css/animacion.css?v=0.9">
    <title>SIVP - Login</title>
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div id="contenedor-login">
        <div class="presentacion">
            <div class="titulo text-center">
                <img src="img/logo.png" alt="logo" width="50%" class="d-block mx-auto">
                <h2 class="nombreApp">SIVP</h2>
                <p class="text-center login__forgot">SIVP &copy; Copyright <?php echo date("Y"); ?>

                    <br>
                    <a href="https://agenciaeaglesoftware.com/" target="_blank" class="linkEagle">Made by Agencia de Desarrollo Eagle Software</a><br>
                    <a href="https://api.whatsapp.com/send/?phone=573015606006&text&type=phone_number&app_absent=0" target="_blank" class="linkEagle"><i class="bi bi-whatsapp"></i></a>
                    <a href="https://www.instagram.com/eaglesoftwares/#" target="_blank" class="linkEagle"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.facebook.com/eaglesoftwares/" target="_blank" class="linkEagle"><i class="bi bi-facebook"></i></a>
                </p>
            </div>
            <div class="contenedor-formulario">
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="form-login">
                    <p><strong><i class="bi bi-box-arrow-in-right"></i> Iniciar sesión </strong> </p>
                    <img src="img/somosLogo.png" alt="logo" width="50%" class="d-block mx-auto">
                    <input type="text" placeholder="Nombre de Usuario" name="username" required class="input-login">
                    <div class="password-container">
                        <input type="password" placeholder="Contraseña" name="password" required class="input-login" id="passwordInput">
                        <span class="toggle-password" onclick="togglePassword()">
                            🔍
                        </span>
                    </div>
                    <input type="submit" value="Iniciar Sesión" name="iniciar" class="btn">

                    <!-- Mensaje que se mostrará cuando se haya procesado la solicitud en el servidor -->
                    <?php if (!empty($username_err) || !empty($password_err)): ?>
                        <span class="msj-error-input"> <?php echo $username_err ?: $password_err; ?></span>
                    <?php endif ?>
                </form>
            </div>
        </div>
    </div>
    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
    <script src="js/tooglePassword.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>

</html>
