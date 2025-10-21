<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Producción - Planificador</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="./assets/css/planificador/planificador.css">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-calendar-check"></i> Planificador</h4>
        <small>Gestión de Producción</small>
    </div>
    
    <div class="mb-3">
        <small class="text-white-50">Usuario:</small>
        <div class="fw-bold">Planificador</div>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="#" class="active">
                <i class="bi bi-calendar3"></i>
                Agenda de Producción
            </a>
        </li>
        <li>
            <a href="#" data-bs-toggle="modal" data-bs-target="#nuevaOrdenModal">
                <i class="bi bi-plus-circle"></i>
                Nueva Orden
            </a>
        </li>
        <li>
            <a href="#" data-bs-toggle="modal" data-bs-target="#buscarOrdenModal">
                <i class="bi bi-search"></i>
                Buscar Orden
            </a>
        </li>
        <li>
            <a href="/timeControl/public/logout">
                <i class="bi bi-box-arrow-right"></i>
                Cerrar Sesión
            </a>
        </li>
    </ul>

    <div class="mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.2);">
        <small class="text-white-50 d-block mb-2">📊 ESTADÍSTICAS</small>
        <div class="mb-2">
            <small>Pendientes: <span class="float-end fw-bold" id="statPendientes">0</span></small>
        </div>
        <div class="mb-2">
            <small>En Proceso: <span class="float-end fw-bold" id="statEnProceso">0</span></small>
        </div>
    </div>
</aside>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h2 class="mb-0">📅 Agenda de Producción</h2>
            <small class="text-muted">Gestión y planificación de órdenes de trabajo</small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#buscarOrdenModal">
                <i class="bi bi-search me-2"></i>Buscar Orden
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaOrdenModal">
                <i class="bi bi-plus-lg me-2"></i>Nueva Orden
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-section">
        <h6 class="mb-3"><i class="bi bi-funnel-fill me-2"></i>Filtros de Búsqueda</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-building"></i> Área</label>
                <select id="filtroArea" class="form-select">
                    <option value="">Todas las áreas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-gear-fill"></i> Máquina</label>
                <select id="filtroMaquina" class="form-select">
                    <option value="">Todas las máquinas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-clipboard-check-fill"></i> Estado</label>
                <select id="filtroEstado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="completada">Completada</option>
                    <option value="pausada">Pausada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button id="btnAplicarFiltros" class="btn btn-dark w-100">
                    <i class="bi bi-search me-2"></i>Aplicar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Calendario -->
    <div class="calendar-container">
        <div id="calendar"></div>
    </div>
</main>

<!-- Modal: Buscar Orden por JOB ID -->
<div class="modal fade" id="buscarOrdenModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-search me-2"></i>Buscar Orden de Producción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">JOB ID</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                        <input type="text" id="inputBuscarJobId" class="form-control" placeholder="Ej: JOB-2024-001" autofocus>
                        <button class="btn btn-primary" id="btnBuscarOrden">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                    <small class="text-muted">Ingrese el JOB ID completo de la orden</small>
                </div>

                <!-- Resultado de búsqueda -->
                <div id="resultadoBusqueda" style="display: none;">
                    <div class="alert alert-success">
                        <h6 class="mb-3"><i class="bi bi-check-circle-fill me-2"></i>Orden Encontrada</h6>
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-6"><strong>JOB ID:</strong></div>
                                <div class="col-6 text-end"><span id="resJobId"></span></div>
                                <div class="col-6"><strong>Item:</strong></div>
                                <div class="col-6 text-end"><span id="resItem"></span></div>
                                <div class="col-6"><strong>Cliente:</strong></div>
                                <div class="col-6 text-end"><span id="resCliente"></span></div>
                                <div class="col-6"><strong>Estado:</strong></div>
                                <div class="col-6 text-end"><span id="resEstado" class="badge"></span></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="d-block mb-1 text-muted">Progreso de Producción</small>
                            <div class="progress" style="height: 30px;">
                                <div id="resProgreso" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">
                                    <span id="resPorcentaje" class="fw-bold">0%</span>
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                Producido: <strong><span id="resProducido">0</span></strong> / <strong><span id="resRequerido">0</span></strong>
                            </small>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="#" id="btnVerReporte" class="btn btn-success btn-lg" target="_blank">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i>Ver Reporte Completo
                        </a>
                        <button type="button" class="btn btn-outline-primary btn-lg" id="btnVerDetalleDesdeBoton">
                            <i class="bi bi-eye me-2"></i>Ver en Calendario
                        </button>
                    </div>
                </div>

                <div id="noEncontrado" style="display: none;">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        No se encontró ninguna orden con ese JOB ID
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nueva Orden -->
<div class="modal fade" id="nuevaOrdenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nueva Orden de Producción</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevaOrden">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">JOB ID *</label>
                            <input type="text" name="job_id" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Item *</label>
                            <input type="text" name="item" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cliente *</label>
                            <input type="text" name="cliente" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PO</label>
                            <input type="text" name="po" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descripción del Producto</label>
                            <textarea name="descripcion_producto" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tamaño/SIZE</label>
                            <input type="text" name="tamano" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Área *</label>
                            <select name="area_id" class="form-select" required>
                                <option value="">Seleccionar área</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Máquina *</label>
                            <select name="maquina_id" class="form-select" required>
                                <option value="">Primero seleccione área</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cantidad *</label>
                            <input type="number" name="cantidad_requerida" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unidad</label>
                            <select name="unidad_medida" class="form-select">
                                <option value="lb">lb</option>
                                <option value="kg">kg</option>
                                <option value="pzas">pzas</option>
                                <option value="cajas">cajas</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Prioridad *</label>
                            <select name="prioridad" class="form-select" required>
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha Programada *</label>
                            <input type="date" name="fecha_programada" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha Entrega *</label>
                            <input type="date" name="fecha_entrega" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notas del Planificador</label>
                            <textarea name="notas_planificador" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Guardar Orden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Detalle de Orden -->
