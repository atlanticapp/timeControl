<?php
// app/views/planificador/reporte_orden.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Producción - <?= htmlspecialchars($reporte['orden']['job_id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --success: #059669;
            --warning: #f59e0b;
            --danger: #dc2626;
        }

        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-card {
            background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%);
            color: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.2);
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
        }

        .day-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s;
        }

        .day-card:hover {
            border-left-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .day-card.has-production {
            border-left-color: var(--success);
            background: linear-gradient(to right, #f0fdf4 0%, white 100%);
        }

        .day-card.no-production {
            border-left-color: var(--warning);
            background: linear-gradient(to right, #fef3c7 0%, white 100%);
        }

        .production-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            gap: 0.5rem;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .progress-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: bold;
            position: relative;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-weight: 500;
        }

        .detail-value {
            font-weight: 600;
            color: #111827;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .day-card { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Botones -->
        <div class="row no-print mb-3">
            <div class="col-12 d-flex justify-content-between">
                <a href="/timeControl/public/planificador" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Planificador
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="bi bi-printer me-2"></i>Imprimir Reporte
                </button>
            </div>
        </div>

        <!-- Header -->
        <div class="header-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="bi bi-calendar-check me-2"></i>
                        Reporte de Producción Diaria
                    </h1>
                    <h3 class="mb-1">JOB ID: <?= htmlspecialchars($reporte['orden']['job_id']) ?></h3>
                    <p class="mb-0 opacity-90">
                        Item: <?= htmlspecialchars($reporte['orden']['item']) ?> | 
                        Cliente: <?= htmlspecialchars($reporte['orden']['cliente']) ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="production-badge badge-<?= $reporte['orden']['estado'] == 'completada' ? 'success' : 'info' ?>">
                        <i class="bi bi-<?= $reporte['orden']['estado'] == 'completada' ? 'check-circle-fill' : 'clock-history' ?>"></i>
                        <?= ucfirst(str_replace('_', ' ', $reporte['orden']['estado'])) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Resumen General -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="progress-circle mx-auto" style="background: conic-gradient(var(--success) <?= min($reporte['estadisticas']['porcentaje_completado'], 100) ?>%, #e5e7eb 0%);">
                        <div style="background: white; width: 75px; height: 75px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <?= number_format($reporte['estadisticas']['porcentaje_completado'], 1) ?>%
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">Progreso Total</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="text-primary mb-2">
                        <i class="bi bi-target fs-2"></i>
                    </div>
                    <h4 class="mb-1"><?= number_format($reporte['orden']['cantidad_requerida'], 2) ?></h4>
                    <p class="text-muted mb-0"><?= htmlspecialchars($reporte['orden']['unidad_medida'] ?? 'lb') ?> Requeridos</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle fs-2"></i>
                    </div>
                    <h4 class="mb-1 text-success"><?= number_format($reporte['estadisticas']['total_producido_real'], 2) ?></h4>
                    <p class="text-muted mb-0"><?= htmlspecialchars($reporte['orden']['unidad_medida'] ?? 'lb') ?> Producidos</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="text-warning mb-2">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                    </div>
                    <h4 class="mb-1 text-warning"><?= number_format($reporte['estadisticas']['cantidad_faltante'], 2) ?></h4>
                    <p class="text-muted mb-0"><?= htmlspecialchars($reporte['orden']['unidad_medida'] ?? 'lb') ?> Faltantes</p>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-calendar3 me-2"></i>Días Programados</span>
                        <span class="detail-value"><?= $reporte['estadisticas']['dias_programados'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-check2-square me-2"></i>Días con Producción</span>
                        <span class="detail-value text-success"><?= $reporte['estadisticas']['dias_con_produccion'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-clipboard-check me-2"></i>Días Completados</span>
                        <span class="detail-value text-primary"><?= $reporte['estadisticas']['dias_completados'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-trash me-2"></i>Scrap Total</span>
                        <span class="detail-value text-danger"><?= number_format($reporte['estadisticas']['total_scrap_real'], 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producción Diaria -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-4">
            <i class="bi bi-calendar3-week me-2"></i>
            Producción por Día
        </h4>

        <?php if (!empty($reporte['distribucion'])): ?>
            <?php foreach ($reporte['distribucion'] as $dia): ?>
            <div class="day-card <?= $dia['cantidad_producida_dia'] > 0 ? 'has-production' : 'no-production' ?>">
                <div class="row align-items-center">
                    <!-- Fecha y Meta -->
                    <div class="col-md-3">
                        <h5 class="mb-2">
                            <i class="bi bi-calendar-event me-2"></i>
                            <?= $dia['fecha_formatted'] ?>
                        </h5>
                        <p class="text-muted mb-0">
                            <strong>Meta:</strong> <?= number_format($dia['cantidad_meta'], 2) ?> <?= htmlspecialchars($reporte['orden']['unidad_medida'] ?? 'lb') ?>
                        </p>
                    </div>

                    <!-- Producción Real -->
                    <div class="col-md-3">
                        <div class="mb-2">
                            <span class="badge bg-success">
                                <i class="bi bi-box-seam me-1"></i>
                                Producido: <?= number_format($dia['cantidad_producida_dia'], 2) ?> <?= htmlspecialchars($reporte['orden']['unidad_medida'] ?? 'lb') ?>
                            </span>
                        </div>
                        <?php if ($dia['cantidad_scrap_dia'] > 0): ?>
                        <div>
                            <span class="badge bg-danger">
                                <i class="bi bi-trash me-1"></i>
                                Scrap: <?= number_format($dia['cantidad_scrap_dia'], 2) ?> <?= htmlspecialchars($reporte['orden']['unidad_medida'] ?? 'lb') ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Detalles -->
                    <div class="col-md-3">
                        <?php if ($dia['total_cajas'] > 0 || $dia['total_piezas'] > 0 || $dia['total_paletas'] > 0): ?>
                        <div class="small">
                            <div><i class="bi bi-box me-1"></i> <strong>Cajas:</strong> <?= number_format($dia['total_cajas']) ?></div>
                            <div><i class="bi bi-box me-1"></i> <strong>Piezas:</strong> <?= number_format($dia['total_piezas']) ?></div>
                            <div><i class="bi bi-layers me-1"></i> <strong>Paletas:</strong> <?= number_format($dia['total_paletas']) ?></div>
                        </div>
                        <?php else: ?>
                        <div class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            <?= $dia['cantidad_producida_dia'] > 0 ? 'Sin detalles registrados' : 'Sin producción este día' ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Cumplimiento -->
                    <div class="col-md-3 text-end">
                        <div class="mb-2">
                            <h3 class="mb-0 <?= $dia['porcentaje_cumplimiento'] >= 100 ? 'text-success' : ($dia['porcentaje_cumplimiento'] >= 50 ? 'text-warning' : 'text-danger') ?>">
                                <?= number_format($dia['porcentaje_cumplimiento'], 1) ?>%
                            </h3>
                            <small class="text-muted">Cumplimiento</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar <?= $dia['porcentaje_cumplimiento'] >= 100 ? 'bg-success' : ($dia['porcentaje_cumplimiento'] >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                                 style="width: <?= min($dia['porcentaje_cumplimiento'], 100) ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <?php if ($dia['registros_del_dia'] > 0): ?>
                <div class="row mt-3 pt-3 border-top">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="bi bi-file-earmark-text me-1"></i>
                            <strong><?= $dia['registros_del_dia'] ?></strong> registro(s) validado(s)
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="text-muted">
                            <i class="bi bi-person-check me-1"></i>
                            Validado por: <strong><?= htmlspecialchars($dia['validadores_dia'] ?: 'N/A') ?></strong>
                        </small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No hay distribución diaria para esta orden. Crea una distribución primero.
            </div>
        <?php endif; ?>
    </div>
</div>

        <!-- Resumen Final -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="stat-card">
                    <h5 class="mb-3">
                        <i class="bi bi-graph-up me-2"></i>
                        Resumen de Producción
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="detail-row">
                                <span class="detail-label">Total Registros QA:</span>
                                <span class="detail-value"><?= $reporte['estadisticas']['total_registros'] ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-row">
                                <span class="detail-label">Eficiencia de Producción:</span>
                                <span class="detail-value text-success">
                                    <?= number_format(($reporte['estadisticas']['total_producido_real'] / $reporte['orden']['cantidad_requerida']) * 100, 2) ?>%
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-row">
                                <span class="detail-label">% Scrap:</span>
                                <span class="detail-value text-danger">
                                    <?= $reporte['estadisticas']['total_producido_real'] > 0 
                                        ? number_format(($reporte['estadisticas']['total_scrap_real'] / $reporte['estadisticas']['total_producido_real']) * 100, 2) 
                                        : '0.00' ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>