<?php
namespace App\Models;

use App\Core\Model;

class Usuario extends Model {
    
    protected $table = 'users';
    
    public function findByCodigo($codigo) {
        $sql = "SELECT id, nombre, codigo_empleado, tipo_usuario, area_id, item, jtWo, password FROM {$this->table} WHERE codigo_empleado = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (nombre, codigo_empleado, password, tipo_usuario, area_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", 
            $data['nombre'], 
            $data['codigo_empleado'], 
            $data['password'], 
            $data['tipo_usuario'], 
            $data['area_id']
        );
        
        return $stmt->execute();
    }
    
    public function getAllAreas() {
        $sql = "SELECT id, nombre FROM area";
        $result = $this->db->query($sql);
        $areas = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $areas[] = $row;
            }
        }
        
        return $areas;
    }
}