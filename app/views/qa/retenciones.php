<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Gestión de Retenciones">
    <title>Control de Calidad - Gestión de Retenciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body class="bg-gray-50">
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 p-6 md:p-8 transition-all duration-300 min-h-screen bg-gray-50">
        <div class="container mx-auto pt-14 lg:pt-4 max-w-7xl">
            <!-- Header Section -->
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-4 md:p-5">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                        <div class="mb-4 md:mb-0">
                            <!-- Título con icono accesible -->
                            <h1 class="text-2xl font-bold text-yellow-600 flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M12 2L1 21h22L12 2zm0 4l7.53 13H4.47L12 6zm-1 5v4h2v-4h-2zm0 6v2h2v-2h-2z" />
                                </svg>
                                Gestión de Retenciones
                            </h1>
                            <p class="text-gray-500 mt-1">Sistema de Control de Calidad - Retenciones</p>
                        </div>
                        <!-- Contador de Retenciones -->
                        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                            </svg>
                            <span class="font-semibold">Total Retenciones: <?php echo count($data['retenciones']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-4 md:px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-bold flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                        </svg>
                        Retenciones Activas
                    </h2>
                </div>

                <?php if (empty($data['retenciones'])): ?>
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                        </svg>
                        <p class="text-xl">No hay retenciones activas</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 text-gray-600 text-sm">
                                <tr>
                                    <th class="px-3 md:px-4 py-3 font-medium text-left">
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar text-[#D39605] mr-2"></i>
                                            Fecha/Hora
                                        </span>
                                    </th>
                                    <th class="px-3 md:px-4 py-3 font-medium text-left">
                                        <span class="flex items-center">
                                            <i class="fas fa-wrench text-[#D39605] mr-2"></i>
                                            Máquina
                                        </span>
                                    </th>
                                    <th class="px-3 md:px-4 py-3 font-medium text-left hidden md:table-cell">
                                        <span class="flex items-center">
                                            <i class="fas fa-tag text-[#D39605] mr-2"></i>
                                            Item
                                        </span>
                                    </th>
                                    <th class="px-3 md:px-4 py-3 font-medium text-left hidden md:table-cell">
                                        <span class="flex items-center">
                                            <i class="fas fa-file-alt text-[#D39605] mr-2"></i>
                                            JT/WO
                                        </span>
                                    </th>
                                    <th class="px-3 md:px-4 py-3 font-medium text-left">
                                        <span class="flex items-center">
                                            <i class="fas fa-plus-square text-[#D39605] mr-2"></i>
                                            Cantidad
                                        </span>
                                    </th>
                                    <th class="px-3 md:px-4 py-3 font-medium text-left hidden sm:table-cell">Estado</th>
                                    <th class="px-3 md:px-4 py-3 font-medium text-left hidden lg:table-cell">
                                        <span class="flex items-center">
                                            <i class="fas fa-comment-alt text-[#D39605] mr-2"></i>
                                            Motivo
                                        </span>
                                    </th>
                                    <th class="px-3 md:px-4 py-3 font-medium text-center">
                                        <span class="flex items-center">
                                            <i class="fas fa-tools text-[#D39605] mr-2"></i>
                                            Acciones
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($data['retenciones'] as $retencion): ?>
                                    <?php
                                    $porcentajeDisponible = ($retencion['cantidad_total'] > 0)
                                        ? ($retencion['cantidad_disponible'] / $retencion['cantidad_total']) * 100
                                        : 0;
                                    $colorEstado = $porcentajeDisponible == 0 ? 'bg-gray-200' : ($porcentajeDisponible < 50 ? 'bg-yellow-200' : 'bg-green-200');
                                    $textEstado = $porcentajeDisponible == 0 ? 'text-gray-600' : ($porcentajeDisponible < 50 ? 'text-yellow-800' : 'text-green-800');
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 md:px-4 py-3 text-sm text-gray-700">
                                            <?= date('d/m/Y H:i', strtotime($retencion['fecha_creacion'])) ?>
                                        </td>
                                        <td class="px-3 md:px-4 py-3">
                                            <div class="text-sm text-gray-900 font-medium">
                                                <?= htmlspecialchars($retencion['nombre_maquina'] ?? 'No especificada') ?>
                                            </div>
                                        </td>
                                        <td class="px-3 md:px-4 py-3 font-medium text-gray-800 hidden md:table-cell">
                                            <?= htmlspecialchars($retencion['item']) ?>
                                        </td>
                                        <td class="px-3 md:px-4 py-3 text-gray-800 hidden md:table-cell">
                                            <?= htmlspecialchars($retencion['jtWo']) ?>
                                        </td>
                                        <td class="px-3 md:px-4 py-3">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900">
                                                    <?= number_format($retencion['cantidad_total'], 2) ?> <span class="text-xs text-gray-500">lb.</span>
                                                </span>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Disp: <span class="font-medium"><?= number_format($retencion['cantidad_disponible'], 2) ?> lb.</span>
                                                </div>

                                                <!-- Barra de progreso -->
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                    <div class="bg-yellow-500 h-1.5 rounded-full" style="width: <?= $porcentajeDisponible ?>%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 md:px-4 py-3 hidden sm:table-cell">
                                            <?php
                                            $gestionado = $retencion['cantidad_total'] - $retencion['cantidad_disponible'];
                                            $porcentajeGestionado = ($retencion['cantidad_total'] > 0)
                                                ? ($gestionado / $retencion['cantidad_total']) * 100
                                                : 0;
                                            ?>
                                            <div class="flex items-center">
                                                <span class="px-2 py-1 text-xs rounded-md <?= $colorEstado ?> <?= $textEstado ?>">
                                                    <?= round($porcentajeGestionado) ?>% Gestionado
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 md:px-4 py-3 text-sm hidden lg:table-cell">
                                            <div class="line-clamp-2">
                                                <?= htmlspecialchars($retencion['motivo']) ?>
                                            </div>
                                        </td>
                                        <td class="px-3 md:px-4 py-3 text-center">
                                            <button type="button"
                                                class="inline-flex items-center px-2 md:px-3 py-1 md:py-1.5 border border-yellow-600 text-yellow-600 rounded-lg hover:bg-yellow-600 hover:text-white transition-colors duration-200 text-sm"
                                                data-retencion='<?= htmlspecialchars(json_encode($retencion), ENT_QUOTES, 'UTF-8') ?>'>
                                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                    <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z" />
                                                </svg>
                                                Gestionar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal Gestionar Retención -->
        <div id="modalGestionar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl transform transition-all">
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-4 flex justify-between items-center rounded-t-lg">
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

                            <!-- Información de la retención -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-5">
                                <h6 class="font-semibold text-gray-700 mb-3">Detalles de la retención:</h6>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                                        <span class="text-gray-500">Item:</span>
                                        <span id="modalItem" class="font-medium text-gray-800 ml-1"></span>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                                        <span class="text-gray-500">JT/WO:</span>
                                        <span id="modalJtWo" class="font-medium text-gray-800 ml-1"></span>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                                        <span class="text-gray-500">Cantidad Total:</span>
                                        <span id="modalCantidadTotal" class="font-medium text-gray-800 ml-1"></span> Lb
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                                        <span class="text-gray-500">Disponible:</span>
                                        <span id="modalCantidadDisponible" class="font-medium text-gray-800 ml-1"></span> Lb
                                    </div>
                                </div>
                            </div>

                            <!-- Balance disponible y asignado con barras de progreso -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
                                <div class="flex flex-col md:flex-row justify-between mb-2">
                                    <div class="flex-1 p-2">
                                        <h6 class="text-gray-700 font-medium mb-2">Balance Actual</h6>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm text-yellow-700">
                                                <i class="fas fa-balance-scale mr-1"></i> Disponible:
                                            </span>
                                            <span id="balanceDisponible" class="font-medium">0.00</span>
                                        </div>
                                        <!-- Barra de progreso disponible -->
                                        <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                            <div id="progresoDisponible" class="bg-yellow-500 h-2 rounded-full" style="width: 100%"></div>
                                        </div>

                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm text-blue-700">
                                                <i class="fas fa-check-circle mr-1"></i> Asignado:
                                            </span>
                                            <span id="balanceAsignado" class="font-medium">0.00</span>
                                        </div>
                                        <!-- Barra de progreso asignado -->
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div id="progresoAsignado" class="bg-blue-500 h-2 rounded-full" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Secciones de destino -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <!-- Destino: Validar -->
                                <div class="border rounded-lg p-4 bg-green-50 border-green-200 shadow-sm hover:shadow transition-shadow">
                                    <div class="flex items-center justify-between mb-3">
                                        <h6 class="font-medium text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i> Liberar a Producción
                                        </h6>
                                        <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full">Validado</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Cantidad (Lb)
                                            </label>
                                            <div class="relative">
                                                <input type="number" name="cantidad_produccion_final" id="cantidad_produccion_final"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md destino-cantidad focus:ring-2 focus:ring-green-300 focus:border-green-400"
                                                    step="0.01" min="0" value="0">
                                                <button type="button" class="absolute right-2 top-2 text-green-600 hover:text-green-800 btn-asignar-todo"
                                                    data-destino="produccion_final">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Comentarios
                                            </label>
                                            <textarea name="motivo_produccion_final" rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-green-300 focus:border-green-400"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Destino: Retrabajo -->
                                <div class="border rounded-lg p-4 bg-blue-50 border-blue-200 shadow-sm hover:shadow transition-shadow">
                                    <div class="flex items-center justify-between mb-3">
                                        <h6 class="font-medium text-blue-700">
                                            <i class="fas fa-tools mr-1"></i> Retrabajo
                                        </h6>
                                        <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full">Proceso</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Cantidad (Lb)
                                            </label>
                                            <div class="relative">
                                                <input type="number" name="cantidad_retrabajo" id="cantidad_retrabajo"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md destino-cantidad focus:ring-2 focus:ring-blue-300 focus:border-blue-400"
                                                    step="0.01" min="0" value="0">
                                                <button type="button" class="absolute right-2 top-2 text-blue-600 hover:text-blue-800 btn-asignar-todo"
                                                    data-destino="retrabajo">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Comentarios
                                            </label>
                                            <textarea name="motivo_retrabajo" rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Destino: Destruir -->
                                <div class="border rounded-lg p-4 bg-red-50 border-red-200 shadow-sm hover:shadow transition-shadow">
                                    <div class="flex items-center justify-between mb-3">
                                        <h6 class="font-medium text-red-700">
                                            <i class="fas fa-trash-alt mr-1"></i> Destruir
                                        </h6>
                                        <span class="text-xs bg-red-200 text-red-800 px-2 py-1 rounded-full">Scrap</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Cantidad (Lb)
                                            </label>
                                            <div class="relative">
                                                <input type="number" name="cantidad_destruccion" id="cantidad_destruccion"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md destino-cantidad focus:ring-2 focus:ring-red-300 focus:border-red-400"
                                                    step="0.01" min="0" value="0">
                                                <button type="button" class="absolute right-2 top-2 text-red-600 hover:text-red-800 btn-asignar-todo"
                                                    data-destino="destruccion">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Comentarios
                                            </label>
                                            <textarea name="motivo_destruccion" rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-red-300 focus:border-red-400"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error message -->
                            <div id="errorMessage" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm" id="errorText"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" id="btnCancelar"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-save mr-2"></i>Guardar Asignaciones
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.message) {
                        const toastrFunction = data.status === "success" ? toastr.success : toastr.error;
                        toastrFunction(data.message, '', {
                            timeOut: 2000
                        });
                    }
                })
                .catch(error => console.error('Error al obtener el estado:', error));
        });
    </script>
    <script>
        // Clase para gestionar retenciones
        class GestionRetenciones {
            constructor() {
                this.retencionActual = null;
                this.cantidadTotal = 0;
                this.cantidadDisponible = 0;
                this.inicializar();
            }

            inicializar() {
                // Configurar toastr
                this.configurarToastr();

                // Event listeners
                this.configurarEventListeners();
            }

            configurarToastr() {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 3000,
                    preventDuplicates: true,
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut'
                };
            }

            configurarEventListeners() {
                // Botones de gestión
                document.querySelectorAll('[data-retencion]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const retencion = JSON.parse(e.currentTarget.dataset.retencion);
                        this.abrirModal(retencion);
                    });
                });

                // Cerrar modal
                document.getElementById('btnCerrarModal').addEventListener('click', () => this.cerrarModal());
                document.getElementById('btnCancelar').addEventListener('click', () => this.cerrarModal());

                // Campos de cantidad
                document.querySelectorAll('.destino-cantidad').forEach(input => {
                    input.addEventListener('input', () => this.actualizarBalances());
                });

                // Botones de asignar todo
                document.querySelectorAll('.btn-asignar-todo').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        this.asignarTodo(e.currentTarget.dataset.destino);
                    });
                });

                // Formulario
                document.getElementById('formGestionar').addEventListener('submit', (e) => this.guardarAsignaciones(e));
            }

            abrirModal(retencion) {
                this.retencionActual = retencion;
                this.cantidadTotal = parseFloat(retencion.cantidad_total);
                this.cantidadDisponible = parseFloat(retencion.cantidad_disponible);

                // Actualizar información en el modal
                document.getElementById('retencionId').value = retencion.id;
                document.getElementById('modalItem').textContent = retencion.item;
                document.getElementById('modalJtWo').textContent = retencion.jtWo;
                document.getElementById('modalCantidadTotal').textContent = this.cantidadTotal.toFixed(2);
                document.getElementById('modalCantidadDisponible').textContent = this.cantidadDisponible.toFixed(2);

                // Limpiar campos
                document.getElementById('cantidad_produccion_final').value = '0';
                document.getElementById('cantidad_retrabajo').value = '0';
                document.getElementById('cantidad_destruccion').value = '0';

                // Actualizar balances y barras de progreso
                this.actualizarBalances();

                // Ocultar mensajes de error
                document.getElementById('errorMessage').classList.add('hidden');

                // Mostrar el modal
                document.getElementById('modalGestionar').classList.remove('hidden');
            }

            cerrarModal() {
                document.getElementById('modalGestionar').classList.add('hidden');
                document.getElementById('formGestionar').reset();
            }

            actualizarBalances() {
                const cantidadProduccion = parseFloat(document.getElementById('cantidad_produccion_final').value) || 0;
                const cantidadRetrabajo = parseFloat(document.getElementById('cantidad_retrabajo').value) || 0;
                const cantidadDestruccion = parseFloat(document.getElementById('cantidad_destruccion').value) || 0;

                const totalAsignado = cantidadProduccion + cantidadRetrabajo + cantidadDestruccion;
                const disponibleActualizado = this.cantidadDisponible - totalAsignado;

                // Actualizar displays de texto
                document.getElementById('balanceAsignado').textContent = totalAsignado.toFixed(2) + ' Lb';
                document.getElementById('balanceDisponible').textContent = disponibleActualizado.toFixed(2) + ' Lb';

                // Actualizar barras de progreso
                // Modificación: Asegurar que el porcentaje asignado no supere el 100%
                const porcentajeAsignado = Math.min((totalAsignado / this.cantidadDisponible) * 100, 100);
                const porcentajeDisponible = Math.max(100 - porcentajeAsignado, 0);

                document.getElementById('progresoDisponible').style.width = porcentajeDisponible + '%';
                document.getElementById('progresoAsignado').style.width = porcentajeAsignado + '%';

                // Cambiar colores de las barras según el estado
                if (porcentajeDisponible < 20) {
                    document.getElementById('progresoDisponible').classList.remove('bg-yellow-500');
                    document.getElementById('progresoDisponible').classList.add('bg-red-500');
                } else {
                    document.getElementById('progresoDisponible').classList.remove('bg-red-500');
                    document.getElementById('progresoDisponible').classList.add('bg-yellow-500');
                }

                // Cambiar color de la barra de asignado cuando excede lo disponible
                if (totalAsignado > this.cantidadDisponible) {
                    document.getElementById('progresoAsignado').classList.add('bg-red-500');
                    document.getElementById('progresoAsignado').classList.remove('bg-blue-500'); // Asumiendo que normalmente es azul
                } else {
                    document.getElementById('progresoAsignado').classList.remove('bg-red-500');
                    document.getElementById('progresoAsignado').classList.add('bg-blue-500');
                }

                // Validar cantidades
                if (totalAsignado > this.cantidadDisponible) {
                    this.mostrarError('La cantidad total asignada excede el balance disponible.');
                    return false;
                } else {
                    document.getElementById('errorMessage').classList.add('hidden');
                    return true;
                }
            }

            asignarTodo(destino) {
                // Resetear todos los campos primero
                document.getElementById('cantidad_produccion_final').value = '0';
                document.getElementById('cantidad_retrabajo').value = '0';
                document.getElementById('cantidad_destruccion').value = '0';

                // Asignar todo al destino seleccionado
                document.getElementById(`cantidad_${destino}`).value = this.cantidadDisponible.toFixed(2);

                // Actualizar balances
                this.actualizarBalances();
            }

            mostrarError(mensaje) {
                const errorMessage = document.getElementById('errorMessage');
                const errorText = document.getElementById('errorText');
                errorMessage.classList.remove('hidden');
                errorText.textContent = mensaje;

                // Mostrar también en toastr
                toastr.error(mensaje);
            }

            guardarAsignaciones(event) {
                event.preventDefault();

                // Validar cantidades
                if (!this.actualizarBalances()) {
                    return false;
                }

                const cantidadProduccion = parseFloat(document.getElementById('cantidad_produccion_final').value) || 0;
                const cantidadRetrabajo = parseFloat(document.getElementById('cantidad_retrabajo').value) || 0;
                const cantidadDestruccion = parseFloat(document.getElementById('cantidad_destruccion').value) || 0;

                // Verificar que se ha asignado al menos una cantidad
                if (cantidadProduccion + cantidadRetrabajo + cantidadDestruccion <= 0) {
                    this.mostrarError('Debe asignar al menos una cantidad a un destino.');
                    return false;
                }

                // Validar que todas las cantidades sean positivas
                if ((cantidadProduccion < 0) || (cantidadRetrabajo < 0) || (cantidadDestruccion < 0)) {
                    this.mostrarError('Las cantidades deben ser valores numéricos positivos.');
                    return false;
                }

                // Crear FormData
                const formData = new FormData();
                formData.append('retencion_id', this.retencionActual.id);

                // Arrays para guardar destinos, cantidades y motivos
                const destinos = [];
                const cantidades = [];
                const motivos = [];

                // Añadir producción final
                if (cantidadProduccion > 0) {
                    destinos.push('produccion_final');
                    cantidades.push(cantidadProduccion);
                    motivos.push(document.querySelector('[name="motivo_produccion_final"]').value || 'Liberado a producción');
                }

                // Añadir retrabajo
                if (cantidadRetrabajo > 0) {
                    destinos.push('retrabajo');
                    cantidades.push(cantidadRetrabajo);
                    motivos.push(document.querySelector('[name="motivo_retrabajo"]').value || 'Enviado a retrabajo');
                }

                // Añadir destrucción
                if (cantidadDestruccion > 0) {
                    destinos.push('destruccion');
                    cantidades.push(cantidadDestruccion);
                    motivos.push(document.querySelector('[name="motivo_destruccion"]').value || 'Material para destruir');
                }

                // Añadir arrays al FormData
                destinos.forEach((destino, index) => {
                    formData.append('destinos[]', destino);
                    formData.append('cantidades[]', cantidades[index]);
                    formData.append('motivos[]', motivos[index]);
                });

                // Mostrar indicador de carga
                const submitButton = event.target.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
                submitButton.disabled = true;

                // Enviar petición usando Fetch API
                this.enviarAsignaciones(formData, submitButton, originalButtonText);
            }

            enviarAsignaciones(formData, submitButton, originalButtonText) {
                fetch('/timeControl/public/asignarDestinos', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        // Primero comprobamos si es una redirección
                        if (response.redirected) {
                            window.location.href = response.url;
                            return Promise.reject('redirect');
                        }

                        // Si no es una redirección, intentamos obtener JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        }

                        // Si no es JSON, tratamos de obtener el texto
                        return response.text().then(text => {
                            return {
                                status: response.ok ? 'success' : 'error',
                                message: text
                            };
                        });
                    })
                    .then(data => {
                        if (data.status === 'error') {
                            throw new Error(data.message || 'Error en el servidor');
                        }

                        // Éxito
                        toastr.success(data.message || 'Destinos asignados correctamente');

                        // Redirigir después de un breve retraso
                        setTimeout(() => {
                            window.location.href = '/timeControl/public/retenciones';
                        }, 1500);
                    })
                    .catch(error => {
                        if (error === 'redirect') {
                            return; // La redirección ya se está manejando
                        }

                        console.error('Error:', error);
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;

                        // Mostrar error
                        this.mostrarError(error.message || 'Error al procesar la solicitud. Inténtelo de nuevo.');
                    });
            }
        }

        // Inicializar la aplicación cuando el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            new GestionRetenciones();
        });
    </script>
</body>

</html>