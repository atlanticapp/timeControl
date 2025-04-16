<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class Accion extends Model
{
    public function guardarProduccion($entregaId, $usuarioId, $comentario = '')
    {
        $this->db->begin_transaction();

        try {
            // Verificar si la entrega existe y su estado actual usando una sola consulta
            $query = "SELECT id, estado_validacion 
                     FROM registro 
                     WHERE id = ? 
                     FOR UPDATE";  // Bloqueo de fila para consistencia
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db->error);
            }
            
            $stmt->bind_param('i', $entregaId);
            if (!$stmt->execute()) {
                throw new Exception("Error al verificar la entrega: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("No se encontró la entrega especificada");
            }

            $registro = $result->fetch_assoc();
            
            if ($registro['estado_validacion'] === 'Guardado') {
                throw new Exception("Esta entrega ya ha sido Guardada");
            }

            // Actualizar estado y registrar historial en una sola transacción
            $queries = [
                "UPDATE registro SET estado_validacion = 'Guardado' WHERE id = ?",
                "INSERT INTO produccion_final (registro_id, usuario_id, tipo_validacion, comentario, fecha_validacion) 
                 VALUES (?, ?, 'produccion', ?, NOW())"
            ];

            foreach ($queries as $query) {
                $stmt = $this->db->prepare($query);
                if (!$stmt) {
                    throw new Exception("Error preparando consulta: " . $this->db->error);
                }

                if (strpos($query, 'UPDATE') !== false) {
                    $stmt->bind_param('i', $entregaId);
                } else {
                    $stmt->bind_param('iss', $entregaId, $usuarioId, $comentario);
                }

                if (!$stmt->execute()) {
                    throw new Exception("Error ejecutando consulta: " . $stmt->error);
                }
            }

            $validacionId = $this->db->insert_id;
            
            // Confirmar transacción
            $this->db->commit();

            Logger::info('Validación de producción exitosa', [
                'entrega_id' => $entregaId,
                'usuario_id' => $usuarioId,
                'validacion_id' => $validacionId
            ]);

            return [
                'success' => true,
                'message' => 'Entrega validada correctamente',
                'validacion_id' => $validacionId
            ];

        } catch (Exception $e) {
            $this->db->rollback();

            Logger::error('Error al validar producción', [
                'entrega_id' => $entregaId,
                'usuario_id' => $usuarioId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
