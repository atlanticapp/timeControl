<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Máquina</title>
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

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 20px;
        }

        select option {
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>

    <div class="container">
        <h2>Selección de Máquina</h2>
        <form method="POST" action="/timeControl/public/seleccionar_maquina">
            <div class="form-group">
                <label for="maquina_id">Selecciona tu máquina:</label>
                <select name="maquina_id" id="maquina_id" required>
        <!-- Aquí se llenan las máquinas -->
        <?php foreach ($maquinas as $maquina): ?>
            <option value="<?php echo $maquina['id']; ?>"><?php echo $maquina['nombre']; ?></option>
        <?php endforeach; ?>
    </select>
            </div>
            <button type="submit" class="btn btn-primary">Seleccionar</button>
        </form>
        <button class="logout-button" onclick="confirmLogout()">Cerrar Sesión</button>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
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
                            window.location.href = "/timeControl/public/datos_trabajo_maquina"; // Limpia la URL
                        }, 2000);
                    }
                });
        });

        function confirmLogout() {
            if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
                window.location.href = "/timeControl/public/logout";
            }
        }
    </script>
    <?php include __DIR__ . "/../layouts/footer.php"; ?>