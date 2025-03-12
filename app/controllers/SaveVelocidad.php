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
            $this->validateRequestMethod('POST');

            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            $this->startSessionIfNeeded();

            $velocidad = new Velocidad();
            $control = new Control();
            $usuario = new Usuario();

            // Obtener información del usuario
            $data = $usuario->findByCodigo($user->codigo_empleado);
            if (!$data) {
                $this->redirectWithMessage('/timeControl/public/control', 'error', 'No se encontró información del usuario.');
            }

            // Validar que se está en producción
            $active_button_id = $control->getActiveButton($user->codigo_empleado);
            if ($active_button_id !== 'Producción') {
                $this->redirectWithMessage('/timeControl/public/control', 'error', 'No se puede registrar velocidad fuera de producción.');
            }

            // Verificar si se recibió la velocidad de producción
            if (!isset($_POST['velocidadProduccion'])) {
                $this->redirectWithMessage('/timeControl/public/control', 'error', 'No se recibió la velocidad de producción.');
            }

            $velocidad_produccion = $_POST['velocidadProduccion'];
            $maquina = $data['maquina_id'] ?? null;
            $area_id = $user->area_id ?? null;
            $jtWo = $data['jtWo'] ?? null;
            $item = $data['item'] ?? null;

            if (!$maquina || !$area_id || !$jtWo || !$item) {
                $this->redirectWithMessage('/timeControl/public/control', 'error', 'Datos insuficientes para registrar velocidad.');
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
                $this->redirectWithMessage('/timeControl/public/control', 'error', 'Error al guardar la velocidad de producción.');
            }

            $this->redirectWithMessage('/timeControl/public/control', 'success', 'Velocidad de producción registrada correctamente.');
            
        } catch (\Exception $e) {
            $this->redirectWithMessage('/timeControl/public/control', 'error', 'Error al guardar la velocidad de producción.');
        }
    }

    private function validateRequestMethod($method)
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            throw new \Exception("Método no permitido");
        }
    }

    /**
     * Método auxiliar para iniciar sesión si no está iniciada
     */
    private function startSessionIfNeeded()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Método auxiliar para establecer mensajes en sesión y redirigir
     */
    private function redirectWithMessage($url, $status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: $url");
        exit();
    }
}
