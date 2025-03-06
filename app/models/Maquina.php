<?php
namespace App\Models;

use App\Core\Model;

class Maquina extends Model {
    
    // Método para obtener las máquinas por área
    public function getMaquinasByArea($area_id) {
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
    
}
