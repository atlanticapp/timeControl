<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProduccionFinal;
use App\Helpers\AuthHelper;
use App\Helpers\Logger;

class ReporteEntregaController extends Controller
{
    private $produccionModel;

    public function __construct()
    {
        $this->produccionModel = new ProduccionFinal();

        if (!AuthHelper::isAuthenticated()) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Debes iniciar sesión.');
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user || $user->tipo_usuario !== 'qa') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado.');
        }
    }

    public function reporteEntrega()
    {
        $user = AuthHelper::getCurrentUser();
        try {
            $entregas_validadas = $this->produccionModel->getProduccionGuardada($user->codigo_empleado);
            $this->view('qa/reporte_entrega', [
                'data' => [
                    'entregas_validadas' => $entregas_validadas
                ]
            ]);
        } catch (\Exception $e) {
            Logger::error('Error en ReporteEntregaController::reporteEntrega', [
                'error' => $e->getMessage()
            ]);
            $this->redirectWithMessage('/timeControl/public/dashboard', 'error', 'Error cargando el reporte de entregas');
        }
    }

    public function detalle($id)
    {
        $user = AuthHelper::getCurrentUser();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $entregaId = $_POST['entrega_id'] ?? null;
            if (!$entregaId) {
                $this->redirectWithMessage('/timeControl/public/reporte-entrega', 'error', 'ID de entrega no válido');
                return;
            }
            $user = AuthHelper::getCurrentUser();
            $data = [
                'pn' => $_POST['pn'] ?? '',
                'jtWo' => $_POST['jtWo'] ?? '',
                'po' => $_POST['po'] ?? '',
                'cajas' => $_POST['cajas'] ?? '',
                'piezas' => $_POST['piezas'] ?? '',
                'cantidad_produccion' => $_POST['cantidad_produccion'] ?? '',
                'paletas' => $_POST['paletas'] ?? '',
                'tipo_transferencia' => $_POST['tipo_transferencia'] ?? '',
                'cliente' => $_POST['cliente'] ?? '',
                'linea' => $_POST['linea'] ?? '',
            ];
            $this->produccionModel->actualizarEntrega($entregaId, $user->codigo_empleado, $data);
            $this->redirectWithMessage('/timeControl/public/reporte-entrega/detalle/' . $entregaId, 'success', 'Entrega actualizada correctamente');
            return;
        }

        $entrega = $this->produccionModel->getEntregaById($id, $user->codigo_empleado);
        if (!$entrega) {
            // Redirigir si no existe
            $this->redirectWithMessage('/timeControl/public/reporte-entrega', 'error', 'Entrega no encontrada');
            return;
        }
        // Si viene ?imprimir=1, podrías marcar como impresa aquí si lo deseas
        $this->view('qa/detalle_entrega', [ 'entrega' => $entrega ]);
    }

    public function guardarEntrega()
    {
        $user = AuthHelper::getCurrentUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $entregaId = $_POST['entrega_id'] ?? null;
            if (!$entregaId) {
                $this->redirectWithMessage('/timeControl/public/reporte-entrega', 'error', 'ID de entrega no válido');
                return;
            }
            $data = [
                'cajas' => $_POST['cajas'] ?? '',
                'piezas' => $_POST['piezas'] ?? '',
                'paletas' => $_POST['paletas'] ?? ''
            ];
            $this->produccionModel->actualizarEntrega($entregaId, $user->codigo_empleado, $data);
            $this->redirectWithMessage('/timeControl/public/reporte-entrega/detalle/' . $entregaId, 'success', 'Entrega actualizada correctamente');
            return;
        }
        $this->redirectWithMessage('/timeControl/public/reporte-entrega', 'error', 'Método no permitido');
    }

    // public function marcarImpresa()
    // {
    //     header('Content-Type: application/json');
    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    //         return;
    //     }
    //     $id = $_GET['id'] ?? null;
    //     if (!$id) {
    //         echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
    //         return;
    //     }
    //     try {
    //         $resultado = $this->produccionModel->marcarComoImpresa($id);
    //         if ($resultado) {
    //             echo json_encode(['status' => 'success', 'message' => 'Entrega marcada como impresa']);
    //         } else {
    //             echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el estado']);
    //         }
    //     } catch (\Exception $e) {
    //         \App\Helpers\Logger::error('Error al marcar como impresa', ['error' => $e->getMessage()]);
    //         echo json_encode(['status' => 'error', 'message' => 'Error interno']);
    //     }
    // }

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
