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
            $query = "SELECT pf.id, pf.fecha_validacion, pf.comentario,
                            r.item, r.jtWo, r.cantidad_produccion,
                            u.nombre as validador_nombre,
                            m.nombre as nombre_maquina
                     FROM produccion_final pf
                     INNER JOIN registro r ON pf.registro_id = r.id
                     INNER JOIN users u ON pf.usuario_id = u.codigo_empleado
                     LEFT JOIN maquinas m ON r.maquina = m.id
                     WHERE r.estado_validacion = 'Guardado' 
                     AND pf.usuario_id = ?
                     ORDER BY pf.fecha_validacion DESC";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando consulta");
            }
            
            $stmt->bind_param('s', $userId);
            
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando consulta");
            }
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            Logger::error('Error obteniendo producción guardada', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return [];
        }
    }
}