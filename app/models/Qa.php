<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Control;
use PDOException;

class Qa extends Model
{
    protected $table = 'registro';

    // Obtener entregas pendientes de validación
    public function getEntregasPendientes($area_id)
    {
        $query = "
        SELECT 
            id, 
            maquina, 
            jtWo, 
            item, 
            area_id,
            codigo_empleado,
            tipo_boton,
            descripcion,
            fecha_registro,
            cantidad_produccion,
            cantidad_scrapt,
            estado_validacion
        FROM registro
        WHERE estado_validacion IN ('Pendiente')
            AND area_id = ?
            AND (
                (tipo_boton = 'Producción' AND descripcion = 'Parcial') 
                OR (tipo_boton = 'final_produccion')
            )
        ORDER BY fecha_registro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $entregas = [
            'entregas_produccion' => [],
            'entregas_scrap' => []
        ];

        while ($row = $result->fetch_assoc()) {
            $datosComunes = $this->prepararDatosComunes($row);

            switch ($row['estado_validacion']) {
                case 'Pendiente':
                    if ($row['cantidad_produccion'] > 0) {
                        $entregas['entregas_produccion'][] = array_merge($datosComunes, [
                            'cantidad' => $row['cantidad_produccion'],
                            'tipo_registro' => 'produccion',
                            'estado_validacion' => 'Pendiente'
                        ]);
                    }

                    if ($row['cantidad_scrapt'] > 0) {
                        $entregas['entregas_scrap'][] = array_merge($datosComunes, [
                            'cantidad' => $row['cantidad_scrapt'],
                            'tipo_registro' => 'scrap',
                            'estado_validacion' => 'Pendiente'
                        ]);
                    }
                    break;
            }
        }

        return $entregas;
    }

    private function prepararDatosComunes($row)
    {
        $control = new Control();
        return [
            'id' => $row['id'],
            'maquina' => $row['maquina'],
            'jtWo' => $row['jtWo'],
            'item' => $row['item'],
            'area_id' => $row['area_id'],
            'codigo_empleado' => $row['codigo_empleado'],
            'tipo_boton' => $row['tipo_boton'],
            'descripcion' => $row['descripcion'],
            'fecha_registro' => $row['fecha_registro'],
            'nombre_empleado' => $this->getNombreEmpleado($row['codigo_empleado']),
            'nombre_maquina' => $control->getNameMaquina($row['maquina'])
        ];
    }

