<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;


class SupervisorController extends Controller {
    private $jwt_secret;

    public function __construct()
    {
        global $jwt_secret;
        $this->jwt_secret = $jwt_secret;

        // Verificar autenticación
        if (!AuthHelper::isAuthenticated()) {
            header('Location: /timeControl/public/login');
            exit();
        }
    }

    public function index()
    {
        try {
            // Obtener el usuario actual
            $user = AuthHelper::getCurrentUser();

            // Renderizar la vista con las máquinas
            $this->view('supervisor/supervisor', [
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Manejar cualquier error
            error_log("Error en index de MaquinaController: " . $e->getMessage());
            header('Location: /timeControl/public/error');
            exit();
        }
    }
}
