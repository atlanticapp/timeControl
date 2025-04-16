<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Logger;
use App\Helpers\AuthHelper;
use App\Models\DestinoDestruccion;

class DestinoDestruccionController extends Controller
{
    private $destinoModel;

    public function __construct()
    {
        $this->destinoModel = new DestinoDestruccion();

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
            $this->view('qa/destinos/destruccion', [
                'data' => [
                    'destinos' => $this->destinoModel->getDestinosDestruccion($user->area_id, $user->codigo_empleado)
                ]
            ]);
        } catch (\Exception $e) {
            Logger::error('Error al cargar vista de destinos destrucción', [
                'error' => $e->getMessage()
            ]);
            $this->redirectWithMessage('/timeControl/public/dashboard', 'error', 'Error al cargar destinos.');
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