<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class CorreccionesOperador extends Model
{
    public function getCorreccionesPendientes($maquinaId)
    {
        $query = "
            SELECT 
                sc.id AS solicitud_id,
                sc.registro_id,
                sc.tipo_cantidad,
                sc.motivo,
                sc.fecha_solicitud,
                sc.estado,
                r.item,
                r.jtWo,
                r.cantidad_produccion,
                r.cantidad_scrapt,
                r.estado_validacion,
                r.maquina,
                u.nombre AS qa_nombre
            FROM solicitudes_correccion sc
            INNER JOIN registro r ON sc.registro_id = r.id
            LEFT JOIN users u ON sc.qa_solicita_id = u.codigo_empleado
            WHERE r.maquina = ? 
                AND sc.estado = 'Pendiente'
                AND r.estado_validacion = 'Correccion'
            ORDER BY sc.fecha_solicitud DESC
        ";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $maquinaId);
            $stmt->execute();
            $result = $stmt->get_result();

            Logger::info('Correcciones pendientes consultadas', [
                'maquina_id' => $maquinaId,
                'cantidad' => $result->num_rows
            ]);

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            Logger::error('Error al obtener correcciones pendientes', [
                'maquina_id' => $maquinaId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function actualizarCorreccion($solicitudId, $registroId, $tipo, $cantidad, $comentario)
    {
        $this->db->begin_transaction();

        try {
            // Verificar si la corrección aún está pendiente
            $query = "SELECT estado FROM solicitudes_correccion WHERE id = ? AND estado = 'Pendiente'";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $solicitudId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception('La corrección ya no está pendiente o no existe');
            }

            // Determinar el campo a actualizar según el tipo
            $campoCantidad = $tipo === 'produccion' ? 'cantidad_produccion' : 'cantidad_scrapt';

            // 1. Actualizar el registro
            $query = "
                UPDATE registro 
                SET 
                    $campoCantidad = ?,
                    estado_validacion = 'Pendiente'
                WHERE id = ?
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('di', $cantidad, $registroId);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar el registro: ' . $stmt->error);
            }

            // 2. Actualizar la solicitud de corrección
            $query = "
                UPDATE solicitudes_correccion 
                SET 
                    estado = 'Procesada',
                    fecha_resolucion = NOW(),
                    cantidad_corregida = ?,
                    comentario_operador = ?
                WHERE id = ? AND estado = 'Pendiente'
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('dsi', $cantidad, $comentario, $solicitudId);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar la solicitud de corrección: ' . $stmt->error);
            }

            $this->db->commit();

            Logger::info('Corrección procesada exitosamente', [
                'solicitud_id' => $solicitudId,
                'registro_id' => $registroId,
            ]);

            return [
                'success' => true,
                'message' => 'Corrección actualizada correctamente'
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            
            Logger::error('Error al procesar corrección', [
                'solicitud_id' => $solicitudId,
                'registro_id' => $registroId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
