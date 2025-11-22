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

// Character Routes
$router->get('/personnage', 'App\Controllers\CharacterController@index');
$router->get('/personnage/create', 'App\Controllers\CharacterController@create');
$router->post('/personnage/create', 'App\Controllers\CharacterController@store');

$router->set404('App\Controllers\ErrorController@error404');

$router->run();
