<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CorreccionesOperador;
use App\Helpers\AuthHelper;
use App\Helpers\Logger;

class CorreccionesOperadorController extends Controller
{
    private $correccionesOperador;

    public function __construct()
    {
        $this->correccionesOperador = new CorreccionesOperador();

        if (!AuthHelper::isAuthenticated()) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Debes iniciar sesión.');
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user || $user->tipo_usuario !== 'operador') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado.');
        }
    }

    /**
     * Procesa la corrección realizada por el operador
     */
    public function procesarCorreccion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Logger::warning('Intento de acceso a procesarCorreccion con método no permitido', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            $this->redirectWithMessage('/timeControl/public/corregir', 'error', 'Método no permitido');
            return;
        }

        $solicitudId = filter_input(INPUT_POST, 'solicitud_id', FILTER_VALIDATE_INT);
        $registroId = filter_input(INPUT_POST, 'registro_id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo'); // 'produccion' o 'scrap'
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_FLOAT);
        $comentario = trim($_POST['comentario'] ?? '');
        $user = AuthHelper::getCurrentUser();

        if (!$solicitudId || !$registroId || !$tipo || $cantidad <= 0) {
            Logger::warning('Datos inválidos al intentar procesar corrección', [
                'solicitud_id' => $solicitudId,
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'comentario' => $comentario,
                'user_id' => $user->codigo_empleado
            ]);
            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Todos los campos son obligatorios');
            return;
        }

        try {
            $resultado = $this->correccionesOperador->actualizarCorreccion($solicitudId, $registroId, $tipo, $cantidad, $comentario);

            if ($resultado['success']) {
                Logger::info('Corrección procesada con éxito', [
                    'solicitud_id' => $solicitudId,
                    'registro_id' => $registroId,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'comentario' => $comentario,
                    'user_id' => $user->codigo_empleado
                ]);
                $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'success', $resultado['message']);
            } else {
                Logger::warning('Error al procesar corrección', [
                    'solicitud_id' => $solicitudId,
                    'registro_id' => $registroId,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'comentario' => $comentario,
                    'user_id' => $user->codigo_empleado,
                    'error' => $resultado['message']
                ]);
                $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', $resultado['message']);
            }
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'CorreccionesOperadorController',
                'method' => 'procesarCorreccion',
                'solicitud_id' => $solicitudId,
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'comentario' => $comentario,
                'user_id' => $user->codigo_empleado
            ]);
            $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'error', 'Error al procesar la corrección: ' . $e->getMessage());
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
