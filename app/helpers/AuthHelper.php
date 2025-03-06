<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthHelper {
    
    public static function isAuthenticated() {
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
    
    public static function getCurrentUser() {
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
    
    public static function logout() {
        setcookie('jwt', '', time() - 3600, '/', '', false, true);
        header('Location: /timeControl/public/login');
    }
}