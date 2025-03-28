<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel QA - Entregas de Producción</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .table-hover tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
            transition: background-color 0.3s ease;
        }
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #3B82F6;
            border-radius: 3px;
        }
    </style>
</head>
<body class="bg-gray-100 flex">
    <!-- Sidebar Navigation -->
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <!-- Main Content -->
    <div class="ml-64 flex-grow">
        <div class="container mx-auto px-6 py-8">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h1 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-clipboard-list mr-3"></i>Entregas de Producción
                    </h1>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span class="font-semibold">Total Entregas: <?php echo count($data['entregas_validadas']); ?></span>
                    </div>
                </div>

                <?php if (empty($data['entregas_validadas'])): ?>
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-box-open text-6xl mb-4"></i>
                        <p class="text-xl">No hay entregas de producción registradas</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full table-hover">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Máquina</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">JT WO</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Empleado</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Producción</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Scrap</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($data['entregas_validadas'] as $entrega): ?>
                                    <tr class="hover:bg-blue-50 transition-colors duration-200">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-700">
                                                <?php echo htmlspecialchars($entrega['item']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($entrega['jtWo']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-700">
                                                <?php echo htmlspecialchars($entrega['nombre_empleado']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <?php echo $entrega['cantidad_produccion']; ?> Lb
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <?php echo $entrega['cantidad_scrapt']; ?> Lb
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('d/m/Y H:i', strtotime($entrega['fecha_validacion'])); ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="#" class="text-blue-600 hover:text-blue-900 transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="text-green-600 hover:text-green-900 transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
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
</body>
</html>