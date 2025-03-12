<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Usuario;
use App\Models\Supervisor;
use App\Models\Maquina;

class SupervisorController extends Controller
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

        // Verificar tipo de usuario
        $user = AuthHelper::getCurrentUser();
        if ($user->tipo_usuario !== 'supervisor') {
            AuthHelper::logout();
        }
    }

    public function index()
    {
        try {
            // Obtener el usuario actual
            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            $usuario = new Usuario();
            $supervisor = new Supervisor();
            $maquina = new Maquina();

            $nombre_area = isset($user->area_id) ? $usuario->getNameArea($user->area_id) : "Área Desconocida";

            // Obtener filtros para el formulario
            $empleados = $supervisor->getEmpleadosPorArea($user->area_id);
            $botones = $supervisor->getTiposBotones();
            $maquinas = $supervisor->getMaquinasPorArea($user->area_id);

            // Renderizar la vista con los datos
            $this->view('supervisor/supervisor', [
                'user' => $user,
                'area' => $nombre_area,
                'empleados' => $empleados,
                'botones' => $botones,
                'maquinas' => $maquinas
            ]);
        } catch (\Exception $e) {
            // Manejar cualquier error
            error_log("Error en index de SupervisorController: " . $e->getMessage());
            header('Location: /timeControl/public/error');
            exit();
        }
    }

    public function getOperacionesAbiertas()
    {
        try {
            $this->validateRequestMethod('POST');

            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            // Obtener los parámetros
            $codigo_empleado = isset($_POST['codigo_empleado']) ? $_POST['codigo_empleado'] : '';
            $tipo_boton = isset($_POST['tipo_boton']) ? $_POST['tipo_boton'] : '';
            $maquina = isset($_POST['maquina']) ? $_POST['maquina'] : '';

            // Obtener las operaciones abiertas
            $supervisor = new Supervisor();
            $operaciones_abiertas = $supervisor->getOperacionesAbiertas($user->area_id, $codigo_empleado, $tipo_boton, $maquina);

            // Enviar la respuesta
            echo json_encode($operaciones_abiertas);
        } catch (\Exception $e) {
            // Manejar cualquier error
            error_log("Error en getOperacionesAbiertas de SupervisorController: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getProduccionDiaria()
    {
        try {
            $this->validateRequestMethod('POST');

            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            // Obtener los parámetros de filtro
            $item = isset($_POST['item']) ? $_POST['item'] : '';
            $jtWo = isset($_POST['jtWo']) ? $_POST['jtWo'] : '';

            // Obtener datos de producción
            $supervisor = new Supervisor();
            $produccion = $supervisor->getProduccionDiaria($user->area_id, $item, $jtWo);

            echo json_encode([
                'produccion' => $produccion['produccion_por_maquina_empleado'],
                'totalProduccion' => $produccion['totalProduccion'],
                'totalScrap' => $produccion['totalScrap']
            ]);
        } catch (\Exception $e) {
            error_log("Error en getProduccionDiaria de SupervisorController: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function validateRequestMethod($method)
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            throw new \Exception("Método no permitido");
        }
    }
}