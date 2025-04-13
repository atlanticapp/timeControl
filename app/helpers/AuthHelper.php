<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthHelper
{

    public static function isAuthenticated()
    {
        if (!isset($_COOKIE['jwt'])) {
            error_log("JWT no encontrado en las cookies");
            return false;
        }

        try {
            global $jwt_secret;
            $decoded = JWT::decode($_COOKIE['jwt'], new Key($jwt_secret, 'HS256'));

            error_log("JWT Decodificado correctamente: " . print_r($decoded, true));
            return isset($decoded->data);
        } catch (\Exception $e) {
            error_log("Error al decodificar JWT: " . $e->getMessage());
            return false;
        }
    }

    public static function getCurrentUser()
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        try {
            global $jwt_secret;
            $decoded = JWT::decode($_COOKIE['jwt'], new Key($jwt_secret, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function updateMaquinaId($maquinaId)
    {
        try {
            $currentUser = self::getCurrentUser();
            if (!$currentUser) {
                throw new \Exception("Usuario no autenticado");
            }

            $userData = [
                'codigo_empleado' => $currentUser->codigo_empleado,
                'nombre' => $currentUser->nombre,
                'tipo_usuario' => $currentUser->tipo_usuario,
                'area_id' => $currentUser->area_id,
                'maquina_id' => $maquinaId
            ];

            $jwt = new JWTHelper();
            return $jwt::createAndStoreJWT($userData);
        } catch (\Exception $e) {
            Logger::error('Error al actualizar maquina_id en JWT', [
                'error' => $e->getMessage(),
                'maquina_id' => $maquinaId
            ]);
            return null;
        }
    }


    public static function logout()
    {
        setcookie('jwt', '', time() - 3600, '/', '', false, true);
        header('Location: /timeControl/public/login');
    }
}
