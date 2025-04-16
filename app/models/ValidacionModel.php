<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class ValidacionModel extends Model
{

    public function validarScrap($registroId, $cantidad, $observaciones, $usuarioId)
    {
        $this->db->begin_transaction();

        try {
            $stmt = $this->db->prepare("SELECT cantidad_scrapt FROM registro WHERE id = ?");
            $stmt->bind_param('i', $registroId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("No se encontró el registro");
            }

            $stmt = $this->db->prepare("
                INSERT INTO scrap_final (registro_id, cantidad, observaciones, usuario_qa_id, fecha_validacion)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param('idsi', $registroId, $cantidad, $observaciones, $usuarioId);
            if (!$stmt->execute()) {
                throw new Exception("Error al guardar el scrap validado: " . $stmt->error);
            }

            $this->actualizarEstadoValidacionScrap($registroId, $usuarioId);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Scrap validado correctamente',
                'scrap_id' => $this->db->insert_id
            ];
        } catch (\Exception $e) {
            $this->db->rollback();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function actualizarEstadoValidacionScrap($registroId, $codeQa)
    {
        $stmt = $this->db->prepare("
        SELECT COUNT(*) AS hay_produccion 
        FROM registro 
        WHERE id = ? AND cantidad_produccion > 0
        ");
        $stmt->bind_param('i', $registroId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hayProduccion = $result->fetch_assoc()['hay_produccion'];

        if ($hayProduccion == 0) {
            $stmt = $this->db->prepare("
            UPDATE registro 
            SET estado_validacion = 'Validado', 
                validado_por = ? 
            WHERE id = ?
        ");
            $stmt->bind_param('ii', $codeQa, $registroId);
            $stmt->execute();
        }
    }

    public function validarProduccion($registroId, $codeQa)
    {
        $query = "UPDATE registro 
              SET estado_validacion = 'Validado', 
                  validado_por = ? 
              WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $codeQa, $registroId);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Producción validada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al validar producción: ' . $stmt->error
            ];
        }
    }
}
