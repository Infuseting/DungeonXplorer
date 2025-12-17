<?php
namespace App\Controllers;
use App\Services\LoggerService;
use App\Services\DifficultyService;
use App\Services\StatusEffectService;
use App\Config\Database;
use App\Models\Character;
use App\Models\Monster;
use App\Models\Combat;
use App\Models\CharacterStats;
use App\Models\Inventory;


class CombatController
{
    public function startCombat( $monsterId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        

        $characterModel = new Character();
        $characterModel->findById($_SESSION['character_id']);
        $characterModel->unsetDb();
        

    
        $_SESSION['maxHpPlayer'] = $characterModel->getVitality();
        $monsterModel = new Monster();
        $monsterModel->findById($monsterId);
        $monsterModel->unsetDb();
        $combat = new Combat($characterModel, $monsterModel);
    
        $_SESSION['combat'] = $combat;
        
        // Log Combat Start
        $logger = new LoggerService();
        $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'COMBAT_START', [
            'monster_id' => $monsterId,
            'monster_name' => $monsterModel->getName()
        ]);
        
        // Difficulty handling
        $difficultyService = new DifficultyService();
        $_SESSION['difficulty_service'] = serialize($difficultyService);
        $_SESSION['current_difficulty'] = $characterModel->getDifficulty();
        $_SESSION['is_ironman'] = $characterModel->isIronman();

        // Initiative System

        // Initiative System
        $statsModel = new CharacterStats();
        $playerStats = $statsModel->getEffectiveStats($_SESSION['character_id']);
        $playerDex = $playerStats['stats']['dexterity'] ?? 10;
        $playerInit = rand(1, 20) + floor(($playerDex - 10) / 2);

        $monsterDex = $monsterModel->getDexterity();
        $monsterInit = rand(1, 20) + floor(($monsterDex - 10) / 2);

        // Determine who starts
        $monsterStarts = false;
        $initMessage = "";

        // Check forced initiative (Failed Flee)
        if (isset($_SESSION['combat_initiative']) && $_SESSION['combat_initiative'] === 'enemy') {
            $monsterStarts = true;
            $initMessage = "Vous avez échoué à fuir ! Le monstre attaque en premier.";
            unset($_SESSION['combat_initiative']);
        } elseif ($monsterInit > $playerInit) {
            $monsterStarts = true;
            $initMessage = "Le monstre est plus rapide ! (Init: Monstre $monsterInit vs Vous $playerInit)";
        } else {
            $initMessage = "Vous avez l'initiative ! (Init: Vous $playerInit vs Monstre $monsterInit)";
        }

        $initialData = null;
        if ($monsterStarts) {
            // Run Monster Turn Immediately
            $monsterResult = $combat->monsterTurn();
            // $monsterResult is [$message, $bool]
            
            $initialData = [
                'message' => $initMessage . " <br>" . $monsterResult[0],
                'hit' => $monsterResult[1],
                'monster_starts' => true
            ];
        } else {
            $initialData = [
                'message' => $initMessage . " À vous d'attaquer !",
                'hit' => false,
                'monster_starts' => false
            ];
        }

        // Fetch Potions (Consumables) from Inventory
        $inventoryModel = new \App\Models\Inventory();
        $inventoryData = $inventoryModel->getCharacterInventory($_SESSION['character_id']);
        
        $potions = [];
        if (!empty($inventoryData['inventory'])) {
            foreach ($inventoryData['inventory'] as $item) {
                // Determine if consumable - Assuming 'consumable' type or specific categorization
                // Adjust this check based on your actual Item definition
                if (isset($item['type']) && $item['type'] === 'consumable') {
                    // Group by Item ID to handle quantities if needed, or pass individual stacks
                    // The Inventory Model returns items. If they are stacked in DB, good. 
                    // If multiple stacks exist, we should group them for display if desired.
                    // For now, let's pass them as is or aggregated.
                    $id = $item['item_id'];
                    if (!isset($potions[$id])) {
                        $potions[$id] = $item;
                        $potions[$id]['count'] = 1; 
                        // If item has a 'quantity' field in inventory, use it. 
                        // The current Inventory::getCharacterInventory output doesn't seem to have explicit 'quantity' column in the SELECT, 
                        // unless it's added dynamically or we missed it. 
                        // Let's assume singular items for now or check if duplicates exist.
                    } else {
                        $potions[$id]['count']++;
                    }
                }
            }
        }
        
