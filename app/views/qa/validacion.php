<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Calidad - Constructor(Qa)</title>
    <!-- Consolidated CSS libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/styleQa.css">
</head>

<body>
    <div class="container mt-4">
        <div class="header-container animate__animated animate__fadeIn p-3 mb-4 text-center text-md-start row align-items-center">
            <div class="col-md-8">
                <h1 class="header-title mb-0">Sistema de Control de Calidad (QA)</h1>
                <p class="mb-0 mt-2 text-muted">Constructor - Validacion de Entregas</p>
            </div>
            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                <div class="time-display">
                    <i class="fas fa-clock me-2"></i>
                    <span id="current-date"></span><br>
                    <span id="current-time" class="fs-5"></span>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4 animate__animated animate__fadeIn" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="produccion-tab" data-bs-toggle="tab" data-bs-target="#produccion" type="button" role="tab" aria-controls="produccion" aria-selected="true">
                    <i class="fas fa-clipboard-list me-2"></i>Pendientes de Producción
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="scrap-tab" data-bs-toggle="tab" data-bs-target="#scrap" type="button" role="tab" aria-controls="scrap" aria-selected="false">
                    <i class="fas fa-check-double me-2"></i>Pendientes de Scrap
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content animate__animated animate__fadeIn" id="myTabContent">
            <!-- Producción Tab -->
            <div class="tab-pane fade show active" id="produccion" role="tabpanel" aria-labelledby="produccion-tab">
                <div class="card">
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="mb-0"><i class="fas fa-tasks me-2"></i>Entregas Pendientes de Validación - Producción</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['entregas_produccion'])): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No hay entregas pendientes de validación en Producción.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i> Operador</th>
                                            <th><i class="fas fa-cogs me-1"></i> Máquina</th>
                                            <th><i class="fas fa-tag me-1"></i> Item</th>
                                            <th><i class="fas fa-file-alt me-1"></i> JT/WO</th>
                                            <th><i class="fas fa-info-circle me-1"></i> Tipo</th>
                                            <th><i class="fas fa-calendar-alt me-1"></i> Fecha/Hora</th>
                                            <th><i class="fas fa-cubes me-1"></i>Cantidad Producción</th>
                                            <th><i class="fas fa-tools me-1"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['entregas_produccion'] as $entrega): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($entrega['nombre_empleado']) ?></td>
                                                <td><?= htmlspecialchars($entrega['nombre_maquina']) ?></td>
                                                <td><?= htmlspecialchars($entrega['item']) ?></td>
                                                <td><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                <td>
                                                    <span class="badge <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'badge-final' : 'badge-parcial' ?>">
                                                        <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'Final' : 'Parcial' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                                <td class="text-end fw-bold"><?= number_format($entrega['cantidad'], 0, ',', '.') ?> <small>unid.</small></td>
                                                <td>
                                                    <button class="btn btn-action btn-review me-1" data-id="<?= $entrega['id'] ?>" data-tipo="produccion">
                                                        <i class="fas fa-search me-1"></i> Revisar
                                                    </button>
                                                    <button class="btn btn-action btn-validate-production" data-id="<?= $entrega['id'] ?>">
                                                        <i class="fas fa-check me-1"></i> Validar
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
            </div>

            <!-- Scrap Tab -->
            <div class="tab-pane fade" id="scrap" role="tabpanel" aria-labelledby="scrap-tab">
                <div class="card">
                    <div class="card-header bg-success text-white py-3">
                        <h3 class="mb-0"><i class="fas fa-check-double me-2"></i>Entregas Pendientes - Scrap</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['entregas_scrap'])): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No hay entregas pendientes de validación en Scrap.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i> Operador</th>
                                            <th><i class="fas fa-cogs me-1"></i> Máquina</th>
                                            <th><i class="fas fa-tag me-1"></i> Item</th>
                                            <th><i class="fas fa-file-alt me-1"></i> JT/WO</th>
                                            <th><i class="fas fa-info-circle me-1"></i> Tipo</th>
                                            <th><i class="fas fa-calendar-alt me-1"></i> Fecha/Hora</th>
                                            <th><i class="fas fa-cubes me-1"></i> Cantidad Scrap</th>
                                            <th><i class="fas fa-tools me-1"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['entregas_scrap'] as $entrega): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($entrega['nombre_empleado']) ?></td>
                                                <td><?= htmlspecialchars($entrega['nombre_maquina']) ?></td>
                                                <td><?= htmlspecialchars($entrega['item']) ?></td>
                                                <td><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                <td>
                                                    <span class="badge <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'badge-final' : 'badge-parcial' ?>">
                                                        <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'Final' : 'Parcial' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                                <td class="text-end fw-bold"><?= number_format($entrega['cantidad'], 0, ',', '.') ?> <small>unid.</small></td>
                                                <td>
                                                    <button class="btn btn-action btn-review me-1" data-id="<?= $entrega['id'] ?>" data-tipo="scrap">
                                                        <i class="fas fa-search me-1"></i> Revisar
                                                    </button>
                                                    <button class="btn btn-action btn-validate-scrap" data-id="<?= $entrega['id'] ?>" data-entrega="<?= $entrega['cantidad'] ?>">
                                                        <i class="fas fa-check me-1"></i> Validar
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
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="toastMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div id="toastBody" class="toast-body"></div>
        </div>
    </div>

    <!-- Modal para Revisar -->
    <div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="revisionModalLabel">Revisar Entrega</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Envíe una nota opcional para revisar la cantidad reportada.
                    </div>
                    <div class="mb-3">
                        <label for="notaRevision" class="form-label">Nota para producción (opcional)</label>
                        <textarea class="form-control" id="notaRevision" rows="3" placeholder="Escriba aquí sus observaciones sobre la cantidad..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="submitRevisionBtn">
                        <i class="fas fa-paper-plane me-1"></i>Enviar Revisión
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Validar -->
    <div class="modal fade" id="validateModal" tabindex="-1" aria-labelledby="validateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validateModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Al validar esta entrega, se registrará como completa en el sistema.
                    </div>
                    <div class="mb-3">
                        <label for="comentarioValidacion" class="form-label">Comentario (opcional)</label>
                        <textarea class="form-control" id="comentarioValidacion" data-id="data-comentario" rows="3" placeholder="Escriba aquí sus observaciones sobre la cantidad..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="submitValidation">
                        <i class="fas fa-check me-1"></i>Validar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/appValidacion.js"></script>


</body>

</html>