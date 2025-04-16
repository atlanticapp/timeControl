<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Revisiones QA">
    <title><?= $data['titulo'] ?></title>
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
                            <h1 class="text-2xl font-bold text-blue-700 flex items-center">
                                <i class="fas fa-clipboard-check mr-3"></i>Correcciones de Producción
                            </h1>
                            <p class="text-gray-500 mt-1">Pendientes de Revisión</p>
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
                            <p class="text-gray-500 text-sm">Total Correcciones</p>
                            <h3 class="text-2xl font-bold text-blue-700 mt-1">
                                <?= $data['estadisticas']['total'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Correcciones de Producción -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Producción</p>
                            <h3 class="text-2xl font-bold text-green-600 mt-1">
                                <?= $data['estadisticas']['produccion'] ?? 0 ?>
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
                                <?= $data['estadisticas']['scrap'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-trash-alt text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 px-5 flex items-center">
                    <i class="fas fa-tasks mr-3 text-xl"></i>
                    <h3 class="text-lg font-bold">Listado de Correcciones Pendientes</h3>
                </div>

                <?php if (empty($data['correcciones'])): ?>
                    <div class="bg-blue-50 border-l-4 border-blue-600 p-4 m-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
                            <span class="text-blue-700">No hay correcciones de producción pendientes de revisión en este momento.</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 text-left text-gray-600 text-sm">
                                <tr>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-calendar-alt text-blue-600 mr-2"></i>Solicitado el</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-industry text-blue-600 mr-2"></i>Máquina</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-box text-blue-600 mr-2"></i>Item</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-file-alt text-blue-600 mr-2"></i>JT/WO</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-tag text-blue-600 mr-2"></i>Tipo</th>
                                    <th class="px-4 py-3 font-medium"><i class="fas fa-layer-group text-blue-600 mr-2"></i>Cantidad</th>
                                    <th class="px-4 py-3 font-medium text-center"><i class="fas fa-comment text-blue-600 mr-2"></i>Motivo</th>
                                    <th class="px-4 py-3 font-medium text-center"><i class="fas fa-tools text-blue-600 mr-2"></i>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['correcciones'] as $correccion): ?>
                                    <tr class="hover:bg-blue-50 border-b border-gray-100">
                                        <td class="px-4 py-3 text-sm"><?= date('d/m/Y H:i', strtotime($correccion['fecha_solicitud'])) ?></td>
                                        <td class="px-4 py-3">
                                            <span class="text-gray-600">
                                                <?= htmlspecialchars($correccion['nombre_maquina'] ?? 'No especificada') ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-medium"><?= htmlspecialchars($correccion['item']) ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($correccion['jtWo']) ?></td>
                                        <td class="px-4 py-3">
                                            <?php if ($correccion['tipo_cantidad'] === 'scrap'): ?>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-trash-alt mr-1"></i>Scrap
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-industry mr-1"></i>Producción
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php if ($correccion['tipo_cantidad'] === 'scrap'): ?>
                                                <span class="text-red-600 font-medium"><?= htmlspecialchars($correccion['cantidad_scrapt']) ?></span>
                                            <?php else: ?>
                                                <span class="text-green-600 font-medium"><?= htmlspecialchars($correccion['cantidad_produccion']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button class="ver-motivo inline-flex items-center px-2.5 py-1.5 border border-blue-600 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition-colors duration-200"
                                                data-motivo="<?= htmlspecialchars($correccion['motivo']) ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button onclick="cancelarCorreccion(<?= $correccion['id'] ?>)"
                                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                                                <i class="fas fa-times mr-2"></i>Cancelar
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

        <!-- Modal para mostrar el motivo -->
        <div id="modalMotivo" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h5 class="text-lg font-bold flex items-center">
                        <i class="fas fa-comment mr-2"></i>Motivo de la Corrección
                    </h5>
                    <button type="button" class="modal-close focus:outline-none text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <p id="textoMotivo" class="text-gray-700 italic"></p>
                    <div class="mt-6 flex justify-end">
                        <button type="button" class="modal-close px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors flex items-center">
                            <i class="fas fa-times mr-2"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

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

        // Modificar la sección del script donde se maneja el modal
        document.querySelectorAll('.ver-motivo').forEach(button => {
            button.addEventListener('click', () => {
                const motivo = button.getAttribute('data-motivo');
                document.getElementById('textoMotivo').textContent = motivo || 'No se ha especificado ningún motivo para esta corrección.';
                document.getElementById('modalMotivo').classList.remove('hidden');
            });
        });

        document.querySelectorAll('.modal-close').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('modalMotivo').classList.add('hidden');
            });
        });

        function cancelarCorreccion(id) {
            if (confirm('¿Está seguro que desea cancelar esta corrección?')) {
                fetch('/timeControl/public/cancelar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: id
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        // La redirección será manejada por el controlador
                        window.location.href = '/timeControl/public/revisiones';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Ocurrió un error al procesar la solicitud');
                    });
            }
        }
    </script>
</body>

</html>