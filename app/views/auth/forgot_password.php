<?php include __DIR__ . "/../layouts/header.php"; ?>

<section class="wrapper">
    <div class="form reset-password active">
        <header>Recuperar Contraseña</header>
        <form id="resetPasswordForm" method="post" action="/timeControl/public/reset_password">
            <p style="text-align: center; color: #666; margin-bottom: 20px; font-size: 14px;">
                Ingrese su código de empleado y nueva contraseña
            </p>
            
            <input type="text" 
                   class="form-control" 
                   id="codigo_empleado" 
                   name="codigo_empleado" 
                   placeholder="Código Empleado" 
                   required 
                   autocomplete="off">
            
            <input type="password" 
                   class="form-control" 
                   id="nueva_password" 
                   name="nueva_password" 
                   placeholder="Nueva Contraseña" 
                   required 
                   autocomplete="new-password">
            
            <input type="password" 
                   class="form-control" 
                   id="confirmar_password" 
                   name="confirmar_password" 
                   placeholder="Confirmar Nueva Contraseña" 
                   required 
                   autocomplete="new-password">
            
            <input type="submit" value="Cambiar Contraseña" />
            
            <a href="/timeControl/public/login" style="text-align: center; display: block; margin-top: 15px; color: #4070f4; text-decoration: none;">
                Volver al Login
            </a>
        </form>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Mostrar mensajes de estado si existen
        fetch('/timeControl/public/getStatus')
            .then(response => response.json())
            .then(data => {
                if (data.status && data.message) {
                    const toastrFunction = data.status === "success" ? toastr.success : toastr.error;
                    toastrFunction(data.message, '', {
                        timeOut: 2000
                    });

                    
                    if (data.status === "success") {
                        setTimeout(function() {
                            toastr.info('Tu contraseña ha sido actualizada correctamente. Redirigiendo al login...', 'Éxito', {
                                timeOut: 3000
                            });
                            
                            setTimeout(function() {
                                window.location.href = '/timeControl/public/login';
                            }, 3000);
                        }, 2500);
                    }
                }
            });

        // Validación de contraseñas en tiempo real
        const form = document.getElementById('resetPasswordForm');
        const nuevaPassword = document.getElementById('nueva_password');
        const confirmarPassword = document.getElementById('confirmar_password');

        confirmarPassword.addEventListener('input', function() {
            if (nuevaPassword.value !== confirmarPassword.value) {
                confirmarPassword.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmarPassword.setCustomValidity('');
            }
        });

        nuevaPassword.addEventListener('input', function() {
            if (nuevaPassword.value.length < 6) {
                nuevaPassword.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
            } else {
                nuevaPassword.setCustomValidity('');
            }
            
            if (confirmarPassword.value) {
                confirmarPassword.dispatchEvent(new Event('input'));
            }
        });
    });
</script>

<?php include __DIR__ . "/../layouts/footer.php"; ?>