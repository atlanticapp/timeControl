<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Producción Guardada">
    <title>Control de Calidad - Producción Guardada</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body class="bg-gray-50">
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 p-6 md:p-8 transition-all duration-300 min-h-screen bg-gray-50">
        <div class="container mx-auto pt-14 lg:pt-4">
            <!-- Header Section -->
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-5">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                        <div class="mb-4 md:mb-0">
                            <!-- Breadcrumb -->
                            <nav class="text-gray-500 mb-2" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    <li><a href="/timeControl/public/qa" class="hover:text-green-600">Inicio</a></li>
                                    <li class="flex items-center">
                                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                                        <span class="text-gray-700">Producción Guardada</span>
                                    </li>
                                </ol>
                            </nav>
                            <!-- Título -->
                            <h1 class="text-2xl font-bold text-green-600 flex items-center">
                                <i class="fas fa-box-archive mr-3"></i>Producción Guardada
                            </h1>
                            <p class="text-gray-500 mt-1">Registro de Producción Validada y Guardada</p>
                        </div>
                        <!-- Contador de Registros -->
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-layer-group mr-2"></i>
                                <span class="font-semibold">Total Registros: <?php echo count($data['produccion'] ?? []); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-4 px-5 flex items-center">
                    <i class="fas fa-box-archive mr-3 text-xl"></i>
                    <h3 class="text-lg font-bold">Registro de Producción Guardada</h3>
                </div>

                <div>
                    <?php if (empty($data['produccion'])): ?>
                        <div class="bg-green-50 border-l-4 border-green-600 p-4 m-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-green-600 text-xl mr-3"></i>
                                <span class="text-green-700">No hay registros de producción guardada.</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 text-left text-gray-600 text-sm">
                                    <tr>
                                        <th class="px-4 py-3 font-medium"><i class="fas fa-calendar-alt text-green-600 mr-2"></i> Fecha/Hora</th>
                                        <th class="px-4 py-3 font-medium"><i class="fas fa-cogs text-green-600 mr-2"></i> Máquina</th>
                                        <th class="px-4 py-3 font-medium"><i class="fas fa-tag text-green-600 mr-2"></i> Item</th>
                                        <th class="px-4 py-3 font-medium"><i class="fas fa-file-alt text-green-600 mr-2"></i> JT/WO</th>
                                        <th class="px-4 py-3 font-medium text-right"><i class="fas fa-cubes text-green-600 mr-2"></i> Cantidad</th>
                                        <th class="px-4 py-3 font-medium"><i class="fas fa-comment text-green-600 mr-2"></i> Comentario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['produccion'] as $item): ?>
                                        <tr class="hover:bg-green-50 border-b border-gray-100">
                                            <td class="px-4 py-3 text-sm">
                                                <?= date('d/m/Y H:i', strtotime($item['fecha_validacion'])) ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?= htmlspecialchars($item['nombre_maquina'] ?? 'N/A') ?>
                                            </td>
                                            <td class="px-4 py-3 font-medium">
                                                <?= htmlspecialchars($item['item']) ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <?= htmlspecialchars($item['jtWo']) ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <?= number_format($item['cantidad_produccion'], 2, '.', ',') ?> Lb
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                <?= htmlspecialchars($item['comentario'] ?: 'Sin comentarios') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Toasts Container -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</body>

</html>