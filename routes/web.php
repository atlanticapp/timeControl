<?php

use App\Controllers\AuthController;
use App\Controllers\ComentarioController;
use App\Core\Router;
use App\Controllers\MaquinaController;
use App\Controllers\DataController;
use App\Controllers\QaController;
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
// Comentario
$router->post('/addComentario', [ComentarioController::class, 'addComentario']);

// Rutas de supervisor
$router->get('/supervisor', [SupervisorController::class, 'index']);
$router->post('/supervisor', [SupervisorController::class, 'index']);

// Rutas QA
$router->get('/dashboard', [QaController::class, 'index']);
$router->get('/validacion', [QaController::class, 'validacion']);
$router->get('/verDetalles', [QaController::class, 'verDetalles']);
$router->post('/validarEnt', [QaController::class, 'validarEnt']);
$router->post('/corregir', [QaController::class, 'corregir']);
$router->get('/reporteScrapt/{empleado_id}/{maquina_id}/{item}/{jtwo}', [QaController::class, 'reporteScrapt']);
$router->get('/historial', [QaController::class, 'historial']);

// Error handling
$router->get('/error', function () {
    // Verificamos si existen los valores de mensaje y estado en la sesión
    $status = isset($_SESSION['status']) ? $_SESSION['status'] : 'error';
    $message = isset($_SESSION['message']) ? $_SESSION['message'] : 'Hubo un error. Por favor, intenta nuevamente.';

    // Limpiar los valores de la sesión para no mostrar los mensajes en futuras peticiones
    unset($_SESSION['status']);
    unset($_SESSION['message']);

    // Si no hay mensaje en la sesión, mostramos el mensaje por defecto
    if ($status === 'error' && empty($message)) {
        $message = 'Ocurrió un error inesperado. Intenta nuevamente más tarde.';
    }

    // Mostrar el mensaje de error
    echo "
        <div style='text-align: center; padding: 20px; font-family: Arial, sans-serif;'>
            <h2 style='color: " . ($status == 'success' ? '#5bc0de' : '#d9534f') . ";'>" . ucfirst($status) . "</h2>
            <p>{$message}</p>
            <a href='javascript:history.back()' style='display: inline-block; padding: 10px 15px; color: white; background-color: #d9534f; text-decoration: none; border-radius: 5px;'>Regresar</a>
        </div>
    ";
});


// Ejecutar el router
$router->run();
