<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control QA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
        }

        .gradient-success {
            background: linear-gradient(135deg, #10B981 0%, #047857 100%);
        }

        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex">
        <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

        <!-- Main Content -->
        <div class="ml-64 w-full p-6">
            <h1 class="text-3xl font-bold mb-6 text-gray-800"><?php echo $data['title']; ?></h1>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <?php
                // Establecer valores predeterminados para evitar problemas con null
                $deliveryStats = [
                    'total' => $data['stats']['pendientes'] ?? 0,
                    'production' => $data['stats']['produccion'] ?? 0,
                    'scrap' => $data['stats']['scrap'] ?? 0,
                    'validated' => $data['stats']['validadas'] ?? 0,
                    'in_process' => $data['stats']['en_proceso'] ?? 0
                ];

                // Función para calcular porcentaje de producción y scrap
                function calculatePercentage($value, $total)
                {
                    return $total > 0 ? number_format(($value / $total) * 100, 2) : 0;
                }

                // Calcular los porcentajes
                $productionPercentage = calculatePercentage($deliveryStats['production'], $deliveryStats['total']);
                $scrapPercentage = calculatePercentage($deliveryStats['scrap'], $deliveryStats['total']);
                ?>

                <!-- Tarjeta: Validación de Entregas -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden stat-card">
                    <div class="gradient-primary text-white p-5">
                        <h3 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-box-open mr-3"></i> Validación de Entregas
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4 mb-4 text-center">
                            <div>
                                <div class="text-4xl font-bold text-blue-600"><?= number_format($deliveryStats['total']) ?></div>
                                <div class="text-sm text-gray-500">Pendientes</div>
                            </div>
                            <div>
                                <div class="text-4xl font-bold text-green-600"><?= number_format($deliveryStats['production']) ?></div>
                                <div class="text-sm text-gray-500">Producción</div>
                            </div>
                            <div>
                                <div class="text-4xl font-bold text-red-600"><?= number_format($deliveryStats['scrap']) ?></div>
                                <div class="text-sm text-gray-500">Scrap</div>
                            </div>
                        </div>

                        <!-- Barras de Progreso Dobles -->
                        <div class="mb-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Producción: <?= $productionPercentage ?>%</span>
                                <span>Scrap: <?= $scrapPercentage ?>%</span>
                            </div>
                            <div class="w-full flex h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="bg-green-500" style="width: <?= $productionPercentage ?>%"></div>
                                <div class="bg-red-500" style="width: <?= $scrapPercentage ?>%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4 text-center">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600"><?= number_format($deliveryStats['validated']) ?></div>
                                <div class="text-sm text-gray-500">Validadas</div>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600"><?= number_format($deliveryStats['in_process']) ?></div>
                                <div class="text-sm text-gray-500">En Proceso</div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between items-center">
                            <a href="/timeControl/public/validacion"
                                class="btn btn-primary bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                                Ver Detalles
                            </a>
                            <div class="text-sm text-gray-500 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Actualizado: <?= date('H:i') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta: En Espera por Acción QA -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden stat-card">
                    <div class="gradient-success text-white p-5">
                        <h3 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-tasks mr-3"></i> En Espera por Acción QA
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <div class="text-5xl font-bold text-green-600"><?= number_format($deliveryStats['validated']) ?></div>
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
                    </div>
                </div>
            </div>



            <!-- Recent Validations Table (Previous implementation) -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="bg-gray-800 text-white p-5 rounded-t-xl">
                    <h3 class="text-xl font-semibold">Validaciones Recientes</h3>
                </div>
                <div class="p-6">
                    <!-- Table remains the same as in previous version -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <?php
                                    $headers = [
                                        'Operador',
                                        'Máquina',
                                        'Item',
                                        'JT/WO',
                                        'Producción',
                                        'Scrapt',
                                        'Fecha Validación'
                                    ];
                                    foreach ($headers as $header): ?>
                                        <th class="px-6 py-3"><?= $header ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['validaciones_recientes']) && !empty($data['validaciones_recientes'])): ?>
                                    <?php foreach ($data['validaciones_recientes'] as $validacion): ?>
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4"><?= htmlspecialchars($validacion['nombre_empleado']) ?></td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($validacion['nombre_maquina']) ?></td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($validacion['item']) ?></td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($validacion['jtWo']) ?></td>
                                            <td class="px-6 py-4"><?= number_format($validacion['total_produccion'], 2) ?></td>
                                            <td class="px-6 py-4"><?= number_format($validacion['total_scrapt'], 2) ?></td>
                                            <td class="px-6 py-4"><?= date('d/m/Y H:i', strtotime($validacion['fecha_validacion'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-gray-500">No hay validaciones recientes</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="<?= __DIR__ ?>/qa/historial" class="btn btn-secondary">Ver Historial Completo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>