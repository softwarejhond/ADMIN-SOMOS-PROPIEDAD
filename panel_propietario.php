<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// PROTECCIÓN HERMÉTICA: Solo el rol 6 puede acceder a este panel
if ($_SESSION['rol'] != 6) {
    // Si no es rol 6, redirigir según su rol
    switch ($_SESSION['rol']) {
        case 1: // Administrador
        case 2: // Operario
        case 3: // Aprobador
        case 4: // Editor
        case 5: // Arrendatario
        
        default:
            header('Location: index.php');
            exit;
    }
}

include("funciones.php");

$empresas    = obtenerEmpresas();
$infoUsuario = obtenerInformacionUsuario();
$rol         = $infoUsuario['rol'];

// Detectar primera vez: datos aún en valores por defecto
require_once __DIR__ . '/conexion.php';
$_usernameCheck = (int)$_SESSION['username'];
$_stmtCheck = $conn->prepare("SELECT email, genero FROM users WHERE username = ? LIMIT 1");
$_stmtCheck->bind_param('i', $_usernameCheck);
$_stmtCheck->execute();
$_rowCheck = $_stmtCheck->get_result()->fetch_assoc();
$_stmtCheck->close();
$primeraVez = $_rowCheck && ($_rowCheck['email'] === '' || $_rowCheck['genero'] === 'No especificado');

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/estilo.css?v=0.0.2">
    <link rel="stylesheet" href="css/slidebar.css?v=0.0.3">
    <link rel="stylesheet" href="css/contadores.css?v=0.7">
    <link rel="stylesheet" href="css/animacion.css?v=0.15">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Panel propietario</title>
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
</head>

<body class="" style="background-color:white">
    <?php include("header.php"); ?>
    
    <div id="mt-3">
        <div class="mt-3 pt-5">
            <?php include("controller/propietarios/cards_valores.php"); ?>
            <div class="container-fluid px-4 pb-4">
                <?php include("controller/propietarios/cartera_propietario.php"); ?>
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
        <li></li>
    </ul>
    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/departamentos_municipios_barrios.js?v=2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="js/real-time-inquilino-proximo-retiro.js?v=0.1"></script>
    <script src="js/real-time-update-contadores.js?v=0.1"></script>
    <script>
        $('#link-dashboard').addClass('pagina-activa');
    </script>