<div class="modal fade" id="detalleOrdenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detalle de Orden</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleOrdenContent">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cerrar
                </button>
                <a href="#" id="btnVerReporteDesdeDetalle" class="btn btn-success" target="_blank">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Ver Reporte
                </a>
                <button type="button" class="btn btn-primary" id="btnDistribuir">
                    <i class="bi bi-distribute-vertical me-2"></i>Distribuir Cantidad
                </button>
                <button type="button" class="btn btn-warning text-dark" id="btnEditarOrden">
                    <i class="bi bi-pencil-square me-2"></i>Editar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Distribuir Cantidad -->
<div class="modal fade" id="distribuirCantidadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-distribute-vertical me-2"></i>Distribuir Cantidad de Producción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <input type="hidden" id="ordenIdDist">

                <!-- Resumen General -->
                <div class="resumen-distribucion">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="d-block mb-1" style="opacity: 0.9;">Cantidad Total Requerida</small>
                            <h3 class="mb-0">
                                <span id="distCantidadTotal">0</span> 
                                <small id="distUnidad">lb</small>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <small class="d-block mb-1" style="opacity: 0.9;">Total Distribuido</small>
                            <h3 class="mb-0">
                                <span id="distTotalAsignado">0</span> 
                                <small id="distUnidad2">lb</small>
                            </h3>
                        </div>
                    </div>
                    <small class="d-block mt-3" style="opacity: 0.9;">
                        Diferencia: <span id="distDiferencia" class="fw-bold fs-5">0</span> lb
                    </small>
                    <div class="progreso-distribucion mt-2">
                        <div id="barraProgreso" style="width: 0%; height: 100%; background: white; transition: width 0.3s;"></div>
                    </div>
                </div>

                <!-- Información de la Orden -->
                <div class="alert alert-info">
                    <div class="row g-2 small">
                        <div class="col-md-6"><strong>Orden:</strong> <span id="distJobId">-</span></div>
                        <div class="col-md-6"><strong>Período:</strong> <span id="distFechas">-</span> a <span id="distFechaFin">-</span></div>
                        <div class="col-md-6"><strong>Total de días:</strong> <span id="distDiasCount">0</span> días</div>
                        <div class="col-md-6"><strong>Promedio/día:</strong> <span id="distPromedioDay">0</span> lb</div>
                    </div>
                </div>

                <!-- Botón distribución automática -->
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-info" id="btnAutoDistribuir">
                        <i class="bi bi-calculator me-2"></i>Distribución Automática (Equitativa)
                    </button>
                </div>

                <!-- Lista de días para distribuir -->
                <div id="distribucionDias">
                    <!-- Se genera dinámicamente con JavaScript -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarDistribucion">
                    <i class="bi bi-check2-circle me-2"></i>Guardar Distribución
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="./assets/js/planificador/Validaciones.js"></script>
<script src="./assets/js/planificador/tipoColores.js"></script>
<script src="./assets/js/planificador/planificador.js"></script>



</body>
</html>