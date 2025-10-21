<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Operaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="assets/css/datos_trabajo.css">
    <link rel="stylesheet" href="assets/css/buttons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/timeControl/public/assets/css/operador/datos_trabajo.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center text-md-start">
                    <span class="custom-icon-header"><i class="bi bi-gear-fill"></i></span>Datos de Operaciones - <?= htmlspecialchars($maquina) ?>
                </h2>

                <!-- Botón Volver a seleccionar Máquina -->
                <a href="/timeControl/public/datos_trabajo_maquina" class="back-button">
                    <span class="custom-icon"><i class="bi bi-arrow-left"></i></span> Volver a seleccionar Máquina
                </a>

                <!-- Formulario de datos -->
                <form class="form" method="post" action="/timeControl/public/seleccionar_data">
                    <div class="form-group">
                        <label for="jtWo"><span class="custom-icon"><i class="bi bi-tag"></i></span>JT/WO:</label>
                        <input type="text" id="jtWo" class="form-control" placeholder="Ingrese Código JT/WO" name="jtWo" required>
                    </div>

                    <div class="form-group">
                        <label for="item"><span class="custom-icon"><i class="bi bi-box"></i></span>Item:</label>
                        <input type="text" id="item" class="form-control" placeholder="Ingrese un Item" name="item" required>
                    </div>

                    <div class="form-group">
                        <label for="po"><span class="custom-icon"><i class="bi bi-file-earmark-text"></i></span>Orden de Compra (PO):</label>
                        <input type="text" id="po" class="form-control" placeholder="Ingrese Orden de Compra" name="po" required>
                    </div>

                    <div class="form-group">
                        <label for="cliente"><span class="custom-icon"><i class="bi bi-person"></i></span>Cliente:</label>
                        <input type="text" id="cliente" class="form-control" placeholder="Ingrese Cliente" name="cliente" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <span class="custom-icon"><i class="bi bi-save"></i></span>Ingresar
                    </button>
                </form>

                <div class="d-flex flex-column gap-3 mt-4">
                    <!-- Botones de acción -->
                    <button class="logout-button" onclick="confirmLogout()">
                        <span class="custom-icon"><i class="bi bi-box-arrow-right"></i></span>Cerrar Sesión
                    </button>

                    <!-- Formulario Espera de Trabajo -->
                    <form class="form" method="POST" action="/timeControl/public/espera_trabajo" onsubmit="return confirmWait()">
                        <input type="hidden" name="tipo_boton" value="Espera_trabajo">
                        <button type="submit" class="wait_button <?= ($active_button_id === 'Espera_trabajo') ? 'active-button' : '' ?>">
                            <span class="custom-icon"><i class="bi bi-hourglass-split"></i></span>Espera Trabajo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/datos_trabajo.js"></script>

    <script>
    // Mejora de interactividad
    $(document).ready(function() {
        // Añadir animación a los campos de formulario al enfocarlos
        $('.form-control').focus(function() {
            $(this).parent().addClass('focused');
        }).blur(function() {
            $(this).parent().removeClass('focused');
        });

        // Validación en tiempo real de los campos
        $('.form-control').on('input', function() {
            if($(this).val().length > 0) {
                $(this).addClass('is-valid').removeClass('is-invalid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });

        // Animación sutil para los botones
        $('.btn, .back-button, .logout-button, .wait_button').mousedown(function() {
            $(this).css('transform', 'scale(0.98)');
        }).mouseup(function() {
            $(this).css('transform', '');
        });
    });
    </script>

    <?php include __DIR__ . "/../layouts/footer.php"; ?>
</body>
</html>
