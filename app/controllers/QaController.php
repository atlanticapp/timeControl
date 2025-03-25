<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Qa;
use App\Models\Scrap;
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
        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            throw new \Exception("Usuario no autenticado");
        }

        if ($user->tipo_usuario !== 'qa') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Tipo de usuario no es Qa.');
        }

        $stats = $this->qa->getDashboardStats($user->area_id);

        $data = [
            'title' => 'Dashboard de Control de Calidad',
            'stats' => $stats
        ];

        $this->view('qa/dashboard', [
            'data' => $data
        ]);
    }

    public function validacion()
    {
        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            throw new \Exception("Usuario no autenticado");
        }

        if ($user->tipo_usuario !== 'qa') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Tipo de usuario no es Qa.');
        }

        // Obtener las entregas pendientes (ahora retorna un array con dos sub-arrays)
        $entregas_pendientes = $this->qa->getEntregasPendientes($user->area_id);

        $data = [
            'title' => 'Validación de Entregas',
            'entregas_produccion' => $entregas_pendientes['entregas_produccion'],
            'entregas_scrap' => $entregas_pendientes['entregas_scrap'],
        ];

        $this->view('qa/validacion', [
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

        $entregaId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);

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
                : $this->qa->validarEntregaScrap($user->codigo_empleado, $entregaId);

            if ($resultado) {
                $this->redirectWithMessage('/timeControl/public/validacion', 'success', 'Entrega validada correctamente');
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
            'entregas_validadas' => $this->qa->getEntregasValidadas($user->codigo_empleado)
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


// Generar reporte de scrap
    // public function reporteScrapt($empleado_id = null, $maquina_id = null, $item = null, $jtwo = null)
    // {
    //     if (!$empleado_id || !$maquina_id || !$item || !$jtwo) {
    //         $this->redirectWithMessage('/timeControl/public/qa', 'error', 'Parámetros incorrectos para generar el reporte');
    //     }

    //     // Obtener los detalles de la entrega
    //     $detalles = $this->qa->getDetallesEntrega($empleado_id, $maquina_id, $item, $jtwo);

    //     if (empty($detalles)) {
    //         $this->redirectWithMessage('/timeControl/public/qa', 'error', 'No se encontraron detalles para generar el reporte');
    //     }

    //     // Calcular el total de scrap
    //     $total_scrapt = 0;
    //     foreach ($detalles as $detalle) {
    //         $total_scrapt += $detalle['cantidad_scrapt'];
    //     }

    //     $data = [
    //         'title' => 'Reporte de Scrapt Final',
    //         'detalles' => $detalles,
    //         'empleado_id' => $empleado_id,
    //         'maquina_id' => $maquina_id,
    //         'nombre_maquina' => $detalles[0]['nombre_maquina'],
    //         'item' => $item,
    //         'jtwo' => $jtwo,
    //         'total_scrapt' => $total_scrapt,
    //         'qa_nombre' => $_SESSION['usuario_nombre'],
    //         'qa_id' => $_SESSION['usuario_id']
    //     ];

    //     $this->view('qa/reporte_scrapt', $data);
    // }