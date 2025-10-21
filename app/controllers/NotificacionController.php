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

    public function checkNewNotifications()
    {
        $user = AuthHelper::getCurrentUser();

        if (!$user) {
            $this->jsonResponse(false, 'Usuario no autenticado');
            return;
        }

        try {
            $notificationModel = new Notificacion();
            $notificaciones = $notificationModel->getPendingNotifications($user->area_id);

            if (!empty($notificaciones)) {
                
                $notificationModel->markNotificationsAsSeen($user->area_id);
            }

            
            $this->jsonResponse(true, 'Consulta exitosa', ['notificaciones' => $notificaciones]);
        } catch (\Exception $e) {
            error_log('❌ Error al verificar notificaciones: ' . $e->getMessage());
            $this->jsonResponse(false, 'Error al consultar notificaciones');
        }
    }

    private function jsonResponse($success, $message, $data = [])
    {
        echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    }



    private function redirectWithMessage($url, $status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: $url");
        exit();
    }
}
