<?php

require __DIR__ . '/../vendor/autoload.php';

$router = new \Bramus\Router\Router();

$router->get('/', 'App\Controllers\HomeController@index');

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    echo '404 - Page Not Found';
});

$router->run();
