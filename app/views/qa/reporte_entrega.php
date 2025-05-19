<?php
// filepath: c:\xampp\htdocs\timeControl\app\views\qa\reporte_entrega.php
/**
 * Vista de Reporte de Entrega: muestra las entregas de producción validadas provenientes de Acción QA
 * Espera recibir $data['entregas_validadas']
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Reporte de Entregas">
    <title>Reporte de Entrega - QA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>
<body class="bg-gray-50">
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>
    <main class="lg:ml-72 p-6 md:p-8 min-h-screen bg-gray-50">
        <div class="container mx-auto pt-14 lg:pt-4">
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-5 flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-green-700 flex items-center">
                        <i class="fas fa-file-alt mr-3"></i>Reporte de Entrega
                    </h1>
                    <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">
                        Total: <?php echo isset($data['entregas_validadas']) ? count($data['entregas_validadas']) : 0; ?> entregas
                    </span>
                </div>
            </div>
            <!-- Filtros profesionales -->
            <form method="get" class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-4 items-end" id="filtrosEntrega">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>" class="border rounded px-2 py-1 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>" class="border rounded px-2 py-1 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Máquina</label>
                    <input type="text" name="maquina" value="<?= htmlspecialchars($_GET['maquina'] ?? '') ?>" placeholder="Nombre máquina" class="border rounded px-2 py-1 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Item</label>
                    <input type="text" name="item" value="<?= htmlspecialchars($_GET['item'] ?? '') ?>" placeholder="P/N" class="border rounded px-2 py-1 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Job Ticket (JTWO)</label>
                    <input type="text" name="jtwo" value="<?= htmlspecialchars($_GET['jtwo'] ?? '') ?>" placeholder="JTWO" class="border rounded px-2 py-1 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">PO</label>
                    <input type="text" name="po" value="<?= htmlspecialchars($_GET['po'] ?? '') ?>" placeholder="PO" class="border rounded px-2 py-1 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Cliente</label>
                    <input type="text" name="cliente" value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>" placeholder="Cliente" class="border rounded px-2 py-1 text-sm">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i> Filtrar
                    </button>
                    <a href="/timeControl/public/reporte-entrega" class="ml-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg flex items-center transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i> Limpiar
                    </a>
                </div>
            </form>
            <!-- Agrupación por máquina -->
            <div class="space-y-6">
            <?php
            // Filtrado profesional en PHP (puede ser reemplazado por SQL en el controlador)
            $entregas = $data['entregas_validadas'] ?? [];
            // Filtros
            if (!empty($_GET['fecha_desde'])) {
                $entregas = array_filter($entregas, function($e) {
                    return date('Y-m-d', strtotime($e['fecha_validacion'])) >= $_GET['fecha_desde'];
                });
            }
            if (!empty($_GET['fecha_hasta'])) {
                $entregas = array_filter($entregas, function($e) {
                    return date('Y-m-d', strtotime($e['fecha_validacion'])) <= $_GET['fecha_hasta'];
                });
            }
            if (!empty($_GET['maquina'])) {
                $entregas = array_filter($entregas, function($e) {
                    return stripos($e['nombre_maquina'], $_GET['maquina']) !== false;
                });
            }
            if (!empty($_GET['item'])) {
                $entregas = array_filter($entregas, function($e) {
                    return stripos($e['item'], $_GET['item']) !== false;
                });
            }
            if (!empty($_GET['jtwo'])) {
                $entregas = array_filter($entregas, function($e) {
                    return stripos($e['jtWo'], $_GET['jtwo']) !== false;
                });
            }
            if (!empty($_GET['po'])) {
                $entregas = array_filter($entregas, function($e) {
                    return stripos($e['po'], $_GET['po']) !== false;
                });
            }
            if (!empty($_GET['cliente'])) {
                $entregas = array_filter($entregas, function($e) {
                    return stripos($e['cliente'], $_GET['cliente']) !== false;
                });
            }
            // Agrupar por máquina
            $agrupadas = [];
            foreach ($entregas as $e) {
                $agrupadas[$e['nombre_maquina']][] = $e;
            }
            ?>
            <?php if (empty($agrupadas)): ?>
                <div class="text-center py-12 text-gray-500 bg-white rounded-xl shadow-sm">
                    <i class="fas fa-box-open text-6xl mb-4"></i>
                    <p class="text-xl">No hay entregas validadas registradas</p>
                </div>
            <?php else: ?>
                <?php foreach ($agrupadas as $maquina => $entregasMaquina): ?>
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-2xl shadow-inner p-6 mb-8 border border-blue-200">
                        <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-cogs"></i> Máquina: <span class="text-blue-600"><?= htmlspecialchars($maquina) ?></span>
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <?php foreach ($entregasMaquina as $entrega): ?>
                            <div class="bg-white rounded-2xl shadow-lg border border-blue-100 p-7 flex flex-col gap-4 group hover:shadow-2xl transition-all duration-200 relative hover:border-blue-400">
                                <!-- Estado y fecha -->
                                <span class="absolute top-5 right-6 text-xs flex flex-col items-end gap-1">
                                    <?php if (!empty($entrega['estado'])): ?>
                                        <span class="flex items-center gap-1 px-2 py-1 rounded-full font-bold uppercase tracking-wide text-xs
                                            <?php
                                                switch (strtolower($entrega['estado'])) {
                                                    case 'impresa':
                                                        echo 'bg-blue-600 text-white';
                                                        break;
                                                    case 'guardada':
                                                        echo 'bg-gray-300 text-gray-700';
                                                        break;
                                                    default:
                                                        echo 'bg-gray-200 text-gray-700';
                                                }
                                            ?>"
                                            title="Estado de la entrega">
                                            <i class="fas
                                                <?php
                                                    switch (strtolower($entrega['estado'])) {
                                                        case 'impresa': echo 'fa-print'; break;
                                                        case 'guardada': echo 'fa-save'; break;
                                                        default: echo 'fa-info-circle';
                                                    }
                                                ?>"></i>
                                            <?= htmlspecialchars(ucfirst($entrega['estado'])) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="flex items-center gap-1 text-gray-400" title="Fecha de validación">
                                        <i class="fas fa-calendar-alt text-green-600"></i>
                                        <?= date('d/m/Y H:i', strtotime($entrega['fecha_validacion'])) ?>
                                    </span>
                                </span>
                                <div class="flex flex-col gap-3 mt-2">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-tag text-gray-400 text-xl"></i>
                                        <span class="text-sm font-bold text-gray-500">Item:</span>
                                        <span class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($entrega['item']) ?></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-alt text-yellow-500 text-xl"></i>
                                        <span class="text-sm font-bold text-yellow-600">JTWO:</span>
                                        <span class="text-base text-yellow-900"><?= htmlspecialchars($entrega['jtWo']) ?></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-invoice text-indigo-500 text-xl"></i>
                                        <span class="text-sm font-bold text-indigo-600">PO:</span>
                                        <span class="text-base text-indigo-900"><?= htmlspecialchars($entrega['po'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-user text-pink-400 text-xl"></i>
                                        <span class="text-sm font-bold text-pink-600">Cliente:</span>
                                        <span class="text-base text-pink-900"><?= htmlspecialchars($entrega['cliente'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-cogs text-blue-400 text-xl"></i>
                                        <span class="text-sm font-bold text-blue-600">Máquina:</span>
                                        <span class="text-base text-blue-900"><?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A') ?></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-cubes text-green-700 text-xl"></i>
                                        <span class="text-sm font-bold text-green-700">Cantidad:</span>
                                        <span class="text-xl font-extrabold text-green-900"><?= number_format($entrega['cantidad_produccion'], 2, '.', ',') ?> Lb</span>
                                    </div>
                                </div>
                                <div class="flex flex-row gap-2 mt-6 justify-center">
                                    <a href="/timeControl/public/reporte-entrega/detalle/<?= $entrega['id'] ?>" class="px-6 py-3 bg-blue-700 hover:bg-blue-900 text-white rounded-xl shadow-lg text-lg font-bold flex items-center gap-2 transition-colors duration-200 border-2 border-blue-800 uppercase tracking-wider">
                                        <i class="fas fa-eye fa-lg"></i> Ver Detalle
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
    </main>
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
</body>
</html>
