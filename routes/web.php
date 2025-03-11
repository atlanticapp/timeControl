<?php
use App\Controllers\AuthController;
use App\Core\Router;
use App\Controllers\MaquinaController;
use App\Controllers\DataController;
use App\Helpers\AuthHelper;
use App\Controllers\RegistroController;
use App\Controllers\SaveVelocidad;
use App\Controllers\SupervisorController;

// Instancia del router
$router = new Router();

// Rutas para autenticación, Login y registro
$router->get('/', [AuthController::class, 'login']);
$router->get('/login', [AuthController::class, 'login']);   // Mostrar formulario de login
$router->post('/login', [AuthController::class, 'login']);  // Procesar login
$router->post('/register', [AuthController::class, 'register']); // Procesar registro
$router->get('/getStatus', [AuthController::class, 'getStatus']); // Consultar estado de sesión
$router->get('/logout', [AuthHelper::class, 'logout']); // Cerrar sesión

// Rutas para manejar operaciones de máquina operador
$router->get('/datos_trabajo_maquina', [MaquinaController::class, 'index']);
$router->post('/seleccionar_maquina', [MaquinaController::class, 'seleccionarMaquina']);

// Rutas para manejar datos de trabajo
$router->get('/datos_trabajo', [DataController::class, 'index']);
$router->post('/seleccionar_data', [DataController::class, 'seleccionarData']);

// Espera Trabajo
$router->post('/espera_trabajo', [DataController::class, 'esperaTrabajo']);

// Control de registros
$router->get('/control', [RegistroController::class, 'index']);
$router->post('/registrar', [RegistroController::class, 'registrar']);


// Velocidad
$router->post('/saveVelocidad', [SaveVelocidad::class, 'saveVelocidad']);

// Rutas de supervisor
$router->get('/supervisor', [SupervisorController::class, 'index']);


// Error
$router->get('/error', function() {
    // Aquí puedes redirigir a una vista de error o mostrar un mensaje de error genérico
    echo 'Hubo un error, por favor intenta nuevamente.';
});
// Ejecutar el router
$router->run();
