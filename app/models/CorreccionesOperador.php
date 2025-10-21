<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class CorreccionesOperador extends Model
{
   
    public function getCorreccionesPendientesPorOperador($codigoEmpleado)
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
            r.po,
            r.cliente,
            r.cantidad_produccion,
            r.cantidad_scrapt,
            r.estado_validacion,
            r.maquina,
            r.fecha_registro,
            u.nombre AS qa_nombre,
            u.codigo_empleado AS qa_codigo,
            m.nombre AS nombre_maquina
        FROM solicitudes_correccion sc
        INNER JOIN registro r ON sc.registro_id = r.id
        LEFT JOIN users u ON sc.qa_solicita_id = u.codigo_empleado
        LEFT JOIN maquinas m ON r.maquina = m.id
        WHERE r.codigo_empleado = ?
            AND sc.estado = 'Pendiente'
        ORDER BY sc.fecha_solicitud DESC
    ";

    try {
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $this->db->error);
        }
        
        // Asegurarnos de que sea un entero
        $codigoEmpleadoInt = (int)$codigoEmpleado;
        $stmt->bind_param('i', $codigoEmpleadoInt);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar consulta: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $correcciones = $result->fetch_all(MYSQLI_ASSOC);

        Logger::info('Correcciones pendientes consultadas para operador', [
            'codigo_empleado' => $codigoEmpleado,
            'cantidad' => count($correcciones)
        ]);

        return $correcciones;
        
    } catch (Exception $e) {
        Logger::error('Error al obtener correcciones pendientes del operador', [
            'codigo_empleado' => $codigoEmpleado,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        return [];
    }
}

  
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
                r.po,
                r.cliente,
                r.cantidad_produccion,
                r.cantidad_scrapt,
                r.estado_validacion,
                r.maquina,
                r.fecha_registro,
                u.nombre AS qa_nombre,
                u.codigo_empleado AS qa_codigo,
                m.nombre AS nombre_maquina
            FROM solicitudes_correccion sc
            INNER JOIN registro r ON sc.registro_id = r.id
            LEFT JOIN users u ON sc.qa_solicita_id = u.codigo_empleado
            LEFT JOIN maquinas m ON r.maquina = m.id
            WHERE r.maquina = ? 
                AND sc.estado = 'Pendiente'
                AND r.estado_validacion = 'Correccion'
            ORDER BY sc.fecha_solicitud DESC
        ";

        try {
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }
            
            $stmt->bind_param('i', $maquinaId);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar consulta: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $correcciones = $result->fetch_all(MYSQLI_ASSOC);

            Logger::info('Correcciones pendientes consultadas por máquina', [
                'maquina_id' => $maquinaId,
                'cantidad' => count($correcciones)
            ]);

            return $correcciones;
            
        } catch (Exception $e) {
            Logger::error('Error al obtener correcciones pendientes', [
                'maquina_id' => $maquinaId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return [];
        }
    }

    public function actualizarCorreccion($solicitudId, $registroId, $tipo, $cantidad, $comentario)
    {
        $this->db->begin_transaction();

        try {
            // 1. Verificar que la corrección existe y está pendiente
            // 1. Verificar que la corrección existe y está pendiente
            $queryVerificar = "
                SELECT sc.id, sc.estado, r.estado_validacion, r.id as registro_id
                FROM solicitudes_correccion sc
                INNER JOIN registro r ON sc.registro_id = r.id
                WHERE sc.id = ? AND sc.estado = 'Pendiente'
            ";
            
            $stmt = $this->db->prepare($queryVerificar);
            
            if (!$stmt) {
                throw new Exception("Error al preparar verificación: " . $this->db->error);
            }
            
            $stmt->bind_param('i', $solicitudId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception('La corrección ya no está pendiente, ha sido cancelada o no existe');
            }
            
            $correccion = $result->fetch_assoc();

        
            $campoCantidad = $tipo === 'produccion' ? 'cantidad_produccion' : 'cantidad_scrapt';

            
            $queryRegistro = "
                UPDATE registro 
                SET 
                    $campoCantidad = ?,
                    estado_validacion = 'Pendiente'
                WHERE id = ?
            ";
            
            $stmt = $this->db->prepare($queryRegistro);
            
            if (!$stmt) {
                throw new Exception("Error al preparar actualización de registro: " . $this->db->error);
            }
            
            $stmt->bind_param('di', $cantidad, $registroId);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar el registro: ' . $stmt->error);
            }

            
            if ($stmt->affected_rows === 0) {
                throw new Exception('No se pudo actualizar el registro. Verifique que el ID sea correcto.');
            }

            $querySolicitud = "
                UPDATE solicitudes_correccion 
                SET 
                    estado = 'Procesada',
                    fecha_resolucion = NOW(),
                    cantidad_corregida = ?,
                    comentario_operador = ?
                WHERE id = ? AND estado = 'Pendiente'
            ";
            
            $stmt = $this->db->prepare($querySolicitud);
            
            if (!$stmt) {
                throw new Exception("Error al preparar actualización de solicitud: " . $this->db->error);
            }
            
            $stmt->bind_param('dsi', $cantidad, $comentario, $solicitudId);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar la solicitud de corrección: ' . $stmt->error);
            }

            // Verificar que se actualizo la solicitud
            if ($stmt->affected_rows === 0) {
                throw new Exception('No se pudo actualizar la solicitud de corrección. Puede que ya haya sido procesada.');
            }

            
            $this->db->commit();

            Logger::info('Corrección procesada exitosamente', [
                'solicitud_id' => $solicitudId,
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'comentario' => $comentario
            ]);

            return [
                'success' => true,
                'message' => 'Corrección actualizada correctamente. El registro ha sido enviado nuevamente a validación.'
            ];
            
        } catch (Exception $e) {
            // Rollback en caso de error
            $this->db->rollback();
            
            Logger::error('Error al procesar corrección', [
                'solicitud_id' => $solicitudId,
                'registro_id' => $registroId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

   
    public function tieneCorrecciones($maquinaId)
    {
        try {
            $query = "
                SELECT COUNT(*) as total
                FROM solicitudes_correccion sc
                INNER JOIN registro r ON sc.registro_id = r.id
                WHERE r.maquina = ? 
                    AND sc.estado = 'Pendiente'
                    AND r.estado_validacion = 'Correccion'
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $maquinaId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['total'] > 0;
            
        } catch (Exception $e) {
            Logger::error('Error al verificar correcciones', [
                'maquina_id' => $maquinaId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}