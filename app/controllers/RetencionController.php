<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Logger;
use App\Helpers\AuthHelper;
use App\Models\Retencion;

class RetencionController extends Controller
{
    private $retencionModel;

    public function __construct()
    {
        $this->retencionModel = new Retencion();
         
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
            $this->view('qa/retenciones', [
                'data' => [
                    'retenciones' => $this->retencionModel->getRetencionesActivas($user->area_id, $user->codigo_empleado)
                ]
            ]);
        } catch (\Exception $e) {
            Logger::error('Error al cargar vista de retenciones', [
                'error' => $e->getMessage()
            ]);
            $this->redirectWithMessage('/timeControl/public/dashboard', 'error', 'Error al cargar retenciones.');
        }
    }

    public function crearRetencion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Logger::warning('Intento de acceso a crearRetencion con método no permitido', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            $this->redirectWithMessage('/timeControl/public/retenciones', 'error', 'Método no permitido');
            return;
        }

        $user = AuthHelper::getCurrentUser();
        $registroId = filter_input(INPUT_POST, 'registro_id', FILTER_VALIDATE_INT);
        $motivo = trim($_POST['motivo'] ?? '');
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_FLOAT);

        Logger::info('Solicitud de creación de retención recibida', [
            'registro_id' => $registroId,
            'cantidad' => $cantidad,
            'user_id' => $user->codigo_empleado
        ]);

        if (!$registroId || $cantidad <= 0 || empty($motivo)) {
            Logger::warning('Datos inválidos al crear retención', [
                'registro_id' => $registroId,
                'cantidad' => $cantidad,
                'motivo' => $motivo
            ]);
            $this->redirectWithMessage('/timeControl/public/retenciones', 'error', 'Datos incompletos o inválidos');
            return;
        }

        try {
            $resultado = $this->retencionModel->crearRetencion(
                $registroId,
                $cantidad,
                $motivo,
                $user->codigo_empleado
            );

            if ($resultado['success']) {
                Logger::info('Retención creada exitosamente', [
                    'retencion_id' => $resultado['retencion_id'],
                    'registro_id' => $registroId,
                    'user_id' => $user->codigo_empleado
                ]);
            } else {
                Logger::warning('Error al crear retención', [
                    'registro_id' => $registroId,
                    'error' => $resultado['message']
                ]);
            }

            $this->redirectWithMessage(
                '/timeControl/public/retenciones',
                $resultado['success'] ? 'success' : 'error',
                $resultado['message']
            );
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'RetencionController',
                'method' => 'crearRetencion',
                'registro_id' => $registroId,
                'user_id' => $user->codigo_empleado
            ]);
            $this->redirectWithMessage(
                '/timeControl/public/retenciones',
                'error',
                'Error al procesar la retención: ' . $e->getMessage()
            );
        }
    }

    public function asignarDestino()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Logger::warning('Intento de acceso a asignarDestinos con método no permitido', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            $this->redirectWithMessage('/timeControl/public/retenciones', 'error', 'Método no permitido');
            return;
        }

        $user = AuthHelper::getCurrentUser();
        $retencionId = filter_input(INPUT_POST, 'retencion_id', FILTER_VALIDATE_INT);

        $destinos = $_POST['destinos'] ?? [];
        $cantidades = $_POST['cantidades'] ?? [];
        $motivos = $_POST['motivos'] ?? [];

        if (empty($destinos) || count($destinos) !== count($cantidades) || count($destinos) !== count($motivos)) {
            Logger::warning('Datos incompletos o mal estructurados en asignarDestinos', [
                'retencion_id' => $retencionId,
                'destinos' => $destinos,
                'cantidades' => $cantidades,
                'motivos' => $motivos
            ]);
            $this->redirectWithMessage('/timeControl/public/retenciones', 'error', 'Los datos de destinos no son válidos');
            return;
        }

        try {
            foreach ($destinos as $index => $tipoDestino) {
                $cantidad = floatval($cantidades[$index]);
                $motivo = $motivos[$index];

                if (!is_numeric($cantidad) || $cantidad <= 0) {
                    throw new \Exception('Las cantidades deben ser valores numéricos positivos');
                }

                if (empty(trim($tipoDestino))) {
                    throw new \Exception('Todos los destinos deben ser especificados');
                }

                $resultado = $this->retencionModel->asignarDestinoRetencion(
                    $retencionId,
                    $tipoDestino,
                    $cantidad,
                    $motivo,
                    $user->codigo_empleado
                );

                Logger::info($resultado['success'] ? 'Destino asignado exitosamente' : 'Error al asignar destino', [
                    'retencion_id' => $retencionId,
                    'tipo_destino' => $tipoDestino,
                    'cantidad' => $cantidad,
                    'user_id' => $user->codigo_empleado,
                    'resultado' => $resultado['message']
                ]);

                if (!$resultado['success']) {
                    $this->redirectWithMessage('/timeControl/public/retenciones', 'error', 'Error al asignar destino: ' . $resultado['message']);
                    return;
                }
            }

            $this->redirectWithMessage('/timeControl/public/retenciones', 'success', 'Todos los destinos fueron asignados correctamente.');
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'RetencionController',
                'method' => 'asignarDestinos',
                'retencion_id' => $retencionId,
                'user_id' => $user->codigo_empleado
            ]);

            $this->redirectWithMessage('/timeControl/public/retenciones', 'error', 'Error al asignar destinos: ' . $e->getMessage());
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
