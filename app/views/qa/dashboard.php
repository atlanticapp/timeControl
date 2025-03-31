<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control QA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 font-sans text-gray-800">
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-64 p-6 min-h-screen">
        <h1 class="text-3xl font-bold mb-6">Panel de Control QA</h1>

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
        ?>

        <div class="bg-blue-500 text-white p-5 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold flex items-center">
                <i class="fas fa-box-open mr-3"></i> Validación de Entregas
            </h3>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4 text-center">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-4xl font-bold text-blue-600"> <?= number_format($deliveryStats['total']) ?> </div>
                <div class="text-sm text-gray-500">Pendientes</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-4xl font-bold text-green-600"> <?= number_format($deliveryStats['production']) ?> </div>
                <div class="text-sm text-gray-500">Producción</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-4xl font-bold text-red-600"> <?= number_format($deliveryStats['scrap']) ?> </div>
                <div class="text-sm text-gray-500">Scrap</div>
            </div>
        </div>

        <div class="mt-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Producción: <?= calculatePercentage($deliveryStats['production'], $deliveryStats['total']) ?>%</span>
                <span>Scrap: <?= calculatePercentage($deliveryStats['scrap'], $deliveryStats['total']) ?>%</span>
            </div>
            <div class="w-full flex h-2.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="bg-green-500" style="width: <?= calculatePercentage($deliveryStats['production'], $deliveryStats['total']) ?>%"></div>
                <div class="bg-red-500" style="width: <?= calculatePercentage($deliveryStats['scrap'], $deliveryStats['total']) ?>%"></div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4 text-center">
            <div class="bg-blue-50 p-3 rounded-lg shadow">
                <div class="text-2xl font-bold text-blue-600"> <?= number_format($deliveryStats['validated']) ?> </div>
                <div class="text-sm text-gray-500">Validadas</div>
            </div>
            <div class="bg-yellow-50 p-3 rounded-lg shadow">
                <div class="text-2xl font-bold text-yellow-600"> <?= number_format($deliveryStats['in_process']) ?> </div>
                <div class="text-sm text-gray-500">En Proceso</div>
            </div>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <a href="/timeControl/public/validacion" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                Ver Detalles
            </a>
            <div class="text-sm text-gray-500 flex items-center">
                <i class="fas fa-info-circle mr-2"></i> Actualizado: <?= date('d/m/Y H:i:s') ?>
            </div>
        </div>

        <!-- ini -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden stat-card">
            <div class="gradient-success text-white p-5">
                <h3 class="text-xl font-semibold flex items-center">
                    <i class="fas fa-tasks mr-3"></i> En Espera por Acción QA
                </h3>
            </div>
            <div class="p-6">
                <div class="text-center mb-4">
                    <div class="text-5xl font-bold text-green-600" id="total_validadas"><?= number_format($deliveryStats['validated']) ?></div>
                    <div class="text-sm text-gray-600 mt-2">Entregas Validadas</div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-green-700">Entregas en Proceso</span>
                        <span class="font-bold text-green-800"><?= number_format($deliveryStats['in_process']) ?></span>
                    </div>
                </div>

                <a href="/timeControl/public/accion"
                    class="w-full btn btn-success bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                    Ver Entregas
                </a>
                <!-- Resumen -->
                <div class="mt-6">
                    <h2 class="text-2xl font-bold mb-4">Resumen</h2>
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2">Item</th>
                                    <th class="border border-gray-300 px-4 py-2">JT/WO</th>
                                    <th class="border border-gray-300 px-4 py-2">Máquina</th>
                                    <th class="border border-gray-300 px-4 py-2">Cantidad Producción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['entregas_validadas'] as $delivery) : ?>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($delivery['item']) ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($delivery['jtWo']) ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($delivery['maquina']) ?></td>
                                        <td class="border border-gray-300 px-4 py-2"><?= number_format($delivery['cantidad_produccion'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin -->



    </main>
</body>

</html>