        // Fetch Unlocked Skills
        $skillModel = new \App\Models\Skill();
        $skills = $skillModel->getUnlockedSkills($_SESSION['character_id']);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
             // Render Partial
            extract([
                'characterModel' => $characterModel,
                'monsterModel' => $monsterModel,
                'initialData' => $initialData,
                'potions' => $potions,
                'skills' => $skills,
                'returnStoryId' => $_GET['story_id'] ?? null
            ]);
            require __DIR__ . '/../Views/game/partials/combat_content.php';
            exit;
        }

        // Fallback for direct access: Redirect to Game (or show full view if you really want)
        // ideally direct access should be handled by the router on client side, but here we enforce SPA.
        // If we redirect to /game, we lose the "start combat" intent unless we store it.
        // For now, let's just show the full view if not AJAX for debugging, OR redirect.
        // A hard redirect to /game means "Cancel Combat Entry".
        header('Location: /game');
        exit;
    }
    public function rollDice() {
    // Toujours démarrer la session si elle n'est pas déjà active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    header('Content-Type: application/json; charset=utf-8');

    if (!empty($_POST['diceRoll'])) {
        $diceRoll = (int) $_POST['diceRoll'];
        $_SESSION['diceRoll'] = $diceRoll;

        echo json_encode([
            "success" => true,
            "value"   => $diceRoll
        ]);
    } else {
        http_response_code(400); // code HTTP explicite
        echo json_encode([
            "success" => false,
            "message" => "Aucune valeur reçue"
        ]);
    }
}

    public function performAction() {
        header('Content-Type: application/json; charset=utf-8');
        ob_clean(); // supprime tout ce qui aurait pu être envoyé avant


        if (!isset($_SESSION['combat'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Aucun combat en cours"]);
            return;
        }

        if (!isset($_SESSION['diceRoll'])) {
            echo json_encode(["success" => false, "message" => "Vous devez lancer le dé avant d'agir"]);
            return;
        }

        $action = $_POST['action'] ?? null;
        if (!$action) {
            echo json_encode(["success" => false, "message" => "Aucune action reçue"]);
            return;
        }

        $combat = $_SESSION['combat'];
        $charId = $_SESSION['character_id'];

        // --- STATUS EFFECT PROCESSING (PLAYER TURN START) ---
        $statusService = new StatusEffectService();
        $statusResult = $statusService->processTurn($charId);
        
        $statusMessages = $statusResult['messages'];
        $preventAction = $statusResult['prevent_action'];

        $playerMessage = ["", 0];
        
        // If Stunned, skip player action logic
        if ($preventAction) {
             $playerMessage[0] = implode("<br>", $statusMessages) . "<br><strong>Vous ne pouvez pas agir !</strong>";
        } else {
             // Normal Turn
             $skillId = $_POST['skill_id'] ?? null;
             $playerMessage = $combat->playerTurn($action, $skillId);
             
             // Prepend status messages (e.g. Poison damage)
             if (!empty($statusMessages)) {
                 $playerMessage[0] = implode("<br>", $statusMessages) . "<br>" . $playerMessage[0];
             }
        }
       

        // Tour du monstre (si combat pas fini)
        $monsterMessage = null;
        if (!$combat->isEnd()) {
            $monsterMessage = $combat->monsterTurn();
        }
        
        if (isset($_SESSION['initialDefence'])) {
            $combat->getJoueur()->setArmorClass($_SESSION['initialDefence']);
            unset($_SESSION['initialDefence']); // supprimer pour éviter que ça reste
        }
         // Consommer le dé
        unset($_SESSION['diceRoll']);

        // Check Victory
        $rewards = [];
        if (!$combat->isMonsterAlive()) {
             $monsterModel = $combat->getMonster();
             $calc = $this->calculateRewards($combat, $monsterModel);
             
             $charModel = $combat->getJoueur();
             
             $saveChar = new Character();
             $saveChar->findById($_SESSION['character_id']);
             $xpRes = $saveChar->addXp($calc['xp']);
             $saveChar->addGold($calc['gold']);
             
             // Loot
             $loot = $this->generateLoot($_SESSION['character_id'], $monsterModel);
             
             // Check Quests
             $pqModel = new \App\Models\PlayerQuest();
             $questUpdates = $pqModel->onMonsterKilled($_SESSION['character_id'], $monsterModel->getId());
             
             $rewards = [
                 'xp' => $calc['xp'],
                 'gold' => $calc['gold'],
                 'levels_gained' => $xpRes['levels_gained'],
                 'loot' => $loot,
                 'quests' => $questUpdates
             ];

             // Log Victory
             $logger = new LoggerService();
             $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'COMBAT_WIN', [
                 'monster_id' => $monsterModel->getId(),
                 'xp_gained' => $calc['xp'],
                 'gold_gained' => $calc['gold'],
                 'loot' => $loot
             ]);
             
             // Update Story Progress if applicable
             if (isset($_GET['story_id']) || isset($_POST['return_story_id']) || isset($_SESSION['combat_story_id'])) {
                 // Try to find story ID
                 $storyId = $_GET['story_id'] ?? ($_POST['return_story_id'] ?? ($_SESSION['combat_story_id'] ?? null));
             }
             
             // If we don't have it directly, try to fetch active story from DB
             if (!isset($storyId)) {
                 $progModel = new \App\Models\StoryProgress();
                 $active = $progModel->getActiveStory($_SESSION['character_id']);
                 if ($active) $storyId = $active['story_id'];
             }
             
             if (isset($storyId)) {
                 $progModel = new \App\Models\StoryProgress();
                 $progress = $progModel->getProgress($_SESSION['character_id'], $storyId);
                 
                 if ($progress) {
                     $nodeId = $progress['current_node_id'];
                     $monsterId = $monsterModel->getId();
                     
                     // session storage for individual kills (finer granularity than node_cleared)
                     $sessionKey = 'killed_monsters_' . $nodeId;
                     if (!isset($_SESSION[$sessionKey])) {
                         $_SESSION[$sessionKey] = [];
                     }
                     if (!in_array($monsterId, $_SESSION[$sessionKey])) {
                         $_SESSION[$sessionKey][] = $monsterId;
                     }
                     
                     // Optimization: Check if all monsters in node are dead?
                     // Requires NodeModel. For now, StoryController will check on load.
                 }
             }
        }

        // Output Construction
        // We'll replace newlines to ensure clean JSON
        $pMsg = $playerMessage[0] ?? '';
        $pMsg = str_replace(["\r\n", "\r", "\n"], ' ', $pMsg); // Replace newlines with space
        
        $mMsg = ($monsterMessage ? $monsterMessage[0] : null);
        if ($mMsg) {
             $mMsg = str_replace(["\r\n", "\r", "\n"], ' ', $mMsg);
        }

        echo json_encode([
            "success" => true,
            "player" => $pMsg,
            "monster" => $mMsg,
            "playerHp" => $combat->getPlayerHp(), 
            "win" => (!$combat->isMonsterAlive()),
            "newTurn" => ($combat->isMonsterAlive() && !$preventAction),
            "damageM" => $playerMessage[1] ?? false,
            "damageJ" => $monsterMessage ? $monsterMessage[1] : false,
            "rewards" => $rewards 
        ]);
        exit;

        // Check Defeat
        if (!$combat->isAlive($combat->getJoueur())) {

             // Log Stats
             $logger = new LoggerService();
             $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'COMBAT_LOSS', [
                 'monster_id' => $monsterModel->getId()
             ]);

             if ($_SESSION['is_ironman']) {
                 $charModel = new Character();
                 $charModel->deleteById($charId);
                 // Redirect handled in JS or via specific response flag?
                 // JS expects JSON.
                 echo json_encode([
                    "success" => true,
                    "player" => $playerMessage[0],
                    "monster" => $monsterMessage ? $monsterMessage[0] : "",
                    "playerHp" => 0,
                    "win" => false,
                    "gameOver" => true,
                    "ironmanDeath" => true,
                    "redirect" => "/personnage/create?error=permadeath" 
                 ]);
                 unset($_SESSION['combat']);
                 return;
             }
        }

    } 

    private function calculateRewards(Combat $combat, Monster $monsterModel) {
        // Simplified formulas
        $str = $monsterModel->getStrength();
        $vit = $monsterModel->getVitality();
        
        $xp = 50 + ($str * 2) + ($vit * 2);
        $gold = rand(5, 15) + $str;
        
        // Difficulty Modifier
        $difficulty = $_SESSION['current_difficulty'] ?? 'NORMAL';
        $diffService = new DifficultyService();
        $xpModifier = $diffService->getXpModifier($difficulty);
        
        return ['xp' => (int)($xp * $xpModifier), 'gold' => (int)$gold];
    }
    
    private function generateLoot($characterId, $monsterModel) {
        $db = Database::getInstance()->getConnection();
        $inventoryModel = new Inventory();
        $loot = [];
        
        // 30% Chance * Difficulty Modifier
        $difficulty = $_SESSION['current_difficulty'] ?? 'NORMAL';
        $diffService = new DifficultyService();
        $lootModifier = $diffService->getLootChanceModifier($difficulty);
        $baseChance = 30;
        
        if (rand(1, 100) <= ($baseChance * $lootModifier)) {
            $res = $db->query("SELECT id, name FROM items ORDER BY RAND() LIMIT 1");
            if ($res && $row = $res->fetch_assoc()) {
                $add = $inventoryModel->addItem($characterId, $row['id']);
                if ($add['success']) $loot[] = $row['name'];
            }
        }
        return $loot;
    }
    public function endCombat() {
        unset($_SESSION['combat']); 
    }

}
