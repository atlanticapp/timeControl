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
        // Log de entrada
        Logger::info('Iniciando procesarCorreccion', [
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST
        ]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Logger::warning('Intento de acceso a procesarCorreccion con método no permitido', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            $this->redirectWithMessage('/timeControl/public/control', 'error', 'Método no permitido');
            return;
        }

        $solicitudId = filter_input(INPUT_POST, 'solicitud_id', FILTER_VALIDATE_INT);
        $registroId = filter_input(INPUT_POST, 'registro_id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_FLOAT);
        $comentario = trim($_POST['comentario'] ?? '');
        $user = AuthHelper::getCurrentUser();

        // Log de datos recibidos
        Logger::info('Datos recibidos en procesarCorreccion', [
            'solicitud_id' => $solicitudId,
            'registro_id' => $registroId,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'comentario' => $comentario,
            'user_id' => $user->codigo_empleado
        ]);

        // Validación de datos
        if (!$solicitudId || !$registroId || !$tipo || $cantidad === false || $cantidad < 0) {
            Logger::warning('Datos inválidos al intentar procesar corrección', [
                'solicitud_id' => $solicitudId,
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'cantidad' => $cantidad
            ]);
            $this->redirectWithMessage('/timeControl/public/control', 'error', 'Todos los campos son obligatorios y la cantidad debe ser mayor o igual a 0');
            return;
        }

        try {
            $resultado = $this->correccionesOperador->actualizarCorreccion(
                $solicitudId, 
                $registroId, 
                $tipo, 
                $cantidad, 
                $comentario
            );

            if ($resultado['success']) {
                Logger::info('Corrección procesada con éxito', [
                    'solicitud_id' => $solicitudId,
                    'registro_id' => $registroId,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'user_id' => $user->codigo_empleado
                ]);
                $this->redirectWithMessage('/timeControl/public/control', 'success', $resultado['message']);
            } else {
                Logger::warning('Error al procesar corrección', [
                    'solicitud_id' => $solicitudId,
                    'error' => $resultado['message']
                ]);
                $this->redirectWithMessage('/timeControl/public/control', 'error', $resultado['message']);
            }
        } catch (\Exception $e) {
            Logger::error('Excepción al procesar corrección', [
                'solicitud_id' => $solicitudId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->redirectWithMessage('/timeControl/public/control', 'error', 'Error al procesar la corrección: ' . $e->getMessage());
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