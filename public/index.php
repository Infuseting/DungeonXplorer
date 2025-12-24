<?php
/**
 * Point d'entrée de l'application DungeonXplorer.
 * Gère l'initialisation, la configuration et le routage.
 */

// Configuration de la gestion des erreurs
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '/../var/log/php_errors.log');


// Chargement de l'autoloader Composer
require __DIR__ . '/../vendor/autoload.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

// Journalisation simple des requêtes entrantes
file_put_contents(__DIR__ . '/../var/log/request_access.log', "[" . date('Y-m-d H:i:s') . "] " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

// Chargement de la configuration via .env
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (Exception $e) {
    // Le fichier .env peut être manquant en production si les variables sont définies autrement
}

// Chargement des fonctions utilitaires
require_once __DIR__ . '/../app/helpers.php';

// Initialisation du routeur
$router = new \Bramus\Router\Router();

// --- Routes API Spécifiques (Combat, Sauvegarde, etc.) ---
$router->post('/game/combat/end', 'App\Controllers\CombatController@endCombat');

$router->post('/game/save', 'App\Controllers\SaveController@saveGame');
$router->post('/game/load', 'App\Controllers\SaveController@loadGame');
$router->get('/game/saves', 'App\Controllers\SaveController@listSaves');
$router->post('/game/saves', 'App\Controllers\SaveController@listSaves');

$router->post('/game/dialogue/select', 'App\Controllers\GameController@selectDialogueOption');

session_start();

// --- Routes Publiques (Authentification, Accueil, OAuth) ---

$router->get('/', 'App\Controllers\HomeController@index');
$router->get('/login', 'App\Controllers\AuthController@login');
$router->post('/login', 'App\Controllers\AuthController@loginPost');
$router->get('/register', 'App\Controllers\AuthController@register');
$router->post('/register', 'App\Controllers\AuthController@registerPost');
$router->get('/logout', 'App\Controllers\AuthController@logout');
$router->get('/forgot-password', 'App\Controllers\AuthController@forgotPassword');
$router->post('/forgot-password', 'App\Controllers\AuthController@forgotPasswordPost');

$router->post('/user/update-profile', 'App\Controllers\UserController@updateProfile');
$router->post('/user/update-email', 'App\Controllers\UserController@updateEmail');
$router->post('/user/update-password', 'App\Controllers\UserController@updatePassword');
$router->get('/user/connected-accounts', 'App\Controllers\OAuthController@getConnectedAccounts'); 
$router->get('/oauth/login/(\w+)', 'App\Controllers\OAuthController@redirect');
$router->get('/oauth/callback/(\w+)', 'App\Controllers\OAuthController@callback');
$router->post('/oauth/callback/(\w+)', 'App\Controllers\OAuthController@callback');
$router->post('/oauth/unlink/(\w+)', 'App\Controllers\OAuthController@unlink');

$router->get('/api/character/(\d+)/render', 'App\Controllers\CharacterAppearanceController@toFullArray');

// --- Routes Personnage (Création, Sélection, Apparence) ---
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
    
        $router->get('/apparence/(preview|\d+)', 'App\Controllers\CharacterAppearanceController@index');
    $router->post('/apparence/(preview|\d+)', 'App\Controllers\CharacterAppearanceController@update');

        $router->get('/difficulty', 'App\Controllers\CharacterDifficultyController@index');
    $router->post('/difficulty', 'App\Controllers\CharacterDifficultyController@store');
});

