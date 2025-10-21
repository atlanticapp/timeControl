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
            $resultado = $this->correccionModel->obtenerCorreccionesPendientes($user->codigo_empleado);
            $estadisticas = $this->correccionModel->obtenerEstadisticasPorTipo();

            if (!$resultado['success'] || !$estadisticas['success']) {
                throw new \Exception($resultado['success'] ? $estadisticas['message'] : $resultado['message']);
            }

            $this->view('qa/revisiones', [
                'correcciones' => $resultado['data'],
                'estadisticas' => $estadisticas['data'],
                'titulo' => 'Correcciones Pendientes'
            ]);
        } catch (\Exception $e) {
            Logger::error('Error al cargar correcciones', ['error' => $e->getMessage()]);
            $this->redirectWithMessage('/timeControl/public/dashboard', 'error', 'Error al cargar la página de correcciones: ' . $e->getMessage());
        }
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

        if (!$registroId || !$tipo) {
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

    public function cancelar()
    {
        try {
            // Verificar que sea una peticion POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Método no permitido');
            }

            // Obtener y decodificar los datos JSON recibidos
            $datos = json_decode(file_get_contents('php://input'), true);
            $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id) {
                throw new \Exception('ID de corrección no válido');
            }

            // Obtener el usuario actual
            $user = AuthHelper::getCurrentUser();

            // Llamar al modelo para cancelar la correccion
            $resultado = $this->correccionModel->cancelarCorreccion($id);

            // Registrar la acción en el log
            Logger::info('Corrección cancelada', [
                'id' => $id,
                'usuario' => $user->codigo_empleado,
                'resultado' => $resultado
            ]);

            if ($resultado['success']) {
                $this->redirectWithMessage(
                    '/timeControl/public/revisiones',
                    'success',
                    'Corrección cancelada exitosamente'
                );
            } else {
                $this->redirectWithMessage(
                    '/timeControl/public/revisiones',
                    'error',
                    'Error al cancelar la corrección: ' . $resultado['message']
                );
            }
        } catch (\Exception $e) {
            Logger::error('Error al cancelar corrección', [
                'error' => $e->getMessage(),
                'id' => $id ?? null
            ]);

            $this->redirectWithMessage(
                '/timeControl/public/revisiones',
                'error',
                'Error al cancelar la corrección: ' . $e->getMessage()
            );
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
