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
        $query = "SELECT descripcion FROM operacion WHERE maquina_id = ? AND tipo_operacion = 'Preparación'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $maquina_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $preparacion = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $preparacion[] = $row['descripcion'];
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
}
