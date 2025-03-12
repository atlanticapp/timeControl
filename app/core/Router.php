<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
        $basePath = '/timeControl/public';
        $uri = str_replace($basePath, '', $uri);
    
        if (isset($this->routes[$method][$uri])) {
            $callback = $this->routes[$method][$uri];
    
            // Si la ruta es un Closure, lo ejecutamos directamente
            if (is_callable($callback)) {
                call_user_func($callback);
                return;
            }
    
            // Si la ruta es un array con [Controller, method], lo ejecutamos
            if (is_array($callback) && count($callback) === 2) {
                [$controller, $method] = $callback;
                (new $controller())->$method();
                return;
            }
    
            // Si el formato es incorrecto
            header('HTTP/1.0 500 Internal Server Error');
            echo "Error en la configuración de la ruta";
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "404 - Página no encontrada";
        }
    }
    
}
