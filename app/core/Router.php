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
            (new $callback[0]())->{$callback[1]}();
        } else {
            header('HTTP/1.0 404 Not Found');
            echo "404 - Página no encontrada";
        }
    }
}
