<?php

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load helper functions
require_once __DIR__ . '/../app/helpers.php';

session_start();

$router = new \Bramus\Router\Router();

$router->get('/', 'App\Controllers\HomeController@index');
$router->get('/login', 'App\Controllers\AuthController@login');
$router->post('/login', 'App\Controllers\AuthController@loginPost');
$router->get('/register', 'App\Controllers\AuthController@register');
$router->post('/register', 'App\Controllers\AuthController@registerPost');
$router->get('/logout', 'App\Controllers\AuthController@logout');

// Character Routes (Protected)
$router->mount('/personnage', function() use ($router) {
    $router->before('GET|POST', '', function() {
        (new \App\Middleware\AuthMiddleware())->handle();
    });

    $router->get('/', 'App\Controllers\CharacterController@index');
    $router->get('/create', 'App\Controllers\CharacterController@create');
    $router->post('/create', 'App\Controllers\CharacterController@store');
    $router->post('/delete', 'App\Controllers\CharacterController@delete');
});

// Game Routes (Protected)
$router->mount('/game', function() use ($router) {
    $router->before('GET|POST', '', function() {
        (new \App\Middleware\AuthMiddleware())->handle();
    });

    $router->post('/', 'App\Controllers\GameController@index'); // POST from character select
    $router->get('/', 'App\Controllers\GameController@index'); // GET for refresh
    
    $router->get('/combat/start/(\d+)', 'App\Controllers\CombatController@startCombat');
    $router->post('/combat/roll-dice', 'App\Controllers\CombatController@rollDice');
    $router->post('/combat/action', 'App\Controllers\CombatController@performAction');
    // Inventory API
    $router->post('/inventory/move', 'App\Controllers\InventoryController@move');
    $router->post('/inventory/equip', 'App\Controllers\InventoryController@equip');
    $router->post('/inventory/unequip', 'App\Controllers\InventoryController@unequip');
});

// Admin Routes (Protected)
$router->mount('/admin', function() use ($router) {
    $router->before('GET|POST', '', function() {
        (new \App\Middleware\AdminMiddleware())->handle();
    });

    $router->get('/', 'App\Controllers\AdminController@dashboard');
    
    // Map Management
    $router->get('/map', 'App\Controllers\AdminMapController@index');
    $router->post('/map/create', 'App\Controllers\AdminMapController@createPoint');
    $router->post('/map/delete/(\d+)', 'App\Controllers\AdminMapController@deletePoint');
});

$router->set404('App\Controllers\ErrorController@error404');

$router->run();
