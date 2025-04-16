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
</head>

<body>
    <div class="container">
        <h2>Datos de Operaciones - <?= htmlspecialchars($maquina) ?></h2>

        <!-- Formulario de datos -->
        <form class="form" method="post" action="/timeControl/public/seleccionar_data">
            <div class="form-group">
                <label for="jtWo">JT/WO:</label>
                <input type="text" id="jtWo" class="form-control" placeholder="Ingrese Código JT/WO" name="jtWo" required>
            </div>

            <div class="form-group">
                <label for="item">Item:</label>
                <input type="text" id="item" class="form-control" placeholder="Ingrese un Item" name="item" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>

        <!-- Botones de acción -->
        <button class="logout-button" onclick="confirmLogout()">Cerrar Sesión</button>

        <!-- Formulario Espera de Trabajo -->
        <form class="form" method="POST" action="/timeControl/public/espera_trabajo" onsubmit="return confirmWait()">
            <input type="hidden" name="tipo_boton" value="Espera_trabajo">
            <button type="submit" class="wait_button <?= ($active_button_id === 'Espera_trabajo') ? 'active-button' : '' ?>">
                Espera Trabajo
            </button>
        </form>

        <!-- Componente de Correcciones -->
        <?php if (isset($mostrar_correcciones) && $mostrar_correcciones): ?>
            <?php include __DIR__ . '/components/correcciones_modal.php'; ?>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/datos_trabajo.js"></script>

    <?php include __DIR__ . "/../layouts/footer.php"; ?>