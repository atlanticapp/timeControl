<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Destinos de Destrucción">
    <title>Control de Calidad - Destinos de Destrucción</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary-blue: #5BA4CF;
            --primary-dark: #4A4A4A;
            --primary-blue-dark: #4A8DB8;
            --bg-light: #F8FAFB;
            --border-light: #E5E9EB;
            --danger-red: #DC2626;
        }

        body {
            background: var(--bg-light);
        }

        .modern-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            box-shadow: 0 4px 20px rgba(91, 164, 207, 0.15);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-image {
            width: 80px;
            height: auto;
            filter: brightness(0) invert(1);
        }

        .modern-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
        }

        .modern-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .modern-table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .modern-table thead {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
        }

        .modern-table thead th {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .modern-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--border-light);
        }

        .modern-table tbody tr:hover {
            background: rgba(91, 164, 207, 0.05);
        }

        .btn-modern {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-secondary {
            background: white;
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
        }

        .btn-secondary:hover {
            background: var(--primary-blue);
            color: white;
        }

        .badge-modern {
            padding: 0.375rem 0.875rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        .badge-validado {
            background: #D1FAE5;
            color: #059669;
        }

        .breadcrumb-modern {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            font-size: 0.875rem;
        }

        .breadcrumb-modern a {
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.2s;
        }

        .breadcrumb-modern a:hover {
            color: white;
        }

        .modern-input,
        .modern-select {
            border: 1px solid var(--border-light);
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 0.875rem;
        }

        .modern-input:focus,
        .modern-select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(91, 164, 207, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 16px 16px 0 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . "/../../layouts/sidebarQa.php"; ?>

    <main class="lg:ml-72 p-6 md:p-8 transition-all duration-300 min-h-screen">
        <div class="modern-header p-6 mb-6">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="logo-container">
                        <img src="/timeControl/public/assets/img/logoprint.png" alt="Control Tiempos Logo" class="logo-image">
                        <div>
                            <nav class="breadcrumb-modern mb-2" aria-label="Breadcrumb">
                                <a href="/timeControl/public/dashboard" class="hover:underline">Inicio</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <a href="/timeControl/public/qa" class="hover:underline">Panel QA</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Destinos de Destrucción</span>
                            </nav>
                            <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-trash-alt"></i>
                                Destinos de Destrucción
                            </h1>
                            <p class="text-white/80 mt-1 text-sm">Control de Destinos para Material Retenido - Destrucción</p>
                        </div>
                    </div>
                    <div class="text-white/90 text-sm">
                        <i class="fas fa-calendar-alt mr-2"></i><?= date('d/m/Y') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 md:px-6 pb-8">
            
            <div class="modern-card p-6 mb-6">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-3" style="color: var(--primary-dark);">
                    <i class="fas fa-filter" style="color: var(--primary-blue);"></i>
                    Filtros de Búsqueda
                </h2>
                <div class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroFecha">Fecha</label>
                        <input type="date" id="filtroFecha" class="modern-input" />
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroItem">Item</label>
                        <input type="text" id="filtroItem" class="modern-input" placeholder="Item" />
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroJtWo">JT/WO</label>
                        <input type="text" id="filtroJtWo" class="modern-input" placeholder="JT/WO" />
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm mb-1" for="filtroMotivo">Motivo</label>
                        <input type="text" id="filtroMotivo" class="modern-input" placeholder="Motivo" />
                    </div>
                    <div>
                        <button id="btnLimpiarFiltros" class="btn-modern btn-secondary">
                            <i class="fas fa-eraser mr-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <div class="modern-card overflow-hidden">
                <div class="p-5 modal-header">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-clipboard-list mr-3"></i>Listado de Destinos - Destrucción
                    </h3>
                    <div class="badge-modern badge-validado mt-2">
                        <i class="fas fa-layer-group mr-1"></i>
                        Total Destinos: <span class="total-count"><?php echo count($data['destinos'] ?? []); ?></span>
                    </div>
                </div>

                <?php if (empty($data['destinos'] ?? [])): ?>
                    <div class="text-center py-12 text-gray-500 fade-in">
                        <i class="fas fa-circle-xmark text-6xl mb-4" style="color: var(--primary-blue);"></i>
                        <p class="text-xl">No hay destinos de destrucción registrados</p>
                    </div>
                <?php else: ?>
                    <?php
                    // Agrupar destinos por máquina
                    $maquinas = [];
                    foreach ($data['destinos'] as $destino) {
                        $maquina = $destino['nombre_maquina'] ?? 'Sin Máquina';
                        if (!isset($maquinas[$maquina])) {
                            $maquinas[$maquina] = [];
                        }
                        $maquinas[$maquina][] = $destino;
                    }
                    ?>

                    <div class="overflow-x-auto">
                        <?php foreach ($maquinas as $nombre_maquina => $destinos): ?>
                            <div class="border-b border-gray-200 maquina-group fade-in">
                                <div class="bg-gray-50 px-4 py-3 font-bold text-base" style="color: var(--primary-blue);">
                                    <i class="fas fa-cogs mr-2"></i> <?= htmlspecialchars($nombre_maquina) ?>
                                </div>
                                <table class="modern-table w-full">
                                    <thead>
                                        <tr>
                                            <th class="text-left"><i class="fas fa-calendar-alt mr-2"></i> Fecha/Hora</th>
                                            <th class="text-left"><i class="fas fa-cogs mr-2"></i> Máquina</th>
                                            <th class="text-left"><i class="fas fa-tag mr-2"></i> Item</th>
                                            <th class="text-left"><i class="fas fa-file-alt mr-2"></i> JT/WO</th>
                                            <th class="text-right"><i class="fas fa-cubes mr-2"></i> Cantidad</th>
                                            <th class="text-left"><i class="fas fa-exclamation-circle mr-2"></i> Motivo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($destinos as $destino): ?>
                                            <tr class="destino-row fade-in"
                                                data-fecha="<?= date('Y-m-d', strtotime($destino['fecha_registro'])) ?>"
                                                data-item="<?= htmlspecialchars($destino['item']) ?>"
                                                data-jtwo="<?= htmlspecialchars($destino['jtWo']) ?>"
                                                data-motivo="<?= htmlspecialchars($destino['motivo']) ?>">
                                                <td class="px-4 py-3 text-sm"><?= date('d/m/Y H:i', strtotime($destino['fecha_registro'])) ?></td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($destino['nombre_maquina'] ?? 'No especificada') ?></td>
                                                <td class="px-4 py-3 font-medium" style="color: var(--primary-dark);"><?= htmlspecialchars($destino['item']) ?></td>
                                                <td class="px-4 py-3">
                                                    <span class="badge-modern badge-validado"><?= htmlspecialchars($destino['jtWo']) ?></span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold">
                                                    <span class="badge-modern badge-validado">
                                                        <?= number_format($destino['cantidad'], 2, '.', ',') ?> Lb
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3"><?= htmlspecialchars($destino['motivo']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <footer class="mt-8 text-center text-gray-500 text-xs py-6">
                <p>© <?= date('Y') ?> Acción QA - Todos los derechos reservados</p>
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
                            timeOut: 2000
                        });
                    }
                })
                .catch(error => console.error('Error al obtener el estado:', error));

            const filtroFecha = document.getElementById('filtroFecha');
            const filtroItem = document.getElementById('filtroItem');
            const filtroJtWo = document.getElementById('filtroJtWo');
            const filtroMotivo = document.getElementById('filtroMotivo');
            const btnLimpiar = document.getElementById('btnLimpiarFiltros');

            function filtrarDestinos() {
                const rows = document.querySelectorAll('.destino-row');
                const fecha = filtroFecha.value;
                const item = filtroItem.value.toLowerCase();
                const jtwo = filtroJtWo.value.toLowerCase();
                const motivo = filtroMotivo.value.toLowerCase();

                rows.forEach(row => {
                    const rowFecha = row.getAttribute('data-fecha');
                    const rowItem = row.getAttribute('data-item').toLowerCase();
                    const rowJtWo = row.getAttribute('data-jtwo').toLowerCase();
                    const rowMotivo = row.getAttribute('data-motivo').toLowerCase();

                    const matchFecha = !fecha || rowFecha === fecha;
                    const matchItem = !item || rowItem.includes(item);
                    const matchJtWo = !jtwo || rowJtWo.includes(jtwo);
                    const matchMotivo = !motivo || rowMotivo.includes(motivo);

                    if (matchFecha && matchItem && matchJtWo && matchMotivo) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Ocultar grupos de máquina sin resultados visibles
                document.querySelectorAll('.maquina-group').forEach(group => {
                    const visibleRows = group.querySelectorAll('.destino-row:not([style*="display: none"])');
                    group.style.display = visibleRows.length > 0 ? '' : 'none';
                });
            }

            filtroFecha.addEventListener('change', filtrarDestinos);
            filtroItem.addEventListener('input', filtrarDestinos);
            filtroJtWo.addEventListener('input', filtrarDestinos);
            filtroMotivo.addEventListener('input', filtrarDestinos);

            btnLimpiar.addEventListener('click', function() {
                filtroFecha.value = '';
                filtroItem.value = '';
                filtroJtWo.value = '';
                filtroMotivo.value = '';
                filtrarDestinos();
            });
        });
    </script>
</body>
</html>