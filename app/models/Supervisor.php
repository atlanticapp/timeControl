<?php

namespace App\Models;

use App\Core\Model;

class Supervisor extends Model
{
    public function getOperacionesAbiertas($area_id, $codigo_empleado = '', $tipo_boton = '', $maquina = '')
    {
        $sql = "SELECT r.*, u.nombre, m.nombre AS nombre_maquina 
                FROM registro r 
                JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                JOIN maquinas m ON r.maquina = m.id
                WHERE r.fecha_fin IS NULL";

        $params = [];
        $param_types = '';

        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $params[] = $area_id;
            $param_types .= 'i';
        }

        if (!empty($codigo_empleado)) {
            $sql .= " AND r.codigo_empleado = ?";
            $params[] = $codigo_empleado;
            $param_types .= 's';
        }

        if (!empty($tipo_boton)) {
            $sql .= " AND r.tipo_boton = ?";
            $params[] = $tipo_boton;
            $param_types .= 's';
        }

        if (!empty($maquina)) {
            $sql .= " AND r.maquina = ?";
            $params[] = $maquina;
            $param_types .= 's';
        }

        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($param_types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $operaciones_abiertas = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $operaciones_abiertas;
        }

        throw new \Exception("Error al preparar la consulta de operaciones abiertas");
    }

    public function getEmpleadosPorArea($area_id)
    {
        $sql = "SELECT DISTINCT u.codigo_empleado, u.nombre 
                FROM users u
                WHERE 1=1";

        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $empleados = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $empleados;
    }

    public function getTiposBotones()
    {
        $sql = "SELECT DISTINCT tipo_boton FROM registro WHERE fecha_fin IS NULL";
        $result = $this->db->query($sql);
        if ($result) {
            $botones = $result->fetch_all(MYSQLI_ASSOC);
            return $botones;
        }

        throw new \Exception("Error al obtener los tipos de botones");
    }

    public function getMaquinasPorArea($area_id)
    {
        $sql = "SELECT DISTINCT m.id, m.nombre 
                FROM maquinas m
                JOIN registro r ON r.maquina = m.id 
                JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                WHERE r.fecha_fin IS NULL";

        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $maquinas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $maquinas;
    }

    public function getProduccionDiaria($area_id, $item = null, $jtWo = null)
    {
        $sql = "SELECT r.*, u.codigo_empleado, u.nombre AS nombre_empleado, 
                m.nombre AS nombre_maquina, r.fecha_registro
            FROM registro r 
            JOIN users u ON r.codigo_empleado = u.codigo_empleado 
            JOIN maquinas m ON r.maquina = m.id
            WHERE (r.tipo_boton = 'final_produccion' OR r.descripcion = 'Parcial') 
            AND DATE(r.fecha_registro) = CURDATE()";

        $params = [];
        $param_types = '';

        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $params[] = $area_id;
            $param_types .= 'i';
        }

        if (!empty($item)) {
            $sql .= " AND r.item = ?";
            $params[] = $item;
            $param_types .= 's';
        }

        if (!empty($jtWo)) {
            $sql .= " AND r.jtWo = ?";
            $params[] = $jtWo;
            $param_types .= 's';
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Error al preparar la consulta de producción diaria");
        }

        if (!empty($params)) {
            $stmt->bind_param($param_types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $totalProduccion = 0;
        $totalScrap = 0;
        $produccion_por_maquina_empleado = [];

        while ($row = $result->fetch_assoc()) {
            $maquina_id = $row['maquina'];
            $empleado_codigo = $row['codigo_empleado'];

            $produccion_por_maquina_empleado[$maquina_id]['nombre_maquina'] = $row['nombre_maquina'];
            $produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['nombre_empleado'] = $row['nombre_empleado'];
            $produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['total_produccion'] =
                ($produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['total_produccion'] ?? 0) + $row['cantidad_produccion'];
            $produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['total_scrap'] =
                ($produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['total_scrap'] ?? 0) + $row['cantidad_scrapt'];
            $produccion_por_maquina_empleado[$maquina_id]['empleados'][$empleado_codigo]['fecha_registro'] = $row['fecha_registro'];

            $totalProduccion += $row['cantidad_produccion'];
            $totalScrap += $row['cantidad_scrapt'];
        }

        $stmt->close();

        return [
            'produccion_por_maquina_empleado' => $produccion_por_maquina_empleado,
            'totalProduccion' => $totalProduccion,
            'totalScrap' => $totalScrap
        ];
    }

    public function getEntregasPendientes($area_id)
    {
        try {
            error_log("DEBUG: getEntregasPendientes llamado con area_id: {$area_id}");
            
            $entregasProduccion = $this->getEntregasProduccionPendientes($area_id);
            $entregasScrap = $this->getEntregasScrapPendientes($area_id);
            $stats = $this->getDashboardStats($area_id);

            error_log("DEBUG: Entregas producción: " . count($entregasProduccion));
            error_log("DEBUG: Entregas scrap: " . count($entregasScrap));
            error_log("DEBUG: Stats: " . print_r($stats, true));

            return [
                'entregas_produccion' => $entregasProduccion,
                'entregas_scrap' => $entregasScrap,
                'stats' => $stats
            ];
        } catch (\Exception $e) {
            error_log("Error en getEntregasPendientes: " . $e->getMessage());
            return [
                'entregas_produccion' => [],
                'entregas_scrap' => [],
                'stats' => [
                    'pendientes' => 0,
                    'produccion_pendiente' => 0,
                    'scrap_pendiente' => 0
                ]
            ];
        }
    }

    public function getEntregasProduccionPendientes($area_id)
    {
        $sql = "SELECT r.id, r.fecha_registro, r.item, r.jtWo, r.po, r.cliente, r.tipo_boton, 
                r.cantidad_produccion, m.nombre AS nombre_maquina, u.nombre as nombre_empleado,
                u.codigo_empleado
                FROM registro r
                JOIN users u ON r.codigo_empleado = u.codigo_empleado
                JOIN maquinas m ON r.maquina = m.id
                WHERE r.estado_validacion = 'Pendiente' 
                AND r.cantidad_produccion > 0";

        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Error preparando consulta de entregas de producción: " . $this->db->error);
            }
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Error preparando consulta de entregas de producción: " . $this->db->error);
            }
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $entregas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        error_log("DEBUG getEntregasProduccionPendientes: " . count($entregas) . " entregas encontradas para area_id {$area_id}");
        
        return $entregas;
    }

    public function getEntregasScrapPendientes($area_id)
    {
        $sql = "SELECT r.id, r.fecha_registro, r.item, r.jtWo, r.po, r.cliente, r.tipo_boton, 
                r.cantidad_scrapt AS cantidad_scrap, m.nombre AS nombre_maquina, u.nombre as nombre_empleado,
                u.codigo_empleado
                FROM registro r
                JOIN users u ON r.codigo_empleado = u.codigo_empleado
                JOIN maquinas m ON r.maquina = m.id
                WHERE r.estado_validacion = 'Pendiente' 
                AND r.cantidad_scrapt > 0";

        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Error preparando consulta de entregas de scrap: " . $this->db->error);
            }
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Error preparando consulta de entregas de scrap: " . $this->db->error);
            }
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $entregas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        error_log("DEBUG getEntregasScrapPendientes: " . count($entregas) . " entregas encontradas para area_id {$area_id}");
        
        return $entregas;
    }

    public function getDashboardStats($area_id)
    {
        try {
            $totalPendientes = $this->contarRegistrosPendientes($area_id);
            $produccionPendiente = $this->contarProduccionPendiente($area_id);
            $scrapPendiente = $this->contarScrapPendiente($area_id);

            $stats = [
                'pendientes' => $totalPendientes,
                'produccion_pendiente' => $produccionPendiente,
                'scrap_pendiente' => $scrapPendiente
            ];

            error_log("DEBUG getDashboardStats resultado: " . print_r($stats, true));
            
            return $stats;
            
        } catch (\Exception $e) {
            error_log("Error en getDashboardStats: " . $e->getMessage());
            return [
                'pendientes' => 0,
                'produccion_pendiente' => 0,
                'scrap_pendiente' => 0
            ];
        }
    }

    private function contarRegistrosPendientes($area_id)
    {
        $sql = "SELECT COUNT(*) as total FROM registro r 
                JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                WHERE r.estado_validacion = 'Pendiente'
                AND (r.cantidad_produccion > 0 OR r.cantidad_scrapt > 0)";
        
        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        error_log("DEBUG contarRegistrosPendientes: {$row['total']} para area_id {$area_id}");
        
        return $row['total'];
    }

    private function contarProduccionPendiente($area_id)
    {
        $sql = "SELECT COUNT(*) as total FROM registro r 
                JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                WHERE r.estado_validacion = 'Pendiente' AND r.cantidad_produccion > 0";
        
        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['total'];
    }

    private function contarScrapPendiente($area_id)
    {
        $sql = "SELECT COUNT(*) as total FROM registro r 
                JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                WHERE r.estado_validacion = 'Pendiente' AND r.cantidad_scrapt > 0";
        
        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['total'];
    }

    public function debugRegistrosPendientes($area_id)
    {
        error_log("=== DEBUG SUPERVISOR MODEL ===");
        error_log("Area ID recibida: {$area_id}");
        
        $debug_info = [];
        
        try {
            $sql1 = "SELECT COUNT(*) as total FROM registro r JOIN users u ON r.codigo_empleado = u.codigo_empleado WHERE u.area_id = ?";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->bind_param("i", $area_id);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            $total_registros = $result1->fetch_assoc()['total'];
            $stmt1->close();
            
            error_log("Total registros para area {$area_id}: {$total_registros}");
            
            $sql2 = "SELECT DISTINCT estado_validacion FROM registro r JOIN users u ON r.codigo_empleado = u.codigo_empleado WHERE u.area_id = ?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->bind_param("i", $area_id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $estados = [];
            while ($row = $result2->fetch_assoc()) {
                $estados[] = $row['estado_validacion'];
            }
            $stmt2->close();
            
            error_log("Estados de validación encontrados: " . implode(', ', $estados));
            
            $debug_info = [
                'total_registros' => $total_registros,
                'estados_validacion' => $estados,
                'registros_pendientes' => $this->contarRegistrosPendientes($area_id),
                'produccion_pendiente' => $this->contarProduccionPendiente($area_id),
                'scrap_pendiente' => $this->contarScrapPendiente($area_id)
            ];
            
        } catch (\Exception $e) {
            error_log("Error en debugRegistrosPendientes: " . $e->getMessage());
            $debug_info['error'] = $e->getMessage();
        }
        
        return $debug_info;
    }

    public function verificarEstadosRegistros($registrosIds, $area_id)
    {
        if (empty($registrosIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($registrosIds), '?'));
        $query = "SELECT r.id, r.estado_validacion 
                  FROM registro r 
                  JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                  WHERE r.id IN ($placeholders) AND u.area_id = ?";

        $stmt = $this->db->prepare($query);
        $params = array_merge($registrosIds, [$area_id]);
        $types = str_repeat('i', count($params));
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $result = $stmt->get_result();

        $estados = [];
        while ($row = $result->fetch_assoc()) {
            $estados[$row['id']] = $row['estado_validacion'];
        }

        return $estados;
    }

    public function verificarRegistroPendiente($registroId, $area_id)
    {
        $query = "SELECT r.estado_validacion 
                  FROM registro r 
                  JOIN users u ON r.codigo_empleado = u.codigo_empleado 
                  WHERE r.id = ? AND u.area_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $registroId, $area_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $registro = $result->fetch_assoc();
        return $registro['estado_validacion'] === 'Pendiente';
    }

    public function rechazarRegistro($registroId, $motivo)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE registro 
                SET estado_validacion = 'Retenido', 
                    comentario = CONCAT(COALESCE(comentario, ''), ' | RECHAZADO POR SUPERVISOR: ', ?)
                WHERE id = ?
            ");
            $stmt->bind_param('si', $motivo, $registroId);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error en rechazarRegistro: " . $e->getMessage());
            return false;
        }
    }

 
    public function validarRegistro($registroId, $supervisorId, $area_id, $comentario = null)
    {
        try {
            // Verificar si el registro está pendiente y pertenece al área
            if (!$this->verificarRegistroPendiente($registroId, $area_id)) {
                error_log("Error en validarRegistro: Registro $registroId no está pendiente o no pertenece al área $area_id");
                return false;
            }

            // Obtener detalles del registro
            $registro = $this->getRegistroDetails($registroId, $area_id);
            if (!$registro) {
                error_log("Error en validarRegistro: Registro $registroId no encontrado");
                return false;
            }

            // Iniciar transaccion
            $this->db->begin_transaction();

            // Actualizar estado de validacion
            $sql = "UPDATE registro 
                    SET estado_validacion = 'Validado', 
                        validado_por = ?,
                        comentario = CONCAT(COALESCE(comentario, ''), ' | VALIDADO POR SUPERVISOR: ', ?)
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('isi', $supervisorId, $comentario ?? 'Validado', $registroId);
            $stmt->execute();
            $stmt->close();

            // Registrar en produccion_final si hay cantidad_produccion
            if ($registro['cantidad_produccion'] > 0) {
                $sql = "INSERT INTO produccion_final (registro_id, usuario_id, tipo_validacion, comentario, fecha_validacion)
                        VALUES (?, ?, 'produccion', ?, NOW())";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('iis', $registroId, $supervisorId, $comentario);
                $stmt->execute();
                $stmt->close();
            }

            // Registrar en scrap_final si hay cantidad_scrapt
            if ($registro['cantidad_scrapt'] > 0) {
                $sql = "INSERT INTO scrap_final (registro_id, cantidad, observaciones, usuario_qa_id, fecha_validacion)
                        VALUES (?, ?, ?, ?, NOW())";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('idsi', $registroId, $registro['cantidad_scrapt'], $comentario, $supervisorId);
                $stmt->execute();
                $stmt->close();
            }

            // Crear notificacion
            $this->crearNotificacion(
                $area_id,
                'info',
                "Registro $registroId validado por supervisor $supervisorId para máquina {$registro['nombre_maquina']}."
            );

            // Confirmar transaccion
            $this->db->commit();
            error_log("DEBUG validarRegistro: Registro $registroId validado exitosamente por supervisor $supervisorId");
            return true;

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error en validarRegistro: " . $e->getMessage());
            return false;
        }
    }

  
    private function crearNotificacion($area_id, $tipo, $mensaje)
    {
        try {
            $sql = "INSERT INTO notificaciones (tipo, mensaje, area_id, estado, fecha)
                    VALUES (?, ?, ?, 'pendiente', NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ssi', $tipo, $mensaje, $area_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (\Exception $e) {
            error_log("Error en crearNotificacion: " . $e->getMessage());
            return false;
        }
    }

    public function getRegistroDetails($registroId, $area_id)
    {
        $sql = "SELECT r.*, m.nombre AS nombre_maquina, u.nombre AS nombre_empleado 
                FROM registro r
                JOIN maquinas m ON r.maquina = m.id
                JOIN users u ON r.codigo_empleado = u.codigo_empleado
                WHERE r.id = ? AND u.area_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $registroId, $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    public function getCorreccionesPendientes($area_id)
{
    try {
        $sql = "SELECT 
                    sc.id,
                    sc.registro_id,
                    sc.tipo_cantidad,
                    sc.motivo,
                    sc.estado,
                    sc.fecha_solicitud,
                    r.item,
                    r.jtWo,
                    r.cantidad_produccion,
                    r.cantidad_scrapt,
                    r.fecha_registro,
                    m.nombre AS nombre_maquina,
                    u.codigo_empleado,
                    u.nombre AS nombre_empleado
                FROM solicitudes_correccion sc
                INNER JOIN registro r ON sc.registro_id = r.id
                INNER JOIN users u ON r.codigo_empleado = u.codigo_empleado
                INNER JOIN maquinas m ON r.maquina = m.id
                WHERE sc.estado = 'Pendiente'";
        
        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $sql .= " ORDER BY sc.fecha_solicitud DESC";
        
        if ($area_id !== null) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $correcciones = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        error_log("DEBUG getCorreccionesPendientes: " . count($correcciones) . " correcciones encontradas para area_id {$area_id}");
        
        return $correcciones;
        
    } catch (\Exception $e) {
        error_log("Error en getCorreccionesPendientes: " . $e->getMessage());
        return [];
    }
}
public function getEstadisticasCorrecciones($area_id)
{
    try {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN sc.tipo_cantidad = 'produccion' THEN 1 ELSE 0 END) as produccion,
                    SUM(CASE WHEN sc.tipo_cantidad = 'scrap' THEN 1 ELSE 0 END) as scrap
                FROM solicitudes_correccion sc
                INNER JOIN registro r ON sc.registro_id = r.id
                INNER JOIN users u ON r.codigo_empleado = u.codigo_empleado
                WHERE sc.estado = 'Pendiente'";
        
        if ($area_id !== null) {
            $sql .= " AND u.area_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $area_id);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        $stmt->close();
        
        return [
            'total' => (int)($stats['total'] ?? 0),
            'produccion' => (int)($stats['produccion'] ?? 0),
            'scrap' => (int)($stats['scrap'] ?? 0)
        ];
        
    } catch (\Exception $e) {
        error_log("Error en getEstadisticasCorrecciones: " . $e->getMessage());
        return [
            'total' => 0,
            'produccion' => 0,
            'scrap' => 0
        ];
    }
}


public function cancelarSolicitudCorreccion($solicitudId)
{
    try {
        $sql = "UPDATE solicitudes_correccion 
                SET estado = 'Cancelada', 
                    fecha_resolucion = NOW() 
                WHERE id = ? AND estado = 'Pendiente'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $solicitudId);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        error_log("DEBUG cancelarSolicitudCorreccion: Solicitud {$solicitudId} cancelada. Filas afectadas: {$affected}");
        
        return $result && $affected > 0;
        
    } catch (\Exception $e) {
        error_log("Error en cancelarSolicitudCorreccion: " . $e->getMessage());
        return false;
    }
}


    public function getDb()
    {
        return $this->db;
    }
}