<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class Correccion extends Model
{
    public function solicitarCorreccion($registroId, $tipo, $nota, $usuarioId)
    {
        $this->db->begin_transaction();

        try {
            // Actualizar estado del registro
            $query = "UPDATE registro SET estado_validacion = 'Correccion' WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $registroId);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar estado del registro: ' . $stmt->error);
            }

            // Insertar solicitud de corrección
            $query = "INSERT INTO solicitudes_correccion 
                      (registro_id, tipo_cantidad, motivo, qa_solicita_id, fecha_solicitud) 
                      VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('isss', $registroId, $tipo, $nota, $usuarioId);

            if (!$stmt->execute()) {
                throw new Exception('Error al registrar la solicitud de corrección: ' . $stmt->error);
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Corrección registrada correctamente'
            ];
        } catch (\Exception $e) {
            $this->db->rollback();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
