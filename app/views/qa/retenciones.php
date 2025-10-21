
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Gestión de Retenciones">
    <title>Control de Calidad - Gestión de Retenciones</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/timeControl/public/assets/css/qa/retenciones.css">
    
</head>

<body>
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 p-4 md:p-6 transition-all duration-300 min-h-screen">
        <div class="modern-header p-6 mb-6">
            <div class="container mx-auto max-w-7xl">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="logo-container">
                        <img src="/timeControl/public/assets/img/logoprint.png" alt="Control Tiempos Logo" class="logo-image">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                Gestión de Retenciones
                            </h1>
                            <p class="text-white/80 mt-1 text-sm">Sistema de Control de Calidad - Retenciones</p>
                        </div>
                    </div>
                    <div class="text-white/90 text-sm">
                        <i class="fas fa-calendar-alt mr-2"></i><?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 md:px-6 pb-8 max-w-7xl">
            <!-- Contador de Retenciones -->
            <div class="stat-card mb-6 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Retenciones</p>
                        <h3 class="text-2xl font-bold" style="color: var(--yellow-primary);">
                            <?php echo count($data['retenciones']); ?>
                        </h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="modern-card overflow-hidden fade-in">
                <div class="modal-header p-5">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3"></i>Retenciones Activas
                    </h2>
                </div>

                <?php if (empty($data['retenciones'])): ?>
                    <div class="flex flex-col items-center justify-center p-12 bg-gray-50 fade-in">
                        <div class="text-center" style="color: var(--yellow-primary);">
                            <i class="fas fa-check-circle text-5xl mb-4"></i>
                            <h4 class="text-xl font-semibold" style="color: var(--primary-dark);">¡Todo al día!</h4>
                            <p class="text-gray-500 text-center max-w-md mt-2">
                                No hay retenciones activas en este momento.
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="modern-table w-full">
                            <thead>
                                <tr>
                                    <th class="text-left"><i class="fas fa-calendar-alt mr-2"></i> Fecha/Hora</th>
                                    <th class="text-left"><i class="fas fa-wrench mr-2"></i> Máquina</th>
                                    <th class="text-left hidden md:table-cell"><i class="fas fa-tag mr-2"></i> Item</th>
                                    <th class="text-left hidden md:table-cell"><i class="fas fa-file-alt mr-2"></i> JT/WO</th>
                                    <th class="text-left"><i class="fas fa-plus-square mr-2"></i> Cantidad</th>
                                    <th class="text-left hidden sm:table-cell">Estado</th>
                                    <th class="text-left hidden lg:table-cell"><i class="fas fa-comment-alt mr-2"></i> Motivo</th>
                                    <th class="text-center"><i class="fas fa-tools mr-2"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['retenciones'] as $retencion): ?>
                                    <?php
                                    $porcentajeDisponible = ($retencion['cantidad_total'] > 0)
                                        ? ($retencion['cantidad_disponible'] / $retencion['cantidad_total']) * 100
                                        : 0;
                                    $gestionado = $retencion['cantidad_total'] - $retencion['cantidad_disponible'];
                                    $porcentajeGestionado = ($retencion['cantidad_total'] > 0)
                                        ? ($gestionado / $retencion['cantidad_total']) * 100
                                        : 0;
                                    $badgeClass = $porcentajeGestionado == 0 ? 'badge-gestionado-0' :
                                        ($porcentajeGestionado < 50 ? 'badge-gestionado-low' : 'badge-gestionado-high');
                                    ?>
                                    <tr class="fade-in mobile-table-row">
                                        <td class="px-4 py-3 text-sm" style="color: var(--primary-dark);">
                                            <div class="mobile-field md:hidden">
                                                <span class="mobile-field-label">Fecha/Hora</span>
                                                <span class="mobile-field-value"><?= date('d/m/Y H:i', strtotime($retencion['fecha_creacion'])) ?></span>
                                            </div>
                                            <span class="hidden md:block"><?= date('d/m/Y H:i', strtotime($retencion['fecha_creacion'])) ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="mobile-field md:hidden">
                                                <span class="mobile-field-label">Máquina</span>
                                                <span class="mobile-field-value"><?= htmlspecialchars($retencion['nombre_maquina'] ?? 'No especificada') ?></span>
                                            </div>
                                            <span class="font-medium hidden md:block" style="color: var(--primary-dark);"><?= htmlspecialchars($retencion['nombre_maquina'] ?? 'No especificada') ?></span>
                                        </td>
                                        <td class="px-4 py-3 font-medium hidden md:table-cell" style="color: var(--primary-dark);">
                                            <div class="mobile-field md:hidden">
                                                <span class="mobile-field-label">Item</span>
                                                <span class="mobile-field-value"><?= htmlspecialchars($retencion['item']) ?></span>
                                            </div>
                                            <span class="hidden md:block"><?= htmlspecialchars($retencion['item']) ?></span>
                                        </td>
                                        <td class="px-4 py-3 hidden md:table-cell">
                                            <div class="mobile-field md:hidden">
                                                <span class="mobile-field-label">JT/WO</span>
                                                <span class="mobile-field-value"><?= htmlspecialchars($retencion['jtWo']) ?></span>
                                            </div>
                                            <span class="badge-modern badge-produccion hidden md:inline-flex"><?= htmlspecialchars($retencion['jtWo']) ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="mobile-field md:hidden">
                                                <span class="mobile-field-label">Cantidad</span>
                                                <span class="mobile-field-value">
                                                    <?= number_format($retencion['cantidad_total'], 2) ?> lb. (Disp: <?= number_format($retencion['cantidad_disponible'], 2) ?> lb.)
                                                </span>
                                            </div>
                                            <div class="flex flex-col hidden md:block">
                                                <span class="font-medium" style="color: var(--primary-dark);">
                                                    <?= number_format($retencion['cantidad_total'], 2) ?> <span class="text-xs text-gray-500">lb.</span>
                                                </span>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Disp: <span class="font-medium"><?= number_format($retencion['cantidad_disponible'], 2) ?> lb.</span>
                                                </div>
                                                <div class="progress-bar mt-1">
                                                    <div class="progress-bar-fill bg-yellow-500" style="width: <?= $porcentajeDisponible ?>%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 hidden sm:table-cell">
                                            <div class="mobile-field sm:hidden">
                                                <span class="mobile-field-label">Estado</span>
                                                <span class="mobile-field-value"><?= round($porcentajeGestionado) ?>% Gestionado</span>
                                            </div>
                                            <span class="badge-modern <?= $badgeClass ?> hidden sm:inline-flex">
                                                <?= round($porcentajeGestionado) ?>% Gestionado
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm hidden lg:table-cell">
                                            <div class="mobile-field lg:hidden">
                                                <span class="mobile-field-label">Motivo</span>
                                                <span class="mobile-field-value"><?= htmlspecialchars($retencion['motivo']) ?></span>
                                            </div>
                                            <span class="hidden lg:block line-clamp-2"><?= htmlspecialchars($retencion['motivo']) ?></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" class="btn-modern btn-primary w-full md:w-auto justify-center"
                                                data-retencion='<?= htmlspecialchars(json_encode($retencion), ENT_QUOTES, 'UTF-8') ?>'>
                                                <i class="fas fa-share-alt mr-1"></i>Gestionar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <footer class="mt-8 text-center text-gray-500 text-xs py-6">
                <p>© <?= date('Y') ?> Gestión de Retenciones - Todos los derechos reservados</p>
                <p class="mt-1">Última actualización: <?= date('d/m/Y H:i:s') ?></p>
            </footer>
        </div>

        <!-- Modal Gestionar Retención -->
        <div id="modalGestionar" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center fade-in">
            <div class="modern-modal w-full max-w-3xl mx-4 overflow-hidden">
                <div class="modal-header flex justify-between items-center">
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-share-alt mr-2"></i>Gestionar Retención
                    </h5>
                    <button type="button" id="btnCerrarModal" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form id="formGestionar">
                        <input type="hidden" id="retencionId" name="retencion_id">
                        <div class="bg-gray-50 rounded-lg p-4 mb-5">
                            <h6 class="font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-info-circle mr-2" style="color: var(--yellow-primary);"></i>
                                Detalles de la retención:
                            </h6>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div><span class="font-medium" style="color: var(--yellow-primary);">Item:</span> <span id="modalItem" class="ml-1"></span></div>
                                <div><span class="font-medium" style="color: var(--yellow-primary);">JT/WO:</span> <span id="modalJtWo" class="ml-1"></span></div>
                                <div><span class="font-medium" style="color: var(--yellow-primary);">Cantidad Total:</span> <span id="modalCantidadTotal" class="ml-1"></span> lb.</div>
                                <div><span class="font-medium" style="color: var(--yellow-primary);">Disponible:</span> <span id="modalCantidadDisponible" class="ml-1"></span> lb.</div>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
                            <div class="flex flex-col md:flex-row justify-between mb-2">
                                <div class="flex-1 p-2">
                                    <h6 class="text-gray-700 font-medium mb-2">Balance Actual</h6>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm" style="color: var(--yellow-primary);">
                                            <i class="fas fa-balance-scale mr-1"></i> Disponible:
                                        </span>
                                        <span id="balanceDisponible" class="font-medium">0.00 lb.</span>
                                    </div>
                                    <div class="progress-bar mb-3">
                                        <div id="progresoDisponible" class="progress-bar-fill bg-yellow-500" style="width: 100%"></div>
                                    </div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm" style="color: var(--primary-blue);">
                                            <i class="fas fa-check-circle mr-1"></i> Asignado:
                                        </span>
                                        <span id="balanceAsignado" class="font-medium">0.00 lb.</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div id="progresoAsignado" class="progress-bar-fill bg-blue-500" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="modern-card p-4 bg-green-50">
                                <div class="flex items-center justify-between mb-3">
                                    <h6 class="font-medium text-green-700">
                                        <i class="fas fa-check-circle mr-1"></i> Liberar a Producción
                                    </h6>
                                    <span class="badge-modern badge-produccion">Validado</span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad (lb.)</label>
                                        <div class="relative">
                                            <input type="number" name="cantidad_produccion_final" id="cantidad_produccion_final"
                                                class="modern-input destino-cantidad" step="0.01" min="0" value="0">
                                            <button type="button" class="absolute right-2 top-2 text-green-600 hover:text-green-800 btn-asignar-todo"
                                                data-destino="produccion_final">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Comentarios</label>
                                        <textarea name="motivo_produccion_final" rows="2" class="modern-textarea"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modern-card p-4 bg-blue-50">
                                <div class="flex items-center justify-between mb-3">
                                    <h6 class="font-medium text-blue-700">
                                        <i class="fas fa-tools mr-1"></i> Retrabajo
                                    </h6>
                                    <span class="badge-modern badge-retrabajo">Proceso</span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad (lb.)</label>
                                        <div class="relative">
                                            <input type="number" name="cantidad_retrabajo" id="cantidad_retrabajo"
                                                class="modern-input destino-cantidad" step="0.01" min="0" value="0">
                                            <button type="button" class="absolute right-2 top-2 text-blue-600 hover:text-blue-800 btn-asignar-todo"
                                                data-destino="retrabajo">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Comentarios</label>
                                        <textarea name="motivo_retrabajo" rows="2" class="modern-textarea"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modern-card p-4 bg-red-50">
                                <div class="flex items-center justify-between mb-3">
                                    <h6 class="font-medium text-red-700">
                                        <i class="fas fa-trash-alt mr-1"></i> Destruir
                                    </h6>
                                    <span class="badge-modern badge-destruccion">Scrap</span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad (lb.)</label>
                                        <div class="relative">
                                            <input type="number" name="cantidad_destruccion" id="cantidad_destruccion"
                                                class="modern-input destino-cantidad" step="0.01" min="0" value="0">
                                            <button type="button" class="absolute right-2 top-2 text-red-600 hover:text-red-800 btn-asignar-todo"
                                                data-destino="destruccion">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Comentarios</label>
                                        <textarea name="motivo_destruccion" rows="2" class="modern-textarea"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="errorMessage" class="error-message hidden">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                            <p class="text-sm" id="errorText"></p>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" id="btnCancelar" class="btn-modern btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn-modern btn-primary">
                                <i class="fas fa-save mr-2"></i>Guardar Asignaciones
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/qa/rentenciones.js"></script>

</body>
</html>
