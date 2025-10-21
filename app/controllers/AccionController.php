<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Qa;
use App\Models\Accion;
use App\Models\Retencion;
use App\Helpers\Logger;
use App\Helpers\AuthHelper;

class AccionController extends Controller
{
    private $qa;
    private $accionModel;
    private $retencionModel;

    public function __construct()
    {
        $this->qa = new Qa();
        $this->accionModel = new Accion();
        $this->retencionModel = new Retencion();
       

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
        // Traer PRODUCCIÓN Y SCRAP validados
        $entregas_produccion = $this->qa->getEntregasValidadasProduccion($user->codigo_empleado);
        $entregas_scrap = $this->qa->getEntregasValidadasScrap($user->codigo_empleado);
        
        
        foreach ($entregas_produccion as &$entrega) {
            $entrega['tipo_entrega'] = 'produccion';
        }
        
        
        foreach ($entregas_scrap as &$entrega) {
            $entrega['tipo_entrega'] = 'scrap';
        }
        
        Logger::info('Cargando página de acción QA', [
            'user_id' => $user->codigo_empleado,
            'total_produccion' => count($entregas_produccion),
            'total_scrap' => count($entregas_scrap)
        ]);

        $this->view('qa/accion', [
            'data' => [
                'title' => 'Acción QA - Entregas Validadas',
                'entregas_validadas' => array_merge($entregas_produccion, $entregas_scrap),
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

    public function guardarProduccion()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $this->sendJsonResponse(false, 'Método no permitido', 405);
    }

    $entrega_id = filter_input(INPUT_POST, 'entrega_id', FILTER_VALIDATE_INT);
    $comentario = trim($_POST['comentario'] ?? '');
    $tipo = trim($_POST['tipo'] ?? 'produccion');
    $user = AuthHelper::getCurrentUser();

    Logger::info('Solicitud de guardar entrega recibida', [
        'user_id' => $user->codigo_empleado,
        'entrega_id' => $entrega_id,
        'tipo' => $tipo
    ]);

    if (!$entrega_id) {
        Logger::warning('Intento de guardar con ID inválido', [
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $_POST['entrega_id'] ?? 'no-id'
        ]);
        return $this->sendJsonResponse(false, 'ID de entrega inválido', 400);
    }

    // Validar tipo
    if (!in_array($tipo, ['produccion', 'scrap'])) {
        return $this->sendJsonResponse(false, 'Tipo de entrega no válido', 400);
    }

    try {
        // ⭐ IMPORTANTE: Usar el modelo Accion para VALIDACIÓN DIRECTA
        // Este método inserta DIRECTAMENTE en produccion_final o scrap_final
        $resultado = $this->accionModel->guardarProduccion($entrega_id, $user->codigo_empleado, $comentario, $tipo);

        if ($resultado['success']) {
            Logger::info('Validación directa exitosa', [
                'user_id' => $user->codigo_empleado,
                'entrega_id' => $entrega_id,
                'tipo' => $tipo,
                'validacion_id' => $resultado['validacion_id'] ?? null
            ]);

            return $this->sendJsonResponse(true, 'Entrega validada correctamente', 200);
        } else {
            Logger::warning('Error en validación directa', [
                'user_id' => $user->codigo_empleado,
                'entrega_id' => $entrega_id,
                'tipo' => $tipo,
                'error' => $resultado['message']
            ]);

            return $this->sendJsonResponse(false, $resultado['message'], 400);
        }
    } catch (\Exception $e) {
        Logger::exception($e, [
            'controller' => 'AccionController',
            'method' => 'guardarProduccion',
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $entrega_id,
            'tipo' => $tipo
        ]);
        return $this->sendJsonResponse(false, 'Error al procesar la validación: ' . $e->getMessage(), 500);
    }
}

// ⭐ MÉTODO PARA RETENER - NO INSERTA EN TABLAS FINALES
public function retener()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        Logger::warning('Intento de acceso a retener con método no permitido', [
            'method' => $_SERVER['REQUEST_METHOD']
        ]);
        return $this->sendJsonResponse(false, 'Método no permitido', 405);
    }

    $user = AuthHelper::getCurrentUser();
    
    $entrega_id = filter_input(INPUT_POST, 'entrega_id', FILTER_VALIDATE_INT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_FLOAT);
    $motivo = trim($_POST['motivo'] ?? '');
    $tipo = trim($_POST['tipo'] ?? 'produccion');

    Logger::info('Solicitud de retención recibida', [
        'user_id' => $user->codigo_empleado,
        'entrega_id' => $entrega_id,
        'cantidad' => $cantidad,
        'motivo' => $motivo,
        'tipo' => $tipo
    ]);

    if (!$entrega_id || $entrega_id <= 0) {
        Logger::warning('ID de entrega inválido para retención', [
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $entrega_id
        ]);
        return $this->sendJsonResponse(false, 'ID de entrega inválido', 400);
    }

    if (!$cantidad || $cantidad <= 0) {
        Logger::warning('Cantidad inválida para retención', [
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $entrega_id,
            'cantidad' => $cantidad
        ]);
        return $this->sendJsonResponse(false, 'La cantidad debe ser mayor que cero', 400);
    }

    if (empty($motivo)) {
        Logger::warning('Motivo vacío para retención', [
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $entrega_id
        ]);
        return $this->sendJsonResponse(false, 'El motivo de retención es obligatorio', 400);
    }

    $motivosPermitidos = ['calidad', 'documentacion', 'cantidad', 'otro'];
    if (!in_array($motivo, $motivosPermitidos)) {
        Logger::warning('Motivo no permitido para retención', [
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $entrega_id,
            'motivo' => $motivo
        ]);
        return $this->sendJsonResponse(false, 'Motivo de retención no válido', 400);
    }

    if (!in_array($tipo, ['produccion', 'scrap'])) {
        Logger::warning('Tipo de entrega no válido para retención', [
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $entrega_id,
            'tipo' => $tipo
        ]);
        return $this->sendJsonResponse(false, 'Tipo de entrega no válido', 400);
    }

    try {
        $registro = $this->qa->obtenerRegistroPorId($entrega_id);
        
        if (!$registro) {
            Logger::warning('Registro no encontrado para retención', [
                'user_id' => $user->codigo_empleado,
                'entrega_id' => $entrega_id
            ]);
            return $this->sendJsonResponse(false, 'No se encontró la entrega especificada', 404);
        }

        if ($registro['estado_validacion'] !== 'Validado') {
            Logger::warning('Estado incorrecto para retención', [
                'user_id' => $user->codigo_empleado,
                'entrega_id' => $entrega_id,
                'estado_actual' => $registro['estado_validacion']
            ]);
            return $this->sendJsonResponse(false, 'La entrega debe estar en estado Validado para ser retenida', 400);
        }

        // Validar cantidad según tipo
        if ($tipo === 'scrap') {
            if ($cantidad > $registro['cantidad_scrapt']) {
                Logger::warning('Cantidad excede scrap disponible para retención', [
                    'user_id' => $user->codigo_empleado,
                    'entrega_id' => $entrega_id,
                    'cantidad_solicitada' => $cantidad,
                    'cantidad_scrapt' => $registro['cantidad_scrapt']
                ]);
                return $this->sendJsonResponse(
                    false, 
                    'La cantidad a retener no puede ser mayor que la cantidad de scrap disponible (' . $registro['cantidad_scrapt'] . ' Lb)', 
                    400
                );
            }
        } else {
            if ($cantidad > $registro['cantidad_produccion']) {
                Logger::warning('Cantidad excede producción disponible para retención', [
                    'user_id' => $user->codigo_empleado,
                    'entrega_id' => $entrega_id,
                    'cantidad_solicitada' => $cantidad,
                    'cantidad_produccion' => $registro['cantidad_produccion']
                ]);
                return $this->sendJsonResponse(
                    false, 
                    'La cantidad a retener no puede ser mayor que la cantidad producida (' . $registro['cantidad_produccion'] . ' Lb)', 
                    400
                );
            }
        }

        $motivosDescripcion = [
            'calidad' => 'Problema de calidad',
            'documentacion' => 'Documentación incompleta',
            'cantidad' => 'Discrepancia en cantidad',
            'otro' => 'Otro motivo'
        ];

        $motivoDescripcion = $motivosDescripcion[$motivo] ?? $motivo;

        // ⭐ IMPORTANTE: Usar crearRetencion() que SOLO crea la retención
        // NO inserta en produccion_final ni scrap_final
        $resultado = $this->retencionModel->crearRetencion(
            $entrega_id, 
            $cantidad, 
            $motivoDescripcion, 
            $user->codigo_empleado,
            $tipo
        );

        if ($resultado['success']) {
            Logger::info('Retención creada exitosamente desde Acción QA', [
                'user_id' => $user->codigo_empleado,
                'entrega_id' => $entrega_id,
                'retencion_id' => $resultado['retencion_id'],
                'cantidad' => $cantidad,
                'motivo' => $motivoDescripcion,
                'tipo' => $tipo
            ]);

            return $this->sendJsonResponse(true, 'Entrega retenida correctamente. Proceda a gestionar la retención.', 200, [
                'retencion_id' => $resultado['retencion_id']
            ]);
        } else {
            Logger::warning('Error al crear retención desde Acción QA', [
                'user_id' => $user->codigo_empleado,
                'entrega_id' => $entrega_id,
                'error' => $resultado['message']
            ]);

            return $this->sendJsonResponse(false, $resultado['message'], 400);
        }
    } catch (\Exception $e) {
        Logger::exception($e, [
            'controller' => 'AccionController',
            'method' => 'retener',
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $entrega_id,
            'cantidad' => $cantidad,
            'motivo' => $motivo,
            'tipo' => $tipo
        ]);
        
        return $this->sendJsonResponse(false, 'Error interno al procesar la retención: ' . $e->getMessage(), 500);
    }
}

    private function sendJsonResponse($success, $message, $statusCode = 200, $data = [])
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);

        $response = [
            'success' => $success,
            'status' => $success ? 'success' : 'error',
            'message' => $message,
            'code' => $statusCode
        ] + $data;

        echo json_encode($response);
        exit();
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