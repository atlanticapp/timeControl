<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class ProduccionFinal extends Model
{
    public function getProduccionGuardada($userId)
    {
        try {
            $resultados = [];
            $hoy = date('Y-m-d');

            // Consulta 1: Producción Final Guardada del día
            $queryProduccionFinal = "
                SELECT 
                    'produccion_final' AS tipo,
                    pf.id, pf.cajas, pf.piezas, pf.paletas, pf.fecha_validacion, pf.comentario, pf.estado,
                    r.item, r.jtWo, r.po, r.cliente, r.cantidad_produccion,
                    u.nombre AS validador_nombre,
                    m.nombre AS nombre_maquina
                FROM produccion_final pf
                INNER JOIN registro r ON pf.registro_id = r.id
                INNER JOIN users u ON pf.usuario_id = u.codigo_empleado
                LEFT JOIN maquinas m ON r.maquina = m.id
                WHERE r.estado_validacion = 'Guardado' 
                AND pf.usuario_id = ?
                AND DATE(pf.fecha_validacion) = CURDATE()
                ORDER BY pf.fecha_validacion DESC
            ";

            $stmt = $this->db->prepare($queryProduccionFinal);
            if (!$stmt) throw new Exception("Error preparando consulta Producción Final");

            $stmt->bind_param('s', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $resultados = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            // Consulta 2: Retenciones con destino a producción_final del día
            $queryDestinos = "
                SELECT 
                    'destino_produccion' AS tipo,
                    rd.id, rd.cantidad AS cantidad_produccion, rd.fecha_registro AS fecha_validacion, rd.estado,
                    rd.motivo AS comentario,
                    reg.item, reg.jtWo, reg.po, reg.cliente,
                    u.nombre AS validador_nombre,
                    m.nombre AS nombre_maquina
                FROM retencion_destinos rd
                INNER JOIN retenciones r ON rd.retencion_id = r.id
                INNER JOIN registro reg ON r.registro_id = reg.id
                LEFT JOIN users u ON rd.usuario_id = u.codigo_empleado
                LEFT JOIN maquinas m ON reg.maquina = m.id
                WHERE rd.tipo_destino = 'produccion_final'
                AND rd.usuario_id = ?
                AND DATE(rd.fecha_registro) = CURDATE()
                ORDER BY rd.fecha_registro DESC
            ";

            $stmt2 = $this->db->prepare($queryDestinos);
            if (!$stmt2) throw new Exception("Error preparando consulta Destino Producción");

            $stmt2->bind_param('s', $userId);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $destinos = $result2->fetch_all(MYSQLI_ASSOC);
            $stmt2->close();

            return array_merge($resultados, $destinos);
        } catch (Exception $e) {
            Logger::error('Error obteniendo producción guardada y destinos producción', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return [];
        }
    }

    public function getEntregaById($id, $userId)
    {
        try {
            // Buscar primero en produccion_final
            $query = "
                SELECT pf.id, pf.cajas, pf.piezas, pf.paletas, pf.fecha_validacion, pf.comentario, pf.estado,
                       r.item, r.jtWo, r.po, r.cliente, r.cantidad_produccion,
                       u.nombre AS validador_nombre,
                       m.nombre AS nombre_maquina,
                       r.id AS registro_id,
                       'produccion_final' AS tipo
                FROM produccion_final pf
                INNER JOIN registro r ON pf.registro_id = r.id
                INNER JOIN users u ON pf.usuario_id = u.codigo_empleado
                LEFT JOIN maquinas m ON r.maquina = m.id
                WHERE pf.id = ? AND pf.usuario_id = ?
                LIMIT 1
            ";

            $stmt = $this->db->prepare($query);
            if (!$stmt) throw new Exception("Error preparando consulta");
            $stmt->bind_param('ii', $id, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $entrega = $result->fetch_assoc();
            $stmt->close();
            if ($entrega) {
                return $entrega;
            }

            // Si no está en produccion_final, buscar en retencion_destinos (destino_produccion)
            $queryDestino = "
                SELECT 
                    rd.id, rd.cajas, rd.piezas, rd.paletas, rd.cantidad AS cantidad_produccion, rd.paletas, rd.fecha_registro AS fecha_validacion, rd.estado,
                    rd.motivo AS comentario, NULL AS estado,
                    reg.item, reg.jtWo, reg.po, reg.cliente,
                    u.nombre AS validador_nombre,
                    m.nombre AS nombre_maquina,
                    reg.id AS registro_id,
                    'destino_produccion' AS tipo
                FROM retencion_destinos rd
                INNER JOIN retenciones r ON rd.retencion_id = r.id
                INNER JOIN registro reg ON r.registro_id = reg.id
                LEFT JOIN users u ON rd.usuario_id = u.codigo_empleado
                LEFT JOIN maquinas m ON reg.maquina = m.id
                WHERE rd.id = ? AND rd.usuario_id = ? AND rd.tipo_destino = 'produccion_final'
                LIMIT 1
            ";
            $stmt2 = $this->db->prepare($queryDestino);
            if (!$stmt2) throw new Exception("Error preparando consulta destino_produccion");
            $stmt2->bind_param('ii', $id, $userId);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $entrega2 = $result2->fetch_assoc();
            $stmt2->close();
            if ($entrega2) {
                return $entrega2;
            }

            return false;
        } catch (Exception $e) {
            Logger::error('Error obteniendo entrega por ID', [
                'error' => $e->getMessage(),
                'id' => $id,
                'user_id' => $userId
            ]);
            return false;
        }
    }

    public function actualizarEntrega($id, $userId, $data)
    {
        try {
            // Intentar actualizar en produccion_final
            $query = "UPDATE produccion_final 
                      SET paletas = ?, cajas = ?, piezas = ? 
                      WHERE id = ? AND usuario_id = ?";
            $stmt = $this->db->prepare($query);
            if ($stmt) {
                $stmt->bind_param(
                    'iiiii',
                    $data['paletas'],
                    $data['cajas'],
                    $data['piezas'],
                    $id,
                    $userId
                );
                $stmt->execute();
                $affected = $stmt->affected_rows;
                $stmt->close();
                if ($affected > 0) return true;
            }

            // Si no se actualizó nada, intentar en retencion_destinos (destino produccion_final)
            $query2 = "UPDATE retencion_destinos 
                        SET paletas = ?, cajas = ?, piezas = ? 
                        WHERE id = ? AND usuario_id = ? AND tipo_destino = 'produccion_final'";
            $stmt2 = $this->db->prepare($query2);
            if ($stmt2) {
                $stmt2->bind_param(
                    'iiiii',
                    $data['paletas'],
                    $data['cajas'],
                    $data['piezas'],
                    $id,
                    $userId
                );
                $stmt2->execute();
                $affected2 = $stmt2->affected_rows;
                $stmt2->close();
                if ($affected2 > 0) return true;
            }

            return false;
        } catch (Exception $e) {
            Logger::error('Error actualizando entrega', [
                'error' => $e->getMessage(),
                'id' => $id,
                'user_id' => $userId,
                'data' => $data
            ]);
            return false;
        }
    }
}
