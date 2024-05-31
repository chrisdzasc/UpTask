<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\LoginController;
use Controllers\DashboardController;
use Controllers\TareaController;
use MVC\Router;

$router = new Router();

// ----- LOGIN -----
$router->get('/', [LoginController::class, 'login']); // Para la vista del Login
$router->post('/', [LoginController::class, 'login']); // Para la vista del Login

// ----- LOGOUT -----
$router->get('/logout', [LoginController::class, 'logout']); // Para la vista del LogOut

// ----- CREAR CUENTA -----
$router->get('/crear', [LoginController::class, 'crear']); // Para la vista de crear cuenta
$router->post('/crear', [LoginController::class, 'crear']); // Para la vista de crear cuenta

// ----- FORMULARIO DE OLVIDE MI PASSWORD -----
$router->get('/olvide', [LoginController::class, 'olvide']); // Para la vista de crear cuenta
$router->post('/olvide', [LoginController::class, 'olvide']); // Para la vista de crear cuenta

// ----- COLOCAR EL NUEVO PASSWORD -----
$router->get('/reestablecer', [LoginController::class, 'reestablecer']); // Para la vista de crear cuenta
$router->post('/reestablecer', [LoginController::class, 'reestablecer']); // Para la vista de crear cuenta

// ----- CONFIRMACIÓN DE CUENTA -----
$router->get('/mensaje', [LoginController::class, 'mensaje']); // Para la vista de crear cuenta
$router->get('/confirmar', [LoginController::class, 'confirmar']); // Para la vista de crear cuenta

// ----- ZONA DE PROYECTO -----
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/crear-proyecto', [DashboardController::class, 'crear_proyecto']);
$router->post('/crear-proyecto', [DashboardController::class, 'crear_proyecto']);
$router->get('/proyecto', [DashboardController::class, 'proyecto']);
$router->get('/perfil', [DashboardController::class, 'perfil']);
$router->post('/perfil', [DashboardController::class, 'perfil']);
$router->get('/cambiar-password', [DashboardController::class, 'cambiar_password']);
$router->post('/cambiar-password', [DashboardController::class, 'cambiar_password']);


// API para las tareas
$router->get('/api/tareas', [TareaController::class, 'index']);
$router->post('/api/tarea', [TareaController::class, 'crear']);
$router->post('/api/tarea/actualizar', [TareaController::class, 'actualizar']);
$router->post('/api/tarea/eliminar', [TareaController::class, 'eliminar']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();