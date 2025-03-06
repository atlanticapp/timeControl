<?php

namespace App\Models;

use App\Core\Model;

class Control extends Model
{
    protected $table = 'users';

    
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
