<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class ValidacionModel extends Model
{

    /**
     * QA: Valida Scrap - Solo marca como Validado, NO inserta en scrap_final
     */
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

            // SOLO actualizar estado a Validado
            // NO insertar en scrap_final aquí
            $this->actualizarEstadoValidacionScrap($registroId, $usuarioId);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Scrap validado correctamente'
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Exception en validarScrap: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * QA: Valida Producción - Solo marca como Validado, NO inserta en produccion_final
     */
    public function validarProduccion($registroId, $usuarioId)
    {
        $this->db->begin_transaction();

        try {
            // SOLO actualizar estado del registro a Validado
            // NO insertar en produccion_final aquí
            $stmt = $this->db->prepare("
                UPDATE registro 
                SET estado_validacion = 'Validado', 
                    validado_por = ? 
                WHERE id = ?
            ");
            $stmt->bind_param('ii', $usuarioId, $registroId);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar estado del registro: " . $stmt->error);
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Producción validada correctamente'
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * SUPERVISOR: Valida Scrap - Solo marca como Validado, NO inserta en scrap_final
     */
    public function validarScrapSupervisor($registroId, $cantidad, $observaciones, $usuarioId)
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

            // SOLO actualizar estado a Validado
            // NO insertar en scrap_final aquí
            $this->actualizarEstadoValidacionScrapSupervisor($registroId, $usuarioId);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Scrap validado correctamente por supervisor'
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Exception en validarScrapSupervisor: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * SUPERVISOR: Valida Producción - Solo marca como Validado, NO inserta en produccion_final
     */
    public function validarProduccionSupervisor($registroId, $usuarioId)
    {
        $this->db->begin_transaction();

        try {
            // SOLO actualizar estado del registro a Validado
            // NO insertar en produccion_final aquí
            $stmt = $this->db->prepare("
                UPDATE registro 
                SET estado_validacion = 'Validado', 
                    validado_por = ? 
                WHERE id = ?
            ");
            $stmt->bind_param('ii', $usuarioId, $registroId);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar estado del registro: " . $stmt->error);
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Producción validada correctamente por supervisor'
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * QA: Registra la producción validada en produccion_final (marca como Guardado)
     * Esto se ejecuta después de validar
     */
    public function registrarProduccionFinal($registroId, $usuarioId, $comentario = '')
    {
        $this->db->begin_transaction();

        try {
            // Verificar que el registro está validado
            $stmt = $this->db->prepare("
                SELECT id, estado_validacion, cantidad_produccion 
                FROM registro 
                WHERE id = ? AND estado_validacion = 'Validado'
            ");
            $stmt->bind_param('i', $registroId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("El registro no está en estado Validado");
            }

            $registro = $result->fetch_assoc();

            // Insertar en produccion_final
            $stmt = $this->db->prepare("
                INSERT INTO produccion_final 
                (registro_id, usuario_id, tipo_validacion, comentario, fecha_validacion) 
                VALUES (?, ?, 'produccion', ?, NOW())
            ");
            $stmt->bind_param('iss', $registroId, $usuarioId, $comentario);

            if (!$stmt->execute()) {
                throw new Exception("Error al registrar en produccion_final: " . $stmt->error);
            }

            // Actualizar estado del registro a Guardado
            $stmt = $this->db->prepare("
                UPDATE registro 
                SET estado_validacion = 'Guardado' 
                WHERE id = ?
            ");
            $stmt->bind_param('i', $registroId);
            $stmt->execute();

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Producción registrada en produccion_final'
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Exception en registrarProduccionFinal: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * QA: Registra el scrap validado en scrap_final (marca como Guardado)
     * Esto se ejecuta después de validar
     */
    public function registrarScrapFinal($registroId, $cantidad, $usuarioId, $observaciones = '')
    {
        $this->db->begin_transaction();

        try {
            // Verificar que el registro está validado
            $stmt = $this->db->prepare("
                SELECT id, estado_validacion, cantidad_scrapt 
                FROM registro 
                WHERE id = ? AND estado_validacion = 'Validado'
            ");
            $stmt->bind_param('i', $registroId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("El registro no está en estado Validado");
            }

            $registro = $result->fetch_assoc();

            // Insertar en scrap_final
            $stmt = $this->db->prepare("
                INSERT INTO scrap_final 
                (registro_id, cantidad, observaciones, usuario_qa_id, fecha_validacion) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param('idss', $registroId, $cantidad, $observaciones, $usuarioId);

            if (!$stmt->execute()) {
                throw new Exception("Error al registrar en scrap_final: " . $stmt->error);
            }

            // Actualizar estado del registro a Guardado
            $stmt = $this->db->prepare("
                UPDATE registro 
                SET estado_validacion = 'Guardado' 
                WHERE id = ?
            ");
            $stmt->bind_param('i', $registroId);
            $stmt->execute();

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Scrap registrado en scrap_final'
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Exception en registrarScrapFinal: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function actualizarEstadoValidacionScrap($registroId, $usuarioId)
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
            $stmt->bind_param('ii', $usuarioId, $registroId);
            $stmt->execute();
        }
    }

    private function actualizarEstadoValidacionScrapSupervisor($registroId, $usuarioId)
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
            $stmt->bind_param('ii', $usuarioId, $registroId);
            $stmt->execute();
        }
    }

    /**
     * Solicita corrección (ambos roles)
     */
    public function solicitarCorreccion($registroId, $tipo, $motivo, $usuarioId)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO solicitudes_correccion (registro_id, tipo_cantidad, motivo, qa_solicita_id, fecha_solicitud)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param('issi', $registroId, $tipo, $motivo, $usuarioId);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Solicitud de corrección enviada correctamente'
                ];
            } else {
                throw new Exception("Error al crear la solicitud: " . $stmt->error);
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}