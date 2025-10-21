<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ScrapFinal;
use App\Helpers\AuthHelper;
use App\Helpers\Logger;

class ReporteScrapController extends Controller
{
    private $scrapModel;

    public function __construct()
    {
        $this->scrapModel = new ScrapFinal();
        AuthHelper::requireAuth();

        if (!AuthHelper::isAuthenticated()) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Debes iniciar sesión.');
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user || $user->tipo_usuario !== 'qa') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado.');
        }
    }

    /**
     * Mostrar listado de reportes de scrap con filtros
     */
    public function reporteScrap()
{
    $user = AuthHelper::getCurrentUser();
    try {
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? null,
            'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
            'maquina' => $_GET['maquina'] ?? null,
            'item' => $_GET['item'] ?? null,
            'jtwo' => $_GET['jtwo'] ?? null,
            'po' => $_GET['po'] ?? null,
            'cliente' => $_GET['cliente'] ?? null
        ];

        $entregas_validadas = $this->scrapModel->getScrapGuardado($user->codigo_empleado, $filtros);
        
        // ✅ FILTRAR DUPLICADOS
        $entregas_unicas = $this->eliminarDuplicados($entregas_validadas);
        
        $this->view('qa/reporte_scrap', [
            'data' => [
                'entregas_validadas' => $entregas_unicas
            ]
        ]);
    } catch (\Exception $e) {
        Logger::error('Error en ReporteScrapController::reporteScrap', [
            'error' => $e->getMessage()
        ]);
        $this->redirectWithMessage('/timeControl/public/dashboard', 'error', 'Error cargando el reporte de scrap');
    }
}

/**
 * Eliminar entregas duplicadas basándose en registro_id
 */
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

    /**
     * Mostrar detalle de una entrega de scrap específica
     */
    public function detalle($id)
    {
        $user = AuthHelper::getCurrentUser();

        try {
            // Obtener la entrega
            $entrega = $this->scrapModel->getEntregaById($id, $user->codigo_empleado);
            
            if (!$entrega) {
                $this->redirectWithMessage('/timeControl/public/reporte-scrap', 'error', 'Entrega no encontrada');
                return;
            }
            
            $this->view('qa/detalle_scrap', ['entrega' => $entrega]);
        } catch (\Exception $e) {
            Logger::error('Error en ReporteScrapController::detalle', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            $this->redirectWithMessage('/timeControl/public/reporte-scrap', 'error', 'Error cargando el detalle de la entrega');
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