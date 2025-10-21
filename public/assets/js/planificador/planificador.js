document.addEventListener('DOMContentLoaded', function() {
    
    
    const baseUrl = window.BASE_URL || window.location.pathname.match(/^(\/[^\/]+\/[^\/]+)/)?.[1] || '';
    console.log('Base URL:', baseUrl);
    
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000
    };

    let ordenActualDetalle = null;
    let distribucionActual = {};
    let currentOrdenId = null;

  
    
    console.log('✓ Script de búsqueda inicializado');
    
    
    $('#btnBuscarOrden').on('click', function(e) {
        e.preventDefault();
        console.log('🔍 Botón buscar clickeado');
        
        const jobId = $('#inputBuscarJobId').val().trim();
        console.log('JOB ID ingresado:', jobId);
        
        if (!jobId) {
            console.warn('⚠️ JOB ID vacío');
            toastr.warning('Por favor ingrese un JOB ID');
            return;
        }

        buscarOrdenPorJobId(jobId);
    });

    // Permitir buscar con Enter
    $('#inputBuscarJobId').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            console.log('↩️ Enter presionado');
            $('#btnBuscarOrden').click();
        }
    });

    $('#btnVerDetalleDesdeBoton').on('click', function(e) {
        e.preventDefault();
        const ordenId = $(this).data('orden-id');
        console.log('👁️ Ver detalle de orden:', ordenId);
        
        if (ordenId) {
            const modalBuscar = bootstrap.Modal.getInstance(document.getElementById('buscarOrdenModal'));
            if (modalBuscar) {
                modalBuscar.hide();
            }
            mostrarDetalleOrden(ordenId);
        }
    });

    function buscarOrdenPorJobId(jobId) {
        console.log('=== INICIANDO BÚSQUEDA ===');
        console.log('JOB ID:', jobId);
        
        const url = baseUrl + '/planificador/buscar-orden';
        console.log('URL completa:', url);
        
        $.ajax({
            url: url,
            method: 'GET',
            data: { job_id: jobId },
            dataType: 'json',
            beforeSend: function() {
                console.log('📤 Enviando petición...');
                $('#btnBuscarOrden')
                    .prop('disabled', true)
                    .html('<i class="bi bi-hourglass-split"></i> Buscando...');
                $('#resultadoBusqueda').hide();
                $('#noEncontrado').hide();
            },
            success: function(response) {
                console.log('✅ Respuesta recibida:', response);
                
                if (response.success) {
                    console.log('✓ Orden encontrada:', response.orden);
                    mostrarResultadoBusqueda(response.orden);
                } else {
                    console.log('✗ Orden no encontrada');
                    $('#noEncontrado').show();
                    toastr.warning('Orden no encontrada con ese JOB ID');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ ERROR EN AJAX:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Status Code:', xhr.status);
                console.error('Response Text:', xhr.responseText);
                
                let mensaje = 'Error al buscar la orden';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    console.error('Error parseado:', errorResponse);
                    mensaje = errorResponse.message || mensaje;
                } catch(e) {
                    if (xhr.status === 404) {
                        mensaje = 'Error 404: La ruta de búsqueda no existe';
                    } else if (xhr.status === 500) {
                        mensaje = 'Error 500 del servidor';
                    }
                }
                
                toastr.error(mensaje);
                $('#noEncontrado').show();
            },
            complete: function() {
                console.log('🏁 Petición completada');
                $('#btnBuscarOrden')
                    .prop('disabled', false)
                    .html('<i class="bi bi-search"></i> Buscar');
            }
        });
    }

    function mostrarResultadoBusqueda(orden) {
    console.log('📊 Mostrando resultado:', orden);
    
    try {
        $('#resJobId').text(orden.job_id);
        $('#resItem').text(orden.item);
        $('#resCliente').text(orden.cliente);
        $('#resProducido').text(parseFloat(orden.cantidad_producida || 0).toFixed(2));
        $('#resRequerido').text(parseFloat(orden.cantidad_requerida || 0).toFixed(2));
        
        // Estado con badge
        const estadoBadges = {
            'pendiente': 'bg-secondary',
            'en_proceso': 'bg-info',
            'completada': 'bg-success',
            'pausada': 'bg-warning',
            'cancelada': 'bg-danger'
        };
        
        $('#resEstado')
            .text(orden.estado.replace('_', ' ').toUpperCase())
            .removeClass()
            .addClass('badge ' + (estadoBadges[orden.estado] || 'bg-secondary'));
        
        // Progreso
        const porcentaje = parseFloat(orden.porcentaje) || 0;
        $('#resProgreso').css('width', porcentaje + '%');
        $('#resPorcentaje').text(porcentaje.toFixed(1) + '%');
        
        // ✓ ARREGLADO: Usar query string con ?id=
        const urlReporte = baseUrl + '/planificador/reporte?id=' + orden.id;
        $('#btnVerReporte').attr('href', urlReporte);
        console.log('URL Reporte:', urlReporte);
        
        // Guardar ID para ver detalle
        $('#btnVerDetalleDesdeBoton').data('orden-id', orden.id);
        
        $('#resultadoBusqueda').show();
        $('#noEncontrado').hide();
        
        toastr.success('Orden encontrada exitosamente');
    } catch(error) {
        console.error('Error al mostrar resultado:', error);
        toastr.error('Error al mostrar los resultados');
    }
}

