<?php

namespace App\Helpers;

use Firebase\JWT\JWT;

class JWTHelper
{

    public static function createAndStoreJWT($user)
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
                "area_id" => $user['area_id'],
                "maquina_id" => $user['maquina_id'] ?? null,
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
}
