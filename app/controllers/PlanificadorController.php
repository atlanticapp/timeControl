<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\OrdenProduccion;
use App\Models\Usuario;

class PlanificadorController extends Controller
{
    private $ordenModel;
    private $usuarioModel;

    public function __construct()
    {
        // Verificar autenticación
        if (!AuthHelper::isAuthenticated()) {
            header('Location: /timeControl/public/login');
            exit();
        }

        $user = AuthHelper::getCurrentUser();
        
        // Solo planificadores pueden acceder
        if ($user->tipo_usuario !== 'planificador') {
            header('Location: /timeControl/public/login');
            exit();
        }

        $this->ordenModel = new OrdenProduccion();
        $this->usuarioModel = new Usuario();
    }

    
    public function index()
    {
        $user = AuthHelper::getCurrentUser();
        
        $areas = $this->usuarioModel->getAllAreas();
        
        $mesActual = date('Y-m-01');
        $mesFinal = date('Y-m-t');
        
        $filtros = [
            'fecha_desde' => $mesActual,
            'fecha_hasta' => $mesFinal
        ];

        $ordenes = $this->ordenModel->obtenerOrdenes($filtros);
        $operadores = $this->obtenerOperadores();

        $data = [
            'user' => $user,
            'areas' => $areas,
            'ordenes' => $ordenes,
            'operadores' => $operadores,
            'mes_actual' => date('Y-m')
        ];

        $this->view('planificador/agenda', $data);
    }
   public function crearOrden()
{
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }

    $user = AuthHelper::getCurrentUser();

    // Validar datos requeridos
    $camposRequeridos = ['job_id', 'item', 'cliente', 'maquina_id', 'cantidad_requerida', 
                         'fecha_programada', 'fecha_entrega'];
    
    foreach ($camposRequeridos as $campo) {
        if (empty($_POST[$campo])) {
            echo json_encode([
                'success' => false,
                'message' => "El campo '$campo' es obligatorio"
            ]);
            exit();
        }
    }

    // Verificar que el JOB ID no exista
    if ($this->ordenModel->existeJobId($_POST['job_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Ya existe una orden con ese JOB ID'
        ]);
        exit();
    }

    // Obtener área de la máquina seleccionada
    $maquinaId = intval($_POST['maquina_id']);
    $areaId = $this->usuarioModel->getAreaIdByMaquina($maquinaId);

    if (!$areaId) {
        echo json_encode([
            'success' => false,
            'message' => 'La máquina seleccionada no tiene área asignada'
        ]);
        exit();
    }

    // Preparar datos
    $data = [
        'job_id' => trim($_POST['job_id']),
        'item' => trim($_POST['item']),
        'cliente' => trim($_POST['cliente']),
        'maquina_id' => $maquinaId,
        'area_id' => $areaId,
        'descripcion_producto' => trim($_POST['descripcion_producto'] ?? ''),
        'tamano' => trim($_POST['tamano'] ?? ''),
        'cantidad_requerida' => floatval($_POST['cantidad_requerida']),
        'unidad_medida' => trim($_POST['unidad_medida'] ?? 'lb'),
        'po' => trim($_POST['po'] ?? ''),
        'fecha_programada' => $_POST['fecha_programada'],
        'fecha_entrega' => $_POST['fecha_entrega'],
        'prioridad' => $_POST['prioridad'] ?? 'media',
        'notas_planificador' => trim($_POST['notas_planificador'] ?? ''),
        'creado_por' => $user->codigo_empleado
    ];

    // Asignar operador si se especificó
    if (!empty($_POST['operador_asignado'])) {
        $data['operador_asignado'] = intval($_POST['operador_asignado']);
    }

    // Crear la orden
    if ($this->ordenModel->crear($data)) {
        
        $ordenId = $this->ordenModel->obtenerPorJobId($data['job_id'])['id'];
        
     
        $distribucionCreada = $this->ordenModel->crearDistribucionAutomatica($ordenId);
        
        if ($distribucionCreada) {
            echo json_encode([
                'success' => true,
                'message' => 'Orden creada y distribuida exitosamente',
                'orden_id' => $ordenId
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Orden creada. ADVERTENCIA: No se pudo crear la distribución diaria automáticamente. Debe hacerse manualmente.',
                'orden_id' => $ordenId,
                'warning' => true
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear la orden en la base de datos'
        ]);
    }
    exit();
}

