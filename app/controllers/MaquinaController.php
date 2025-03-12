<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Maquina;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class MaquinaController extends Controller
{
    private $jwt_secret;

    public function __construct()
    {
        global $jwt_secret;
        $this->jwt_secret = $jwt_secret;

        // Verificar autenticación
        if (!AuthHelper::isAuthenticated()) {
            header('Location: /timeControl/public/login');
            exit();
        }
    }

    public function index()
    {
        try {
            // Obtener el usuario actual
            $user = AuthHelper::getCurrentUser();

            // Verificar si ya ingresó datos
            session_start();
            if (isset($_SESSION['data_entered']) && $_SESSION['data_entered'] === true) {
                header('Location: /timeControl/public/control');
                exit();
            }

            // Obtener lista de máquinas para el área del usuario
            $maquinaModel = new Maquina();
            $maquinas = $maquinaModel->getMaquinasByArea($user->area_id);

            // Renderizar la vista con las máquinas
            $this->view('operador/maquina', [
                'user' => $user,
                'maquinas' => $maquinas
            ]);
        } catch (\Exception $e) {
            // Manejar cualquier error
           $this->redirectToError();
        }
    }

    public function seleccionarMaquina()
    {
        // Validar la máquina seleccionada
        $maquina_id = $_POST['maquina_id'] ?? null;
        if (!$maquina_id) {
            $this->redirectToError();
        }

        // Obtener el JWT actual de la cookie
        $jwt = $_COOKIE['jwt'] ?? null;
        if (!$jwt) {
            $this->redirectToLogin();
        }

        // Decodificar el JWT
        $userData = $this->decodeJWT($jwt);
        if (!$userData) {
            $this->redirectToError();
        }

        // Actualizar la máquina del usuario
        $maquinaModel = new Maquina();
        if (!$maquinaModel->actualizarMaquinaId($maquina_id, $userData['codigo_empleado'])) {
            $this->redirectToError();
        }

        // Actualizar el JWT con el nuevo maquina_id
        $nuevo_jwt = $this->generateJWT($userData, $maquina_id);
        if (!$nuevo_jwt) {
            $this->redirectToError();
        }

        // Establecer la cookie con el nuevo JWT
        $this->setJWTCookie($nuevo_jwt);

        // Redirigir al usuario después de seleccionar la máquina
        header('Location: /timeControl/public/datos_trabajo');
        exit();
    }

    private function redirectToLogin()
    {
        header('Location: /timeControl/public/login');
        exit();
    }

    private function redirectToError()
    {
        header('Location: /timeControl/public/error');
        exit();
    }

    private function decodeJWT($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->jwt_secret, 'HS256'));
            return (array) $decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function generateJWT($userData, $maquina_id)
    {
        try {
            $nuevo_payload = [
                'iat' => time(),
                'exp' => time() + 3600, // Expira en 1 hora
                'data' => array_merge($userData, ['maquina_id' => $maquina_id])
            ];
            return JWT::encode($nuevo_payload, $this->jwt_secret, 'HS256');
        } catch (\Exception $e) {
            error_log("Error al generar el token JWT: " . $e->getMessage());
            return null;
        }
    }

    private function setJWTCookie($jwt)
    {
        setcookie('jwt', $jwt, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}
