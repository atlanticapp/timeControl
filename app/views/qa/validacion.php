
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Constructor QA">
    <title>Control de Calidad - Constructor(QA)</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/timeControl/public/assets/css/qa/validacion.css">
    


</head>

<body>
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 p-4 md:p-6 transition-all duration-300 min-h-screen">
        <div class="modern-header p-6 mb-6">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="logo-container">
                        <img src="/timeControl/public/assets/img/logoprint.png" alt="Control Tiempos Logo" class="logo-image">
                        <div>
                            <nav class="breadcrumb-modern mb-2" aria-label="Breadcrumb">
                                <a href="/timeControl/public/qa" class="hover:underline">Inicio</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Validación de Entregas</span>
                            </nav>
                            <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-check-circle"></i>
                                Validación de Entregas
                            </h1>
                            <p class="text-white/80 mt-1 text-sm">Control y Validación de Entregas de Producción y Scrap</p>
                        </div>
                    </div>
                    <div class="text-white/90 text-sm">
                        <i class="fas fa-calendar-alt mr-2"></i><?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 md:px-6 pb-8">
            <!-- Estadísticas Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="stat-card fade-in">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Entregas Pendientes</p>
                            <h3 class="text-2xl font-bold" style="color: var(--primary-blue);">
                                <?= $data['stats']['pendientes'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(91, 164, 207, 0.1); color: var(--primary-blue);">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card fade-in">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Producción</p>
                            <h3 class="text-2xl font-bold text-green-600">
                                <?= $data['stats']['produccion_pendiente'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: #D1FAE5; color: #059669;">
                            <i class="fas fa-industry"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card fade-in">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Scrap</p>
                            <h3 class="text-2xl font-bold text-red-600">
                                <?= $data['stats']['scrap_pendiente'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: #FEE2E2; color: #DC2626;">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros de búsqueda -->
            <div class="modern-card p-6 mb-6 fade-in">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-3" style="color: var(--primary-dark);">
                    <i class="fas fa-filter" style="color: var(--primary-blue);"></i>
                    Filtros de Búsqueda
                </h2>
                <div class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroFecha">Fecha</label>
                        <input type="date" id="filtroFecha" class="modern-input" />
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroItem">Item</label>
                        <input type="text" id="filtroItem" class="modern-input" placeholder="Item" />
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroJtWo">JT/WO</label>
                        <input type="text" id="filtroJtWo" class="modern-input" placeholder="JT/WO" />
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroPO">PO</label>
                        <input type="text" id="filtroPO" class="modern-input" placeholder="PO" />
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroCliente">Cliente</label>
                        <input type="text" id="filtroCliente" class="modern-input" placeholder="Cliente" />
                    </div>
                    <div>
                        <button id="btnLimpiarFiltros" class="btn-modern btn-secondary">
                            <i class="fas fa-eraser mr-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de entregas pendientes -->
            <div class="modern-card overflow-hidden fade-in">
                <div class="p-5 modal-header">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-clipboard-check mr-3"></i>Entregas Pendientes de Validación
                    </h3>
                    <div class="badge-modern badge-produccion mt-2">
                        <i class="fas fa-layer-group mr-1"></i>
                        Entregas Pendientes: <span id="pending-counter"><?php echo count($data['entregas_produccion'] ?? []) + count($data['entregas_scrap'] ?? []); ?></span>
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
                    <div class="flex flex-col items-center justify-center p-12 bg-gray-50 fade-in">
                        <div class="text-center" style="color: var(--primary-blue);">
                            <i class="fas fa-check-circle text-5xl mb-4"></i>
                            <h4 class="text-xl font-semibold" style="color: var(--primary-dark);">¡Todo al día!</h4>
                            <p class="text-gray-500 text-center max-w-md mt-2">
                                No hay entregas pendientes de validación en este momento.
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <?php foreach ($maquinas as $nombre_maquina => $entregas): ?>
                            <div class="border-b border-gray-200 maquina-group fade-in">
                                <div class="bg-gray-50 px-4 py-3 font-bold text-base" style="color: var(--primary-blue);">
                                    <i class="fas fa-cogs mr-2"></i> <?= htmlspecialchars($nombre_maquina) ?>
                                </div>
                                <table class="modern-table w-full" id="tablaEntregas">
                                    <thead>
                                        <tr>
                                            <th class="text-left"><i class="fas fa-calendar-alt mr-2"></i> Fecha/Hora</th>
                                            <th class="text-left"><i class="fas fa-tag mr-2"></i> Item</th>
                                            <th class="text-left"><i class="fas fa-file-alt mr-2"></i> JT/WO</th>
                                            <th class="text-left"><i class="fas fa-barcode mr-2"></i> PO</th>
                                            <th class="text-left"><i class="fas fa-user mr-2"></i> Cliente</th>
                                            <th class="text-left"><i class="fas fa-info-circle mr-2"></i> Tipo</th>
                                            <th class="text-left"><i class="fas fa-cubes mr-2"></i> Detalle</th>
                                            <th class="text-center"><i class="fas fa-tools mr-2"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($entregas as $entrega): ?>
                                            <tr class="entrega-row fade-in"
                                                data-fecha="<?= date('Y-m-d', strtotime($entrega['fecha_registro'])) ?>"
                                                data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                                <td class="px-4 py-3">
                                                    <div class="text-sm">
                                                        <div class="font-medium" style="color: var(--primary-dark);"><?= date('d/m/Y', strtotime($entrega['fecha_registro'])) ?></div>
                                                        <div class="text-gray-500"><?= date('H:i', strtotime($entrega['fecha_registro'])) ?></div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 font-medium" style="color: var(--primary-dark);"><?= htmlspecialchars($entrega['item']) ?></td>
                                                <td class="px-4 py-3">
                                                    <span class="badge-modern badge-produccion"><?= htmlspecialchars($entrega['jtWo']) ?></span>
                                                </td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($entrega['po']) ?></td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($entrega['cliente']) ?></td>
                                                <td class="px-4 py-3">
                                                    <span class="badge-modern <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'badge-final' : 'badge-parcial' ?>">
                                                        <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'Final' : 'Parcial' ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="space-y-2">
                                                        <?php foreach ($entrega['entregas'] as $detalle): ?>
                                                            <div class="flex items-center justify-between py-1.5 px-3 rounded-md badge-modern <?= $detalle['tipo'] == 'scrap' ? 'badge-scrap' : 'badge-produccion' ?>">
                                                                <span class="font-medium"><?= ucfirst($detalle['tipo']) ?></span>
                                                                <span class="font-bold"><?= number_format($detalle['cantidad'], 2) ?> lb.</span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <?php foreach ($entrega['entregas'] as $index => $detalle): ?>
                                                        <div class="flex space-x-2 <?= $index > 0 ? 'mt-2' : '' ?>">
                                                            <button class="btn-modern btn-review w-full justify-center"
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
                                                            <button class="btn-modern <?= $detalle['tipo'] == 'scrap' ? 'btn-validate-scrap' : 'btn-validate-production' ?> w-full justify-center"
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

            <footer class="mt-8 text-center text-gray-500 text-xs py-6">
                <p>© <?= date('Y') ?> Validación de Entregas - Todos los derechos reservados</p>
                <p class="mt-1">Última actualización: <?= date('d/m/Y H:i:s') ?></p>
            </footer>
        </div>

        <!-- Review Modal -->
        <div id="revisionModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden fade-in">
            <div class="modern-modal w-full max-w-md mx-4 overflow-hidden" role="dialog" aria-labelledby="revisionModalTitle" aria-modal="true">
                <div class="modal-header flex justify-between items-center">
                    <h2 id="revisionModalTitle" class="text-lg font-bold flex items-center">
                        <i class="fas fa-search mr-2"></i>Revisar Entrega
                    </h2>
                    <button type="button" class="modal-close focus:outline-none text-white hover:text-gray-200 transition-colors" aria-label="Cerrar">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4 mb-5">
                        <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2" style="color: var(--primary-blue);"></i>
                            Detalles de la entrega:
                        </h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="font-medium" style="color: var(--primary-blue);">Máquina:</span> <span id="revisionMaquina" class="ml-1"></span></div>
                            <div><span class="font-medium" style="color: var(--primary-blue);">Item:</span> <span id="revisionItem" class="ml-1"></span></div>
                            <div><span class="font-medium" style="color: var(--primary-blue);">JT/WO:</span> <span id="revisionJtWo" class="ml-1"></span></div>
                            <div><span class="font-medium" style="color: var(--primary-blue);">Cantidad:</span> <span id="revisionCantidad" class="ml-1"></span></div>
                            <div class="col-span-2"><span class="font-medium" style="color: var(--primary-blue);">Tipo:</span> <span id="revisionTipo" class="ml-1"></span></div>
                        </div>
                    </div>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-5 flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                        <div>Especifique un motivo opcional para revisar la cantidad reportada.</div>
                    </div>
                    <div class="mb-5">
                        <label for="notaRevision" class="block text-gray-700 font-medium mb-2">Motivo de corrección (opcional)</label>
                        <textarea class="modern-textarea" id="notaRevision" rows="3" placeholder="Escriba aquí sus observaciones sobre la cantidad..." aria-describedby="notaRevisionHelp"></textarea>
                        <p id="notaRevisionHelp" class="text-xs text-gray-500 mt-1">Su comentario ayudará a entender el motivo de la revisión.</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" class="modal-close btn-modern btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn-modern btn-primary" id="submitRevisionBtn">
                            <i class="fas fa-paper-plane mr-2"></i>Enviar Revisión
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation Modal -->
        <div id="validateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden fade-in">
            <div class="modern-modal w-full max-w-md mx-4 overflow-hidden" role="dialog" aria-labelledby="validateModalTitle" aria-modal="true">
                <div class="modal-header flex justify-between items-center">
                    <h2 id="validateModalTitle" class="text-lg font-bold flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>Validar Entrega
                    </h2>
                    <button type="button" class="modal-close focus:outline-none text-white hover:text-gray-200 transition-colors" aria-label="Cerrar">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4 mb-5">
                        <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2" style="color: var(--primary-blue);"></i>
                            Detalles de la entrega a validar:
                        </h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="font-medium" style="color: var(--primary-blue);">Máquina:</span> <span id="validacionMaquina" class="ml-1"></span></div>
                            <div><span class="font-medium" style="color: var(--primary-blue);">Item:</span> <span id="validacionItem" class="ml-1"></span></div>
                            <div><span class="font-medium" style="color: var(--primary-blue);">JT/WO:</span> <span id="validacionJtWo" class="ml-1"></span></div>
                            <div><span class="font-medium" style="color: var(--primary-blue);">Cantidad:</span> <span id="validacionCantidad" class="ml-1"></span></div>
                            <div class="col-span-2"><span class="font-medium" style="color: var(--primary-blue);">Tipo:</span> <span id="validacionTipo" class="ml-1"></span></div>
                        </div>
                    </div>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-5 flex items-start">
                        <i class="fas fa-check-circle text-green-600 mt-0.5 mr-3"></i>
                        <div>Al validar esta entrega, se registrará como completa en el sistema.</div>
                    </div>
                    <div class="mb-5">
                        <label for="comentarioValidacion" class="block text-gray-700 font-medium mb-2">Comentario (opcional)</label>
                        <textarea class="modern-textarea" id="comentarioValidacion" data-id="data-comentario" rows="3" placeholder="Escriba aquí sus observaciones sobre la entrega..." aria-describedby="comentarioValidacionHelp"></textarea>
                        <p id="comentarioValidacionHelp" class="text-xs text-gray-500 mt-1">Su comentario quedará registrado con la validación.</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" class="modal-close btn-modern btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn-modern btn-validate-production" id="submitValidation">
                            <i class="fas fa-check mr-2"></i>Validar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toasts Container -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/appValidacion.js"></script>
    <script src="assets/js/qa/validacion.js"></script>
    <script src="assets/js/qa/paginacion.js"></script>
</body>
</html>