    public function actualizarOrden()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /timeControl/public/planificador');
            exit();
        }

        $user = AuthHelper::getCurrentUser();
        $ordenId = intval($_POST['orden_id'] ?? 0);

        if ($ordenId <= 0) {
            $this->redirectWithMessage(
                '/timeControl/public/planificador',
                'error',
                'Orden no válida'
            );
            return;
        }

        $data = [];
        $camposPermitidos = [
            'job_id', 'item', 'cliente', 'maquina_id', 'descripcion_producto',
            'tamano', 'cantidad_requerida', 'unidad_medida', 'po',
            'fecha_programada', 'fecha_entrega', 'prioridad', 'estado',
            'notas_planificador', 'operador_asignado'
        ];

        foreach ($camposPermitidos as $campo) {
            if (isset($_POST[$campo])) {
                $data[$campo] = $_POST[$campo];
            }
        }

        if (isset($data['maquina_id'])) {
            $maquinaId = intval($data['maquina_id']);
            $data['area_id'] = $this->usuarioModel->getAreaIdByMaquina($maquinaId);
        }

        if ($this->ordenModel->actualizar($ordenId, $data, $user->codigo_empleado)) {
            $this->redirectWithMessage(
                '/timeControl/public/planificador',
                'success',
                'Orden actualizada exitosamente'
            );
        } else {
            $this->redirectWithMessage(
                '/timeControl/public/planificador',
                'error',
                'Error al actualizar la orden'
            );
        }
    }


    public function cambiarFecha()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $user = AuthHelper::getCurrentUser();
        $ordenId = intval($_POST['orden_id'] ?? 0);
        $nuevaFecha = $_POST['nueva_fecha'] ?? '';

        if ($ordenId <= 0 || empty($nuevaFecha)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit();
        }

        $data = ['fecha_programada' => $nuevaFecha];
        
        if ($this->ordenModel->actualizar($ordenId, $data, $user->codigo_empleado)) {
            echo json_encode(['success' => true, 'message' => 'Fecha actualizada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
        exit();
    }


    public function cambiarEstado()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }

        $user = AuthHelper::getCurrentUser();
        $ordenId = intval($_POST['orden_id'] ?? 0);
        $nuevoEstado = $_POST['estado'] ?? '';

        $estadosValidos = ['pendiente', 'en_proceso', 'completada', 'cancelada', 'pausada'];

        if ($ordenId <= 0 || !in_array($nuevoEstado, $estadosValidos)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit();
        }

        if ($this->ordenModel->cambiarEstado($ordenId, $nuevoEstado, $user->codigo_empleado)) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
        exit();
    }
