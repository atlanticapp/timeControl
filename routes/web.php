<?php

use App\Controllers\AccionController;
use App\Controllers\AuthController;
use App\Controllers\ComentarioController;
use App\Controllers\CorreccionController;
use App\Controllers\CorreccionesOperadorController;
use App\Core\Router;
use App\Controllers\MaquinaController;
use App\Controllers\DataController;
use App\Controllers\DestinoDestruccionController;
use App\Controllers\DestinoProdController;
use App\Controllers\DestinoRetrabajoController;
use App\Controllers\NotificacionController;
use App\Controllers\ProduccionController;
use App\Controllers\QaController;
use App\Helpers\AuthHelper;
use App\Controllers\RegistroController;
use App\Controllers\ReporteEntregaController;
use App\Controllers\RetencionController;
use App\Controllers\SaveVelocidad;
use App\Controllers\SupervisorController;
use App\Controllers\ReporteScrapController;
use App\Controllers\PlanificadorController;
use App\Models\DestinoDestruccion;

// Instancia del router
$router = new Router();

// Rutas para autenticación, Login y registro
$router->get('/', [AuthController::class, 'login']);
$router->get('/login', [AuthController::class, 'login']);   
$router->post('/login', [AuthController::class, 'login']);  // Procesar login
$router->post('/register', [AuthController::class, 'register']); // Procesar registro
$router->get('/getStatus', [AuthController::class, 'getStatus']); // Consultar estado de sesión
$router->get('/logout', [AuthController::class, 'logout']); // Cerrar sesión

// Ruta para mostrar el formulario de recuperación de contraseña
$router->get('/forgot_password', [AuthController::class, 'forgotPassword']);

// Ruta para procesar el cambio de contraseña
$router->post('/reset_password', [AuthController::class, 'resetPassword']);

// Rutas para manejar operaciones de máquina operador
$router->get('/datos_trabajo_maquina', [MaquinaController::class, 'index']);
$router->post('/seleccionar_maquina', [MaquinaController::class, 'seleccionarMaquina']);
$router->get('/ordenes_diarias', [MaquinaController::class, 'ordenesDiarias']);

// Ruta para seleccionar una orden específica para trabajar
$router->post('/seleccionar_orden', [MaquinaController::class, 'seleccionarOrden']);

// Ruta AJAX para obtener detalles de una orden
$router->get('/obtener_detalle_orden', [MaquinaController::class, 'obtenerDetalleOrden']);

// Rutas para manejar datos de trabajo
$router->get('/datos_trabajo', [DataController::class, 'index']);
$router->post('/seleccionar_data', [DataController::class, 'seleccionarData']);

// Espera Trabajo
$router->post('/espera_trabajo', [DataController::class, 'esperaTrabajo']);




// Control de registros
$router->get('/control', [RegistroController::class, 'index']);
$router->post('/registrar', [RegistroController::class, 'registrar']);

// Velocidad
$router->post('/saveVelocidad', [SaveVelocidad::class, 'saveVelocidad']);

// Comentario
$router->post('/addComentario', [ComentarioController::class, 'addComentario']);

// Correcciones Operador
$router->get('/correcciones', [CorreccionesOperadorController::class, 'correccionesPendientes']);
$router->post('/procesarCorreccion', [CorreccionesOperadorController::class, 'procesarCorreccion']);


// Rutas QA (mantengo como estaban)
$router->get('/dashboard', [QaController::class, 'index']);
$router->get('/checkNewNotifications', [NotificacionController::class, 'checkNewNotifications']);
$router->get('/validacion', [QaController::class, 'validacion']);
$router->get('/verificarEstadoPendiente', [QaController::class, 'verificarEstadoPendiente']);
$router->get('/verificarEstadosRegistros', [QaController::class, 'verificarEstadosRegistros']);
$router->post('/validarScrap', [QaController::class, 'validarScrap']);
$router->post('/validarProduccion', [QaController::class, 'validarProduccion']);
$router->post('/revisar', [CorreccionController::class, 'revisar']);
$router->get('/revisiones', [CorreccionController::class, 'index']);
$router->post('/cancelar', [CorreccionController::class, 'cancelar']);
$router->post('/solicitarCorreccion', [CorreccionController::class, 'solicitarCorreccion']);

// Rutas Destinos (QA)
$router->get('/destinos/destruccion', [DestinoDestruccionController::class, 'index']);
$router->get('/destinos/produccion', [DestinoProdController::class, 'index']);
$router->get('/destinos/retrabajo', [DestinoRetrabajoController::class, 'index']);


// Rutas para Reportes de Scrap
$router->get('/reporte_scrap', [ReporteScrapController::class, 'reporteScrap']);
$router->get('/reporte_scrap/detalle/{id}', [ReporteScrapController::class, 'detalle']);

// ===== RUTAS PARA ACCION QA (NUEVAS/CORREGIDAS) =====
$router->get('/accion', [AccionController::class, 'accion']);
$router->post('/guardarProduccion', [AccionController::class, 'guardarProduccion']);

