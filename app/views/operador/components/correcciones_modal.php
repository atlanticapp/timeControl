<style>
    :root {
        --production-color: #0d6efd;
        --scrap-color: #dc3545;
    }

    .badge-production {
        background-color: var(--production-color);
        color: white;
    }

    .badge-scrap {
        background-color: var(--scrap-color);
        color: white;
    }

    .card-production {
        border-left: 5px solid var(--production-color);
    }

    .card-scrap {
        border-left: 5px solid var(--scrap-color);
    }

    .alert-notification {
        border-left: 5px solid #ffc107;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .correction-card {
        transition: transform 0.2s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .correction-card:hover {
        transform: translateY(-5px);
    }

    .item-badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    .pending-badge {
        position: absolute;
        top: 15px;
        right: 15px;
    }
</style>
<div class="alert alert-notification d-flex align-items-center gap-3 mb-4">
    <div class="flex-shrink-0">
        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
    </div>
    <div class="flex-grow-1">
        <h4 class="alert-heading mb-1">¡Atención! Tienes correcciones pendientes</h4>
        <p class="mb-0">Hay correcciones pendientes para esta máquina.</p>
    </div>
    <div class="flex-shrink-0">
        <button type="button" class="btn btn-warning px-4" data-bs-toggle="modal" data-bs-target="#correccionesModal">
            <i class="bi bi-pencil-square me-2"></i>Ver Correcciones
        </button>
    </div>
</div>

<!-- Modal de Correcciones -->
<div class="modal fade" id="correccionesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-clipboard-check me-2"></i>Correcciones Pendientes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-3 align-items-center">
                                        <div class="bg-dark text-white py-2 px-3 rounded-3 item-badge">
                                            <i class="bi bi-tag-fill me-1"></i>
                                            Item: <?= htmlspecialchars($correccion['item']) ?>
                                        </div>
                                        <div class="bg-secondary text-white py-2 px-3 rounded-3 item-badge">
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
                                                <input type="number" name="cantidad" class="form-control form-control-lg cantidad-input" required min="0">
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
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Corrección
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.correction-form');
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

        // Para cada formulario de corrección
        forms.forEach(form => {
            const confirmBtn = form.querySelector('.confirm-correction');

            confirmBtn.addEventListener('click', function() {
                const cantidad = form.querySelector('.cantidad-input').value;
                const comentario = form.querySelector('.comentario-input').value;

                document.getElementById('confirmQuantity').textContent = cantidad;
                document.getElementById('confirmComment').textContent = comentario || '(Sin comentario)';

                confirmationModal.show();

                document.getElementById('submitCorrection').onclick = function() {
                    // Mostrar notificación con toastr
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "timeOut": "3000"
                    };

                    toastr.success('La corrección ha sido procesada correctamente');

                    // Enviar el formulario
                    form.submit();
                    confirmationModal.hide();
                };
            });
        });

        // Animación para las tarjetas de corrección
        const correctionCards = document.querySelectorAll('.correction-card');
        correctionCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.boxShadow = '0 8px 16px rgba(0, 0, 0, 0.15)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
            });
        });
    });
</script>