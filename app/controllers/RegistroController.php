<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Usuario;
use App\Models\Control;
use App\Models\Notificacion;

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
                $this->redirectWithMessage('/timeControl/public/login', 'error', 'Tipo de usuario no es operador.');
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
            $nombre_area = isset($user->area_id) ? $usuario->getNameArea($user->area_id) : "No asignado";

            $maquinaSeleccionada = $user->maquina_id;
            // Obtener datos adicionales
            $active_button_id = $control->getActiveButton($user->codigo_empleado);
            $bad_copy = isset($data['maquina_id']) ? $control->getBadCopy($data['maquina_id']) : null;
            $historial = $control->obtenerHistorial($data['item'] ?? '', $data['jtWo'] ?? '', $data['maquina_id'] ?? '', $user->codigo_empleado);
            $preparacion = isset($data['maquina_id']) ? $control->getPreparacion($data['maquina_id']) : [];

            if ($maquinaSeleccionada) {
                $correccionesModel = new \App\Models\CorreccionesOperador();
                $correccionesPendientes = $correccionesModel->getCorreccionesPendientes($user->maquina_id);

                // Si hay correcciones pendientes, mostrar botón/modal en datos_trabajo
                if (!empty($correccionesPendientes)) {

                    $this->view('operador/control', [
                        'data' => $data,
                        'maquina' => $nombre_maquina,
                        'area' => $nombre_area,
                        'bad_copy' => $bad_copy,
                        'active_button_id' => $active_button_id,
                        'historial' => $historial,
                        'preparacion' => $preparacion,
                        'correcciones_pendientes' => $correccionesPendientes,
                        'mostrar_correcciones' => true
                    ]);
                    return;
                }
            }


            // Renderizar la vista con la información del usuario
            $this->view('operador/control', [
                'data' => $data,
                'maquina' => $nombre_maquina,
                'area' => $nombre_area,
                'bad_copy' => $bad_copy,
                'active_button_id' => $active_button_id,
                'historial' => $historial,
                'preparacion' => $preparacion,
                'mostrar_correcciones' => false
            ]);
        } catch (\Exception $e) {
            header('Location: /timeControl/public/login?status=error');
        }
    }

    public function registrar()
    {
        try {
            // 1. Validación inicial
            $this->validateRequestMethod('POST');
            $user = AuthHelper::getCurrentUser();
            if (!$user) {
                throw new \Exception("Usuario no autenticado");
            }

            $this->startSessionIfNeeded();

            // 2. Cargar modelos y datos básicos
            $controlModel = new Control();
            $usuarioModel = new Usuario();
            $userData = $usuarioModel->findByCodigo($user->codigo_empleado);

            if (!$userData) {
                $this->redirectWithMessage('/timeControl/public/control', 'error', 'No se encontró información del usuario.');
            }

            // 3. Validar campos requeridos
            $requiredFields = [
                'tipo_boton' => $_POST['tipo_boton'] ?? null,
                'item' => $userData['item'] ?? null,
                'jtWo' => $userData['jtWo'] ?? null,
                'po' => $userData['po'] ?? null,
                'cliente' => $userData['cliente'] ?? null,
                'maquina' => $userData['maquina_id'] ?? null
            ];

            foreach ($requiredFields as $field => $value) {
                if (empty($value)) {
                    $this->redirectWithMessage('/timeControl/public/control', 'error', "Campo requerido faltante: {$field}");
                }
            }

            // 4. Configurar zona horaria y fecha
            date_default_timezone_set("America/Santo_Domingo");
            $currentDateTime = date("Y-m-d H:i:s");

            // 5. Cerrar registro previo si existe
            $controlModel->updatePreviousRegistro($user->codigo_empleado, $currentDateTime);

            // 6. Preparar datos base del registro
            $registroData = [
                'tipo_boton' => $requiredFields['tipo_boton'],
                'codigo_empleado' => $user->codigo_empleado,
                'item' => $requiredFields['item'],
                'maquina' => $requiredFields['maquina'],
                'area_id' => $user->area_id,
                'jtWo' => $requiredFields['jtWo'],
                'po' => $requiredFields['po'],
                'cliente' => $requiredFields['cliente'],
                'descripcion' => $_POST['badCopy'] ?? 'Unknown',
                'fecha_registro' => $currentDateTime,
                'fecha_fin' => null,
                'cantidad_produccion' => $this->parseQuantity($_POST['finalProductionValue'] ?? $_POST['parcialProductionValue'] ?? 0),
                'cantidad_scrapt' => $this->parseQuantity($_POST['finalScraptAmount'] ?? $_POST['parcialScraptAmount'] ?? 0)
            ];

            // Variable para determinar si se debe enviar notificación
            $shouldNotify = false;

            // 7. Manejo específico por tipo de botón
            switch ($registroData['tipo_boton']) {
                case 'final_produccion':
                    $registroData['fecha_fin'] = $currentDateTime;
                    unset($_SESSION['data_entered']);
                    $controlModel->resetUserData($user->codigo_empleado);
                    // Notificar si hay producción o scrap
                    $shouldNotify = ($registroData['cantidad_produccion'] > 0 || $registroData['cantidad_scrapt'] > 0);
                    break;

                case 'Parcial':
                    $registroData['fecha_fin'] = $currentDateTime;
                    $registroData['tipo_boton'] = 'Producción';
                    $registroData['descripcion'] = 'Parcial';
                    // Notificar si hay producción o scrap
                    $shouldNotify = ($registroData['cantidad_produccion'] > 0 || $registroData['cantidad_scrapt'] > 0);
                    break;

                case 'Contratiempos':
                    $registroData['descripcion'] = $_POST['badCopy'] ?? 'Incidente reportado';
                    $registroData['cantidad_produccion'] = 0;
                    $registroData['cantidad_scrapt'] = 0;
                    // No notificar para contratiempos
                    $shouldNotify = false;
                    break;

                case 'Velocidad':
                case 'Preparación':
                    $registroData['cantidad_produccion'] = 0;
                    $registroData['cantidad_scrapt'] = 0;
                    // No notificar para velocidad o preparación
                    $shouldNotify = false;
                    break;
            }

            // 8. Procesamiento del registro con transacción
            try {
                // Insertar registro
                $registroExitoso = $controlModel->insertRegistro($registroData);
                if (!$registroExitoso) {
                    throw new \Exception("Error al guardar el registro");
                }

                // Actualizar estado del botón
                $estadoBotonExitoso = $controlModel->actualizarEstadoBoton(
                    $user->codigo_empleado,
                    $registroData['tipo_boton']
                );
                if (!$estadoBotonExitoso) {
                    throw new \Exception("Error al actualizar el estado del botón");
                }

                // 9. Notificar a los QA del área solo si corresponde
                if ($shouldNotify) {
                    $this->sendNotificationToQA(
                        $user->area_id,
                        "Nueva entrega de producción o scrap registrada",
                        "Se ha registrado una nueva cantidad de producción o scrap en la máquina {$registroData['maquina']}."
                    );
                }

                $this->redirectWithMessage('/timeControl/public/control', 'success', 'Registro guardado correctamente.');
            } catch (\Exception $e) {
                throw $e;
            }
        } catch (\Exception $e) {
            error_log("Error en RegistroController::registrar - " . $e->getMessage());
            $this->redirectWithMessage(
                '/timeControl/public/error',
                'error',
                'Ocurrió un error inesperado: ' . $e->getMessage()
            );
        }
    }
    // Método para enviar la notificación a los QA del área
    private function sendNotificationToQA($areaId, $title, $message)
    {
        $notificationModel = new Notificacion();
        $notificationModel->createNotification($areaId, $message, 'info');
    }






    /**
     * Método auxiliar para parsear cantidades numéricas
     */
    private function parseQuantity($value)
    {
        if ($value === 'Unknown' || $value === '' || $value === null) {
            return 0;
        }
        return is_numeric($value) ? (float)$value : 0;
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
