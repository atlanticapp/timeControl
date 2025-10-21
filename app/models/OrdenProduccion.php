<?php

namespace App\Models;

use App\Core\Model;

class OrdenProduccion extends Model
{
    protected $table = 'ordenes_produccion';
    protected $tableDistribucion = 'ordenes_distribucion_diaria';
    protected $tableHistorial = 'ordenes_historial';

 
public function crear($data)
{
    // Extraer datos y asegurar tipos correctos
    $jobId = (string) trim($data['job_id'] ?? '');
    $item = (string) trim($data['item'] ?? '');
    $cliente = (string) trim($data['cliente'] ?? '');
    $maquinaId = (int) ($data['maquina_id'] ?? 0);
    $areaId = (int) ($data['area_id'] ?? 0);
    $descripcion = (string) trim($data['descripcion_producto'] ?? '');
    $tamano = (string) trim($data['tamano'] ?? '');
    $cantidad = (float) ($data['cantidad_requerida'] ?? 0);
    $unidad = (string) trim($data['unidad_medida'] ?? 'lb');
    $po = (string) trim($data['po'] ?? '');
    $fechaProgramada = (string) trim($data['fecha_programada'] ?? '');
    $fechaEntrega = (string) trim($data['fecha_entrega'] ?? '');
    $prioridad = (string) trim($data['prioridad'] ?? 'media');
    $notas = (string) trim($data['notas_planificador'] ?? '');
    $creadoPor = (int) ($data['creado_por'] ?? 0);

    // Validar datos críticos
    if (empty($jobId) || empty($item) || empty($cliente)) {
        error_log("Error: Datos críticos vacíos en crear orden");
        return false;
    }

    // Log para debug
    error_log("=== CREAR ORDEN ===");
    error_log("JOB ID: $jobId");
    error_log("Item: $item");
    error_log("Cliente: $cliente");
    error_log("Máquina ID: $maquinaId");
    error_log("Área ID: $areaId");

    $sql = "INSERT INTO {$this->table} 
            (job_id, item, cliente, maquina_id, area_id, descripcion_producto, 
             tamano, cantidad_requerida, unidad_medida, po, fecha_programada, 
             fecha_entrega, prioridad, notas_planificador, creado_por) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);
    
    if (!$stmt) {
        error_log("Error en prepare: " . $this->db->error);
        return false;
    }

    
    $success = $stmt->bind_param(
        "sssiiissdsssssi",  // 15 tipos (era 14 antes - faltaba una 's')
        $jobId,             // 1: s - string
        $item,              // 2: s - string
        $cliente,           // 3: s - string
        $maquinaId,         // 4: i - integer
        $areaId,            // 5: i - integer
        $descripcion,       // 6: s - string
        $tamano,            // 7: s - string
        $cantidad,          // 8: d - double
        $unidad,            // 9: s - string
        $po,                // 10: s - string
        $fechaProgramada,   // 11: s - string
        $fechaEntrega,      // 12: s - string
        $prioridad,         // 13: s - string
        $notas,             // 14: s - string
        $creadoPor          // 15: i - integer
    );

    if (!$success) {
        error_log("Error en bind_param: " . $stmt->error);
        $stmt->close();
        return false;
    }

    // Ejecutar
    $executeResult = $stmt->execute();

    if (!$executeResult) {
        error_log("Error al ejecutar insert: " . $stmt->error);
        error_log("SQL: " . $sql);
    } else {
        $insertId = $stmt->insert_id;
        error_log("✓ Orden creada exitosamente - JOB ID: $jobId - ID: $insertId");
    }

    $stmt->close();
    return $executeResult;
}

