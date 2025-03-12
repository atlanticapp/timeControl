<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Control;
use App\Models\Data;
use App\Models\Usuario;

class DataController extends Controller
{

    public function __construct()
    {
        // Verificar autenticación y redirigir si no está autenticado
        if (!AuthHelper::isAuthenticated()) {
            header('Location: /timeControl/public/login');
        }
    }

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

            // Verificar si ya se ingresaron datos
            if (!empty($_SESSION['data_entered'])) {
                header('Location: /timeControl/public/control');
                exit();
            }
            // Obtener el ID del botón activo

            $control = new Control();
            $active_button_id = $control->getActiveButton($user->codigo_empleado);

            // Renderizar la vista con la información del usuario
            $this->view('operador/datos_trabajo', [
                'usuario' => $user,
                'active_button_id' => $active_button_id
            ]);
        } catch (\Exception $e) {
            error_log("Error en datos_trabajo: " . $e->getMessage());
            header('Location: /timeControl/public/login?status=error');
        }
    }

    public function seleccionarData()
    {
        session_start(); // Asegurar que la sesión esté iniciada

        // Obtener usuario actual del JWT
        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            header('Location: /timeControl/public/login');
            exit();
        }

        // Verificar que los datos fueron enviados por POST
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['jtWo'], $_POST['item'])) {
            $jtWo = $_POST['jtWo'];
            $item = $_POST['item'];

            // Instanciar el modelo de datos
            $data = new Data();
            $control = new Control();

            // Actualizar JTWO e ITEM del usuario
            $data->updateJtWoItem($user->codigo_empleado, $jtWo, $item);

            // Actualizar la fecha de fin en el registro "Espera Trabajo"
            $data->updateFinEspera($user->codigo_empleado);

            // Marcar que los datos han sido ingresados
            $_SESSION['data_entered'] = true;

            // Cerrar Espera Trabajo si está abierto
            date_default_timezone_set("America/Santo_Domingo");
            $fecha_actual = date("Y-m-d H:i:s");
            $control->updatePreviousRegistro($user->codigo_empleado, $fecha_actual);

            // Redirigir a la página de control
            header('Location: /timeControl/public/control');
            exit();
        }

        // Si no se enviaron los datos requeridos
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo "No se enviaron todos los datos requeridos.";
        }
    }

    public function esperaTrabajo()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception("Método no permitido");
            }

            // Validar autenticación del usuario
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

            // Obtener información del usuario
            $data = $usuario->findByCodigo($user->codigo_empleado);
            if (!$data) {
                throw new \Exception("No se encontró información del usuario.");
            }

            $codigo_empleado = $user->codigo_empleado;
            $tipo_boton = $_POST['tipo_boton'] ?? null;
            $maquina = $data['maquina_id'] ?? null;
            $area_id = $user->area_id ?? null;

            // Configurar zona horaria y obtener fecha actual
            date_default_timezone_set("America/Santo_Domingo");
            $fecha_actual = date("Y-m-d H:i:s");

            // Verificar si el botón ya está activo
            if ($control->getActiveButton($codigo_empleado) === 'Espera_trabajo') {
                $this->setSessionMessage('error', 'El botón ya está activo, no se puede crear un nuevo registro.');
                $this->redirect('/timeControl/public/datos_trabajo');
            }

            // Crear el registro de espera de trabajo
            $registroData = [
                'tipo_boton' => $tipo_boton,
                'codigo_empleado' => $codigo_empleado,
                'maquina' => $maquina,
                'area_id' => $area_id,
                'fecha_registro' => $fecha_actual
            ];

            if (!$control->insertEsperaTrabajo($registroData)) {
                throw new \Exception("Error al insertar el registro de espera de trabajo.");
            }

            // Actualizar estado del botón
            if (!$control->actualizarEstadoBoton($codigo_empleado, $tipo_boton)) {
                throw new \Exception("Error al actualizar el estado del botón.");
            }

            $this->setSessionMessage('success', 'Registro de espera de trabajo exitoso!');
            $this->redirect('/timeControl/public/datos_trabajo');
        } catch (\Exception $e) {
            $this->redirect('/timeControl/public/error');
        }
    }

    /**
     * Método auxiliar para establecer mensajes en sesión
     */
    private function setSessionMessage($status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
    }

    /**
     * Método auxiliar para redirigir
     */
    private function redirect($url)
    {
        header("Location: $url");
        exit();
    }
}
