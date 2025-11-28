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
    $router->before('GET|POST', '.*', function() {
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
    $router->before('GET|POST', '.*', function() {
        (new \App\Middleware\AuthMiddleware())->handle();
    });

    $router->post('/', 'App\Controllers\GameController@index'); // POST from character select
    $router->get('/', 'App\Controllers\GameController@index'); // GET for refresh

    // Map API
    $router->post('/submap/load', 'App\Controllers\GameController@loadSubMap');
    
    // NPC API
    $router->get('/npc/(\d+)', 'App\Controllers\GameController@getNPC');
    $router->get('/dialogue/tree/(\d+)', 'App\Controllers\GameController@getDialogueTree');
    $router->post('/dialogue/complete', 'App\Controllers\GameController@completeDialogue');

    // Quest API
    $router->post('/quest/accept', 'App\Controllers\GameController@acceptQuest');
    $router->get('/quest/log', 'App\Controllers\GameController@getQuestLog');

    // Inventory API
    $router->post('/inventory/move', 'App\Controllers\InventoryController@move');
    $router->post('/inventory/equip', 'App\Controllers\InventoryController@equip');
    $router->post('/inventory/unequip', 'App\Controllers\InventoryController@unequip');

    // Shop API
    $router->get('/shop/(\d+)', 'App\Controllers\ShopController@getShop');
    $router->post('/shop/buy', 'App\Controllers\ShopController@buy');
    $router->post('/shop/sell', 'App\Controllers\ShopController@sell');
});

// Admin Routes (Protected)
$router->mount('/admin', function() use ($router) {
    $router->before('GET|POST', '.*', function() {
        (new \App\Middleware\AdminMiddleware())->handle();
    });
    $router->before('GET|POST', '', function() {
        (new \App\Middleware\AdminMiddleware())->handle();
    });

    $router->get('/', 'App\Controllers\AdminController@dashboard');
    $router->get('/stats', 'App\Controllers\AdminController@stats');
    
    // Map Management
    $router->get('/map', 'App\Controllers\AdminMapController@index');
    $router->post('/map/create', 'App\Controllers\AdminMapController@createPoint');
    $router->post('/map/delete/(\d+)', 'App\Controllers\AdminMapController@deletePoint');
    
    // Points Management
    $router->get('/points', 'App\Controllers\AdminMapController@managePoints');
    $router->post('/points/update-submap', 'App\Controllers\AdminMapController@updatePointSubMap');
    $router->post('/points/update-npc', 'App\Controllers\AdminMapController@updatePointNPC');
    $router->post('/points/update-visibility', 'App\Controllers\AdminMapController@updatePointVisibility');
    
    // Items Management
    $router->get('/items', 'App\Controllers\AdminItemController@index');
    $router->match('GET|POST', '/items/create', 'App\Controllers\AdminItemController@create');
    $router->match('GET|POST', '/items/edit/(\d+)', 'App\Controllers\AdminItemController@edit');
    $router->post('/items/delete/(\d+)', 'App\Controllers\AdminItemController@delete');
    
    // NPC Management
    $router->get('/npcs', 'App\Controllers\AdminNPCController@index');
    $router->match('GET|POST', '/npcs/create', 'App\Controllers\AdminNPCController@create');
    $router->match('GET|POST', '/npcs/edit/(\d+)', 'App\Controllers\AdminNPCController@edit');
    $router->post('/npcs/delete/(\d+)', 'App\Controllers\AdminNPCController@delete');
    $router->post('/npcs/(\d+)/regenerate-inventory', 'App\Controllers\AdminNPCController@regenerateInventory');
    
    // Dialogue Management
    $router->get('/dialogues', 'App\Controllers\AdminDialogueController@index');
    $router->match('GET|POST', '/dialogues/create', 'App\Controllers\AdminDialogueController@create');
    $router->match('GET|POST', '/dialogues/edit/(\d+)', 'App\Controllers\AdminDialogueController@edit');
    $router->post('/dialogues/delete/(\d+)', 'App\Controllers\AdminDialogueController@delete');
    $router->get('/dialogues/tree/(\d+)', 'App\Controllers\AdminDialogueController@editTree');
    $router->post('/dialogues/node/add', 'App\Controllers\AdminDialogueController@addNode');
    $router->post('/dialogues/node/update', 'App\Controllers\AdminDialogueController@updateNode');
    $router->post('/dialogues/node/delete', 'App\Controllers\AdminDialogueController@deleteNode');
    
    // Quest Management
    $router->get('/quests', 'App\Controllers\AdminQuestController@index');
    $router->match('GET|POST', '/quests/create', 'App\Controllers\AdminQuestController@create');
    $router->match('GET|POST', '/quests/edit/(\d+)', 'App\Controllers\AdminQuestController@edit');
    $router->post('/quests/delete/(\d+)', 'App\Controllers\AdminQuestController@delete');
    $router->post('/quests/stage/add', 'App\Controllers\AdminQuestController@addStage');
    $router->post('/quests/stage/update', 'App\Controllers\AdminQuestController@updateStage');
    $router->post('/quests/stage/delete', 'App\Controllers\AdminQuestController@deleteStage');
    $router->post('/quests/objective/add', 'App\Controllers\AdminQuestController@addObjective');
    $router->post('/quests/objective/update', 'App\Controllers\AdminQuestController@updateObjective');
    $router->post('/quests/objective/delete', 'App\Controllers\AdminQuestController@deleteObjective');
    $router->post('/quests/assign-npc', 'App\Controllers\AdminQuestController@assignNPC');
    $router->post('/quests/remove-npc', 'App\Controllers\AdminQuestController@removeNPC');
    $router->post('/quests/stage/add-unlock', 'App\Controllers\AdminQuestController@addMapUnlock');
    $router->post('/quests/stage/remove-unlock', 'App\Controllers\AdminQuestController@removeMapUnlock');
    $router->post('/quests/prerequisite/add', 'App\Controllers\AdminQuestController@addPrerequisite');
    $router->post('/quests/prerequisite/remove', 'App\Controllers\AdminQuestController@removePrerequisite');
});

$router->set404('App\Controllers\ErrorController@error404');

$router->run();