// --- Routes Jeu (Gameplay principal) ---
$router->mount('/game', function() use ($router) {
    $router->before('GET|POST', '', function() {
        (new AuthMiddleware())->handle();
    });
    $router->before('GET|POST', '.*', function() {
        (new AuthMiddleware())->handle();
    });

        $router->get('/', 'App\Controllers\GameController@index');
    $router->post('/', 'App\Controllers\GameController@index');
        $router->post('/submap/load', 'App\Controllers\GameController@loadSubMap');
    $router->get('/map/points/(\\d+)', 'App\\Controllers\\GameController@getMapPoints');
        $router->get('/npc/(\d+)', 'App\Controllers\GameController@getNPC');
    $router->get('/dialogue/tree/(\d+)', 'App\Controllers\GameController@getDialogueTree');
    $router->post('/dialogue/complete', 'App\Controllers\GameController@completeDialogue');

        $router->post('/quest/accept', 'App\Controllers\GameController@acceptQuest');
    $router->get('/quest/log', 'App\Controllers\GameController@getQuestLog');
    
        $router->get('/quest/daily', 'App\Controllers\GameController@getDailyQuests');
    $router->post('/quest/daily/claim', 'App\Controllers\GameController@claimDailyQuestReward');
    
    $router->get('/combat/start/(\d+)', 'App\Controllers\CombatController@startCombat');
    $router->post('/combat/roll-dice', 'App\Controllers\CombatController@rollDice');
    $router->post('/combat/action', 'App\Controllers\CombatController@performAction');
        $router->post('/inventory/move', 'App\Controllers\InventoryController@move');
    $router->post('/inventory/equip', 'App\Controllers\InventoryController@equip');
    $router->post('/inventory/unequip', 'App\Controllers\InventoryController@unequip');

        $router->get('/shop/(\d+)', 'App\Controllers\ShopController@getShop');
    $router->post('/shop/buy', 'App\Controllers\ShopController@buy');
    $router->post('/shop/sell', 'App\Controllers\ShopController@sell');

        $router->get('/skills', 'App\Controllers\SkillsController@index');
    $router->post('/skills/unlock', 'App\Controllers\SkillsController@unlock');

    // Routes Maison
    $router->get('/house', 'App\Controllers\HouseController@index');
    $router->get('/house/available', 'App\Controllers\HouseController@available');
    $router->post('/house/purchase', 'App\Controllers\HouseController@purchase');
    $router->post('/house/set-primary', 'App\Controllers\HouseController@setPrimary');
    $router->post('/house/rename', 'App\Controllers\HouseController@rename');
    $router->get('/house/furniture', 'App\Controllers\HouseController@furnitureShop');
    $router->post('/house/furniture/purchase', 'App\Controllers\HouseController@purchaseFurniture');
    $router->post('/house/furniture/sell', 'App\Controllers\HouseController@sellFurniture');
    $router->post('/house/deposit', 'App\Controllers\HouseController@deposit');
    $router->post('/house/withdraw', 'App\Controllers\HouseController@withdraw');
    $router->get('/house/inventory', 'App\Controllers\HouseController@getInventory');
    $router->get('/house/bonuses', 'App\Controllers\HouseController@getBonuses');

});

// --- Routes Histoires (Navigation, Exploration) ---
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
    $router->match('GET|POST', '/exit', 'App\Controllers\StoryController@exitStory');
    $router->post('/reset', 'App\Controllers\StoryController@resetProgress');
    $router->post('/trap/avoid', 'App\Controllers\StoryController@attemptTrapAvoidance');
    $router->post('/search', 'App\Controllers\StoryController@searchRoom');
    $router->post('/npc/interact', 'App\Controllers\StoryController@interactWithNPC');
});

