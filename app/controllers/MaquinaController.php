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
            error_log("Error en index de MaquinaController: " . $e->getMessage());
            header('Location: /timeControl/public/error');
            exit();
        }
    }

    public function seleccionarMaquina()
    {
        session_start(); // Asegurarse de que la sesión esté iniciada

        // Obtener usuario actual desde el JWT
        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            header('Location: /timeControl/public/login');
            exit();
        }

        // Validar la máquina seleccionada
        $maquina_id = $_POST['maquina_id'] ?? null;

        // Obtener el JWT actual de la cookie
        $jwt = $_COOKIE['jwt'] ?? null;
        if (!$jwt) {
            header('Location: /timeControl/public/login');
            exit();
        }

        // Decodificar el JWT existente
        try {
            $decoded = JWT::decode($jwt, new Key($this->jwt_secret, 'HS256'));
            $userData = (array) $decoded->data;
        } catch (\Exception $e) {
            error_log("Error al decodificar JWT: " . $e->getMessage());
            header('Location: /timeControl/public/error');
            exit();
        }

        // Actualizar el campo maquina_id en la base de datos
        $maquinaModel = new Maquina();
        if (!$maquinaModel->actualizarMaquinaId($maquina_id, $userData['codigo_empleado'])) {
            error_log("Error al actualizar maquina_id en la base de datos.");
            header('Location: /timeControl/public/error');
            exit();
        }

        // Actualizar el JWT con el nuevo maquina_id
        $nuevo_payload = [
            'iat' => time(),
            'exp' => time() + 3600, // Expira en 1 hora
            'data' => array_merge($userData, ['maquina_id' => $maquina_id])
        ];

        try {
            $nuevo_jwt = JWT::encode($nuevo_payload, $this->jwt_secret, 'HS256');

            // Establecer la cookie con el nuevo JWT
            setcookie('jwt', $nuevo_jwt, [
                'expires' => time() + 3600,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        } catch (\Exception $e) {
            error_log("Error al generar el token JWT: " . $e->getMessage());
            header('Location: /timeControl/public/error');
            exit();
        }

        // Redirigir al usuario después de seleccionar la máquina
        header('Location: /timeControl/public/datos_trabajo');
        exit();
    }
}
