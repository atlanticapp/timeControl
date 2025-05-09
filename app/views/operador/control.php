<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Tiempos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" href="assets/css/styleControl.css?v=<?php echo time(); ?>">
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
        <!-- Componente de Correcciones -->
        <?php if (isset($mostrar_correcciones) && $mostrar_correcciones): ?>
            <?php include __DIR__ . '/components/correcciones_modal.php'; ?>
        <?php endif; ?>
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
                <!-- Nuevos campos añadidos para PO y Cliente -->
                <tr>
                    <th>Orden de Compra:</th>
                    <td><?php echo htmlspecialchars($data['po'] ?? ''); ?></td>
                </tr>
                <tr>
                    <th>Cliente:</th>
                    <td><?php echo htmlspecialchars($data['cliente'] ?? ''); ?></td>
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
    <!-- Librerías -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.message) {
                        const toastrFunction = data.status === "success" ? toastr.success : toastr.error;
                        toastrFunction(data.message, '', {
                            timeOut: 2000
                        });
                    }
                })
                .catch(error => console.error('Error al obtener el estado:', error));
        });

        function numberWithCommas(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Validación moderna para Final de Producción
        function validateFinalProduction() {
            const prodInput = document.getElementById("finalProductionValue").value.trim();
            const scrapInput = document.getElementById("finalScraptAmount").value.trim();

            const prod = prodInput !== '' ? parseFloat(prodInput).toFixed(2) : '0.00';
            const scrap = scrapInput !== '' ? parseFloat(scrapInput).toFixed(2) : '0.00';

            Swal.fire({
                title: 'Confirmación de Final de Producción',
                html: `
            <p><strong>Cantidad producida:</strong> ${prod} lb</p>
            <p><strong>Cantidad de scrap:</strong> ${scrap} lb</p>
            <p style='color: red;'>¿Está seguro de que desea ingresar el Final de producción? Se cerrará la sesión...</p>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#f44336',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("finForm").submit();
                }
            });

            return false;
        }


        // Validación moderna para Entrega Parcial
        function validateParcial() {
            const prodInput = document.getElementById("parcialProductionValue").value.trim();
            const scrapInput = document.getElementById("parcialScraptAmount").value.trim();

            if (prodInput === '' && scrapInput === '') {
                alert("Debes ingresar al menos un valor en Producción o Scrap.");
                return false;
            }

            const prod = prodInput !== '' ? parseFloat(prodInput).toFixed(2) : '0.00';
            const scrap = scrapInput !== '' ? parseFloat(scrapInput).toFixed(2) : '0.00';

            Swal.fire({
                title: 'Confirmación de Parcial',
                html: `
                    <p><strong>Cantidad producida:</strong> ${prod} lb</p>
                    <p><strong>Cantidad de scrap:</strong> ${scrap} lb</p>
                    <p style='color: red;'>¿Está seguro de que desea ingresar el Parcial?</p>
                    `,

                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#f44336',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("parcialForm").submit();
                }
            });

            return false;
        }



        // Validación simple de contratiempos
        function validateBadCopyForm() {
            const val = document.getElementById("badCopy").value;
            if (val === "") {
                toastr.warning("Por favor, seleccione una opción válida para Contratiempos.");
                return false;
            }
            return confirm('¿Está seguro de que desea realizar "Contratiempos"?');
        }

        // Toggle visibilidad
        function toggleFinalProductionInput() {
            const el = document.getElementById("finalProductionInput");
            el.style.display = (el.style.display === "none") ? "block" : "none";
        }

        function toggleParcialInput() {
            const el = document.getElementById("parcialInput");
            el.style.display = (el.style.display === "none" || el.style.display === "") ? "block" : "none";
        }

        // Confirmaciones generales
        function confirmMakeReady() {
            return confirm('¿Está seguro de que desea realizar "Preparación"?');
        }

        function confirmGoodCopy() {
            return confirm('¿Está seguro de que desea realizar "Producción"?');
        }

        function validateVelocidad() {
            const velocidad = numberWithCommas(document.getElementById("velocidadProduccion").value.trim());
            return confirm(`¿Está seguro de que desea Ingresar ${velocidad} de velocidad?`);
        }
    </script>

</body>

</html>