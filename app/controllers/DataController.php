<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Data;
use App\Models\Control;

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
            $active_button_id = $control->getActiveButton($user->codigo_empleado); // Obtiene el botón activo

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

            // Actualizar JTWO e ITEM del usuario
            $data->updateJtWoItem($user->codigo_empleado, $jtWo, $item);

            // Actualizar la fecha de fin en el registro "Espera Trabajo"
            $data->updateFinEspera($user->codigo_empleado);

            // Marcar que los datos han sido ingresados
            $_SESSION['data_entered'] = true;

            // Redirigir a la página de control
            header('Location: /timeControl/public/control');
            exit();
        }

        // Si no se enviaron los datos requeridos
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo "No se enviaron todos los datos requeridos.";
        }
    }
}
