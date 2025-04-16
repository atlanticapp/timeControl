<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Destinos de Retrabajo">
    <title>Control de Calidad - Destinos de Retrabajo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body class="bg-gray-50">
    <?php include __DIR__ . "/../../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 p-6 md:p-8 transition-all duration-300 min-h-screen bg-gray-50">
        <div class="container mx-auto pt-14 lg:pt-4">
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-5">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                        <div class="mb-4 md:mb-0">
                            <h1 class="text-2xl font-bold text-green-700 flex items-center">
                                <i class="fas fa-recycle mr-3"></i>Destinos de Retrabajo
                            </h1>
                            <p class="text-gray-500 mt-1">Control de Destinos para Material Retenido - Retrabajo</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center">
                        <i class="fas fa-list-alt mr-3"></i>Listado de Destinos - Retrabajo
                    </h3>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-recycle"></i>
                        <span class="font-semibold">Total: <?php echo count($data['destinos'] ?? []); ?></span>
                    </div>
                </div>

                <?php if (empty($data['destinos'])): ?>
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-recycle text-6xl mb-4"></i>
                        <p class="text-xl">No hay destinos de retrabajo registrados</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 text-gray-600 text-sm">
                                <tr>
                                    <th class="px-4 py-3 text-left">Fecha</th>
                                    <th class="px-4 py-3 text-left">Máquina</th>
                                    <th class="px-4 py-3 text-left">Item</th>
                                    <th class="px-4 py-3 text-left">JT/WO</th>
                                    <th class="px-4 py-3 text-left">Cantidad</th>
                                    <th class="px-4 py-3 text-left">Motivo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($data['destinos'] as $destino): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3"><?= date('d/m/Y H:i', strtotime($destino['fecha_registro'])) ?></td>
                                        <td class="px-4 py-3">
                                            <span class="text-gray-800">
                                                <?= htmlspecialchars($destino['nombre_maquina'] ?? 'No especificada') ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($destino['item']) ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($destino['jtWo']) ?></td>
                                        <td class="px-4 py-3 font-bold"><?= number_format($destino['cantidad'], 2) ?> <span class="text-xs text-gray-500">lb.</span></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($destino['motivo']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

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
</body>
</html>