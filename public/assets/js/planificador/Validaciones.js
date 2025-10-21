// ============ VALIDACIONES DE FECHAS Y ÓRDENES ============

document.addEventListener('DOMContentLoaded', function() {
    
    // Obtener fecha actual (sin hora)
    function obtenerFechaActual() {
        const hoy = new Date();
        return hoy.toISOString().split('T')[0];
    }

    // Validar que una fecha no sea anterior a hoy
    function validarFechaNoAnterior(fecha, nombreCampo = 'la fecha') {
        const hoy = obtenerFechaActual();
        if (fecha < hoy) {
            return {
                valido: false,
                mensaje: `${nombreCampo} no puede ser anterior a hoy (${hoy})`
            };
        }
        return { valido: true };
    }

    // Validar rango de fechas
    function validarRangoFechas(fechaInicio, fechaFin) {
        if (fechaInicio > fechaFin) {
            return {
                valido: false,
                mensaje: 'La fecha de programación no puede ser posterior a la fecha de entrega'
            };
        }
        return { valido: true };
    }

    // ============ VALIDACIONES EN MODAL NUEVA ORDEN ============

    $('#nuevaOrdenModal').on('show.bs.modal', function() {
        const hoy = obtenerFechaActual();
        
        // Establecer fecha mínima en inputs de fecha
        document.querySelector('#nuevaOrdenModal input[name="fecha_programada"]').setAttribute('min', hoy);
        document.querySelector('#nuevaOrdenModal input[name="fecha_entrega"]').setAttribute('min', hoy);
    });

    // Validar fecha de programación
    document.addEventListener('change', function(e) {
        if (e.target.matches('#nuevaOrdenModal input[name="fecha_programada"]')) {
            const fechaProgramada = e.target.value;
            const validacion = validarFechaNoAnterior(fechaProgramada, 'Fecha de programación');
            
            if (!validacion.valido) {
                toastr.warning(validacion.mensaje);
                e.target.value = '';
                return;
            }

            // Actualizar fecha mínima de entrega
            const fechaEntrega = document.querySelector('#nuevaOrdenModal input[name="fecha_entrega"]');
            fechaEntrega.setAttribute('min', fechaProgramada);
        }
    });

    // Validar fecha de entrega
    document.addEventListener('change', function(e) {
        if (e.target.matches('#nuevaOrdenModal input[name="fecha_entrega"]')) {
            const fechaEntrega = e.target.value;
            const fechaProgramada = document.querySelector('#nuevaOrdenModal input[name="fecha_programada"]').value;
            
            const validacion = validarFechaNoAnterior(fechaEntrega, 'Fecha de entrega');
            if (!validacion.valido) {
                toastr.warning(validacion.mensaje);
                e.target.value = '';
                return;
            }

            if (fechaProgramada) {
                const validacionRango = validarRangoFechas(fechaProgramada, fechaEntrega);
                if (!validacionRango.valido) {
                    toastr.warning(validacionRango.mensaje);
                    e.target.value = '';
                }
            }
        }
    });

    // ============ VALIDACIONES EN FORMULARIO NUEVA ORDEN (SUBMIT) ============

    $('#formNuevaOrden').on('submit', function(e) {
        const fechaProgramada = document.querySelector('#nuevaOrdenModal input[name="fecha_programada"]').value;
        const fechaEntrega = document.querySelector('#nuevaOrdenModal input[name="fecha_entrega"]').value;
        const cantidad = parseFloat(document.querySelector('#nuevaOrdenModal input[name="cantidad_requerida"]').value);

        // Validar fechas
        const validacionProgramada = validarFechaNoAnterior(fechaProgramada, 'Fecha de programación');
        if (!validacionProgramada.valido) {
            e.preventDefault();
            toastr.error(validacionProgramada.mensaje);
            return;
        }

        const validacionEntrega = validarFechaNoAnterior(fechaEntrega, 'Fecha de entrega');
        if (!validacionEntrega.valido) {
            e.preventDefault();
            toastr.error(validacionEntrega.mensaje);
            return;
        }

        const validacionRango = validarRangoFechas(fechaProgramada, fechaEntrega);
        if (!validacionRango.valido) {
            e.preventDefault();
            toastr.error(validacionRango.mensaje);
            return;
        }

        // Validar cantidad
        if (isNaN(cantidad) || cantidad <= 0) {
            e.preventDefault();
            toastr.error('La cantidad debe ser mayor a 0');
            return;
        }
    });

    // ============ VALIDACIONES EN MODAL EDITAR ORDEN ============

    const abrirModalEditarOriginal = window.abrirModalEditar;
    window.abrirModalEditar = function(orden) {
        abrirModalEditarOriginal(orden);
        
        setTimeout(function() {
            const hoy = obtenerFechaActual();
            
            document.querySelector('#editarOrdenModal input[name="fecha_programada"]').setAttribute('min', hoy);
            document.querySelector('#editarOrdenModal input[name="fecha_entrega"]').setAttribute('min', hoy);

            document.querySelector('#editarOrdenModal input[name="fecha_programada"]').addEventListener('change', function() {
                const fechaProgramada = this.value;
                const validacion = validarFechaNoAnterior(fechaProgramada, 'Fecha de programación');
                
                if (!validacion.valido) {
                    toastr.warning(validacion.mensaje);
                    this.value = orden.fecha_programada;
                    return;
                }

                const fechaEntrega = document.querySelector('#editarOrdenModal input[name="fecha_entrega"]').value;
                if (fechaEntrega) {
                    document.querySelector('#editarOrdenModal input[name="fecha_entrega"]').setAttribute('min', fechaProgramada);
                }
            });

            document.querySelector('#editarOrdenModal input[name="fecha_entrega"]').addEventListener('change', function() {
                const fechaEntrega = this.value;
                const fechaProgramada = document.querySelector('#editarOrdenModal input[name="fecha_programada"]').value;
                
                const validacion = validarFechaNoAnterior(fechaEntrega, 'Fecha de entrega');
                if (!validacion.valido) {
                    toastr.warning(validacion.mensaje);
                    this.value = orden.fecha_entrega;
                    return;
                }

                if (fechaProgramada) {
                    const validacionRango = validarRangoFechas(fechaProgramada, fechaEntrega);
                    if (!validacionRango.valido) {
                        toastr.warning(validacionRango.mensaje);
                        this.value = orden.fecha_entrega;
                    }
                }
            });
        }, 100);
    };

    // Validar cantidad requerida
    document.addEventListener('change', function(e) {
        if (e.target.matches('input[name="cantidad_requerida"]')) {
            const cantidad = parseFloat(e.target.value);
            if (isNaN(cantidad) || cantidad <= 0) {
                toastr.warning('La cantidad debe ser un número mayor a 0');
                e.target.value = '';
            }
        }
    });

    // ============ DISTRIBUCIÓN MANUAL - SIN AUTO-DISTRIBUIR ============

    // Remover el botón de auto-distribuir del DOM si existe
    const btnAutoDistribuir = document.getElementById('btnAutoDistribuir');
    if (btnAutoDistribuir) {
        btnAutoDistribuir.style.display = 'none';
    }

    // Interceptar función generarDistribucion para inicializar en 0
    const generarDistribucionOriginal = window.generarDistribucion;
    window.generarDistribucion = function(cantidad, fechaInicio, fechaFin) {
        if (new Date(fechaInicio) > new Date(fechaFin)) {
            toastr.error('Error: fecha de inicio posterior a fecha de fin');
            return;
        }

        // Llamar a la función original pero con modificaciones
        const contenedor = document.getElementById('distribucionDias');
        contenedor.innerHTML = '';
        window.distribucionActual = {};

        let fechaActual = new Date(fechaInicio);
        const fechaFinal = new Date(fechaFin);
        let diasTotales = 0;

        let temp = new Date(fechaInicio);
        while (temp <= fechaFinal) {
            diasTotales++;
            temp.setDate(temp.getDate() + 1);
        }

        for (let i = 0; i < diasTotales; i++) {
            const fecha = fechaActual.toISOString().split('T')[0];
            window.distribucionActual[fecha] = 0; // Iniciar en 0, no distribuir automático

            const diaDiv = document.createElement('div');
            diaDiv.className = 'distribucion-dia card mb-2';
            diaDiv.innerHTML = `
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <small class="text-muted d-block">${fechaActual.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' })}</small>
                            <strong class="fs-6">${fecha}</strong>
                        </div>
                        <div class="col-md-7">
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control distribucion-cantidad" 
                                       data-fecha="${fecha}"
                                       value="0" 
                                       step="0.01" 
                                       min="0"
                                       placeholder="Ingrese cantidad">
                                <span class="input-group-text" id="distUnidad">lb</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            contenedor.appendChild(diaDiv);

            const input = diaDiv.querySelector('input');
            input.addEventListener('change', function() {
                window.distribucionActual[fecha] = parseFloat(this.value) || 0;
                window.actualizarResumen();
            });

            input.addEventListener('input', function() {
                window.distribucionActual[fecha] = parseFloat(this.value) || 0;
                window.actualizarResumen();
            });

            fechaActual.setDate(fechaActual.getDate() + 1);
        }

        window.actualizarResumen();
    };

    // ============ MOVER ÓRDENES ENTRE DÍAS - INTERFAZ ============

    // Agregar modal para mover orden a otro día
    if (!document.getElementById('moverOrdenModal')) {
        const modalMover = `
        <div class="modal fade" id="moverOrdenModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-calendar-event me-2"></i>Reprogramar Orden
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="ordenIdMover">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nueva Fecha de Programación</label>
                            <input type="date" id="nuevaFechaProgramada" class="form-control">
                        </div>
                        <div class="alert alert-info">
                            <small id="infoRangoFechas"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-warning text-dark" id="btnConfirmarMover">
                            <i class="bi bi-check2 me-2"></i>Reprogramar
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalMover);
    }

    // Función para abrir modal de mover orden
    window.abrirModalMoverOrden = function(ordenId, fechaActual, fechaEntrega) {
        document.getElementById('ordenIdMover').value = ordenId;
        
        const hoy = obtenerFechaActual();
        const inputNuevaFecha = document.getElementById('nuevaFechaProgramada');
        inputNuevaFecha.setAttribute('min', hoy);
        inputNuevaFecha.setAttribute('max', fechaEntrega);
        inputNuevaFecha.value = fechaActual;

        document.getElementById('infoRangoFechas').innerHTML = 
            `<strong>Rango válido:</strong> ${hoy} a ${fechaEntrega}`;

        const modalMover = new bootstrap.Modal(document.getElementById('moverOrdenModal'));
        modalMover.show();
    };

    // Manejar confirmación de mover orden
    document.getElementById('btnConfirmarMover').addEventListener('click', function() {
        const ordenId = document.getElementById('ordenIdMover').value;
        const nuevaFecha = document.getElementById('nuevaFechaProgramada').value;

        if (!nuevaFecha) {
            toastr.warning('Seleccione una fecha válida');
            return;
        }

        $.ajax({
            url: window.baseUrl + '/planificador/mover-orden',
            method: 'POST',
            data: {
                orden_id: ordenId,
                nueva_fecha_programada: nuevaFecha
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success('Orden reprogramada exitosamente');
                    bootstrap.Modal.getInstance(document.getElementById('moverOrdenModal')).hide();
                    window.calendar.refetchEvents();
                } else {
                    toastr.error(response.message || 'Error al reprogramar');
                }
            },
            error: function(xhr) {
                toastr.error('Error al reprogramar la orden');
                console.error(xhr);
            }
        });
    });

    // ============ AGREGAR BOTÓN MOVER EN DETALLE DE ORDEN ============

    // Interceptar mostrarDetalleOrden
    const mostrarDetalleOrdenOriginal = window.mostrarDetalleOrden;
    window.mostrarDetalleOrden = function(ordenId) {
        mostrarDetalleOrdenOriginal(ordenId);

        // Esperar a que se cargue el modal
        setTimeout(function() {
            const footerModal = document.querySelector('#detalleOrdenModal .modal-footer');
            
            // Verificar si el botón ya existe
            if (!document.getElementById('btnMoverOrden')) {
                const btnMover = document.createElement('button');
                btnMover.id = 'btnMoverOrden';
                btnMover.className = 'btn btn-warning text-dark';
                btnMover.innerHTML = '<i class="bi bi-calendar-event me-2"></i>Reprogramar';
                
                // Insertar antes del botón de editar
                const btnEditar = footerModal.querySelector('#btnEditarOrden');
                if (btnEditar) {
                    btnEditar.parentNode.insertBefore(btnMover, btnEditar);
                } else {
                    footerModal.appendChild(btnMover);
                }

                // Event listener
                btnMover.addEventListener('click', function() {
                    const orden = window.ordenActualDetalle;
                    bootstrap.Modal.getInstance(document.getElementById('detalleOrdenModal')).hide();
                    window.abrirModalMoverOrden(orden.id, orden.fecha_programada, orden.fecha_entrega);
                });
            }
        }, 100);
    };

});