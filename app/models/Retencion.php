<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class Retencion extends Model
{
    public function getRetencionesActivas($areaId = null, $codigoEmpleado = null)
    {
        try {
            $query = "
            SELECT 
                r.*,
                reg.jtWo,
                reg.item,
                reg.maquina,
                m.nombre as nombre_maquina,
                reg.area_id,
                u.nombre as usuario_nombre
            FROM retenciones r
            INNER JOIN registro reg ON r.registro_id = reg.id
            LEFT JOIN users u ON r.usuario_id = u.codigo_empleado
            LEFT JOIN maquinas m ON reg.maquina = m.id
            WHERE r.estado = 'activa'
            " . ($areaId ? "AND reg.area_id = ?" : "") . "
            " . ($codigoEmpleado ? "AND r.usuario_id = ?" : "") . "
            ORDER BY r.fecha_creacion DESC
        ";

            $stmt = $this->db->prepare($query);

            if ($areaId && $codigoEmpleado) {
                $stmt->bind_param('is', $areaId, $codigoEmpleado);
            } elseif ($areaId) {
                $stmt->bind_param('i', $areaId);
            } elseif ($codigoEmpleado) {
                $stmt->bind_param('s', $codigoEmpleado);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            Logger::info('Consulta de retenciones activas', [
                'area_id' => $areaId,
                'codigo_empleado' => $codigoEmpleado,
                'cantidad' => $result->num_rows
            ]);

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            Logger::error('Error al obtener retenciones activas', [
                'area_id' => $areaId,
                'codigo_empleado' => $codigoEmpleado,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    public function crearRetencion($registroId, $cantidad, $motivo, $usuarioId)
    {
        $this->db->begin_transaction();

        try {
            // Verificar que la producción esté validada
            $query = "
                SELECT 
                    estado_validacion, 
                    cantidad_produccion 
                FROM registro 
                WHERE id = ?
            ";

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
            $query = "
                INSERT INTO retenciones (
                    registro_id, 
                    cantidad_total, 
                    cantidad_disponible, 
                    motivo, 
                    usuario_id, 
                    fecha_creacion, 
                    estado
                ) VALUES (?, ?, ?, ?, ?, NOW(), 'activa')
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('iddsi', $registroId, $cantidad, $cantidad, $motivo, $usuarioId);

            if (!$stmt->execute()) {
                throw new Exception("Error al crear retención: " . $stmt->error);
            }

            $retencionId = $this->db->insert_id;

            // Actualizar estado del registro
            $query = "
                UPDATE registro 
                SET estado_validacion = 'Retenido' 
                WHERE id = ?
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $registroId);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar estado del registro: " . $stmt->error);
            }

            $this->db->commit();

            Logger::info('Retención creada exitosamente', [
                'retencion_id' => $retencionId,
                'registro_id' => $registroId,
                'cantidad' => $cantidad
            ]);

            return [
                'success' => true,
                'message' => 'Retención creada correctamente',
                'retencion_id' => $retencionId
            ];
        } catch (Exception $e) {
            $this->db->rollback();

            Logger::error('Error al crear retención', [
                'registro_id' => $registroId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

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
