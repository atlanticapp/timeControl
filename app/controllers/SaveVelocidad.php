<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Usuario;
use App\Models\Control;
use App\Models\Velocidad;

class saveVelocidad extends Controller
{
    public function saveVelocidad()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception("Método no permitido");
            }

            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $velocidad = new Velocidad();
            $control = new Control();
            $usuario = new Usuario();

            // Obtener información del usuario
            $data = $usuario->findByCodigo($user->codigo_empleado);
            if (!$data) {
                throw new \Exception("No se encontró información del usuario.");
            }

            // Validar que se está en producción
            $active_button_id = $control->getActiveButton($user->codigo_empleado);
            if ($active_button_id !== 'Producción') {
                throw new \Exception("No se está en producción.");
            }

            // Verificar si se recibió la velocidad de producción
            if (!isset($_POST['velocidadProduccion'])) {
                throw new \Exception("No se recibió la velocidad de producción.");
            }

            $velocidad_produccion = $_POST['velocidadProduccion'];
            $maquina = $data['maquina_id'] ?? null;
            $area_id = $user->area_id ?? null;
            $jtWo = $data['jtWo'] ?? null;
            $item = $data['item'] ?? null;

            if (!$maquina || !$area_id || !$jtWo || !$item) {
                throw new \Exception("Datos insuficientes para registrar velocidad.");
            }

            // Insertar en la base de datos
            $registroExitoso = $velocidad->saveVelocidad([
                'maquina' => $maquina,
                'area_id' => $area_id,
                'jtWo' => $jtWo,
                'item' => $item,
                'velocidad_produccion' => $velocidad_produccion
            ]);

            if (!$registroExitoso) {
                throw new \Exception("Error al guardar la velocidad de producción.");
            }

            header("Location: /timeControl/public/control?status=success");
            exit();
        } catch (\Exception $e) {
            error_log("Error en RegistroController@saveVelocidad: " . $e->getMessage());
            header("Location: /timeControl/public/control?status=error");
            exit();
        }
    }
}
