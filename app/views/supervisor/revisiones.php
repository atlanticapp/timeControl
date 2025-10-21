<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Supervisor - Revisiones">
    <title><?= $data['titulo'] ?? 'Revisiones Pendientes' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/timeControl/public/assets/css/supervisor/revisiones.css">
</head>

<body>
    <?php include __DIR__ . "/../layouts/sidebarSupervisor.php"; ?>

    <main class="lg:ml-72 transition-all duration-300 min-h-screen">
        <div class="container mx-auto px-4 md:px-6 py-8">
            <!-- Header -->
            <div class="modern-card p-6 mb-6">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div class="mb-4 md:mb-0">
                        <nav class="text-sm text-gray-500 mb-2">
                            <a href="/timeControl/public/supervisor" class="hover:text-blue-600">
                                <i class="fas fa-home mr-1"></i>Inicio
                            </a>
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <span style="color: var(--primary-blue);">Revisiones Pendientes</span>
                        </nav>
                        <h1 class="text-2xl font-bold flex items-center gap-3" style="color: var(--primary-blue);">
                            <i class="fas fa-search"></i>
                            Revisiones Pendientes
                        </h1>
                        <p class="text-gray-500 mt-1">Control de Correcciones Solicitadas - Área <?= htmlspecialchars($data['area'] ?? 'N/A') ?></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="/timeControl/public/supervisor" class="btn-modern" style="background: white; color: var(--primary-blue); border: 2px solid var(--primary-blue);">
                            <i class="fas fa-arrow-left"></i>Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total de Correcciones -->
                <div class="stat-card" style="border-left-color: var(--primary-blue);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Total Correcciones</p>
                            <h3 class="text-3xl font-bold" style="color: var(--primary-blue);">
                                <?= $data['estadisticas']['total'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(91, 164, 207, 0.1); color: var(--primary-blue);">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>

                <!-- Correcciones de Produccion -->
                <div class="stat-card" style="border-left-color: #10B981;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Producción</p>
                            <h3 class="text-3xl font-bold text-green-600">
                                <?= $data['estadisticas']['produccion'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: #D1FAE5; color: #059669;">
                            <i class="fas fa-industry"></i>
                        </div>
                    </div>
                </div>

                <!-- Correcciones de Scrap -->
                <div class="stat-card" style="border-left-color: #DC2626;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Scrap</p>
                            <h3 class="text-3xl font-bold text-red-600">
                                <?= $data['estadisticas']['scrap'] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: #FEE2E2; color: #DC2626;">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Revisiones -->
            <div class="modern-card overflow-hidden">
                <div class="p-5" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);">
                    <h3 class="text-lg font-bold text-white flex items-center gap-3">
                        <i class="fas fa-tasks"></i>
                        Listado de Revisiones Pendientes
                    </h3>
                </div>

                <?php if (empty($data['correcciones'])): ?>
                    <div class="flex flex-col items-center justify-center p-12">
                        <div class="stat-icon mb-4" style="background: rgba(91, 164, 207, 0.1); color: var(--primary-blue); width: 80px; height: 80px; font-size: 32px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 class="text-xl font-semibold mb-2" style="color: var(--primary-dark);">¡Todo al día!</h4>
                        <p class="text-gray-500 text-center max-w-md">
                            No hay revisiones pendientes en este momento.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="modern-table w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">Solicitado el</th>
                                    <th class="text-left">Máquina</th>
                                    <th class="text-left">Operador</th>
                                    <th class="text-left">Item</th>
                                    <th class="text-left">JT/WO</th>
                                    <th class="text-left">Tipo</th>
                                    <th class="text-left">Cantidad</th>
                                    <th class="text-center">Motivo</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php foreach ($data['correcciones'] as $correccion): ?>
                                    <tr>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="font-medium" style="color: var(--primary-dark);">
                                                <?= date('d/m/Y', strtotime($correccion['fecha_solicitud'])) ?>
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                <?= date('H:i', strtotime($correccion['fecha_solicitud'])) ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="text-gray-600">
                                                <?= htmlspecialchars($correccion['nombre_maquina'] ?? 'No especificada') ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="font-medium"><?= htmlspecialchars($correccion['nombre_empleado']) ?></div>
                                            <div class="text-gray-500 text-xs">ID: <?= htmlspecialchars($correccion['codigo_empleado']) ?></div>
                                        </td>
                                        <td class="px-4 py-3 font-medium"><?= htmlspecialchars($correccion['item']) ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($correccion['jtWo']) ?></td>
                                        <td class="px-4 py-3">
                                            <?php if ($correccion['tipo_cantidad'] === 'scrap'): ?>
                                                <span class="badge-modern badge-scrap">
                                                    <i class="fas fa-trash-alt mr-1"></i>Scrap
                                                </span>
                                            <?php else: ?>
                                                <span class="badge-modern badge-produccion">
                                                    <i class="fas fa-industry mr-1"></i>Producción
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php if ($correccion['tipo_cantidad'] === 'scrap'): ?>
                                                <span class="text-red-600 font-bold"><?= number_format($correccion['cantidad_scrapt'], 2) ?> lb.</span>
                                            <?php else: ?>
                                                <span class="text-green-600 font-bold"><?= number_format($correccion['cantidad_produccion'], 2) ?> lb.</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button class="ver-motivo btn-modern" 
                                                    style="background: white; color: var(--primary-blue); border: 2px solid var(--primary-blue); padding: 0.5rem 1rem;"
                                                    data-motivo="<?= htmlspecialchars($correccion['motivo']) ?>">
                                                <i class="fas fa-eye"></i>Ver
                                            </button>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button onclick="cancelarCorreccion(<?= $correccion['id'] ?>)"
                                                    class="btn-modern"
                                                    style="background: #DC2626; color: white; padding: 0.5rem 1rem;">
                                                <i class="fas fa-times"></i>Cancelar
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
        <div id="modalMotivo" class="modern-modal fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="modal-content bg-white w-full max-w-md mx-4">
                <div class="px-6 py-4 flex justify-between items-center" style="background: var(--primary-blue);">
                    <h5 class="text-lg font-bold flex items-center text-white">
                        <i class="fas fa-comment mr-2"></i>Motivo de la Corrección
                    </h5>
                    <button type="button" class="modal-close focus:outline-none text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div class="modern-card p-4 mb-4" style="background: var(--bg-light);">
                        <p id="textoMotivo" class="text-gray-700 italic"></p>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="modal-close btn-modern" style="background: #E5E7EB; color: var(--primary-dark);">
                            <i class="fas fa-times"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        // Configuración de Toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };

        // Mostrar mensajes de sesión
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
                const status = "<?= $_SESSION['status'] ?>";
                const message = "<?= htmlspecialchars($_SESSION['message'], ENT_QUOTES) ?>";
                
                if (typeof toastr !== 'undefined') {
                    if (status === 'success') {
                        toastr.success(message);
                    } else if (status === 'error') {
                        toastr.error(message);
                    }
                } else {
                    // Fallback si toastr no está disponible
                    showNotification(message, status === 'success' ? 'success' : 'error');
                }
                
                <?php 
                    unset($_SESSION['status']); 
                    unset($_SESSION['message']); 
                ?>
            <?php endif; ?>
        });

        // Sistema de notificaciones alternativo
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-md p-4 rounded-lg shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            } text-white`;
            notification.innerHTML = `
                <div class="flex items-start">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-3 mt-1"></i>
                    <div class="flex-1">
                        <p class="font-semibold">${type === 'success' ? 'Éxito' : type === 'error' ? 'Error' : 'Información'}</p>
                        <p class="text-sm">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Modal de motivo
        document.querySelectorAll('.ver-motivo').forEach(button => {
            button.addEventListener('click', () => {
                const motivo = button.getAttribute('data-motivo');
                document.getElementById('textoMotivo').textContent = motivo || 'No se ha especificado ningún motivo para esta corrección.';
                document.getElementById('modalMotivo').classList.remove('hidden');
            });
        });

        // Cerrar modal
        document.querySelectorAll('.modal-close').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('modalMotivo').classList.add('hidden');
            });
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalMotivo').addEventListener('click', (e) => {
            if (e.target.id === 'modalMotivo') {
                document.getElementById('modalMotivo').classList.add('hidden');
            }
        });

        // Función para cancelar corrección
        function cancelarCorreccion(id) {
            if (confirm('¿Está seguro que desea cancelar esta solicitud de corrección?\n\nEl operador no será notificado de esta cancelación.')) {
                // Mostrar loading
                toastr.info('Procesando...', '', {timeOut: 0, extendedTimeOut: 0});

                // Debug: mostrar el ID que se está enviando
                console.log('Cancelando corrección ID:', id);

                fetch('/timeControl/public/supervisor/cancelar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    // Limpiar toasts anteriores
                    toastr.clear();
                    
                    if (data.success) {
                        toastr.success(data.message);
                        // Recargar la página después de 1 segundo
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(data.message || 'Error al cancelar la solicitud');
                    }
                })
                .catch(error => {
                    toastr.clear();
                    console.error('Error completo:', error);
                    toastr.error('Ocurrió un error al procesar la solicitud: ' + error.message);
                });
            }
        }

        // Logout
        window.confirmLogout = function() {
            if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
                document.cookie = 'jwt=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                window.location.href = "/timeControl/public/logout";
            }
        };
    </script>
</body>
</html>