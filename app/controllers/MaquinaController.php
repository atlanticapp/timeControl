<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Maquina;
use App\Helpers\Logger;

class MaquinaController extends Controller
{
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

            if ($user->tipo_usuario !== 'operador') {
                $this->redirectWithMessage('/timeControl/public/login', 'error', 'Tipo de usuario no es operador.');
            }

            // Verificar si ya ingresó datos
            session_start();
            if (isset($_SESSION['data_entered']) && $_SESSION['data_entered'] === true) {
                header('Location: /timeControl/public/control');
                exit();
            }

            // Obtener lista de máquinas para el área del usuario
            $maquinaModel = new Maquina();
            $maquinas = $maquinaModel->getMaquinasByArea($user->area_id);

            // Renderizar la vista con las máquinas
            $this->view('operador/maquina', [
                'user' => $user,
                'maquinas' => $maquinas
            ]);
        } catch (\Exception $e) {
            // Manejar cualquier error
            $this->redirectWithMessage('/timeControl/public/error', 'error', 'Error al cargar la vista de máquinas.');
        }
    }

    public function seleccionarMaquina()
    {
        try {
            $maquinaId = filter_input(INPUT_POST, 'maquina_id', FILTER_VALIDATE_INT);
            if (!$maquinaId) {
                throw new \Exception("ID de máquina no válido");
            }

            // Actualizar la máquina en el JWT
            $jwt = AuthHelper::updateMaquinaId($maquinaId);
            if (!$jwt) {
                throw new \Exception("Error al actualizar la máquina seleccionada");
            }

            // Verificar si hay correcciones pendientes
            $user = AuthHelper::getCurrentUser();
            $correccionesModel = new \App\Models\CorreccionesOperador();
            $correccionesPendientes = $correccionesModel->getCorreccionesPendientes(
                $user->codigo_empleado,
                $maquinaId
            );

            $maquinaModel = new Maquina();
            if (!$maquinaModel->actualizarMaquinaId($maquinaId, $user->codigo_empleado)) {
                error_log("Error al actualizar maquina_id en la base de datos.");
                header('Location: /timeControl/public/error');
                exit();
            }

            if (!empty($correccionesPendientes)) {
                header('Location: /timeControl/public/correcciones');
                exit();
            }

            header('Location: /timeControl/public/datos_trabajo');
            exit();
        } catch (\Exception $e) {
            Logger::error('Error en selección de máquina', [
                'error' => $e->getMessage()
            ]);
            $this->redirectWithMessage(
                '/timeControl/public/maquinas',
                'error',
                'Error al seleccionar la máquina: ' . $e->getMessage()
            );
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
