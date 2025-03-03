<?php
namespace App\Core;

class Autoload {
    public static function register() {
        spl_autoload_register(function ($class) {
            $path = str_replace('\\', '/', $class);
            $path = str_replace('App/', '', $path);
            require_once __DIR__ . '/../../app/' . $path . '.php';
        });
    }
}