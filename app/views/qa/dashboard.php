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
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panel de Control QA</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="bg-gray-50">
        <main class="lg:ml-72 p-4 md:p-6 transition-all duration-300 min-h-screen">
            <div class="max-w-7xl mx-auto pt-16 lg:pt-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>Panel de Control QA
                    </h1>
                    <div class="flex flex-wrap gap-2">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center shadow-md transition-all">
                            <i class="fas fa-sync-alt mr-2"></i> Actualizar datos
                        </button>
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium flex items-center shadow-md transition-all">
                            <i class="fas fa-filter mr-2"></i> Filtros
                        </button>
                    </div>
                </div>

                <?php
                $deliveryStats = [
                    'total' => $data['stats']['pendientes'] ?? 0,
                    'production' => $data['stats']['produccion_pendiente'] ?? 0,
                    'scrap' => $data['stats']['scrap_pendiente'] ?? 0,
                    'validated' => $data['stats']['validadas'] ?? 0,
                    'retenciones' => $data['stats']['retenciones'] ?? 0
                ];
                function calculatePercentage($value, $total)
                {
                    return $total > 0 ? number_format(($value / $total) * 100, 2) : 0;
                }
                $productionPercent = calculatePercentage($deliveryStats['production'], $deliveryStats['total']);
                $scrapPercent = calculatePercentage($deliveryStats['scrap'], $deliveryStats['total']);
                ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
                    <!-- Validación de Entregas Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-teal-600 to-teal-700 text-white p-4 flex justify-between items-center">
                            <h3 class="text-lg md:text-xl font-semibold flex items-center">
                                <i class="fas fa-check-circle mr-2"></i> Validación de Entregas
                            </h3>
                            <span class="bg-teal-500 px-2 py-1 rounded-full text-xs flex items-center shadow-inner">
                                <i class="fas fa-sync-alt mr-1"></i> Tiempo real
                            </span>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-3 gap-3 text-center">
                                <!-- Pendientes Box -->
                                <div class="bg-teal-50 p-3 rounded-lg shadow-sm hover:shadow transition-shadow">
                                    <div class="flex justify-center mb-1">
                                        <i class="fas fa-clipboard-list text-teal-500 text-lg"></i>
                                    </div>
                                    <div class="text-xs md:text-sm text-teal-600 font-medium mb-1">Pendientes</div>
                                    <div class="text-xl md:text-2xl font-bold text-teal-700">
                                        <?= number_format($deliveryStats['total']) ?>
                                    </div>
                                </div>

                                <!-- Producción Box -->
                                <div class="bg-green-50 p-3 rounded-lg shadow-sm hover:shadow transition-shadow">
                                    <div class="flex justify-center mb-1">
                                        <i class="fas fa-industry text-green-500 text-lg"></i>
                                    </div>
                                    <div class="text-xs md:text-sm text-green-600 font-medium mb-1">Producción</div>
                                    <div class="text-xl md:text-2xl font-bold text-green-700">
                                        <?= number_format($deliveryStats['production']) ?>
                                    </div>
                                </div>

                                <!-- Scrap Box -->
                                <div class="bg-red-50 p-3 rounded-lg shadow-sm hover:shadow transition-shadow">
                                    <div class="flex justify-center mb-1">
                                        <i class="fas fa-trash-alt text-red-500 text-lg"></i>
                                    </div>
                                    <div class="text-xs md:text-sm text-red-600 font-medium mb-1">Scrap</div>
                                    <div class="text-xl md:text-2xl font-bold text-red-700">
                                        <?= number_format($deliveryStats['scrap']) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="flex justify-between text-xs text-gray-600 mb-2">
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                        Prod: <?= $productionPercent ?>%
                                    </span>
                                    <span class="flex items-center">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                        Scrap: <?= $scrapPercent ?>%
                                    </span>
                                </div>
                                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden flex">
                                    <div class="bg-green-500 transition-all duration-500" style="width: <?= $productionPercent ?>%"></div>
                                    <div class="bg-red-500 transition-all duration-500" style="width: <?= $scrapPercent ?>%"></div>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <a href="/timeControl/public/validacion" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition-all flex items-center w-full justify-center shadow-sm hover:shadow">
                                    <i class="fas fa-check-circle mr-2"></i> Ir a Validación
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Acción QA Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white p-4 flex justify-between items-center">
                            <h3 class="text-lg md:text-xl font-semibold flex items-center">
                                <i class="fas fa-clipboard-check mr-2"></i> Acción QA
                            </h3>
                            <span class="bg-amber-500 px-2 py-1 rounded-full text-xs flex items-center shadow-inner">
                                <i class="fas fa-sync-alt mr-1"></i> Tiempo real
                            </span>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="text-center">
                                <div class="bg-amber-50 p-5 rounded-lg shadow-sm hover:shadow transition-shadow">
                                    <div class="flex justify-center mb-2">
                                        <i class="fas fa-check-double text-amber-500 text-2xl"></i>
                                    </div>
                                    <div class="text-sm text-amber-600 font-medium mb-1">Validadas</div>
                                    <div class="text-3xl font-bold text-amber-700">
                                        <?= number_format($deliveryStats['validated']) ?>
                                    </div>
                                    <div class="mt-2 text-xs text-amber-600">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        <?= number_format(($deliveryStats['validated'] / max(1, $deliveryStats['total'] + $deliveryStats['validated'])) * 100, 1) ?>% completado
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <a href="/timeControl/public/accion" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg transition-all flex items-center w-full justify-center shadow-sm hover:shadow">
                                    <i class="fas fa-clipboard-check mr-2"></i> Ir a Acción QA
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Retenciones Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 text-white p-4 flex justify-between items-center">
                            <h3 class="text-lg md:text-xl font-semibold flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Retenciones
                            </h3>
                            <span class="bg-yellow-500 px-2 py-1 rounded-full text-xs flex items-center shadow-inner">
                                <i class="fas fa-sync-alt mr-1"></i> Tiempo real
                            </span>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="text-center">
                                <div class="bg-yellow-50 p-5 rounded-lg shadow-sm hover:shadow transition-shadow">
                                    <div class="flex justify-center mb-2">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                                    </div>
                                    <div class="text-sm text-yellow-700 font-medium mb-1">Retenciones Activas</div>
                                    <div class="text-3xl font-bold text-yellow-800">
                                        <?= count($deliveryStats['retenciones']) ?>
                                    </div>
                                    <div class="mt-2 text-xs text-yellow-700">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Requiere atención inmediata
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <a href="/timeControl/public/retenciones" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-all flex items-center w-full justify-center shadow-sm hover:shadow">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> Ver Retenciones
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-route mr-3 text-blue-600"></i>
                        Destinos Post Retención
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3">
                                <h3 class="text-lg font-semibold flex items-center">
                                    <i class="fas fa-box-open mr-2"></i> Producción Final
                                </h3>
                            </div>
                            <div class="p-4">
                                <div class="text-center text-3xl font-bold text-blue-600 mb-1">
                                    <?= number_format($data['destinos']['produccion']['total'] ?? 0) ?>
                                </div>
                                <div class="text-xs text-gray-500 text-center mb-2">Destinos asignados</div>
                                <div class="text-center text-lg font-semibold text-blue-700 mb-3">
                                    <?= number_format($data['destinos']['produccion']['cantidad'] ?? 0, 2) ?> <span class="text-sm">lb.</span>
                                </div>
                                <a href="/timeControl/public/destinos/produccion" class="block text-center bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2 rounded transition-colors">
                                    Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-3">
                                <h3 class="text-lg font-semibold flex items-center">
                                    <i class="fas fa-recycle mr-2"></i> Retrabajo
                                </h3>
                            </div>
                            <div class="p-4">
                                <div class="text-center text-3xl font-bold text-green-600 mb-1">
                                    <?= number_format($data['destinos']['retrabajo']['total'] ?? 0) ?>
                                </div>
                                <div class="text-xs text-gray-500 text-center mb-2">Destinos asignados</div>
                                <div class="text-center text-lg font-semibold text-green-700 mb-3">
                                    <?= number_format($data['destinos']['retrabajo']['cantidad'] ?? 0, 2) ?> <span class="text-sm">lb.</span>
                                </div>
                                <a href="/timeControl/public/destinos/retrabajo" class="block text-center bg-green-50 text-green-600 hover:bg-green-100 px-4 py-2 rounded transition-colors">
                                    Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-3">
                                <h3 class="text-lg font-semibold flex items-center">
                                    <i class="fas fa-trash-alt mr-2"></i> Destrucción
                                </h3>
                            </div>
                            <div class="p-4">
                                <div class="text-center text-3xl font-bold text-red-600 mb-1">
                                    <?= number_format($data['destinos']['destruccion']['total'] ?? 0) ?>
                                </div>
                                <div class="text-xs text-gray-500 text-center mb-2">Destinos asignados</div>
                                <div class="text-center text-lg font-semibold text-red-700 mb-3">
                                    <?= number_format($data['destinos']['destruccion']['cantidad'] ?? 0, 2) ?> <span class="text-sm">lb.</span>
                                </div>
                                <a href="/timeControl/public/destinos/destruccion" class="block text-center bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded transition-colors">
                                    Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Dashboard inferior: Revisiones pendientes y Resumen de entregas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Revisiones Pendientes sección mejorada -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-rose-600 to-rose-700 text-white p-4 flex items-center justify-between">
                            <h3 class="text-lg md:text-xl font-semibold flex items-center">
                                <i class="fas fa-search mr-2"></i> Revisiones Pendientes
                            </h3>
                            <span class="bg-rose-500 px-2 py-1 rounded-full text-xs flex items-center shadow-inner">
                                <i class="fas fa-sync-alt mr-1"></i> En Proceso
                            </span>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-center space-x-8">
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-rose-600 mb-1">
                                        <?= number_format($data['revisiones_pendientes']) ?>
                                    </div>
                                    <p class="text-sm text-gray-500">Pendientes de revisión</p>
                                </div>
                            </div>

                            <div class="text-center mt-6">
                                <a href="/timeControl/public/revisiones" class="inline-flex items-center px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-colors shadow-sm hover:shadow">
                                    <i class="fas fa-search mr-2"></i> Ver Revisiones Pendientes
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Entregas mejorado para mobile primero -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 flex justify-between items-center">
                            <h2 class="text-lg md:text-xl font-semibold flex items-center">
                                <i class="fas fa-clipboard-list mr-2"></i> Últimas Validaciones
                            </h2>
                            <span class="bg-blue-500 px-2 py-1 rounded-full text-xs flex items-center">
                                <i class="fas fa-history mr-1"></i> Recientes
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                        <th class="px-3 py-2 border-b">Máquina</th>
                                        <th class="px-3 py-2 border-b">Item</th>
                                        <th class="px-3 py-2 border-b hidden md:table-cell">JT/WO</th>
                                        <th class="px-3 py-2 border-b">Tipo</th>
                                        <th class="px-3 py-2 border-b text-right">Cant.</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <?php
                                    $entregas = array_slice($data['entregas_validadas'], 0, 5); // Mostrar solo 5 para mejor visualización mobile
                                    if (!empty($entregas)) :
                                        foreach ($entregas as $delivery) : ?>
                                            <tr class="hover:bg-blue-50 text-xs sm:text-sm transition-colors duration-200">
                                                <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-700">
                                                    <?= htmlspecialchars($delivery['nombre_maquina']) ?>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                                                    <?= htmlspecialchars($delivery['item']) ?>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap hidden md:table-cell text-gray-600">
                                                    <?= htmlspecialchars($delivery['jtWo']) ?>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full
                                            <?= $delivery['tipo_boton'] == 'final_produccion'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-yellow-100 text-yellow-800' ?>">
                                                        <i class="fas <?= $delivery['tipo_boton'] == 'final_produccion' ? 'fa-flag-checkered' : 'fa-hourglass-half' ?> mr-1"></i>
                                                        <?= $delivery['tipo_boton'] == 'final_produccion' ? 'Final' : 'Parcial' ?>
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-right font-medium">
                                                    <?= number_format($delivery['cantidad_produccion'], 1) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach;
                                    else : ?>
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                                                    No hay entregas validadas.
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-t bg-gray-50 text-center">
                            <a href="/timeControl/public/accion" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Ver todas las entregas <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer del dashboard -->
                <footer class="mt-8 text-center text-gray-500 text-xs py-6">
                    <p>© <?= date('Y') ?> Panel de Control QA - Todos los derechos reservados</p>
                    <p class="mt-1">Última actualización: <?= date('d/m/Y H:i:s') ?></p>
                </footer>
            </div>
        </main>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script>
            function initDashboard() {
                const progressBars = document.querySelectorAll('[class*="bg-green-500"], [class*="bg-red-500"]');
                progressBars.forEach(bar => {
                    const originalWidth = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = originalWidth;
                    }, 300);
                });
            }

            // Inicializar al cargar
            document.addEventListener('DOMContentLoaded', initDashboard);
        </script>
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