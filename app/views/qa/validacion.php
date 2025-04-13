<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Calidad - Constructor QA">
    <title>Control de Calidad - Constructor(QA)</title>
    <!-- Optimized CSS imports -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body class="bg-light">
    <?php include __DIR__ . "/../layouts/sidebarQa.php"; ?>
    
    <main class="lg:ml-64 p-4 md:p-6 transition-all duration-300 min-h-screen">
        <div class="container-fluid px-0">
            <!-- Header Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center g-0">
                        <div class="col-md-8">
                            <h1 class="h3 fw-bold text-primary mb-0">Sistema de Control de Calidad (QA)</h1>
                            <p class="text-muted mb-0 mt-1">Constructor - Validación de Entregas</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex align-items-center justify-content-md-end">
                                <i class="fas fa-calendar-alt text-primary me-2 fs-5"></i>
                                <div>
                                    <div id="current-date" class="small text-muted"></div>
                                    <div id="current-time" class="fs-5 fw-semibold"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-pills nav-fill mb-4 bg-white shadow-sm rounded p-2" id="mainTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active d-flex align-items-center justify-content-center" id="produccion-tab" data-bs-toggle="tab" data-bs-target="#produccion" type="button" role="tab" aria-controls="produccion" aria-selected="true">
                        <i class="fas fa-clipboard-list me-2"></i>
                        <span>Pendientes de Producción</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center justify-content-center" id="scrap-tab" data-bs-toggle="tab" data-bs-target="#scrap" type="button" role="tab" aria-controls="scrap" aria-selected="false">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span>Pendientes de Scrap</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="mainTabContent">
                <!-- Production Tab -->
                <div class="tab-pane fade show active" id="produccion" role="tabpanel" aria-labelledby="produccion-tab">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary bg-gradient text-white py-3 d-flex align-items-center">
                            <i class="fas fa-tasks me-2 fs-5"></i>
                            <h3 class="mb-0 h5 fw-bold">Entregas Pendientes de Validación - Producción</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($data['entregas_produccion'])): ?>
                                <div class="alert alert-info m-3 mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle fs-4 me-3"></i>
                                        <span>No hay entregas pendientes de validación en Producción.</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0"><i class="fas fa-user text-primary me-1"></i> Operador</th>
                                                <th class="border-0"><i class="fas fa-cogs text-primary me-1"></i> Máquina</th>
                                                <th class="border-0"><i class="fas fa-tag text-primary me-1"></i> Item</th>
                                                <th class="border-0"><i class="fas fa-file-alt text-primary me-1"></i> JT/WO</th>
                                                <th class="border-0"><i class="fas fa-info-circle text-primary me-1"></i> Tipo</th>
                                                <th class="border-0"><i class="fas fa-calendar-alt text-primary me-1"></i> Fecha/Hora</th>
                                                <th class="border-0 text-end"><i class="fas fa-cubes text-primary me-1"></i> Cantidad</th>
                                                <th class="border-0 text-center"><i class="fas fa-tools text-primary me-1"></i> Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['entregas_produccion'] as $entrega): ?>
                                                <tr>
                                                    <td class="align-middle"><?= htmlspecialchars($entrega['nombre_empleado']) ?></td>
                                                    <td class="align-middle"><?= htmlspecialchars($entrega['nombre_maquina']) ?></td>
                                                    <td class="align-middle fw-medium"><?= htmlspecialchars($entrega['item']) ?></td>
                                                    <td class="align-middle"><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                    <td class="align-middle">
                                                        <span class="badge rounded-pill <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                                            <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'Final' : 'Parcial' ?>
                                                        </span>
                                                    </td>
                                                    <td class="align-middle small"><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                                    <td class="text-end align-middle fw-bold"><?= number_format($entrega['cantidad'], 0, ',', '.') ?> <small class="text-muted">unid.</small></td>
                                                    <td class="text-center align-middle">
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-primary btn-review" data-id="<?= $entrega['id'] ?>" data-tipo="produccion" title="Revisar">
                                                                <i class="fas fa-search"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-success btn-validate-production" data-id="<?= $entrega['id'] ?>" title="Validar">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </div>
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
                    <div class="card shadow-sm">
                        <div class="card-header bg-danger bg-gradient text-white py-3 d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                            <h3 class="mb-0 h5 fw-bold">Entregas Pendientes - Scrap</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($data['entregas_scrap'])): ?>
                                <div class="alert alert-info m-3 mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle fs-4 me-3"></i>
                                        <span>No hay entregas pendientes de validación en Scrap.</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0"><i class="fas fa-user text-danger me-1"></i> Operador</th>
                                                <th class="border-0"><i class="fas fa-cogs text-danger me-1"></i> Máquina</th>
                                                <th class="border-0"><i class="fas fa-tag text-danger me-1"></i> Item</th>
                                                <th class="border-0"><i class="fas fa-file-alt text-danger me-1"></i> JT/WO</th>
                                                <th class="border-0"><i class="fas fa-info-circle text-danger me-1"></i> Tipo</th>
                                                <th class="border-0"><i class="fas fa-calendar-alt text-danger me-1"></i> Fecha/Hora</th>
                                                <th class="border-0 text-end"><i class="fas fa-cubes text-danger me-1"></i> Cantidad</th>
                                                <th class="border-0 text-center"><i class="fas fa-tools text-danger me-1"></i> Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['entregas_scrap'] as $entrega): ?>
                                                <tr>
                                                    <td class="align-middle"><?= htmlspecialchars($entrega['nombre_empleado']) ?></td>
                                                    <td class="align-middle"><?= htmlspecialchars($entrega['nombre_maquina']) ?></td>
                                                    <td class="align-middle fw-medium"><?= htmlspecialchars($entrega['item']) ?></td>
                                                    <td class="align-middle"><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                    <td class="align-middle">
                                                        <span class="badge rounded-pill <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                                            <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'Final' : 'Parcial' ?>
                                                        </span>
                                                    </td>
                                                    <td class="align-middle small"><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                                    <td class="text-end align-middle fw-bold"><?= number_format($entrega['cantidad'], 0, ',', '.') ?> <small class="text-muted">unid.</small></td>
                                                    <td class="text-center align-middle">
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-primary btn-review" data-id="<?= $entrega['id'] ?>" data-tipo="scrap" title="Revisar">
                                                                <i class="fas fa-search"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-success btn-validate-scrap" data-id="<?= $entrega['id'] ?>" data-entrega="<?= $entrega['cantidad'] ?>" title="Validar">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </div>
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
            <div id="toastMessage" class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 fs-5"></i>
                        <span id="toastBody"></span>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Review Modal -->
        <div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="revisionModalLabel"><i class="fas fa-search me-2"></i>Revisar Entrega</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info d-flex">
                            <i class="fas fa-info-circle mt-1 me-3 fs-5"></i>
                            <div>Envíe una nota opcional para revisar la cantidad reportada.</div>
                        </div>
                        <div class="mb-3">
                            <label for="notaRevision" class="form-label fw-medium">Nota para producción (opcional)</label>
                            <textarea class="form-control" id="notaRevision" rows="3" placeholder="Escriba aquí sus observaciones sobre la cantidad..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="submitRevisionBtn">
                            <i class="fas fa-paper-plane me-1"></i>Enviar Revisión
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation Modal -->
        <div class="modal fade" id="validateModal" tabindex="-1" aria-labelledby="validateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="validateModalLabel"><i class="fas fa-check-circle me-2"></i>Validar Entrega</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success d-flex">
                            <i class="fas fa-check-circle mt-1 me-3 fs-5"></i>
                            <div>Al validar esta entrega, se registrará como completa en el sistema.</div>
                        </div>
                        <div class="mb-3">
                            <label for="comentarioValidacion" class="form-label fw-medium">Comentario (opcional)</label>
                            <textarea class="form-control" id="comentarioValidacion" data-id="data-comentario" rows="3" placeholder="Escriba aquí sus observaciones sobre la cantidad..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="submitValidation">
                            <i class="fas fa-check me-1"></i>Validar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="assets/js/appValidacion.js"></script>
</body>

</html>