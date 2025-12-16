<?php
namespace App\Controllers;
use App\Services\LoggerService;
use App\Services\DifficultyService;
use App\Services\StatusEffectService;
use App\Models\Character;
use App\Models\Monster;
use App\Models\Combat;
use App\Models\CharacterStats;

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

        require_once __DIR__ . '/../Views/game/interfaceCombat.php';
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
             
             $rewards = [
                 'xp' => $calc['xp'],
                 'gold' => $calc['gold'],
                 'levels_gained' => $xpRes['levels_gained'],
                 'loot' => $loot
             ];

             // Log Victory
             $logger = new LoggerService();
             $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'COMBAT_WIN', [
                 'monster_id' => $monsterModel->getId(),
                 'xp_gained' => $calc['xp'],
                 'gold_gained' => $calc['gold'],
                 'loot' => $loot
             ]);
        }

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

        echo json_encode([
            "success" => true,
            "player"  => $playerMessage[0],
            "monster" => $monsterMessage ? $monsterMessage[0] : "",
            "playerHp" => $combat->getPlayerHp(), 
            "win" => !$combat->isMonsterAlive(),
            "newTurn" => !$combat->isEnd(),
            "damageM" => $playerMessage[1],
            "damageJ" => $monsterMessage ? $monsterMessage[1] : 0,
            "rewards" => $rewards
        ]);
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
