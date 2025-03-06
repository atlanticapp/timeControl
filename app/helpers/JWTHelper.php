<?php

namespace App\Helpers;

class JWTHelper
{
    private static $secret_key = 'tu_clave_secreta_muy_segura';
    private static $algorithm = 'HS256';
    private static $cookie_name = 'jwt';

    public static function createToken($user)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);

        // Agregar maquina_id al payload
        $payload = json_encode([
            'user_id' => $user['id'],
            'codigo_empleado' => $user['codigo_empleado'],
            'tipo_usuario' => $user['tipo_usuario'],
            'maquina_id' => $user['maquina_id'],  // Agregar esta línea
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // Token válido por 24 horas
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        // Almacenar el token en una cookie
        setcookie(self::$cookie_name, $jwt, time() + (60 * 60 * 24), '/', '', false, true);

        return $jwt;
    }

    public static function validateToken($token)
    {
        $tokenParts = explode('.', $token);

        if (count($tokenParts) != 3) {
            return false;
        }

        $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[0]));
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
        $signatureProvided = $tokenParts[2];

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $payloadObj = json_decode($payload);

        return ($base64UrlSignature === $signatureProvided) && (isset($payloadObj->exp) && $payloadObj->exp > time());
    }

    public static function getPayload($token)
    {
        $tokenParts = explode('.', $token);

        if (count($tokenParts) != 3) {
            return null;
        }

        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));

        return json_decode($payload, true);
    }

    public static function getToken()
    {
        return $_COOKIE[self::$cookie_name] ?? null;
    }

    public static function clearToken()
    {
        if (isset($_COOKIE[self::$cookie_name])) {
            setcookie(self::$cookie_name, '', time() - 3600, '/', '', false, true);
        }
    }
}
