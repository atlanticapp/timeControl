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

    public function obtenerCorreccionesPendientes($userId = null)
    {
        try {
            $query = "SELECT
                sc.id,
                sc.registro_id,
                sc.tipo_cantidad,
                sc.motivo,
                sc.fecha_solicitud,
                sc.fecha_resolucion,
                uq.nombre AS qa_solicita,
                ur.nombre AS empleado_entrega,
                r.item,
                r.jtWo,
                r.cantidad_produccion,
                r.cantidad_scrapt,
                r.maquina,
                m.nombre AS nombre_maquina  /* Agregamos el nombre de la máquina */
            FROM
                solicitudes_correccion sc
            INNER JOIN
                users uq ON sc.qa_solicita_id = uq.codigo_empleado
            INNER JOIN
                registro r ON sc.registro_id = r.id
            INNER JOIN
                users ur ON r.codigo_empleado = ur.codigo_empleado
            LEFT JOIN
                maquinas m ON r.maquina = m.id  /* Agregamos el JOIN con la tabla maquinas */
            WHERE
                sc.fecha_resolucion IS NULL
                " . ($userId ? "AND sc.qa_solicita_id = ?" : "") . "
            ORDER BY
                sc.fecha_solicitud DESC";

            if ($userId) {
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('s', $userId); // código_empleado es string
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->db->query($query);
            }

            if (!$result) {
                throw new Exception('Error al obtener correcciones pendientes: ' . $this->db->error);
            }

            $correcciones = [];
            while ($row = $result->fetch_assoc()) {
                $correcciones[] = $row;
            }

            return [
                'success' => true,
                'data' => $correcciones
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function obtenerEstadisticasPorTipo($userId = null)
    {
        try {
            // Consulta para obtener el total de correcciones pendientes por tipo
            $query = "SELECT 
                        COALESCE(SUM(CASE WHEN tipo_cantidad = 'produccion' THEN 1 ELSE 0 END), 0) as produccion,
                        COALESCE(SUM(CASE WHEN tipo_cantidad = 'scrap' THEN 1 ELSE 0 END), 0) as scrap,
                        COUNT(*) as total
                    FROM solicitudes_correccion
                    WHERE fecha_resolucion IS NULL
                    " . ($userId ? "AND qa_solicita_id = ?" : "");

            if ($userId) {
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('s', $userId);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->db->query($query);
            }

            if (!$result) {
                throw new Exception('Error al obtener estadísticas: ' . $this->db->error);
            }

            $estadisticas = $result->fetch_assoc();

            // Asegurar que todas las claves existan con valores por defecto
            $estadisticasFormateadas = [
                'total' => (int)($estadisticas['total'] ?? 0),
                'produccion' => (int)($estadisticas['produccion'] ?? 0),
                'scrap' => (int)($estadisticas['scrap'] ?? 0)
            ];

            return [
                'success' => true,
                'data' => $estadisticasFormateadas
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function cancelarCorreccion($id)
    {
        $this->db->begin_transaction();

        try {
            // Obtener el ID del registro relacionado
            $query = "SELECT registro_id FROM solicitudes_correccion WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new Exception('Error al obtener el registro relacionado: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception('No se encontró la solicitud de corrección.');
            }

            $row = $result->fetch_assoc();
            $registroId = $row['registro_id'];

            // Actualizar estado de la solicitud de corrección
            $query = "UPDATE solicitudes_correccion 
                      SET fecha_resolucion = NOW(), 
                          estado = 'Cancelada'
                      WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new Exception('Error al cancelar la solicitud de corrección: ' . $stmt->error);
            }

            // Restaurar estado del registro (asumimos 'Validado' como estado anterior)
            $query = "UPDATE registro SET estado_validacion = 'Pendiente' WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $registroId);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar el estado del registro: ' . $stmt->error);
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Corrección cancelada correctamente.'
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
