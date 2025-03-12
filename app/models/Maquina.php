<?php

namespace App\Models;

use App\Core\Model;

class Maquina extends Model
{
    // Método para obtener las máquinas por área
    public function getMaquinasByArea($area_id)
    {
        $sql = "SELECT id, nombre FROM maquinas WHERE area_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $area_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $maquinas = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $maquinas[] = $row;
            }
        }

        return $maquinas;
    }

    public function actualizarMaquinaId($maquina_id, $codigo_empleado)
    {

        $update_query = "UPDATE users SET maquina_id = ? WHERE codigo_empleado = ?";

        // Preparar y ejecutar la consulta
        if ($stmt = $this->db->prepare($update_query)) {
            $stmt->bind_param("is", $maquina_id, $codigo_empleado); // 'i' para entero y 's' para string
            $stmt->execute();
            $stmt->close();

            return true; // Retorna verdadero si la actualización fue exitosa
        }

        return false; // Retorna falso si hubo un error
    }
}
