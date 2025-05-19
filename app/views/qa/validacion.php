<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Constructor QA">
    <title>Control de Calidad - Constructor(QA)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body class="bg-gray-50">
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 p-4 md:p-6 transition-all duration-300 min-h-screen bg-gray-50">
        <div class="container mx-auto pt-14 lg:pt-4">
            <!-- Header Section -->
            <div class="bg-white rounded-xl shadow-md mb-6">
                <div class="p-5">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                        <div class="mb-4 md:mb-0">
                            <!-- Breadcrumb -->
                            <nav class="text-gray-500 mb-2" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    <li><a href="/timeControl/public/qa" class="hover:text-teal-600 transition-colors">Inicio</a></li>
                                    <li class="flex items-center">
                                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                                        <span class="text-gray-700">Validación de Entregas</span>
                                    </li>
                                </ol>
                            </nav>
                            <!-- Título -->
                            <h1 class="text-2xl font-bold text-teal-600 flex items-center">
                                <i class="fas fa-check-circle mr-3"></i>Validación de Entregas
                            </h1>
                            <p class="text-gray-500 mt-1">Control y Validación de Entregas de Producción y Scrap</p>
                        </div>
                        <!-- Contador de Entregas -->
                        <div class="flex items-center">
                            <div class="bg-teal-100 text-teal-800 px-4 py-2 rounded-lg flex items-center shadow-sm">
                                <i class="fas fa-layer-group mr-2"></i>
                                <span class="font-semibold">Entregas Pendientes: <span id="pending-counter"><?php echo count($data['entregas_produccion'] ?? []) + count($data['entregas_scrap'] ?? []); ?></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total de Correcciones -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Entregas Pendientes</p>
                            <h3 class="text-2xl font-bold text-green-600 mt-1">
                                <?= $data['stats']['pendientes'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-clipboard-list text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Correcciones de Producción -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Producción</p>
                            <h3 class="text-2xl font-bold text-green-600 mt-1">
                                <?= $data['stats']['produccion_pendiente'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-industry text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Correcciones de Scrap -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Scrap</p>
                            <h3 class="text-2xl font-bold text-red-600 mt-1">
                                <?= $data['stats']['scrap_pendiente'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-trash-alt text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros de búsqueda -->
            <div class="bg-white rounded-xl shadow-md p-4 mb-6 flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-gray-600 text-sm mb-1" for="filtroFecha">Fecha</label>
                    <input type="date" id="filtroFecha" class="border border-gray-300 rounded px-2 py-1 w-40" />
                </div>
                <!-- Se elimina el filtro de máquina -->
                <div>
                    <label class="block text-gray-600 text-sm mb-1" for="filtroItem">Item</label>
                    <input type="text" id="filtroItem" class="border border-gray-300 rounded px-2 py-1 w-40" placeholder="Item" />
                </div>
                <div>
                    <label class="block text-gray-600 text-sm mb-1" for="filtroJtWo">JT/WO</label>
                    <input type="text" id="filtroJtWo" class="border border-gray-300 rounded px-2 py-1 w-40" placeholder="JT/WO" />
                </div>
                <div>
                    <label class="block text-gray-600 text-sm mb-1" for="filtroPO">PO</label>
                    <input type="text" id="filtroPO" class="border border-gray-300 rounded px-2 py-1 w-40" placeholder="PO" />
                </div>
                <div>
                    <label class="block text-gray-600 text-sm mb-1" for="filtroCliente">Cliente</label>
                    <input type="text" id="filtroCliente" class="border border-gray-300 rounded px-2 py-1 w-40" placeholder="Cliente" />
                </div>
                <div>
                    <button id="btnLimpiarFiltros" class="ml-2 px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-gray-700">Limpiar</button>
                </div>
            </div>

            <!-- Lista de entregas pendientes -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-teal-600 to-teal-700 text-white py-4 px-5 flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-check mr-3 text-xl"></i>
                        <h3 class="text-lg font-bold">Entregas Pendientes de Validación</h3>
                    </div>
                </div>

                <?php
                // Agrupar entregas por máquina
                $maquinas = [];
                foreach ($data['entregas_produccion'] as $entrega) {
                    $maquina = $entrega['nombre_maquina'];
                    if (!isset($maquinas[$maquina])) $maquinas[$maquina] = [];
                    $key = $entrega['fecha_registro'] . '_' . $entrega['jtWo'] . '_' . $entrega['item'];
                    if (!isset($maquinas[$maquina][$key])) {
                        $maquinas[$maquina][$key] = [
                            'fecha_registro' => $entrega['fecha_registro'],
                            'item' => $entrega['item'],
                            'jtWo' => $entrega['jtWo'],
                            'po' => $entrega['po'] ?? '',
                            'cliente' => $entrega['cliente'] ?? '',
                            'tipo_boton' => $entrega['tipo_boton'],
                            'entregas' => []
                        ];
                    }
                    $maquinas[$maquina][$key]['entregas'][] = [
                        'id' => $entrega['id'],
                        'tipo' => 'produccion',
                        'cantidad' => $entrega['cantidad_produccion']
                    ];
                }
                foreach ($data['entregas_scrap'] as $entrega) {
                    $maquina = $entrega['nombre_maquina'];
                    if (!isset($maquinas[$maquina])) $maquinas[$maquina] = [];
                    $key = $entrega['fecha_registro'] . '_' . $entrega['jtWo'] . '_' . $entrega['item'];
                    if (!isset($maquinas[$maquina][$key])) {
                        $maquinas[$maquina][$key] = [
                            'fecha_registro' => $entrega['fecha_registro'],
                            'item' => $entrega['item'],
                            'jtWo' => $entrega['jtWo'],
                            'po' => $entrega['po'] ?? '',
                            'cliente' => $entrega['cliente'] ?? '',
                            'tipo_boton' => $entrega['tipo_boton'],
                            'entregas' => []
                        ];
                    }
                    $maquinas[$maquina][$key]['entregas'][] = [
                        'id' => $entrega['id'],
                        'tipo' => 'scrap',
                        'cantidad' => $entrega['cantidad_scrapt']
                    ];
                }
                ?>

                <?php if (empty($maquinas)): ?>
                    <div class="flex flex-col items-center justify-center p-12 bg-gray-50">
                        <div class="text-teal-600 mb-4">
                            <i class="fas fa-check-circle text-5xl"></i>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-700 mb-2">¡Todo al día!</h4>
                        <p class="text-gray-500 text-center max-w-md">
                            No hay entregas pendientes de validación en este momento.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <?php foreach ($maquinas as $nombre_maquina => $entregas): ?>
                            <div class="border-b border-gray-200">
                                <div class="bg-gray-100 px-4 py-2 font-bold text-teal-700 flex items-center text-base">
                                    <i class="fas fa-cogs mr-2"></i> <?= htmlspecialchars($nombre_maquina) ?>
                                </div>
                                <table class="w-full" id="tablaEntregas">
                                    <thead class="bg-gray-50 text-left text-gray-600 text-sm">
                                        <tr>
                                            <th class="px-4 py-3 font-medium"><i class="fas fa-calendar-alt text-teal-600 mr-2"></i> Fecha/Hora</th>
                                            <th class="px-4 py-3 font-medium"><i class="fas fa-tag text-teal-600 mr-2"></i> Item</th>
                                            <th class="px-4 py-3 font-medium"><i class="fas fa-file-alt text-teal-600 mr-2"></i> JT/WO</th>
                                            <th class="px-4 py-3 font-medium"><i class="fas fa-barcode text-teal-600 mr-2"></i> PO</th>
                                            <th class="px-4 py-3 font-medium"><i class="fas fa-user text-teal-600 mr-2"></i> Cliente</th>
                                            <th class="px-4 py-3 font-medium"><i class="fas fa-info-circle text-teal-600 mr-2"></i> Tipo</th>
                                            <th class="px-4 py-3 font-medium"><i class="fas fa-cubes text-teal-600 mr-2"></i> Detalle</th>
                                            <th class="px-4 py-3 font-medium text-center"><i class="fas fa-tools text-teal-600 mr-2"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($entregas as $entrega): ?>
                                            <tr class="hover:bg-gray-50/50 border-b border-gray-100 entrega-row"
                                                data-fecha="<?= date('Y-m-d', strtotime($entrega['fecha_registro'])) ?>"
                                                data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>"
                                            >
                                                <td class="px-4 py-3">
                                                    <div class="text-sm">
                                                        <div class="font-medium"><?= date('d/m/Y', strtotime($entrega['fecha_registro'])) ?></div>
                                                        <div class="text-gray-500"><?= date('H:i', strtotime($entrega['fecha_registro'])) ?></div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($entrega['item']) ?></td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($entrega['po']) ?></td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($entrega['cliente']) ?></td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold 
                        <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                                        <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'Final' : 'Parcial' ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="space-y-2">
                                                        <?php foreach ($entrega['entregas'] as $detalle): ?>
                                                            <div class="flex items-center justify-between py-1.5 px-3 rounded-md <?= $detalle['tipo'] == 'scrap' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' ?>">
                                                                <span class="font-medium">
                                                                    <?= ucfirst($detalle['tipo']) ?>
                                                                </span>
                                                                <span class="font-bold">
                                                                    <?= number_format($detalle['cantidad'], 2) ?> lb.
                                                                </span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <?php foreach ($entrega['entregas'] as $index => $detalle): ?>
                                                        <div class="flex space-x-2 <?= $index > 0 ? 'mt-2' : '' ?>">
                                                            <button class="btn-review inline-flex items-center px-2.5 py-1.5 border border-blue-600 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition-colors duration-200 text-sm w-full justify-center"
                                                                data-id="<?= $detalle['id'] ?>"
                                                                data-tipo="<?= $detalle['tipo'] ?>"
                                                                data-cantidad="<?= $detalle['cantidad'] ?>"
                                                                data-maquina="<?= htmlspecialchars($nombre_maquina) ?>"
                                                                data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                                data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                                data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                                data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                                                <i class="fas fa-search mr-1"></i>Revisar
                                                            </button>
                                                            <button class="<?= $detalle['tipo'] == 'scrap' ? 'btn-validate-scrap' : 'btn-validate-production' ?> 
                                    inline-flex items-center px-2.5 py-1.5 border border-green-600 text-green-600 rounded 
                                    hover:bg-green-600 hover:text-white transition-colors duration-200 text-sm w-full justify-center"
                                                                data-id="<?= $detalle['id'] ?>"
                                                                data-tipo="<?= $detalle['tipo'] ?>"
                                                                data-cantidad="<?= $detalle['cantidad'] ?>"
                                                                data-maquina="<?= htmlspecialchars($nombre_maquina) ?>"
                                                                data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                                data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                                data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                                data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                                                <i class="fas fa-check mr-1"></i>Validar
                                                            </button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Review Modal -->
        <div id="revisionModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden backdrop-blur-sm transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all" role="dialog" aria-labelledby="revisionModalTitle" aria-modal="true">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h2 id="revisionModalTitle" class="text-lg font-bold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Revisar Entrega
                    </h2>
                    <button type="button" class="modal-close focus:outline-none text-white hover:text-gray-200 transition-colors" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Información de la entrega -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-5 shadow-sm">
                        <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Detalles de la entrega:
                        </h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                <span class="font-medium text-blue-600">Máquina:</span>
                                <span id="revisionMaquina" class="ml-1"></span>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                <span class="font-medium text-blue-600">Item:</span>
                                <span id="revisionItem" class="ml-1"></span>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                <span class="font-medium text-blue-600">JT/WO:</span>
                                <span id="revisionJtWo" class="ml-1"></span>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                <span class="font-medium text-blue-600">Cantidad:</span>
                                <span id="revisionCantidad" class="ml-1"></span>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100 col-span-2">
                                <span class="font-medium text-blue-600">Tipo:</span>
                                <span id="revisionTipo" class="ml-1"></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-5 rounded-r flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>Especifique un motivo opcional para revisar la cantidad reportada.</div>
                    </div>

                    <div class="mb-5">
                        <label for="notaRevision" class="block text-gray-700 font-medium mb-2">Motivo de corrección (opcional)</label>
                        <textarea class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow"
                            id="notaRevision"
                            rows="3"
                            placeholder="Escriba aquí sus observaciones sobre la cantidad..."
                            aria-describedby="notaRevisionHelp"></textarea>
                        <p id="notaRevisionHelp" class="text-xs text-gray-500 mt-1">Su comentario ayudará a entender el motivo de la revisión.</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" class="modal-close px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors flex items-center focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </button>
                        <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center focus:outline-none focus:ring-2 focus:ring-blue-500" id="submitRevisionBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Enviar Revisión
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation Modal -->
        <div id="validateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden backdrop-blur-sm transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all" role="dialog" aria-labelledby="validateModalTitle" aria-modal="true">
                <div class="bg-green-600 text-white px-6 py-4 flex justify-between items-center">
                    <h2 id="validateModalTitle" class="text-lg font-bold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Validar Entrega
                    </h2>
                    <button type="button" class="modal-close focus:outline-none text-white hover:text-gray-200 transition-colors" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Información de la entrega -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-5 shadow-sm">
                        <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Detalles de la entrega a validar:
                        </h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                <span class="font-medium text-green-600">Máquina:</span>
                                <span id="validacionMaquina" class="ml-1"></span>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                <span class="font-medium text-green-600">Item:</span>
                                <span id="validacionItem" class="ml-1"></span>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                <span class="font-medium text-green-600">JT/WO:</span>
                                <span id="validacionJtWo" class="ml-1"></span>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100">
                                <span class="font-medium text-green-600">Cantidad:</span>
                                <span id="validacionCantidad" class="ml-1"></span>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border border-gray-100 col-span-2">
                                <span class="font-medium text-green-600">Tipo:</span>
                                <span id="validacionTipo" class="ml-1"></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-5 rounded-r flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <div>Al validar esta entrega, se registrará como completa en el sistema.</div>
                    </div>

                    <div class="mb-5">
                        <label for="comentarioValidacion" class="block text-gray-700 font-medium mb-2">Comentario (opcional)</label>
                        <textarea class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-shadow"
                            id="comentarioValidacion"
                            data-id="data-comentario"
                            rows="3"
                            placeholder="Escriba aquí sus observaciones sobre la entrega..."
                            aria-describedby="comentarioValidacionHelp"></textarea>
                        <p id="comentarioValidacionHelp" class="text-xs text-gray-500 mt-1">Su comentario quedará registrado con la validación.</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" class="modal-close px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors flex items-center focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </button>
                        <button type="button" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center focus:outline-none focus:ring-2 focus:ring-green-500" id="submitValidation">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Validar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toasts Container -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/appValidacion.js"></script>
    <script>
        document.addEventListener('newDelivery', (e) => {
            // Opción 1: Recargar inmediatamente
            // window.location.reload();

            // Opción 2: Mostrar notificación con opción de recargar
            toastr.info('Nueva entrega detectada', 'Hay nuevos registros disponibles', {
                timeOut: 5000,
                extendedTimeOut: 1000,
                closeButton: true,
                tapToDismiss: false,
                onclick: () => window.location.reload()
            });
        });
    </script>
    <script>
$(document).ready(function () {
    function filtrar() {
        let fecha = $('#filtroFecha').val();
        let item = $('#filtroItem').val().toLowerCase();
        let jtwo = $('#filtroJtWo').val().toLowerCase();
        let po = $('#filtroPO').val().toLowerCase();
        let cliente = $('#filtroCliente').val().toLowerCase();

        $('.entrega-row').each(function () {
            let $row = $(this);
            let match = true;
            if (fecha && $row.data('fecha') !== fecha) match = false;
            if (item && !$row.data('item').toLowerCase().includes(item)) match = false;
            if (jtwo && !$row.data('jtwo').toLowerCase().includes(jtwo)) match = false;
            if (po && !$row.data('po').toLowerCase().includes(po)) match = false;
            if (cliente && !$row.data('cliente').toLowerCase().includes(cliente)) match = false;
            $row.toggle(match);
        });
    }

    $('#filtroFecha, #filtroItem, #filtroJtWo, #filtroPO, #filtroCliente').on('input change', filtrar);
    $('#btnLimpiarFiltros').on('click', function (e) {
        e.preventDefault();
        $('#filtroFecha, #filtroItem, #filtroJtWo, #filtroPO, #filtroCliente').val('');
        filtrar();
    });
});
</script>
</body>

</html>