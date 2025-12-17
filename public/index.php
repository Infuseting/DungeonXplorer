<?php



error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '/../var/log/php_errors.log');


require __DIR__ . '/../vendor/autoload.php';
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

// DEBUG: Log all requests
file_put_contents(__DIR__ . '/../var/log/request_access.log', "[" . date('Y-m-d H:i:s') . "] " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

// Load environment variables
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (Exception $e) {
    //error_log('Failed to load environment variables from .env file: ' . $e->getMessage());
}

// Load helper functions
require_once __DIR__ . '/../app/helpers.php';

$router = new \Bramus\Router\Router();

$router->post('/game/combat/end', 'App\Controllers\CombatController@endCombat');

// Save System Routes
$router->post('/game/save', 'App\Controllers\SaveController@saveGame');
$router->post('/game/load', 'App\Controllers\SaveController@loadGame');
$router->post('/game/saves', 'App\Controllers\SaveController@listSaves');

// Dialogue Routes
$router->post('/game/dialogue/select', 'App\Controllers\GameController@selectDialogueOption');

if ($_SERVER['REQUEST_URI'] === '/migrate-consumables') {
    require_once __DIR__ . '/../migration_consumables_buffs.php';
    exit;
}


session_start();

$router->get('/', 'App\Controllers\HomeController@index');
$router->get('/login', 'App\Controllers\AuthController@login');
$router->post('/login', 'App\Controllers\AuthController@loginPost');
$router->get('/register', 'App\Controllers\AuthController@register');
$router->post('/register', 'App\Controllers\AuthController@registerPost');
$router->get('/logout', 'App\Controllers\AuthController@logout');
$router->get('/forgot-password', 'App\Controllers\AuthController@forgotPassword');
$router->post('/forgot-password', 'App\Controllers\AuthController@forgotPasswordPost');

// User Profile & OAuth (Public/Protected mixed, handled by controllers/middleware)
$router->post('/user/update-profile', 'App\Controllers\UserController@updateProfile');
$router->post('/user/update-email', 'App\Controllers\UserController@updateEmail');
$router->post('/user/update-password', 'App\Controllers\UserController@updatePassword');
$router->get('/user/connected-accounts', 'App\Controllers\OAuthController@getConnectedAccounts'); // Should likely be protected

// OAuth Social Login
$router->get('/oauth/login/(\w+)', 'App\Controllers\OAuthController@redirect');
$router->get('/oauth/callback/(\w+)', 'App\Controllers\OAuthController@callback');
$router->post('/oauth/callback/(\w+)', 'App\Controllers\OAuthController@callback');
$router->post('/oauth/unlink/(\w+)', 'App\Controllers\OAuthController@unlink');

// API Routes
$router->get('/api/character/(\d+)/render', 'App\Controllers\CharacterAppearanceController@toFullArray');

// Character Routes (Protected)
$router->mount('/personnage', function() use ($router) {
    $router->before('GET|POST', '', function() {
        (new AuthMiddleware())->handle();
    });
    $router->before('GET|POST', '.*', function() {
        (new AuthMiddleware())->handle();
    });
    $router->get('/', 'App\Controllers\CharacterController@index');
    $router->get('/create', 'App\Controllers\CharacterController@create');
    $router->post('/create', 'App\Controllers\CharacterController@store');
    $router->post('/delete', 'App\Controllers\CharacterController@delete');
    
    // Appearance routes - accepte "preview" ou un ID numérique
    $router->get('/apparence/(preview|\d+)', 'App\Controllers\CharacterAppearanceController@index');
    $router->post('/apparence/(preview|\d+)', 'App\Controllers\CharacterAppearanceController@update');

    // Difficulty Routes
    $router->get('/difficulty', 'App\Controllers\CharacterDifficultyController@index');
    $router->post('/difficulty', 'App\Controllers\CharacterDifficultyController@store');
});

// Game Routes (Protected)
$router->mount('/game', function() use ($router) {
    $router->before('GET|POST', '', function() {
        (new AuthMiddleware())->handle();
    });
    $router->before('GET|POST', '.*', function() {
        (new AuthMiddleware())->handle();
    });

    $router->post('/', 'App\Controllers\GameController@index'); // POST from character select
    $router->get('/', 'App\Controllers\GameController@index'); // GET for refresh

    // Map API
    $router->post('/submap/load', 'App\Controllers\GameController@loadSubMap');
    $router->get('/map/points/(\\d+)', 'App\\Controllers\\GameController@getMapPoints');
    // NPC API
    $router->get('/npc/(\d+)', 'App\Controllers\GameController@getNPC');
    $router->get('/dialogue/tree/(\d+)', 'App\Controllers\GameController@getDialogueTree');
    $router->post('/dialogue/complete', 'App\Controllers\GameController@completeDialogue');

    // Quest API
    $router->post('/quest/accept', 'App\Controllers\GameController@acceptQuest');
    $router->get('/quest/log', 'App\Controllers\GameController@getQuestLog');
    
    $router->get('/combat/start/(\d+)', 'App\Controllers\CombatController@startCombat');
    $router->post('/combat/roll-dice', 'App\Controllers\CombatController@rollDice');
    $router->post('/combat/action', 'App\Controllers\CombatController@performAction');
    // Inventory API
    $router->post('/inventory/move', 'App\Controllers\InventoryController@move');
    $router->post('/inventory/equip', 'App\Controllers\InventoryController@equip');
    $router->post('/inventory/unequip', 'App\Controllers\InventoryController@unequip');

    // Shop API
    $router->get('/shop/(\d+)', 'App\Controllers\ShopController@getShop');
    $router->post('/shop/buy', 'App\Controllers\ShopController@buy');
    $router->post('/shop/sell', 'App\Controllers\ShopController@sell');

    // Skills API
    $router->get('/skills', 'App\Controllers\SkillsController@index');
    $router->post('/skills/unlock', 'App\Controllers\SkillsController@unlock');

});

// Story Routes (Protected)
$router->mount('/story', function() use ($router) {
    $router->before('GET|POST', '.*', function() {
        (new AuthMiddleware())->handle();
    });

    $router->get('/enter/(\d+)', 'App\Controllers\StoryController@enterStory');
    $router->get('/current', 'App\Controllers\StoryController@getCurrentNode');
    $router->post('/move', 'App\Controllers\StoryController@moveToNode');
    $router->post('/loot', 'App\Controllers\StoryController@collectLoot');
    $router->post('/flee', 'App\Controllers\StoryController@attemptFlee');
    $router->post('/clear-monsters', 'App\Controllers\StoryController@clearMonsters');
    $router->post('/exit', 'App\Controllers\StoryController@exitStory');
    $router->post('/trap/avoid', 'App\Controllers\StoryController@attemptTrapAvoidance');
    $router->post('/search', 'App\Controllers\StoryController@searchRoom');
});

// Admin Routes (Protected)
$router->mount('/admin', function() use ($router) {
    $router->before('GET|POST', '.*', function() {
        (new AdminMiddleware())->handle();
    });
    $router->before('GET|POST', '', function() {
        (new AdminMiddleware())->handle();
    });

    $router->get('/', 'App\Controllers\AdminController@dashboard');
    $router->get('/stats', 'App\Controllers\AdminController@stats');
    $router->get('/logs', 'App\Controllers\AdminLogController@index');
    
    // Factions Management
    $router->get('/factions', 'App\Controllers\AdminFactionController@index');
    $router->get('/factions/create', 'App\Controllers\AdminFactionController@create');
    $router->post('/factions/create', 'App\Controllers\AdminFactionController@create');
    $router->get('/factions/edit/(\d+)', 'App\Controllers\AdminFactionController@edit');
    $router->post('/factions/edit/(\d+)', 'App\Controllers\AdminFactionController@edit');
    $router->post('/factions/delete/(\d+)', 'App\Controllers\AdminFactionController@delete');

    // Map Management
    $router->get('/map', 'App\Controllers\AdminMapController@index');
    $router->post('/map/update', 'App\Controllers\AdminMapController@updateMap');
    $router->post('/map/create', 'App\Controllers\AdminMapController@createPoint');
    $router->post('/map/delete/(\d+)', 'App\Controllers\AdminMapController@deletePoint');
    
    // Points Management
    $router->get('/points', 'App\Controllers\AdminMapController@managePoints');
    $router->post('/points/update-submap', 'App\Controllers\AdminMapController@updatePointSubMap');
    $router->post('/points/update-npc', 'App\Controllers\AdminMapController@updatePointNPC');
    $router->post('/points/update-visibility', 'App\Controllers\AdminMapController@updatePointVisibility');
    $router->post('/points/update-story', 'App\Controllers\AdminMapController@updatePointStory');
    
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
    $router->post('/quests/reward/item/add', 'App\Controllers\AdminQuestController@addRewardItem');
    $router->post('/quests/reward/item/remove', 'App\Controllers\AdminQuestController@removeRewardItem');
    
    // User Management
    $router->get('/users', 'App\Controllers\AdminUserController@index');
    $router->post('/users/reset-password/(\d+)', 'App\Controllers\AdminUserController@resetPassword');
    $router->post('/users/delete/(\d+)', 'App\Controllers\AdminUserController@delete');
    
    // User Profile Settings (Self)


    // Character Management
    $router->get('/characters', 'App\Controllers\AdminCharacterController@index');
    $router->post('/characters/delete/(\d+)', 'App\Controllers\AdminCharacterController@delete');

    // Story Management
    $router->get('/stories', 'App\Controllers\AdminStoryController@index');
    $router->match('GET|POST', '/stories/create', 'App\Controllers\AdminStoryController@create');
    $router->match('GET|POST', '/stories/edit/(\d+)', 'App\Controllers\AdminStoryController@edit');
    $router->post('/stories/delete/(\d+)', 'App\Controllers\AdminStoryController@delete');
    
    // Story Nodes Management
    $router->get('/stories/(\d+)/nodes', 'App\Controllers\AdminStoryController@manageNodes');
    $router->post('/stories/(\d+)/nodes/create', 'App\Controllers\AdminStoryController@createNode');
    $router->post('/stories/nodes/(\d+)/edit', 'App\Controllers\AdminStoryController@updateNode');
    $router->post('/stories/nodes/(\d+)/delete', 'App\Controllers\AdminStoryController@deleteNode');
    $router->post('/stories/nodes/upload-image', 'App\Controllers\AdminStoryController@uploadNodeImage');
    $router->post('/stories/connections/create', 'App\Controllers\AdminStoryController@createConnection');
    $router->post('/stories/connections/(\d+)/edit', 'App\Controllers\AdminStoryController@updateConnection');
    $router->post('/stories/connections/(\d+)/delete', 'App\Controllers\AdminStoryController@deleteConnection');

    // Procedural Management
    $router->get('/procedural', 'App\Controllers\AdminProceduralController@index');
    $router->match('GET|POST', '/procedural/create', 'App\Controllers\AdminProceduralController@create');
    $router->match('GET|POST', '/procedural/edit/(\d+)', 'App\Controllers\AdminProceduralController@edit');
    $router->post('/procedural/delete/(\d+)', 'App\Controllers\AdminProceduralController@delete');

    // Procedural Pools
    $router->get('/procedural/(\d+)/monsters', 'App\Controllers\AdminProceduralController@monsterPools');
    $router->post('/procedural/(\d+)/monsters/add', 'App\Controllers\AdminProceduralController@addMonsterPool');
    $router->post('/procedural/monsters/delete/(\d+)', 'App\Controllers\AdminProceduralController@deleteMonsterPool');
    
    $router->get('/procedural/(\d+)/loot', 'App\Controllers\AdminProceduralController@lootPools');
    $router->post('/procedural/(\d+)/loot/add', 'App\Controllers\AdminProceduralController@addLootPool');
    $router->post('/procedural/loot/delete/(\d+)', 'App\Controllers\AdminProceduralController@deleteLootPool');

    // Monster Management
    $router->get('/monsters', 'App\Controllers\AdminMonsterController@index');
    $router->match('GET|POST', '/monsters/create', 'App\Controllers\AdminMonsterController@create');
    $router->match('GET|POST', '/monsters/edit/(\d+)', 'App\Controllers\AdminMonsterController@edit');
    $router->post('/monsters/delete/(\d+)', 'App\Controllers\AdminMonsterController@delete');

    // Node Entity Management (API)
    $router->get('/stories/nodes/(\d+)/entities', 'App\Controllers\AdminStoryController@getNodeEntities');
    $router->post('/stories/nodes/entities/add', 'App\Controllers\AdminStoryController@addNodeEntity');
    $router->post('/stories/nodes/entities/remove', 'App\Controllers\AdminStoryController@removeNodeEntity');

    // Classes & Skills Management
    $router->get('/classes', 'App\Controllers\AdminClassController@index');
    $router->match('GET|POST', '/classes/create', 'App\Controllers\AdminClassController@create');
    $router->post('/classes/store', 'App\Controllers\AdminClassController@store');
    $router->match('GET|POST', '/classes/edit/(\d+)', 'App\Controllers\AdminClassController@edit');
    $router->post('/classes/update/(\d+)', 'App\Controllers\AdminClassController@update');
    $router->post('/classes/delete/(\d+)', 'App\Controllers\AdminClassController@delete');

    $router->match('GET|POST', '/classes/skills/add/(\d+)', 'App\Controllers\AdminClassController@addSkill');
    $router->post('/classes/skills/store/(\d+)', 'App\Controllers\AdminClassController@storeSkill');
    $router->match('GET|POST', '/classes/skills/edit/(\d+)', 'App\Controllers\AdminClassController@editSkill');
    $router->post('/classes/skills/update/(\d+)', 'App\Controllers\AdminClassController@updateSkill');
    $router->post('/classes/skills/delete/(\d+)', 'App\Controllers\AdminClassController@deleteSkill');
    $router->post('/classes/skills/save-positions', 'App\Controllers\AdminClassController@updateSkillPositions');
});

$router->set404('App\Controllers\ErrorController@error404');

$router->run();
