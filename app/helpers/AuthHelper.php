<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthHelper {
    
    public static function isAuthenticated() {
        if (!isset($_COOKIE['jwt'])) {
            return false;
        }
        
        try {
            global $jwt_secret;
            $decoded = JWT::decode($_COOKIE['jwt'], new Key($jwt_secret, 'HS256'));
            return true;
        } catch (\Exception $e) {
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
    }
}