    // Validar entrega (aceptar)
    public function validarEntregaProduccion($codigo_empleado_qa, $entregaId)
    {
        try {
            // Iniciar transacción
            $this->db->begin_transaction();

            // Consultar el estado actual de la entrega
            $queryVerificar = "SELECT estado_validacion FROM registro WHERE id = ? FOR UPDATE";
            $stmtVerificar = $this->db->prepare($queryVerificar);
            $stmtVerificar->bind_param("i", $entregaId);
            $stmtVerificar->execute();
            $resultVerificar = $stmtVerificar->get_result();

            // Si no se encuentra el registro, cancelar la transacción
            if ($resultVerificar->num_rows === 0) {
                $stmtVerificar->close();
                $this->db->rollback();
                return false;
            }

            $registroOriginal = $resultVerificar->fetch_assoc();
            $stmtVerificar->close();

            // Obtener el estado actual de la entrega
            $estado_actual = $registroOriginal['estado_validacion'];

            // Procesar según el estado actual
            if ($estado_actual === 'Pendiente') {
                $nuevo_estado = 'Validado';

                // Actualizar el estado de validación
                $queryActualizar = "UPDATE registro SET estado_validacion = ?, validado_por = ?, fecha_validacion = NOW() WHERE id = ?";
                $stmtActualizar = $this->db->prepare($queryActualizar);
                $stmtActualizar->bind_param("sii", $nuevo_estado, $codigo_empleado_qa, $entregaId);
                $resultadoActualizacion = $stmtActualizar->execute();

                // Verificar si la actualización fue exitosa
                if (!$resultadoActualizacion || $stmtActualizar->affected_rows === 0) {
                    $stmtActualizar->close();
                    $this->db->rollback();
                    return false;
                }
                $stmtActualizar->close();
            } elseif ($estado_actual === 'Validado') {
                // Si ya está validado como producción o totalmente validado, no hacer nada
                $this->db->rollback();
                return false;
            } else {
                // Si el estado no es uno de los esperados, no se puede validar
                $this->db->rollback();
                return false;
            }

            // Confirmar la transacción si todo fue bien
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            // En caso de error, revertir la transacción
            $this->db->rollback();
            error_log('Error en validación de producción: ' . $e->getMessage());
            return false;
        }
    }
    public function validarEntregaScrap($codigo_empleado_qa, $entregaId, $comentario)
    {
        try {
            // Iniciar transacción
            $this->db->begin_transaction();

            // Consultar el estado actual de la entrega
            $queryVerificar = "SELECT * FROM registro WHERE id = ? FOR UPDATE";
            $stmtVerificar = $this->db->prepare($queryVerificar);
            $stmtVerificar->bind_param("i", $entregaId);
            $stmtVerificar->execute();
            $resultVerificar = $stmtVerificar->get_result();

            if ($resultVerificar->num_rows === 0) {
                $stmtVerificar->close();
                $this->db->rollback();
                return ["estado" => false, "mensaje" => "No se encontró el registro", "scrap_guardado" => false];
            }

            $registroOriginal = $resultVerificar->fetch_assoc();
            $stmtVerificar->close();

            // Verificar el estado actual y proceder según el tipo de entrega
            $estado_actual = $registroOriginal['estado_validacion'];
            $nuevo_estado = null;
            $validacion_actualizada = false;

            // Procesar según el estado actual
            if ($estado_actual === 'Pendiente') {
                $nuevo_estado = 'Validado';

                // Actualizar el estado de validación a 'scrap_validado'
                $queryActualizar = "UPDATE registro SET estado_validacion = ?, validado_por = ?, fecha_validacion = NOW() WHERE id = ?";
                $stmtActualizar = $this->db->prepare($queryActualizar);
                $stmtActualizar->bind_param("sii", $nuevo_estado, $codigo_empleado_qa, $entregaId);
                $resultadoActualizacion = $stmtActualizar->execute();

                if (!$resultadoActualizacion || $stmtActualizar->affected_rows === 0) {
                    $stmtActualizar->close();
                    $this->db->rollback();
                    return ["estado" => false, "mensaje" => "Error al actualizar el estado a scrap_validado", "scrap_guardado" => false];
                }
                $stmtActualizar->close();
                $validacion_actualizada = true;
            } elseif ($estado_actual === 'produccion_validada') {
                // Si el estado es 'produccion_validada', actualizar a 'Validado'
                $nuevo_estado = 'Validado';

                // Actualizar el estado a 'Validado'
                $queryActualizar = "UPDATE registro SET estado_validacion = ?, validado_por = ?, fecha_validacion = NOW() WHERE id = ?";
                $stmtActualizar = $this->db->prepare($queryActualizar);
                $stmtActualizar->bind_param("sii", $nuevo_estado, $codigo_empleado_qa, $entregaId);
                $resultadoActualizacion = $stmtActualizar->execute();

                if (!$resultadoActualizacion || $stmtActualizar->affected_rows === 0) {
                    $stmtActualizar->close();
                    $this->db->rollback();
                    return ["estado" => false, "mensaje" => "Error al actualizar a estado Validado", "scrap_guardado" => false];
                }
                $stmtActualizar->close();
                $validacion_actualizada = true;
            } elseif ($estado_actual === 'Validado') {
                // Si ya está totalmente validado, indicar que no se actualizó el estado pero continuar con el registro de scrap
                $validacion_actualizada = false;
                $nuevo_estado = $estado_actual;
            } else {
                // Si el estado no es uno de los esperados, no se puede validar
                $this->db->rollback();
                return ["estado" => false, "mensaje" => "Estado actual no permite validación de scrap: " . $estado_actual, "scrap_guardado" => false];
            }

            // Verificar que haya datos válidos para el scrap
            if (empty($registroOriginal['cantidad_scrapt']) || $registroOriginal['cantidad_scrapt'] <= 0) {
                // Si no hay cantidad de scrap válida y no se actualizó validación, cancelamos todo
                if ($validacion_actualizada) {
                    $this->db->rollback();
                    return ["estado" => false, "mensaje" => "No hay cantidad de scrap válida para registrar", "scrap_guardado" => false];
                } else {
                    // Si no se actualizó la validación, podemos confirmar la transacción vacía
                    $this->db->commit();
                    return ["estado" => true, "mensaje" => "No hay scrap para registrar", "scrap_guardado" => false, "nuevo_estado" => $nuevo_estado];
                }
            }

            // Preparar los datos para la tabla scrap_final
            $datosScrap = [
                'codigo_empleado' => $registroOriginal['codigo_empleado'],
                'maquina_id' => $registroOriginal['maquina'] ?? null,
                'item' => $registroOriginal['item'] ?? '',
                'jtwo' => $registroOriginal['jtWo'] ?? '',
                'cantidad' => $registroOriginal['cantidad_scrapt'] ?? 0,
                'aprobado_por' => $codigo_empleado_qa,
                'comentario' => $comentario
            ];

            // Insertar en la tabla scrap_final
            $queryScrap = "
                INSERT INTO scrap_final (codigo_empleado, maquina_id, item, jtwo, cantidad, aprobado_por, fecha_aprobacion, comentario) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";

            $stmtScrap = $this->db->prepare($queryScrap);
            $stmtScrap->bind_param(
                "iissiis",
                $datosScrap['codigo_empleado'],
                $datosScrap['maquina_id'],
                $datosScrap['item'],
                $datosScrap['jtwo'],
                $datosScrap['cantidad'],
                $datosScrap['aprobado_por'],
                $datosScrap['comentario']
            );
            $resultadoScrap = $stmtScrap->execute();

            if (!$resultadoScrap) {
                $stmtScrap->close();
                $this->db->rollback();
                return ["estado" => false, "mensaje" => "Error al guardar el registro de scrap en la base de datos", "scrap_guardado" => false];
            }
            $stmtScrap->close();

            // Confirmar la transacción
            $this->db->commit();

            // Determinar el mensaje de respuesta basado en si se actualizó la validación o solo se guardó el scrap
            $mensajeRespuesta = $validacion_actualizada ?
                "Validación de scrap exitosa y registro guardado correctamente" :
                "Registro de scrap guardado correctamente (validación no requerida)";

            return [
                "estado" => true,
                "mensaje" => $mensajeRespuesta,
                "scrap_guardado" => true,
                "nuevo_estado" => $nuevo_estado
            ];
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $this->db->rollback();
            error_log('Error en validación de scrap: ' . $e->getMessage());
            return ["estado" => false, "mensaje" => "Error en el proceso: " . $e->getMessage(), "scrap_guardado" => false];
        }
    }



