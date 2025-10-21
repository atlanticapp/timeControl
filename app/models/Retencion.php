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
                    reg.cantidad_produccion,
                    reg.cantidad_scrapt,
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
            
            $retenciones = [];
            while ($row = $result->fetch_assoc()) {
                $row['tipo_retencion'] = $this->extraerTipoRetencion($row['motivo']);
                $row['motivo_limpio'] = $this->limpiarMotivo($row['motivo']);
                $retenciones[] = $row;
            }

            Logger::info('Consulta de retenciones activas', [
                'area_id' => $areaId,
                'codigo_empleado' => $codigoEmpleado,
                'cantidad' => count($retenciones)
            ]);

            return $retenciones;
        } catch (\Exception $e) {
            Logger::error('Error al obtener retenciones activas', [
                'area_id' => $areaId,
                'codigo_empleado' => $codigoEmpleado,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    
    public function crearRetencion($registroId, $cantidad, $motivo, $usuarioId, $tipo = 'produccion')
    {
        $this->db->begin_transaction();

        try {
            $query = "SELECT id, estado_validacion, cantidad_produccion, cantidad_scrapt 
                     FROM registro 
                     WHERE id = ? 
                     FOR UPDATE";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $registroId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("No se encontró el registro especificado");
            }

            $registro = $result->fetch_assoc();
            
            if ($registro['estado_validacion'] !== 'Validado') {
                throw new Exception("El registro debe estar en estado Validado");
            }

            $cantidadDisponible = $tipo === 'scrap' ? 
                (float)$registro['cantidad_scrapt'] : 
                (float)$registro['cantidad_produccion'];

            if ($cantidad > $cantidadDisponible) {
                $tipoTexto = $tipo === 'scrap' ? 'scrap' : 'producción';
                throw new Exception("La cantidad a retener ({$cantidad} Lb) no puede ser mayor que la cantidad de {$tipoTexto} disponible ({$cantidadDisponible} Lb)");
            }

            if ($cantidad <= 0) {
                throw new Exception("La cantidad debe ser mayor que cero");
            }

            $motivoConTipo = "[TIPO:{$tipo}] {$motivo}";

            $insertQuery = "INSERT INTO retenciones 
                           (registro_id, cantidad_total, cantidad_disponible, motivo, estado, usuario_id, fecha_creacion) 
                           VALUES (?, ?, ?, ?, 'activa', ?, NOW())";
            
            $stmt = $this->db->prepare($insertQuery);
            $stmt->bind_param('iddsi', $registroId, $cantidad, $cantidad, $motivoConTipo, $usuarioId);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear la retención: " . $stmt->error);
            }

            $retencionId = $this->db->insert_id;

            $updateQuery = "UPDATE registro SET estado_validacion = 'Retenido' WHERE id = ?";
            $stmt = $this->db->prepare($updateQuery);
            $stmt->bind_param('i', $registroId);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el estado: " . $stmt->error);
            }

            $this->db->commit();

            Logger::info('Retención creada exitosamente', [
                'retencion_id' => $retencionId,
                'registro_id' => $registroId,
                'cantidad' => $cantidad,
                'tipo' => $tipo,
                'usuario_id' => $usuarioId
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
                'cantidad' => $cantidad,
                'tipo' => $tipo,
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
        $this->db->begin_transaction();

        try {
            $query = "SELECT r.cantidad_disponible, r.registro_id, r.motivo 
                     FROM retenciones r 
                     WHERE r.id = ? AND r.estado = 'activa'";
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
            $tipoRetencion = $this->extraerTipoRetencion($row['motivo']);

            if ($cantidad > $cantidadDisponible) {
                throw new Exception("La cantidad solicitada ({$cantidad} Lb) excede la disponible ({$cantidadDisponible} Lb)");
            }

            if ($cantidad <= 0) {
                throw new Exception("La cantidad debe ser mayor que cero");
            }

            // Insertar en retencion_destinos
            // AQUI es donde se registra el destino (produccion_final, destruccion, retrabajo)
            $query = "INSERT INTO retencion_destinos 
                     (retencion_id, tipo_destino, cantidad, motivo, usuario_id, fecha_registro) 
                     VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('isdsi', $retencionId, $tipoDestino, $cantidad, $motivo, $usuarioId);

            if (!$stmt->execute()) {
                throw new Exception("Error al registrar destino: " . $stmt->error);
            }

            $destinoId = $this->db->insert_id;

            $nuevaDisponible = $cantidadDisponible - $cantidad;
            $query = "UPDATE retenciones SET cantidad_disponible = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('di', $nuevaDisponible, $retencionId);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar cantidad disponible: " . $stmt->error);
            }

            // Si se asignó todo, cerrar la retención
            if ($nuevaDisponible <= 0) {
                $query = "UPDATE retenciones SET estado = 'cerrada', fecha_cierre = NOW() WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('i', $retencionId);

                if (!$stmt->execute()) {
                    throw new Exception("Error al cerrar retención: " . $stmt->error);
                }

                // Actualizar estado del registro a Guardado
                $this->actualizarEstadoRegistroPostRetencion($registroId);
            }

            // NO insertar nada en produccion_final ni scrap_final
            // Solo queda el registro en retencion_destinos
            // El reporte viene de retencion_destinos, no de produccion_final/scrap_final

            $this->db->commit();

            Logger::info('Destino asignado a retención', [
                'retencion_id' => $retencionId,
                'destino_id' => $destinoId,
                'tipo_destino' => $tipoDestino,
                'cantidad' => $cantidad,
                'tipo_retencion' => $tipoRetencion,
                'motivo' => $motivo
            ]);

            return [
                'success' => true,
                'message' => 'Destino asignado correctamente',
                'destino_id' => $destinoId
            ];
        } catch (Exception $e) {
            $this->db->rollback();

            Logger::error('Error al asignar destino', [
                'retencion_id' => $retencionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    
    private function actualizarEstadoRegistroPostRetencion($registroId)
    {
        try {
            // Verificar si hay retenciones activas
            $query = "SELECT COUNT(*) as retenciones_activas FROM retenciones 
                      WHERE registro_id = ? AND estado = 'activa'";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $registroId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            // Si no hay retenciones activas, marcar como Guardado
            if ($row['retenciones_activas'] == 0) {
                $query = "UPDATE registro SET estado_validacion = 'Guardado' WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('i', $registroId);
                $stmt->execute();

                Logger::info('Registro actualizado a Guardado después de liberar retención', [
                    'registro_id' => $registroId
                ]);
            }
        } catch (Exception $e) {
            Logger::error('Error al actualizar estado del registro', [
                'registro_id' => $registroId,
                'error' => $e->getMessage()
            ]);
        }
    }

    
    public function extraerTipoRetencion($motivo)
    {
        if (preg_match('/\[TIPO:(produccion|scrap)\]/', $motivo, $matches)) {
            return $matches[1];
        }
        return 'produccion'; // Por defecto
    }

    
    public function limpiarMotivo($motivo)
    {
        return preg_replace('/\[TIPO:(produccion|scrap)\]\s*/', '', $motivo);
    }

   
    public function obtenerRetencionPorId($retencionId)
    {
        try {
            $query = "SELECT r.*, reg.item, reg.jtWo, reg.maquina, m.nombre as nombre_maquina,
                            reg.cantidad_produccion, reg.cantidad_scrapt
                     FROM retenciones r
                     INNER JOIN registro reg ON r.registro_id = reg.id
                     LEFT JOIN maquinas m ON reg.maquina = m.id
                     WHERE r.id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $retencionId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }

            $retencion = $result->fetch_assoc();
            $retencion['tipo_retencion'] = $this->extraerTipoRetencion($retencion['motivo']);
            $retencion['motivo_limpio'] = $this->limpiarMotivo($retencion['motivo']);

            return $retencion;

        } catch (Exception $e) {
            Logger::error('Error al obtener retención por ID', [
                'retencion_id' => $retencionId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    
    public function obtenerDestinosRetencion($retencionId)
    {
        try {
            $query = "SELECT * FROM retencion_destinos 
                      WHERE retencion_id = ?
                      ORDER BY fecha_registro DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $retencionId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $destinos = [];
            while ($row = $result->fetch_assoc()) {
                $destinos[] = $row;
            }

            return $destinos;

        } catch (Exception $e) {
            Logger::error('Error al obtener destinos de retención', [
                'retencion_id' => $retencionId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}