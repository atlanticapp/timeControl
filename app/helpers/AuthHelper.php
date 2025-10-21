<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthHelper
{
    /**
     * Inicia la sesión si no está activa
     */
    private static function ensureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuración segura de sesión
            ini_set('session.cookie_lifetime', 0); // Cierra con el navegador
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
    }

    /**
     * Verifica si el usuario está autenticado
     */
    public static function isAuthenticated()
    {
        self::ensureSession();
        
        // Verificar si hay datos de usuario en la sesión
        if (!isset($_SESSION['user_data'])) {
            return false;
        }

        // Verificar expiración
        if (isset($_SESSION['expires_at']) && $_SESSION['expires_at'] < time()) {
            self::logout();
            return false;
        }

        return true;
    }

    /**
     * Obtiene el usuario actual de la sesión
     */
    public static function getCurrentUser()
    {
        self::ensureSession();
        
        if (!self::isAuthenticated()) {
            return null;
        }

        // Convertir array a objeto para mantener compatibilidad
        return (object) $_SESSION['user_data'];
    }

    /**
     * Guarda los datos del usuario en la sesión
     */
    public static function setUserData($userData)
    {
        self::ensureSession();
        
        // Guardar datos del usuario
        $_SESSION['user_data'] = [
            'id' => $userData['id'],
            'codigo_empleado' => $userData['codigo_empleado'],
            'nombre' => $userData['nombre'],
            'tipo_usuario' => $userData['tipo_usuario'],
            'area_id' => $userData['area_id'],
            'maquina_id' => $userData['maquina_id'] ?? null,
            'item' => $userData['item'] ?? null,
            'jtWo' => $userData['jtWo'] ?? null,
            'po' => $userData['po'] ?? null,
            'cliente' => $userData['cliente'] ?? null,
            'active_button_id' => $userData['active_button_id'] ?? null
        ];

        // Establecer tiempo de expiración (15 horas)
        $_SESSION['expires_at'] = time() + (60 * 60 * 15);
        
        // Guardar timestamp de último acceso
        $_SESSION['last_activity'] = time();
        
        return true;
    }

    /**
     * Actualiza solo el maquina_id en la sesión
     */
    public static function updateMaquinaId($maquinaId)
    {
        self::ensureSession();
        
        if (!self::isAuthenticated()) {
            return false;
        }

        $_SESSION['user_data']['maquina_id'] = $maquinaId;
        $_SESSION['last_activity'] = time();
        
        return true;
    }

    /**
     * Actualiza los datos de trabajo en la sesión
     */
    public static function updateDatosTrabajo($item, $jtWo, $po = null, $cliente = null)
    {
        self::ensureSession();
        
        if (!self::isAuthenticated()) {
            return false;
        }

        $_SESSION['user_data']['item'] = $item;
        $_SESSION['user_data']['jtWo'] = $jtWo;
        $_SESSION['user_data']['po'] = $po;
        $_SESSION['user_data']['cliente'] = $cliente;
        $_SESSION['last_activity'] = time();
        
        return true;
    }

    /**
     * Actualiza el botón activo
     */
    public static function updateActiveButton($buttonId)
    {
        self::ensureSession();
        
        if (!self::isAuthenticated()) {
            return false;
        }

        $_SESSION['user_data']['active_button_id'] = $buttonId;
        $_SESSION['last_activity'] = time();
        
        return true;
    }

    /**
     * Cierra la sesión del usuario
     */
    public static function logout()
    {
        self::ensureSession();
        
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destruir la sesión
        session_destroy();
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    public static function hasRole($role)
    {
        $user = self::getCurrentUser();
        return $user && $user->tipo_usuario === $role;
    }

    /**
     * Requiere autenticación, si no redirige al login
     */
    public static function requireAuth()
    {
        if (!self::isAuthenticated()) {
            header('Location: /timeControl/public/login');
            exit();
        }
    }

    /**
     * Requiere un rol específico
     */
    public static function requireRole($role)
    {
        self::requireAuth();
        
        if (!self::hasRole($role)) {
            header('Location: /timeControl/public/login');
            exit();
        }
    }

    /**
     * Renueva la sesión (extiende el tiempo de expiración)
     */
    public static function renewSession()
    {
        self::ensureSession();
        
        if (self::isAuthenticated()) {
            $_SESSION['expires_at'] = time() + (60 * 60 * 15);
            $_SESSION['last_activity'] = time();
            return true;
        }
        
        return false;
    }

    /**
     * Obtiene información de la sesión actual para debugging
     */
    public static function getSessionInfo()
    {
        self::ensureSession();
        
        return [
            'session_id' => session_id(),
            'authenticated' => self::isAuthenticated(),
            'user' => self::getCurrentUser(),
            'expires_at' => $_SESSION['expires_at'] ?? null,
            'last_activity' => $_SESSION['last_activity'] ?? null
        ];
    }
}