    // Enviar corrección al operador
    public function enviarCorreccion($codigo_empleado, $maquina_id, $item, $jtwo, $comentario)
    {
        $query = "
            UPDATE registro
            SET estado_validacion = 'Corregir',
                comentario_qa = ?
            WHERE codigo_empleado = ?
            AND maquina = ?
            AND item = ?
            AND jtWo = ?
            AND tipo_boton = 'final_produccion'
            AND descripcion = 'Parcial'";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("siiss", $comentario, $codigo_empleado, $maquina_id, $item, $jtwo);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getEntregasValidadasProduccion($userqa)
    {
        $query = "SELECT *
    FROM registro
    WHERE estado_validacion = 'Validado'
        AND validado_por = ?
        AND cantidad_produccion > 0";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userqa);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    private function getNombreEmpleado($codigo_empleado)
    {
        $query = "SELECT nombre FROM users WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $codigo_empleado);
        $stmt->execute();
        $result = $stmt->get_result();
        $empleado = $result->fetch_assoc();
        return $empleado ? $empleado['nombre'] : 'Desconocido';
    }

    public function obtenerRegistroPorId($id)
    {
        try {
            $query = "SELECT * FROM registros WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Obtiene el conteo de entregas pendientes por área
     */
    public function getCountEntregasPendientes($area_id)
    {
        $query = "SELECT 
                SUM(CASE 
                    WHEN estado_validacion = 'Pendiente' AND cantidad_produccion > 0 THEN 1 
                    ELSE 0 
                END) AS total_produccion,
                SUM(CASE 
                    WHEN estado_validacion = 'Pendiente' AND cantidad_scrapt > 0 THEN 1 
                    ELSE 0 
                END) AS total_scrap,
                COUNT(*) AS total
            FROM registro
            WHERE estado_validacion = 'Pendiente'
            AND area_id = ?
            AND (
                (tipo_boton = 'Producción' AND descripcion = 'Parcial') 
                OR tipo_boton = 'final_produccion'
            )";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return [
            'total' => $row['total'] ?? 0,
            'total_scrap' => $row['total_scrap'] ?? 0,
            'total_produccion' => $row['total_produccion'] ?? 0
        ];
    }

    public function getCountEntregasEnProceso($area_id)
    {
        $query = "SELECT 
                SUM(CASE 
                        WHEN estado_validacion = 'Corregir' THEN 1 
                        ELSE 0 
                        END) AS total
                FROM registro
                WHERE area_id = ?";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$area_id]);
            $result = $stmt->fetch();  // Sin usar PDO::FETCH_ASSOC

            return $result['total'] ?? 0;  // Retorna el total o 0 si no hay datos
        } catch (PDOException $e) {
            // En caso de error, loguea el error y retorna 0
            error_log("Error en getCountEntregasEnProceso: " . $e->getMessage());
            return 0;
        }
    }




    /**
     * Obtiene el conteo de entregas validadas
     */
    public function getCountEntregasValidadas($area_id)
    {
        $query = "  SELECT 
            SUM(
            CASE 
                WHEN estado_validacion = 'validado' THEN 1
                ELSE 0
            END
        ) AS total
            FROM registro
            WHERE area_id = ?
        AND (
        (tipo_boton = 'final_produccion') 
        OR (tipo_boton = 'Producción' AND descripcion = 'Parcial')
    )";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'] ?? 0;
    }

    public function getEntregasProduccionValidadas($userqa)
    {
        $query = "SELECT 
        r.id,
        r.codigo_empleado,
        u.nombre AS nombre_empleado,
        r.maquina,
        m.nombre AS nombre_maquina,
        r.item,
        r.jtWo,
        r.cantidad_produccion,
        r.fecha_validacion,
        r.estado_validacion
    FROM registro r
    LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
    LEFT JOIN maquinas m ON r.maquina = m.id
    WHERE 
        r.estado_validacion = 'Validado' 
        AND r.validado_por = ?
        AND r.cantidad_produccion > 0
    ORDER BY 
        r.fecha_validacion DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userqa);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    /**
     * Obtiene estadísticas para el dashboard
     */
    public function getDashboardStats($area_id)
    {
        // Obtener las estadísticas de entregas pendientes
        $pendientes = $this->getCountEntregasPendientes($area_id);

        // Obtener las entregas en proceso
        $en_proceso = $this->getCountEntregasEnProceso($area_id);

        // Recopilar estadísticas para el dashboard
        $stats = [
            'pendientes' => $pendientes['total'],
            'scrap_pendientes' => $pendientes['total_scrap'],
            'produccion_pendiente' => $pendientes['total_produccion'],
            'validadas' => $this->getCountEntregasValidadas($area_id),
            'en_proceso' => $en_proceso // Número de entregas en proceso (estado 'Corregir')
        ];

        return $stats;
    }

    public function getValidacionesRecientes($userqa)
    {
        $query = "SELECT 
            r.id,
            r.codigo_empleado,
            u.nombre AS nombre_empleado,
            r.maquina,
            m.nombre AS nombre_maquina,
            r.item,
            r.jtWo,
            r.cantidad_produccion,
            r.fecha_validacion
        FROM registro r
        LEFT JOIN users u ON r.codigo_empleado = u.codigo_empleado
        LEFT JOIN maquinas m ON r.maquina = m.id
        WHERE 
            r.validado_por = ?
        ORDER BY 
            r.fecha_validacion DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userqa);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
