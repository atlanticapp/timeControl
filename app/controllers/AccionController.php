<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Qa;
use App\Models\Accion;
use App\Helpers\Logger;
use App\Helpers\AuthHelper;

class AccionController extends Controller
{
    private $qa;
    private $accionModel;

    public function accion()
    {
        $user = AuthHelper::getCurrentUser();
        $this->qa = new Qa();
        $this->accionModel = new Accion();
        $this->view('qa/accion', [
            'data' => [
                'entregas_validadas' => $this->qa->getEntregasValidadasProduccion($user->codigo_empleado)
            ]
        ]);
    }

    public function guardarProduccion()
    {
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Logger::warning('Intento de acceso a guardarProduccion con método no permitido', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            $this->redirectWithMessage('/timeControl/public/error', 'error', 'Método no permitido');
            return;
        }

        // Obtener y validar datos
        $entrega_id = filter_input(INPUT_POST, 'entrega_id', FILTER_VALIDATE_INT);
        $comentario = trim($_POST['comentario'] ?? '');
        $user = AuthHelper::getCurrentUser();

        // Log de solicitud recibida
        Logger::info('Solicitud de guardar producción recibida', [
            'user_id' => $user->codigo_empleado,
            'entrega_id' => $entrega_id
        ]);

        // Validaciones
        if (!$entrega_id) {
            Logger::warning('Intento de guardar producción con ID inválido', [
                'user_id' => $user->codigo_empleado,
                'entrega_id' => $_POST['entrega_id'] ?? 'no-id'
            ]);
            $this->redirectWithMessage('/timeControl/public/accion', 'error', 'ID de entrega inválido');
            return;
        }

        try {
            $this->accionModel = new Accion();
            $resultado = $this->accionModel->guardarProduccion($entrega_id, $user->codigo_empleado, $comentario);

            if ($resultado['success']) {
                Logger::info('Guardado de producción exitoso', [
                    'user_id' => $user->codigo_empleado,
                    'entrega_id' => $entrega_id,
                    'validacion_id' => $resultado['validacion_id'] ?? null
                ]);

                $this->redirectWithMessage('/timeControl/public/accion', 'success', 'Entrega validada correctamente');
            } else {
                Logger::warning('Error en guardado de producción', [
                    'user_id' => $user->codigo_empleado,
                    'entrega_id' => $entrega_id,
                    'error' => $resultado['message']
                ]);

                $this->redirectWithMessage('/timeControl/public/accion', 'error', $resultado['message']);
            }
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'AccionController',
                'method' => 'guardarProduccion',
                'user_id' => $user->codigo_empleado,
                'entrega_id' => $entrega_id
            ]);
            $this->redirectWithMessage('/timeControl/public/accion', 'error', 'Error al procesar la validación: ' . $e->getMessage());
        }
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
