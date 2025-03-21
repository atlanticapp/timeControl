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

        // Obtener las entregas pendientes (ahora retorna un array con dos sub-arrays)
        $entregas_pendientes = $this->qa->getEntregasPendientes($user->area_id);

        $data = [
            'title' => 'Panel de QA - Validación de Entregas',
            'entregas_produccion' => $entregas_pendientes['entregas_produccion'],
            'entregas_scrap' => $entregas_pendientes['entregas_scrap'],
            'entregas_validadas' => $this->qa->getEntregasValidadas()
        ];

        $this->view('qa/validacionEnt', [
            'data' => $data
        ]);
    }

    // Validar entrega (aceptar)
    public function validarEnt()
    {
        try {
            // Verificar si es una solicitud POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                return;
            }

            // Obtener usuario actual
            $user = AuthHelper::getCurrentUser();

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'No se pudo obtener el usuario actual']);
                return;
            }

            // Extraer los valores de POST
            $idEntrega = isset($_POST['id']) ? (int)$_POST['id'] : null;
            $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : null;

            error_log("validarEnt - ID: $idEntrega, Tipo: $tipo");

            // Verificar que existan los parámetros necesarios
            if (!$idEntrega || !$tipo) {
                echo json_encode(['success' => false, 'message' => 'Faltan parámetros para validar la entrega']);
                return;
            }

            // Validar la entrega
            $validacionExitosa = $this->qa->validarEntrega($user->codigo_empleado, $idEntrega);

            error_log("Resultado validación: " . ($validacionExitosa ? "Éxito" : "Fracaso"));

            // Si es de tipo scrap y la validación fue exitosa, guardar en tabla scrap_final
            if ($validacionExitosa && $tipo === 'scrap') {
                try {
                    // Obtener modelo y datos del registro original
                    $registroOriginal = $this->qa->obtenerRegistroPorId($idEntrega);

                    error_log("Registro original: " . ($registroOriginal ? "Encontrado" : "No encontrado"));

                    if ($registroOriginal) {
                        // Cargar modelo de Scrap
                        $scrapModel = new Scrap();

                        // Determinar el campo jtwo según disponibilidad
                        $jtwoValue = '';
                        if (isset($registroOriginal['jtWo'])) {
                            $jtwoValue = $registroOriginal['jtWo'];
                        } elseif (isset($registroOriginal['jtwo'])) {
                            $jtwoValue = $registroOriginal['jtwo'];
                        }

                        // Preparar datos
                        $datosScrap = [
                            'codigo_empleado' => $registroOriginal['codigo_empleado'],
                            'maquina_id' => $registroOriginal['maquina'],
                            'item' => $registroOriginal['item'] ?? '',
                            'jtwo' => $jtwoValue,
                            'cantidad' => $registroOriginal['cantidad_scrapt'],
                            'aprobado_por' => $user->codigo_empleado,
                            'fecha_aprobacion' => $registroOriginal['fecha_validacion'] ?? date('Y-m-d H:i:s')
                        ];

                        error_log("Datos a guardar en scrap_final: " . json_encode($datosScrap));

                        // Guardar en la tabla scrap_final
                        $scrapGuardado = $scrapModel->guardarScrapFinal($datosScrap);

                        error_log("Resultado guardado en scrap_final: " . ($scrapGuardado ? "Éxito" : "Fracaso"));
                    }
                } catch (Exception $e) {
                    error_log("Error al guardar scrap_final: " . $e->getMessage());
                    // Continuamos el flujo aunque haya un error al guardar scrap_final
                }
            }

            if ($validacionExitosa) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Entrega de ' . ($tipo === 'produccion' ? 'producción' : 'scrap') . ' validada correctamente'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al validar la entrega']);
            }
        } catch (Exception $e) {
            error_log("Error general en validarEnt: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
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

    // Dashboard con resumen de estadísticas
    public function dashboard()
    {
        $user = AuthHelper::getCurrentUser();
        $area_id = $user->area_id;

        // Obtener estadísticas generales del dashboard
        $stats = $this->qa->getDashboardStats($area_id);

        // Obtener estadísticas por máquina
        $stats_maquinas = $this->qa->getEstadisticasPorMaquina();

        // Obtener validaciones recientes
        $validaciones_recientes = $this->qa->getEntregasValidadas();

        $data = [
            'title' => 'Dashboard de Control de Calidad',
            'stats' => $stats,
            'stats_maquinas' => $stats_maquinas,
            'validaciones_recientes' => $validaciones_recientes
        ];

        $this->view('qa/dashboard', [
            'data' => $data
        ]);
    }

    // Método para ver el historial de validaciones
    public function historial()
    {
        $data = [
            'title' => 'Historial de Validaciones',
            'entregas_validadas' => $this->qa->getEntregasValidadas()
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
