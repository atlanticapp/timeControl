<?php

namespace App\Models;

use App\Core\Model;
use PDOException;


class Scrap extends Model
{
    public function guardarScrapFinal($datos)
    {

        $query = "INSERT INTO scrap_final (
                codigo_empleado, 
                maquina_id, 
                item, 
                jtwo, 
                cantidad, 
                aprobado_por, 
                fecha_aprobacion
            ) VALUES (
                :codigo_empleado, 
                :maquina_id, 
                :item, 
                :jtwo, 
                :cantidad, 
                :aprobado_por, 
                :fecha_aprobacion
            )";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param(':codigo_empleado', $datos['codigo_empleado']);
            $stmt->bind_param(':maquina_id', $datos['maquina_id']);
            $stmt->bind_param(':item', $datos['item']);
            $stmt->bind_param(':jtwo', $datos['jtwo']);
            $stmt->bind_param(':cantidad', $datos['cantidad']);
            $stmt->bind_param(':aprobado_por', $datos['aprobado_por']);
            $stmt->bind_param(':fecha_aprobacion', $datos['fecha_aprobacion']);

            $stmt->execute();
            $stmt->close();

            return true;
        }

        return false;
    }
}
