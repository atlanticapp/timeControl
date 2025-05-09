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
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding-top: 15px;
            padding-bottom: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 1.5rem;
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            color: #212529;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #dee2e6;
            font-size: 1.75rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-control {
            border-radius: 6px;
            padding: 0.6rem 0.75rem;
            transition: all 0.2s ease;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        label {
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: #495057;
            display: flex;
            align-items: center;
        }

        .btn-primary {
            padding: 0.6rem 1rem;
            font-weight: 500;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
        }

        .back-button {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 15px;
            margin: 0 0 1.5rem 0;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            width: 100%;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background-color: #5a6268;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .back-button i {
            margin-right: 8px;
            font-size: 1.4rem;
        }

        .logout-button, .wait_button {
            margin-top: 1.25rem;
            border-radius: 8px;
            padding: 12px 15px;
            font-weight: 500;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-button:hover, .wait_button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .custom-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 7px;
            margin-right: 10px;
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            font-size: 1.3rem;
        }

        .custom-icon-header {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            margin-right: 10px;
            background-color: #0d6efd;
            color: white;
            font-size: 1.4rem;
        }

        .btn .custom-icon, 
        .back-button .custom-icon,
        .logout-button .custom-icon,
        .wait_button .custom-icon {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .back-button .custom-icon {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .logout-button .custom-icon {
            background-color: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 576px) {
            .container {
                padding: 1.25rem;
                margin: 0 15px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .custom-icon {
                font-size: 1rem;
            }

            .custom-icon-header {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 360px) {
            .container {
                padding: 1rem;
            }

            h2 {
                font-size: 1.35rem;
            }

            .custom-icon {
                font-size: 0.9rem;
            }

            .custom-icon-header {
                font-size: 1.1rem;
            }
        }
    </style>
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
