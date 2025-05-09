<?php

namespace App\Models;

use App\Core\Model;
use PDOException;
use PDO;
use Exception;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    // Insertar una notificación en la base de datos
    public function createNotification($area_id, $mensaje, $tipo = 'info', $fecha = null)
    {
        $fecha = $fecha ?? date('Y-m-d H:i:s'); 

        $sql = "INSERT INTO {$this->table} (area_id, mensaje, tipo, fecha, estado) 
                VALUES (?, ?, ?, ?, 'pendiente')";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$area_id, $mensaje, $tipo, $fecha]);
        } catch (PDOException $e) {
            error_log("❌ Error al insertar notificación: " . $e->getMessage());
            return false;
        }
    }

    public function getPendingNotifications($area_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE area_id = ? AND estado = 'pendiente' ORDER BY fecha DESC";
    
        try {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $this->db->error);
            }
    
            $stmt->bind_param("i", $area_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
    
            $stmt->close();
            return $notifications;
        } catch (Exception $e) {
            error_log("Error al obtener notificaciones pendientes: " . $e->getMessage());
            return [];
        }
    }

    // Marcar una notificación como leída
    public function markNotificationsAsSeen($area_id)
    {
        $sql = "UPDATE {$this->table} SET estado = 'leído' 
                WHERE area_id = ? AND estado = 'pendiente'";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$area_id]);
        } catch (PDOException $e) {
            error_log("❌ Error al actualizar notificaciones: " . $e->getMessage());
        }
    }
}