public function distribuirCantidad($ordenId, $distribucion)
{
    error_log("=== MODEL: distribuirCantidad ===");
    error_log("Orden ID: $ordenId");
    error_log("Distribución recibida: " . json_encode($distribucion));
    error_log("Total días: " . count($distribucion));
    
    // Validar parámetros
    if (!is_numeric($ordenId) || $ordenId <= 0) {
        error_log("ERROR: Orden ID inválido");
        return false;
    }
    
    if (!is_array($distribucion) || empty($distribucion)) {
        error_log("ERROR: Distribución vacía o no es array");
        return false;
    }
    
  
        // Iniciar transacción para asegurar consistencia
        $this->db->begin_transaction();
        
        // 1. Eliminar distribuciones anteriores
        $sqlDelete = "DELETE FROM {$this->tableDistribucion} WHERE orden_id = ?";
        $stmtDelete = $this->db->prepare($sqlDelete);
        
        if (!$stmtDelete) {
            error_log("ERROR en prepare DELETE: " . $this->db->error);
            $this->db->rollback();
            return false;
        }
        
        $stmtDelete->bind_param("i", $ordenId);
        
        if (!$stmtDelete->execute()) {
            error_log("ERROR al ejecutar DELETE: " . $stmtDelete->error);
            $stmtDelete->close();
            $this->db->rollback();
            return false;
        }
        
        $rowsDeleted = $stmtDelete->affected_rows;
        error_log("Registros eliminados: $rowsDeleted");
        $stmtDelete->close();
        
        // 2. Insertar nueva distribución
        $sql = "INSERT INTO {$this->tableDistribucion} 
                (orden_id, fecha, cantidad_meta, cantidad_producida, cantidad_scrap, estado) 
                VALUES (?, ?, ?, 0.00, 0.00, 'pendiente')";
        
        $insertados = 0;
        foreach ($distribucion as $fecha => $cantidad) {
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("ERROR en prepare INSERT: " . $this->db->error);
                $this->db->rollback();
                return false;
            }
            
            // Validar fecha
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                error_log("ERROR: Fecha inválida: $fecha");
                $stmt->close();
                $this->db->rollback();
                return false;
            }
            
            // Validar cantidad
            $cantidadFloat = floatval($cantidad);
            if ($cantidadFloat <= 0) {
                error_log("WARNING: Cantidad <= 0 para fecha $fecha: $cantidadFloat (omitiendo)");
                $stmt->close();
                continue;
            }
            
            $stmt->bind_param("isd", $ordenId, $fecha, $cantidadFloat);
            
            if (!$stmt->execute()) {
                error_log("ERROR al insertar distribución para fecha $fecha: " . $stmt->error);
                error_log("Valores: orden_id=$ordenId, fecha=$fecha, cantidad=$cantidadFloat");
                $stmt->close();
                $this->db->rollback();
                return false;
            }
            
            $insertados++;
            error_log("✓ Insertado: fecha=$fecha, cantidad=$cantidadFloat");
            $stmt->close();
        }
        
        error_log("Total registros insertados: $insertados");
        
        if ($insertados === 0) {
            error_log("ERROR: No se insertó ningún registro");
            $this->db->rollback();
            return false;
        }
        
        // 3. Commit de la transacción
        $this->db->commit();
        error_log("✓ Transacción completada exitosamente");
        
        return true;
        
    
}
public function obtenerReporteConProduccionDiaria($ordenId)
{
    // Obtener datos básicos de la orden
    $orden = $this->obtenerPorId($ordenId);
    
    if (!$orden) {
        error_log("ERROR: Orden $ordenId no encontrada");
        return null;
    }

    error_log("=== GENERANDO REPORTE PARA ORDEN $ordenId (JOB: {$orden['job_id']}) ===");

   
    $queryEstadisticas = "SELECT 
                            r.jtWo as job_id,
                            op.job_id as orden_job_id,
                            op.item,
                            op.cliente,
                            op.cantidad_requerida,
                            op.unidad_medida,
                            
                            COUNT(DISTINCT r.id) as total_registros,
                            COALESCE(SUM(r.cantidad_produccion), 0) as cantidad_total_producida,
                            COALESCE(SUM(r.cantidad_scrapt), 0) as cantidad_total_scrap,
                            
                            ROUND((COALESCE(SUM(r.cantidad_produccion), 0) / op.cantidad_requerida) * 100, 2) as porcentaje_completado,
                            COALESCE(op.cantidad_requerida - SUM(r.cantidad_produccion), op.cantidad_requerida) as cantidad_faltante,
                            
                            MIN(r.fecha_registro) as primer_registro,
                            MAX(r.fecha_registro) as ultimo_registro,
                            
                            GROUP_CONCAT(DISTINCT u.nombre SEPARATOR ', ') as operadores,
                            GROUP_CONCAT(DISTINCT uv.nombre SEPARATOR ', ') as validadores
                            
                        FROM registro r
                        INNER JOIN ordenes_produccion op ON r.jtWo = op.job_id
                        LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                        LEFT JOIN users uv ON r.validado_por = uv.codigo_empleado
                        WHERE r.jtWo = ?
                        AND r.estado_validacion = 'Guardado'
                        GROUP BY r.jtWo, op.id, op.item, op.cliente, op.cantidad_requerida, op.unidad_medida";
    
    $stmt = $this->db->prepare($queryEstadisticas);
    
    if (!$stmt) {
        error_log("ERROR al preparar query estadísticas: " . $this->db->error);
        return null;
    }
    
    $stmt->bind_param('s', $orden['job_id']);
    
    if (!$stmt->execute()) {
        error_log("ERROR al ejecutar query estadísticas: " . $stmt->error);
        $stmt->close();
        return null;
    }
    
    $resultEstadisticas = $stmt->get_result();
    $estadisticasGenerales = $resultEstadisticas->fetch_assoc();
    $stmt->close();

    if (!$estadisticasGenerales) {
        error_log("WARNING: No se encontraron estadísticas para el job_id: {$orden['job_id']}");
        $estadisticasGenerales = [
            'total_registros' => 0,
            'cantidad_total_producida' => 0,
            'cantidad_total_scrap' => 0,
            'porcentaje_completado' => 0,
            'cantidad_faltante' => $orden['cantidad_requerida'],
            'operadores' => 'N/A',
            'validadores' => 'N/A'
        ];
    }

    error_log("✓ Estadísticas obtenidas - Producido: {$estadisticasGenerales['cantidad_total_producida']}, Scrap: {$estadisticasGenerales['cantidad_total_scrap']}");

  
    $queryDistribucion = "SELECT 
                            dd.id,
                            dd.fecha,
                            dd.cantidad_meta,
                            dd.cantidad_producida,
                            dd.cantidad_scrap,
                            dd.estado as estado_distribucion,
                            DATE_FORMAT(dd.fecha, '%d/%m/%Y') as fecha_formatted,
                            DATE_FORMAT(dd.fecha, '%Y-%m-%d') as fecha_sql
                         FROM ordenes_distribucion_diaria dd
                         WHERE dd.orden_id = ?
                         ORDER BY dd.fecha ASC";
    
    $stmt = $this->db->prepare($queryDistribucion);
    
    if (!$stmt) {
        error_log("ERROR al preparar query distribución: " . $this->db->error);
        return null;
    }
    
    $stmt->bind_param('i', $ordenId);
    
    if (!$stmt->execute()) {
        error_log("ERROR al ejecutar query distribución: " . $stmt->error);
        $stmt->close();
        return null;
    }
    
    $result = $stmt->get_result();
    $distribucion = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    error_log("✓ Distribución obtenida: " . count($distribucion) . " días");

  
    foreach ($distribucion as &$dia) {
        $queryDiaDetalle = "SELECT 
                                COUNT(DISTINCT r.id) as registros_del_dia,
                                COALESCE(SUM(pf.cajas), 0) as total_cajas,
                                COALESCE(SUM(pf.piezas), 0) as total_piezas,
                                COALESCE(SUM(pf.paletas), 0) as total_paletas,
                                GROUP_CONCAT(DISTINCT u.nombre SEPARATOR ', ') as validadores_dia
                            FROM registro r
                            LEFT JOIN produccion_final pf ON pf.registro_id = r.id
                            LEFT JOIN users u ON r.validado_por = u.codigo_empleado
                            WHERE r.jtWo = ?
                            AND DATE(r.fecha_registro) = ?
                            AND r.estado_validacion = 'Guardado'";
        
        $stmtDia = $this->db->prepare($queryDiaDetalle);
        
        if (!$stmtDia) {
            error_log("ERROR al preparar query detalle día: " . $this->db->error);
            // Valores por defecto
            $dia['registros_del_dia'] = 0;
            $dia['total_cajas'] = 0;
            $dia['total_piezas'] = 0;
            $dia['total_paletas'] = 0;
            $dia['validadores_dia'] = 'N/A';
            $dia['cantidad_producida_dia'] = floatval($dia['cantidad_producida'] ?? 0);
            $dia['cantidad_scrap_dia'] = floatval($dia['cantidad_scrap'] ?? 0);
            $dia['porcentaje_cumplimiento'] = $dia['cantidad_meta'] > 0 
                ? round((floatval($dia['cantidad_producida']) / floatval($dia['cantidad_meta'])) * 100, 2)
                : 0;
            continue;
        }
        
        $stmtDia->bind_param('ss', $orden['job_id'], $dia['fecha_sql']);
        $stmtDia->execute();
        $resultDia = $stmtDia->get_result();
        $detalleDia = $resultDia->fetch_assoc();
        $stmtDia->close();
        
        if ($detalleDia) {
            $dia['registros_del_dia'] = intval($detalleDia['registros_del_dia']);
            $dia['total_cajas'] = intval($detalleDia['total_cajas']);
            $dia['total_piezas'] = intval($detalleDia['total_piezas']);
            $dia['total_paletas'] = intval($detalleDia['total_paletas']);
            $dia['validadores_dia'] = $detalleDia['validadores_dia'] ?? 'N/A';
        } else {
            $dia['registros_del_dia'] = 0;
            $dia['total_cajas'] = 0;
            $dia['total_piezas'] = 0;
            $dia['total_paletas'] = 0;
            $dia['validadores_dia'] = 'N/A';
        }
        
        
        $queryProduccionDia = "SELECT 
                                    COALESCE(SUM(r.cantidad_produccion), 0) as produccion_real,
                                    COALESCE(SUM(r.cantidad_scrapt), 0) as scrap_real
                                FROM registro r
                                WHERE r.jtWo = ?
                                AND DATE(r.fecha_registro) = ?
                                AND r.estado_validacion = 'Guardado'";
        
        $stmtProd = $this->db->prepare($queryProduccionDia);
        
        if ($stmtProd) {
            $stmtProd->bind_param('ss', $orden['job_id'], $dia['fecha_sql']);
            $stmtProd->execute();
            $resultProd = $stmtProd->get_result();
            $produccionDia = $resultProd->fetch_assoc();
            $stmtProd->close();
            
            $dia['cantidad_producida_dia'] = floatval($produccionDia['produccion_real'] ?? 0);
            $dia['cantidad_scrap_dia'] = floatval($produccionDia['scrap_real'] ?? 0);
        } else {
           
            $dia['cantidad_producida_dia'] = floatval($dia['cantidad_producida'] ?? 0);
            $dia['cantidad_scrap_dia'] = floatval($dia['cantidad_scrap'] ?? 0);
        }
        
       
        $dia['porcentaje_cumplimiento'] = $dia['cantidad_meta'] > 0 
            ? round(($dia['cantidad_producida_dia'] / floatval($dia['cantidad_meta'])) * 100, 2)
            : 0;
        
        
        if ($dia['cantidad_producida_dia'] > 0 || $dia['cantidad_scrap_dia'] > 0) {
            $updateDist = "UPDATE ordenes_distribucion_diaria 
                          SET cantidad_producida = ?, 
                              cantidad_scrap = ?
                          WHERE id = ?";
            $stmtUpdate = $this->db->prepare($updateDist);
            if ($stmtUpdate) {
                $stmtUpdate->bind_param('ddi', 
                    $dia['cantidad_producida_dia'], 
                    $dia['cantidad_scrap_dia'], 
                    $dia['id']
                );
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }
        }
    }

    error_log("✓ Distribución enriquecida con detalles diarios");

    // ===== OBTENER TODOS LOS REGISTROS VALIDADOS =====
    $queryRegistros = "SELECT 
                          r.id,
                          r.cantidad_produccion,
                          r.cantidad_scrapt,
                          r.fecha_registro as fecha_validacion,
                          DATE(r.fecha_registro) as fecha_registro,
                          u.nombre as validador_nombre,
                          pf.cajas,
                          pf.piezas,
                          pf.paletas,
                          pf.comentario,
                          m.nombre as maquina_nombre
                       FROM registro r
                       LEFT JOIN produccion_final pf ON pf.registro_id = r.id
                       LEFT JOIN users u ON r.validado_por = u.codigo_empleado
                       LEFT JOIN maquinas m ON r.maquina = m.id
                       WHERE r.jtWo = ? 
                       AND r.estado_validacion = 'Guardado'
                       ORDER BY r.fecha_registro DESC";
    
    $stmt = $this->db->prepare($queryRegistros);
    
    if (!$stmt) {
        error_log("ERROR al preparar query registros: " . $this->db->error);
        return null;
    }
    
    $stmt->bind_param('s', $orden['job_id']);
    
    if (!$stmt->execute()) {
        error_log("ERROR al ejecutar query registros: " . $stmt->error);
        $stmt->close();
        return null;
    }
    
    $result = $stmt->get_result();
    $registros = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    error_log("✓ Registros validados obtenidos: " . count($registros));

    // ===== CALCULAR ESTADÍSTICAS FINALES =====
    $diasConProduccion = count(array_filter($distribucion, function($d) {
        return $d['cantidad_producida_dia'] > 0;
    }));
    
    $diasCompletados = count(array_filter($distribucion, function($d) {
        return floatval($d['porcentaje_cumplimiento']) >= 100;
    }));

    error_log("✓ Estadísticas finales calculadas");

    return [
        'orden' => $orden,
        'distribucion' => $distribucion,
        'registros' => $registros,
        'estadisticas' => [
            'porcentaje_completado' => floatval($estadisticasGenerales['porcentaje_completado']),
            'cantidad_faltante' => floatval($estadisticasGenerales['cantidad_faltante']),
            'total_producido_real' => floatval($estadisticasGenerales['cantidad_total_producida']),
            'total_scrap_real' => floatval($estadisticasGenerales['cantidad_total_scrap']),
            'total_registros' => intval($estadisticasGenerales['total_registros']),
            'dias_programados' => count($distribucion),
            'dias_con_produccion' => $diasConProduccion,
            'dias_completados' => $diasCompletados,
            'operadores' => $estadisticasGenerales['operadores'],
            'validadores' => $estadisticasGenerales['validadores'],
            'primer_registro' => $estadisticasGenerales['primer_registro'] ?? null,
            'ultimo_registro' => $estadisticasGenerales['ultimo_registro'] ?? null
        ]
    ];
}
    
    public function obtenerDistribucionDiaria($ordenId)
    {
        $distribucion = [];

        $sql = "SELECT * FROM {$this->tableDistribucion} 
                WHERE orden_id = ? 
                ORDER BY fecha ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $ordenId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $distribucion = $result->fetch_all(MYSQLI_ASSOC);
        }

        $stmt->close();
        return $distribucion;
    }

    
    public function actualizarProduccionDiaria($distribucionId, $cantidadProducida, $cantidadScrap = 0)
    {
        $sql = "UPDATE {$this->tableDistribucion} 
                SET cantidad_producida = ?, cantidad_scrap = ?, estado = 'completada'
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ddi", $cantidadProducida, $cantidadScrap, $distribucionId);
        $success = $stmt->execute();

        if (!$success) {
            error_log("Error al actualizar producción diaria: " . $stmt->error);
        }

        $stmt->close();
        return $success;
    }

    public function obtenerOrdenes($filtros = [])
    {
        $ordenes = [];
        $where = [];
        $params = [];
        $types = '';

        if (!empty($filtros['estado'])) {
            $where[] = "o.estado = ?";
            $params[] = $filtros['estado'];
            $types .= 's';
        }

        if (!empty($filtros['area_id'])) {
            $where[] = "o.area_id = ?";
            $params[] = $filtros['area_id'];
            $types .= 'i';
        }

        if (!empty($filtros['maquina_id'])) {
            $where[] = "o.maquina_id = ?";
            $params[] = $filtros['maquina_id'];
            $types .= 'i';
        }

        if (!empty($filtros['fecha_desde'])) {
            $where[] = "o.fecha_programada >= ?";
            $params[] = $filtros['fecha_desde'];
            $types .= 's';
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = "o.fecha_programada <= ?";
            $params[] = $filtros['fecha_hasta'];
            $types .= 's';
        }

        if (!empty($filtros['cliente'])) {
            $where[] = "o.cliente LIKE ?";
            $params[] = '%' . $filtros['cliente'] . '%';
            $types .= 's';
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT 
                    o.*,
                    m.nombre as maquina_nombre,
                    a.nombre as area_nombre,
                    u.nombre as operador_nombre,
                    p.nombre as planificador_nombre,
                    COALESCE(SUM(dd.cantidad_producida), 0) as total_producido
                FROM {$this->table} o
                LEFT JOIN maquinas m ON o.maquina_id = m.id
                LEFT JOIN area a ON o.area_id = a.id
                LEFT JOIN users u ON o.operador_asignado = u.codigo_empleado
                LEFT JOIN users p ON o.creado_por = p.codigo_empleado
                LEFT JOIN {$this->tableDistribucion} dd ON o.id = dd.orden_id
                {$whereClause}
                GROUP BY o.id
                ORDER BY o.fecha_programada ASC, o.prioridad DESC";

        $stmt = $this->db->prepare($sql);

        if (count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $ordenes = $result->fetch_all(MYSQLI_ASSOC);
        }

        $stmt->close();
        return $ordenes;
    }

  
    public function obtenerPorId($id)
    {
        $orden = null;

        $sql = "SELECT 
                    o.*,
                    m.nombre as maquina_nombre,
                    a.nombre as area_nombre,
                    u.nombre as operador_nombre,
                    p.nombre as planificador_nombre,
                    COALESCE(SUM(dd.cantidad_producida), 0) as total_producido,
                    COALESCE(SUM(dd.cantidad_scrap), 0) as total_scrap
                FROM {$this->table} o
                LEFT JOIN maquinas m ON o.maquina_id = m.id
                LEFT JOIN area a ON o.area_id = a.id
                LEFT JOIN users u ON o.operador_asignado = u.codigo_empleado
                LEFT JOIN users p ON o.creado_por = p.codigo_empleado
                LEFT JOIN {$this->tableDistribucion} dd ON o.id = dd.orden_id
                WHERE o.id = ?
                GROUP BY o.id";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $orden = $result->fetch_assoc();
        }

        $stmt->close();
        return $orden;
    }

    /**
     * Obtener metas diarias para una máquina
     */
    public function obtenerMetasDiarias($maquinaId, $fecha = null)
    {
        if ($fecha === null) {
            $fecha = date('Y-m-d');
        }

        $metas = [];

        $sql = "SELECT 
                    dd.*,
                    o.job_id,
                    o.item,
                    o.cliente,
                    o.prioridad,
                    o.unidad_medida,
                    m.nombre as maquina_nombre,
                    a.nombre as area_nombre
                FROM {$this->tableDistribucion} dd
                INNER JOIN {$this->table} o ON dd.orden_id = o.id
                INNER JOIN maquinas m ON o.maquina_id = m.id
                INNER JOIN area a ON o.area_id = a.id
                WHERE o.maquina_id = ? 
                AND dd.fecha = ?
                AND dd.estado IN ('pendiente', 'en_proceso')
                ORDER BY o.prioridad DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $maquinaId, $fecha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $metas = $result->fetch_all(MYSQLI_ASSOC);
        }

        $stmt->close();
        return $metas;
    }

    public function obtenerMetasPorArea($areaId, $fecha = null)
    {
        if ($fecha === null) {
            $fecha = date('Y-m-d');
        }

        $metas = [];

        $sql = "SELECT 
                    dd.*,
                    o.job_id,
                    o.item,
                    o.cliente,
                    o.prioridad,
                    o.unidad_medida,
                    m.nombre as maquina_nombre,
                    a.nombre as area_nombre
                FROM {$this->tableDistribucion} dd
                INNER JOIN {$this->table} o ON dd.orden_id = o.id
                INNER JOIN maquinas m ON o.maquina_id = m.id
                INNER JOIN area a ON o.area_id = a.id
                WHERE o.area_id = ? 
                AND dd.fecha = ?
                AND dd.estado IN ('pendiente', 'en_proceso')
                ORDER BY o.prioridad DESC, m.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $areaId, $fecha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $metas = $result->fetch_all(MYSQLI_ASSOC);
        }

        $stmt->close();
        return $metas;
    }

    /**
     * Obtener orden por JOB ID
     */
    public function obtenerPorJobId($jobId)
    {
        $orden = null;

        $sql = "SELECT 
                    o.*,
                    m.nombre as maquina_nombre,
                    a.nombre as area_nombre
                FROM {$this->table} o
                LEFT JOIN maquinas m ON o.maquina_id = m.id
                LEFT JOIN area a ON o.area_id = a.id
                WHERE o.job_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $jobId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $orden = $result->fetch_assoc();
        }

        $stmt->close();
        return $orden;
    }

    public function actualizar($id, $data, $usuarioId)
    {
        $ordenActual = $this->obtenerPorId($id);
        if (!$ordenActual) {
            return false;
        }

        $campos = [];
        $params = [];
        $types = '';

        $camposPermitidos = [
            'job_id', 'item', 'cliente', 'maquina_id', 'area_id',
            'descripcion_producto', 'tamano', 'cantidad_requerida',
            'unidad_medida', 'po', 'fecha_programada', 'fecha_entrega',
            'prioridad', 'estado', 'notas_planificador', 'operador_asignado'
        ];

        foreach ($camposPermitidos as $campo) {
            if (isset($data[$campo])) {
                $campos[] = "$campo = ?";
                $params[] = $data[$campo];
                
                if (in_array($campo, ['maquina_id', 'area_id', 'operador_asignado'])) {
                    $types .= 'i';
                } elseif ($campo === 'cantidad_requerida') {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }

                if (isset($ordenActual[$campo]) && $ordenActual[$campo] != $data[$campo]) {
                    $this->registrarHistorial(
                        $id,
                        $campo,
                        $ordenActual[$campo],
                        $data[$campo],
                        $usuarioId
                    );
                }
            }
        }

        if (empty($campos)) {
            return false;
        }

        $campos[] = "actualizado_por = ?";
        $params[] = $usuarioId;
        $types .= 'i';

        $params[] = $id;
        $types .= 'i';

        $sql = "UPDATE {$this->table} 
                SET " . implode(', ', $campos) . "
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();

        if (!$success) {
            error_log("Error al actualizar orden: " . $stmt->error);
        }

        $stmt->close();
        return $success;
    }
    public function cambiarEstado($id, $nuevoEstado, $usuarioId)
    {
        $orden = $this->obtenerPorId($id);
        if (!$orden) {
            return false;
        }

        $camposAdicionales = [];

        if ($nuevoEstado === 'en_proceso' && $orden['estado'] === 'pendiente') {
            $camposAdicionales[] = "fecha_inicio_real = NOW()";
        }

        if ($nuevoEstado === 'completada') {
            $camposAdicionales[] = "fecha_fin_real = NOW()";
        }

        $sqlAdicional = count($camposAdicionales) > 0 ? ', ' . implode(', ', $camposAdicionales) : '';

        $sql = "UPDATE {$this->table} 
                SET estado = ?, actualizado_por = ? {$sqlAdicional}
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sii", $nuevoEstado, $usuarioId, $id);
        $success = $stmt->execute();

        if ($success) {
            $this->registrarHistorial($id, 'estado', $orden['estado'], $nuevoEstado, $usuarioId);
        }

        $stmt->close();
        return $success;
    }
    public function existeJobId($jobId, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE job_id = ?";
        
        if ($excluirId !== null) {
            $sql .= " AND id != ?";
        }

        $stmt = $this->db->prepare($sql);
        
        if ($excluirId !== null) {
            $stmt->bind_param("si", $jobId, $excluirId);
        } else {
            $stmt->bind_param("s", $jobId);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] > 0;
    }
    private function registrarHistorial($ordenId, $campo, $valorAnterior, $valorNuevo, $usuarioId)
    {
        $sql = "INSERT INTO {$this->tableHistorial} 
                (orden_id, campo_modificado, valor_anterior, valor_nuevo, usuario_id) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        
        $valorAnt = $valorAnterior !== null ? (string)$valorAnterior : null;
        $valorNvo = $valorNuevo !== null ? (string)$valorNuevo : null;

        $stmt->bind_param("isssi", $ordenId, $campo, $valorAnt, $valorNvo, $usuarioId);
        $stmt->execute();
        $stmt->close();
    }
    public function eliminar($id)
{
    // Iniciar transacción
    $this->db->begin_transaction();
    
   
        // 1. Eliminar distribución diaria
        $sqlDeleteDist = "DELETE FROM {$this->tableDistribucion} WHERE orden_id = ?";
        $stmtDist = $this->db->prepare($sqlDeleteDist);
        
        if (!$stmtDist) {
            error_log("ERROR al preparar DELETE distribución: " . $this->db->error);
            $this->db->rollback();
            return false;
        }
        
        $stmtDist->bind_param("i", $id);
        
        if (!$stmtDist->execute()) {
            error_log("ERROR al ejecutar DELETE distribución: " . $stmtDist->error);
            $stmtDist->close();
            $this->db->rollback();
            return false;
        }
        
        $rowsDist = $stmtDist->affected_rows;
        $stmtDist->close();
        
        error_log("Registros de distribución eliminados: $rowsDist");
        
        // 2. Eliminar historial
        $sqlDeleteHist = "DELETE FROM {$this->tableHistorial} WHERE orden_id = ?";
        $stmtHist = $this->db->prepare($sqlDeleteHist);
        
        if (!$stmtHist) {
            error_log("ERROR al preparar DELETE historial: " . $this->db->error);
            $this->db->rollback();
            return false;
        }
        
        $stmtHist->bind_param("i", $id);
        $stmtHist->execute();
        $rowsHist = $stmtHist->affected_rows;
        $stmtHist->close();
        
        error_log("Registros de historial eliminados: $rowsHist");
        
        // 3. Eliminar la orden
        $sqlDeleteOrden = "DELETE FROM {$this->table} WHERE id = ?";
        $stmtOrden = $this->db->prepare($sqlDeleteOrden);
        
        if (!$stmtOrden) {
            error_log("ERROR al preparar DELETE orden: " . $this->db->error);
            $this->db->rollback();
            return false;
        }
        
        $stmtOrden->bind_param("i", $id);
        
        if (!$stmtOrden->execute()) {
            error_log("ERROR al ejecutar DELETE orden: " . $stmtOrden->error);
            $stmtOrden->close();
            $this->db->rollback();
            return false;
        }
        
        $rowsOrden = $stmtOrden->affected_rows;
        $stmtOrden->close();
        
        error_log("Órdenes eliminadas: $rowsOrden");
        
        // Confirmar transacción
        $this->db->commit();
        error_log("✓ Orden $id eliminada exitosamente");
        
        return true;
        
    
}
public function puedeSerEliminada($id)
{
    $orden = $this->obtenerPorId($id);
    
    if (!$orden) {
        return false;
    }
    
    // Permitir eliminar si está en estado pendiente
    if ($orden['estado'] === 'pendiente') {
        return true;
    }
    
    // No permitir eliminar si está en proceso o completada
    if (in_array($orden['estado'], ['en_proceso', 'completada'])) {
        return false;
    }
    
    return true;
}  
/**
 * Crear distribución automática uniforme para una orden
 */