// --- Routes Administration ---
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
    
        $router->get('/factions', 'App\Controllers\AdminFactionController@index');
    $router->get('/factions/create', 'App\Controllers\AdminFactionController@create');
    $router->post('/factions/create', 'App\Controllers\AdminFactionController@create');
    $router->get('/factions/edit/(\d+)', 'App\Controllers\AdminFactionController@edit');
    $router->post('/factions/edit/(\d+)', 'App\Controllers\AdminFactionController@edit');
    $router->post('/factions/delete/(\d+)', 'App\Controllers\AdminFactionController@delete');

        $router->get('/map', 'App\Controllers\AdminMapController@index');
    $router->post('/map/update', 'App\Controllers\AdminMapController@updateMap');
    $router->post('/map/update/(\d+)', 'App\Controllers\AdminMapController@updatePoint');
    $router->post('/map/create', 'App\Controllers\AdminMapController@createPoint');
    $router->post('/map/delete/(\d+)', 'App\Controllers\AdminMapController@deletePoint');
    $router->post('/map/upload-icon', 'App\Controllers\AdminMapController@uploadIcon');
    
        $router->get('/points', 'App\Controllers\AdminMapController@managePoints');
    $router->post('/points/update-submap', 'App\Controllers\AdminMapController@updatePointSubMap');
    $router->post('/points/update-npc', 'App\Controllers\AdminMapController@updatePointNPC');
    $router->post('/points/update-visibility', 'App\Controllers\AdminMapController@updatePointVisibility');
    $router->post('/points/update-story', 'App\Controllers\AdminMapController@updatePointStory');
    
        $router->get('/items', 'App\Controllers\AdminItemController@index');
    $router->match('GET|POST', '/items/create', 'App\Controllers\AdminItemController@create');
    $router->match('GET|POST', '/items/edit/(\d+)', 'App\Controllers\AdminItemController@edit');
    $router->post('/items/delete/(\d+)', 'App\Controllers\AdminItemController@delete');
    
        $router->get('/npcs', 'App\Controllers\AdminNPCController@index');
    $router->match('GET|POST', '/npcs/create', 'App\Controllers\AdminNPCController@create');
    $router->match('GET|POST', '/npcs/edit/(\d+)', 'App\Controllers\AdminNPCController@edit');
    $router->post('/npcs/delete/(\d+)', 'App\Controllers\AdminNPCController@delete');
    $router->post('/npcs/(\d+)/regenerate-inventory', 'App\Controllers\AdminNPCController@regenerateInventory');
    
        $router->get('/dialogues', 'App\Controllers\AdminDialogueController@index');
    $router->match('GET|POST', '/dialogues/create', 'App\Controllers\AdminDialogueController@create');
    $router->match('GET|POST', '/dialogues/edit/(\d+)', 'App\Controllers\AdminDialogueController@edit');
    $router->post('/dialogues/delete/(\d+)', 'App\Controllers\AdminDialogueController@delete');
    $router->get('/dialogues/tree/(\d+)', 'App\Controllers\AdminDialogueController@editTree');
    $router->post('/dialogues/node/add', 'App\Controllers\AdminDialogueController@addNode');
    $router->post('/dialogues/node/update', 'App\Controllers\AdminDialogueController@updateNode');
    $router->post('/dialogues/node/delete', 'App\Controllers\AdminDialogueController@deleteNode');
    
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
    
    $router->get('/quests/daily', 'App\Controllers\AdminQuestController@dailyIndex');
    $router->post('/quests/daily/create', 'App\Controllers\AdminQuestController@dailyCreate');
    $router->post('/quests/daily/edit/(\d+)', 'App\Controllers\AdminQuestController@dailyEdit');
    $router->post('/quests/daily/toggle/(\d+)', 'App\Controllers\AdminQuestController@dailyToggle');
    $router->post('/quests/daily/delete/(\d+)', 'App\Controllers\AdminQuestController@dailyDelete');
    
    // Routes maisons admin
    $router->get('/houses', 'App\Controllers\AdminHouseController@index');
    $router->match('GET|POST', '/houses/create', 'App\Controllers\AdminHouseController@create');
    $router->match('GET|POST', '/houses/edit/(\d+)', 'App\Controllers\AdminHouseController@edit');
    $router->post('/houses/delete/(\d+)', 'App\Controllers\AdminHouseController@delete');
    
    // Routes meubles admin
    $router->get('/furniture', 'App\Controllers\AdminHouseController@furnitureIndex');
    $router->match('GET|POST', '/furniture/create', 'App\Controllers\AdminHouseController@furnitureCreate');
    $router->match('GET|POST', '/furniture/edit/(\d+)', 'App\Controllers\AdminHouseController@furnitureEdit');
    $router->post('/furniture/delete/(\d+)', 'App\Controllers\AdminHouseController@furnitureDelete');
    
        $router->get('/users', 'App\Controllers\AdminUserController@index');
    $router->post('/users/reset-password/(\d+)', 'App\Controllers\AdminUserController@resetPassword');
    $router->post('/users/delete/(\d+)', 'App\Controllers\AdminUserController@delete');
    
    

        $router->get('/characters', 'App\Controllers\AdminCharacterController@index');
    $router->post('/characters/delete/(\d+)', 'App\Controllers\AdminCharacterController@delete');

        $router->get('/stories', 'App\Controllers\AdminStoryController@index');
    $router->match('GET|POST', '/stories/create', 'App\Controllers\AdminStoryController@create');
    $router->match('GET|POST', '/stories/edit/(\d+)', 'App\Controllers\AdminStoryController@edit');
    $router->post('/stories/delete/(\d+)', 'App\Controllers\AdminStoryController@delete');
    
        $router->get('/stories/(\d+)/nodes', 'App\Controllers\AdminStoryController@manageNodes');
    $router->post('/stories/(\d+)/nodes/create', 'App\Controllers\AdminStoryController@createNode');
    $router->post('/stories/nodes/(\d+)/edit', 'App\Controllers\AdminStoryController@updateNode');
    $router->post('/stories/nodes/(\d+)/delete', 'App\Controllers\AdminStoryController@deleteNode');
    $router->post('/stories/nodes/upload-image', 'App\Controllers\AdminStoryController@uploadNodeImage');
    $router->post('/stories/connections/create', 'App\Controllers\AdminStoryController@createConnection');
    $router->post('/stories/connections/(\d+)/edit', 'App\Controllers\AdminStoryController@updateConnection');
    $router->post('/stories/connections/(\d+)/delete', 'App\Controllers\AdminStoryController@deleteConnection');

        $router->get('/procedural', 'App\Controllers\AdminProceduralController@index');
    $router->match('GET|POST', '/procedural/create', 'App\Controllers\AdminProceduralController@create');
    $router->match('GET|POST', '/procedural/edit/(\d+)', 'App\Controllers\AdminProceduralController@edit');
    $router->post('/procedural/delete/(\d+)', 'App\Controllers\AdminProceduralController@delete');

        $router->get('/procedural/(\d+)/monsters', 'App\Controllers\AdminProceduralController@monsterPools');
    $router->post('/procedural/(\d+)/monsters/add', 'App\Controllers\AdminProceduralController@addMonsterPool');
    $router->post('/procedural/monsters/delete/(\d+)', 'App\Controllers\AdminProceduralController@deleteMonsterPool');
    
    $router->get('/procedural/(\d+)/loot', 'App\Controllers\AdminProceduralController@lootPools');
    $router->post('/procedural/(\d+)/loot/add', 'App\Controllers\AdminProceduralController@addLootPool');
    $router->post('/procedural/loot/delete/(\d+)', 'App\Controllers\AdminProceduralController@deleteLootPool');

        $router->get('/monsters', 'App\Controllers\AdminMonsterController@index');
    $router->match('GET|POST', '/monsters/create', 'App\Controllers\AdminMonsterController@create');
    $router->match('GET|POST', '/monsters/edit/(\d+)', 'App\Controllers\AdminMonsterController@edit');
    $router->post('/monsters/delete/(\d+)', 'App\Controllers\AdminMonsterController@delete');

        $router->get('/stories/nodes/(\d+)/entities', 'App\Controllers\AdminStoryController@getNodeEntities');
    $router->post('/stories/nodes/entities/add', 'App\Controllers\AdminStoryController@addNodeEntity');
    $router->post('/stories/nodes/entities/remove', 'App\Controllers\AdminStoryController@removeNodeEntity');

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

// Gestion de l'erreur 404
$router->set404('App\Controllers\ErrorController@error404');

// Exécution du routage
$router->run();
