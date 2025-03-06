<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Operaciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #585758;
        }

        .container {
            width: 50%;
            background-color: #605e5f;
            padding: 30px;
            border-radius: 10px;
        }

        h2,
        label {
            color: white;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 20px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #3f9ed7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        button:hover {
            background-color: #585758;
        }

        /* Estilo para el botón de cerrar sesión */
        .logout-button {
            width: 100%;
            padding: 10px;
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .logout-button:hover {
            background-color: #c9302c;
        }

        .active-button {
            position: relative;
            background-color: #4ac84a !important;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            overflow: hidden;
            font-weight: bold;
            z-index: 1;
            animation: activeGlow 1.5s infinite ease-in-out;
        }

        .active-button::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background-color: rgba(57, 255, 20, 0.4);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            animation: expandGlow 1.5s infinite ease-in-out;
        }

        @keyframes expandGlow {

            0%,
            100% {
                width: 0;
                height: 0;
                opacity: 0;
            }

            50% {
                width: 200%;
                height: 200%;
                opacity: 1;
            }
        }

        @keyframes activeGlow {

            0%,
            100% {
                box-shadow: 0 0 10px #39ff14;
            }

            50% {
                box-shadow: 0 0 20px #39ff14, 0 0 30px #39ff14;
            }
        }

        .wait_button {
            width: 100%;
            padding: 10px;
            background-color: #f0ad4e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .wait_button:hover {
            background-color: #ec971f;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Datos de Operaciones</h2>
        <form class="form" method="post" action="/timeControl/public/seleccionar_data">
            <label for="jtWo">JT/WO:</label>
            <input type="text" id="jtWo" placeholder="Ingrese Código JT/WO" name="jtWo" required>

            <label for="item">Item:</label>
            <input type="text" id="item" placeholder="Ingrese un Item" name="item" required>

            <button type="submit">Ingresar</button>
        </form>
        <!-- Botón de cerrar sesión con estilo rojo -->
        <button class="logout-button" onclick="confirmLogout()">Cerrar Sesión</button>
        <br><br>
        <!-- Formulario para Espera de Trabajo -->
        <form class="form" method="POST" action="./modulos/guardar_registros.php" onsubmit="return confirmWait()">
            <input type="hidden" name="tipo_boton" value="Espera_trabajo">
            <button type="submit" class="wait_button <?php echo ($active_button_id === 'Espera_trabajo') ? 'active-button' : ''; ?>">Espera Trabajo</button>
        </form>
    </div>

    <!-- Scripts JS necesarios -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        // Función para mostrar mensajes según el parámetro 'status' en la URL
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/timeControl/public/getStatus') // Llama al endpoint de PHP
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.message) {
                        const toastrFunction = data.status === "success" ? toastr.success : toastr.error;

                        toastrFunction(data.message, '', {
                            timeOut: 2000
                        });

                        setTimeout(() => {
                            window.location.href = "/timeControl/public/ingresar_datos"; // Limpia la URL
                        }, 2000);
                    }
                });
        });

        // Función para confirmar la espera de trabajo antes de enviar el formulario
        function confirmWait() {
            return confirm("¿Estás seguro de que deseas poner en espera el trabajo?");
        }

        // Función para confirmar el cierre de sesión
        function confirmLogout() {
            if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
                window.location.href = "./modulos/logout.php";
            }
        }
    </script>
    <?php include __DIR__ . "/../layouts/footer.php"; ?>