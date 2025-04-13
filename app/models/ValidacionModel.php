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

    /**
     * Crear una retención para producción validada
     */
    public function crearRetencion($registroId, $cantidad, $motivo, $usuarioId)
    {
        // Iniciar transacción
        $this->db->begin_transaction();

        try {
            // Verificar que la producción esté validada
            $query = "SELECT estado_validacion, cantidad_produccion FROM registro WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $registroId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("No se encontró el registro");
            }

            $row = $result->fetch_assoc();

            if ($row['estado_validacion'] !== 'Validado') {
                throw new Exception("La producción debe estar validada para retenerla");
            }

            if ($cantidad > $row['cantidad_produccion']) {
                throw new Exception("La cantidad a retener no puede ser mayor que la cantidad producida");
            }

            // Crear retención
            $query = "INSERT INTO retenciones 
                     (registro_id, cantidad_total, cantidad_disponible, motivo, usuario_id, fecha_creacion, estado) 
                     VALUES (?, ?, ?, ?, ?, NOW(), 'activa')";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('iddsi', $registroId, $cantidad, $cantidad, $motivo, $usuarioId);

            if (!$stmt->execute()) {
                throw new Exception("Error al crear retención: " . $stmt->error);
            }

            $retencionId = $this->db->insert_id;

            // Actualizar estado del registro
            $query = "UPDATE registro SET estado_validacion = 'Retenido' WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $registroId);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar estado del registro: " . $stmt->error);
            }

            // Confirmar transacción
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Retención creada correctamente',
                'retencion_id' => $retencionId
            ];
        } catch (Exception $e) {
            // Revertir cambios en caso de error
            $this->db->rollback();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Asignar un destino a material retenido
     */
    public function asignarDestinoRetencion($retencionId, $tipoDestino, $cantidad, $motivo, $usuarioId)
    {
        // Iniciar transacción
        $this->db->begin_transaction();

        try {
            // Verificar disponibilidad en la retención
            $query = "SELECT cantidad_disponible, registro_id FROM retenciones WHERE id = ? AND estado = 'activa'";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $retencionId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("No se encontró la retención o está cerrada");
            }

            $row = $result->fetch_assoc();
            $cantidadDisponible = $row['cantidad_disponible'];
            $registroId = $row['registro_id'];

            if ($cantidad > $cantidadDisponible) {
                throw new Exception("La cantidad solicitada excede la disponible");
            }

            // Registrar destino
            $query = "INSERT INTO retencion_destinos 
                     (retencion_id, tipo_destino, cantidad, motivo, usuario_id, fecha_registro) 
                     VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('isdsi', $retencionId, $tipoDestino, $cantidad, $motivo, $usuarioId);

            if (!$stmt->execute()) {
                throw new Exception("Error al registrar destino: " . $stmt->error);
            }

            $destinoId = $this->db->insert_id;

            // Actualizar cantidad disponible
            $nuevaDisponible = $cantidadDisponible - $cantidad;
            $query = "UPDATE retenciones SET cantidad_disponible = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('di', $nuevaDisponible, $retencionId);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar cantidad disponible: " . $stmt->error);
            }

            // Si ya no queda cantidad disponible, cerrar la retención
            if ($nuevaDisponible <= 0) {
                $query = "UPDATE retenciones SET estado = 'cerrada', fecha_cierre = NOW() WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('i', $retencionId);

                if (!$stmt->execute()) {
                    throw new Exception("Error al cerrar retención: " . $stmt->error);
                }

                // Actualizar estado del registro si corresponde (si toda la cantidad está procesada)
                $this->actualizarEstadoRegistroPostRetencion($registroId);
            }

            // Confirmar transacción
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Destino asignado correctamente',
                'destino_id' => $destinoId
            ];
        } catch (Exception $e) {
            // Revertir cambios en caso de error
            $this->db->rollback();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualiza el estado de validación de scrap en un registro
     */
    private function actualizarEstadoValidacion($registroId)
    {
        // Verificar el estado de validación actual y si hay producción y scrap pendientes
        $query = "SELECT 
                    id, 
                    cantidad_produccion, 
                    cantidad_scrapt, 
                    estado_validacion
                  FROM registro WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $registroId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return; // No hacer nada si no existe el registro
        }

        $row = $result->fetch_assoc();

        // Verificar si hay validación de producción
        $query = "SELECT COUNT(*) as total FROM validaciones_produccion WHERE registro_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $registroId);
        $stmt->execute();
        $resultProd = $stmt->get_result();
        $rowProd = $resultProd->fetch_assoc();
        $produccionValidada = $rowProd['total'] > 0;

        // Verificar si hay validación de scrap
        $query = "SELECT COUNT(*) as total FROM scrap_final WHERE registro_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $registroId);
        $stmt->execute();
        $resultScrap = $stmt->get_result();
        $rowScrap = $resultScrap->fetch_assoc();
        $scrapValidado = $rowScrap['total'] > 0;

        // Determinar el nuevo estado
        $nuevoEstado = 'Pendiente';

        if ($produccionValidada && $scrapValidado) {
            $nuevoEstado = 'Validado';
        } else if ($produccionValidada && $row['cantidad_scrapt'] <= 0) {
            $nuevoEstado = 'Validado'; // No hay scrap, solo producción
        } else if ($scrapValidado && $row['cantidad_produccion'] <= 0) {
            $nuevoEstado = 'Validado'; // No hay producción, solo scrap
        } else if ($produccionValidada || $scrapValidado) {
            $nuevoEstado = 'Parcial'; // Se validó uno pero falta el otro
        }

        // Actualizar el estado
        $query = "UPDATE registro SET estado_validacion = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $nuevoEstado, $registroId);
        $stmt->execute();
    }

    /**
     * Actualiza el estado del registro después de procesar una retención
     */
    private function actualizarEstadoRegistroPostRetencion($registroId)
    {
        $query = "SELECT COUNT(*) as retenciones_activas FROM retenciones 
                  WHERE registro_id = ? AND estado = 'activa'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $registroId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Si no hay retenciones activas, marcar como validado
        if ($row['retenciones_activas'] == 0) {
            $query = "UPDATE registro SET estado_validacion = 'Validado' WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $registroId);
            $stmt->execute();
        }
    }
}
