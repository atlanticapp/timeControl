<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Usuario;

class AuthController extends Controller
{
    /**
     * Maneja el login de usuarios
     */
    public function login()
    {
        // Si ya está autenticado, redirigir según rol
        if (AuthHelper::isAuthenticated()) {
            $this->redirectAfterLogin(AuthHelper::getCurrentUser());
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->processLogin();
            return;
        }

        // Mostrar formulario de login
        $usuario = new Usuario();
        $areas = $usuario->getAllAreas();
        $this->view('auth/login', ['areas' => $areas]);
    }

    /**
     * Procesa el formulario de login
     */
    private function processLogin()
    {
        $codigo_empleado = trim($_POST['codigo_empleado'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validar campos vacíos
        if (empty($codigo_empleado) || empty($password)) {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'error',
                'Por favor complete todos los campos'
            );
            return;
        }

        $usuario = new Usuario();
        $user = $usuario->findByCodigo($codigo_empleado);

        // Verificar credenciales
        if (!$user || !password_verify($password, $user['password'])) {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'error',
                $user ? 'Contraseña incorrecta' : 'Código de empleado no encontrado'
            );
            return;
        }

        // Guardar datos en sesión
        if (!AuthHelper::setUserData($user)) {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'error',
                'Error al crear la sesión'
            );
            return;
        }

        // Log de login exitoso
        error_log("Login exitoso - Usuario: {$user['codigo_empleado']}, Tipo: {$user['tipo_usuario']}, Session ID: " . session_id());

        // Redirigir según el tipo de usuario y datos disponibles
        $this->redirectAfterLogin((object)$user);
    }

    /**
     * Redirige al usuario después del login según su perfil
     */
    private function redirectAfterLogin($user)
{
    // Convertir a objeto si es array
    if (is_array($user)) {
        $user = (object)$user;
    }

    // AGREGAR ESTA CONDICIÓN PARA PLANIFICADORES
    if ($user->tipo_usuario === 'planificador') {
        header('Location: /timeControl/public/planificador');
        exit();
    }

    // Si es supervisor o QA, ir directo a su dashboard
    if (in_array($user->tipo_usuario, ['supervisor', 'qa'])) {
        $route = $user->tipo_usuario === 'supervisor' ? 'supervisor' : 'dashboard';
        header("Location: /timeControl/public/{$route}");
        exit();
    }

    // Para operadores: verificar si tienen datos de trabajo
    if (!empty($user->item) && !empty($user->jtWo)) {
        header('Location: /timeControl/public/control');
        exit();
    }

    // Si no tiene datos de trabajo, ir a seleccionar máquina
    header('Location: /timeControl/public/datos_trabajo_maquina');
    exit();
}
    /**
     * Maneja el registro de nuevos usuarios
     */
    public function register()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header('Location: /timeControl/public/login');
            exit();
        }

        // Validar y sanitizar datos
        $nombre = trim($_POST['nombre'] ?? '');
        $codigo_empleado = trim($_POST['codigo_empleado'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['confirm_pwd'] ?? '';
        $tipo_usuario = trim($_POST['tipo_usuario'] ?? '');
        $area_id = filter_input(INPUT_POST, 'area_id', FILTER_VALIDATE_INT);

        // Validaciones
        if (empty($nombre) || empty($codigo_empleado) || empty($password) || empty($tipo_usuario) || !$area_id) {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'error',
                'Todos los campos son obligatorios'
            );
            return;
        }

        if ($password !== $password2) {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'error',
                'Las contraseñas no coinciden'
            );
            return;
        }

        if (strlen($password) < 6) {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'error',
                'La contraseña debe tener al menos 6 caracteres'
            );
            return;
        }

        $usuario = new Usuario();

        // Verificar si el código ya existe
        if ($usuario->findByCodigo($codigo_empleado)) {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'error',
                'El código de empleado ya está registrado'
            );
            return;
        }

        // Crear usuario
        $userData = [
            'nombre' => $nombre,
            'codigo_empleado' => $codigo_empleado,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'tipo_usuario' => $tipo_usuario,
            'area_id' => $area_id
        ];

        if ($usuario->create($userData)) {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'success',
                'Usuario registrado correctamente. Por favor inicie sesión.'
            );
        } else {
            $this->redirectWithMessage(
                '/timeControl/public/login',
                'error',
                'Error al registrar el usuario. Intente nuevamente.'
            );
        }
    }

    /**
     * Endpoint para obtener el estado de los mensajes (AJAX)
     */
    public function getStatus()
    {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $response = [
            'status' => $_SESSION['status'] ?? '',
            'message' => $_SESSION['message'] ?? ''
        ];

        // Limpiar mensajes de la sesión
        unset($_SESSION['status'], $_SESSION['message']);

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

public function logout()
{
    // Log antes de cerrar sesión
    $user = AuthHelper::getCurrentUser();
    if ($user) {
        error_log("Logout - Usuario: {$user->codigo_empleado}, Session ID: " . session_id());
    }
    
    // Cerrar sesión
    AuthHelper::logout();
    
    // Redirigir al login
    header('Location: /timeControl/public/login');
    exit();
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
    public function forgotPassword()
    {
        // Cargar la vista para el formulario de recuperacion de contraseña
        $this->view('auth/forgot_password');
    }

    
    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo_empleado = $_POST['codigo_empleado'] ?? '';
            $nueva_password = $_POST['nueva_password'] ?? '';
            $confirmar_password = $_POST['confirmar_password'] ?? '';

            if ($nueva_password !== $confirmar_password) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Las contraseñas no coinciden';
                header('Location: /timeControl/public/reset-password');
                exit;
            }

            if (strlen($nueva_password) < 6) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'La contraseña debe tener al menos 6 caracteres';
                header('Location: /timeControl/public/reset-password');
                exit;
            }

            $usuarioModel = $this->model('Usuario');
            $usuario = $usuarioModel->findByCodigo($codigo_empleado);
            if (!$usuario) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Código de empleado no encontrado';
                header('Location: /timeControl/public/reset-password');
                exit;
            }

            $nueva_password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);

            if ($usuarioModel->updatePassword($codigo_empleado, $nueva_password_hash)) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Contraseña actualizada correctamente';
                header('Location: /timeControl/public/login');
                exit;
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Error al actualizar la contraseña';
                header('Location: /timeControl/public/reset-password');
                exit;
            }
        } else {
            // Cargar la vista para el formulario de cambio de contraseña
            $this->view('auth/reset_password');
        }
    }
}