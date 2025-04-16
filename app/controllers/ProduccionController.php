<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProduccionFinal;
use App\Helpers\AuthHelper;
use App\Helpers\Logger;

class ProduccionController extends Controller
{
    private $produccionModel;

    public function __construct()
    {
        $this->produccionModel = new ProduccionFinal();

        if (!AuthHelper::isAuthenticated()) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Debes iniciar sesión.');
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user || $user->tipo_usuario !== 'qa') {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Acceso denegado.');
        }
    }

    public function index()
    {
        $user = AuthHelper::getCurrentUser();

        try {
            $user = AuthHelper::getCurrentUser();

            $produccionGuardada = $this->produccionModel->getProduccionGuardada($user->codigo_empleado);

            $this->view('produccion/index', [
                'produccion' => $produccionGuardada,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Logger::error('Error en ProduccionController::index', [
                'error' => $e->getMessage()
            ]);
            $this->redirectWithMessage('/timeControl/public/dashboard', 'error', 'Error cargando la producción');
        }
    }


    private function redirectWithMessage($url, $status, $message)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;

        header("Location: $url");
        exit();
    }
}
