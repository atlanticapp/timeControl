<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Usuario;
use App\Models\Control;

class RegistroController extends Controller
{

    public function index()
    {
        try {
            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $usuario = new Usuario();
            $control = new Control();

            
            // Obtiene data usuario desde el modelo
            $data = $usuario->findByCodigo($user->codigo_empleado);

            // Obtiene Nombre Maquina
            $nombre_maquina = $control->getNameMaquina($data['maquina_id']);

            // Obtiene Nombre Area
            $nombre_area = $control->getNameArea($user->area_id);

            // Obtener el ID del botón activo
            $active_button_id = $control->getActiveButton($user->codigo_empleado);

            $bad_copy = $control->getBadCopy($data['maquina_id']);

            // Historial Entrega Parcial
            $obtenerHistorial = $control->obtenerHistorial($data['item'], $data['jtWo'], $data['maquina_id'], $user->codigo_empleado);

            $preparacion = $control->getPreparacion($data['maquina_id']);

            // Renderizar la vista con la información del usuario
            $this->view('operador/control', [
                'usuario' => $user,
                'data' => $data,
                'maquina' => $nombre_maquina,
                'area' => $nombre_area,
                'bad_copy' => $bad_copy,
                'active_button_id' => $active_button_id,
                'historial' => $obtenerHistorial,
                'preparacion' => $preparacion
            ]);
        } catch (\Exception $e) {
            error_log("Error en datos_trabajo: " . $e->getMessage());
            header('Location: /timeControl/public/login?status=error');
        }
    }
}