public function obtenerOrdenesCalendario()
{
    header('Content-Type: application/json');

    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');
    $areaId = !empty($_GET['area_id']) ? intval($_GET['area_id']) : null;
    $maquinaId = !empty($_GET['maquina_id']) ? intval($_GET['maquina_id']) : null;
    $estado = $_GET['estado'] ?? null;

    $filtros = [
        'fecha_desde' => $fechaInicio,
        'fecha_hasta' => $fechaFin
    ];

    if ($areaId) $filtros['area_id'] = $areaId;
    if ($maquinaId) $filtros['maquina_id'] = $maquinaId;
    if ($estado) $filtros['estado'] = $estado;

    $ordenes = $this->ordenModel->obtenerOrdenes($filtros);

    $eventos = [];
    foreach ($ordenes as $orden) {
        $color = $this->obtenerColorEstado($orden['estado']);
        
        // Extraer la unidad de medida de la cantidad
        $unidad = $orden['unidad_medida'] ?? 'lb';
        
        $eventos[] = [
            'id' => $orden['id'],
            'title' => $orden['job_id'] . ' - ' . $orden['cliente'],
            'start' => $orden['fecha_programada'],
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'item' => $orden['item'],
                'maquina' => $orden['maquina_nombre'],
                'area' => $orden['area_nombre'] ?? '',
                'cantidad' => $orden['cantidad_requerida'] . ' ' . $unidad,
                'estado' => $orden['estado'],
                'prioridad' => $orden['prioridad'],
                'unidad' => $unidad
            ]
        ];
    }

    error_log("Eventos generados: " . count($eventos));
    echo json_encode($eventos);
    exit();
}
    public function obtenerDetalleOrden()
    {
        header('Content-Type: application/json');

        $ordenId = intval($_GET['id'] ?? 0);

        if ($ordenId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            exit();
        }

        $orden = $this->ordenModel->obtenerPorId($ordenId);

        if ($orden) {
            echo json_encode(['success' => true, 'orden' => $orden]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
        }
        exit();
    }
    public function obtenerMaquinasPorArea()
    {
        header('Content-Type: application/json');

        $areaId = intval($_GET['area_id'] ?? 0);

        if ($areaId <= 0) {
            echo json_encode([]);
            exit();
        }

        $maquinas = $this->usuarioModel->getMaquinasByArea($areaId);
        echo json_encode($maquinas);
        exit();
    }
public function distribuirCantidad()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }

    header('Content-Type: application/json');
    
    $user = AuthHelper::getCurrentUser();
    $ordenId = intval($_POST['orden_id'] ?? 0);
    
    // Log para debug
    error_log("=== DISTRIBUCIÓN ===");
    error_log("Orden ID: " . $ordenId);
    error_log("POST completo: " . print_r($_POST, true));
    
    // Obtener distribución del POST
    $distribucion = [];
    
    foreach ($_POST as $key => $value) {
        // Buscar claves como "distribucion[2025-10-15]" o directamente el array
        if ($key === 'distribucion' && is_array($value)) {
            // Si viene como array directo
            $distribucion = $value;
            break;
        } elseif (preg_match('/^distribucion\[([0-9\-]+)\]$/', $key, $matches)) {
            // Si viene en formato distribucion[fecha]
            $fecha = $matches[1];
            $cantidad = floatval($value);
            if ($cantidad > 0) {
                $distribucion[$fecha] = $cantidad;
            }
        }
    }

    // Si no se encontró distribución en POST, intentar con php://input
    if (empty($distribucion)) {
        $jsonData = file_get_contents('php://input');
        $postData = json_decode($jsonData, true);
        
        if ($postData && isset($postData['distribucion'])) {
            $distribucion = $postData['distribucion'];
        }
    }

    // Log de distribución procesada
    error_log("Distribución procesada: " . json_encode($distribucion));
    error_log("Total elementos en distribución: " . count($distribucion));

    // Validaciones
    if ($ordenId <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Orden ID inválido',
            'debug' => ['orden_id' => $ordenId]
        ]);
        exit();
    }

    if (empty($distribucion)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Distribución vacía o formato incorrecto',
            'debug' => [
                'post_keys' => array_keys($_POST),
                'distribucion_count' => count($distribucion),
                'sample_post' => array_slice($_POST, 0, 5)
            ]
        ]);
        exit();
    }

    $orden = $this->ordenModel->obtenerPorId($ordenId);
    if (!$orden) {
        echo json_encode([
            'success' => false, 
            'message' => 'Orden no encontrada',
            'debug' => ['orden_id' => $ordenId]
        ]);
        exit();
    }

    // Verificar que la suma de cantidades distribuidas coincida con la requerida
    $totalDistribuido = 0;
    foreach ($distribucion as $cantidad) {
        $totalDistribuido += floatval($cantidad);
    }
    
    $cantidadRequerida = floatval($orden['cantidad_requerida']);
    
    // Permitir una diferencia mínima por redondeo (0.01)
    $diferencia = abs($totalDistribuido - $cantidadRequerida);
    
    error_log("Total Distribuido: $totalDistribuido");
    error_log("Cantidad Requerida: $cantidadRequerida");
    error_log("Diferencia: $diferencia");
    
    if ($diferencia > 0.01) {
        echo json_encode([
            'success' => false, 
            'message' => "Total distribuido (" . number_format($totalDistribuido, 2) . ") no coincide con cantidad requerida (" . number_format($cantidadRequerida, 2) . "). Diferencia: " . number_format($diferencia, 2),
            'debug' => [
                'total_distribuido' => $totalDistribuido,
                'cantidad_requerida' => $cantidadRequerida,
                'diferencia' => $diferencia,
                'dias' => array_keys($distribucion)
            ]
        ]);
        exit();
    }

    // Si la diferencia es muy pequeña (por redondeo), ajustar
    if ($diferencia > 0 && $diferencia <= 0.01) {
        // Ajustar la última fecha para que coincida exactamente
        $fechas = array_keys($distribucion);
        $ultimaFecha = end($fechas);
        $ajuste = $cantidadRequerida - $totalDistribuido;
        $distribucion[$ultimaFecha] = round($distribucion[$ultimaFecha] + $ajuste, 2);
        error_log("Ajuste por redondeo aplicado: +$ajuste a fecha $ultimaFecha");
        $totalDistribuido = $cantidadRequerida; // Actualizar total
    }

    // Intentar guardar la distribución
    if ($this->ordenModel->distribuirCantidad($ordenId, $distribucion)) {
        error_log("✓ Distribución guardada exitosamente");
        echo json_encode([
            'success' => true, 
            'message' => 'Distribución guardada correctamente',
            'data' => [
                'orden_id' => $ordenId,
                'total_distribuido' => $totalDistribuido,
                'dias_asignados' => count($distribucion),
                'fechas' => array_keys($distribucion)
            ]
        ]);
    } else {
        error_log("✗ Error al guardar distribución en BD");
        echo json_encode([
            'success' => false, 
            'message' => 'Error al guardar la distribución en la base de datos. Verifica los logs del servidor.'
        ]);
    }
    exit();
}

    public function obtenerDistribucion()
{
    header('Content-Type: application/json');

    $ordenId = intval($_GET['orden_id'] ?? 0);

    if ($ordenId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        exit();
    }

    $distribucion = $this->ordenModel->obtenerDistribucionDiaria($ordenId);
    
    // Log para debug
    error_log("Distribución de orden $ordenId: " . json_encode($distribucion));
    
    echo json_encode([
        'success' => true,
        'distribucion' => $distribucion
    ]);
    exit();
}
    public function obtenerMetasPorArea()
    {
        header('Content-Type: application/json');

        $user = AuthHelper::getCurrentUser();
        $areaId = intval($_GET['area_id'] ?? 0);
        $fecha = $_GET['fecha'] ?? date('Y-m-d');

        if ($areaId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Área inválida']);
            exit();
        }

        $metas = $this->ordenModel->obtenerMetasPorArea($areaId, $fecha);

        echo json_encode([
            'success' => true,
            'metas' => $metas,
            'fecha' => $fecha
        ]);
        exit();
    }
    public function actualizarProduccionDiaria()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /timeControl/public/operador');
            exit();
        }

        header('Content-Type: application/json');

        $user = AuthHelper::getCurrentUser();
        $distribucionId = intval($_POST['distribucion_id'] ?? 0);
        $cantidadProducida = floatval($_POST['cantidad_producida'] ?? 0);
        $cantidadScrap = floatval($_POST['cantidad_scrap'] ?? 0);

        if ($distribucionId <= 0 || $cantidadProducida < 0 || $cantidadScrap < 0) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit();
        }

        if ($this->ordenModel->actualizarProduccionDiaria($distribucionId, $cantidadProducida, $cantidadScrap)) {
            echo json_encode([
                'success' => true,
                'message' => 'Producción registrada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar producción'
            ]);
        }
        exit();
    }
    private function obtenerOperadores()
    {
        return $this->usuarioModel->getOperadores();
    }

    private function obtenerColorEstado($estado)
    {
        $colores = [
            'pendiente' => '#6c757d',
            'en_proceso' => '#0dcaf0',
            'completada' => '#198754',
            'cancelada' => '#dc3545',
            'pausada' => '#ffc107'
        ];

        return $colores[$estado] ?? '#6c757d';
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
    public function obtenerAreas()
{
    header('Content-Type: application/json');

    $areas = $this->usuarioModel->getAllAreas();
    
    if ($areas) {
        echo json_encode($areas);
    } else {
        echo json_encode([]);
    }
    exit();
}
public function verReporteOrden()
{
    $user = AuthHelper::getCurrentUser();
    
    // Obtener el ID de los parámetros GET
    $ordenId = intval($_GET['id'] ?? 0);
    
    if (!$ordenId || $ordenId <= 0) {
        $this->redirectWithMessage(
            '/timeControl/public/planificador',
            'error',
            'Orden inválida o no especificada'
        );
        return;
    }

    // Obtener reporte con producción diaria real
    $reporte = $this->ordenModel->obtenerReporteConProduccionDiaria($ordenId);

    if (!$reporte) {
        $this->redirectWithMessage(
            '/timeControl/public/planificador',
            'error',
            'Orden no encontrada'
        );
        return;
    }

    $data = [
        'user' => $user,
        'reporte' => $reporte
    ];

    $this->view('planificador/reporte_orden', $data);
}

public function buscarOrdenPorJobId()
{
    header('Content-Type: application/json');
    
    error_log("=== BUSCAR ORDEN POR JOB ID ===");
    error_log("Request URI: " . $_SERVER['REQUEST_URI']);
    error_log("GET params: " . print_r($_GET, true));

    $jobId = $_GET['job_id'] ?? null;

    if (!$jobId) {
        error_log("ERROR: JOB ID no proporcionado");
        echo json_encode([
            'success' => false, 
            'message' => 'JOB ID requerido'
        ]);
        exit();
    }

    error_log("Buscando JOB ID: " . $jobId);

    $orden = $this->ordenModel->obtenerPorJobId(trim($jobId));

    if ($orden) {
        // Calcular cantidad producida desde distribución diaria
        $cantidadProducida = 0;
        
        // Obtener distribución para sumar lo producido
        $distribucion = $this->ordenModel->obtenerDistribucionDiaria($orden['id']);
        if ($distribucion) {
            foreach ($distribucion as $dist) {
                $cantidadProducida += floatval($dist['cantidad_producida'] ?? 0);
            }
        }
        
        // Si no hay distribución, usar el campo directo si existe
        if ($cantidadProducida == 0 && isset($orden['total_producido'])) {
            $cantidadProducida = floatval($orden['total_producido']);
        }

        $cantidadRequerida = floatval($orden['cantidad_requerida'] ?? 1);
        $porcentaje = ($cantidadRequerida > 0) 
            ? round(($cantidadProducida / $cantidadRequerida) * 100, 2) 
            : 0;

        error_log("✓ Orden encontrada:");
        error_log("  ID: " . $orden['id']);
        error_log("  JOB ID: " . $orden['job_id']);
        error_log("  Cantidad Producida: $cantidadProducida");
        error_log("  Cantidad Requerida: $cantidadRequerida");
        error_log("  Porcentaje: $porcentaje%");

        echo json_encode([
            'success' => true,
            'orden' => [
                'id' => $orden['id'],
                'job_id' => $orden['job_id'],
                'item' => $orden['item'],
                'cliente' => $orden['cliente'],
                'estado' => $orden['estado'],
                'cantidad_requerida' => $cantidadRequerida,
                'cantidad_producida' => $cantidadProducida,
                'porcentaje' => $porcentaje,
                'url_reporte' => '/timeControl/public/planificador/reporte?id=' . $orden['id']
            ]
        ]);
    } else {
        error_log("✗ Orden no encontrada: " . $jobId);
        echo json_encode([
            'success' => false, 
            'message' => 'No se encontró ninguna orden con el JOB ID: ' . $jobId
        ]);
    }
    exit();
}
public function editarOrdenForm()
{
    $user = AuthHelper::getCurrentUser();
    $ordenId = intval($_GET['id'] ?? 0);
    
    if ($ordenId <= 0) {
        $this->redirectWithMessage(
            '/timeControl/public/planificador',
            'error',
            'Orden inválida'
        );
        return;
    }
    
    $orden = $this->ordenModel->obtenerPorId($ordenId);
    
    if (!$orden) {
        $this->redirectWithMessage(
            '/timeControl/public/planificador',
            'error',
            'Orden no encontrada'
        );
        return;
    }
    
    $areas = $this->usuarioModel->getAllAreas();
    $maquinas = $this->usuarioModel->getMaquinasByArea($orden['area_id']);
    $operadores = $this->obtenerOperadores();
    
    $data = [
        'user' => $user,
        'orden' => $orden,
        'areas' => $areas,
        'maquinas' => $maquinas,
        'operadores' => $operadores
    ];
    
    $this->view('planificador/editar_orden', $data);
}
public function guardarEdicion()
{
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }
    
    $user = AuthHelper::getCurrentUser();
    $ordenId = intval($_POST['orden_id'] ?? 0);
    
    if ($ordenId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Orden ID inválido']);
        exit();
    }
    
    $orden = $this->ordenModel->obtenerPorId($ordenId);
    
    if (!$orden) {
        echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
        exit();
    }
    
    // Si se cambió el job_id, verificar que no exista otro
    if ($_POST['job_id'] !== $orden['job_id']) {
        if ($this->ordenModel->existeJobId($_POST['job_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Ya existe otra orden con ese JOB ID'
            ]);
            exit();
        }
    }
    
    $data = [
        'job_id' => trim($_POST['job_id'] ?? $orden['job_id']),
        'item' => trim($_POST['item'] ?? $orden['item']),
        'cliente' => trim($_POST['cliente'] ?? $orden['cliente']),
        'maquina_id' => intval($_POST['maquina_id'] ?? $orden['maquina_id']),
        'area_id' => intval($_POST['area_id'] ?? $orden['area_id']),
        'descripcion_producto' => trim($_POST['descripcion_producto'] ?? $orden['descripcion_producto']),
        'tamano' => trim($_POST['tamano'] ?? $orden['tamano']),
        'cantidad_requerida' => floatval($_POST['cantidad_requerida'] ?? $orden['cantidad_requerida']),
        'unidad_medida' => trim($_POST['unidad_medida'] ?? $orden['unidad_medida']),
        'po' => trim($_POST['po'] ?? $orden['po']),
        'fecha_programada' => $_POST['fecha_programada'] ?? $orden['fecha_programada'],
        'fecha_entrega' => $_POST['fecha_entrega'] ?? $orden['fecha_entrega'],
        'prioridad' => $_POST['prioridad'] ?? $orden['prioridad'],
        'estado' => $_POST['estado'] ?? $orden['estado'],
        'notas_planificador' => trim($_POST['notas_planificador'] ?? $orden['notas_planificador'])
    ];
    
    if (!empty($_POST['operador_asignado'])) {
        $data['operador_asignado'] = intval($_POST['operador_asignado']);
    }
    
    if ($this->ordenModel->actualizar($ordenId, $data, $user->codigo_empleado)) {
        echo json_encode([
            'success' => true,
            'message' => 'Orden actualizada correctamente',
            'orden_id' => $ordenId
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar la orden'
        ]);
    }
    exit();
}

public function eliminarOrden()
{
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }
    
    $user = AuthHelper::getCurrentUser();
    $ordenId = intval($_POST['orden_id'] ?? 0);
    
    if ($ordenId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Orden ID inválido']);
        exit();
    }
    
    $orden = $this->ordenModel->obtenerPorId($ordenId);
    
    if (!$orden) {
        echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
        exit();
    }
    
    // Verificar si puede ser eliminada
    if (!$this->ordenModel->puedeSerEliminada($ordenId)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se puede eliminar una orden en estado ' . $orden['estado'] . '. Solo se pueden eliminar órdenes pendientes.'
        ]);
        exit();
    }
    
    // Registrar en historial antes de eliminar
    $descripcion = "Orden eliminada: {$orden['job_id']} - Cliente: {$orden['cliente']}";
    error_log("ELIMINACIÓN: " . $descripcion . " - Usuario: {$user->codigo_empleado}");
    
    if ($this->ordenModel->eliminar($ordenId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Orden eliminada correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar la orden. Verifica que no tenga datos asociados.'
        ]);
    }
    exit();
}

}