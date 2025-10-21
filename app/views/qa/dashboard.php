
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de Control QA - Atlantic KPG">
    <title>Panel de Control QA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/timeControl/public/assets/css/qa/dashboard.css">
</head>

<body>
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 transition-all duration-300 min-h-screen">
        <div class="modern-header p-6 mb-6">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="logo-container">
                        <img src="/timeControl/public/assets/img/logoprint.png" alt="Control Tiempos Logo" class="logo-image">
                        <div>
                            <nav class="breadcrumb-modern mb-2" aria-label="Breadcrumb">
                                <a href="/timeControl/public/dashboard" class="hover:underline">Inicio</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Panel QA</span>
                            </nav>
                            <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-tachometer-alt"></i>
                                Panel de Control QA
                            </h1>
                            <p class="text-white/80 mt-1 text-sm">Control de Validaciones y Retenciones</p>
                        </div>
                    </div>
                    <div class="text-white/90 text-sm">
                        <i class="fas fa-calendar-alt mr-2"></i><?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 md:px-6 pb-8">
            <?php
            $deliveryStats = [
                'total' => $data['stats']['pendientes'] ?? 0,
                'production' => $data['stats']['produccion_pendiente'] ?? 0,
                'scrap' => $data['stats']['scrap_pendiente'] ?? 0,
                'validated' => $data['stats']['validadas'] ?? 0,
                'retenciones' => $data['stats']['retenciones'] ?? 0
            ];
            function calculatePercentage($value, $total)
            {
                return $total > 0 ? number_format(($value / $total) * 100, 2) : 0;
            }
            $productionPercent = calculatePercentage($deliveryStats['production'], $deliveryStats['total']);
            $scrapPercent = calculatePercentage($deliveryStats['scrap'], $deliveryStats['total']);
            ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Entregas Pendientes</p>
                            <h3 class="text-3xl font-bold" style="color: var(--primary-blue);">
                                <?= number_format($deliveryStats['total']) ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: rgba(91, 164, 207, 0.1); color: var(--primary-blue);">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Producción</p>
                            <h3 class="text-3xl font-bold text-green-600">
                                <?= number_format($deliveryStats['production']) ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: #D1FAE5; color: #059669;">
                            <i class="fas fa-industry"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Scrap</p>
                            <h3 class="text-3xl font-bold text-red-600">
                                <?= number_format($deliveryStats['scrap']) ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: #FEE2E2; color: #DC2626;">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Validadas</p>
                            <h3 class="text-3xl font-bold text-amber-600">
                                <?= number_format($deliveryStats['validated']) ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: #FEF3C7; color: #D97706;">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Retenciones Activas</p>
                            <h3 class="text-3xl font-bold text-yellow-600">
                                <?= count($deliveryStats['retenciones']) ?>
                            </h3>
                        </div>
                        <div class="stat-icon" style="background: #FEF3C7; color: #D97706;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="modern-card p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-3" style="color: var(--primary-dark);">
                        <i class="fas fa-search" style="color: var(--primary-blue);"></i>
                        Revisiones Pendientes
                    </h2>
                    <div class="flex items-center justify-center space-x-8">
                        <div class="text-center">
                            <div class="text-4xl font-bold" style="color: var(--primary-blue);">
                                <?= number_format($data['revisiones_pendientes']) ?>
                            </div>
                            <p class="text-sm text-gray-500">Pendientes de revisión</p>
                        </div>
                    </div>
                    <div class="text-center mt-6">
                        <a href="/timeControl/public/revisiones" class="btn-modern btn-primary">
                            <i class="fas fa-search"></i> Ver Revisiones Pendientes
                        </a>
                    </div>
                </div>

                <div class="modern-card overflow-hidden">
                    <div class="p-5" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);">
                        <h2 class="text-lg font-bold text-white flex items-center gap-3">
                            <i class="fas fa-clipboard-list"></i> Últimas Validaciones
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="modern-table w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">Máquina</th>
                                    <th class="text-left">Item</th>
                                    <th class="text-left hidden md:table-cell">JT/WO</th>
                                    <th class="text-left">Tipo</th>
                                    <th class="text-right">Cant.</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php
                                $entregas = array_slice($data['entregas_validadas'], 0, 5);
                                if (!empty($entregas)) :
                                    foreach ($entregas as $delivery) : ?>
                                        <tr class="hover:bg-blue-50 text-sm transition-colors duration-200">
                                            <td class="px-4 py-3 font-medium" style="color: var(--primary-dark);">
                                                <?= htmlspecialchars($delivery['nombre_maquina']) ?>
                                            </td>
                                            <td class="px-4 py-3"><?= htmlspecialchars($delivery['item']) ?></td>
                                            <td class="px-4 py-3 hidden md:table-cell"><?= htmlspecialchars($delivery['jtWo']) ?></td>
                                            <td class="px-4 py-3">
                                                <span class="badge-modern <?= $delivery['tipo_boton'] == 'final_produccion' ? 'badge-final' : 'badge-parcial' ?>">
                                                    <i class="fas <?= $delivery['tipo_boton'] == 'final_produccion' ? 'fa-flag-checkered' : 'fa-hourglass-half' ?> mr-1"></i>
                                                    <?= $delivery['tipo_boton'] == 'final_produccion' ? 'Final' : 'Parcial' ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-medium">
                                                <?= number_format($delivery['cantidad_produccion'], 1) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else : ?>
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-inbox text-4xl mb-2" style="color: var(--primary-blue);"></i>
                                                No hay entregas validadas.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-t bg-gray-50 text-center" style="border-color: var(--border-light);">
                        <a href="/timeControl/public/accion" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Ver todas las entregas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <footer class="mt-8 text-center text-gray-500 text-xs py-6">
                <p>© <?= date('Y') ?> Panel de Control QA - Todos los derechos reservados</p>
                <p class="mt-1">Última actualización: <?= date('d/m/Y H:i:s') ?></p>
            </footer>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/qa/dashboard.js"></script>
    
</body>
</html>
