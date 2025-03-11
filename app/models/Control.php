<?php

namespace App\Models;

use App\Core\Model;

class Control extends Model
{
    protected $table = 'users';

    public function obtenerHistorial($item, $jtWo, $maquina_id, $codigo_empleado)
    {
        $query = "
            SELECT fecha_registro, cantidad_produccion, cantidad_scrapt
            FROM registro
            WHERE descripcion = 'Parcial'
            AND item = ?
            AND jtWo = ?
            AND maquina = ?
            AND codigo_empleado = ?
            AND fecha_registro >= DATE_SUB(NOW(), INTERVAL 10 HOUR)
            ORDER BY fecha_registro DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssii", $item, $jtWo, $maquina_id, $codigo_empleado);
        $stmt->execute();
        $result = $stmt->get_result();

        $historial = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $historial[] = $row;
            }
        }

        return $historial;
    }

    public function getNameMaquina($maquina_id)
    {
        $query_maquina = "SELECT nombre FROM maquinas WHERE id = ?";
        $stmt_maquina = $this->db->prepare($query_maquina);
        $stmt_maquina->bind_param("i", $maquina_id);
        $stmt_maquina->execute();
        $stmt_maquina->bind_result($nombreMaquina);
        $stmt_maquina->fetch();
        $stmt_maquina->close();

        return $nombreMaquina;
    }

    public function getNameArea($area_id)
    {
        $areaSql = "SELECT nombre FROM area WHERE id = ?";
        $areaStmt = $this->db->prepare($areaSql);
        $areaStmt->bind_param("i", $area_id);
        $areaStmt->execute();
        $areaStmt->bind_result($nombreArea);
        $areaStmt->fetch();
        $areaStmt->close();

        return $nombreArea;
    }

    public function getBadCopy($maquina_id)
    {
        $query = "SELECT * FROM operacion WHERE maquina_id = ? AND tipo_operacion = 'Contratiempos'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $maquina_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $badCopy = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $badCopy[] = $row;
            }
        }

        return $badCopy;
    }

    public function getPreparacion($maquina_id)
    {
        $query = "SELECT * FROM operacion WHERE maquina_id = ? AND tipo_operacion = 'Preparación'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $maquina_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $preparacion = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $preparacion[] = $row;
            }
        }

        return $preparacion;
    }


    public function getActiveButton($codigo_empleado)
    {
        $query_active_button = "SELECT active_button_id FROM {$this->table} WHERE codigo_empleado = ?";
        $stmt_active_button = $this->db->prepare($query_active_button);
        $stmt_active_button->bind_param("s", $codigo_empleado);
        $stmt_active_button->execute();
        $stmt_active_button->bind_result($active_button_id);
        $stmt_active_button->fetch();
        $stmt_active_button->close();

        return $active_button_id;
    }

    public function insertRegistro($registroData)
    {
        // Extraer los valores del arreglo $registroData
        $tipo_boton = $registroData['tipo_boton'];
        $codigo_empleado = $registroData['codigo_empleado'];
        $item = $registroData['item'];
        $maquina = $registroData['maquina'];
        $area_id = $registroData['area_id'];
        $descripcion = $registroData['descripcion'];
        $jtWo = $registroData['jtWo'];
        $cantidad_scrapt = $registroData['cantidad_scrapt'];
        $cantidad_produccion = $registroData['cantidad_produccion'];
        $fecha_registro = $registroData['fecha_registro'];
        $fecha_fin = $registroData['fecha_fin'];

        // Sentencia SQL para insertar en la base de datos
        $sql = "INSERT INTO registro (tipo_boton, codigo_empleado, item, maquina, area_id, descripcion, jtWo, cantidad_scrapt, cantidad_produccion, fecha_registro, fecha_fin) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar la consulta
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "sssssssssss",
            $tipo_boton,
            $codigo_empleado,
            $item,
            $maquina,
            $area_id,
            $descripcion,
            $jtWo,
            $cantidad_scrapt,
            $cantidad_produccion,
            $fecha_registro,
            $fecha_fin
        );

        // Ejecutar la consulta y devolver el resultado
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }


    // Función para actualizar el estado del botón
    public function actualizarEstadoBoton($codigo_empleado, $nuevo_tipo_boton)
    {
        $sql = "UPDATE users SET active_button_id = ? WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $nuevo_tipo_boton, $codigo_empleado);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Función para actualizar el registro anterior
    public function updatePreviousRegistro($codigo_empleado, $fecha_actual)
    {
        $sql = "UPDATE registro SET fecha_fin = ? WHERE codigo_empleado = ? AND fecha_fin IS NULL ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $fecha_actual, $codigo_empleado);
        $stmt->execute();
        $stmt->close();
    }

    // Función para actualizar datos de usuario cuando la producción finaliza
    public function resetUserData($codigo_empleado)
    {
        $sql = "UPDATE users SET jtWo = NULL, item = NULL, active_button_id = 'defaultButtonId' WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $codigo_empleado);
        $stmt->execute();
        $stmt->close();
    }

    public function insertEsperaTrabajo($registrodata)
    {
        $tipo_boton = $registrodata['tipo_boton'];
        $codigo_empleado = $registrodata['codigo_empleado'];
        $maquina = $registrodata['maquina'];
        $area_id = $registrodata['area_id'];
        $fecha_registro = $registrodata['fecha_registro'];

        $sql = "INSERT INTO registro (tipo_boton, codigo_empleado, maquina, area_id, fecha_registro) 
                    VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "sssss",
            $tipo_boton,
            $codigo_empleado,
            $maquina,
            $area_id,
            $fecha_registro,
        );

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
}
