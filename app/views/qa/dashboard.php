<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control QA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 font-sans text-gray-800">
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>
    <!-- Contenido principal mejorado -->
    <main class="lg:ml-72 p-6 md:p-8 transition-all duration-300 min-h-screen bg-gray-50">
        <div class="container mx-auto pt-14 lg:pt-4">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>Panel de Control QA
                </h1>
            </div>

            <?php
            $deliveryStats = [
                'total' => $data['stats']['pendientes'] ?? 0,
                'production' => $data['stats']['produccion_pendiente'] ?? 0,
                'scrap' => $data['stats']['scrap_pendientes'] ?? 0,
                'validated' => $data['stats']['validadas'] ?? 0,
                'en_proceso' => $data['stats']['en_proceso'] ?? 0
            ];
            function calculatePercentage($value, $total)
            {
                return $total > 0 ? number_format(($value / $total) * 100, 2) : 0;
            }
            $productionPercent = calculatePercentage($deliveryStats['production'], $deliveryStats['total']);
            $scrapPercent = calculatePercentage($deliveryStats['scrap'], $deliveryStats['total']);
            ?>

            <!-- Cards de estadísticas mejoradas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Validación de Entregas -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-transform hover:shadow-xl">
                    <div class="bg-gradient-to-r from-teal-600 to-teal-700 text-white p-6 flex justify-between items-center">
                        <h3 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-check-circle mr-3"></i> Validación de Entregas
                        </h3>
                        <span class="bg-teal-500 px-3 py-1 rounded-full text-xs flex items-center shadow-inner">
                            <i class="fas fa-sync-alt mr-1"></i> Tiempo real
                        </span>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <?php
                            $statBoxes = [
                                ['label' => 'Pendientes', 'color' => 'teal', 'value' => $deliveryStats['total'], 'icon' => 'clipboard-list'],
                                ['label' => 'Producción', 'color' => 'green', 'value' => $deliveryStats['production'], 'icon' => 'industry'],
                                ['label' => 'Scrap', 'color' => 'red', 'value' => $deliveryStats['scrap'], 'icon' => 'trash-alt'],
                            ];
                            foreach ($statBoxes as $box) :
                            ?>
                                <div class="bg-<?= $box['color'] ?>-50 p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                                    <div class="flex justify-center mb-2">
                                        <i class="fas fa-<?= $box['icon'] ?> text-<?= $box['color'] ?>-500 text-lg"></i>
                                    </div>
                                    <div class="text-sm text-<?= $box['color'] ?>-600 font-medium mb-1"><?= $box['label'] ?></div>
                                    <div class="text-2xl md:text-3xl font-bold text-<?= $box['color'] ?>-700">
                                        <?= number_format($box['value']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between text-xs text-gray-600 mb-2">
                                <span class="flex items-center">
                                    <span class="w-3 h-3 bg-green-500 rounded-full mr-1"></span>
                                    Producción: <?= $productionPercent ?>%
                                </span>
                                <span class="flex items-center">
                                    <span class="w-3 h-3 bg-red-500 rounded-full mr-1"></span>
                                    Scrap: <?= $scrapPercent ?>%
                                </span>
                            </div>
                            <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden flex">
                                <div class="bg-green-500 transition-all duration-500" style="width: <?= $productionPercent ?>%"></div>
                                <div class="bg-red-500 transition-all duration-500" style="width: <?= $scrapPercent ?>%"></div>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <a href="/timeControl/public/validacion" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg transition-all flex items-center w-full justify-center shadow-md hover:shadow-lg">
                                <i class="fas fa-check-circle mr-2"></i> Ir a Validación
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Acción QA (Simplificado) -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-transform hover:shadow-xl">
                    <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white p-6 flex justify-between items-center">
                        <h3 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-clipboard-check mr-3"></i> Acción QA
                        </h3>
                        <span class="bg-amber-500 px-3 py-1 rounded-full text-xs flex items-center shadow-inner">
                            <i class="fas fa-sync-alt mr-1"></i> Tiempo real
                        </span>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="text-center">
                            <div class="bg-amber-50 p-6 rounded-lg shadow hover:shadow-md transition-shadow">
                                <div class="flex justify-center mb-3">
                                    <i class="fas fa-check-double text-amber-500 text-2xl"></i>
                                </div>
                                <div class="text-sm text-amber-600 font-medium mb-2">Validadas</div>
                                <div class="text-3xl md:text-4xl font-bold text-amber-700">
                                    <?= number_format($deliveryStats['validated']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <a href="/timeControl/public/accion"
                                class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-lg transition-all flex items-center w-full justify-center shadow-md hover:shadow-lg">
                                <i class="fas fa-clipboard-check mr-2"></i> Ir a Acción QA
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Retenciones (Actualizado) -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-transform hover:shadow-xl">
                    <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 text-white p-6 flex justify-between items-center">
                        <h3 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3"></i> Retenciones
                        </h3>
                        <span class="bg-yellow-500 px-3 py-1 rounded-full text-xs flex items-center shadow-inner">
                            <i class="fas fa-sync-alt mr-1"></i> Activo
                        </span>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-3 gap-4">
                            <?php
                            $retencionStats = [
                                [
                                    'icon' => 'box-check',
                                    'color' => 'blue',
                                    'label' => 'Producción',
                                    'value' => $data['destinos']['produccion']['total'] ?? 0
                                ],
                                [
                                    'icon' => 'recycle',
                                    'color' => 'green',
                                    'label' => 'Retrabajo',
                                    'value' => $data['destinos']['retrabajo']['total'] ?? 0
                                ],
                                [
                                    'icon' => 'trash-alt',
                                    'color' => 'red',
                                    'label' => 'Destrucción',
                                    'value' => $data['destinos']['destruccion']['total'] ?? 0
                                ]
                            ];

                            foreach ($retencionStats as $stat): ?>
                                <div class="bg-<?= $stat['color'] ?>-50 p-4 rounded-lg text-center">
                                    <i class="fas fa-<?= $stat['icon'] ?> text-<?= $stat['color'] ?>-500 text-xl mb-2"></i>
                                    <div class="text-sm font-medium text-<?= $stat['color'] ?>-600"><?= $stat['label'] ?></div>
                                    <div class="text-xl font-bold text-<?= $stat['color'] ?>-700"><?= $stat['value'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="flex justify-center">
                            <a href="/timeControl/public/retenciones" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg transition-all flex items-center w-full justify-center shadow-md hover:shadow-lg">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Ver Retenciones
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Destinos (Actualizada) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
                <!-- Producción Final -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-box-open mr-2"></i> Producción Final
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="text-center text-3xl font-bold text-blue-600 mb-2">
                            <?= number_format($data['destinos']['produccion']['total'] ?? 0) ?>
                        </div>
                        <div class="text-sm text-gray-500 text-center mb-2">Destinos asignados</div>
                        <div class="text-center text-lg font-semibold text-blue-700 mb-4">
                            <?= number_format($data['destinos']['produccion']['cantidad'] ?? 0, 2) ?> <span class="text-sm">lb.</span>
                        </div>
                        <a href="/timeControl/public/destinos/produccion" class="block text-center bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2 rounded transition-colors">
                            Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Retrabajo -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-recycle mr-2"></i> Retrabajo
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="text-center text-3xl font-bold text-green-600 mb-2">
                            <?= number_format($data['destinos']['retrabajo']['total'] ?? 0) ?>
                        </div>
                        <div class="text-sm text-gray-500 text-center mb-2">Destinos asignados</div>
                        <div class="text-center text-lg font-semibold text-green-700 mb-4">
                            <?= number_format($data['destinos']['retrabajo']['cantidad'] ?? 0, 2) ?> <span class="text-sm">lb.</span>
                        </div>
                        <a href="/timeControl/public/destinos/retrabajo" class="block text-center bg-green-50 text-green-600 hover:bg-green-100 px-4 py-2 rounded transition-colors">
                            Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Destrucción -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-trash-alt mr-2"></i> Destrucción
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="text-center text-3xl font-bold text-red-600 mb-2">
                            <?= number_format($data['destinos']['destruccion']['total'] ?? 0) ?>
                        </div>
                        <div class="text-sm text-gray-500 text-center mb-2">Destinos asignados</div>
                        <div class="text-center text-lg font-semibold text-red-700 mb-4">
                            <?= number_format($data['destinos']['destruccion']['cantidad'] ?? 0, 2) ?> <span class="text-sm">lb.</span>
                        </div>
                        <a href="/timeControl/public/destinos/destruccion" class="block text-center bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded transition-colors">
                            Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Revisiones Pendientes (Nueva sección) -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-8">
                <div class="bg-gradient-to-r from-rose-600 to-rose-700 text-white p-5 flex justify-between items-center">
                    <h3 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-search mr-3"></i> Revisiones Pendientes
                    </h3>
                    <span class="bg-rose-500 px-3 py-1 rounded-full text-xs flex items-center shadow-inner">
                        <i class="fas fa-sync-alt mr-1"></i> En Proceso
                    </span>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="text-4xl font-bold text-rose-600 mb-2">
                            <?= number_format($data['revisiones_pendientes']) ?>
                        </div>
                        <p class="text-gray-500">Entregas pendientes de revisión</p>
                    </div>
                    <div class="text-center">
                        <a href="/timeControl/public/revisiones"
                            class="inline-flex items-center px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i> Ver Revisiones Pendientes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Resumen de Entregas mejorado -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-8">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-5 flex justify-between items-center">
                    <h2 class="text-xl font-bold flex items-center">
                        <i class="fas fa-clipboard-list mr-3"></i> Resumen de Entregas
                    </h2>
                    <span class="bg-blue-500 px-3 py-1 rounded-full text-xs flex items-center">
                        <i class="fas fa-history mr-1"></i> Últimas validaciones
                    </span>
                </div>
                <div class="p-4 overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left text-sm">
                                <?php
                                $headers = [
                                    ['icon' => 'cogs', 'label' => 'Máquina'],
                                    ['icon' => 'tag', 'label' => 'Item'],
                                    ['icon' => 'file-alt', 'label' => 'JT/WO'],
                                    ['icon' => 'info-circle', 'label' => 'Tipo'],
                                    ['icon' => 'cubes', 'label' => 'Cantidad'],
                                    ['icon' => 'calendar-check', 'label' => 'Estado'],
                                ];
                                foreach ($headers as $h) {
                                    echo "<th class='px-4 py-3 border-b-2 border-gray-200'><i class='fas fa-{$h['icon']} text-blue-600 mr-2'></i>{$h['label']}</th>";
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $entregas = array_slice($data['entregas_validadas'], 0, 10); // Mostrar solo 10
                            if (!empty($entregas)) :
                                foreach ($entregas as $delivery) : ?>
                                    <tr class="hover:bg-blue-50 border-b border-gray-100 text-sm transition-colors duration-200">
                                        <td class="px-4 py-3"><?= htmlspecialchars($delivery['nombre_maquina']) ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($delivery['item']) ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($delivery['jtWo']) ?></td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full
                                <?= $delivery['tipo_boton'] == 'final_produccion'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800' ?>">
                                                <i class="fas <?= $delivery['tipo_boton'] == 'final_produccion' ? 'fa-flag-checkered' : 'fa-hourglass-half' ?> mr-1"></i>
                                                <?= $delivery['tipo_boton'] == 'final_produccion' ? 'Final' : 'Parcial' ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-medium"><?= number_format($delivery['cantidad_produccion'], 2) ?> lb.</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                                <i class="fas fa-check-circle mr-1"></i> Validado
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                            No hay entregas validadas registradas.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        // Notificaciones
        document.addEventListener("DOMContentLoaded", function() {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 3000
            };

            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.message) {
                        toastr[data.status](data.message);
                    }
                })
                .catch(console.error);
        });
    </script>
</body>

</html>