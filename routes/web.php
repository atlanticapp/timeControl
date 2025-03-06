<?php
use App\Controllers\AuthController;
use App\Core\Router;
use App\Controllers\MaquinaController;
use App\Controllers\DataController;
use App\Helpers\AuthHelper;
use App\Controllers\RegistroController;

// Instancia del router
$router = new Router();

// Rutas para autenticación y registro
$router->get('/', [AuthController::class, 'login']);
$router->get('/login', [AuthController::class, 'login']);   // Mostrar formulario de login
$router->post('/login', [AuthController::class, 'login']);  // Procesar login
$router->post('/register', [AuthController::class, 'register']); // Procesar registro
$router->get('/getStatus', [AuthController::class, 'getStatus']); // Consultar estado de sesión
$router->get('/logout', [AuthHelper::class, 'logout']); // Cerrar sesión

// Rutas para manejar operaciones de máquina y operador
$router->get('/datos_trabajo_maquina', [MaquinaController::class, 'index']);
$router->post('/seleccionar_maquina', [MaquinaController::class, 'seleccionarMaquina']);

// Rutas para manejar datos de trabajo
$router->get('/datos_trabajo', [DataController::class, 'index']);
$router->post('/seleccionar_data', [DataController::class, 'seleccionarData']);
$router->get('/control', [RegistroController::class, 'index']);

// Ejecutar el router
$router->run();
