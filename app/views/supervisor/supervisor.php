<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Supervisor">
    <title>Control de Supervisor - Atlantic KPG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/timeControl/public/assets/css/supervisor/supervisor.css">
</head>

<body>
    <?php include __DIR__ . "/../layouts/sidebarSupervisor.php"; ?>

    <main class="lg:ml-72 transition-all duration-300 min-h-screen">
         
        <div class="modern-header p-6 mb-6">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="logo-container">
                        <img src="/timeControl/public/assets/img/logoprint.png" alt="Control Tiempos Logo" class="logo-image">
                        <div>
                            <nav class="breadcrumb-modern mb-2" aria-label="Breadcrumb">
                                <a href="/timeControl/public/supervisor" class="hover:underline">Inicio</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Dashboard Supervisor</span>
                            </nav>
                            <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard - Área <?= htmlspecialchars($area) ?>
                            </h1>
                            <p class="text-white/80 mt-1 text-sm">Control de Operaciones, Producción y Validaciones</p>
                        </div>
                    </div>
                    <div class="text-white/90 text-sm">
                        <i class="fas fa-calendar-alt mr-2"></i><?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 md:px-6 pb-8">
              
            <div class="modern-tabs mb-6 fade-in">
                <ul class="flex flex-wrap gap-2">
                    <li>
                        <a href="#operaciones-abiertas" class="tab-link" id="tab-operaciones">
                            <i class="fas fa-tasks mr-2"></i>Operaciones Abiertas
                        </a>
                    </li>
                    <li>
                        <a href="#produccion-scrap" class="tab-link" id="tab-produccion">
                            <i class="fas fa-industry mr-2"></i>Producción y Scrap
                        </a>
                    </li>
                    <li>
                        <a href="#validaciones" class="tab-link" id="tab-validaciones">
                            <i class="fas fa-check-circle mr-2"></i>Validaciones
                        </a>
                    </li>
                </ul>
            </div>

             
            <section id="operaciones-abiertas" class="tab-content hidden fade-in">
                <div class="modern-card p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-3" style="color: var(--primary-dark);">
                        <i class="fas fa-tasks" style="color: var(--primary-blue);"></i>
                        Operaciones Abiertas
                    </h2>

                    <!-- Formulario de filtros -->
                    <form method="post" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="codigo_empleado" class="block text-sm font-medium mb-2" style="color: var(--primary-dark);">Empleado:</label>
                                <select class="modern-input w-full" id="codigo_empleado" name="codigo_empleado">
                                    <option value="">Seleccione un empleado</option>
                                    <?php foreach ($empleados as $empleado) : ?>
                                        <option value="<?= htmlspecialchars($empleado['codigo_empleado']) ?>" <?= isset($filters['codigo_empleado']) && $filters['codigo_empleado'] === $empleado['codigo_empleado'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($empleado['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="tipo_boton" class="block text-sm font-medium mb-2" style="color: var(--primary-dark);">Tipo Botón:</label>
                                <select class="modern-input w-full" id="tipo_boton" name="tipo_boton">
                                    <option value="">Seleccione una Operación</option>
                                    <?php foreach ($botones as $boton) : ?>
                                        <option value="<?= htmlspecialchars($boton['tipo_boton']) ?>" <?= isset($filters['tipo_boton']) && $filters['tipo_boton'] === $boton['tipo_boton'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($boton['tipo_boton']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="maquina" class="block text-sm font-medium mb-2" style="color: var(--primary-dark);">Máquina:</label>
                                <select class="modern-input w-full" id="maquina" name="maquina">
                                    <option value="">Seleccione una máquina</option>
                                    <?php foreach ($maquinas as $maquina) : ?>
                                        <option value="<?= htmlspecialchars($maquina['id']) ?>" <?= isset($filters['maquina']) && $filters['maquina'] === $maquina['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($maquina['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="btn-modern btn-primary w-full">
                                    <i class="fas fa-filter"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="modern-table w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">Nombre</th>
                                    <th class="text-left">JT/WO</th>
                                    <th class="text-left">Operación</th>
                                    <th class="text-left">Item</th>
                                    <th class="text-left">Máquina</th>
                                    <th class="text-left">Tiempo</th>
                                    <th class="text-left">Descripción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php if (empty($operaciones_abiertas)) : ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-2 block" style="color: var(--primary-blue);"></i>
                                            No hay operaciones abiertas
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($operaciones_abiertas as $row) : ?>
                                        <?php
                                        $fechaRegistro = new \DateTime($row['fecha_registro']);
                                        $fechaActual = new \DateTime();
                                        $intervalo = $fechaRegistro->diff($fechaActual);
                                        $tiempoTranscurrido = $intervalo->format('%H:%I:%S');
                                        $tiempoTranscurridoClass = $intervalo->h >= 8 ? 'text-red-600' : ($intervalo->h >= 6 ? 'text-yellow-600' : 'text-green-600');
                                        $rowClass = $row['tipo_boton'] === 'Contratiempos' ? 'bg-red-50' : '';
                                        ?>
                                        <tr class="<?= $rowClass ?>" data-start-time="<?= strtotime($row['fecha_registro']) ?>">
                                            <td class="px-4 py-3 font-medium"><?= htmlspecialchars($row['nombre']) ?></td>
                                            <td class="px-4 py-3"><?= htmlspecialchars($row['jtWo']) ?></td>
                                            <td class="px-4 py-3"><?= htmlspecialchars($row['tipo_boton']) ?></td>
                                            <td class="px-4 py-3"><?= htmlspecialchars($row['item']) ?></td>
                                            <td class="px-4 py-3"><?= htmlspecialchars($row['nombre_maquina']) ?></td>
                                            <td class="px-4 py-3"><span class="tiempo font-bold <?= $tiempoTranscurridoClass ?>"><?= $tiempoTranscurrido ?></span></td>
                                            <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($row['descripcion']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

             
            <section id="produccion-scrap" class="tab-content hidden fade-in">
                <div class="modern-card p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-3" style="color: var(--primary-dark);">
                        <i class="fas fa-industry" style="color: var(--primary-blue);"></i>
                        Producción y Scrap por Máquina
                    </h2>

                    <form method="POST" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="item" class="block text-sm font-medium mb-2" style="color: var(--primary-dark);">Filtrar por Item:</label>
                                <input type="text" id="item" name="item" class="modern-input w-full" value="<?= htmlspecialchars($filters['item'] ?? '') ?>" placeholder="Item">
                            </div>
                            <div>
                                <label for="jtWo" class="block text-sm font-medium mb-2" style="color: var(--primary-dark);">Filtrar por JT/WO:</label>
                                <input type="text" id="jtWo" name="jtWo" class="modern-input w-full" value="<?= htmlspecialchars($filters['jtWo'] ?? '') ?>" placeholder="JT/WO">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="btn-modern btn-primary w-full">
                                    <i class="fas fa-filter"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if (!empty($produccion['produccion_por_maquina_empleado'])) : ?>
                        <?php foreach ($produccion['produccion_por_maquina_empleado'] as $maquina_id => $maquina) : ?>
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2" style="color: var(--primary-blue);">
                                    <i class="fas fa-cogs"></i><?= htmlspecialchars($maquina['nombre_maquina']) ?>
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="modern-table w-full">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Código</th>
                                                <th class="text-left">Empleado</th>
                                                <th class="text-left">Producción</th>
                                                <th class="text-left">Scrap</th>
                                                <th class="text-left">Hora</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                            <?php foreach ($maquina['empleados'] as $empleado_codigo => $empleado) : ?>
                                                <tr>
                                                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($empleado_codigo) ?></td>
                                                    <td class="px-4 py-3"><?= htmlspecialchars($empleado['nombre_empleado']) ?></td>
                                                    <td class="px-4 py-3">
                                                        <span class="badge-modern badge-produccion">
                                                            <?= number_format((float)$empleado['total_produccion'], 2, '.', ',') ?> lb.
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="badge-modern badge-scrap">
                                                            <?= number_format((float)$empleado['total_scrap'], 2, '.', ',') ?> lb.
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 font-bold" style="color: var(--primary-blue);">
                                                        <?= !empty($empleado['fecha_registro']) ? date('g:i A', strtotime($empleado['fecha_registro'])) : 'N/A' ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="modern-card p-6 mt-6" style="background: linear-gradient(135deg, rgba(91, 164, 207, 0.1) 0%, rgba(74, 141, 184, 0.1) 100%);">
                            <h3 class="text-lg font-bold mb-3" style="color: var(--primary-dark);">
                                <i class="fas fa-chart-bar mr-2" style="color: var(--primary-blue);"></i>Totales Generales
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="stat-icon" style="background: #D1FAE5; color: #059669;">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Producción</p>
                                        <p class="text-2xl font-bold" style="color: #059669;">
                                            <?= number_format($produccion['totalProduccion'], 2, '.', ',') ?> lb.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="stat-icon" style="background: #FEE2E2; color: #DC2626;">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Scrap</p>
                                        <p class="text-2xl font-bold" style="color: #DC2626;">
                                            <?= number_format($produccion['totalScrap'], 2, '.', ',') ?> lb.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-6xl mb-4 block" style="color: var(--primary-blue); opacity: 0.3;"></i>
                            <p class="text-gray-600 font-semibold">No hay datos de producción disponibles</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            
            <section id="validaciones" class="tab-content hidden fade-in">
                <!-- Estadísticas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                    <div class="stat-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Entregas Pendientes</p>
                                <h3 class="text-3xl font-bold" style="color: var(--primary-blue);">
                                    <?= $validaciones['stats']['pendientes'] ?? 0 ?>
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
                                    <?= $validaciones['stats']['produccion_pendiente'] ?? 0 ?>
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
                                    <?= $validaciones['stats']['scrap_pendiente'] ?? 0 ?>
                                </h3>
                            </div>
                            <div class="stat-icon" style="background: #FEE2E2; color: #DC2626;">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="modern-card p-4 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--primary-dark);" for="filtroFecha">Fecha</label>
                            <input type="date" id="filtroFecha" class="modern-input w-full" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--primary-dark);" for="filtroItem">Item</label>
                            <input type="text" id="filtroItem" class="modern-input w-full" placeholder="Item" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--primary-dark);" for="filtroJtWo">JT/WO</label>
                            <input type="text" id="filtroJtWo" class="modern-input w-full" placeholder="JT/WO" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--primary-dark);" for="filtroPO">PO</label>
                            <input type="text" id="filtroPO" class="modern-input w-full" placeholder="PO" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--primary-dark);" for="filtroCliente">Cliente</label>
                            <input type="text" id="filtroCliente" class="modern-input w-full" placeholder="Cliente" />
                        </div>
                        <div>
                            <button id="btnLimpiarFiltros" class="btn-modern btn-secondary w-full">
                                <i class="fas fa-times"></i>Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de entregas -->
                <div class="modern-card overflow-hidden">
                    <div class="p-5" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);">
                        <h3 class="text-lg font-bold text-white flex items-center gap-3">
                            <i class="fas fa-clipboard-check"></i>
                            Entregas Pendientes de Validación
                        </h3>
                    </div>

                    <?php
                    $entregas_produccion = $validaciones['entregas_produccion'] ?? [];
                    $entregas_scrap = $validaciones['entregas_scrap'] ?? [];

                    $maquinas = [];
                    foreach ($entregas_produccion as $entrega) {
                        $maquina = $entrega['nombre_maquina'];
                        if (!isset($maquinas[$maquina])) $maquinas[$maquina] = [];
                        $key = $entrega['fecha_registro'] . '_' . $entrega['jtWo'] . '_' . $entrega['item'];
                        if (!isset($maquinas[$maquina][$key])) {
                            $maquinas[$maquina][$key] = [
                                'fecha_registro' => $entrega['fecha_registro'],
                                'item' => $entrega['item'],
                                'jtWo' => $entrega['jtWo'],
                                'po' => $entrega['po'] ?? '',
                                'cliente' => $entrega['cliente'] ?? '',
                                'tipo_boton' => $entrega['tipo_boton'],
                                'entregas' => []
                            ];
                        }
                        $maquinas[$maquina][$key]['entregas'][] = [
                            'id' => $entrega['id'],
                            'tipo' => 'produccion',
                            'cantidad' => $entrega['cantidad_produccion']
                        ];
                    }
                    foreach ($entregas_scrap as $entrega) {
                        $maquina = $entrega['nombre_maquina'];
                        if (!isset($maquinas[$maquina])) $maquinas[$maquina] = [];
                        $key = $entrega['fecha_registro'] . '_' . $entrega['jtWo'] . '_' . $entrega['item'];
                        if (!isset($maquinas[$maquina][$key])) {
                            $maquinas[$maquina][$key] = [
                                'fecha_registro' => $entrega['fecha_registro'],
                                'item' => $entrega['item'],
                                'jtWo' => $entrega['jtWo'],
                                'po' => $entrega['po'] ?? '',
                                'cliente' => $entrega['cliente'] ?? '',
                                'tipo_boton' => $entrega['tipo_boton'],
                                'entregas' => []
                            ];
                        }
                        $maquinas[$maquina][$key]['entregas'][] = [
                            'id' => $entrega['id'],
                            'tipo' => 'scrap',
                            'cantidad' => $entrega['cantidad_scrap'] ?? 0
                        ];
                    }
                    ?>

                    <?php if (empty($maquinas)) : ?>
                        <div class="flex flex-col items-center justify-center p-12">
                            <div class="stat-icon mb-4" style="background: rgba(91, 164, 207, 0.1); color: var(--primary-blue); width: 80px; height: 80px; font-size: 32px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4 class="text-xl font-semibold mb-2" style="color: var(--primary-dark);">¡Todo al día!</h4>
                            <p class="text-gray-500 text-center max-w-md">
                                No hay entregas pendientes de validación en este momento.
                            </p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($maquinas as $nombre_maquina => $entregas) : ?>
                            <div class="border-b" style="border-color: var(--border-light);">
                                <div class="px-6 py-3 font-bold flex items-center gap-2" style="background: rgba(91, 164, 207, 0.05); color: var(--primary-blue);">
                                    <i class="fas fa-cogs"></i> <?= htmlspecialchars($nombre_maquina) ?>
                                </div>
                                
                                <!-- Desktop Table -->
                                <div class="hidden md:block overflow-x-auto">
                                    <table class="w-full" id="tablaEntregas">
                                        <thead style="background: var(--bg-light);">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-sm font-medium" style="color: var(--primary-dark);">
                                                    <i class="fas fa-calendar-alt mr-1" style="color: var(--primary-blue);"></i> Fecha/Hora
                                                </th>
                                                <th class="px-4 py-3 text-left text-sm font-medium" style="color: var(--primary-dark);">
                                                    <i class="fas fa-tag mr-1" style="color: var(--primary-blue);"></i> Item
                                                </th>
                                                <th class="px-4 py-3 text-left text-sm font-medium" style="color: var(--primary-dark);">
                                                    <i class="fas fa-file-alt mr-1" style="color: var(--primary-blue);"></i> JT/WO
                                                </th>
                                                <th class="px-4 py-3 text-left text-sm font-medium" style="color: var(--primary-dark);">
                                                    <i class="fas fa-barcode mr-1" style="color: var(--primary-blue);"></i> PO
                                                </th>
                                                <th class="px-4 py-3 text-left text-sm font-medium" style="color: var(--primary-dark);">
                                                    <i class="fas fa-user mr-1" style="color: var(--primary-blue);"></i> Cliente
                                                </th>
                                                <th class="px-4 py-3 text-left text-sm font-medium" style="color: var(--primary-dark);">
                                                    <i class="fas fa-info-circle mr-1" style="color: var(--primary-blue);"></i> Tipo
                                                </th>
                                                <th class="px-4 py-3 text-left text-sm font-medium" style="color: var(--primary-dark);">
                                                    <i class="fas fa-cubes mr-1" style="color: var(--primary-blue);"></i> Detalle
                                                </th>
                                                <th class="px-4 py-3 text-center text-sm font-medium" style="color: var(--primary-dark);">
                                                    <i class="fas fa-tools mr-1" style="color: var(--primary-blue);"></i> Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                            <?php foreach ($entregas as $entrega) : ?>
                                                <tr class="entrega-row border-b" style="border-color: var(--border-light);"
                                                    data-fecha="<?= date('Y-m-d', strtotime($entrega['fecha_registro'])) ?>"
                                                    data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                    data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                    data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                    data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                                    <td class="px-4 py-3 text-sm">
                                                        <div class="font-medium" style="color: var(--primary-dark);"><?= date('d/m/Y', strtotime($entrega['fecha_registro'])) ?></div>
                                                        <div class="text-gray-500 text-xs"><?= date('H:i', strtotime($entrega['fecha_registro'])) ?></div>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($entrega['item']) ?></td>
                                                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($entrega['po']) ?></td>
                                                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($entrega['cliente']) ?></td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <span class="badge-modern <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'badge-final' : 'badge-parcial' ?>">
                                                            <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'FINAL' : 'PARCIAL' ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <div class="space-y-2">
                                                            <?php foreach ($entrega['entregas'] as $detalle) : ?>
                                                                <div class="badge-modern <?= $detalle['tipo'] == 'scrap' ? 'badge-scrap' : 'badge-produccion' ?>">
                                                                    <span class="font-medium"><?= ucfirst($detalle['tipo']) ?>:</span>
                                                                    <span class="font-bold ml-1"><?= number_format($detalle['cantidad'], 2) ?> lb.</span>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <div class="space-y-2">
                                                            <?php foreach ($entrega['entregas'] as $index => $detalle) : ?>
                                                                <div class="flex flex-col gap-2 <?= $index > 0 ? 'pt-2 border-t' : '' ?>" style="border-color: var(--border-light);">
                                                                    <button class="btn-review btn-modern btn-secondary text-xs"
                                                                            data-id="<?= $detalle['id'] ?>"
                                                                            data-tipo="<?= $detalle['tipo'] ?>"
                                                                            data-cantidad="<?= $detalle['cantidad'] ?>"
                                                                            data-maquina="<?= htmlspecialchars($nombre_maquina) ?>"
                                                                            data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                                            data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                                            data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                                            data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                                                        <i class="fas fa-search"></i>Revisar
                                                                    </button>
                                                                    <button class="<?= $detalle['tipo'] == 'scrap' ? 'btn-validate-scrap' : 'btn-validate-production' ?> btn-modern btn-success text-xs"
                                                                            data-id="<?= $detalle['id'] ?>"
                                                                            data-tipo="<?= $detalle['tipo'] ?>"
                                                                            data-cantidad="<?= $detalle['cantidad'] ?>"
                                                                            data-maquina="<?= htmlspecialchars($nombre_maquina) ?>"
                                                                            data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                                            data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                                            data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                                            data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                                                        <i class="fas fa-check"></i>Validar
                                                                    </button>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mobile View -->
                                <div class="md:hidden p-3 space-y-3">
                                    <?php foreach ($entregas as $entrega) : ?>
                                        <div class="mobile-table-row"
                                             data-fecha="<?= date('Y-m-d', strtotime($entrega['fecha_registro'])) ?>"
                                             data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                             data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                             data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                             data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                            <div class="mobile-field">
                                                <span class="mobile-field-label"><i class="fas fa-calendar-alt mr-1" style="color: var(--primary-blue);"></i>Fecha/Hora:</span>
                                                <span class="mobile-field-value"><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></span>
                                            </div>
                                            <div class="mobile-field">
                                                <span class="mobile-field-label"><i class="fas fa-tag mr-1" style="color: var(--primary-blue);"></i>Item:</span>
                                                <span class="mobile-field-value font-medium"><?= htmlspecialchars($entrega['item']) ?></span>
                                            </div>
                                            <div class="mobile-field">
                                                <span class="mobile-field-label"><i class="fas fa-file-alt mr-1" style="color: var(--primary-blue);"></i>JT/WO:</span>
                                                <span class="mobile-field-value"><?= htmlspecialchars($entrega['jtWo']) ?></span>
                                            </div>
                                            <div class="mobile-field">
                                                <span class="mobile-field-label"><i class="fas fa-barcode mr-1" style="color: var(--primary-blue);"></i>PO:</span>
                                                <span class="mobile-field-value"><?= htmlspecialchars($entrega['po']) ?></span>
                                            </div>
                                            <div class="mobile-field">
                                                <span class="mobile-field-label"><i class="fas fa-user mr-1" style="color: var(--primary-blue);"></i>Cliente:</span>
                                                <span class="mobile-field-value"><?= htmlspecialchars($entrega['cliente']) ?></span>
                                            </div>
                                            <div class="mobile-field">
                                                <span class="mobile-field-label"><i class="fas fa-info-circle mr-1" style="color: var(--primary-blue);"></i>Tipo:</span>
                                                <span class="badge-modern <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'badge-final' : 'badge-parcial' ?>">
                                                    <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'FINAL' : 'PARCIAL' ?>
                                                </span>
                                            </div>
                                            <div class="mobile-field flex-col items-start">
                                                <span class="mobile-field-label mb-2"><i class="fas fa-cubes mr-1" style="color: var(--primary-blue);"></i>Detalle:</span>
                                                <div class="w-full space-y-2">
                                                    <?php foreach ($entrega['entregas'] as $detalle) : ?>
                                                        <div class="badge-modern <?= $detalle['tipo'] == 'scrap' ? 'badge-scrap' : 'badge-produccion' ?>">
                                                            <span class="font-medium"><?= ucfirst($detalle['tipo']) ?>:</span>
                                                            <span class="font-bold ml-1"><?= number_format($detalle['cantidad'], 2) ?> lb.</span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <div class="mobile-field flex-col items-stretch pt-3 border-t-2" style="border-color: var(--border-light);">
                                                <span class="mobile-field-label mb-2"><i class="fas fa-tools mr-1" style="color: var(--primary-blue);"></i>Acciones:</span>
                                                <div class="flex flex-col gap-2">
                                                    <?php foreach ($entrega['entregas'] as $index => $detalle) : ?>
                                                        <?php if ($index > 0) : ?>
                                                            <span class="mobile-field-label mb-2 mt-3"><i class="fas fa-tools mr-1" style="color: var(--primary-blue);"></i>Acciones <?= ucfirst($detalle['tipo']) ?>:</span>
                                                        <?php endif; ?>
                                                        <button class="btn-review btn-modern btn-secondary w-full"
                                                                data-id="<?= $detalle['id'] ?>"
                                                                data-tipo="<?= $detalle['tipo'] ?>"
                                                                data-cantidad="<?= $detalle['cantidad'] ?>"
                                                                data-maquina="<?= htmlspecialchars($nombre_maquina) ?>"
                                                                data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                                data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                                data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                                data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                                            <i class="fas fa-search"></i>Revisar
                                                        </button>
                                                        <button class="<?= $detalle['tipo'] == 'scrap' ? 'btn-validate-scrap' : 'btn-validate-production' ?> btn-modern btn-success w-full"
                                                                data-id="<?= $detalle['id'] ?>"
                                                                data-tipo="<?= $detalle['tipo'] ?>"
                                                                data-cantidad="<?= $detalle['cantidad'] ?>"
                                                                data-maquina="<?= htmlspecialchars($nombre_maquina) ?>"
                                                                data-item="<?= htmlspecialchars($entrega['item']) ?>"
                                                                data-jtwo="<?= htmlspecialchars($entrega['jtWo']) ?>"
                                                                data-po="<?= htmlspecialchars($entrega['po']) ?>"
                                                                data-cliente="<?= htmlspecialchars($entrega['cliente']) ?>">
                                                            <i class="fas fa-check"></i>Validar
                                                        </button>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Modales (Review y Validation) -->
                <!-- Review Modal -->
                <div id="revisionModal" class="modern-modal fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden transition-opacity duration-300">
                    <div class="modal-content bg-white w-full max-w-md mx-4" role="dialog" aria-labelledby="revisionModalTitle" aria-modal="true">
                        <div class="px-6 py-4 flex justify-between items-center" style="background: var(--primary-blue);">
                            <h2 id="revisionModalTitle" class="text-lg font-bold flex items-center text-white">
                                <i class="fas fa-search mr-2"></i>
                                Revisar Entrega
                            </h2>
                            <button type="button" class="modal-close focus:outline-none text-white hover:text-gray-200 transition-colors" aria-label="Cerrar">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="p-6">
                            <div class="modern-card p-4 mb-5">
                                <h3 class="font-semibold mb-3 flex items-center" style="color: var(--primary-dark);">
                                    <i class="fas fa-info-circle mr-2" style="color: var(--primary-blue);"></i>
                                    Detalles de la entrega:
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div class="p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium" style="color: var(--primary-blue);">Máquina:</span>
                                        <span id="revisionMaquina" class="ml-1"></span>
                                    </div>
                                    <div class="p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium" style="color: var(--primary-blue);">Item:</span>
                                        <span id="revisionItem" class="ml-1"></span>
                                    </div>
                                    <div class="p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium" style="color: var(--primary-blue);">JT/WO:</span>
                                        <span id="revisionJtWo" class="ml-1"></span>
                                    </div>
                                    <div class="p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium" style="color: var(--primary-blue);">Cantidad:</span>
                                        <span id="revisionCantidad" class="ml-1"></span>
                                    </div>
                                    <div class="p-3 rounded col-span-1 sm:col-span-2" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium" style="color: var(--primary-blue);">Tipo:</span>
                                        <span id="revisionTipo" class="ml-1"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5">
                                <label for="notaRevision" class="block font-medium mb-2" style="color: var(--primary-dark);">Motivo de corrección (opcional)</label>
                                <textarea class="modern-input w-full"
                                          id="notaRevision"
                                          rows="3"
                                          placeholder="Escriba aquí sus observaciones sobre la cantidad..."></textarea>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                                <button type="button" class="modal-close btn-modern btn-secondary">
                                    <i class="fas fa-times"></i>Cancelar
                                </button>
                                <button type="button" class="btn-modern btn-primary" id="submitRevisionBtn">
                                    <i class="fas fa-paper-plane"></i>Enviar Revisión
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validation Modal -->
                <div id="validateModal" class="modern-modal fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden transition-opacity duration-300">
                    <div class="modal-content bg-white w-full max-w-md mx-4" role="dialog" aria-labelledby="validateModalTitle" aria-modal="true">
                        <div class="px-6 py-4 flex justify-between items-center" style="background: #10B981;">
                            <h2 id="validateModalTitle" class="text-lg font-bold flex items-center text-white">
                                <i class="fas fa-check-circle mr-2"></i>
                                Validar Entrega
                            </h2>
                            <button type="button" class="modal-close focus:outline-none text-white hover:text-gray-200 transition-colors" aria-label="Cerrar">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="p-6">
                            <div class="modern-card p-4 mb-5">
                                <h3 class="font-semibold mb-3 flex items-center" style="color: var(--primary-dark);">
                                    <i class="fas fa-info-circle mr-2 text-green-600"></i>
                                    Detalles de la entrega a validar:
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div class="p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium text-green-600">Máquina:</span>
                                        <span id="validacionMaquina" class="ml-1"></span>
                                    </div>
                                    <div class="p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium text-green-600">Item:</span>
                                        <span id="validacionItem" class="ml-1"></span>
                                    </div>
                                    <div class="p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium text-green-600">JT/WO:</span>
                                        <span id="validacionJtWo" class="ml-1"></span>
                                    </div>
                                    <div class="p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium text-green-600">Cantidad:</span>
                                        <span id="validacionCantidad" class="ml-1"></span>
                                    </div>
                                    <div class="p-3 rounded col-span-1 sm:col-span-2" style="background: var(--bg-light); border: 1px solid var(--border-light);">
                                        <span class="font-medium text-green-600">Tipo:</span>
                                        <span id="validacionTipo" class="ml-1"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5">
                                <label for="comentarioValidacion" class="block font-medium mb-2" style="color: var(--primary-dark);">Comentario (opcional)</label>
                                <textarea class="modern-input w-full"
                                          id="comentarioValidacion"
                                          data-id="data-comentario"
                                          rows="3"
                                          placeholder="Escriba aquí sus observaciones sobre la entrega..."></textarea>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                                <button type="button" class="modal-close btn-modern btn-secondary">
                                    <i class="fas fa-times"></i>Cancelar
                                </button>
                                <button type="button" class="btn-modern btn-success" id="submitValidationBtn">
                                    <i class="fas fa-check"></i>Validar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/appValidacionSupervisor.js"></script>
    <script src="assets/js/supervisor/supervisor.js"></script>
    <script src="assets/js/supervisor/paginacion.js"></script>



   
</body>
</html>