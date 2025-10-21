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
        AuthHelper::requireAuth();

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
        
        // ✅ FILTRAR DUPLICADOS POR registro_id
        $entregas_unicas = $this->eliminarDuplicados($entregas_validadas);
        
        $this->view('qa/reporte_entrega', [
            'data' => [
                'entregas_validadas' => $entregas_unicas
            ]
        ]);
    } catch (\Exception $e) {
        Logger::error('Error en ReporteEntregaController::reporteEntrega', [
            'error' => $e->getMessage()
        ]);
        $this->redirectWithMessage('/timeControl/public/dashboard', 'error', 'Error cargando el reporte de entregas');
    }
}

private function eliminarDuplicados($entregas)
{
    $registrosVistos = [];
    $entregasUnicas = [];
    
    foreach ($entregas as $entrega) {
       
        $registroId = $entrega['registro_id'] ?? $entrega['id'];
        
        
        if (!isset($registrosVistos[$registroId])) {
            $registrosVistos[$registroId] = true;
            $entregasUnicas[] = $entrega;
        }
    }
    
    return $entregasUnicas;
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
