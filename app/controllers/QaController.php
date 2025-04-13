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
        if (!$user || $user->tipo_usuario !== 'qa') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado.');
        }
    }

    public function index()
    {
        $user = AuthHelper::getCurrentUser();

        try {
            $notificaciones = (new Notificacion())->getPendingNotifications($user->area_id);

            $this->view('qa/dashboard', [
                'data' => [
                    'title' => 'Dashboard de Control de Calidad',
                    'stats' => $this->qa->getDashboardStats($user->area_id),
                    'entregasPendientes' => $this->qa->getEntregasPendientes($user->area_id),
                    'notificaciones' => $notificaciones,
                    'entregas_validadas' => $this->qa->getEntregasValidadasProduccion($user->codigo_empleado)
                ]
            ]);
        } catch (\Exception $e) {
            error_log('Error en dashboard QA: ' . $e->getMessage());
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Error al cargar el dashboard.');
        }
    }

    public function validacion()
    {
        $user = AuthHelper::getCurrentUser();

        $entregas_pendientes = $this->qa->getEntregasPendientes($user->area_id);

        $this->view('qa/validacion', [
            'data' => [
                'title' => 'Validación de Entregas',
                'entregas_produccion' => $entregas_pendientes['entregas_produccion'] ?? [],
                'entregas_scrap' => $entregas_pendientes['entregas_scrap'] ?? []
            ]
        ]);
    }

    /**
     * Procesa la validación de scrap
     * @return void
     */
    public function validarScrap()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->logAndRedirect(
                'warning',
                'Intento de acceso a validarScrap con método no permitido',
                ['method' => $_SERVER['REQUEST_METHOD']],
                'error',
                'Método no permitido'
            );
            return;
        }

        $user = AuthHelper::getCurrentUser();
        $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_FLOAT);
        $observaciones = trim($_POST['comentario'] ?? '');

        Logger::info('Solicitud de validación de scrap recibida', compact('registroId', 'cantidad') + ['user_id' => $user->codigo_empleado]);

        if (!$registroId) {
            $this->logAndRedirect(
                'warning',
                'ID de registro inválido al validar scrap',
                ['user_id' => $user->codigo_empleado, 'registro_id' => $_POST['id'] ?? 'no-id'],
                'error',
                'ID de registro inválido'
            );
            return;
        }

        if ($cantidad <= 0) {
            $this->logAndRedirect(
                'warning',
                'Cantidad inválida al validar scrap',
                compact('registroId', 'cantidad') + ['user_id' => $user->codigo_empleado],
                'error',
                'La cantidad debe ser mayor que cero'
            );
            return;
        }

        try {
            $resultado = $this->validacionModel->validarScrap($registroId, $cantidad, $observaciones, $user->codigo_empleado);

            $logLevel = $resultado['success'] ? 'info' : 'warning';
            $logMessage = $resultado['success'] ? 'Validación de scrap exitosa' : 'Error en validación de scrap';
            Logger::$logLevel($logMessage, compact('registroId', 'cantidad') + ['user_id' => $user->codigo_empleado, 'mensaje' => $resultado['message']]);

            $this->redirectWithMessage('/timeControl/public/validacion', $resultado['success'] ? 'success' : 'error', $resultado['message']);
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'QaController',
                'method' => 'validarScrap',
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId,
                'cantidad' => $cantidad
            ]);

            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Error al procesar la validación: ' . $e->getMessage());
        }
    }


    /**
     * Procesa la validación de producción
     * @return void
     */
    public function validarProduccion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Logger::warning('Intento de acceso a validarProduccion con método no permitido', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Método no permitido');
            return;
        }

        $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $user = AuthHelper::getCurrentUser();

        // Log de solicitud recibida
        Logger::info('Solicitud de validación de producción recibida', [
            'user_id' => $user->codigo_empleado,
            'registro_id' => $registroId
        ]);

        if (!$registroId) {
            Logger::warning('Intento de validar producción con ID inválido', [
                'user_id' => $user->codigo_empleado,
                'registro_id' => $_POST['id'] ?? 'no-id'
            ]);
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'ID de registro inválido');
            return;
        }

        try {
            $resultado = $this->validacionModel->validarProduccion($registroId, $user->codigo_empleado);

            if ($resultado['success']) {
                // Registrar la acción en historial si es necesario
                $this->registrarHistorialValidacion($registroId, 'produccion', $user->codigo_empleado);

                Logger::info('Validación de producción exitosa', [
                    'user_id' => $user->codigo_empleado,
                    'registro_id' => $registroId,
                    'validacion_id' => $resultado['validacion_id'] ?? null
                ]);

                $this->redirectWithMessage('/timeControl/public/validacion', 'success', $resultado['message']);
            } else {
                Logger::warning('Error en validación de producción', [
                    'user_id' => $user->codigo_empleado,
                    'registro_id' => $registroId,
                    'error' => $resultado['message']
                ]);

                $this->redirectWithMessage('/timeControl/public/validacion', 'error', $resultado['message']);
            }
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'QaController',
                'method' => 'validarProduccion',
                'user_id' => $user->codigo_empleado,
                'registro_id' => $registroId
            ]);
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Error al procesar la validación: ' . $e->getMessage());
        }
    }

    /**
     * Registra una entrada en el historial de validaciones
     * @param int $registroId ID del registro
     * @param string $tipo Tipo de validación (produccion/scrap)
     * @param string $usuarioId ID del usuario que realiza la validación
     * @return void
     */
    private function registrarHistorialValidacion($registroId, $tipo, $usuarioId)
    {
        try {
            // Si tienes una tabla de historial, aquí puedes registrar la acción
            // Este método es opcional y depende de tu implementación específica
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

    public function accion()
    {
        $user = AuthHelper::getCurrentUser();

        $this->view('qa/accion', [
            'data' => [
                'entregas_validadas' => $this->qa->getEntregasValidadasProduccion($user->codigo_empleado)
            ]
        ]);
    }

    public function historial()
    {
        $user = AuthHelper::getCurrentUser();

        $this->view('qa/historial', [
            'title' => 'Historial de Validaciones',
            'entregas_validadas' => $this->qa->getEntregasValidadasProduccion($user->codigo_empleado)
        ]);
    }

    private function logAndRedirect($logLevel, $logMessage, array $context, $type, $message)
    {
        Logger::$logLevel($logMessage, $context);
        $this->redirectWithMessage('/timeControl/public/validacion', $type, $message);
    }

    /**
     * Redirige a una URL con un mensaje de estado
     * @param string $url URL de destino
     * @param string $status Estado del mensaje (success/error/info/warning)
     * @param string $message Texto del mensaje
     * @return void
     */
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