// ========== LÍNEA ~282: En actualizarLinkReporte() ==========
function actualizarLinkReporte(ordenId) {
    // ✓ ARREGLADO: Usar query string con ?id=
    const urlReporte = baseUrl + '/planificador/reporte?id=' + ordenId;
    $('#btnVerReporteDesdeDetalle').attr('href', urlReporte);
    console.log('Link reporte actualizado:', urlReporte);
}



    // ============ CARGAR ÁREAS AL INICIO ============
    
    cargarAreasInicio();

    function cargarAreasInicio() {
        $.ajax({
            url: baseUrl + '/planificador/obtener-areas',
            method: 'GET',
            success: function(areas) {
                const filtroArea = document.getElementById('filtroArea');
                filtroArea.innerHTML = '<option value="">Todas las áreas</option>';
                areas.forEach(area => {
                    filtroArea.innerHTML += `<option value="${area.id}">${area.nombre}</option>`;
                });
            },
            error: function() {
                console.log('Error cargando áreas para filtro');
            }
        });
    }
    
    $('#filtroArea').on('change', function() {
        const areaId = $(this).val();
        const filtroMaquina = document.getElementById('filtroMaquina');
        
        filtroMaquina.innerHTML = '<option value="">Todas las máquinas</option>';
        
        if (!areaId) {
            return;
        }

        $.ajax({
            url: baseUrl + '/planificador/obtener-maquinas-por-area',
            method: 'GET',
            data: { area_id: areaId },
            success: function(maquinas) {
                maquinas.forEach(maq => {
                    filtroMaquina.innerHTML += `<option value="${maq.id}">${maq.nombre}</option>`;
                });
            },
            error: function() {
                toastr.error('Error al cargar máquinas');
            }
        });
    });

    // ============ CARGAR ÁREAS EN MODAL NUEVA ORDEN ============
    
    $('#nuevaOrdenModal').on('show.bs.modal', function() {
        cargarAreasModal();
    });

    function cargarAreasModal() {
        $.ajax({
            url: baseUrl + '/planificador/obtener-areas',
            method: 'GET',
            success: function(areas) {
                const areasSelect = document.querySelector('#nuevaOrdenModal select[name="area_id"]');
                areasSelect.innerHTML = '<option value="">Seleccionar área</option>';
                areas.forEach(area => {
                    areasSelect.innerHTML += `<option value="${area.id}">${area.nombre}</option>`;
                });
            },
            error: function() {
                console.log('Error cargando áreas en modal');
            }
        });
    }

    $('#nuevaOrdenModal select[name="area_id"]').on('change', function() {
        const areaId = $(this).val();
        const maquinaSelect = document.querySelector('#nuevaOrdenModal select[name="maquina_id"]');
        
        if (!areaId) {
            maquinaSelect.innerHTML = '<option value="">Primero seleccione área</option>';
            return;
        }

        $.ajax({
            url: baseUrl + '/planificador/obtener-maquinas-por-area',
            method: 'GET',
            data: { area_id: areaId },
            success: function(maquinas) {
                maquinaSelect.innerHTML = '<option value="">Seleccionar máquina</option>';
                maquinas.forEach(maq => {
                    maquinaSelect.innerHTML += `<option value="${maq.id}">${maq.nombre}</option>`;
                });
            },
            error: function() {
                toastr.error('Error al cargar máquinas');
            }
        });
    });

    // ============ FORMULARIO NUEVA ORDEN ============
    
    $('#formNuevaOrden').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: baseUrl + '/planificador/crear',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    toastr.success('Orden creada exitosamente');
                    $('#formNuevaOrden')[0].reset();
                    $('#nuevaOrdenModal').modal('hide');
                    calendar.refetchEvents();
                } else {
                    toastr.error(response.message || 'Error al crear orden');
                }
            },
            error: function(xhr) {
                let mensaje = 'Error al crear la orden';
                try {
                    const jsonResponse = JSON.parse(xhr.responseText);
                    mensaje = jsonResponse.message || mensaje;
                } catch(e) {}
                toastr.error(mensaje);
            }
        });
    });

    // ============ CALENDARIO ============
    
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', list: 'Lista' },
        editable: false,
        events: function(info, successCallback, failureCallback) {
            const filtros = {
                fecha_inicio: info.startStr.split('T')[0],
                fecha_fin: info.endStr.split('T')[0]
            };

            const areaId = document.getElementById('filtroArea').value;
            const maquinaId = document.getElementById('filtroMaquina').value;
            const estado = document.getElementById('filtroEstado').value;

            if (areaId) filtros.area_id = areaId;
            if (maquinaId) filtros.maquina_id = maquinaId;
            if (estado) filtros.estado = estado;

            $.ajax({
                url: baseUrl + '/planificador/obtener-ordenes-calendario',
                method: 'GET',
                data: filtros,
                success: function(ordenes) {
                    cargarDistribucionesParaCalendario(ordenes, successCallback);
                    actualizarEstadisticas();
                },
                error: function(xhr, status, error) {
                    console.error('Error cargando eventos:', error);
                    toastr.error('Error al cargar las órdenes');
                    failureCallback(error);
                }
            });
        },
        eventClick: function(info) {
            const ordenId = info.event.extendedProps.orden_id || info.event.id;
            mostrarDetalleOrden(ordenId);
        }
    });

    calendar.render();

    function cargarDistribucionesParaCalendario(ordenes, callback) {
        const eventosFinales = [];
        let ordenesCompletadas = 0;

        if (ordenes.length === 0) {
            callback([]);
            return;
        }

        ordenes.forEach(orden => {
            $.ajax({
                url: baseUrl + '/planificador/obtener-distribucion',
                method: 'GET',
                data: { orden_id: orden.id },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.distribucion && response.distribucion.length > 0) {
                        response.distribucion.forEach(dist => {
                            eventosFinales.push({
                                id: orden.id + '_' + dist.fecha,
                                title: orden.title,
                                start: dist.fecha,
                                backgroundColor: orden.backgroundColor,
                                borderColor: orden.borderColor,
                                extendedProps: {
                                    ...orden.extendedProps,
                                    orden_id: orden.id,
                                    meta_diaria: dist.cantidad_meta
                                }
                            });
                        });
                    } else {
                        eventosFinales.push(orden);
                    }
                    
                    ordenesCompletadas++;
                    if (ordenesCompletadas === ordenes.length) {
                        callback(eventosFinales);
                    }
                },
                error: function() {
                    eventosFinales.push(orden);
                    ordenesCompletadas++;
                    if (ordenesCompletadas === ordenes.length) {
                        callback(eventosFinales);
                    }
                }
            });
        });
    }

    function actualizarEstadisticas() {
        const filtros = {};
        const areaId = document.getElementById('filtroArea').value;
        const maquinaId = document.getElementById('filtroMaquina').value;

        if (areaId) filtros.area_id = areaId;
        if (maquinaId) filtros.maquina_id = maquinaId;

        $.ajax({
            url: baseUrl + '/planificador/obtener-ordenes-calendario',
            method: 'GET',
            data: filtros,
            success: function(eventos) {
                const pendientes = eventos.filter(e => e.extendedProps.estado === 'pendiente').length;
                const enProceso = eventos.filter(e => e.extendedProps.estado === 'en_proceso').length;
                
                document.getElementById('statPendientes').textContent = pendientes;
                document.getElementById('statEnProceso').textContent = enProceso;
            }
        });
    }

    document.getElementById('btnAplicarFiltros').addEventListener('click', function() {
        calendar.refetchEvents();
        toastr.info('Filtros aplicados');
    });

    function mostrarDetalleOrden(ordenId) {
        $.ajax({
            url: baseUrl + '/planificador/obtener-detalle-orden',
            method: 'GET',
            data: { id: ordenId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const orden = response.orden;
                    ordenActualDetalle = orden;
                    currentOrdenId = ordenId;
                    
                    // Actualizar link del reporte
                    actualizarLinkReporte(ordenId);

                    const estadoBadges = {
                        'pendiente': 'bg-secondary',
                        'en_proceso': 'bg-info',
                        'completada': 'bg-success',
                        'pausada': 'bg-warning',
                        'cancelada': 'bg-danger'
                    };

                    const html = `
                        <div class="row g-3">
                            <div class="col-md-6"><strong>JOB ID:</strong> ${orden.job_id}</div>
                            <div class="col-md-6"><strong>Item:</strong> ${orden.item}</div>
                            <div class="col-md-6"><strong>Cliente:</strong> ${orden.cliente}</div>
                            <div class="col-md-6"><strong>PO:</strong> ${orden.po || 'N/A'}</div>
                            <div class="col-md-12"><strong>Descripción:</strong> ${orden.descripcion_producto || 'N/A'}</div>
                            <div class="col-md-6"><strong>Área:</strong> ${orden.area_nombre}</div>
                            <div class="col-md-6"><strong>Máquina:</strong> ${orden.maquina_nombre}</div>
                            <div class="col-md-6"><strong>Cantidad:</strong> ${orden.cantidad_requerida} ${orden.unidad_medida}</div>
                            <div class="col-md-6"><strong>Estado:</strong> <span class="badge ${estadoBadges[orden.estado] || 'bg-secondary'}">${orden.estado.replace('_', ' ').toUpperCase()}</span></div>
                            <div class="col-md-6"><strong>Fecha Programada:</strong> ${orden.fecha_programada}</div>
                            <div class="col-md-6"><strong>Fecha Entrega:</strong> ${orden.fecha_entrega}</div>
                            <div class="col-md-12"><strong>Notas:</strong> ${orden.notas_planificador || 'N/A'}</div>
                        </div>
                    `;

                    document.getElementById('detalleOrdenContent').innerHTML = html;
                    
                    document.getElementById('btnDistribuir').onclick = function() {
                        abrirDistribucion(orden);
                    };
                    
                    // Botón Editar
                    document.getElementById('btnEditarOrden').onclick = function() {
                        abrirModalEditar(orden);
                    };
                    
                    const modal = new bootstrap.Modal(document.getElementById('detalleOrdenModal'));
                    modal.show();
                } else {
                    toastr.error(response.message || 'Orden no encontrada');
                }
            },
            error: function(xhr) {
                console.error('Error al cargar detalle:', xhr);
                toastr.error('Error al cargar el detalle');
            }
        });
    }

    // ============ EDITAR ORDEN ============

    function abrirModalEditar(orden) {
        console.log('🖊️ Editando orden:', orden);
        
        // Cerrar modal de detalle
        const modalDetalle = bootstrap.Modal.getInstance(document.getElementById('detalleOrdenModal'));
        if (modalDetalle) {
            modalDetalle.hide();
        }

        // Crear modal de edición dinámicamente
        const modalHTML = `
        <div class="modal fade" id="editarOrdenModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>Editar Orden: ${orden.job_id}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formEditarOrden">
                        <input type="hidden" name="orden_id" value="${orden.id}">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">JOB ID *</label>
                                    <input type="text" name="job_id" class="form-control" value="${orden.job_id}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Item *</label>
                                    <input type="text" name="item" class="form-control" value="${orden.item}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cliente *</label>
                                    <input type="text" name="cliente" class="form-control" value="${orden.cliente}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">PO</label>
                                    <input type="text" name="po" class="form-control" value="${orden.po || ''}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Descripción del Producto</label>
                                    <textarea name="descripcion_producto" class="form-control" rows="2">${orden.descripcion_producto || ''}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tamaño/SIZE</label>
                                    <input type="text" name="tamano" class="form-control" value="${orden.tamano || ''}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Área *</label>
                                    <select name="area_id" class="form-select" required>
                                        <option value="">Cargando...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Máquina *</label>
                                    <select name="maquina_id" class="form-select" required>
                                        <option value="">Cargando...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cantidad *</label>
                                    <input type="number" name="cantidad_requerida" class="form-control" step="0.01" value="${orden.cantidad_requerida}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Unidad</label>
                                    <select name="unidad_medida" class="form-select">
                                        <option value="lb" ${orden.unidad_medida === 'lb' ? 'selected' : ''}>lb</option>
                                        <option value="kg" ${orden.unidad_medida === 'kg' ? 'selected' : ''}>kg</option>
                                        <option value="pzas" ${orden.unidad_medida === 'pzas' ? 'selected' : ''}>pzas</option>
                                        <option value="cajas" ${orden.unidad_medida === 'cajas' ? 'selected' : ''}>cajas</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Prioridad *</label>
                                    <select name="prioridad" class="form-select" required>
                                        <option value="baja" ${orden.prioridad === 'baja' ? 'selected' : ''}>Baja</option>
                                        <option value="media" ${orden.prioridad === 'media' ? 'selected' : ''}>Media</option>
                                        <option value="alta" ${orden.prioridad === 'alta' ? 'selected' : ''}>Alta</option>
                                        <option value="urgente" ${orden.prioridad === 'urgente' ? 'selected' : ''}>Urgente</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Estado *</label>
                                    <select name="estado" class="form-select" required>
                                        <option value="pendiente" ${orden.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                        <option value="en_proceso" ${orden.estado === 'en_proceso' ? 'selected' : ''}>En Proceso</option>
                                        <option value="completada" ${orden.estado === 'completada' ? 'selected' : ''}>Completada</option>
                                        <option value="pausada" ${orden.estado === 'pausada' ? 'selected' : ''}>Pausada</option>
                                        <option value="cancelada" ${orden.estado === 'cancelada' ? 'selected' : ''}>Cancelada</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha Programada *</label>
                                    <input type="date" name="fecha_programada" class="form-control" value="${orden.fecha_programada}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha Entrega *</label>
                                    <input type="date" name="fecha_entrega" class="form-control" value="${orden.fecha_entrega}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Notas del Planificador</label>
                                    <textarea name="notas_planificador" class="form-control" rows="3">${orden.notas_planificador || ''}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="btnEliminarOrden">
                                <i class="bi bi-trash me-2"></i>Eliminar Orden
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

        // Eliminar modal previo si existe
        const modalExistente = document.getElementById('editarOrdenModal');
        if (modalExistente) {
            modalExistente.remove();
        }

        // Agregar nuevo modal al body
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Cargar áreas
        cargarAreasParaEdicion(orden.area_id, orden.maquina_id);

        // Manejar cambio de área
        document.querySelector('#editarOrdenModal select[name="area_id"]').addEventListener('change', function() {
            cargarMaquinasParaEdicion(this.value);
        });

        // Manejar submit del formulario
        document.getElementById('formEditarOrden').addEventListener('submit', function(e) {
            e.preventDefault();
            guardarEdicionOrden(new FormData(this));
        });

        // Manejar botón eliminar
        document.getElementById('btnEliminarOrden').addEventListener('click', function() {
            eliminarOrden(orden.id, orden.job_id);
        });

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('editarOrdenModal'));
        modal.show();

        // Limpiar al cerrar
        document.getElementById('editarOrdenModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    function cargarAreasParaEdicion(areaIdSeleccionada, maquinaIdSeleccionada) {
        $.ajax({
            url: baseUrl + '/planificador/obtener-areas',
            method: 'GET',
            success: function(areas) {
                const select = document.querySelector('#editarOrdenModal select[name="area_id"]');
                select.innerHTML = '<option value="">Seleccionar área</option>';
                
                areas.forEach(area => {
                    const selected = area.id == areaIdSeleccionada ? 'selected' : '';
                    select.innerHTML += `<option value="${area.id}" ${selected}>${area.nombre}</option>`;
                });

                // Cargar máquinas del área seleccionada
                if (areaIdSeleccionada) {
                    cargarMaquinasParaEdicion(areaIdSeleccionada, maquinaIdSeleccionada);
                }
            },
            error: function() {
                toastr.error('Error al cargar áreas');
            }
        });
    }

    function cargarMaquinasParaEdicion(areaId, maquinaIdSeleccionada = null) {
        const select = document.querySelector('#editarOrdenModal select[name="maquina_id"]');
        
        if (!areaId) {
            select.innerHTML = '<option value="">Primero seleccione área</option>';
            return;
        }

        $.ajax({
            url: baseUrl + '/planificador/obtener-maquinas-por-area',
            method: 'GET',
            data: { area_id: areaId },
            success: function(maquinas) {
                select.innerHTML = '<option value="">Seleccionar máquina</option>';
                
                maquinas.forEach(maq => {
                    const selected = maq.id == maquinaIdSeleccionada ? 'selected' : '';
                    select.innerHTML += `<option value="${maq.id}" ${selected}>${maq.nombre}</option>`;
                });
            },
            error: function() {
                toastr.error('Error al cargar máquinas');
            }
        });
    }

    function guardarEdicionOrden(formData) {
        $.ajax({
            url: baseUrl + '/planificador/guardar-edicion',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    toastr.success('Orden actualizada exitosamente');
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editarOrdenModal'));
                    if (modal) modal.hide();
                    
                    calendar.refetchEvents();
                } else {
                    toastr.error(response.message || 'Error al actualizar la orden');
                }
            },
            error: function(xhr) {
                let mensaje = 'Error al actualizar la orden';
                try {
                    const jsonResponse = JSON.parse(xhr.responseText);
                    mensaje = jsonResponse.message || mensaje;
                } catch(e) {}
                toastr.error(mensaje);
            }
        });
    }

    // ============ ELIMINAR ORDEN ============

    function eliminarOrden(ordenId, jobId) {
        // Confirmación con SweetAlert o confirm nativo
        if (!confirm(`¿Está seguro de eliminar la orden ${jobId}?\n\nEsta acción no se puede deshacer.`)) {
            return;
        }

        $.ajax({
            url: baseUrl + '/planificador/eliminar-orden',
            method: 'POST',
            data: { orden_id: ordenId },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    toastr.success('Orden eliminada exitosamente');
                    
                    // Cerrar modal de edición
                    const modalEditar = bootstrap.Modal.getInstance(document.getElementById('editarOrdenModal'));
                    if (modalEditar) modalEditar.hide();
                    
                    // Cerrar modal de detalle si está abierto
                    const modalDetalle = bootstrap.Modal.getInstance(document.getElementById('detalleOrdenModal'));
                    if (modalDetalle) modalDetalle.hide();
                    
                    // Refrescar calendario
                    calendar.refetchEvents();
                } else {
                    toastr.error(response.message || 'Error al eliminar la orden');
                }
            },
            error: function(xhr) {
                let mensaje = 'Error al eliminar la orden';
                try {
                    const jsonResponse = JSON.parse(xhr.responseText);
                    mensaje = jsonResponse.message || mensaje;
                } catch(e) {}
                toastr.error(mensaje);
            }
        });
    }

    // ============ DISTRIBUCIÓN ============

    function abrirDistribucion(orden) {
        document.getElementById('ordenIdDist').value = orden.id;
        document.getElementById('distCantidadTotal').textContent = orden.cantidad_requerida;
        document.getElementById('distUnidad').textContent = orden.unidad_medida || 'lb';
        document.getElementById('distUnidad2').textContent = orden.unidad_medida || 'lb';
        document.getElementById('distJobId').textContent = orden.job_id;
        document.getElementById('distFechas').textContent = orden.fecha_programada;
        document.getElementById('distFechaFin').textContent = orden.fecha_entrega;

        const inicio = new Date(orden.fecha_programada);
        const fin = new Date(orden.fecha_entrega);
        const diasTotales = Math.floor((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
        
        document.getElementById('distDiasCount').textContent = diasTotales;
        document.getElementById('distPromedioDay').textContent = (orden.cantidad_requerida / diasTotales).toFixed(2);

        generarDistribucion(orden.cantidad_requerida, orden.fecha_programada, orden.fecha_entrega);
        
        const modalDetalle = bootstrap.Modal.getInstance(document.getElementById('detalleOrdenModal'));
        if (modalDetalle) {
            modalDetalle.hide();
        }
        
        const modalDist = new bootstrap.Modal(document.getElementById('distribuirCantidadModal'));
        modalDist.show();
    }

    function generarDistribucion(cantidad, fechaInicio, fechaFin) {
        const contenedor = document.getElementById('distribucionDias');
        contenedor.innerHTML = '';
        distribucionActual = {};

        let fechaActual = new Date(fechaInicio);
        const fechaFinal = new Date(fechaFin);
        let diasTotales = 0;

        let temp = new Date(fechaInicio);
        while (temp <= fechaFinal) {
            diasTotales++;
            temp.setDate(temp.getDate() + 1);
        }

        const cantidadPorDia = cantidad / diasTotales;
        let totalAsignado = 0;

        for (let i = 0; i < diasTotales; i++) {
            const fecha = fechaActual.toISOString().split('T')[0];
            let cantAsignada = cantidadPorDia;
            
            if (i === diasTotales - 1) {
                cantAsignada = cantidad - totalAsignado;
            }

            cantAsignada = Math.round(cantAsignada * 100) / 100;
            distribucionActual[fecha] = cantAsignada;
            totalAsignado += cantAsignada;

            const diaDiv = document.createElement('div');
            diaDiv.className = 'distribucion-dia';
            diaDiv.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">${fechaActual.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' })}</small><br>
                        <strong>${fecha}</strong>
                    </div>
                    <div class="col-md-6">
                        <input type="number" 
                               class="form-control input-cantidad distribucion-cantidad" 
                               data-fecha="${fecha}"
                               value="${cantAsignada}" 
                               step="0.01" 
                               min="0">
                    </div>
                </div>
            `;

            contenedor.appendChild(diaDiv);

            const input = diaDiv.querySelector('input');
            input.addEventListener('change', function() {
                distribucionActual[fecha] = parseFloat(this.value) || 0;
                actualizarResumen();
            });

            fechaActual.setDate(fechaActual.getDate() + 1);
        }

        actualizarResumen();
    }

    function actualizarResumen() {
        const total = parseFloat(document.getElementById('distCantidadTotal').textContent);
        const asignado = Object.values(distribucionActual).reduce((a, b) => a + parseFloat(b || 0), 0);
        const diferencia = total - asignado;
        const porcentaje = (asignado / total) * 100;

        document.getElementById('distTotalAsignado').textContent = asignado.toFixed(2);
        document.getElementById('distDiferencia').textContent = diferencia.toFixed(2);
        document.getElementById('barraProgreso').style.width = Math.min(porcentaje, 100) + '%';
        
        const diffElem = document.getElementById('distDiferencia');
        if (Math.abs(diferencia) < 0.01) {
            diffElem.className = 'fw-bold text-success';
        } else {
            diffElem.className = 'fw-bold text-danger';
        }
    }

    document.getElementById('btnAutoDistribuir').addEventListener('click', function() {
        const total = parseFloat(document.getElementById('distCantidadTotal').textContent);
        const inputs = document.querySelectorAll('.distribucion-cantidad');
        const dias = inputs.length;
        const cantidadPorDia = total / dias;

        let totalAcumulado = 0;
        inputs.forEach((input, index) => {
            if (index === dias - 1) {
                const resto = parseFloat((total - totalAcumulado).toFixed(2));
                input.value = resto;
                distribucionActual[input.dataset.fecha] = resto;
            } else {
                const cant = parseFloat(cantidadPorDia.toFixed(2));
                input.value = cant;
                distribucionActual[input.dataset.fecha] = cant;
                totalAcumulado += cant;
            }
        });

        actualizarResumen();
        toastr.success('Distribución automática calculada');
    });

    document.getElementById('btnGuardarDistribucion').addEventListener('click', function() {
        const total = parseFloat(document.getElementById('distCantidadTotal').textContent);
        const asignado = Object.values(distribucionActual).reduce((a, b) => a + parseFloat(b || 0), 0);

        if (Math.abs(asignado - total) > 0.01) {
            toastr.error(`Total distribuido debe ser igual a ${total.toFixed(2)}`);
            return;
        }

        const ordenId = document.getElementById('ordenIdDist').value;
        
        const postData = { orden_id: ordenId };
        for (const [fecha, cantidad] of Object.entries(distribucionActual)) {
            postData[`distribucion[${fecha}]`] = cantidad;
        }

        $.ajax({
            url: baseUrl + '/planificador/distribuir-cantidad',
            method: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success('Distribución guardada correctamente');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('distribuirCantidadModal'));
                    if (modal) modal.hide();
                    calendar.refetchEvents();
                } else {
                    toastr.error(response.message || 'Error al guardar distribución');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                toastr.error('Error al guardar distribución');
            }
        });
    });

});