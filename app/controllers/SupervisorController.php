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
            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            $usuario = new Usuario();
            $supervisor = new Supervisor();

            $area = $usuario->getNameArea($user->area_id ?? null) ?? "Área Desconocida";

            // Obtener filtros
            $empleados = $supervisor->getEmpleadosPorArea($user->area_id);
            $botones = $supervisor->getTiposBotones();
            $maquinas = $supervisor->getMaquinasPorArea($user->area_id);

            // Obtener parámetros de filtro
            $codigo_empleado = $_POST['codigo_empleado'] ?? '';
            $tipo_boton = $_POST['tipo_boton'] ?? '';
            $maquina = $_POST['maquina'] ?? '';
            $item = $_POST['item'] ?? '';
            $jtWo = $_POST['jtWo'] ?? '';

            // Obtener datos
            $operaciones_abiertas = $supervisor->getOperacionesAbiertas($user->area_id, $codigo_empleado, $tipo_boton, $maquina);
            $produccion = $supervisor->getProduccionDiaria($user->area_id, $item, $jtWo);

            // Renderizar vista
            $this->view('supervisor/supervisor', compact('user', 'area', 'empleados', 'botones', 'maquinas', 'operaciones_abiertas', 'produccion'));
        } catch (\Exception $e) {
            error_log("Error en index de SupervisorController: " . $e->getMessage());
            header('Location: /timeControl/public/error');
            exit();
        }
    }
}
