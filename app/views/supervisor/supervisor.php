<?php include __DIR__ . "/../layouts/header.php"; ?>
<style>
    /* Estilos generales */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 16px;
        line-height: 1.6;
        margin: 0;
        padding: 20px;
        background-color: #f8f9fa;
    }

    /* Menu */
    .tab-link {
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .tab-link.active {
        color: #2563eb;
        /* Color azul para la pestaña activa */
        border-bottom: 2px solid #2563eb;
        /* Línea de la pestaña activa */
    }

    .tab-content {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .tab-content.hidden {
        opacity: 0;
        transform: translateY(-10px);
    }

    .tab-content.show {
        opacity: 1;
        transform: translateY(0);
    }

    .tiempo {
        font-weight: bold;
    }

    /* Contenido */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .section-title {
        font-size: 32px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
        color: #333;
        text-transform: uppercase;
        cursor: pointer;
    }

    .filter-form {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f1f1f1;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    form input[type=text],
    form select {
        padding: 10px;
        width: 100%;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    form button {
        padding: 10px 22px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    form button:hover {
        background-color: #45a049;
    }

    .table-modern {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .table-modern th,
    .table-modern td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .table-modern th {
        background-color: #f2f2f2;
        color: #333;
        font-weight: bold;
        text-transform: uppercase;
    }

    .table-modern tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .table-modern tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Estilos específicos para operaciones */
    .table-modern tbody tr[data-start-time] {
        transition: background-color 0.3s;
    }

    .table-modern tbody tr[data-start-time]:hover {
        background-color: #e9ecef;
    }

    .text-success {
        font-size: 25px;
        color: #38a169;
        font-weight: 500;
    }

    .text-warning {
        font-size: 25px;
        color: #ecc94b;
        font-weight: 500;
    }

    .text-danger {
        font-size: 25px;
        color: #e53e3e;
        font-weight: 500;
    }

    .fecha-estilo {
        font-weight: 500;
        /* Usar un peso de fuente seminegrita para un toque sofisticado */
        color: #4A4A4A;
        /* Color gris oscuro más sutil para una apariencia más profesional */
        font-size: 1.1em;
        /* Tamaño de fuente ligeramente mayor para resaltar sin ser excesivo */
        border-bottom: 1px solid #DCDCDC;
        /* Línea delgada y sutil debajo del texto */
        padding-bottom: 4px;
        /* Espacio más ajustado entre la línea y el texto */
        display: inline-block;
        /* Asegura que el borde y el padding se apliquen correctamente */
        margin-top: 4px;
        /* Añade un pequeño espacio arriba para separarla del texto anterior */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
</style>

<main class="container mx-auto px-4 py-6">
    <!-- Menú de Pestañas -->
    <div class="mb-6">
        <ul class="flex border-b border-gray-300">
            <li class="mr-1">
                <a href="#operaciones-abiertas" class="tab-link py-2 px-4 block text-center text-gray-700 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500" id="tab-operaciones">
                    Operaciones Abiertas
                </a>
            </li>
            <li class="mr-1">
                <a href="#produccion-scrap" class="tab-link py-2 px-4 block text-center text-gray-700 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500" id="tab-produccion">
                    Producción y Scrap
                </a>
            </li>
        </ul>
    </div>

    <!-- Sección de Operaciones Abiertas -->
    <section id="operaciones-abiertas" class="tab-content bg-green-50 shadow-lg rounded-lg p-6 mb-10 hidden">
        <span class="fecha-estilo"><?php echo date('d/m/Y'); ?></span>
        <h2 class="section-title" id="operaciones-abiertas-toggle">Operaciones Abiertas - Área
            <?= htmlspecialchars($area) ?>
        </h2>

        <!-- Formulario de filtros -->
        <form method="post" class="space-y-4 mb-6 bg-gray-50 p-4 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="codigo_empleado" class="block text-gray-700 font-medium">Empleado:</label>
                    <select class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="codigo_empleado" name="codigo_empleado">
                        <option value="">Seleccione un empleado</option>
                        <?php foreach ($empleados as $empleado) : ?>
                            <option value="<?= htmlspecialchars($empleado['codigo_empleado']) ?>" <?= isset($filters['codigo_empleado']) && $filters['codigo_empleado'] === $empleado['codigo_empleado'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($empleado['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="tipo_boton" class="block text-gray-700 font-medium">Tipo Botón:</label>
                    <select class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="tipo_boton" name="tipo_boton">
                        <option value="">Seleccione una Operación</option>
                        <?php foreach ($botones as $boton) : ?>
                            <option value="<?= htmlspecialchars($boton['tipo_boton']) ?>" <?= isset($filters['tipo_boton']) && $filters['tipo_boton'] === $boton['tipo_boton'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($boton['tipo_boton']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="maquina" class="block text-gray-700 font-medium">Máquina:</label>
                    <select class="form-select mt-1 block w-full border-gray-300 rounded-md shadow-sm" id="maquina" name="maquina">
                        <option value="">Seleccione una máquina</option>
                        <?php foreach ($maquinas as $maquina) : ?>
                            <option value="<?= htmlspecialchars($maquina['id']) ?>" <?= isset($filters['maquina']) && $filters['maquina'] === $maquina['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($maquina['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filtrar</button>
                </div>
            </div>
        </form>


        <div id="operaciones-abiertas-content" class="overflow-x-auto">
            <table class="table table-modern">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="py-3 px-6 text-left">Nombre</th>
                        <th class="py-3 px-6 text-left">Operación</th>
                        <th class="py-3 px-6 text-left">Item</th>
                        <th class="py-3 px-6 text-left">Máquina</th>
                        <th class="py-3 px-6 text-left">Tiempo Transcurrido</th>
                        <th class="py-3 px-6 text-left">Descripción</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php if (empty($operaciones_abiertas)) : ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay operaciones abiertas que coincidan con los filtros seleccionados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($operaciones_abiertas as $row) : ?>
                            <?php
                            // Calcular el tiempo transcurrido
                            $fechaRegistro = new DateTime($row['fecha_registro']);
                            $fechaActual = new DateTime();
                            $intervalo = $fechaRegistro->diff($fechaActual);
                            $tiempoTranscurrido = $intervalo->format('%H:%I:%S');

                            // Determinar la clase de color según el tiempo transcurrido
                            $tiempoTranscurridoClass = '';
                            if ($intervalo->h >= 8) {
                                $tiempoTranscurridoClass = 'text-danger';
                            } elseif ($intervalo->h >= 6) {
                                $tiempoTranscurridoClass = 'text-warning';
                            } else {
                                $tiempoTranscurridoClass = 'text-success';
                            }

                            // Determinar la clase de la fila según el tipo de operación
                            $rowClass = $row['tipo_boton'] === 'Contratiempos' ? 'bg-red-100 blink' : 'bg-white';
                            ?>
                            <tr class="<?= $rowClass ?>" data-start-time="<?= $row['fecha_registro'] ?>">
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                <td><?= htmlspecialchars($row['tipo_boton']) ?></td>
                                <td><?= htmlspecialchars($row['item']) ?></td>
                                <td><?= htmlspecialchars($row['nombre_maquina']) ?></td>
                                <td><span class="tiempo <?= $tiempoTranscurridoClass ?>"><?= $tiempoTranscurrido ?></span></td>
                                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </section>

    <!-- Sección de Producción y Scrap -->
    <section id="produccion-scrap" class="tab-content bg-blue-50 p-6 rounded-lg shadow-lg hidden">
        <span class="fecha-estilo"><?php echo date('d/m/Y'); ?></span>
        <h2 class="section-title" id="detalle-produccion-scrap-toggle">
            Producción y Scrap X Máquina y Empleado - Área
            <?= htmlspecialchars($area) ?>
        </h2>

        <!-- Formulario de filtro -->
        <form method="POST" class="space-y-6 mb-6 bg-gray-50 p-6 rounded-lg shadow-lg">
            <div class="space-y-4">
                <div>
                    <label for="item" class="block text-gray-700 font-medium text-lg">Filtrar por Item:</label>
                    <input type="text" id="item" name="item" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="jtWo" class="block text-gray-700 font-medium text-lg">Filtrar por JT/WO:</label>
                    <input type="text" id="jtWo" name="jtWo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Filtrar
                </button>
            </div>
        </form>

        <!-- Detalle de producción y scrap por máquina -->
        <div class="overflow-x-auto" id="detalle-produccion-scrap-content">
            <?php if (!empty($produccion['produccion_por_maquina_empleado'])) : ?>
                <?php foreach ($produccion['produccion_por_maquina_empleado'] as $maquina_id => $maquina) : ?>
                    <strong>
                        <h3 class="text-xl font-semibold mb-4 border-b-2 border-gray-300 pb-2">
                            <?= htmlspecialchars($maquina['nombre_maquina']); ?>
                        </h3>
                    </strong>
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">Código Empleado</th>
                                <th class="py-3 px-4 text-left">Nombre Empleado</th>
                                <th class="py-3 px-4 text-left">Producción</th>
                                <th class="py-3 px-4 text-left">Scrap</th>
                                <th class="py-3 px-4 text-left">Hora</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            <?php foreach ($maquina['empleados'] as $empleado_codigo => $empleado) : ?>
                                <tr>
                                    <td class="py-3 px-4"><?= htmlspecialchars($empleado_codigo); ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($empleado['nombre_empleado']); ?></td>
                                    <td class="py-3 px-4"><?= number_format((float)$empleado['total_produccion'], 2, '.', ','); ?></td>
                                    <td class="py-3 px-4"><?= number_format((float)$empleado['total_scrap'], 2, '.', ','); ?></td>
                                    <td class="py-3 px-4 font-bold text-blue-600 text-lg text-center">
                                        <?= !empty($empleado['fecha_registro']) ? date('g:i A', strtotime($empleado['fecha_registro'])) : 'N/A'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table><br>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-center text-gray-600 font-semibold">No hay datos de producción o scrap que coincidan con los filtros seleccionados.</p>
            <?php endif; ?>
        </div>

        <!-- Totales generales de producción y scrap -->
        <div class="totals">
            <h3>Totales Generales</h3>
            <p>Total Producción:
                <b><?php echo number_format($produccion['totalProduccion'], 2, '.', ','); ?></b>
            </p>
            <p>Total Scrap:
                <b><?php echo number_format($produccion['totalScrap'], 2, '.', ','); ?></b>
            </p>
        </div>
    </section>
</main>

<!-- Scripts para las pestañas -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove 'active' class from all tab links
                tabLinks.forEach(link => link.classList.remove('active'));

                // Hide all tab contents
                tabContents.forEach(content => content.classList.add('hidden'));

                // Show the selected tab content
                const targetId = this.getAttribute('href');
                document.querySelector(targetId).classList.remove('hidden');
                document.querySelector(targetId).classList.add('show');

                // Add 'active' class to the clicked tab link
                this.classList.add('active');
            });
        });

        // Set default tab
        document.querySelector('#tab-operaciones').click();
    });
</script>


<div class="fixed bottom-4 right-4">
    <button class="btn btn-danger py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="confirmLogout()">Cerrar Sesión</button>
</div>
<style>
    .maquina-title {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }

    .table-maquina {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .table-maquina th,
    .table-maquina td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .table-maquina th {
        background-color: #f2f2f2;
        color: #333;
        font-weight: bold;
        text-transform: uppercase;
    }

    .table-maquina tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .table-maquina tbody tr:hover {
        background-color: #f1f1f1;
    }

    .totals {
        margin-top: 30px;
        padding: 20px;
        background-color: #f1f1f1;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .totals h3 {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .totals p {
        font-size: 18px;
        color: #555;
        margin-bottom: 5px;
    }

    @keyframes blink {
        0% {
            background-color: rgba(255, 99, 71, 0.2);
        }

        /* Red color with transparency */
        50% {
            background-color: rgba(255, 99, 71, 0.5);
        }

        /* More opaque red color */
        100% {
            background-color: rgba(255, 99, 71, 0.2);
        }

        /* Back to initial red color */
    }

    .blink {
        animation: blink 1s infinite;
        /* Adjust the duration as needed */
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener elementos relevantes
        const operacionesAbiertasToggle = document.getElementById('operaciones-abiertas-toggle');
        const operacionesAbiertasContent = document.getElementById('operaciones-abiertas-content');
        const detalleProduccionScrapToggle = document.getElementById('detalle-produccion-scrap-toggle');
        const detalleProduccionScrapContent = document.getElementById('detalle-produccion-scrap-content');

        // Mostrar u ocultar secciones al hacer clic en los títulos
        operacionesAbiertasToggle.addEventListener('click', function() {
            toggleSection(operacionesAbiertasContent);
        });

        detalleProduccionScrapToggle.addEventListener('click', function() {
            toggleSection(detalleProduccionScrapContent);
        });

        // Función para alternar la visibilidad de una sección
        function toggleSection(section) {
            section.classList.toggle('active');
            if (section.classList.contains('active')) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        }
    });
</script>
<script>
    $(document).ready(function() {
        function updateTiempoTranscurrido() {
            $('tbody tr').each(function() {
                var startTime = $(this).data('start-time');
                if (startTime) {
                    var fechaRegistro = new Date(startTime);
                    var fechaActual = new Date();
                    var diff = Math.abs(fechaActual - fechaRegistro) / 1000;
                    var horas = Math.floor(diff / 3600);
                    var minutos = Math.floor((diff % 3600) / 60);
                    var segundos = Math.floor(diff % 60);
                    var tiempoTranscurrido = ('0' + horas).slice(-2) + ':' +
                        ('0' + minutos).slice(-2) + ':' +
                        ('0' + segundos).slice(-2);

                    var tiempoTranscurridoClass = '';
                    if (horas >= 4) {
                        tiempoTranscurridoClass = 'text-danger';
                    } else if (horas >= 2) {
                        tiempoTranscurridoClass = 'text-warning';
                    } else {
                        tiempoTranscurridoClass = 'text-success';
                    }

                    $(this).find('.tiempo').text(tiempoTranscurrido).removeClass('text-success text-warning text-danger').addClass(tiempoTranscurridoClass);
                }
            });
        }

        updateTiempoTranscurrido();
        setInterval(updateTiempoTranscurrido, 1000);
    });

    function confirmLogout() {
        if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
            document.cookie = 'jwt=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            window.location.href = "./login.php";
        }
    }
</script>
</body>

</html>