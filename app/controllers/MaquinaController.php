<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuthHelper;
use App\Models\Maquina;
use Firebase\JWT\JWT;

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
            error_log("Error en datos_trabajo_maquina: " . $e->getMessage());
            header('Location: /timeControl/public/login');
            exit();
        }
    }

    public function seleccionarMaquina()
    {
        session_start(); // Asegurar que la sesión esté iniciada
    
        // Obtener usuario actual del JWT
        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            header('Location: /timeControl/public/login');
            exit();
        }
        $maquina_id = $_POST['maquina_id'] ?? null;
    
        // Crear un nuevo JWT conservando los datos anteriores
        $nuevo_payload = [
            'iat' => time(),
            'exp' => time() + 3600, // Expira en 1 hora
            'data' => array_merge((array) $user, ['maquina_id' => $maquina_id]) // Mantiene datos anteriores
        ];
    
        global $jwt_secret;
        $nuevo_jwt = JWT::encode($nuevo_payload, $jwt_secret, 'HS256');
    
        // Guardar la cookie con el nuevo JWT
        setcookie('jwt', $nuevo_jwt, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => false, // Cambia a true en HTTPS
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    
        header('Location: /timeControl/public/datos_trabajo');
        exit();
    }
    
}