<?php if ($primeraVez): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: '<i class="bi bi-person-fill-gear text-success"></i> Actualiza tu perfil',
        html: `
            <p class="text-muted small mb-3">Por seguridad, completa tus datos antes de continuar. <strong>No podr&aacute;s cerrar este cuadro sin actualizar.</strong></p>
            <div class="text-start">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Correo electr&oacute;nico *</label>
                    <input type="email" id="swal-email" class="form-control form-control-sm" placeholder="tu@correo.com">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">G&eacute;nero *</label>
                    <select id="swal-genero" class="form-select form-select-sm">
                        <option value="">Seleccionar...</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="No binario">No binario</option>
                        <option value="Prefiero no decir">Prefiero no decir</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Tel&eacute;fono *</label>
                    <input type="tel" id="swal-telefono" class="form-control form-control-sm" placeholder="300 000 0000">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Direcci&oacute;n *</label>
                    <input type="text" id="swal-direccion" class="form-control form-control-sm" placeholder="Calle 1 # 2-3">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Edad *</label>
                    <input type="number" id="swal-edad" class="form-control form-control-sm" min="18" max="100" placeholder="Ej: 35">
                </div>
                <hr class="my-2">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Nueva contrase&ntilde;a *</label>
                    <input type="password" id="swal-password" class="form-control form-control-sm" placeholder="M&iacute;nimo 8 caracteres" autocomplete="new-password">
                    <div class="progress mt-1" style="height:5px;">
                        <div id="swal-pwd-bar" class="progress-bar" style="width:0%;transition:width 0.3s;"></div>
                    </div>
                    <div id="swal-pwd-label" style="font-size:0.72rem;margin-top:2px;"></div>
                    <ul id="swal-pwd-rules" class="mt-1 mb-0 ps-3" style="font-size:0.72rem;color:#555;">
                        <li id="rule-len">M&iacute;nimo 8 caracteres</li>
                        <li id="rule-upper">Al menos 1 may&uacute;scula (A-Z)</li>
                        <li id="rule-num">Al menos 1 n&uacute;mero (0-9)</li>
                        <li id="rule-special">Al menos 1 car&aacute;cter especial (!@#$...)</li>
                    </ul>
                </div>
                <div class="mb-1">
                    <label class="form-label small fw-semibold">Confirmar contrase&ntilde;a *</label>
                    <input type="password" id="swal-password2" class="form-control form-control-sm" placeholder="Repite la contrase&ntilde;a" autocomplete="new-password">
                    <div id="swal-pwd-match" style="font-size:0.72rem;margin-top:2px;"></div>
                </div>
            </div>`,
        width: 500,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showCancelButton: false,
        confirmButtonText: 'Guardar y continuar',
        confirmButtonColor: '#1a6e1a',
        didOpen: function () {
            var pwd    = document.getElementById('swal-password');
            var pwd2   = document.getElementById('swal-password2');
            var bar    = document.getElementById('swal-pwd-bar');
            var label  = document.getElementById('swal-pwd-label');
            var matchEl= document.getElementById('swal-pwd-match');

            var colors  = ['', '#dc3545', '#fd7e14', '#ffc107', '#28a745'];
            var labels  = ['', 'Muy débil', 'Débil', 'Media', 'Fuerte'];

            function checkRules(v) {
                return {
                    len:     v.length >= 8,
                    upper:   /[A-Z]/.test(v),
                    num:     /[0-9]/.test(v),
                    special: /[^a-zA-Z0-9]/.test(v)
                };
            }

            function paintRule(id, ok) {
                var el = document.getElementById(id);
                if (el) { el.style.color = ok ? '#1a6e1a' : '#555'; el.style.fontWeight = ok ? 'bold' : 'normal'; }
            }

            pwd.addEventListener('input', function () {
                var v = pwd.value;
                var r = checkRules(v);
                var score = [r.len, r.upper, r.num, r.special].filter(Boolean).length;

                paintRule('rule-len',     r.len);
                paintRule('rule-upper',   r.upper);
                paintRule('rule-num',     r.num);
                paintRule('rule-special', r.special);

                bar.style.width           = (score * 25) + '%';
                bar.style.backgroundColor = colors[score] || '#dc3545';
                label.textContent         = labels[score] || '';
                label.style.color         = colors[score] || '#dc3545';

                if (pwd2.value) {
                    var m = pwd.value === pwd2.value;
                    matchEl.textContent = m ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden';
                    matchEl.style.color = m ? '#1a6e1a' : '#dc3545';
                }
            });

            pwd2.addEventListener('input', function () {
                var m = pwd.value === pwd2.value;
                matchEl.textContent = m ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden';
                matchEl.style.color = m ? '#1a6e1a' : '#dc3545';
            });
        },
        preConfirm: function () {
            var email     = document.getElementById('swal-email').value.trim();
            var genero    = document.getElementById('swal-genero').value;
            var telefono  = document.getElementById('swal-telefono').value.trim();
            var direccion = document.getElementById('swal-direccion').value.trim();
            var edad      = parseInt(document.getElementById('swal-edad').value, 10);
            var password  = document.getElementById('swal-password').value;
            var password2 = document.getElementById('swal-password2').value;

            if (!email || !genero || !telefono || !direccion || !edad || !password || !password2) {
                Swal.showValidationMessage('Todos los campos son obligatorios.');
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                Swal.showValidationMessage('Ingresa un correo electrónico válido.');
                return false;
            }
            if (edad < 18 || edad > 100) {
                Swal.showValidationMessage('La edad debe estar entre 18 y 100 años.');
                return false;
            }
            if (password.length < 8 || !/[A-Z]/.test(password) || !/[0-9]/.test(password) || !/[^a-zA-Z0-9]/.test(password)) {
                Swal.showValidationMessage('La contraseña no cumple los requisitos de seguridad.');
                return false;
            }
            if (password !== password2) {
                Swal.showValidationMessage('Las contraseñas no coinciden.');
                return false;
            }
            return { email: email, genero: genero, telefono: telefono, direccion: direccion, edad: edad, password: password };
        }
    }).then(function (result) {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function () { Swal.showLoading(); }
        });

        fetch('controller/propietarios/actualizar_perfil_propietario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(result.value)
        })
        .then(function (r) { return r.json(); })
        .then(function (resp) {
            if (resp.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Perfil actualizado',
                    text: 'Tus datos han sido guardados correctamente.',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', resp.message || 'No se pudo actualizar el perfil.', 'error').then(function () {
                    location.reload();
                });
            }
        })
        .catch(function () {
            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
        });
    });
});
</script>
<?php endif; ?>
</body>

</html>