<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Control;

class RegistroController extends Controller
{

    public function index() {
        try {
            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            // Obtener el ID del botón activo
            $control = new Control();
            $active_button_id = $control->getActiveButton($user->codigo_empleado); // Obtiene el botón activo

            // Renderizar la vista con la información del usuario
            $this->view('operador/control', [
                'usuario' => $user,
                'active_button_id' => $active_button_id
            ]);
        } catch (\Exception $e) {
            error_log("Error en datos_trabajo: " . $e->getMessage());
            header('Location: /timeControl/public/login?status=error');
        }
    }
}