public function crearDistribucionAutomatica($ordenId)
{
    error_log("=== CREAR DISTRIBUCIÓN AUTOMÁTICA ===");
    error_log("Orden ID: $ordenId");
    
    $orden = $this->obtenerPorId($ordenId);
    
    if (!$orden) {
        error_log("ERROR: Orden $ordenId no encontrada");
        return false;
    }
    
    // Verificar si ya tiene distribución
    $distribucionExistente = $this->obtenerDistribucionDiaria($ordenId);
    if (!empty($distribucionExistente)) {
        error_log("INFO: Orden $ordenId ya tiene distribución");
        return true;
    }
    
    // Calcular días laborables entre fecha programada y entrega
    $fechaInicio = new \DateTime($orden['fecha_programada']);
    $fechaFin = new \DateTime($orden['fecha_entrega']);
    $diasLaborables = 0;
    $fechas = [];
    
    $fechaActual = clone $fechaInicio;
    while ($fechaActual <= $fechaFin) {
        // Incluir todos los días (puedes modificar para excluir domingos si quieres)
        $fechas[] = $fechaActual->format('Y-m-d');
        $diasLaborables++;
        $fechaActual->modify('+1 day');
    }
    
    if ($diasLaborables == 0) {
        error_log("ERROR: No hay días para distribuir");
        return false;
    }
    
    // Distribuir uniformemente
    $cantidadPorDia = floor(($orden['cantidad_requerida'] / $diasLaborables) * 100) / 100; // Redondear a 2 decimales hacia abajo
    $distribucion = [];
    $totalDistribuido = 0;
    
    foreach ($fechas as $index => $fecha) {
        if ($index == count($fechas) - 1) {
            // Último día: asignar el resto para evitar problemas de redondeo
            $distribucion[$fecha] = round($orden['cantidad_requerida'] - $totalDistribuido, 2);
        } else {
            $distribucion[$fecha] = $cantidadPorDia;
            $totalDistribuido += $cantidadPorDia;
        }
    }
    
    error_log("Distribución calculada: " . json_encode($distribucion));
    error_log("Total días: $diasLaborables");
    error_log("Cantidad por día: $cantidadPorDia");
    
    return $this->distribuirCantidad($ordenId, $distribucion);
}
}