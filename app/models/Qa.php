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
        WHERE estado_validacion IN ('Pendiente', 'scrap_validado', 'produccion_validada')
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

                case 'scrap_validado':
                    if ($row['cantidad_produccion'] > 0) {
                        $entregas['entregas_produccion'][] = array_merge($datosComunes, [
                            'cantidad' => $row['cantidad_produccion'],
                            'tipo_registro' => 'produccion',
                            'estado_validacion' => 'scrap_validado'
                        ]);
                    }
                    break;

                case 'produccion_validada':
                    if ($row['cantidad_scrapt'] > 0) {
                        $entregas['entregas_scrap'][] = array_merge($datosComunes, [
                            'cantidad' => $row['cantidad_scrapt'],
                            'tipo_registro' => 'scrap',
                            'estado_validacion' => 'produccion_validada'
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

            // Si el estado actual es 'Pendiente', se puede validar
            if ($estado_actual === 'Pendiente') {
                $nuevo_estado = 'produccion_validada';

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
            } elseif ($estado_actual === 'scrap_validado') {
                // Si el estado es 'scrap_validado', actualizar a 'Validado'
                $nuevo_estado = 'Validado';

                // Actualizar el estado a 'Validado'
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
            } elseif ($estado_actual === 'produccion_validada') {
                // Si ya está validado como producción, no hacer nada
                $this->db->rollback();
                return false;
            } else {
                // Si el estado no es 'Pendiente', 'scrap_validado' ni 'produccion_validada', no se puede validar
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
                return false;
            }

            $registroOriginal = $resultVerificar->fetch_assoc();
            $stmtVerificar->close();

            // Verificar el estado actual y proceder según el tipo de entrega
            $estado_actual = $registroOriginal['estado_validacion'];

            // Si el estado es 'Pendiente', proceder con la validación de scrap
            if ($estado_actual === 'Pendiente') {
                $nuevo_estado = 'scrap_validado';

                // Actualizar el estado de validación a 'scrap_validado'
                $queryActualizar = "UPDATE registro SET estado_validacion = ?, validado_por = ?, fecha_validacion = NOW() WHERE id = ?";
                $stmtActualizar = $this->db->prepare($queryActualizar);
                $stmtActualizar->bind_param("sii", $nuevo_estado, $codigo_empleado_qa, $entregaId);
                $resultadoActualizacion = $stmtActualizar->execute();

                if (!$resultadoActualizacion || $stmtActualizar->affected_rows === 0) {
                    $stmtActualizar->close();
                    $this->db->rollback();
                    return false;
                }
                $stmtActualizar->close();
            } elseif ($estado_actual === 'scrap_validado') {
                // Si el estado ya es 'scrap_validado', no hacer nada
                $this->db->rollback();
                return false;
            } elseif ($estado_actual === 'produccion_validada') {
                // Si el estado es 'produccion_validada', actualizar a 'Validado' y guardar en scrap_final
                $nuevo_estado = 'Validado';
                $queryActualizar = "UPDATE registro SET estado_validacion = ?, validado_por = ?, fecha_validacion = NOW() WHERE id = ?";
                $stmtActualizar = $this->db->prepare($queryActualizar);
                $stmtActualizar->bind_param("sii", $nuevo_estado, $codigo_empleado_qa, $entregaId);
                $resultadoActualizacion = $stmtActualizar->execute();

                if (!$resultadoActualizacion || $stmtActualizar->affected_rows === 0) {
                    $stmtActualizar->close();
                    $this->db->rollback();
                    return false;
                }
                $stmtActualizar->close();
            } else {
                // Si el estado es 'Validado' o cualquier otro estado no permitido, no hacer nada
                $this->db->rollback();
                return false;
            }

            // Preparar los datos para la tabla scrap_final (se ejecuta en todos los casos)
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
                return false;
            }
            $stmtScrap->close();

            // Confirmar la transacción
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $this->db->rollback();
            error_log('Error en validación de scrap: ' . $e->getMessage());
            return false;
        }
    }



    // Enviar corrección al operador
    public function enviarCorreccion($codigo_empleado, $maquina_id, $item, $jtwo, $comentario)
    {
        $query = "
            UPDATE registro
            SET estado_validacion = 'Correccion',
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

    public function getEntregasValidadas($userqa)
    {
        $query = "SELECT 
            id,
            tipo_boton,
            codigo_empleado,
            maquina,
            item,
            jtWo,
            cantidad_produccion,
            cantidad_scrapt,
            fecha_validacion,
            validado_por,
            descripcion,
            estado_validacion
        FROM registro
        WHERE estado_validacion = 'Validado'
            AND validado_por = ?";

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
        $query = "
        SELECT 
            SUM(
                CASE 
                    WHEN estado_validacion = 'Pendiente' AND (cantidad_produccion > 0 OR cantidad_scrapt > 0) THEN 1
                    ELSE 0
                END
            ) AS total
        FROM registro
        WHERE estado_validacion IN ('Pendiente')
            AND area_id = ?
            AND (
                (tipo_boton = 'Producción' AND descripcion = 'Parcial') 
                OR (tipo_boton = 'final_produccion')
            )";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'] ?? 0;
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
        $stats = [
            'pendientes' => $this->getCountEntregasPendientes($area_id),
            'validadas' => $this->getCountEntregasValidadas($area_id)
        ];

        return $stats;
    }
}
