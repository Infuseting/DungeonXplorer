<?php

require __DIR__ . '/../vendor/autoload.php';

$router = new \Bramus\Router\Router();

$router->get('/', function() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DungeonXplorer</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-900 text-white h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-5xl font-bold mb-4 text-blue-500">DungeonXplorer</h1>
            <p class="text-xl text-gray-300">Hello World! Bramus Router & Tailwind CSS are working.</p>
        </div>
    </body>
    </html>
    <?php
});

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    echo '404 - Page Not Found';
});

$router->run();
