<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use Firebase\JWT\JWT;

class AuthController extends Controller {
    
    public function login() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            session_start();
            
            $codigo_empleado = $_POST['codigo_empleado'];
            $password = $_POST['password'];
            
            $usuario = new Usuario();
            $user = $usuario->findByCodigo($codigo_empleado);
            
            if ($user && password_verify($password, $user['password'])) {
                $this->createAndStoreJWT($user);
                
                if (!empty($user['item']) && !empty($user['jtWo'])) {
                    header('Location: /timeControl/public/control');
                } else {
                    $redirectPage = ($user['tipo_usuario'] === 'supervisor') 
                        ? 'supervisor' 
                        : 'datos_trabajo_maquina';
                    header("Location: /timeControl/public/{$redirectPage}");
                }
                exit();
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $user ? 'Contraseña incorrecta.' : 'Código de empleado incorrecto.';
                header('Location: /timeControl/public/login');
                exit();
            }
        }
        
        $usuario = new Usuario();
        $areas = $usuario->getAllAreas();
        
        $this->view('auth/login', ['areas' => $areas]);
    }
    
    public function register() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            session_start();
            
            $userData = [
                'nombre' => $_POST['nombre'],
                'codigo_empleado' => $_POST['codigo_empleado'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'tipo_usuario' => $_POST['tipo_usuario'],
                'area_id' => $_POST['area_id']
            ];
            
            $usuario = new Usuario();
            $result = $usuario->create($userData);
            
            $_SESSION['status'] = $result ? 'success' : 'error';
            $_SESSION['message'] = $result ? 'Usuario registrado correctamente' : 'Error al registrar usuario';
            
            header('Location: /timeControl/public/login');
            exit();
        }
    }
    
    private function createAndStoreJWT($user) {
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

    public function getStatus() {
        session_start();
        $status = $_SESSION['status'] ?? null;
        $message = $_SESSION['message'] ?? null;
        
        // Limpiar mensajes después de obtenerlos
        unset($_SESSION['status'], $_SESSION['message']);
        
        echo json_encode(["status" => $status, "message" => $message]);
    }
}
