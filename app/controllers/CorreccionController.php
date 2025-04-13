<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Correccion;
use App\Helpers\Logger;
use App\Helpers\AuthHelper;

class CorreccionController extends Controller
{
    private $correccionModel;

    public function __construct()
    {
        $this->correccionModel = new Correccion();
    }

    public function revisar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Logger::warning('Intento de acceso a corregir con método no permitido', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Método no permitido');
            return;
        }

        $registroId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
        $nota = trim($_POST['nota'] ?? '');
        $user = AuthHelper::getCurrentUser();

        if (!$registroId || !$tipo || empty($nota)) {
            Logger::warning('Datos inválidos al intentar corregir', [
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'nota' => $nota,
                'user_id' => $user->codigo_empleado
            ]);
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Todos los campos son obligatorios');
            return;
        }

        try {
            $resultado = $this->correccionModel->solicitarCorreccion($registroId, $tipo, $nota, $user->codigo_empleado);

            if ($resultado['success']) {
                Logger::info('Corrección enviada con éxito', [
                    'registro_id' => $registroId,
                    'tipo' => $tipo,
                    'nota' => $nota,
                    'user_id' => $user->codigo_empleado
                ]);
                $this->redirectWithMessage('/timeControl/public/validacion', 'success', $resultado['message']);
            } else {
                Logger::warning('Error al enviar corrección', [
                    'registro_id' => $registroId,
                    'tipo' => $tipo,
                    'nota' => $nota,
                    'user_id' => $user->codigo_empleado,
                    'error' => $resultado['message']
                ]);
                $this->redirectWithMessage('/timeControl/public/validacion', 'error', $resultado['message']);
            }
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'QaController',
                'method' => 'corregir',
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'nota' => $nota,
                'user_id' => $user->codigo_empleado
            ]);
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Error al enviar la corrección: ' . $e->getMessage());
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
