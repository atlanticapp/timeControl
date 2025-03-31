<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    private $notification;

    public function __construct()
    {
        $this->notification = new Notificacion();

        if (!AuthHelper::isAuthenticated()) {
            header('Location: /timeControl/public/login');
            exit();
        }
    }

    // Crear una nueva notificación
    public function create()
    {
        $user = AuthHelper::getCurrentUser();

        if (!$user) {
            throw new \Exception("Usuario no autenticado");
        }

        $mensaje = $_POST['mensaje'] ?? '';
        $tipo = $_POST['tipo'] ?? 'info';  // info, alerta, éxito, error
        $fecha = $_POST['fecha'] ?? date('Y-m-d H:i:s');

        if (!$mensaje) {
            return $this->redirectWithMessage('/notificaciones', 'error', 'El mensaje es obligatorio.');
        }

        try {
            // Insertar la notificación
            $this->notification->createNotification($mensaje, $tipo, $fecha);

            // Redirigir con mensaje de éxito
            return $this->redirectWithMessage('/timeControl/public/control', 'success', 'Notificación creada con éxito.');
        } catch (\Exception $e) {
            error_log('Error al crear notificación: ' . $e->getMessage());

            return $this->redirectWithMessage('/timeControl/public/control', 'error', 'Ocurrió un error al crear la notificación.');
        }
    }



    public function checkNewNotifications()
    {
        $user = AuthHelper::getCurrentUser();

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }

        try {
            $notificationModel = new Notificacion();
            $notificaciones = $notificationModel->getPendingNotificationsForUser($user->codigo_empleado);

            if (!empty($notificaciones)) {
                // Marcar notificaciones como vistas después de mostrarlas
                $notificationModel->markNotificationsAsSeen($user->codigo_empleado);

                echo json_encode([
                    'success' => true,
                    'notificaciones' => $notificaciones
                ]);
            } else {
                echo json_encode(['success' => false, 'notificaciones' => []]);
            }
        } catch (\Exception $e) {
            error_log('Error al verificar notificaciones: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al consultar notificaciones']);
        }
    }


    private function redirectWithMessage($url, $status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: $url");
        exit();
    }
}
