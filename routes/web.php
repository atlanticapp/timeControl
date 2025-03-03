<?php
use App\Controllers\AuthController;
use App\Core\Router;

// Instancia del router
$router = new Router();

// Definir rutas
$router->get('/', [AuthController::class, 'login']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/getStatus', [AuthController::class, 'getStatus']);

// Ejecutar el router
$router->run();
