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

        // Soporte para rutas con parámetros tipo {id}
        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // El primer match es la ruta completa
                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                    return;
                }
                if (is_array($callback) && count($callback) === 2) {
                    [$controller, $methodName] = $callback;
                    call_user_func_array([new $controller(), $methodName], $matches);
                    return;
                }
                header('HTTP/1.0 500 Internal Server Error');
                echo "Error en la configuración de la ruta";
                return;
            }
        }

        // Si no se encontró ninguna ruta
        header('HTTP/1.0 404 Not Found');
        echo "404 - Página no encontrada";
    }
    
}
