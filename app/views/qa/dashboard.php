<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control QA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 font-sans text-gray-800">
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-64 p-4 md:p-6 transition-all duration-300 min-h-screen">
        <div class="container mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl md:text-3xl font-bold">
                    <i class="fas fa-tachometer-alt mr-3 text-blue-500"></i>Panel de Control QA
                </h1>
                <div class="text-sm text-gray-500 flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    <span id="current-date"><?= date('d/m/Y H:i:s') ?></span>
                </div>
            </div>

            <?php
            $deliveryStats = [
                'total' => $data['stats']['pendientes'] ?? 0,
                'production' => $data['stats']['produccion_pendiente'] ?? 0,
                'scrap' => $data['stats']['scrap_pendientes'] ?? 0,
                'validated' => $data['stats']['validadas'] ?? 0,
                'in_process' => $data['stats']['en_proceso'] ?? 0
            ];
            function calculatePercentage($value, $total)
            {
                return $total > 0 ? number_format(($value / $total) * 100, 2) : 0;
            }
            $productionPercent = calculatePercentage($deliveryStats['production'], $deliveryStats['total']);
            $scrapPercent = calculatePercentage($deliveryStats['scrap'], $deliveryStats['total']);
            ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Validación de Entregas -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-blue-600 text-white p-5 flex justify-between items-center">
                        <h3 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-box-open mr-3"></i> Validación de Entregas
                        </h3>
                        <span class="bg-blue-500 px-3 py-1 rounded-full text-xs flex items-center">
                            <i class="fas fa-sync-alt mr-1"></i> Tiempo real
                        </span>
                    </div>
                    <div class="p-5 space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                            <?php
                            $statBoxes = [
                                ['label' => 'Pendientes', 'color' => 'blue', 'value' => $deliveryStats['total']],
                                ['label' => 'Producción', 'color' => 'green', 'value' => $deliveryStats['production']],
                                ['label' => 'Scrap', 'color' => 'red', 'value' => $deliveryStats['scrap']],
                            ];
                            foreach ($statBoxes as $box) :
                            ?>
                                <div class="bg-<?= $box['color'] ?>-50 p-4 rounded-lg shadow">
                                    <div class="text-sm text-<?= $box['color'] ?>-500 mb-1"><?= $box['label'] ?></div>
                                    <div class="text-3xl md:text-4xl font-bold text-<?= $box['color'] ?>-600">
                                        <?= number_format($box['value']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div>
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
                            <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden flex">
                                <div class="bg-green-500 transition-all duration-500" style="width: <?= $productionPercent ?>%"></div>
                                <div class="bg-red-500 transition-all duration-500" style="width: <?= $scrapPercent ?>%"></div>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <a href="/timeControl/public/validacion" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-all flex items-center w-full justify-center">
                                <i class="fas fa-search-plus mr-2"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>

                <!-- En Espera por Acción QA -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-green-600 text-white p-5 flex justify-between items-center">
                        <h3 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-tasks mr-3"></i> En Espera por Acción QA
                        </h3>
                        <span class="bg-green-500 px-3 py-1 rounded-full text-xs flex items-center">
                            <i class="fas fa-check-circle mr-1"></i> Activo
                        </span>
                    </div>
                    <div class="p-5 space-y-6">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-4xl md:text-5xl font-bold text-green-600 animate-pulse" id="total_validadas">
                                <?= number_format($deliveryStats['validated']) ?>
                            </div>
                            <div class="text-sm text-gray-600 mt-2">Entregas Validadas</div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-yellow-700 flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Entregas en Proceso
                                </span>
                                <span class="font-bold text-yellow-800 text-lg">
                                    <?= number_format($deliveryStats['in_process']) ?>
                                </span>
                            </div>
                        </div>

                        <a href="/timeControl/public/accion" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-all flex items-center w-full justify-center">
                            <i class="fas fa-eye mr-2"></i> Ver Entregas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Resumen de Entregas -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                <div class="bg-blue-600 text-white p-5">
                    <h2 class="text-xl font-bold flex items-center">
                        <i class="fas fa-clipboard-list mr-3"></i> Resumen de Entregas
                    </h2>
                </div>
                <div class="p-5 overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left text-sm">
                                <?php
                                $headers = [
                                    ['icon' => 'tag', 'label' => 'Item'],
                                    ['icon' => 'file-alt', 'label' => 'JT/WO'],
                                    ['icon' => 'cogs', 'label' => 'Máquina'],
                                    ['icon' => 'info-circle', 'label' => 'Tipo'],
                                    ['icon' => 'cubes', 'label' => 'Cantidad'],
                                    ['icon' => 'calendar-check', 'label' => 'Estado'],
                                ];
                                foreach ($headers as $h) {
                                    echo "<th class='px-4 py-3 border-b-2 border-gray-200'><i class='fas fa-{$h['icon']} text-blue-500 mr-2'></i>{$h['label']}</th>";
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['entregas_validadas'] as $delivery) : ?>
                                <tr class="hover:bg-gray-50 border-b border-gray-100 text-sm">
                                    <td class="px-4 py-3"><?= htmlspecialchars($delivery['item']) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($delivery['jtWo']) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($delivery['maquina']) ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full
                                            <?= $delivery['tipo_boton'] == 'final_produccion'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-yellow-100 text-yellow-800' ?>">
                                            <i class="fas <?= $delivery['tipo_boton'] == 'final_produccion' ? 'fa-flag-checkered' : 'fa-hourglass-half' ?> mr-1"></i>
                                            <?= $delivery['tipo_boton'] == 'final_produccion' ? 'Final' : 'Parcial' ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-medium"><?= number_format($delivery['cantidad_produccion'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-check-circle mr-1"></i> Validado
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($data['entregas_validadas'])) : ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">No hay entregas validadas registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Actualizar la fecha y hora actual
        function updateDateTime() {
            const now = new Date();
            const options = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            document.getElementById('current-date').textContent = now.toLocaleDateString('es-ES', options).replace(',', '');
        }

        // Actualizar cada segundo
        setInterval(updateDateTime, 1000);
    </script>
</body>

</html>