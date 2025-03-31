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
    public function createNotification($codigo_empleado, $mensaje, $tipo = 'info', $fecha = null)
    {
        $fecha = $fecha ?? date('Y-m-d H:i:s');

        $sql = "INSERT INTO {$this->table} (codigo_empleado, mensaje, tipo, fecha, estado) 
                VALUES (?, ?, ?, ?, 'pendiente')";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$codigo_empleado, $mensaje, $tipo, $fecha]);
        } catch (PDOException $e) {
            error_log("Error al insertar notificación: " . $e->getMessage());
            return false;
        }
    }

    // Obtener las notificaciones pendientes para un empleado
    public function getPendingNotifications($codigo_empleado)
    {
        $sql = "SELECT * FROM {$this->table} WHERE codigo_empleado = ? AND estado = 'pendiente' ORDER BY fecha DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$codigo_empleado]);

            $notifications = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $notifications[] = $row;
            }

            return $notifications;
        } catch (PDOException $e) {
            error_log("Error al obtener notificaciones pendientes: " . $e->getMessage());
            return [];
        }
    }

    // Contar las notificaciones pendientes para un empleado
    public function countPendingNotifications($codigo_empleado)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE codigo_empleado = ? AND estado = 'pendiente'";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$codigo_empleado]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return isset($result['count']) ? (int)$result['count'] : 0;
        } catch (PDOException $e) {
            error_log("Error al contar notificaciones pendientes: " . $e->getMessage());
            return 0;
        }
    }

    // Marcar una notificación como leída
    public function markNotificationsAsSeen($codigo_empleado)
    {
        $sql = "UPDATE {$this->table} SET estado = 'leído' 
                WHERE codigo_empleado = ? AND estado = 'pendiente'";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$codigo_empleado]);
        } catch (PDOException $e) {
            error_log("Error al actualizar notificaciones: " . $e->getMessage());
        }
    }


    public function getPendingNotificationsForUser($codigo_empleado)
    {
        $sql = "SELECT id, mensaje, tipo, fecha FROM {$this->table} 
                WHERE codigo_empleado = ? AND estado = 'pendiente' 
                ORDER BY fecha DESC";

        try {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $codigo_empleado); // Enlaza el parámetro
            $stmt->execute();

            $result = $stmt->get_result();
            $notificaciones = [];

            while ($row = $result->fetch_assoc()) {
                $notificaciones[] = $row;
            }

            $stmt->close();
            return $notificaciones;
        } catch (Exception $e) {
            error_log("Error al obtener notificaciones pendientes: " . $e->getMessage());
            return [];
        }
    }
}
