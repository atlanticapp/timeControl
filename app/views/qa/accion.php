
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Entregas de Producción">
    <title>Control de Calidad - Entregas de Producción</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="./assets/css/qa/accion.css">
</head>

<body>
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 p-6 md:p-8 transition-all duration-300 min-h-screen">
        <div class="modern-header p-6 mb-6">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="logo-container">
                        <img src="/timeControl/public/assets/img/logoprint.png" alt="Control Tiempos Logo" class="logo-image">
                        <div>
                            <nav class="breadcrumb-modern mb-2" aria-label="Breadcrumb">
                                <a href="/timeControl/public/dashboard" class="hover:underline">Inicio</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <a href="/timeControl/public/qa" class="hover:underline">Panel QA</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Acción QA</span>
                            </nav>
                            <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-clipboard-check"></i>
                                Acción QA
                            </h1>
                            <p class="text-white/80 mt-1 text-sm">Validación de Entregas de Producción y Scrap</p>
                        </div>
                    </div>
                    <div class="text-white/90 text-sm">
                        <i class="fas fa-calendar-alt mr-2"></i><?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 md:px-6 pb-8">
            <!-- Filtros de búsqueda -->
            <div class="modern-card p-6 mb-6">
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
                        <label class="block text-gray-600 text-sm mb-1" for="filtroTipo">Tipo</label>
                        <select id="filtroTipo" class="modern-select">
                            <option value="">Todos</option>
                            <option value="produccion">Producción</option>
                            <option value="scrap">Scrap</option>
                        </select>
                    </div>
                    <div>
                        <button id="btnLimpiarFiltros" class="btn-modern btn-secondary">
                            <i class="fas fa-eraser mr-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="modern-card overflow-hidden">
                <div class="p-5 modal-header">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-clipboard-list mr-3"></i>Entregas Validadas de Producción y Scrap
                    </h3>
                    <div class="badge-modern badge-validado mt-2">
                        <i class="fas fa-layer-group mr-1"></i>
                        Total Entregas: <span class="total-count"><?php echo isset($data['entregas_validadas']) ? count($data['entregas_validadas']) : 0; ?></span>
                    </div>
                </div>

                <?php if (empty($data['entregas_validadas'] ?? [])): ?>
                    <div class="text-center py-12 text-gray-500 fade-in">
                        <i class="fas fa-box-open text-6xl mb-4" style="color: var(--primary-blue);"></i>
                        <p class="text-xl">No hay entregas registradas</p>
                    </div>
                <?php else: ?>
                    <?php
                    // Agrupar entregas por máquina
                    $maquinas = [];
                    foreach ($data['entregas_validadas'] as $entrega) {
                        $maquina = $entrega['nombre_maquina'] ?? 'Sin Máquina';
                        if (!isset($maquinas[$maquina])) {
                            $maquinas[$maquina] = [];
                        }
                        $maquinas[$maquina][] = $entrega;
                    }
                    ?>

                    <div class="overflow-x-auto">
                        <?php foreach ($maquinas as $nombre_maquina => $entregas): ?>
                            <div class="border-b border-gray-200 maquina-group fade-in">
                                <div class="bg-gray-50 px-4 py-3 font-bold text-base" style="color: var(--primary-blue);">
                                    <i class="fas fa-cogs mr-2"></i> <?= htmlspecialchars($nombre_maquina) ?>
                                </div>
                                <table class="modern-table w-full">
                                    <thead>
                                        <tr>
                                            <th class="text-left"><i class="fas fa-calendar-alt mr-2"></i> Fecha/Hora</th>
                                            <th class="text-left"><i class="fas fa-cogs mr-2"></i> Máquina</th>
                                            <th class="text-left"><i class="fas fa-tag mr-2"></i> Item</th>
                                            <th class="text-left"><i class="fas fa-file-alt mr-2"></i> JT/WO</th>
                                            <th class="text-left"><i class="fas fa-file-invoice mr-2"></i> PO</th>
                                            <th class="text-left"><i class="fas fa-user mr-2"></i> Cliente</th>
                                            <th class="text-center"><i class="fas fa-list mr-2"></i> Tipo</th>
                                            <th class="text-right"><i class="fas fa-cubes mr-2"></i> Cantidad</th>
                                            <th class="text-center"><i class="fas fa-check-circle mr-2"></i> Estado</th>
                                            <th class="text-center"><i class="fas fa-tools mr-2"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($entregas as $entrega): 
                                            $es_scrap = isset($entrega['tipo_entrega']) && $entrega['tipo_entrega'] === 'scrap';
                                            $cantidad_mostrar = $es_scrap ? $entrega['cantidad_scrapt'] : $entrega['cantidad_produccion'];
                                            $tipo_texto = $es_scrap ? 'scrap' : 'produccion';
                                        ?>
                                            <tr class="entrega-row fade-in"
                                                data-fecha="<?= date('Y-m-d', strtotime($entrega['fecha_registro'])) ?>"
                                                data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                data-po="<?= htmlspecialchars($entrega['po'] ?? '') ?>"
                                                data-cliente="<?= htmlspecialchars($entrega['cliente'] ?? '') ?>"
                                                data-tipo="<?= $tipo_texto ?>">
                                                <td class="px-4 py-3 text-sm"><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A') ?></td>
                                                <td class="px-4 py-3 font-medium" style="color: var(--primary-dark);"><?= htmlspecialchars($entrega['item']) ?></td>
                                                <td class="px-4 py-3">
                                                    <span class="badge-modern badge-validado"><?= htmlspecialchars($entrega['jtWo']) ?></span>
                                                </td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($entrega['po'] ?? 'N/A') ?></td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($entrega['cliente'] ?? 'N/A') ?></td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="badge-modern <?= $es_scrap ? 'badge-scrap' : 'badge-produccion' ?>">
                                                        <i class="fas <?= $es_scrap ? 'fa-trash' : 'fa-check' ?> mr-1"></i>
                                                        <?= $es_scrap ? 'Scrap' : 'Producción' ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold">
                                                    <span class="badge-modern <?= $es_scrap ? 'badge-scrap' : 'badge-produccion' ?>">
                                                        <?= number_format($cantidad_mostrar, 2, '.', ',') ?> Lb
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="badge-modern <?= $entrega['estado_validacion'] === 'Guardado' ? 'badge-guardado' : ($entrega['estado_validacion'] === 'Retenido' ? 'badge-retenido' : 'badge-validado') ?>">
                                                        <?= htmlspecialchars($entrega['estado_validacion']) ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <?php if ($entrega['estado_validacion'] === 'Validado'): ?>
                                                        <div class="flex justify-center space-x-2">
                                                            <button class="btn-modern btn-validate-final"
                                                                onclick="openValidateModal(<?= (int)$entrega['id'] ?>, 
                                                                    '<?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>', 
                                                                    '<?= htmlspecialchars($entrega['item'], ENT_QUOTES, 'UTF-8') ?>', 
                                                                    '<?= htmlspecialchars($entrega['jtWo'], ENT_QUOTES, 'UTF-8') ?>', 
                                                                    '<?= (float)$cantidad_mostrar ?>', 
                                                                    '<?= $tipo_texto ?>')">
                                                                <i class="fas fa-check mr-1"></i> Validar
                                                            </button>
                                                            <button class="btn-modern btn-retain"
                                                                onclick="openRetainModal(<?= (int)$entrega['id'] ?>, 
                                                                    '<?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>', 
                                                                    '<?= htmlspecialchars($entrega['item'], ENT_QUOTES, 'UTF-8') ?>', 
                                                                    '<?= htmlspecialchars($entrega['jtWo'], ENT_QUOTES, 'UTF-8') ?>', 
                                                                    '<?= (float)$cantidad_mostrar ?>', 
                                                                    '<?= $tipo_texto ?>')">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i> Retener
                                                            </button>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-gray-500 text-sm">No disponible</span>
                                                    <?php endif; ?>
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

            <!-- Validation Modal -->
            <div id="validateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
                <div class="modern-modal w-full max-w-md mx-4 overflow-hidden fade-in">
                    <div class="modal-header flex justify-between items-center">
                        <h5 class="text-lg font-bold flex items-center"><i class="fas fa-check-circle mr-2"></i>Validar Entrega</h5>
                        <button type="button" onclick="closeModal('validateModal')" class="focus:outline-none text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h6 class="font-semibold text-gray-700 mb-2">Detalles de la entrega a validar:</h6>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><span class="font-medium">Máquina:</span> <span id="validacionMaquina"></span></div>
                                <div><span class="font-medium">Item:</span> <span id="validacionItem"></span></div>
                                <div><span class="font-medium">JT/WO:</span> <span id="validacionJtWo"></span></div>
                                <div><span class="font-medium">Cantidad:</span> <span id="validacionCantidad"></span> Lb</div>
                                <div class="col-span-2"><span class="font-medium">Tipo:</span> <span id="validacionTipo"></span></div>
                            </div>
                        </div>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 flex">
                            <i class="fas fa-check-circle text-green-600 mt-1 mr-3 text-lg"></i>
                            <div>Al validar esta entrega, se registrará como completa en el sistema.</div>
                        </div>
                        <form id="validateForm">
                            <input type="hidden" id="validateEntregaId" name="entrega_id">
                            <input type="hidden" id="validateTipo" name="tipo">
                            <div class="mb-4">
                                <label for="comentarioValidacion" class="block text-gray-700 font-medium mb-2">Comentario (opcional)</label>
                                <textarea class="modern-textarea" id="comentarioValidacion" name="comentario" rows="3" placeholder="Escriba aquí sus observaciones sobre la entrega..."></textarea>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('validateModal')" class="btn-modern btn-secondary">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </button>
                                <button type="button" id="btnSubmitValidate" onclick="submitValidation()" class="btn-modern btn-validate-final">
                                    <i class="fas fa-check mr-2"></i>Validar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Retain Modal -->
            <div id="retainModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
                <div class="modern-modal w-full max-w-md mx-4 overflow-hidden fade-in">
                    <div class="modal-header flex justify-between items-center">
                        <h5 class="text-lg font-bold flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Retener Entrega
                        </h5>
                        <button type="button" onclick="closeModal('retainModal')" class="focus:outline-none text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h6 class="font-semibold text-gray-700 mb-2">Detalles de la entrega a retener:</h6>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><span class="font-medium">Máquina:</span> <span id="retencionMaquina"></span></div>
                                <div><span class="font-medium">Item:</span> <span id="retencionItem"></span></div>
                                <div><span class="font-medium">JT/WO:</span> <span id="retencionJtWo"></span></div>
                                <div><span class="font-medium">Cantidad:</span> <span id="retencionCantidad"></span> Lb</div>
                                <div class="col-span-2"><span class="font-medium">Tipo:</span> <span id="retencionTipo"></span></div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4 flex">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3 text-lg"></i>
                            <div>Al retener esta entrega, se marcará para revisión adicional y no se procesará hasta resolver los problemas.</div>
                        </div>
                        <form id="retainForm">
                            <input type="hidden" id="retainEntregaId" name="entrega_id">
                            <input type="hidden" id="retainCantidadInput" name="cantidad">
                            <input type="hidden" id="retainTipo" name="tipo">
                            <div class="mb-4">
                                <label for="retainMotivo" class="block text-sm font-medium text-gray-700 mb-1">
                                    Motivo de retención: <span class="text-red-500">*</span>
                                </label>
                                <select id="retainMotivo" name="motivo" class="modern-select" required>
                                    <option value="">Seleccione un motivo</option>
                                    <option value="calidad">Problema de calidad</option>
                                    <option value="documentacion">Documentación incompleta</option>
                                    <option value="cantidad">Discrepancia en cantidad</option>
                                    <option value="otro">Otro motivo</option>
                                </select>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('retainModal')" class="btn-modern btn-secondary">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </button>
                                <button type="button" id="btnSubmitRetain" onclick="submitRetention()" class="btn-modern btn-retain">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Retener
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <footer class="mt-8 text-center text-gray-500 text-xs py-6">
                <p>© <?= date('Y') ?> Acción QA - Todos los derechos reservados</p>
                <p class="mt-1">Última actualización: <?= date('d/m/Y H:i:s') ?></p>
            </footer>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/qa/accion.js"></script>


</body>
</html>
