<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Logger;
use App\Models\Usuario;
use App\Models\Supervisor;
use App\Models\ValidacionModel;

class SupervisorController extends Controller
{
    private $jwt_secret;
    private $supervisor;
    private $validacionModel;

    public function __construct()
    {
        global $jwt_secret;
        $this->jwt_secret = $jwt_secret;
        $this->supervisor = new Supervisor();
        $this->validacionModel = new ValidacionModel();
        

        if (!AuthHelper::isAuthenticated()) {
            header('Location: /timeControl/public/login');
            exit();
        }

        $user = AuthHelper::getCurrentUser();
        if ($user->tipo_usuario !== 'supervisor') {
            AuthHelper::logout();
        }
    }

    public function index()
    {
        try {
            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            $usuario = new Usuario();
            $area_id = $user->area_id;
            
            // Debug logs
            error_log("=== DEBUG SupervisorController ===");
            error_log("Usuario: " . $user->codigo_empleado);
            error_log("Area ID: " . ($area_id ?: 'NULL'));

            $area = $usuario->getNameArea($area_id);
            if (!$area || $area === "Área Desconocida") {
                error_log("ADVERTENCIA: Area ID {$area_id} no encontrada para supervisor {$user->codigo_empleado}");
                $area = "Área Desconocida";
            }

            // Obtener filtros
            $filters = [
                'codigo_empleado' => $_POST['codigo_empleado'] ?? '',
                'tipo_boton' => $_POST['tipo_boton'] ?? '',
                'maquina' => $_POST['maquina'] ?? '',
                'item' => $_POST['item'] ?? '',
                'jtWo' => $_POST['jtWo'] ?? ''
            ];

            // Obtener datos filtrados por area_id
            $empleados = $this->supervisor->getEmpleadosPorArea($area_id);
            $botones = $this->supervisor->getTiposBotones();
            $maquinas = $this->supervisor->getMaquinasPorArea($area_id);
            $operaciones_abiertas = $this->supervisor->getOperacionesAbiertas($area_id, $filters['codigo_empleado'], $filters['tipo_boton'], $filters['maquina']);
            $produccion = $this->supervisor->getProduccionDiaria($area_id, $filters['item'], $filters['jtWo']);
            
            
            error_log("Llamando a getEntregasPendientes...");
            $validaciones = $this->supervisor->getEntregasPendientes($area_id);
            
            // Debug final
            error_log("=== DATOS FINALES PARA LA VISTA ===");
            error_log("Total entregas producción: " . count($validaciones['entregas_produccion']));
            error_log("Total entregas scrap: " . count($validaciones['entregas_scrap']));
            error_log("Stats: " . print_r($validaciones['stats'], true));

            // Renderizar vista
            $this->view('supervisor/supervisor', compact(
                'user', 
                'area', 
                'empleados', 
                'botones', 
                'maquinas', 
                'operaciones_abiertas', 
                'produccion', 
                'validaciones', 
                'filters'
            ));

        } catch (\Exception $e) {
            error_log("ERROR en index de SupervisorController: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            
            $validaciones = [
                'entregas_produccion' => [],
                'entregas_scrap' => [],
                'stats' => [
                    'pendientes' => 0,
                    'produccion_pendiente' => 0,
                    'scrap_pendiente' => 0
                ]
            ];
            
            $this->view('supervisor/supervisor', [
                'user' => AuthHelper::getCurrentUser(),
                'area' => 'Error al cargar área',
                'empleados' => [],
                'botones' => [],
                'maquinas' => [],
                'operaciones_abiertas' => [],
                'produccion' => ['produccion_por_maquina_empleado' => [], 'totalProduccion' => 0, 'totalScrap' => 0],
                'validaciones' => $validaciones,
                'filters' => [],
                'error_message' => 'Error al cargar los datos: ' . $e->getMessage()
            ]);
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

            // Obtener estados con validación de area 
            $estados = $this->supervisor->verificarEstadosRegistros(
                $registrosIds,
                $user->area_id
            );

            return $this->sendJsonResponse(true, '', 200, [
                'estados' => $estados,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Logger::error('Supervisor_EstadosRegistros_Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => $user->codigo_empleado,
                'input' => $_GET
            ]);

            return $this->sendJsonResponse(false, 'Error interno al verificar estados', 500);
        }
    }

    /**
     * Verifica si un registro específico sigue pendiente 
     */
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
            $esPendiente = $this->supervisor->verificarRegistroPendiente($registroId, $user->area_id);

            return $this->sendJsonResponse(true, '', 200, [
                'pendiente' => $esPendiente,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Logger::error('Supervisor_EstadoPendiente_Error', [
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

        Logger::info('Solicitud de validación de scrap recibida (SUPERVISOR)', compact('registroId', 'cantidad') + ['user_id' => $user->codigo_empleado]);

        if (!$registroId) {
            Logger::warning('ID de registro inválido al validar scrap (SUPERVISOR)', [
                'user_id' => $user->codigo_empleado, 
                'registro_id' => $_POST['id'] ?? 'no-id'
            ]);
            return $this->sendJsonResponse(false, 'ID de registro inválido', 400);
        }

        if ($cantidad <= 0) {
            Logger::warning('Cantidad inválida al validar scrap (SUPERVISOR)', 
                compact('registroId', 'cantidad') + ['user_id' => $user->codigo_empleado]
            );
            return $this->sendJsonResponse(false, 'La cantidad debe ser mayor que cero', 400);
        }

        try {
            $resultado = $this->validacionModel->validarScrapSupervisor($registroId, $cantidad, $observaciones, $user->codigo_empleado);

            return $this->sendJsonResponse(
                $resultado['success'], 
                $resultado['message'],
                $resultado['success'] ? 200 : 400
            );

        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'SupervisorController',
                'method' => 'validarScrap',
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId
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

        Logger::info('Solicitud de validación de producción recibida (SUPERVISOR)', [
            'user_id' => $user->codigo_empleado,
            'registro_id' => $registroId
        ]);

        if (!$registroId) {
            Logger::warning('Intento de validar producción con ID inválido (SUPERVISOR)', [
                'user_id' => $user->codigo_empleado,
                'registro_id' => $_POST['id'] ?? 'no-id'
            ]);
            return $this->sendJsonResponse(false, 'ID de registro inválido', 400);
        }

        try {
            $resultado = $this->validacionModel->validarProduccionSupervisor($registroId, $user->codigo_empleado);

            if ($resultado['success']) {
                $this->registrarHistorialValidacion($registroId, 'produccion', $user->codigo_empleado);
            }

            return $this->sendJsonResponse(
                $resultado['success'], 
                $resultado['message'],
                $resultado['success'] ? 200 : 400
            );

        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'SupervisorController',
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

        Logger::info('Solicitud de revisión recibida (SUPERVISOR)', [
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
            
            Logger::info('Resultado de solicitud de corrección (SUPERVISOR)', [
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId,
                'success' => $resultado['success'],
                'mensaje' => $resultado['message']
            ]);

            return $this->sendJsonResponse(
                $resultado['success'], 
                $resultado['message'],
                $resultado['success'] ? 200 : 400
            );

        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'SupervisorController',
                'method' => 'revisar',
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId
            ]);
            
            return $this->sendJsonResponse(false, 'Error al procesar la revisión: ' . $e->getMessage(), 500);
        }
    }

    
    public function rechazarEntrega()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendJsonResponse(false, 'Método no permitido', 405);
        }

        $user = AuthHelper::getCurrentUser();
        $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $motivo = trim($_POST['motivo_rechazo'] ?? '');

        if (!$registroId) {
            return $this->sendJsonResponse(false, 'ID de registro inválido', 400);
        }

        try {
            if ($this->supervisor->rechazarRegistro($registroId, $motivo)) {
                return $this->sendJsonResponse(true, 'Entrega rechazada correctamente', 200);
            } else {
                return $this->sendJsonResponse(false, 'Error al rechazar la entrega', 400);
            }
        } catch (\Exception $e) {
            Logger::error("Error en rechazarEntrega: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'Error al procesar el rechazo: ' . $e->getMessage(), 500);
        }
    }
  
    private function registrarHistorialValidacion($registroId, $tipo, $usuarioId)
    {
        try {
            // Si tienes una tabla de historial, aquí puedes registrar la acción
            // Este método es opcional y depende de tu implementación específica
            Logger::info('Registrando historial de validación (SUPERVISOR)', [
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'usuario_id' => $usuarioId
            ]);
        } catch (\Exception $e) {
            Logger::warning('Error al registrar historial de validación (SUPERVISOR)', [
                'error' => $e->getMessage(),
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'usuario_id' => $usuarioId
            ]);
        }
    }

    
    public function solicitarCorreccion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendJsonResponse(false, 'Método no permitido', 405);
        }

        $user = AuthHelper::getCurrentUser();
        $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
        $motivo = trim($_POST['motivo'] ?? '');

        Logger::info('Solicitud de corrección recibida (SUPERVISOR)', [
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
            
            Logger::info('Resultado de solicitud de corrección (SUPERVISOR)', [
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
                'controller' => 'SupervisorController',
                'method' => 'solicitarCorreccion',
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId
            ]);
            
            return $this->sendJsonResponse(false, 'Error al procesar la solicitud: ' . $e->getMessage(), 500);
        }
    }


    public function getRegistroDetails()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $registroId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $user = AuthHelper::getCurrentUser();

        if (!$registroId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de registro inválido']);
            return;
        }

        try {
            $registro = $this->supervisor->getRegistroDetails($registroId, $user->area_id);
            
            if ($registro) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $registro
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Registro no encontrado']);
            }
        } catch (\Exception $e) {
            error_log("Error en getRegistroDetails: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
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
        $this->redirectWithMessage('/timeControl/public/supervisor', $type, $message);
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


    public function debugValidaciones()
    {
        $user = AuthHelper::getCurrentUser();
        $area_id = $user->area_id;
        
        echo "<h2>Debug de Validaciones</h2>";
        echo "<p>Area ID: {$area_id}</p>";
        echo "<p>Usuario: {$user->codigo_empleado}</p>";
        
        try {
            $debug_info = $this->supervisor->debugRegistrosPendientes($area_id);
            echo "<h3>Información de Debug:</h3>";
            echo "<pre>" . print_r($debug_info, true) . "</pre>";
            
            $validaciones = $this->supervisor->getEntregasPendientes($area_id);
            echo "<h3>Datos de Validaciones:</h3>";
            echo "<pre>" . print_r($validaciones, true) . "</pre>";
            
        } catch (\Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            echo "<p style='color: red;'>Trace: " . $e->getTraceAsString() . "</p>";
        }
    }
    public function revisiones()
{
    try {
        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            throw new \Exception("Usuario no autenticado");
        }

        $usuario = new Usuario();
        $area_id = $user->area_id;
        
        error_log("=== DEBUG Revisiones Supervisor ===");
        error_log("Usuario: " . $user->codigo_empleado);
        error_log("Area ID: " . ($area_id ?: 'NULL'));

        $area = $usuario->getNameArea($area_id);
        if (!$area || $area === "Área Desconocida") {
            error_log("ADVERTENCIA: Area ID {$area_id} no encontrada");
            $area = "Área Desconocida";
        }

        // Obtener correcciones pendientes
        $correcciones = $this->supervisor->getCorreccionesPendientes($area_id);
        $estadisticas = $this->supervisor->getEstadisticasCorrecciones($area_id);
        
        error_log("Correcciones pendientes encontradas: " . count($correcciones));
        error_log("Estadísticas: " . print_r($estadisticas, true));

        // Preparar datos para la vista
        $data = [
            'titulo' => 'Revisiones Pendientes - Supervisor',
            'area' => $area,
            'correcciones' => $correcciones,
            'estadisticas' => $estadisticas,
            'user' => $user
        ];

        // Renderizar vista de revisiones
        $this->view('supervisor/revisiones', $data);

    } catch (\Exception $e) {
        error_log("ERROR en revisiones de SupervisorController: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Mostrar pagina con datos vacíos en caso de error
        $this->view('supervisor/revisiones', [
            'titulo' => 'Revisiones Pendientes - Supervisor',
            'area' => 'Error al cargar área',
            'correcciones' => [],
            'estadisticas' => [
                'total' => 0,
                'produccion' => 0,
                'scrap' => 0
            ],
            'user' => AuthHelper::getCurrentUser(),
            'error_message' => 'Error al cargar los datos: ' . $e->getMessage()
        ]);
    }
}
public function cancelarCorreccion()
{
    // Verificar metodo HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $this->sendJsonResponse(false, 'Método no permitido', 405);
    }

    $user = AuthHelper::getCurrentUser();
    
    // Log para debugging
    error_log("=== CANCELAR CORRECCIÓN ===");
    error_log("Usuario: " . $user->codigo_empleado);
    error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'no-content-type'));
    
    // Obtener datos del POST (JSON)
    $rawInput = file_get_contents('php://input');
    error_log("Raw input: " . $rawInput);
    
    $input = json_decode($rawInput, true);
    
    // Verificar si hay error en el JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error decodificando JSON: " . json_last_error_msg());
        return $this->sendJsonResponse(false, 'Error al procesar los datos: ' . json_last_error_msg(), 400);
    }
    
    // Obtener y validar ID
    $solicitudId = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
    
    error_log("ID recibido: " . var_export($input['id'] ?? null, true));
    error_log("ID después de filtrar: " . var_export($solicitudId, true));

    Logger::info('Solicitud de cancelación recibida (SUPERVISOR)', [
        'user_id' => $user->codigo_empleado,
        'solicitud_id' => $solicitudId,
        'input_completo' => $input
    ]);

    if (!$solicitudId || $solicitudId < 1) {
        error_log("ID inválido: " . var_export($solicitudId, true));
        return $this->sendJsonResponse(false, 'ID de solicitud inválido', 400);
    }

    try {
        // Verificar que la solicitud pertenezca al area del supervisor
        $stmt = $this->supervisor->getDb()->prepare("
            SELECT sc.id, sc.estado, u.area_id 
            FROM solicitudes_correccion sc
            INNER JOIN registro r ON sc.registro_id = r.id
            INNER JOIN users u ON r.codigo_empleado = u.codigo_empleado
            WHERE sc.id = ?
        ");
        
        $stmt->bind_param("i", $solicitudId);
        $stmt->execute();
        $result = $stmt->get_result();
        $solicitud = $result->fetch_assoc();
        $stmt->close();
        
        if (!$solicitud) {
            error_log("Solicitud no encontrada: {$solicitudId}");
            return $this->sendJsonResponse(false, 'Solicitud no encontrada', 404);
        }
        
        // Verificar que el supervisor tenga permiso 
        if ($solicitud['area_id'] != $user->area_id) {
            error_log("Supervisor {$user->codigo_empleado} intentó cancelar solicitud de área diferente");
            return $this->sendJsonResponse(false, 'No tiene permiso para cancelar esta solicitud', 403);
        }
        
        // Verificar que la solicitud esté en estado Pendiente
        if ($solicitud['estado'] !== 'Pendiente') {
            error_log("Solicitud {$solicitudId} no está pendiente. Estado actual: {$solicitud['estado']}");
            return $this->sendJsonResponse(
                false, 
                'Esta solicitud ya fue procesada y no puede ser cancelada',
                400
            );
        }
        
        // Proceder con la cancelacion
        $resultado = $this->supervisor->cancelarSolicitudCorreccion($solicitudId);
        
        if ($resultado) {
            Logger::info('Corrección cancelada exitosamente (SUPERVISOR)', [
                'user_id' => $user->codigo_empleado,
                'solicitud_id' => $solicitudId
            ]);
            
            return $this->sendJsonResponse(
                true, 
                'Solicitud de corrección cancelada correctamente',
                200
            );
        } else {
            Logger::warning('No se pudo cancelar la corrección (SUPERVISOR)', [
                'user_id' => $user->codigo_empleado,
                'solicitud_id' => $solicitudId
            ]);
            
            return $this->sendJsonResponse(
                false, 
                'No se pudo cancelar la solicitud. Puede que ya haya sido procesada.',
                400
            );
        }

    } catch (\Exception $e) {
        Logger::exception($e, [
            'controller' => 'SupervisorController',
            'method' => 'cancelarCorreccion',
            'user_id' => $user->codigo_empleado,
            'solicitud_id' => $solicitudId
        ]);
        
        error_log("Error en cancelarCorreccion: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        return $this->sendJsonResponse(
            false, 
            'Error al procesar la cancelación: ' . $e->getMessage(), 
            500
        );
    }
}
    
    
}