<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Tiempos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="assets/css/styleControl.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="public/assets/css/operador/control.css?v=<?php echo time(); ?>">

</head>

<body>
    <aside class="fixed top-0 left-0 w-72 h-full bg-teal-800 text-white shadow-lg transition-all duration-300 lg:block hidden">
    <div class="p-6">
        <nav>
            <ul>
                <li class="mb-2">
                    <a href="/timeControl/public/logout" class="flex items-center px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors" onclick="return confirm('¿Estás seguro de cerrar sesión?')">
                        <i class="fas fa-sign-out-alt mr-3"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
    
    <div class="container">
        <!-- Banner de Notificación de Correcciones -->
        <?php if (isset($mostrar_correcciones) && $mostrar_correcciones): ?>
        <div class="alert alert-notification alert-warning d-flex align-items-center gap-3 mb-4 mt-3">
            <div class="flex-shrink-0">
                <i class="bi bi-exclamation-triangle-fill fs-1 text-warning correction-badge"></i>
            </div>
            <div class="flex-grow-1">
                <h4 class="alert-heading mb-1">
                    <i class="bi bi-bell-fill me-2"></i>¡Atención! Tienes correcciones pendientes
                </h4>
                <p class="mb-0">Hay <?= count($correcciones_pendientes) ?> corrección(es) pendiente(s) que requieren tu atención.</p>
            </div>
            <div class="flex-shrink-0">
                <button type="button" class="btn btn-warning px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#correccionesModal">
                    <i class="bi bi-pencil-square me-2"></i>Ver Correcciones (<?= count($correcciones_pendientes) ?>)
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Logo y Menú de Usuario -->
        <img src="./assets/img/logoContr.png" alt="Logo de la empresa" class="logo">
        <div class="user-menu">
            <table>
                <tr>
                    <th>Bienvenido:</th>
                    <td><?php echo htmlspecialchars($data['nombre']); ?>!</td>
                </tr>
                <tr>
                    <th>Job Ticket:</th>
                    <td><?php echo htmlspecialchars($data['jtWo']); ?></td>
                </tr>
                <tr>
                    <th>Maquina:</th>
                    <td><?php echo htmlspecialchars($maquina); ?></td>
                </tr>
                <tr>
                    <th>Item:</th>
                    <td><?php echo htmlspecialchars($data['item']); ?></td>
                </tr>
                <tr>
                    <th>Orden de Compra:</th>
                    <td><?php echo htmlspecialchars($data['po'] ?? ''); ?></td>
                </tr>
                <tr>
                    <th>Cliente:</th>
                    <td><?php echo htmlspecialchars($data['cliente'] ?? ''); ?></td>
                </tr>
            </table>
        </div>

        <center>
            <h1>Control de Tiempos - <?php echo htmlspecialchars($area); ?></h1>
        </center>

        <!-- Botones de Control -->
        <div class="buttons">
            <!-- Formulario para Preparación -->
            <form id="makeReadyForm" action="/timeControl/public/registrar" method="post" onsubmit="return confirmMakeReady()">
                <input type="hidden" name="tipo_boton" value="Preparación">
                <button type="submit" id="makeReadyButton" <?php if ($data['active_button_id'] === 'Preparación') echo 'class="active-button"'; ?>>Preparación</button>
            </form>

            <!-- Formulario para Producción -->
            <form id="goodCopyForm" action="/timeControl/public/registrar" method="post" onsubmit="return confirmGoodCopy()">
                <input type="hidden" name="tipo_boton" value="Producción">
                <button type="submit" id="goodCopyButton" <?php if ($active_button_id === 'Producción') echo 'class="active-button"'; ?>>Producción</button>
            </form>

            <!-- Formulario para Contratiempos -->
            <form id="badCopyForm" action="/timeControl/public/registrar" method="post" onsubmit="return validateBadCopyForm()">
                <input type="hidden" name="tipo_boton" value="Contratiempos">
                <button type="submit" id="badCopyButton" <?php if ($active_button_id === 'Contratiempos') echo 'class="active-button"'; ?>>Contratiempos</button>
                <select name="badCopy" id="badCopy" required>
                    <option value="">Seleccionar Contratiempos</option>
                    <?php if (!empty($bad_copy)): ?>
                        <?php foreach ($bad_copy as $row): ?>
                            <option value="<?= htmlspecialchars($row['descripcion']) ?>"><?= htmlspecialchars($row['descripcion']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay contratiempos disponibles</option>
                    <?php endif; ?>
                </select>
            </form>

            <!-- Formulario para Velocidad -->
            <form id="velocidadForm" class="velForm" action="/timeControl/public/saveVelocidad" method="post">
                <input type="hidden" name="tipo_boton" value="Velocidad">
                <input type="number" style="font-size: 20px;" name="velocidadProduccion" id="velocidadProduccion" placeholder="Velocidad Producción" required>
                <button type="submit">Asignar Velocidad</button>
            </form>

            <!-- Formulario para Fin de Producción -->
            <form id="finForm" action="/timeControl/public/registrar" method="post" onsubmit="return validateFinalProduction()">
                <input type="hidden" name="tipo_boton" value="final_produccion">
                <button type="button" onclick="toggleFinalProductionInput()" id="finButton">Fin</button>
                <div class="finalProductionInput" id="finalProductionInput" style="display: none;">
                    <label for="finalProductionValue" class="input-label">Producción <span class="unit">(Lb)</span>:</label>
                    <input type="number" name="finalProductionValue" id="finalProductionValue" placeholder="Cantidad producida" step="0.01" inputmode="decimal">

                    <label for="finalScraptAmount" class="input-label">Scrap <span class="unit">(Lb)</span>:</label>
                    <input type="number" name="finalScraptAmount" id="finalScraptAmount" placeholder="Cantidad de scrap" step="0.01" inputmode="decimal">

                    <button type="submit" id="finProdSubmit">Registrar</button>
                </div>
            </form>

            <!-- Formulario para Parcial -->
            <form id="parcialForm" action="/timeControl/public/registrar" method="post" onsubmit="return validateParcial()">
                <input type="hidden" name="tipo_boton" value="Parcial">
                <button type="button" onclick="toggleParcialInput()" id="parcialButton">Entrega Parcial</button>
                <div class="parcialInput" id="parcialInput" style="display: none;">
                    <label for="parcialProductionValue" class="input-label">Producción <span class="unit">(Lb)</span>:</label>
                    <input type="number" name="parcialProductionValue" id="parcialProductionValue" placeholder="Cantidad producida" step="0.01" inputmode="decimal">

                    <label for="parcialScraptAmount" class="input-label">Scrap <span class="unit">(Lb)</span>:</label>
                    <input type="number" name="parcialScraptAmount" id="parcialScraptAmount" placeholder="Cantidad de scrap" step="0.01" inputmode="decimal">

                    <button type="submit" id="parcialSubmit">Registrar</button>
                </div>
            </form>

            <!-- Resumen de Entrega Parcial -->
            <div class="historial">
                <h2>Resumen de Entrega Parcial</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Total Producción</th>
                            <th>Total Scrapt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_produccion = 0;
                        $total_scrapt = 0;
                        foreach ($historial as $registro) {
                            $total_produccion += $registro['cantidad_produccion'];
                            $total_scrapt += $registro['cantidad_scrapt'];
                        }
                        ?>
                        <tr>
                            <td id="totalProduccion"><strong><?php echo $total_produccion; ?></strong></td>
                            <td id="totalScrapt"><strong><?php echo $total_scrapt; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección de Comentarios -->
        <div class="comment-section">
            <h2>Comentarios</h2>
            <form class="comment-form" action="/timeControl/public/addComentario" method="post">
                <textarea name="comentario" id="comentario" rows="3" placeholder="Escribe tu comentario aquí..." maxlength="255" required></textarea>
                <button type="submit">Guardar Comentario</button>
            </form>
            
        </div>

        <!-- Tabla de Preparación -->
        <table>
            <thead>
                <tr>
                    <th>Preparación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($preparacion)): ?>
                    <?php foreach ($preparacion as $row): ?>
                        <tr><td><?= htmlspecialchars($row['descripcion']) ?></td></tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td>No hay operaciones disponibles</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <!-- Modal de Correcciones -->
    <div class="modal fade" id="correccionesModal" tabindex="-1" aria-labelledby="correccionesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="correccionesModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>Correcciones Pendientes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <?php if (empty($correcciones_pendientes)): ?>
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                            <div>No hay correcciones pendientes en este momento</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($correcciones_pendientes as $correccion): ?>
                            <?php
                            $esTipoProduccion = $correccion['tipo_cantidad'] === 'produccion';
                            $colorClase = $esTipoProduccion ? 'production' : 'scrap';
                            $iconoTipo = $esTipoProduccion ? 'bi-boxes' : 'bi-trash';
                            $textoTipo = $esTipoProduccion ? 'Producción' : 'Scrap';
                            ?>
                            <div class="card mb-4 correction-card card-<?= $colorClase ?>">
                                <div class="card-header bg-light position-relative py-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div class="d-flex gap-3 align-items-center flex-wrap">
                                            <div class="bg-dark text-white py-2 px-3 rounded-3">
                                                <i class="bi bi-tag-fill me-1"></i>
                                                Item: <?= htmlspecialchars($correccion['item']) ?>
                                            </div>
                                            <div class="bg-secondary text-white py-2 px-3 rounded-3">
                                                <i class="bi bi-file-earmark-text me-1"></i>
                                                JT/WO: <?= htmlspecialchars($correccion['jtWo']) ?>
                                            </div>
                                            <div class="badge badge-<?= $colorClase ?> py-2 px-3 d-flex align-items-center">
                                                <i class="bi <?= $iconoTipo ?> me-1"></i>
                                                <?= $textoTipo ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="d-flex mb-2">
                                                <div class="flex-shrink-0 me-2">
                                                    <i class="bi bi-person-circle text-secondary fs-5"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Solicitado por:</small>
                                                    <strong><?= htmlspecialchars($correccion['qa_nombre']) ?></strong>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-2">
                                                    <i class="bi bi-calendar-event text-secondary fs-5"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Fecha solicitud:</small>
                                                    <strong><?= date('d/m/Y H:i', strtotime($correccion['fecha_solicitud'])) ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="flex-shrink-0 me-2">
                                                    <i class="bi <?= $iconoTipo ?> text-<?= $esTipoProduccion ? 'primary' : 'danger' ?> fs-5"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Tipo:</small>
                                                    <strong class="text-<?= $esTipoProduccion ? 'primary' : 'danger' ?>"><?= $textoTipo ?></strong>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <i class="bi bi-123 text-secondary fs-5"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Cantidad actual:</small>
                                                    <strong>
                                                        <?php if ($esTipoProduccion): ?>
                                                            <?= htmlspecialchars($correccion['cantidad_produccion']) ?>
                                                        <?php else: ?>
                                                            <?= htmlspecialchars($correccion['cantidad_scrapt']) ?>
                                                        <?php endif; ?>
                                                        lb
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-light mb-4">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-2">
                                                <i class="bi bi-chat-quote text-secondary fs-5"></i>
                                            </div>
                                            <div>
                                                <?php if (!empty($correccion['motivo'])): ?>
                                                    <small class="text-muted d-block">Motivo de corrección:</small>
                                                    <div class="mt-1"><?= htmlspecialchars($correccion['motivo']) ?></div>
                                                <?php else: ?>
                                                    <small class="text-muted d-block">Sin motivo especificado</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <form method="POST" action="/timeControl/public/procesarCorreccion" class="correction-form">
                                        <input type="hidden" name="solicitud_id" value="<?= $correccion['solicitud_id'] ?>">
                                        <input type="hidden" name="registro_id" value="<?= $correccion['registro_id'] ?>">
                                        <input type="hidden" name="tipo" value="<?= $correccion['tipo_cantidad'] ?>">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i>Nueva cantidad:
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-<?= $esTipoProduccion ? 'primary' : 'danger' ?> text-white">
                                                        <i class="bi <?= $iconoTipo ?>"></i>
                                                    </span>
                                                    <input type="number" name="cantidad" class="form-control form-control-lg cantidad-input" required min="0" step="0.01">
                                                    <span class="input-group-text">lb</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">
                                                <i class="bi bi-chat-left-text me-1"></i>Comentario (opcional):
                                            </label>
                                            <textarea name="comentario" class="form-control comentario-input" rows="3"
                                                      placeholder="Explique el motivo de la corrección o agregue información adicional..."></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-<?= $esTipoProduccion ? 'primary' : 'danger' ?> btn-lg confirm-correction">
                                                <i class="bi bi-check2-circle me-2"></i>Procesar Corrección
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cerrar
                    </button>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="confirmationModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Corrección
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="fw-bold mb-3">Por favor, confirme los siguientes datos:</p>
                    <div class="alert alert-light">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0 me-2">
                                <i class="bi bi-123 text-dark fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Nueva cantidad:</small>
                                <strong id="confirmQuantity">0</strong> lb
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-2">
                                <i class="bi bi-chat-quote text-dark fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Comentario:</small>
                                <div id="confirmComment" class="mt-1">(Sin comentario)</div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger d-flex align-items-center mt-3 mb-0">
                        <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                        <div>¿Está seguro de procesar esta corrección?</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="submitCorrection">
                        <i class="bi bi-check2-circle me-1"></i>Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/operador/control.js"></script>


    
</body>
</html>