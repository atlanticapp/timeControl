<?php
namespace App\Helpers;

class ValidationHelper {
    
    public static function validatePassword($password, $confirmPassword) {
        if ($password !== $confirmPassword) {
            return [false, 'Las contraseñas no coinciden'];
        }
        
        if (strlen($password) < 8) {
            return [false, 'La contraseña debe tener al menos 8 caracteres'];
        }
        
        return [true, ''];
    }
    
    public static function validateRequiredFields($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return [false, "El campo {$field} es requerido"];
            }
        }
        
        return [true, ''];
    }
}