<?php

namespace App\Models;

use App\Core\Model;
use App\Helpers\Logger;
use Exception;

class Accion extends Model
{
    
    public function guardarProduccion($entregaId, $usuarioId, $comentario = '', $tipo = 'produccion')
    {
        $this->db->begin_transaction();

        try {
            // Verificar entrega con el tipo correcto
            $query = "SELECT id, estado_validacion, cantidad_produccion, cantidad_scrapt 
                     FROM registro 
                     WHERE id = ? 
                     FOR UPDATE";
            
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
            
            // Verificar estado
            if ($registro['estado_validacion'] !== 'Validado') {
                throw new Exception("Esta entrega no está en estado 'Validado'. Estado actual: " . $registro['estado_validacion']);
            }

            // VALIDACIÓN SEGÚN TIPO
            if ($tipo === 'scrap') {
                if ($registro['cantidad_scrapt'] <= 0) {
                    throw new Exception("La cantidad de scrap debe ser mayor que cero");
                }
            } else {
                if ($registro['cantidad_produccion'] <= 0) {
                    throw new Exception("La cantidad de producción debe ser mayor que cero");
                }
            }

            // Actualizar estado a "Guardado"
            $updateQuery = "UPDATE registro SET estado_validacion = 'Guardado' WHERE id = ?";
            $stmt = $this->db->prepare($updateQuery);
            if (!$stmt) {
                throw new Exception("Error preparando actualización: " . $this->db->error);
            }

            $stmt->bind_param('i', $entregaId);
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el estado: " . $stmt->error);
            }

            
            if ($tipo === 'scrap') {
                // Para scrap_final: registro_id, cantidad, observaciones, usuario_id, fecha_validacion
                $cantidad = $registro['cantidad_scrapt'];
                $insertQuery = "INSERT INTO scrap_final (registro_id, cantidad, observaciones, usuario_id, fecha_validacion) 
                               VALUES (?, ?, ?, ?, NOW())";
                
                $stmt = $this->db->prepare($insertQuery);
                if (!$stmt) {
                    throw new Exception("Error preparando inserción en scrap_final: " . $this->db->error);
                }
                
                // tipos: i=integer, d=decimal, s=string, i=integer
                $stmt->bind_param('idsi', $entregaId, $cantidad, $comentario, $usuarioId);
            } else {
                // Para produccion_final: registro_id, usuario_id, comentario, fecha_validacion
                $insertQuery = "INSERT INTO produccion_final (registro_id, usuario_id, comentario, fecha_validacion) 
                               VALUES (?, ?, ?, NOW())";
                
                $stmt = $this->db->prepare($insertQuery);
                if (!$stmt) {
                    throw new Exception("Error preparando inserción en produccion_final: " . $this->db->error);
                }
                
                // tipos: i=integer, s=string, s=string
                $stmt->bind_param('iss', $entregaId, $usuarioId, $comentario);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Error al registrar en historial: " . $stmt->error);
            }

            $validacionId = $this->db->insert_id;
            
            $this->db->commit();

            Logger::info('Entrega guardada exitosamente desde Acción QA', [
                'entrega_id' => $entregaId,
                'usuario_id' => $usuarioId,
                'validacion_id' => $validacionId,
                'tipo' => $tipo,
                'comentario' => $comentario
            ]);

            return [
                'success' => true,
                'message' => 'Entrega procesada y guardada correctamente',
                'validacion_id' => $validacionId
            ];

        } catch (Exception $e) {
            $this->db->rollback();

            Logger::error('Error al guardar entrega desde Acción QA', [
                'entrega_id' => $entregaId,
                'usuario_id' => $usuarioId,
                'tipo' => $tipo,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar disponibilidad considerando el tipo
     */
    public function verificarDisponibilidadEntrega($entregaId, $tipo = 'produccion')
    {
        try {
            $query = "SELECT 
                        id, 
                        estado_validacion, 
                        cantidad_produccion,
                        cantidad_scrapt,
                        item,
                        jtWo
                      FROM registro 
                      WHERE id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param('i', $entregaId);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return [
                    'disponible' => false,
                    'motivo' => 'Entrega no encontrada'
                ];
            }

            $registro = $result->fetch_assoc();

            if ($registro['estado_validacion'] !== 'Validado') {
                return [
                    'disponible' => false,
                    'motivo' => 'La entrega no está en estado Validado'
                ];
            }

            // Validar según tipo
            $cantidad = $tipo === 'scrap' ? $registro['cantidad_scrapt'] : $registro['cantidad_produccion'];
            
            if ($cantidad <= 0) {
                return [
                    'disponible' => false,
                    'motivo' => "La cantidad de {$tipo} no es válida"
                ];
            }

            return [
                'disponible' => true,
                'registro' => $registro
            ];

        } catch (Exception $e) {
            Logger::error('Error al verificar disponibilidad de entrega', [
                'entrega_id' => $entregaId,
                'tipo' => $tipo,
                'error' => $e->getMessage()
            ]);

            return [
                'disponible' => false,
                'motivo' => 'Error interno al verificar la entrega'
            ];
        }
    }
    public function getHistorialAcciones($usuarioId, $limit = 50)
    {
        try {
            
            $query = "
                (SELECT 
                    pf.id,
                    pf.registro_id,
                    pf.fecha_validacion,
                    pf.comentario,
                    r.item,
                    r.jtWo,
                    r.cantidad_produccion as cantidad,
                    r.maquina,
                    m.nombre as nombre_maquina,
                    u.nombre as nombre_empleado,
                    'produccion' as tipo
                FROM produccion_final pf
                INNER JOIN registro r ON pf.registro_id = r.id
                LEFT JOIN maquinas m ON r.maquina = m.id
                LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                WHERE pf.usuario_id = ?)
                
                UNION ALL
                
                (SELECT 
                    sf.id,
                    sf.registro_id,
                    sf.fecha_validacion,
                    sf.observaciones as comentario,
                    r.item,
                    r.jtWo,
                    sf.cantidad,
                    r.maquina,
                    m.nombre as nombre_maquina,
                    u.nombre as nombre_empleado,
                    'scrap' as tipo
                FROM scrap_final sf
                INNER JOIN registro r ON sf.registro_id = r.id
                LEFT JOIN maquinas m ON r.maquina = m.id
                LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
                WHERE sf.usuario_qa_id = ?)
                
                ORDER BY fecha_validacion DESC
                LIMIT ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param('ssi', $usuarioId, $usuarioId, $limit);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $historial = $result->fetch_all(MYSQLI_ASSOC);

            Logger::info('Historial de acciones QA obtenido', [
                'usuario_id' => $usuarioId,
                'total_registros' => count($historial)
            ]);

            return $historial;

        } catch (Exception $e) {
            Logger::error('Error al obtener historial de acciones QA', [
                'usuario_id' => $usuarioId,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    public function getEstadisticasUsuario($usuarioId, $fechaInicio = null, $fechaFin = null)
    {
        try {
            $whereClauseProd = "WHERE pf.usuario_id = ?";
            $whereClauseScrap = "WHERE sf.usuario_qa_id = ?";
            $params = [$usuarioId];
            $types = 's';

            if ($fechaInicio) {
                $whereClauseProd .= " AND DATE(pf.fecha_validacion) >= ?";
                $whereClauseScrap .= " AND DATE(sf.fecha_validacion) >= ?";
                $params[] = $fechaInicio;
                $types .= 's';
            }

            if ($fechaFin) {
                $whereClauseProd .= " AND DATE(pf.fecha_validacion) <= ?";
                $whereClauseScrap .= " AND DATE(sf.fecha_validacion) <= ?";
                $params[] = $fechaFin;
                $types .= 's';
            }

            // Estadísticas de producción
            $queryProd = "SELECT 
                            COUNT(*) as total,
                            SUM(r.cantidad_produccion) as cantidad_total
                          FROM produccion_final pf
                          INNER JOIN registro r ON pf.registro_id = r.id
                          {$whereClauseProd}";

            $stmt = $this->db->prepare($queryProd);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $resultProd = $stmt->get_result()->fetch_assoc();

            // Estadísticas de scrap (usando la cantidad de la tabla scrap_final)
            $queryScrap = "SELECT 
                             COUNT(*) as total,
                             SUM(sf.cantidad) as cantidad_total
                           FROM scrap_final sf
                           INNER JOIN registro r ON sf.registro_id = r.id
                           {$whereClauseScrap}";

            $stmt = $this->db->prepare($queryScrap);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $resultScrap = $stmt->get_result()->fetch_assoc();

            return [
                'produccion' => [
                    'total' => (int)$resultProd['total'],
                    'cantidad' => (float)($resultProd['cantidad_total'] ?? 0)
                ],
                'scrap' => [
                    'total' => (int)$resultScrap['total'],
                    'cantidad' => (float)($resultScrap['cantidad_total'] ?? 0)
                ],
                'total_general' => (int)$resultProd['total'] + (int)$resultScrap['total']
            ];

        } catch (Exception $e) {
            Logger::error('Error al obtener estadísticas de usuario', [
                'usuario_id' => $usuarioId,
                'error' => $e->getMessage()
            ]);

            return [
                'produccion' => ['total' => 0, 'cantidad' => 0],
                'scrap' => ['total' => 0, 'cantidad' => 0],
                'total_general' => 0
            ];
        }
    }
}