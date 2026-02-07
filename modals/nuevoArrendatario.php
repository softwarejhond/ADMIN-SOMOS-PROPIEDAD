<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function mostrarFormularioArrendatario() {
    // Cerrar el sidebar antes de mostrar el SweetAlert
    const offcanvasElement = document.getElementById('offcanvasWithBothOptions');
    if (offcanvasElement) {
        const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
        if (offcanvas) {
            offcanvas.hide();
        }
    }
    
    // Esperar un momento para que el sidebar se cierre completamente
    setTimeout(() => {
        Swal.fire({
            title: 'Crear Nuevo Arrendatario',
            html: `
                <div class="container-fluid">
                    <form id="formNuevoArrendatario" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">Cédula de Ciudadanía *</label>
                                    <input type="number" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="telefono" class="form-label">Teléfono *</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="genero" class="form-label">Género *</label>
                                    <select class="form-control" id="genero" name="genero" required>
                                        <option value="">Seleccionar género</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edad" class="form-label">Edad *</label>
                                    <input type="number" class="form-control" id="edad" name="edad" min="18" max="120" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="direccion" class="form-label">Dirección *</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Contraseña *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Debe contener solo letras y números</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="confirmPassword" class="form-label">Confirmar Contraseña *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="foto" class="form-label">Foto de Perfil</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg,image/jpg,image/png">
                            <small class="form-text text-muted">Formatos permitidos: JPG, PNG. Máximo 6MB</small>
                        </div>
                    </form>
                </div>
            `,
            width: '800px',
            backdrop: true,
            allowOutsideClick: true,
            showCancelButton: true,
            confirmButtonText: 'Crear Arrendatario',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#00976a',
            cancelButtonColor: '#d33',
            customClass: {
                container: 'swal-container-top'
            },
            didOpen: () => {
                // Asegurar que no haya backdrops conflictivos
                document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop').forEach(backdrop => {
                    backdrop.remove();
                });
                
                // Funcionalidad para mostrar/ocultar contraseña
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');
                const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
                const confirmPasswordInput = document.getElementById('confirmPassword');
                
                togglePassword.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
                
                toggleConfirmPassword.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (confirmPasswordInput.type === 'password') {
                        confirmPasswordInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        confirmPasswordInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            },
            preConfirm: () => {
                const form = document.getElementById('formNuevoArrendatario');
                const formData = new FormData(form);
                
                // Validar campos requeridos
                const username = formData.get('username');
                const nombre = formData.get('nombre');
                const email = formData.get('email');
                const telefono = formData.get('telefono');
                const genero = formData.get('genero');
                const edad = formData.get('edad');
                const direccion = formData.get('direccion');
                const password = formData.get('password');
                const confirmPassword = formData.get('confirmPassword');
                
                if (!username || !nombre || !email || !telefono || !genero || !edad || !direccion || !password || !confirmPassword) {
                    Swal.showValidationMessage('Por favor, complete todos los campos obligatorios');
                    return false;
                }
                
                // Validar que la cédula sea un número válido
                if (!/^\d{7,12}$/.test(username)) {
                    Swal.showValidationMessage('La cédula debe contener entre 7 y 12 dígitos');
                    return false;
                }
                
                // Validar email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    Swal.showValidationMessage('Por favor, ingrese un email válido');
                    return false;
                }
                
                // Validar teléfono
                if (!/^\d{7,15}$/.test(telefono.replace(/\s+/g, ''))) {
                    Swal.showValidationMessage('El teléfono debe contener entre 7 y 15 dígitos');
                    return false;
                }
                
                // Validar contraseña alfanumérica
                if (!/^[a-zA-Z0-9]+$/.test(password)) {
                    Swal.showValidationMessage('La contraseña debe contener solo letras y números');
                    return false;
                }
                
                // Validar longitud mínima de contraseña
                if (password.length < 6) {
                    Swal.showValidationMessage('La contraseña debe tener al menos 6 caracteres');
                    return false;
                }
                
                // Validar que las contraseñas coincidan
                if (password !== confirmPassword) {
                    Swal.showValidationMessage('Las contraseñas no coinciden');
                    return false;
                }
                
                // Validar archivo de imagen si se seleccionó
                const foto = formData.get('foto');
                if (foto && foto.size > 0) {
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(foto.type)) {
                        Swal.showValidationMessage('Solo se permiten archivos de imagen JPG o PNG');
                        return false;
                    }
                    if (foto.size > 6 * 1024 * 1024) { // 6MB
                        Swal.showValidationMessage('La imagen no debe superar los 6MB');
                        return false;
                    }
                }
                
                return formData;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                crearArrendatario(result.value);
            }
        });
    }, 300); // Esperar 300ms para que el sidebar se cierre
}

function crearArrendatario(formData) {
    // Mostrar loading
    Swal.fire({
        title: 'Creando arrendatario...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar datos via AJAX
    fetch('controller/arrendatarios/crearArrendatario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                confirmButtonColor: '#00976a'
            }).then(() => {
                // Recargar la página o actualizar la tabla
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#d33'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la solicitud',
            confirmButtonColor: '#d33'
        });
    });
}

function generarPasswordAleatoria() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}
</script>

<style>
.swal-container-top {
    z-index: 10000 !important;
}
</style>