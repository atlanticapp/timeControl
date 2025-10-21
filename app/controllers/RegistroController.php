<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Logger;
use App\Models\Usuario;
use App\Models\Control;

class RegistroController extends Controller
{
    public function index()
    {
        try {
            
            AuthHelper::requireRole('operador');
            $user = AuthHelper::getCurrentUser();
            
            
            Logger::info('Acceso a control de registro', [
                'codigo_empleado' => $user->codigo_empleado,
                'session_id' => session_id()
            ]);

            $usuario = new Usuario();
            $control = new Control();

            $data = $usuario->findByCodigo($user->codigo_empleado);
            if (!$data) {
                throw new \Exception("No se encontró información del usuario.");
            }

            // Verificar que tenga datos de trabajo
            if (empty($data['jtWo']) || empty($data['item'])) {
                Logger::warning('Usuario sin datos de trabajo', [
                    'codigo_empleado' => $user->codigo_empleado
                ]);
                header('Location: /timeControl/public/datos_trabajo');
                exit();
            }

            // Obtener información de maquina y area
            $nombre_maquina = isset($data['maquina_id']) ? 
                $control->getNameMaquina($data['maquina_id']) : "No asignado";
            $nombre_area = isset($user->area_id) ? 
                $usuario->getNameArea($user->area_id) : "No asignado";

            // Obtener datos adicionales
            $active_button_id = $control->getActiveButton($user->codigo_empleado);
            $bad_copy = isset($data['maquina_id']) ? 
                $control->getBadCopy($data['maquina_id']) : null;
            
            $historial = $control->obtenerHistorial(
                $data['item'] ?? '', 
                $data['jtWo'] ?? '', 
                $data['maquina_id'] ?? '', 
                $user->codigo_empleado
            );
            
            $preparacion = isset($data['maquina_id']) ? 
                $control->getPreparacion($data['maquina_id']) : [];

            // Obtener correcciones pendientes
            $correccionesModel = new \App\Models\CorreccionesOperador();
            $correccionesPendientes = $correccionesModel->getCorreccionesPendientesPorOperador($user->codigo_empleado);
            
            $mostrar_correcciones = !empty($correccionesPendientes);

            // Log de renderizado
            Logger::info('Renderizando vista de control', [
                'codigo_empleado' => $user->codigo_empleado,
                'maquina' => $nombre_maquina,
                'correcciones_pendientes' => count($correccionesPendientes)
            ]);

            // Renderizar la vista
            $this->view('operador/control', [
                'data' => $data,
                'maquina' => $nombre_maquina,
                'area' => $nombre_area,
                'bad_copy' => $bad_copy,
                'active_button_id' => $active_button_id,
                'historial' => $historial,
                'preparacion' => $preparacion,
                'correcciones_pendientes' => $correccionesPendientes,
                'mostrar_correcciones' => $mostrar_correcciones
            ]);
            
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'RegistroController',
                'method' => 'index',
                'user' => $user->codigo_empleado ?? 'unknown'
            ]);
            header('Location: /timeControl/public/login?status=error');
            exit();
        }
    }

    public function registrar()
    {
        try {
            
            AuthHelper::requireRole('operador');
            $user = AuthHelper::getCurrentUser();
            
            // Log de inicio de registro
            Logger::info('Iniciando registro', [
                'codigo_empleado' => $user->codigo_empleado,
                'session_id' => session_id(),
                'tipo_boton' => $_POST['tipo_boton'] ?? 'unknown'
            ]);

           
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new \Exception("Método no permitido");
            }

            // Cargar modelos
            $controlModel = new Control();
            $usuarioModel = new Usuario();
            $userData = $usuarioModel->findByCodigo($user->codigo_empleado);

            if (!$userData) {
                throw new \Exception('No se encontró información del usuario.');
            }

            // Validar campos requeridos
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
                    throw new \Exception("Campo requerido faltante: {$field}");
                }
            }

            // Configurar zona horaria y fecha
            date_default_timezone_set("America/Santo_Domingo");
            $currentDateTime = date("Y-m-d H:i:s");

            // Cerrar registro previo si existe
            $controlModel->updatePreviousRegistro($user->codigo_empleado, $currentDateTime);

            // Preparar datos base del registro
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

            
            $shouldNotify = false;

           
            switch ($registroData['tipo_boton']) {
                case 'final_produccion':
                    $registroData['fecha_fin'] = $currentDateTime;
                    // Limpiar datos de trabajo en BD
                    $controlModel->resetUserData($user->codigo_empleado);
                    $shouldNotify = ($registroData['cantidad_produccion'] > 0 || $registroData['cantidad_scrapt'] > 0);
                    break;

                case 'Parcial':
                    $registroData['fecha_fin'] = $currentDateTime;
                    $registroData['tipo_boton'] = 'Producción';
                    $registroData['descripcion'] = 'Parcial';
                    $shouldNotify = ($registroData['cantidad_produccion'] > 0 || $registroData['cantidad_scrapt'] > 0);
                    break;

                case 'Contratiempos':
                    $registroData['descripcion'] = $_POST['badCopy'] ?? 'Incidente reportado';
                    $registroData['cantidad_produccion'] = 0;
                    $registroData['cantidad_scrapt'] = 0;
                    $shouldNotify = false;
                    break;

                case 'Velocidad':
                case 'Preparación':
                    $registroData['cantidad_produccion'] = 0;
                    $registroData['cantidad_scrapt'] = 0;
                    $shouldNotify = false;
                    break;
            }

            // Insertar registro
            if (!$controlModel->insertRegistro($registroData)) {
                throw new \Exception("Error al guardar el registro");
            }

            
            if (!$controlModel->actualizarEstadoBoton($user->codigo_empleado, $registroData['tipo_boton'])) {
                throw new \Exception("Error al actualizar el estado del botón");
            }

            AuthHelper::updateActiveButton($registroData['tipo_boton']);

            // Notificar a QA si corresponde
            if ($shouldNotify) {
                $notificationModel = new \App\Models\Notificacion();
                $notificationModel->createNotification(
                    $user->area_id,
                    "Nueva entrega registrada en máquina {$registroData['maquina']}",
                    'info'
                );
            }

            Logger::info('Registro guardado exitosamente', [
                'codigo_empleado' => $user->codigo_empleado,
                'tipo_boton' => $registroData['tipo_boton'],
                'cantidad_produccion' => $registroData['cantidad_produccion'],
                'cantidad_scrapt' => $registroData['cantidad_scrapt']
            ]);

            $this->redirectWithMessage('/timeControl/public/control', 'success', 'Registro guardado correctamente.');
            
        } catch (\Exception $e) {
            Logger::exception($e, [
                'controller' => 'RegistroController',
                'method' => 'registrar',
                'user' => $user->codigo_empleado ?? 'unknown'
            ]);
            
            $this->redirectWithMessage(
                '/timeControl/public/control',
                'error',
                'Ocurrió un error: ' . $e->getMessage()
            );
        }
    }

    private function parseQuantity($value)
    {
        if ($value === 'Unknown' || $value === '' || $value === null) {
            return 0;
        }
        return is_numeric($value) ? (float)$value : 0;
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