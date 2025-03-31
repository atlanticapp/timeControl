<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Notificacion;
use App\Models\Qa;
use Exception;

class QaController extends Controller
{
    private $qa;

    public function __construct()
    {
        $this->qa = new Qa();

        if (!AuthHelper::isAuthenticated()) {
            header('Location: /timeControl/public/login');
            exit();
        }
        $user = AuthHelper::getCurrentUser();
        if (!$user || $user->tipo_usuario !== 'qa') {
            header('Location: /timeControl/public/login');
            exit();
        }
    }

    // Mostrar panel principal de QA
    public function index()
    {
        // Obtener el usuario actual
        $user = AuthHelper::getCurrentUser();

        if (!$user) {
            throw new \Exception("Usuario no autenticado");
        }

        if ($user->tipo_usuario !== 'qa') {
            return $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado: Solo QA permitido.');
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $entregasPendientes = $this->qa->getEntregasPendientes($user->area_id);

            // Obtener notificaciones pendientes
            $notificationModel = new Notificacion();
            $notificaciones = $notificationModel->getPendingNotifications($user->area_id);

            $validacionesRecientes = $this->qa->getValidacionesRecientes($user->area_id);

            $this->view('qa/dashboard', [
                'data' => [
                    'title' => 'Dashboard de Control de Calidad',
                    'stats' => $this->qa->getDashboardStats($user->area_id),
                    'entregasPendientes' => $entregasPendientes,
                    'notificaciones' => $notificaciones,
                    'entregas_validadas' => $this->qa->getEntregasValidadasProduccion($user->codigo_empleado)
                ]
            ]);
        } catch (\Exception $e) {
            error_log('Error en el dashboard de QA: ' . $e->getMessage());
            return $this->redirectWithMessage('/timeControl/public/login', 'error', 'Ocurrió un error al cargar el dashboard. Intente nuevamente.');
        }
    }


    public function validacion()
    {
        // Obtener el usuario actual
        $user = AuthHelper::getCurrentUser();

        // Verificar si el usuario está autenticado
        if (!$user) {
            throw new \Exception("Usuario no autenticado");
        }

        // Verificar que el tipo de usuario sea 'qa'
        if ($user->tipo_usuario !== 'qa') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Tipo de usuario no es QA.');
        }

        // Obtener las entregas pendientes. Ahora la función retorna un array con dos sub-arrays: 'entregas_produccion' y 'entregas_scrap'
        $entregas_pendientes = $this->qa->getEntregasPendientes($user->area_id);

        // Preparar los datos para la vista
        $data = [
            'title' => 'Validación de Entregas',
            'entregas_produccion' => $entregas_pendientes['entregas_produccion'],
            'entregas_scrap' => $entregas_pendientes['entregas_scrap'],
        ];

        $this->view('qa/validacion', [
            'data' => $data
        ]);
    }

    public function accion()
    {
        // Obtener el usuario actual
        $user = AuthHelper::getCurrentUser();

        // Verificar si el usuario está autenticado
        if (!$user) {
            throw new \Exception("Usuario no autenticado");
        }

        // Verificar que el tipo de usuario sea 'qa'
        if ($user->tipo_usuario !== 'qa') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Tipo de usuario no es QA.');
        }

        $data = [
            'entregas_validadas' => $this->qa->getValidacionesRecientes($user->codigo_empleado)
        ];

        $this->view('qa/accion', [
            'data' => $data
        ]);
    }

    public function validar()
    {
        // Verificar si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Método no permitido.');
            return;
        }

        $user = AuthHelper::getCurrentUser();

        if (!$user) {
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Usuario actual irreconocible');
            return;
        }

        $entregaId = filter_input(INPUT_POST, 'id');
        $tipo = filter_input(INPUT_POST, 'tipo');
        $comentario = filter_input(INPUT_POST, 'comentario');

        // Verificar que existan los parámetros necesarios
        if (!$entregaId) {
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'ID de entrega inválido');
            return;
        }

        if (!in_array($tipo, ['produccion', 'scrap'])) {
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Tipo de entrega inválido');
            return;
        }

        try {
            // Validar la entrega según el tipo
            $resultado = $tipo === 'produccion'
                ? $this->qa->validarEntregaProduccion($user->codigo_empleado, $entregaId)
                : $this->qa->validarEntregaScrap($user->codigo_empleado, $entregaId, $comentario);

            if ($resultado) {
                $this->redirectWithMessage('/timeControl/public/validacion', 'success', 'Entrega validada correctamente');
                $_SESSION['notificacion_pendientes_mostrada'] = false;
            } else {
                $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'No fue posible validar la entrega');
            }
        } catch (Exception $e) {
            error_log("Error en validación de entrega: " . $e->getMessage());
            $this->redirectWithMessage('/timeControl/public/validacion', 'error', 'Error al procesar la solicitud');
        }
    }

    // Enviar corrección al operador
    public function corregir()
    {
        // Verificar si es una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage('/timeControl/public/qa', 'error', 'Método no permitido');
        }

        $codigo_empleado = isset($_POST['empleado_id']) ? $_POST['empleado_id'] : null;
        $maquina_id = isset($_POST['maquina_id']) ? $_POST['maquina_id'] : null;
        $item = isset($_POST['item']) ? $_POST['item'] : null;
        $jtwo = isset($_POST['jtwo']) ? $_POST['jtwo'] : null;
        $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';

        if (!$codigo_empleado || !$maquina_id || !$item || !$jtwo || empty($comentario)) {
            $this->redirectWithMessage('/timeControl/public/qa', 'error', 'Faltan parámetros para enviar la corrección');
        }

        if ($this->qa->enviarCorreccion($codigo_empleado, $maquina_id, $item, $jtwo, $comentario)) {
            $this->redirectWithMessage('/timeControl/public/qa', 'success', 'Solicitud de corrección enviada al operador');
        } else {
            $this->redirectWithMessage('/timeControl/public/qa', 'error', 'Error al enviar la solicitud de corrección');
        }
    }

    // Método para ver el historial de validaciones
    public function historial()
    {
        $user = AuthHelper::getCurrentUser();
        $data = [
            'title' => 'Historial de Validaciones',
            'entregas_validadas' => $this->qa->getEntregasValidadasProduccion($user->codigo_empleado)
        ];

        $this->view('qa/historial', $data);
    }

    private function redirectWithMessage($url, $status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: $url");
        exit();
    }
}
