<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes Diarias - <?= htmlspecialchars($maquina_nombre) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/operador/ordenes_diarias.css">
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header-card">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-2">
                        <i class="bi bi-calendar-check text-primary"></i>
                        Órdenes de Producción
                    </h2>
                    <p class="text-muted mb-0">
                        <strong><?= htmlspecialchars($maquina_nombre) ?></strong> | 
                        Operador: <?= htmlspecialchars($user->nombre) ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="calendario-navegacion justify-content-md-end">
                        <button class="btn btn-outline-secondary" onclick="cambiarFecha(-1)">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </button>
                        <input type="date" 
                               class="form-control fecha-selector" 
                               id="fecha" 
                               value="<?= htmlspecialchars($fecha_actual) ?>"
                               onchange="cambiarFecha(0)">
                        <button class="btn btn-outline-secondary" onclick="cambiarFecha(1)">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </button>
                        <button class="btn btn-outline-primary" onclick="cambiarFecha(0, true)">
                            <i class="bi bi-calendar-today"></i> Hoy
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Columna principal: Órdenes -->
            <div class="col-lg-8">
                <div class="bg-white rounded-3 p-4 shadow-sm">
                    <h4 class="mb-4">
                        <i class="bi bi-list-check"></i>
                        Órdenes del día: <?= date('d/m/Y', strtotime($fecha_actual)) ?>
                    </h4>

                    <?php if (empty($ordenes)): ?>
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h5>No hay órdenes programadas para este día</h5>
                            <p>Selecciona otra fecha o consulta con tu supervisor</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($ordenes as $orden): ?>
                            <?php
                                $progreso = 0;
                                if ($orden['cantidad_meta'] > 0) {
                                    $progreso = ($orden['cantidad_producida'] / $orden['cantidad_meta']) * 100;
                                }
                                $prioridadClass = 'prioridad-' . strtolower($orden['prioridad']);
                                $estadoColor = $orden['estado_dia'] === 'en_proceso' ? 'success' : 'secondary';
                                $estadoTexto = $orden['estado_dia'] === 'en_proceso' ? 'En Proceso' : 'Pendiente';
                            ?>
                            <div class="orden-card <?= $prioridadClass ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge badge-prioridad bg-<?= $estadoColor ?>">
                                                <?= htmlspecialchars($estadoTexto) ?>
                                            </span>
                                            <span class="badge badge-prioridad bg-<?= 
                                                $orden['prioridad'] === 'urgente' ? 'danger' : 
                                                ($orden['prioridad'] === 'alta' ? 'warning' : 
                                                ($orden['prioridad'] === 'media' ? 'info' : 'success')) 
                                            ?>">
                                                <i class="bi bi-flag-fill"></i> 
                                                <?= ucfirst($orden['prioridad']) ?>
                                            </span>
                                        </div>

                                        <h5 class="mb-2">
                                            <i class="bi bi-file-earmark-text"></i>
                                            JOB: <?= htmlspecialchars($orden['job_id']) ?>
                                        </h5>

                                        <div class="row g-2 mb-3">
                                            <div class="col-sm-6">
                                                <small class="text-muted d-block">Item:</small>
                                                <strong><?= htmlspecialchars($orden['item']) ?></strong>
                                            </div>
                                            <div class="col-sm-6">
                                                <small class="text-muted d-block">Cliente:</small>
                                                <strong><?= htmlspecialchars($orden['cliente']) ?></strong>
                                            </div>
                                            <div class="col-sm-6">
                                                <small class="text-muted d-block">PO:</small>
                                                <strong><?= htmlspecialchars($orden['po']) ?></strong>
                                            </div>
                                            <div class="col-sm-6">
                                                <small class="text-muted d-block">Tamaño:</small>
                                                <strong><?= htmlspecialchars($orden['tamano']) ?></strong>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1">Progreso del día:</small>
                                            <div class="progress-custom">
                                                <div class="progress-bar-custom" style="width: <?= min($progreso, 100) ?>%">
                                                    <?= number_format($progreso, 1) ?>%
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <?= number_format($orden['cantidad_producida'], 2) ?> / 
                                                <?= number_format($orden['cantidad_meta'], 2) ?> 
                                                <?= htmlspecialchars($orden['unidad_medida']) ?>
                                            </small>
                                        </div>

                                        <?php if (!empty($orden['notas_planificador'])): ?>
                                            <div class="alert alert-info py-2 px-3 mb-0">
                                                <i class="bi bi-info-circle"></i>
                                                <small><?= nl2br(htmlspecialchars($orden['notas_planificador'])) ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="stat-card mb-3">
                                            <i class="bi bi-bullseye"></i>
                                            <div class="stat-number">
                                                <?= number_format($orden['cantidad_meta'], 0) ?>
                                            </div>
                                            <small>Meta del Día</small>
                                            <div class="mt-2">
                                                <small><?= htmlspecialchars($orden['unidad_medida']) ?></small>
                                            </div>
                                        </div>

                                        <form method="POST" action="/timeControl/public/seleccionar_orden">
                                            <input type="hidden" name="distribucion_id" value="<?= $orden['distribucion_id'] ?>">
                                            <button type="submit" class="btn btn-trabajar w-100">
                                                <i class="bi bi-play-circle"></i>
                                                <?= $orden['estado_dia'] === 'en_proceso' ? 'Continuar' : 'Iniciar' ?>
                                            </button>
                                        </form>

                                        <button class="btn btn-outline-secondary btn-sm w-100 mt-2" 
                                                onclick="verDetalle(<?= $orden['orden_id'] ?>)">
                                            <i class="bi bi-eye"></i> Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Columna lateral: Resumen semanal -->
            <div class="col-lg-4">
                <div class="bg-white rounded-3 p-4 shadow-sm mb-4">
                    <h5 class="mb-3">
                        <i class="bi bi-graph-up"></i>
                        Estadísticas del Día
                    </h5>
                    <?php
                        $totalOrdenes = count($ordenes);
                        $totalMeta = 0;
                        $totalProducido = 0;
                        $ordenesEnProceso = 0;
                        
                        foreach ($ordenes as $orden) {
                            $totalMeta += $orden['cantidad_meta'];
                            $totalProducido += $orden['cantidad_producida'];
                            if ($orden['estado_dia'] === 'en_proceso') {
                                $ordenesEnProceso++;
                            }
                        }
                        
                        $progresoGeneral = $totalMeta > 0 ? ($totalProducido / $totalMeta) * 100 : 0;
                    ?>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-0 text-primary"><?= $totalOrdenes ?></h3>
                                <small class="text-muted">Órdenes Total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-0 text-success"><?= $ordenesEnProceso ?></h3>
                                <small class="text-muted">En Proceso</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-2">Progreso General</small>
                                <div class="progress-custom">
                                    <div class="progress-bar-custom" style="width: <?= min($progresoGeneral, 100) ?>%">
                                        <?= number_format($progresoGeneral, 1) ?>%
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <?= number_format($totalProducido, 2) ?> / <?= number_format($totalMeta, 2) ?> lb
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen Semanal -->
                <div class="resumen-semanal">
                    <h5 class="mb-3">
                        <i class="bi bi-calendar-week"></i>
                        Resumen de la Semana
                    </h5>
                    
                    <?php if (empty($resumen_semanal)): ?>
                        <p class="text-muted text-center">No hay datos disponibles</p>
                    <?php else: ?>
                        <?php foreach ($resumen_semanal as $dia): ?>
                            <?php
                                $esDiaActual = $dia['fecha'] === $fecha_actual;
                                $diaNombre = date('l', strtotime($dia['fecha']));
                                $diasSemana = [
                                    'Monday' => 'Lunes',
                                    'Tuesday' => 'Martes',
                                    'Wednesday' => 'Miércoles',
                                    'Thursday' => 'Jueves',
                                    'Friday' => 'Viernes',
                                    'Saturday' => 'Sábado',
                                    'Sunday' => 'Domingo'
                                ];
                                $diaTexto = $diasSemana[$diaNombre] ?? $diaNombre;
                            ?>
                            <div class="dia-resumen <?= $esDiaActual ? 'border-primary border-3' : '' ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= $diaTexto ?></strong>
                                        <small class="d-block text-muted">
                                            <?= date('d/m/Y', strtotime($dia['fecha'])) ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="d-block text-primary">
                                            <?= $dia['total_ordenes'] ?> orden(es)
                                        </strong>
                                        <small class="text-muted">
                                            <?= number_format($dia['cantidad_total'], 0) ?> <?= htmlspecialchars($dia['unidad_medida']) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Botones de acción -->
                <div class="mt-4 d-grid gap-2">
                    <a href="/timeControl/public/datos_trabajo_maquina" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Cambiar Máquina
                    </a>
                    <button class="btn btn-outline-danger" onclick="confirmLogout()">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles -->
    <div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detalleModalLabel">
                        <i class="bi bi-info-circle"></i> Detalles de la Orden
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalleContenido">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="./assets/js/operador/ordenes_diarias.js "></script>
</body>
</html>