<?php

namespace App\Models;

use App\Core\Model;

class Data extends Model
{
    protected $table = 'users';

    public function updateJtWoItemPoCliente($codigo_empleado, $jtWo, $item, $po, $cliente)
    {
        $sql_update = "UPDATE {$this->table} SET jtWo = ?, item = ?, po = ?, cliente = ? WHERE codigo_empleado = ?";
        $stmt_update = $this->db->prepare($sql_update);
        $stmt_update->bind_param("sssss", $jtWo, $item, $po, $cliente, $codigo_empleado);
        $stmt_update->execute();
        $stmt_update->close();
    }

    public function updateFinEspera($codigo_empleado)
    {
        date_default_timezone_set("America/Santo_Domingo");
        $fecha_actual = date("Y-m-d H:i:s");

        $sql_update = "UPDATE registro SET fecha_fin = ? WHERE codigo_empleado = ? AND tipo_boton = 'Espera_trabajo' AND fecha_fin IS NULL ORDER BY id DESC LIMIT 1";
        $stmt_update = $this->db->prepare($sql_update);
        $stmt_update->bind_param("ss", $fecha_actual, $codigo_empleado);
        $stmt_update->execute();
        $stmt_update->close();
    }
}
