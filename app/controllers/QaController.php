<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Logger;
use App\Models\Notificacion;
use App\Models\Qa;
use App\Models\ValidacionModel;

class QaController extends Controller
{
    private $qa;
    private $validacionModel;

    public function __construct()
    {
        $this->qa = new Qa();
        $this->validacionModel = new ValidacionModel();
        

        if (!AuthHelper::isAuthenticated()) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Debes iniciar sesión.');
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user || !in_array($user->tipo_usuario, ['qa', 'supervisor'])) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado.');
        }
    }

   
    public function accion()
{
    $user = AuthHelper::getCurrentUser();

    try {
        
        $entregas_produccion = $this->qa->getEntregasValidadasProduccion($user->codigo_empleado);
        $entregas_scrap = $this->qa->getEntregasValidadasScrap($user->codigo_empleado); // NUEVO
        
        Logger::info('Cargando página de acción QA', [
            'user_id' => $user->codigo_empleado,
            'total_produccion' => count($entregas_produccion),
            'total_scrap' => count($entregas_scrap)
        ]);

        $this->view('qa/accion', [
            'data' => [
                'title' => 'Acción QA - Entregas Validadas',
                'entregas_validadas' => array_merge($entregas_produccion, $entregas_scrap), // Combinar ambos
                'user' => $user
            ]
        ]);
    } catch (\Exception $e) {
        Logger::error('Error al cargar la página de acción QA', [
            'error' => $e->getMessage(),
            'user_id' => $user->codigo_empleado
        ]);
        $this->redirectWithMessage('/timeControl/public/qa', 'error', 'Error al cargar las entregas validadas.');
    }
}

    public function index()
    {
        $user = AuthHelper::getCurrentUser();

        try {
            $notificaciones = (new Notificacion())->getPendingNotifications($user->area_id);

            
            $destinosStats = $this->qa->getDestinosStats($user->area_id, $user->codigo_empleado);

            
            $stats = $this->qa->getDashboardStats($user->area_id);

            $this->view('qa/dashboard', [
                'data' => [
                    'title' => 'Dashboard de Control de Calidad',
                    'stats' => $stats,
                    'entregasPendientes' => $this->qa->getEntregasPendientes($user->area_id),
                    'notificaciones' => $notificaciones,
                    'entregas_validadas' => $this->qa->getEntregasValidadasProduccion($user->codigo_empleado),
                    'destinos' => $destinosStats,
                    'revisiones_pendientes' => $stats['en_proceso'] ?? 0
                ]
            ]);
        } catch (\Exception $e) {
            Logger::error('Error en dashboard QA', [
                'error' => $e->getMessage(),
                'user_id' => $user->codigo_empleado
            ]);
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Error al cargar el dashboard.');
        }
    }

    public function validacion()
    {
        $user = AuthHelper::getCurrentUser();

        try {
            // Obtener entregas pendientes separadas
            $entregasProduccion = $this->qa->getEntregasProduccionPendientes($user->area_id);
            $entregasScrap = $this->qa->getEntregasScrapPendientes($user->area_id);

            
            $stats = $this->qa->getDashboardStats($user->area_id);

            
            $timestamp_actual = date('Y-m-d H:i:s');

            $this->view('qa/validacion', [
                'data' => [
                    'title' => 'Validación de Entregas',
                    'entregas_produccion' => $entregasProduccion,
                    'entregas_scrap' => $entregasScrap,
                    'stats' => [
                        'pendientes' => $stats['pendientes'] ?? 0,
                        'produccion_pendiente' => $stats['produccion_pendiente'] ?? 0,
                        'scrap_pendiente' => $stats['scrap_pendiente'] ?? 0
                    ],
                    'timestamp_consulta' => $timestamp_actual,
                    'area_id' => $user->area_id
                ]
            ]);
        } catch (\Exception $e) {
            Logger::error('Error al cargar la página de validación', [
                'error' => $e->getMessage(),
                'user_id' => $user->codigo_empleado
            ]);
            $this->redirectWithMessage('/timeControl/public/qa', 'error', 'Error al cargar las entregas pendientes.');
        }
    }

    /**
     * Verifica los estados actuales de un conjunto de registros
     */
    public function verificarEstadosRegistros()
    {
        try {
            $user = AuthHelper::getCurrentUser();

            if (!$this->validateGetRequest(['ids'])) {
                return $this->sendJsonResponse(false, 'Parámetros requeridos faltantes', 400);
            }

            // Validar y procesar IDs
            $registrosIds = array_unique(
                array_filter(
                    explode(',', $_GET['ids']),
                    function ($id) {
                        return is_numeric($id) && $id > 0;
                    }
                )
            );

            if (empty($registrosIds)) {
                return $this->sendJsonResponse(false, 'Lista de IDs inválida', 400);
            }

            
            $estados = $this->qa->verificarEstadosRegistros(
                $registrosIds,
                $user->area_id
            );

            return $this->sendJsonResponse(true, '', 200, [
                'estados' => $estados,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Logger::error('QA_EstadosRegistros_Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => $user->codigo_empleado,
                'input' => $_GET
            ]);

            return $this->sendJsonResponse(false, 'Error interno al verificar estados', 500);
        }
    }

    
    public function verificarEstadoPendiente()
    {
        try {
            $user = AuthHelper::getCurrentUser();

            // Validación de entrada
            if (!$this->validateGetRequest(['id'])) {
                return $this->sendJsonResponse(false, 'Parámetros requeridos faltantes', 400);
            }

            $registroId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if (!$registroId || $registroId < 1) {
                return $this->sendJsonResponse(false, 'ID de registro inválido', 400);
            }

            // Verificar existencia y estado
            $esPendiente = $this->qa->verificarRegistroPendiente($registroId, $user->area_id);

            return $this->sendJsonResponse(true, '', 200, [
                'pendiente' => $esPendiente,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Logger::error('QA_EstadoPendiente_Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => $user->codigo_empleado,
                'input' => $_GET
            ]);

            return $this->sendJsonResponse(false, 'Error interno al verificar estado', 500);
        }
    }


  
    public function validarScrap()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendJsonResponse(false, 'Método no permitido', 405);
        }

        $user = AuthHelper::getCurrentUser();
        $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_FLOAT);
        $observaciones = trim($_POST['comentario'] ?? '');

        Logger::info('Solicitud de validación de scrap recibida', compact('registroId', 'cantidad') + ['user_id' => $user->codigo_empleado]);

        if (!$registroId) {
            Logger::warning('ID de registro inválido al validar scrap', [
                'user_id' => $user->codigo_empleado, 
                'registro_id' => $_POST['id'] ?? 'no-id'
            ]);
            return $this->sendJsonResponse(false, 'ID de registro inválido', 400);
        }

        if ($cantidad <= 0) {
            Logger::warning('Cantidad inválida al validar scrap', 
                compact('registroId', 'cantidad') + ['user_id' => $user->codigo_empleado]
            );
            return $this->sendJsonResponse(false, 'La cantidad debe ser mayor que cero', 400);
        }

        try {
            $resultado = $this->validacionModel->validarScrap($registroId, $cantidad, $observaciones, $user->codigo_empleado);

            $logLevel = $resultado['success'] ? 'info' : 'warning';
            $logMessage = $resultado['success'] ? 'Validación de scrap exitosa' : 'Error en validación de scrap';
            Logger::$logLevel($logMessage, compact('registroId', 'cantidad') + [
                'user_id' => $user->codigo_empleado, 
                'mensaje' => $resultado['message']
            ]);

            return $this->sendJsonResponse(
                $resultado['success'], 
                $resultado['message'],
                $resultado['success'] ? 200 : 400
            );

        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'QaController',
                'method' => 'validarScrap',
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId,
                'cantidad' => $cantidad
            ]);

            return $this->sendJsonResponse(false, 'Error al procesar la validación: ' . $e->getMessage(), 500);
        }
    }

   
    public function validarProduccion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendJsonResponse(false, 'Método no permitido', 405);
        }

        $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $user = AuthHelper::getCurrentUser();

        Logger::info('Solicitud de validación de producción recibida', [
            'user_id' => $user->codigo_empleado,
            'registro_id' => $registroId
        ]);

        if (!$registroId) {
            Logger::warning('Intento de validar producción con ID inválido', [
                'user_id' => $user->codigo_empleado,
                'registro_id' => $_POST['id'] ?? 'no-id'
            ]);
            return $this->sendJsonResponse(false, 'ID de registro inválido', 400);
        }

        try {
            $resultado = $this->validacionModel->validarProduccion($registroId, $user->codigo_empleado);

            if ($resultado['success']) {
                $this->registrarHistorialValidacion($registroId, 'produccion', $user->codigo_empleado);

                Logger::info('Validación de producción exitosa', [
                    'user_id' => $user->codigo_empleado,
                    'registro_id' => $registroId,
                    'validacion_id' => $resultado['validacion_id'] ?? null
                ]);
            } else {
                Logger::warning('Error en validación de producción', [
                    'user_id' => $user->codigo_empleado,
                    'registro_id' => $registroId,
                    'error' => $resultado['message']
                ]);
            }

            return $this->sendJsonResponse(
                $resultado['success'], 
                $resultado['message'],
                $resultado['success'] ? 200 : 400
            );

        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'QaController',
                'method' => 'validarProduccion',
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId
            ]);
            
            return $this->sendJsonResponse(false, 'Error al procesar la validación: ' . $e->getMessage(), 500);
        }
    }

   
    public function revisar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendJsonResponse(false, 'Método no permitido', 405);
        }

        $user = AuthHelper::getCurrentUser();
        $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
        $motivo = trim($_POST['motivo'] ?? '');

        Logger::info('Solicitud de revisión recibida (QA)', [
            'user_id' => $user->codigo_empleado,
            'registro_id' => $registroId,
            'tipo' => $tipo,
            'motivo' => $motivo
        ]);

        if (!$registroId || !$tipo) {
            return $this->sendJsonResponse(false, 'Datos de solicitud inválidos', 400);
        }

        try {
            $resultado = $this->validacionModel->solicitarCorreccion($registroId, $tipo, $motivo, $user->codigo_empleado);
            
            Logger::info('Resultado de solicitud de corrección (QA)', [
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId,
                'success' => $resultado['success'],
                'mensaje' => $resultado['message']
            ]);

            return $this->sendJsonResponse(
                $resultado['success'], 
                $resultado['message'],
                $resultado['success'] ? 200 : 400,
                ['reload' => true]  // Indicar que debe recargar la página
            );

        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'QaController',
                'method' => 'revisar',
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId
            ]);
            
            return $this->sendJsonResponse(false, 'Error al procesar la revisión: ' . $e->getMessage(), 500);
        }
    }

    
    public function solicitarCorreccion()
{
   
    if (ob_get_level()) {
        ob_clean();
    }
 
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $user = AuthHelper::getCurrentUser();
    $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
    $motivo = trim($_POST['motivo'] ?? '');

    
    error_log("QA solicitarCorreccion - ID: $registroId, Tipo: $tipo, Usuario: {$user->codigo_empleado}");

    if (!$registroId || !$tipo) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Datos de solicitud inválidos'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $resultado = $this->validacionModel->solicitarCorreccion(
            $registroId, 
            $tipo, 
            $motivo, 
            $user->codigo_empleado
        );
        
        // Log del resultado
        error_log("QA solicitarCorreccion resultado: " . ($resultado['success'] ? 'SUCCESS' : 'FAIL'));
        
        http_response_code($resultado['success'] ? 200 : 400);
        echo json_encode([
            'success' => $resultado['success'],
            'message' => $resultado['message'],
            'reload' => true
        ], JSON_UNESCAPED_UNICODE);
        exit;

    } catch (\Exception $e) {
        error_log("QA solicitarCorreccion ERROR: " . $e->getMessage());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

   
    private function registrarHistorialValidacion($registroId, $tipo, $usuarioId)
    {
        try {
            
            Logger::info('Registrando historial de validación', [
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'usuario_id' => $usuarioId
            ]);
        } catch (\Exception $e) {
            Logger::warning('Error al registrar historial de validación', [
                'error' => $e->getMessage(),
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'usuario_id' => $usuarioId
            ]);
        }
    }

    private function sendJsonResponse($success, $message, $statusCode = 200, $data = [])
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);

        $response = [
            'success' => $success,
            'message' => $message,
            'code' => $statusCode
        ] + $data;

        echo json_encode($response);
        exit();
    }

    private function validateGetRequest($requiredParams)
    {
        foreach ($requiredParams as $param) {
            if (!isset($_GET[$param]) || empty($_GET[$param])) {
                return false;
            }
        }
        return true;
    }

    private function logAndRedirect($logLevel, $logMessage, array $context, $type, $message)
    {
        Logger::$logLevel($logMessage, $context);
        $this->redirectWithMessage('/timeControl/public/validacion', $type, $message);
    }

    private function redirectWithMessage($url, $status, $message)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;

        header("Location: $url");
        exit();
    }
}