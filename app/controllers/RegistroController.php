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

            if ($user->tipo_usuario !== 'operador') {
                header('Location: /timeControl/public/login?status=error=Tipo de usuario no es operador.');
            }

            $usuario = new Usuario();
            $control = new Control();

            // Obtener información del usuario
            $data = $usuario->findByCodigo($user->codigo_empleado);
            if (!$data) {
                throw new \Exception("No se encontró información del usuario.");
            }

            if (empty($data['jtWo']) || empty($data['item'])) {
                header('Location: /timeControl/public/datos_trabajo');
            }

            // Validar existencia de máquina y área antes de llamar a los métodos
            $nombre_maquina = isset($data['maquina_id']) ? $control->getNameMaquina($data['maquina_id']) : "No asignado";
            $nombre_area = isset($user->area_id) ? $control->getNameArea($user->area_id) : "No asignado";

            // Obtener datos adicionales
            $active_button_id = $control->getActiveButton($user->codigo_empleado);
            $bad_copy = isset($data['maquina_id']) ? $control->getBadCopy($data['maquina_id']) : null;
            $historial = $control->obtenerHistorial($data['item'] ?? '', $data['jtWo'] ?? '', $data['maquina_id'] ?? '', $user->codigo_empleado);
            $preparacion = isset($data['maquina_id']) ? $control->getPreparacion($data['maquina_id']) : [];

            // Renderizar la vista con la información del usuario
            $this->view('operador/control', [
                'data' => $data,
                'maquina' => $nombre_maquina,
                'area' => $nombre_area,
                'bad_copy' => $bad_copy,
                'active_button_id' => $active_button_id,
                'historial' => $historial,
                'preparacion' => $preparacion
            ]);
        } catch (\Exception $e) {
            header('Location: /timeControl/public/login?status=error');
        }
    }

    public function registrar()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception("Método no permitido");
            }

            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $control = new Control();
            $usuario = new Usuario();

            // Obtener información del usuario
            $data = $usuario->findByCodigo($user->codigo_empleado);
            if (!$data) {
                throw new \Exception("No se encontró información del usuario.");
            }

            // Recibir y validar datos del formulario
            $tipo_boton = $_POST['tipo_boton'] ?? null;
            $item = $data['item'];
            $jtWo = $data['jtWo'];
            $cantidad_scrapt = $_POST['finalScraptAmount'] ?? 'Unknown';
            $cantidad_produccion = $_POST['finalProductionValue'] ?? 'Unknown';
            $descripcion = $_POST['badCopy'] ?? 'Unknown';
            $codigo_empleado = $user->codigo_empleado;
            $maquina = $data['maquina_id'] ?? null;
            $area_id = $user->area_id ?? null;

            if (!$tipo_boton || !$item || !$jtWo || !$maquina) {
                throw new \Exception("Todos los campos son obligatorios.");
            }

            // Obtener la fecha y hora actual
            date_default_timezone_set("America/Santo_Domingo");
            $fecha_actual = date("Y-m-d H:i:s");

            // Cerrar registro previo si existe
            $control->updatePreviousRegistro($codigo_empleado, $fecha_actual);

            // Crear el array con los datos comunes para el registro
            $registroData = [
                'codigo_empleado' => $codigo_empleado,
                'item' => $item,
                'maquina' => $maquina,
                'area_id' => $area_id,
                'descripcion' => $descripcion,
                'jtWo' => $jtWo,
                'cantidad_scrapt' => $cantidad_scrapt,
                'cantidad_produccion' => $cantidad_produccion,
                'fecha_registro' => $fecha_actual,
                'fecha_fin' => null // Se cierra con `updatePreviousRegistro()`
            ];

            // Dependiendo del tipo de botón, manejar diferentes lógicas
            switch ($tipo_boton) {
                case 'Preparación':
                    // Lógica para preparación (si aplica)
                    $registroData['tipo_boton'] = 'Preparación';
                    break;

                case 'Producción':
                    // Lógica para producción (si aplica)
                    $registroData['tipo_boton'] = 'Producción';
                    break;

                case 'Contratiempos':
                    // Lógica para contratiempos
                    $registroData['tipo_boton'] = 'Contratiempos';
                    $registroData['descripcion'] = $_POST['badCopy'] ?? ''; // Ejemplo de dato extra para contratiempos
                    break;

                case 'Velocidad':
                    // Lógica para velocidad
                    $registroData['tipo_boton'] = 'Velocidad';
                    break;

                case 'final_produccion':
                    // Lógica para fin de producción
                    $registroData['tipo_boton'] = 'final_produccion';
                    $registroData['fecha_fin'] = $fecha_actual;
                    unset($_SESSION['data_entered']);
                    $control->resetUserData($codigo_empleado);
                    break;

                case 'Parcial':
                    // Lógica para entrega parcial
                    $registroData['tipo_boton'] = 'Producción';
                    $registroData['descripcion'] = 'Parcial';
                    $registroData['cantidad_produccion'] = $_POST['parcialProductionValue'] ?? 'Unknown';
                    $registroData['cantidad_scrapt'] = $_POST['parcialScraptAmount'] ?? 'Unknown';
                    break;

                default:
                    throw new \Exception("Tipo de botón no reconocido.");
            }

            // Insertar el registro en la base de datos
            $registroExitoso = $control->insertRegistro($registroData);

            if (!$registroExitoso) {
                throw new \Exception("Error al guardar el registro.");
            }

            // Llamada al método que actualiza el estado del botón del usuario
            $estadoBotonExitoso = $control->actualizarEstadoBoton($codigo_empleado, $registroData['tipo_boton']);
            if (!$estadoBotonExitoso) {
                throw new \Exception("Error al actualizar el estado del botón.");
            }

            header("Location: /timeControl/public/control?status=success");
            exit();
        } catch (\Exception $e) {
            error_log("Error en RegistroController@registrar: " . $e->getMessage());
            header("Location: /timeControl/public/control?status=error");
            exit();
        }
    }
}
