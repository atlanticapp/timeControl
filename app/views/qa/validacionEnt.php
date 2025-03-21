<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Calidad - Constructor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <link rel="stylesheet" href="assets/css/styleQa.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-toast@1.0.1/dist/bootstrap-toast.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="header-container animate__animated animate__fadeIn p-3 mb-4 text-center text-md-start row align-items-center">
            <div class="col-md-8">
                <h1 class="header-title mb-0">Sistema de Control de Calidad</h1>
                <p class="mb-0 mt-2 text-light">Constructor - División de Etiquetas y Empaques</p>
            </div>
            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                <div class="time-display">
                    <i class="fas fa-clock me-2"></i>
                    <span id="current-date"></span><br>
                    <span id="current-time" class="fs-5"></span>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4 animate__animated animate__fadeIn" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab" aria-controls="pendientes" aria-selected="true">
                    <i class="fas fa-clipboard-list me-2"></i>Pendientes de Validación
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="validadas-tab" data-bs-toggle="tab" data-bs-target="#validadas" type="button" role="tab" aria-controls="validadas" aria-selected="false">
                    <i class="fas fa-check-double me-2"></i>Pendientes de Corrección
                </button>
            </li>
        </ul>

        <div class="tab-content animate__animated animate__fadeIn" id="myTabContent">
            <div class="tab-pane fade show active" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                <div class="card">
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="mb-0"><i class="fas fa-tasks me-2"></i>Entregas pendientes de validación</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($data['entregas_produccion']) && empty($data['entregas_scrap'])): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No hay entregas pendientes de validación.
                            </div>
                        <?php else: ?>
                            <h4 class="section-title production-title"><i class="fas fa-industry me-2"></i>Registro de Producción</h4>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i> Operador</th>
                                            <th><i class="fas fa-cogs me-1"></i> Máquina</th>
                                            <th><i class="fas fa-tag me-1"></i> Item</th>
                                            <th><i class="fas fa-file-alt me-1"></i> JT/WO</th>
                                            <th><i class="fas fa-info-circle me-1"></i> Tipo</th>
                                            <th><i class="fas fa-calendar-alt me-1"></i> Fecha/Hora</th>
                                            <th><i class="fas fa-cubes me-1"></i> Cantidad</th>
                                            <th><i class="fas fa-tools me-1"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($data['entregas_produccion'])): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay registros de producción pendientes.</td>
                                            </tr>
                                        <?php else: ?>
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
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <h4 class="section-title scrap-title"><i class="fas fa-trash-alt me-2"></i>Registro de Scrap</h4>
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
                                            <th><i class="fas fa-cubes me-1"></i> Cantidad</th>
                                            <th><i class="fas fa-tools me-1"></i> Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($data['entregas_scrap'])): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay registros de scrap pendientes.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($data['entregas_scrap'] as $entrega): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($entrega['nombre_empleado']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['nombre_maquina']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['item']) ?></td>
                                                    <td><?= htmlspecialchars($entrega['jtWo']) ?></td>
                                                    <td>
                                                        <span class="badge <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'badge-final' : 'badge-parcial' ?>">
                                                            <i class="fas fa-<?= ($entrega['tipo_boton'] == 'final_produccion') ? 'flag-checkered' : 'spinner' ?> me-1"></i>
                                                            <?= ($entrega['tipo_boton'] == 'final_produccion') ? 'Final' : 'Parcial' ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($entrega['fecha_registro'])) ?></td>
                                                    <td class="text-end fw-bold"><?= number_format($entrega['cantidad'], 0, ',', '.') ?> <small>unid.</small></td>
                                                    <td>
                                                        <button class="btn btn-action btn-review me-1" data-id="<?= $entrega['id'] ?>" data-tipo="scrap">
                                                            <i class="fas fa-search me-1"></i> Revisar
                                                        </button>
                                                        <button class="btn btn-action btn-validate-scrap" data-id="<?= $entrega['id'] ?>">
                                                            <i class="fas fa-check me-1"></i> Validar
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="validadas" role="tabpanel" aria-labelledby="validadas-tab">
                <div class="card">
                    <div class="card-header bg-success text-white py-3">
                        <h3 class="mb-0"><i class="fas fa-check-double me-2"></i>Entregas validadas</h3>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="toastMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
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
                    <h5 class="modal-title" id="validateModalLabel">Validar Entrega</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Al validar esta entrega, se registrará como completa en el sistema.
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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-toast@1.0.1/dist/bootstrap-toast.min.js"></script>

    <script>
        // Función para mostrar Toast
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('toastMessage');
            const toastBody = document.getElementById('toastBody');

            // Limpiar clases de tipo anteriores
            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');

            // Añadir clase según el tipo
            switch (type) {
                case 'danger':
                    toastEl.classList.add('bg-danger', 'text-white');
                    break;
                case 'warning':
                    toastEl.classList.add('bg-warning');
                    break;
                case 'info':
                    toastEl.classList.add('bg-info', 'text-white');
                    break;
                default: // success
                    toastEl.classList.add('bg-success', 'text-white');
            }

            // Establecer el mensaje
            toastBody.innerHTML = message;

            // Mostrar el toast usando Bootstrap 5
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
            toast.show();
        }

        // Manejar "Revisar" - Envía una nota opcional a producción
        document.querySelectorAll('.btn-review').forEach(button => {
            button.addEventListener('click', function() {
                const entregaId = this.getAttribute('data-id');
                const tipo = this.getAttribute('data-tipo');

                // Configurar el modal para revisión
                const modalTitle = document.getElementById('revisionModalLabel');
                modalTitle.textContent = `Revisar entrega de ${tipo === 'produccion' ? 'producción' : 'scrap'}`;

                // Limpiar textarea
                document.getElementById('notaRevision').value = '';

                // Guardar ID y tipo para uso posterior
                document.getElementById('submitRevisionBtn').setAttribute('data-id', entregaId);
                document.getElementById('submitRevisionBtn').setAttribute('data-tipo', tipo);

                // Mostrar modal
                const revisionModal = new bootstrap.Modal(document.getElementById('revisionModal'));
                revisionModal.show();
            });
        });

        // Enviar revisión
        document.getElementById('submitRevisionBtn').addEventListener('click', function() {
            const nota = document.getElementById('notaRevision').value;
            const entregaId = this.getAttribute('data-id');
            const tipo = this.getAttribute('data-tipo');

            // Cerrar modal
            const revisionModal = bootstrap.Modal.getInstance(document.getElementById('revisionModal'));
            revisionModal.hide();

            // Enviar datos a servidor
            fetch('/timeControl/public/logout', {
                    method: 'GET',
                    body: JSON.stringify({
                        id: entregaId,
                        tipo: tipo,
                        nota: nota
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(`Se ha enviado la revisión de ${tipo} con éxito`, 'success');
                    } else {
                        showToast(data.message || 'Error al enviar la revisión', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Hubo un problema con la solicitud', 'danger');
                });

            // Si estás en modo de desarrollo o no tienes el endpoint listo, puedes usar este código temporal:
            /*
            setTimeout(() => {
                showToast(`Se ha enviado la revisión de ${tipo} con éxito`, 'success');
            }, 500);
            */
        });

        // Manejar "Validar Producción"
        document.querySelectorAll('.btn-validate-production').forEach(button => {
            button.addEventListener('click', function() {
                const entregaId = this.getAttribute('data-id');

                // Configurar el modal para validación
                document.getElementById('validateModalLabel').textContent = 'Validar Entrega de Producción';
                document.getElementById('submitValidation').setAttribute('data-id', entregaId);
                document.getElementById('submitValidation').setAttribute('data-tipo', 'produccion');

                // Mostrar modal
                const validateModal = new bootstrap.Modal(document.getElementById('validateModal'));
                validateModal.show();
            });
        });

        // Manejar "Validar Scrap"
        document.querySelectorAll('.btn-validate-scrap').forEach(button => {
            button.addEventListener('click', function() {
                const entregaId = this.getAttribute('data-id');

                // Configurar el modal para validación
                document.getElementById('validateModalLabel').textContent = 'Validar Entrega de Scrap';
                document.getElementById('submitValidation').setAttribute('data-id', entregaId);
                document.getElementById('submitValidation').setAttribute('data-tipo', 'scrap');

                // Mostrar modal
                const validateModal = new bootstrap.Modal(document.getElementById('validateModal'));
                validateModal.show();
            });
        });

        // Enviar validación
        // Función para validar producción/scrap y mostrar toast
        document.getElementById('submitValidation').addEventListener('click', function() {
            const entregaId = this.getAttribute('data-id');
            const tipo = this.getAttribute('data-tipo');

            // Cerrar modal
            const validateModal = bootstrap.Modal.getInstance(document.getElementById('validateModal'));
            validateModal.hide();

            // Mostrar indicador de carga
            const loadingToast = showToast(`Validando entrega de ${tipo}...`, 'info');

            // Crear FormData para envío POST tradicional
            const formData = new FormData();
            formData.append('id', entregaId);
            formData.append('tipo', tipo);

            // Enviar datos a servidor usando fetch con FormData
            fetch('/timeControl/public/validarEnt', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        showToast(`Entrega de ${tipo === 'produccion' ? 'producción' : 'scrap'} validada correctamente`, 'success');
                    } else {
                        // Mostrar mensaje de error
                        showToast(data.message || 'Error al validar la entrega', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Hubo un problema con la solicitud: ' + error.message, 'danger');
                });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Llama al endpoint para obtener el estado y mensaje
            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    // Asegúrate de que el 'status' y 'message' estén presentes
                    if (data.status && data.message) {
                        const toastrFunction = data.status === "success" ? toastr.success : toastr.error;

                        // Muestra el mensaje usando toastr
                        toastrFunction(data.message, '', {
                            timeOut: 2000 // El mensaje desaparece después de 2 segundos
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al obtener el estado:', error);
                });
        });

        function updateClock() {
            const date = new Date();
            const formattedDate = date.toLocaleDateString('es-ES');
            const formattedTime = date.toLocaleTimeString('es-ES');
            document.getElementById('current-date').textContent = formattedDate;
            document.getElementById('current-time').textContent = formattedTime;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>