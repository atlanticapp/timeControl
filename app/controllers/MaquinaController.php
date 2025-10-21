<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Maquina;
use App\Models\CorreccionesOperador;
use App\Helpers\Logger;

class MaquinaController extends Controller
{
    private $jwt_secret;
    private $correccionesOperador;

    public function __construct()
    {
        global $jwt_secret;
        $this->jwt_secret = $jwt_secret;
        $this->correccionesOperador = new CorreccionesOperador();

        if (!AuthHelper::isAuthenticated()) {
            header('Location: /timeControl/public/login');
            exit();
        }
    }

    public function index()
    {
        try {
            $user = AuthHelper::getCurrentUser();

            if ($user->tipo_usuario !== 'operador') {
                $this->redirectWithMessage('/timeControl/public/login', 'error', 'Tipo de usuario no es operador.');
            }

            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (isset($_SESSION['data_entered']) && $_SESSION['data_entered'] === true) {
                header('Location: /timeControl/public/control');
                exit();
            }

            $maquinaModel = new Maquina();
            $maquinas = $maquinaModel->getMaquinasByArea($user->area_id);

           
            $correccionesPendientes = $this->correccionesOperador->getCorreccionesPendientesPorOperador($user->codigo_empleado);
            $mostrarCorrecciones = !empty($correccionesPendientes);

            $this->view('operador/maquina', [
                'user' => $user,
                'maquinas' => $maquinas,
                'correcciones_pendientes' => $correccionesPendientes,
                'mostrar_correcciones' => $mostrarCorrecciones
            ]);
        } catch (\Exception $e) {
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

            $jwt = AuthHelper::updateMaquinaId($maquinaId);
            if (!$jwt) {
                throw new \Exception("Error al actualizar la máquina seleccionada");
            }

            $user = AuthHelper::getCurrentUser();
            $maquinaModel = new Maquina();
            if (!$maquinaModel->actualizarMaquinaId($maquinaId, $user->codigo_empleado)) {
                error_log("Error al actualizar maquina_id en la base de datos.");
                header('Location: /timeControl/public/error');
                exit();
            }

            
            $correccionesPendientes = $this->correccionesOperador->getCorreccionesPendientesPorOperador($user->codigo_empleado);
            if (!empty($correccionesPendientes)) {
                $this->redirectWithMessage('/timeControl/public/datos_trabajo', 'info', 'Tienes correcciones pendientes que requieren tu atención.');
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: $url");
        exit();
    }
}