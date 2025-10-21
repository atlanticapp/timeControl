<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class ScrapFinal extends Model
{
    protected $table = 'scrap_final';

    /**
     * Obtener entregas de scrap guardadas (incluye retenciones)
     */
    public function getScrapGuardado($userqa, $filtros = [])
    {
        try {
            $query = "SELECT 
                        sf.id,
                        sf.registro_id,
                        sf.cantidad,
                        sf.observaciones,
                        sf.fecha_validacion,
                        sf.usuario_id,
                        r.item,
                        r.jtWo,
                        r.po,
                        r.cliente,
                        r.maquina,
                        r.codigo_empleado,
                        r.cantidad_scrapt,
                        m.nombre AS nombre_maquina,
                        u.nombre AS nombre_empleado,
                        'normal' as origen
                    FROM scrap_final sf
                    INNER JOIN registro r ON sf.registro_id = r.id
                    LEFT JOIN maquinas m ON r.maquina = m.id
                    LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                    WHERE sf.usuario_id = ?
                        AND r.estado_validacion = 'Guardado'
                    
                    UNION ALL
                    
                    SELECT 
                        rd.id,
                        rd.retencion_id as registro_id,
                        rd.cantidad,
                        rd.motivo as observaciones,
                        rd.fecha_registro as fecha_validacion,
                        rd.usuario_id,
                        r.item,
                        r.jtWo,
                        r.po,
                        r.cliente,
                        r.maquina,
                        r.codigo_empleado,
                        r.cantidad_scrapt,
                        m.nombre AS nombre_maquina,
                        u.nombre AS nombre_empleado,
                        'retencion' as origen
                    FROM retencion_destinos rd
                    INNER JOIN retenciones ret ON rd.retencion_id = ret.id
                    INNER JOIN registro r ON ret.registro_id = r.id
                    LEFT JOIN maquinas m ON r.maquina = m.id
                    LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                    WHERE rd.usuario_id = ?
                        AND rd.tipo_destino = 'destruccion'";

            $params = [$userqa, $userqa];
            $types = 'ss';

            // Aplicar filtros
            $having = [];

            if (!empty($filtros['fecha_desde'])) {
                $having[] = "fecha_validacion >= ?";
                $params[] = $filtros['fecha_desde'];
                $types .= 's';
            }

            if (!empty($filtros['fecha_hasta'])) {
                $having[] = "fecha_validacion <= ?";
                $params[] = $filtros['fecha_hasta'];
                $types .= 's';
            }

            if (!empty($filtros['maquina'])) {
                $having[] = "nombre_maquina LIKE ?";
                $params[] = '%' . $filtros['maquina'] . '%';
                $types .= 's';
            }

            if (!empty($filtros['item'])) {
                $having[] = "item LIKE ?";
                $params[] = '%' . $filtros['item'] . '%';
                $types .= 's';
            }

            if (!empty($filtros['jtwo'])) {
                $having[] = "jtWo LIKE ?";
                $params[] = '%' . $filtros['jtwo'] . '%';
                $types .= 's';
            }

            if (!empty($filtros['po'])) {
                $having[] = "po LIKE ?";
                $params[] = '%' . $filtros['po'] . '%';
                $types .= 's';
            }

            if (!empty($filtros['cliente'])) {
                $having[] = "cliente LIKE ?";
                $params[] = '%' . $filtros['cliente'] . '%';
                $types .= 's';
            }

            // Wrap query in subquery to apply HAVING filters
            if (!empty($having)) {
                $query = "SELECT * FROM (" . $query . ") as combined WHERE " . implode(' AND ', $having);
            }

            $query .= " ORDER BY fecha_validacion DESC";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param($types, ...$params);
            
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $entregas = [];

            while ($row = $result->fetch_assoc()) {
                $entregas[] = $row;
            }

            $stmt->close();

            Logger::info('Scrap guardado obtenido exitosamente (incluye retenciones)', [
                'usuario_qa' => $userqa,
                'total' => count($entregas),
                'filtros_aplicados' => array_filter($filtros)
            ]);

            return $entregas;

        } catch (Exception $e) {
            Logger::error('Error al obtener scrap guardado', [
                'usuario_qa' => $userqa,
                'filtros' => $filtros,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener una entrega de scrap específica por ID
     */
    public function getEntregaById($id, $userqa)
    {
        try {
            // Primero intentar obtener de scrap_final
            $query = "SELECT 
                        sf.id,
                        sf.registro_id,
                        sf.cantidad,
                        sf.observaciones,
                        sf.fecha_validacion,
                        sf.usuario_id,
                        r.item,
                        r.jtWo,
                        r.po,
                        r.cliente,
                        r.maquina,
                        r.codigo_empleado,
                        r.cantidad_scrapt,
                        m.nombre AS nombre_maquina,
                        u.nombre AS nombre_empleado,
                        'normal' as origen
                    FROM scrap_final sf
                    INNER JOIN registro r ON sf.registro_id = r.id
                    LEFT JOIN maquinas m ON r.maquina = m.id
                    LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                    WHERE sf.id = ? 
                        AND sf.usuario_id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param('is', $id, $userqa);
            
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $entrega = $result->fetch_assoc();
                $stmt->close();
                return $entrega;
            }

            $stmt->close();

            // Si no se encuentra, buscar en retencion_destinos
            $query = "SELECT 
                        rd.id,
                        rd.retencion_id as registro_id,
                        rd.cantidad,
                        rd.motivo as observaciones,
                        rd.fecha_registro as fecha_validacion,
                        rd.usuario_id,
                        r.item,
                        r.jtWo,
                        r.po,
                        r.cliente,
                        r.maquina,
                        r.codigo_empleado,
                        r.cantidad_scrapt,
                        m.nombre AS nombre_maquina,
                        u.nombre AS nombre_empleado,
                        'retencion' as origen,
                        rd.tipo_destino
                    FROM retencion_destinos rd
                    INNER JOIN retenciones ret ON rd.retencion_id = ret.id
                    INNER JOIN registro r ON ret.registro_id = r.id
                    LEFT JOIN maquinas m ON r.maquina = m.id
                    LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                    WHERE rd.id = ? 
                        AND rd.usuario_id = ?
                        AND rd.tipo_destino = 'destruccion'";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('is', $id, $userqa);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }

            $entrega = $result->fetch_assoc();
            $stmt->close();

            Logger::info('Entrega de scrap obtenida por ID', [
                'id' => $id,
                'usuario_qa' => $userqa,
                'origen' => $entrega['origen']
            ]);

            return $entrega;

        } catch (Exception $e) {
            Logger::error('Error al obtener entrega de scrap por ID', [
                'id' => $id,
                'usuario_qa' => $userqa,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

  
    public function getEstadisticasScrap($userqa, $fechaInicio = null, $fechaFin = null)
    {
        try {
            $whereClause = "WHERE sf.usuario_id = ?";
            $params = [$userqa];
            $types = 's';

            if ($fechaInicio) {
                $whereClause .= " AND DATE(sf.fecha_validacion) >= ?";
                $params[] = $fechaInicio;
                $types .= 's';
            }

            if ($fechaFin) {
                $whereClause .= " AND DATE(sf.fecha_validacion) <= ?";
                $params[] = $fechaFin;
                $types .= 's';
            }

            // Scrap normal
            $query = "SELECT 
                        COUNT(*) as total_entregas,
                        SUM(sf.cantidad) as cantidad_total,
                        'normal' as origen
                      FROM scrap_final sf
                      {$whereClause}";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $normal = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $whereClause2 = str_replace('sf.usuario_id', 'rd.usuario_id', $whereClause);
            $whereClause2 = str_replace('sf.fecha_validacion', 'rd.fecha_registro', $whereClause2);
            $whereClause2 .= " AND rd.tipo_destino = 'destruccion'";

            $query2 = "SELECT 
                        COUNT(*) as total_entregas,
                        SUM(rd.cantidad) as cantidad_total,
                        'retencion' as origen
                       FROM retencion_destinos rd
                       {$whereClause2}";

            $stmt = $this->db->prepare($query2);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $retencion = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return [
                'total_entregas' => (int)$normal['total_entregas'] + (int)$retencion['total_entregas'],
                'cantidad_total' => (float)($normal['cantidad_total'] ?? 0) + (float)($retencion['cantidad_total'] ?? 0),
                'normal' => [
                    'total_entregas' => (int)$normal['total_entregas'],
                    'cantidad_total' => (float)($normal['cantidad_total'] ?? 0)
                ],
                'retencion' => [
                    'total_entregas' => (int)$retencion['total_entregas'],
                    'cantidad_total' => (float)($retencion['cantidad_total'] ?? 0)
                ]
            ];

        } catch (Exception $e) {
            Logger::error('Error al obtener estadísticas de scrap', [
                'usuario_qa' => $userqa,
                'error' => $e->getMessage()
            ]);

            return [
                'total_entregas' => 0,
                'cantidad_total' => 0
            ];
        }
    }
}