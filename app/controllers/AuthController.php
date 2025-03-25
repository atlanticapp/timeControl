<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use Firebase\JWT\JWT;

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
                $this->createAndStoreJWT($user);

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
        $password2 = $_POST['password2'] ?? null;
        $tipo_usuario = filter_input(INPUT_POST, 'tipo_usuario');
        $area_id = filter_input(INPUT_POST, 'area_id', FILTER_VALIDATE_INT);

        if($password != $password2){
            $this->redirectWithMessage('/timeControl/public/register', 'error', 'Las contraseñas no coinciden');
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


    private function createAndStoreJWT($user)
    {
        global $jwt_secret;

        $payload = [
            "iat" => time(),
            "exp" => time() + (60 * 60 * 15), // 15 horas de expiración
            "data" => [
                "id" => $user['id'],
                "nombre" => $user['nombre'],
                "codigo_empleado" => $user['codigo_empleado'],
                "tipo_usuario" => $user['tipo_usuario'],
                "area_id" => $user['area_id']
            ]
        ];

        try {
            $new_jwt = JWT::encode($payload, $jwt_secret, 'HS256');
            setcookie('jwt', $new_jwt, time() + (60 * 60 * 15), '/', '', false, true);
            return true;
        } catch (\Exception $e) {
            error_log("Error al generar el token JWT: " . $e->getMessage());
            return false;
        }
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
