<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Entregas de Producción">
    <title>Control de Calidad - Entregas de Producción</title>
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
                            <!-- Título -->
                            <h1 class="text-2xl font-bold text-amber-600 flex items-center">
                                <i class="fas fa-clipboard-check mr-3"></i>Acción QA
                            </h1>
                            <p class="text-gray-500 mt-1">Validación de Entregas de Producción</p>
                        </div>
                        <!-- Contador de Entregas -->
                        <div class="flex items-center space-x-4">
                            <div class="bg-amber-100 text-amber-800 px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-layer-group mr-2"></i>
                                <span class="font-semibold">Total Entregas: <?php echo count($data['entregas_validadas']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center">
                        <i class="fas fa-clipboard-list mr-3"></i>Entregas Pendientes de Validación
                    </h3>
                </div>

                <?php if (empty($data['entregas_validadas'])): ?>
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-box-open text-6xl mb-4"></i>
                        <p class="text-xl">No hay entregas de producción registradas</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 text-left text-gray-600 text-sm">
                                <tr>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-calendar-alt text-blue-600 mr-2"></i> Fecha/Hora</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-cogs text-blue-600 mr-2"></i> Máquina</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-tag text-blue-600 mr-2"></i> Item</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-file-alt text-blue-600 mr-2"></i> JT/WO</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-file-invoice text-blue-600 mr-2"></i> PO</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-user text-blue-600 mr-2"></i> Cliente</th>
                                    <th class="px-4 py-3 font-medium text-right"><i class="fas fa-cubes text-blue-600 mr-2"></i> Cantidad</th>
                                    <th class="px-4 py-3 font-medium text-center"><i class="fas fa-tools text-blue-600 mr-2"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['entregas_validadas'] as $entrega): ?>
                                    <tr class="hover:bg-blue-50 border-b border-gray-100">
                                        <td class="px-4 py-3 text-sm"><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-3 font-medium"><?= htmlspecialchars($entrega['item']) ?></td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?= htmlspecialchars($entrega['jtWo']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($entrega['po'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($entrega['cliente'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-3 text-right font-bold">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <?= number_format($entrega['cantidad_produccion'], 2, '.', ',') ?> Lb
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <button class="btn-validate inline-flex items-center px-2.5 py-1.5 border border-green-600 text-green-600 rounded hover:bg-green-600 hover:text-white transition-colors duration-200"
                                                    onclick="openValidateModal(<?= $entrega['id'] ?>, 
                                                        '<?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A') ?>', 
                                                        '<?= htmlspecialchars($entrega['item']) ?>', 
                                                        '<?= htmlspecialchars($entrega['jtWo']) ?>', 
                                                        '<?= $entrega['cantidad_produccion'] ?>')">
                                                    <i class="fas fa-check mr-1"></i> Validar
                                                </button>
                                                <button class="btn-retain inline-flex items-center px-2.5 py-1.5 border border-yellow-500 text-yellow-500 rounded hover:bg-yellow-500 hover:text-white transition-colors duration-200"
                                                    onclick="openRetainModal(<?= $entrega['id'] ?>, 
                                                      '<?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A') ?>', 
                                                      '<?= htmlspecialchars($entrega['item']) ?>', 
                                                      '<?= htmlspecialchars($entrega['jtWo']) ?>', 
                                                      '<?= $entrega['cantidad_produccion'] ?>')">
                                                    <i class="fas fa-times mr-1"></i> Retener
                                                </button>
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

        <!-- Validation Modal -->
        <div id="validateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-transform duration-300 scale-95">
                <div class="bg-green-600 text-white px-6 py-4 flex justify-between items-center">
                    <h5 class="text-lg font-bold flex items-center"><i class="fas fa-check-circle mr-2"></i>Validar Entrega</h5>
                    <button type="button" onclick="closeModal('validateModal')" class="focus:outline-none text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Información de la entrega -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-gray-700 mb-2">Detalles de la entrega a validar:</h6>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="font-medium">Máquina:</span> <span id="validacionMaquina"></span></div>
                            <div><span class="font-medium">Item:</span> <span id="validacionItem"></span></div>
                            <div><span class="font-medium">JT/WO:</span> <span id="validacionJtWo"></span></div>
                            <div><span class="font-medium">Cantidad:</span> <span id="validacionCantidad"></span> Lb</div>
                        </div>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 flex">
                        <i class="fas fa-check-circle text-green-600 mt-1 mr-3 text-lg"></i>
                        <div>Al validar esta entrega, se registrará como completa en el sistema.</div>
                    </div>

                    <form id="validateForm">
                        <input type="hidden" id="validateEntregaId" name="entrega_id">

                        <div class="mb-4">
                            <label for="comentarioValidacion" class="block text-gray-700 font-medium mb-2">Comentario (opcional)</label>
                            <textarea class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                id="comentarioValidacion" name="comentario" rows="3"
                                placeholder="Escriba aquí sus observaciones sobre la entrega..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('validateModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors flex items-center">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </button>
                            <button type="button" onclick="submitValidation()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
                                <i class="fas fa-check mr-2"></i>Validar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Retain Modal -->
        <div id="retainModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-transform duration-300 scale-95">
                <div class="bg-yellow-500 text-white px-6 py-4 flex justify-between items-center">
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Retener Entrega
                    </h5>
                    <button type="button" onclick="closeModal('retainModal')" class="focus:outline-none text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Información de la entrega -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-gray-700 mb-2">Detalles de la entrega a retener:</h6>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="font-medium">Máquina:</span> <span id="retencionMaquina"></span></div>
                            <div><span class="font-medium">Item:</span> <span id="retencionItem"></span></div>
                            <div><span class="font-medium">JT/WO:</span> <span id="retencionJtWo"></span></div>
                            <div><span class="font-medium">Cantidad:</span> <span id="retencionCantidad"></span> Lb</div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4 flex">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3 text-lg"></i>
                        <div>Al retener esta entrega, se marcará para revisión adicional y no se procesará hasta resolver los problemas.</div>
                    </div>

                    <form id="retainForm">
                        <input type="hidden" id="retainEntregaId" name="entrega_id">

                        <div class="mb-4">
                            <label for="retainMotivo" class="block text-sm font-medium text-gray-700 mb-1">
                                Motivo de retención: <span class="text-red-500">*</span>
                            </label>
                            <select id="retainMotivo" name="motivo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                                <option value="">Seleccione un motivo</option>
                                <option value="calidad">Problema de calidad</option>
                                <option value="documentacion">Documentación incompleta</option>
                                <option value="cantidad">Discrepancia en cantidad</option>
                                <option value="otro">Otro motivo</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('retainModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors flex items-center">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </button>
                            <button type="button" onclick="submitRetention()" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Retener
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Toasts Container -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50"></div>

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
        // Configuración de toastr
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: "toast-bottom-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "5000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut"
        };

        // Funciones para modals
        function openValidateModal(id, maquina, item, jtWo, cantidad) {
            document.getElementById('validateEntregaId').value = id;
            document.getElementById('validacionMaquina').textContent = maquina;
            document.getElementById('validacionItem').textContent = item;
            document.getElementById('validacionJtWo').textContent = jtWo;
            document.getElementById('validacionCantidad').textContent = cantidad;

            document.getElementById('validateModal').classList.remove('hidden');
            document.getElementById('validateModal').classList.add('flex');

            // Animación
            setTimeout(() => {
                document.querySelector('#validateModal > div').classList.remove('scale-95');
                document.querySelector('#validateModal > div').classList.add('scale-100');
            }, 10);
        }

        function openRetainModal(id, maquina, item, jtWo, cantidad) {
            document.getElementById('retainEntregaId').value = id;
            document.getElementById('retencionMaquina').textContent = maquina;
            document.getElementById('retencionItem').textContent = item;
            document.getElementById('retencionJtWo').textContent = jtWo;
            document.getElementById('retencionCantidad').textContent = cantidad;

            document.getElementById('retainModal').classList.remove('hidden');
            document.getElementById('retainModal').classList.add('flex');

            // Animación
            setTimeout(() => {
                document.querySelector('#retainModal > div').classList.remove('scale-95');
                document.querySelector('#retainModal > div').classList.add('scale-100');
            }, 10);
        }

        function closeModal(modalId) {
            // Animación de salida
            document.querySelector(`#${modalId} > div`).classList.remove('scale-100');
            document.querySelector(`#${modalId} > div`).classList.add('scale-95');

            setTimeout(() => {
                document.getElementById(modalId).classList.add('hidden');
                document.getElementById(modalId).classList.remove('flex');

                // Limpiar formulario
                if (modalId === 'validateModal') {
                    document.getElementById('comentarioValidacion').value = '';
                } else if (modalId === 'retainModal') {
                    document.getElementById('retainMotivo').value = '';
                }
            }, 200);
        }

        // Funciones para enviar formularios
        function submitValidation() {
            const entregaId = document.getElementById('validateEntregaId').value;
            const comentario = document.getElementById('comentarioValidacion').value;

            // Validaciones básicas
            if (!entregaId) {
                toastr.error('No se ha seleccionado una entrega para validar');
                return;
            }

            // Mostrar indicador de carga
            const submitButton = document.querySelector('#validateForm button[type="button"]');
            const originalContent = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

            // Crear FormData
            const formData = new FormData();
            formData.append('entrega_id', entregaId);
            formData.append('comentario', comentario);

            // Realizar la petición al servidor
            fetch('/timeControl/public/guardarProduccion', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la petición');
                    }
                    // Cerrar el modal y recargar la página para mostrar el mensaje del backend
                    closeModal('validateModal');
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error al procesar la solicitud. Por favor, intente nuevamente.');
                })
                .finally(() => {
                    // Restaurar el botón
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalContent;
                });
        }

        function submitRetention() {
            const entregaId = document.getElementById('retainEntregaId').value;
            const motivo = document.getElementById('retainMotivo').value;
            const cantidad = document.getElementById('retencionCantidad').textContent;

            // Validaciones básicas del lado del cliente
            if (!entregaId || !motivo || !cantidad) {
                toastr.error('Todos los campos son obligatorios');
                return;
            }

            // Validar que la cantidad sea un número válido
            const cantidadNum = parseFloat(cantidad);
            if (isNaN(cantidadNum) || cantidadNum <= 0) {
                toastr.error('La cantidad debe ser un número válido mayor que 0');
                return;
            }

            // Mostrar indicador de carga
            const submitButton = document.querySelector('#retainForm button[type="button"]');
            const originalContent = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';

            // Crear FormData con los datos necesarios
            const formData = new FormData();
            formData.append('registro_id', entregaId);
            formData.append('cantidad', cantidadNum);
            formData.append('motivo', motivo);

            // Realizar la petición al servidor
            fetch('/timeControl/public/retener', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la petición');
                    }
                    // Cerrar el modal y recargar la página para mostrar el mensaje del backend
                    closeModal('retainModal');
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error al procesar la solicitud. Por favor, intente nuevamente.');
                })
                .finally(() => {
                    // Restaurar el botón
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalContent;
                });
        }

        // Función auxiliar para actualizar el contador de entregas
        function updateEntregasCount() {
            const totalCounter = document.querySelector('.total-entregas');
            if (totalCounter) {
                const currentTotal = parseInt(totalCounter.textContent || '0') - 1;
                totalCounter.textContent = Math.max(0, currentTotal);
            }
        }
    </script>

</body>

</html>