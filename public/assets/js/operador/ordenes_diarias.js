// Configurar toastr
        toastr.options = {
            timeOut: 3000,
            positionClass: 'toast-top-right',
            progressBar: true
        };

        // Mostrar mensajes de sesión
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/timeControl/public/getStatus')
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.message) {
                        if (data.status === 'success') {
                            toastr.success(data.message);
                        } else if (data.status === 'error') {
                            toastr.error(data.message);
                        } else if (data.status === 'info') {
                            toastr.info(data.message);
                        }
                    }
                });
        });

        // Cambiar fecha
        function cambiarFecha(dias, hoy = false) {
            const fechaInput = document.getElementById('fecha');
            let nuevaFecha;

            if (hoy) {
                nuevaFecha = new Date();
            } else {
                nuevaFecha = new Date(fechaInput.value);
                nuevaFecha.setDate(nuevaFecha.getDate() + dias);
            }

            const año = nuevaFecha.getFullYear();
            const mes = String(nuevaFecha.getMonth() + 1).padStart(2, '0');
            const dia = String(nuevaFecha.getDate()).padStart(2, '0');
            const fechaFormateada = `${año}-${mes}-${dia}`;

            window.location.href = `/timeControl/public/ordenes_diarias?fecha=${fechaFormateada}`;
        }

        // Ver detalle de orden
        function verDetalle(ordenId) {
            const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
            const contenido = document.getElementById('detalleContenido');
            
            modal.show();

            fetch(`/timeControl/public/obtener_detalle_orden?orden_id=${ordenId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const orden = data.orden;
                        contenido.innerHTML = `
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">JOB ID</label>
                                    <p class="fw-bold">${orden.job_id}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Item</label>
                                    <p class="fw-bold">${orden.item}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Cliente</label>
                                    <p class="fw-bold">${orden.cliente}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">PO</label>
                                    <p class="fw-bold">${orden.po || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Máquina</label>
                                    <p class="fw-bold">${orden.maquina_nombre}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Área</label>
                                    <p class="fw-bold">${orden.area_nombre}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Tamaño</label>
                                    <p class="fw-bold">${orden.tamano || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Cantidad Requerida</label>
                                    <p class="fw-bold">${parseFloat(orden.cantidad_requerida).toFixed(2)} ${orden.unidad_medida}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Fecha Programada</label>
                                    <p class="fw-bold">${new Date(orden.fecha_programada).toLocaleDateString('es-DO')}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Fecha Entrega</label>
                                    <p class="fw-bold">${new Date(orden.fecha_entrega).toLocaleDateString('es-DO')}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small">Descripción del Producto</label>
                                    <p>${orden.descripcion_producto || 'Sin descripción'}</p>
                                </div>
                                ${orden.notas_planificador ? `
                                    <div class="col-12">
                                        <label class="text-muted small">Notas del Planificador</label>
                                        <div class="alert alert-info">
                                            ${orden.notas_planificador}
                                        </div>
                                    </div>
                                ` : ''}
                                <div class="col-12">
                                    <label class="text-muted small">Operador Asignado</label>
                                    <p class="fw-bold">${orden.operador_nombre || 'No asignado'}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small">Creado por</label>
                                    <p>${orden.planificador_nombre || 'N/A'}</p>
                                </div>
                            </div>
                        `;
                    } else {
                        contenido.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                Error al cargar los detalles: ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    contenido.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            Error de conexión al cargar los detalles
                        </div>
                    `;
                    console.error('Error:', error);
                });
        }

        // Confirmar logout
        function confirmLogout() {
            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                window.location.href = '/timeControl/public/logout';
            }
        }