$router->post('/accion/retener', [AccionController::class, 'retener']);

// ===== RUTAS PARA RETENCIONES (existentes) =====
$router->get('/retenciones', [RetencionController::class, 'index']);
$router->post('/retener', [RetencionController::class, 'crearRetencion']); 
$router->post('/asignarDestinos', [RetencionController::class, 'asignarDestino']);

$router->get('/reporte-entrega', [ReporteEntregaController::class, 'reporteEntrega']);
$router->get('/reporte-entrega/detalle/{id}', [ReporteEntregaController::class, 'detalle']);
$router->post('/guardar-entrega', [ReporteEntregaController::class, 'guardarEntrega']);
$router->post('/marcar-impresa', [ReporteEntregaController::class, 'marcarImpresa']);

// Rutas de supervisor (corregidas para consistencia)
$router->get('/supervisor', [SupervisorController::class, 'index']);
$router->post('/supervisor', [SupervisorController::class, 'index']);
$router->get('/supervisor/revisiones', [SupervisorController::class, 'revisiones']);

$router->post('/supervisor/cancelar', [SupervisorController::class, 'cancelarCorreccion']);

// Rutas específicas para supervisor (usamos subrutas para acciones)
$router->post('/supervisor/validarProduccion', [SupervisorController::class, 'validarProduccion']);
$router->post('/supervisor/validarScrap', [SupervisorController::class, 'validarScrap']);
$router->post('/supervisor/solicitarCorreccion', [SupervisorController::class, 'solicitarCorreccion']);
$router->post('/supervisor/rechazarEntrega', [SupervisorController::class, 'rechazarEntrega']);
$router->post('/supervisor/revisar', [SupervisorController::class, 'revisar']); // Nueva ruta para revisar en supervisor

// Rutas AJAX para supervisor
$router->get('/supervisor/verificarEstadosRegistros', [SupervisorController::class, 'verificarEstadosRegistros']);
$router->get('/supervisor/verificarEstadoPendiente', [SupervisorController::class, 'verificarEstadoPendiente']);
$router->get('/supervisor/getRegistroDetails', [SupervisorController::class, 'getRegistroDetails']);

// Rutas del Planificador
$router->get('/planificador', [PlanificadorController::class, 'index']);
$router->post('/planificador/crear', [PlanificadorController::class, 'crearOrden']);
$router->post('/planificador/actualizar', [PlanificadorController::class, 'actualizarOrden']);
$router->post('/planificador/cambiar-fecha', [PlanificadorController::class, 'cambiarFecha']);
$router->post('/planificador/cambiar-estado', [PlanificadorController::class, 'cambiarEstado']);
$router->get('/planificador/obtener-ordenes-calendario', [PlanificadorController::class, 'obtenerOrdenesCalendario']);
$router->get('/planificador/obtener-detalle-orden', [PlanificadorController::class, 'obtenerDetalleOrden']);
$router->get('/planificador/obtener-maquinas-por-area', [PlanificadorController::class, 'obtenerMaquinasPorArea']);

$router->get('/planificador/obtener-areas', [PlanificadorController::class, 'obtenerAreas']);

$router->post('/planificador/distribuir-cantidad', [PlanificadorController::class, 'distribuirCantidad']);
$router->get('/planificador/obtener-distribucion', [PlanificadorController::class, 'obtenerDistribucion']);
$router->get('/planificador/obtener-metas-por-area', [PlanificadorController::class, 'obtenerMetasPorArea']);
$router->post('/planificador/actualizar-produccion-diaria', [PlanificadorController::class, 'actualizarProduccionDiaria']);
$router->get('/planificador/reporte', [PlanificadorController::class, 'verReporteOrden']);
$router->get('/planificador/buscar-orden', [PlanificadorController::class, 'buscarOrdenPorJobId']);
$router->post('/planificador/guardar-edicion', [PlanificadorController::class, 'guardarEdicion']);
$router->post('/planificador/eliminar-orden', [PlanificadorController::class, 'eliminarOrden']);








// Error handling
$router->get('/error', function () {
    $status = isset($_SESSION['status']) ? $_SESSION['status'] : 'error';
    $message = isset($_SESSION['message']) ? $_SESSION['message'] : 'Hubo un error. Por favor, intenta nuevamente.';
    unset($_SESSION['status']);
    unset($_SESSION['message']);
    if ($status === 'error' && empty($message)) {
        $message = 'Ocurrió un error inesperado. Intenta nuevamente más tarde.';
    }
    echo "
        <div style='text-align: center; padding: 20px; font-family: Arial, sans-serif;'>
            <h2 style='color: " . ($status == 'success' ? '#5bc0de' : '#d9534f') . ";'>" . ucfirst($status) . "</h2>
            <p>{$message}</p>
            <a href='javascript:history.back()' style='display: inline-block; padding: 10px 15px; color: white; background-color: #d9534f; text-decoration: none; border-radius: 5px;'>Regresar</a>
        </div>
    ";
});

// Ejecutar el router
$router->run();