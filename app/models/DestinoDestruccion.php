<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class DestinoDestruccion extends Model
{
    public function getDestinosDestruccion($areaId = null, $userId = null)
    {
        try {
            $query = "
                SELECT 
                    rd.id,
                    rd.retencion_id,
                    rd.tipo_destino,
                    rd.cantidad,
                    rd.motivo,
                    rd.fecha_registro,
                    r.registro_id,
                    reg.jtWo,
                    reg.item,
                    reg.maquina,
                    m.nombre as nombre_maquina,
                    reg.area_id,
                    u.nombre as usuario_nombre
                FROM retencion_destinos rd
                INNER JOIN retenciones r ON rd.retencion_id = r.id
                INNER JOIN registro reg ON r.registro_id = reg.id
                LEFT JOIN users u ON rd.usuario_id = u.codigo_empleado
                LEFT JOIN maquinas m ON reg.maquina = m.id
                WHERE rd.tipo_destino = 'destruccion'
                " . ($areaId ? "AND reg.area_id = ?" : "") . "
                " . ($userId ? "AND rd.usuario_id = ?" : "") . "
                ORDER BY rd.fecha_registro DESC";

            $stmt = $this->db->prepare($query);
            
            // Preparar los parámetros de manera dinámica
            $types = '';
            $params = [];
            
            if ($areaId) {
                $types .= 'i';
                $params[] = $areaId;
            }
            if ($userId) {
                $types .= 's'; // código_empleado es string
                $params[] = $userId;
            }
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            Logger::info('Consulta de destinos destrucción', [
                'area_id' => $areaId,
                'usuario_id' => $userId,
                'cantidad' => $result->num_rows
            ]);

            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (\Exception $e) {
            Logger::error('Error al obtener destinos destrucción', [
                'error' => $e->getMessage(),
                'area_id' => $areaId,
                'usuario_id' => $userId
            ]);
            
            return [];
        }
    }
}