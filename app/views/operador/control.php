<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Tiempos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" href="./assets/css/styleControl.css?v=<?php echo time(); ?>">
    <style>
        .historial {
            width: 150%;
            margin-top: 20px;
        }

        .styled-table {
            width: 200px auto;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 18px;
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }

        .user-menu table {
            max-width: auto;


        }

        .user-menu th,
        .user-menu td {

            text-align: left;
        }

        .comment-section {
            margin-top: 20px;
            padding: 20px;
            background-color: #f2f2f2;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .comment-section h2 {
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }

        .comment-form {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }

        .comment-form textarea {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 3px;
            resize: vertical;
            /* Permite redimensionar verticalmente */
        }

        .comment-form button {
            width: 25%;
            padding: 10px;
            font-size: 16px;
            background-color: #009879;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .comment-form button:hover {
            background-color: #007d6e;
            /* Color de fondo más oscuro al pasar el ratón */
        }
    </style>

</head>

<body>
    <div class="container">
        <img src="./assets/img/logoContr.png" alt="Logo de la empresa" class="logo">
        <div class="user-menu">
            <table>
                <tr>
                    <th>Bienvenido:</th>
                    <td><?php echo htmlspecialchars($data['nombre']); ?>!</td>
                </tr>
                <tr>
                    <th>Job Ticket:</th>
                    <td><?php echo htmlspecialchars($data['jtWo']); ?></td>
                </tr>
                <tr>
                    <th>Maquina:</th>
                    <td><?php echo htmlspecialchars($maquina); ?></td>
                </tr>
                <tr>
                    <th>Item:</th>
                    <td><?php echo htmlspecialchars($data['item']); ?></td>
                </tr>
            </table>
        </div>
        <center>
            <h1>Control de Tiempos - <?php echo htmlspecialchars($area); ?></h1>
        </center>
        <div class="buttons">
            <!-- Formulario para Preparación -->
            <form id="makeReadyForm" action="/timeControl/public/registrar" method="post" onsubmit="return confirmMakeReady()">
                <input type="hidden" name="tipo_boton" value="Preparación">
                <button type="submit" id="makeReadyButton" <?php if ($data['active_button_id'] === 'Preparación') echo 'class="active-button"'; ?>>Preparación</button>
            </form>
            <!-- Formulario para Producción -->
            <form id="goodCopyForm" action="/timeControl/public/registrar" method="post" onsubmit="return confirmGoodCopy()">
                <input type="hidden" name="tipo_boton" value="Producción">
                <button type="submit" id="goodCopyButton" <?php if ($active_button_id === 'Producción') echo 'class="active-button"'; ?>>Producción</button>
            </form>
            <!-- Formulario para Contratiempos -->
            <form id="badCopyForm" action="/timeControl/public/registrar" method="post" onsubmit="return validateBadCopyForm()">
                <input type="hidden" name="tipo_boton" value="Contratiempos">
                <button type="submit" id="badCopyButton" <?php if ($active_button_id === 'Contratiempos') echo 'class="active-button"'; ?>>Contratiempos</button>
                <select name="badCopy" id="badCopy" required>
                    <option value="">Seleccionar Contratiempos</option>
                    <?php
                    // Verificar si el array $bad_copy tiene elementos
                    if (!empty($bad_copy)) {
                        // Iterar sobre el array y mostrar las opciones
                        foreach ($bad_copy as $row) {
                            echo "<option value='" . htmlspecialchars($row['descripcion']) . "'>" . htmlspecialchars($row['descripcion']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay contratiempos disponibles</option>";
                    }
                    ?>
                </select>
            </form>
            <!-- Formulario para Velocidad -->
            <form id="velocidadForm" class="velForm" action="/timeControl/public/saveVelocidad" method="post">
                <input type="hidden" name="tipo_boton" value="Velocidad">
                <input type="number" style="font-size: 20px;" name="velocidadProduccion" id="velocidadProduccion" placeholder="Velocidad Producción" required>
                <button type="submit">Asignar Velocidad</button>
            </form>
            <!-- Formulario para Fin de Producción -->
            <form id="finForm" action="/timeControl/public/registrar" method="post" onsubmit="return validateFinalProduction()">
                <input type="hidden" name="tipo_boton" value="final_produccion">
                <button type="button" onclick="toggleFinalProductionInput()" id="finButton" <?php if ($active_button_id === 'final_produccion'); ?>>Fin</button>
                <div class="finalProductionInput" id="finalProductionInput" style="display: <?php echo ($active_button_id === 'finButton') ? 'block' : 'none'; ?>;">
                    <label for="finalProductionValue" class="input-label">Producción <span class="unit">(Lb)</span>:</label>
                    <input type="number" name="finalProductionValue" id="finalProductionValue" placeholder="Cantidad producida" step="0.01" inputmode="decimal">

                    <label for="finalScraptAmount" class="input-label">Scrap <span class="unit">(Lb)</span>:</label>
                    <input type="number" name="finalScraptAmount" id="finalScraptAmount" placeholder="Cantidad de scrap" step="0.01" inputmode="decimal">

                    <button type="submit" id="finProdSubmit">Registrar</button>
                </div>
            </form>
            <!-- Formulario para Parcial -->
            <form id="parcialForm" action="/timeControl/public/registrar" method="post" onsubmit="return validateParcial()">
                <input type="hidden" name="tipo_boton" value="Parcial">
                <button type="button" onclick="toggleParcialInput()" id="parcialButton" <?php if ($active_button_id === 'Parcial') echo 'class="active-button"'; ?>>Entrega Parcial</button>
                <div class="parcialInput" id="parcialInput" style="display: <?php echo ($active_button_id === 'parcialButton') ? 'block' : 'none'; ?>;">
                    <label for="parcialProductionValue" class="input-label">Producción <span class="unit">(Lb)</span>:</label>
                    <input type="number" name="parcialProductionValue" id="parcialProductionValue" placeholder="Cantidad producida" step="0.01" inputmode="decimal">

                    <label for="parcialScraptAmount" class="input-label">Scrap <span class="unit">(Lb)</span>:</label>
                    <input type="number" name="parcialScraptAmount" id="parcialScraptAmount" placeholder="Cantidad de scrap" step="0.01" inputmode="decimal">

                    <button type="submit" id="parcialSubmit">Registrar</button>
                </div>
            </form>

            <div class="historial">
                <h2>Resumen de Entrega Parcial</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Total Producción</th>
                            <th>Total Scrapt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_produccion = 0;
                        $total_scrapt = 0;
                        foreach ($historial as $registro) {
                            $total_produccion += $registro['cantidad_produccion'];
                            $total_scrapt += $registro['cantidad_scrapt'];
                        }
                        ?>
                        <tr>
                            <td id="totalProduccion"><strong><?php echo $total_produccion; ?></strong></td>
                            <td id="totalScrapt"><strong><?php echo $total_scrapt; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>
        <!-- Agrega esta sección al final del contenido dentro de <div class="container"> -->
        <div class="comment-section">
            <h2>Comentarios</h2>
            <form class="comment-form" action="/timeControl/public/addComentario" method="post">
                <textarea name="comentario" id="comentario" rows="3" placeholder="Escribe tu comentario aquí..." maxlength="255" required></textarea>
                <button type="submit">Guardar Comentario</button>
            </form>
        </div>
        <table>

            <thead>
                <tr>
                    <th>Preparación</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si el array $preparacion tiene elementos
                if (!empty($preparacion)) {
                    // Iterar sobre el array y mostrar las opciones
                    foreach ($preparacion as $row) {
                        echo "<tr><td>" . htmlspecialchars($row['descripcion']) . "</td></tr>";
                    }
                } else {
                    echo "<tr><td>No hay operaciones disponibles</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Llama al endpoint para obtener el estado y mensaje
            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    // Asegúrate de que el 'status' y 'message' estén presentes
                    if (data.status && data.message) {
                        const toastrFunction = data.status === "success" ? toastr.success : toastr.error;

                        // Muestra el mensaje usando toastr
                        toastrFunction(data.message, '', {
                            timeOut: 2000 // El mensaje desaparece después de 2 segundos
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al obtener el estado:', error);
                });
        });


        // Validar Contratiempos
        function validateBadCopyForm() {
            var badCopySelect = document.getElementById("badCopy");
            var selectedOption = badCopySelect.options[badCopySelect.selectedIndex].value;
            if (selectedOption === "") {
                alert("Por favor, seleccione una opción válida para Contratiempos.");
                return false;
            }
            return confirm('¿Está seguro de que desea realizar "Contratiempos"?');
        }

        // Final de Producción
        function validateFinalProduction() {
            var finalProductionValue = document.getElementById("finalProductionValue").value.trim();
            var finalScraptAmount = document.getElementById("finalScraptAmount").value.trim();

            // Convertir los valores a formato con separador de miles
            finalProductionValue = numberWithCommas(finalProductionValue);
            finalScraptAmount = numberWithCommas(finalScraptAmount);

            // Construir el mensaje de alerta personalizado
            var alertMessage = "<div style='padding: 20px; background-color: #f2f2f2; border: 1px solid #ccc; border-radius: 5px;'>";
            alertMessage += "<h2 style='margin-top: 0;'>Confirmación de Final de Producción</h2>";
            alertMessage += "<p><strong>Cantidad producida:</strong> " + finalProductionValue + "</p>";
            alertMessage += "<p><strong>Cantidad de scrap:</strong> " + finalScraptAmount + " Lb</p>";
            alertMessage += "<p style='color: red;'>¿Está seguro de que desea ingresar el Final de producción? Se cerrará la sesión...</p>";
            alertMessage += "<div style='text-align: center; margin-top: 20px;'>";
            alertMessage += "<button id='confirmButton' style='padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;'>Sí, confirmar</button>";
            alertMessage += "<button id='cancelButton' style='padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;'>Cancelar</button>";
            alertMessage += "</div></div>";

            // Mostrar la alerta personalizada
            showCustomAlert(alertMessage);

            // Agregar eventos de clic a los botones de la alerta
            document.getElementById("confirmButton").addEventListener("click", confirmFinalProduction);
            document.getElementById("cancelButton").addEventListener("click", cancelFinalProduction);

            // Evitar el envío automático del formulario
            return false;
        }

        // Función para mostrar la alerta personalizada
        function showCustomAlert(message) {
            // Crear un div para mostrar la alerta
            var alertContainer = document.createElement("div");
            alertContainer.innerHTML = message;
            alertContainer.style.position = "fixed";
            alertContainer.style.top = "50%";
            alertContainer.style.left = "50%";
            alertContainer.style.transform = "translate(-50%, -50%)";
            alertContainer.style.zIndex = "9999";
            alertContainer.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
            alertContainer.style.padding = "20px";
            alertContainer.style.color = "#000";
            alertContainer.style.maxWidth = "400px";
            alertContainer.style.borderRadius = "5px";

            // Agregar la alerta al body del documento
            document.body.appendChild(alertContainer);
        }

        // Función para cerrar la alerta y confirmar la acción
        function confirmFinalProduction() {
            // Cerrar la alerta
            document.querySelector("div[style*='rgba(0, 0, 0, 0.5)']").remove();

            // Enviar el formulario
            document.getElementById("finForm").submit();

        }

        // Función para cerrar la alerta y cancelar la acción
        function cancelFinalProduction() {
            // Cerrar la alerta
            document.querySelector("div[style*='rgba(0, 0, 0, 0.5)']").remove();
        }

        // Función para agregar separadores de miles a un número
        function numberWithCommas(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function toggleFinalProductionInput() {
            var finalProductionInput = document.getElementById("finalProductionInput");
            if (finalProductionInput.style.display === "none") {
                finalProductionInput.style.display = "block";
            } else {
                finalProductionInput.style.display = "none";
            }
        }

        // Validar Formulario de Parcial
        function validateParcial() {
            var parcialProductionValue = document.getElementById("parcialProductionValue").value.trim();
            var parcialScraptAmount = document.getElementById("parcialScraptAmount").value.trim();

            // Convertir los valores a formato con separador de miles
            parcialProductionValue = numberWithCommas(parcialProductionValue);
            parcialScraptAmount = numberWithCommas(parcialScraptAmount);

            // Construir el mensaje de alerta personalizado
            var alertMessage = "<div style='padding: 20px; background-color: #f2f2f2; border: 1px solid #ccc; border-radius: 5px;'>";
            alertMessage += "<h2 style='margin-top: 0;'>Confirmación de Parcial</h2>";
            alertMessage += "<p><strong>Cantidad producida:</strong> " + parcialProductionValue + "</p>";
            alertMessage += "<p><strong>Cantidad de scrap:</strong> " + parcialScraptAmount + " Lb</p>";
            alertMessage += "<p style='color: red;'>¿Está seguro de que desea ingresar el Parcial? Se cerrará la sesión...</p>";
            alertMessage += "<div style='text-align: center; margin-top: 20px;'>";
            alertMessage += "<button id='confirmParcialButton' style='padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;'>Sí, confirmar</button>";
            alertMessage += "<button id='cancelParcialButton' style='padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;'>Cancelar</button>";
            alertMessage += "</div></div>";

            // Mostrar la alerta personalizada
            showCustomParcialAlert(alertMessage);

            // Agregar eventos de clic a los botones de la alerta
            document.getElementById("confirmParcialButton").addEventListener("click", confirmParcial);
            document.getElementById("cancelParcialButton").addEventListener("click", cancelParcial);

            // Evitar el envío automático del formulario
            return false;
        }

        // Función para mostrar la alerta personalizada de Parcial
        function showCustomParcialAlert(message) {
            // Crear un div para mostrar la alerta de Parcial
            var parcialAlertContainer = document.createElement("div");
            parcialAlertContainer.innerHTML = message;
            parcialAlertContainer.style.position = "fixed";
            parcialAlertContainer.style.top = "50%";
            parcialAlertContainer.style.left = "50%";
            parcialAlertContainer.style.transform = "translate(-50%, -50%)";
            parcialAlertContainer.style.zIndex = "9999";
            parcialAlertContainer.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
            parcialAlertContainer.style.padding = "20px";
            parcialAlertContainer.style.color = "#000";
            parcialAlertContainer.style.maxWidth = "400px";
            parcialAlertContainer.style.borderRadius = "5px";

            // Agregar la alerta de Parcial al body del documento
            document.body.appendChild(parcialAlertContainer);
        }

        // Función para cerrar la alerta de Parcial y confirmar la acción
        function confirmParcial() {
            // Cerrar la alerta de Parcial
            document.querySelector("div[style*='rgba(0, 0, 0, 0.5)']").remove();

            // Enviar el formulario de Parcial
            document.getElementById("parcialForm").submit();
        }

        // Función para cerrar la alerta de Parcial y cancelar la acción
        function cancelParcial() {
            // Cerrar la alerta de Parcial
            document.querySelector("div[style*='rgba(0, 0, 0, 0.5)']").remove();
        }

        // Función para alternar la visibilidad de los campos de entrada para Parcial
        function toggleParcialInput() {
            var parcialInput = document.getElementById("parcialInput");
            if (parcialInput.style.display === "none" || parcialInput.style.display === "") {
                parcialInput.style.display = "block";
            } else {
                parcialInput.style.display = "none";
            }
        }



        function confirmMakeReady() {
            return confirm('¿Está seguro de que desea realizar "Preparación"?');
        }

        function confirmGoodCopy() {
            return confirm('¿Está seguro de que desea realizar "Producción"?');
        }

        function validateVelocidad() {
            velocidadPro = document.getElementById("velocidadProduccion").value.trim();
            velocidadPro = numberWithCommas(velocidadPro);
            return confirm('¿Está seguro de que desea Ingresar ' + velocidadPro + " de velocidad?");
        }
    </script>
</body>

</html>