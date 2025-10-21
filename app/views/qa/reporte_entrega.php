
<?php
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/timeControl/public/assets/css/qa/reporte_entrega.css">
</head>
<body>
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>
    <main class="lg:ml-72 p-4 md:p-6 min-h-screen">
        <div class="modern-header p-6 mb-6">
            <div class="container mx-auto max-w-7xl">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="logo-container">
                        <img src="/timeControl/public/assets/img/logoprint.png" alt="Control Tiempos Logo" class="logo-image">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-file-alt"></i> Reporte de Entrega
                            </h1>
                            <p class="text-white/80 mt-1 text-sm">Sistema de Control de Calidad - Entregas</p>
                        </div>
                    </div>
                    <div class="text-white/90 text-sm">
                        <i class="fas fa-calendar-alt mr-2"></i><?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 md:px-6 pb-8 max-w-7xl">
            <!-- Contador de Entregas -->
            <div class="stat-card mb-6 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Entregas</p>
                        <h3 class="text-2xl font-bold" style="color: var(--green-primary);">
                            <?php echo isset($data['entregas_validadas']) ? count($data['entregas_validadas']) : 0; ?>
                        </h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <form method="get" class="modern-card p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="filtrosEntrega">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>" class="modern-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>" class="modern-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Máquina</label>
                    <input type="text" name="maquina" value="<?= htmlspecialchars($_GET['maquina'] ?? '') ?>" placeholder="Nombre máquina" class="modern-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Item</label>
                    <input type="text" name="item" value="<?= htmlspecialchars($_GET['item'] ?? '') ?>" placeholder="P/N" class="modern-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Job Ticket (JTWO)</label>
                    <input type="text" name="jtwo" value="<?= htmlspecialchars($_GET['jtwo'] ?? '') ?>" placeholder="JTWO" class="modern-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">PO</label>
                    <input type="text" name="po" value="<?= htmlspecialchars($_GET['po'] ?? '') ?>" placeholder="PO" class="modern-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Cliente</label>
                    <input type="text" name="cliente" value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>" placeholder="Cliente" class="modern-input">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-modern btn-primary flex-1 justify-center">
                        <i class="fas fa-filter mr-2"></i> Filtrar
                    </button>
                    <a href="/timeControl/public/reporte-entrega" class="btn-modern btn-secondary flex-1 justify-center">
                        <i class="fas fa-times mr-2"></i> Limpiar
                    </a>
                </div>
            </form>

            <!-- Agrupación por máquina -->
            <div class="space-y-6">
                <?php
                $entregas_originales = $data['entregas_validadas'] ?? [];
                $registrosVistos = [];
                $entregas = [];

                foreach ($entregas_originales as $e) {
                    $registroId = $e['registro_id'] ?? $e['id'] ?? ($e['item'] . '_' . $e['jtWo'] . '_' . date('Y-m-d', strtotime($e['fecha_validacion'])));
                    if (!isset($registrosVistos[$registroId])) {
                        $registrosVistos[$registroId] = true;
                        $entregas[] = $e;
                    }
                }

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

                $agrupadas = [];
                foreach ($entregas as $e) {
                    $agrupadas[$e['nombre_maquina']][] = $e;
                }
                ?>

                <?php if (empty($agrupadas)): ?>
                    <div class="modern-card text-center p-12 fade-in">
                        <div class="text-center" style="color: var(--green-primary);">
                            <i class="fas fa-box-open text-5xl mb-4"></i>
                            <h4 class="text-xl font-semibold" style="color: var(--primary-dark);">¡Sin registros!</h4>
                            <p class="text-gray-500 text-center max-w-md mt-2">
                                No hay entregas validadas registradas.
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($agrupadas as $maquina => $entregasMaquina): ?>
                        <div class="machine-section p-6 mb-8 fade-in">
                            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                                <i class="fas fa-cogs" style="color: var(--green-primary);"></i>
                                Máquina: <span style="color: var(--green-dark);"><?= htmlspecialchars($maquina) ?></span>
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($entregasMaquina as $entrega): ?>
                                    <div class="entry-card p-6 relative fade-in">
                                        <span class="absolute top-4 right-4 text-xs flex flex-col items-end gap-1">
                                            <?php if (!empty($entrega['estado'])): ?>
                                                <span class="badge-modern <?php
                                                    switch (strtolower($entrega['estado'])) {
                                                        case 'impresa': echo 'badge-impresa'; break;
                                                        case 'guardada': echo 'badge-guardada'; break;
                                                        default: echo 'badge-default';
                                                    }
                                                ?>" title="Estado de la entrega">
                                                    <i class="fas <?php
                                                        switch (strtolower($entrega['estado'])) {
                                                            case 'impresa': echo 'fa-print'; break;
                                                            case 'guardada': echo 'fa-save'; break;
                                                            default: echo 'fa-info-circle';
                                                        }
                                                    ?> mr-1"></i>
                                                    <?= htmlspecialchars(ucfirst($entrega['estado'])) ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="flex items-center gap-1 text-gray-400" title="Fecha de validación">
                                                <i class="fas fa-calendar-alt" style="color: var(--green-primary);"></i>
                                                <?= date('d/m/Y H:i', strtotime($entrega['fecha_validacion'])) ?>
                                            </span>
                                        </span>
                                        <div class="space-y-3">
                                            <div class="entry-field md:flex items-center gap-3">
                                                <span class="entry-field-label md:inline hidden">Item:</span>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-tag text-gray-400"></i>
                                                    <span class="entry-field-value font-semibold" style="color: var(--primary-dark);"><?= htmlspecialchars($entrega['item']) ?></span>
                                                </div>
                                            </div>
                                            <div class="entry-field md:flex items-center gap-3">
                                                <span class="entry-field-label md:inline hidden">JTWO:</span>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-file-alt text-yellow-500"></i>
                                                    <span class="entry-field-value" style="color: var(--primary-dark);"><?= htmlspecialchars($entrega['jtWo']) ?></span>
                                                </div>
                                            </div>
                                            <div class="entry-field md:flex items-center gap-3">
                                                <span class="entry-field-label md:inline hidden">PO:</span>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-file-invoice text-indigo-500"></i>
                                                    <span class="entry-field-value"><?= htmlspecialchars($entrega['po'] ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                            <div class="entry-field md:flex items-center gap-3">
                                                <span class="entry-field-label md:inline hidden">Cliente:</span>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-user text-pink-400"></i>
                                                    <span class="entry-field-value"><?= htmlspecialchars($entrega['cliente'] ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                            <div class="entry-field md:flex items-center gap-3">
                                                <span class="entry-field-label md:inline hidden">Máquina:</span>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-cogs text-blue-400"></i>
                                                    <span class="entry-field-value"><?= htmlspecialchars($entrega['nombre_maquina'] ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                            <div class="entry-field md:flex items-center gap-3">
                                                <span class="entry-field-label md:inline hidden">Cantidad:</span>
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-cubes" style="color: var(--green-primary);"></i>
                                                    <span class="entry-field-value font-extrabold text-lg" style="color: var(--green-dark);"><?= number_format($entrega['cantidad_produccion'], 2, '.', ',') ?> lb.</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-center mt-4">
                                            <a href="/timeControl/public/reporte-entrega/detalle/<?= $entrega['id'] ?>" class="btn-modern btn-primary uppercase tracking-wider justify-center">
                                                <i class="fas fa-eye"></i> Ver Detalle
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <footer class="mt-8 text-center text-gray-500 text-xs py-6">
                <p>© <?= date('Y') ?> Reporte de Entrega - Todos los derechos reservados</p>
                <p class="mt-1">Última actualización: <?= date('d/m/Y H:i:s') ?></p>
            </footer>
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
                            timeOut: 2000,
                            positionClass: 'toast-bottom-right'
                        });
                    }
                })
                .catch(error => console.error('Error al obtener el estado:', error));

            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = 0;
                setTimeout(() => {
                    el.style.opacity = 1;
                }, 100);
            });
        });
    </script>
</body>
</html>
