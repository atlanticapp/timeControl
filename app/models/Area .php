<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class Area extends Model
{
    protected $table = 'area';

    public function updateLastNotificationTime($areaId)
    {
        $sql = "UPDATE {$this->table} SET ultima_notificacion = NOW() WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$areaId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar la última notificación del área: " . $e->getMessage());
            return false;
        }
    }
}
