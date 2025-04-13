<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\JWTHelper;
use App\Models\Usuario;

class AuthController extends Controller
{

    public function login()
    {
        // Borrar cookie JWT
        setcookie('jwt', '', time() - 3600, '/', '', false, true);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            session_start(); // Asegurar que las sesiones están iniciadas
            unset($_SESSION['data_entered']); // 🛑 Evitar redirecciones incorrectas

            $codigo_empleado = $_POST['codigo_empleado'] ?? '';
            $password = $_POST['password'] ?? '';

            $usuario = new Usuario();
            $user = $usuario->findByCodigo($codigo_empleado);

            if ($user && password_verify($password, $user['password'])) {
                $jwt = new JWTHelper();
                $jwt->createAndStoreJWT($user);

                if (!empty($user['item']) && !empty($user['jtWo'])) {
                    $_SESSION['data_entered'] = true;
                    header('Location: /timeControl/public/control');
                    exit();
                } else {
                    $redirectPage = ($user['tipo_usuario'] === 'supervisor') ? 'supervisor' : (($user['tipo_usuario'] === 'qa') ? 'dashboard' : 'datos_trabajo_maquina');
                    header("Location: /timeControl/public/{$redirectPage}");
                    exit();
                }
            } else {
                $this->redirectWithMessage('/timeControl/public/login', 'error', $user ? 'Contraseña incorrecta.' : 'Código de empleado incorrecto.');
            }
        }

        // Cargar áreas para el formulario de registro
        $usuario = new Usuario();
        $areas = $usuario->getAllAreas();

        $this->view('auth/login', ['areas' => $areas]);
    }


    public function register()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }
        session_start();
        // Obtener y validar datos del formulario
        $nombre = filter_input(INPUT_POST, 'nombre');
        $codigo_empleado = filter_input(INPUT_POST, 'codigo_empleado');
        $password = $_POST['password'] ?? null;
        $password2 = $_POST['confirm_pwd'] ?? null;
        $tipo_usuario = filter_input(INPUT_POST, 'tipo_usuario');
        $area_id = filter_input(INPUT_POST, 'area_id', FILTER_VALIDATE_INT);

        if ($password != $password2) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Las contraseñas no coinciden');
        }

        if (!$nombre || !$codigo_empleado || !$password || !$tipo_usuario || !$area_id) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'Todos los campos son obligatorios');
            exit;
        }

        $usuario = new Usuario();

        // Verificar si el código de empleado ya existe
        if ($usuario->findByCodigo($codigo_empleado)) {
            $this->redirectWithMessage('/timeControl/public/login', 'error', 'El código de empleado ya está registrado');
            exit;
        }

        $userData = [
            'nombre' => $nombre,
            'codigo_empleado' => $codigo_empleado,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'tipo_usuario' => $tipo_usuario,
            'area_id' => $area_id
        ];

        $mensaje = $usuario->create($userData) ?
            ['success', 'Usuario registrado correctamente'] :
            ['error', 'Error al registrar usuario'];

        $this->redirectWithMessage('/timeControl/public/login', $mensaje[0], $mensaje[1]);
        exit;
    }

    public function getStatus()
    {
        // Asegurarse de que la sesión esté iniciada
        session_start();

        // Verificar si hay un mensaje de estado en la sesión
        if (isset($_SESSION['status']) && isset($_SESSION['message'])) {
            $status = $_SESSION['status'];
            $message = $_SESSION['message'];

            // Limpiar las variables de estado para que no persistan
            unset($_SESSION['status']);
            unset($_SESSION['message']);

            // Devolver la respuesta en formato JSON
            echo json_encode([
                'status' => $status,
                'message' => $message
            ]);
        } else {
            // Si no hay estado, devolver una respuesta vacía
            echo json_encode([
                'status' => '',
                'message' => ''
            ]);
        }

        exit(); // Finalizar el script después de enviar la respuesta
    }


    private function redirectWithMessage($url, $status, $message)
    {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
        header("Location: $url");
        exit();
    }
}
