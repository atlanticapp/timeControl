<?php

namespace App\Models;

use App\Core\Model;

class Velocidad extends Model
{
    protected $table = 'velocidad';

    public function saveVelocidad($data)
    {
        $sql = "INSERT INTO {$this->table} (maquina, area_id, jtWo, item, velocidad_produccion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("ssssd", 
            $data['maquina'], 
            $data['area_id'], 
            $data['jtWo'], 
            $data['item'], 
            $data['velocidad_produccion']
        );
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
}
