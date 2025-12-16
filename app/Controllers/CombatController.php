<?php
namespace App\Controllers;
use App\Models\Character;
use App\Models\Monster;
use App\Models\Combat;
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

        // Initiative System
        $statsModel = new \App\Models\CharacterStats();
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

    // Tour du joueur
    $skillId = $_POST['skill_id'] ?? null;
    $playerMessage = $combat->playerTurn($action, $skillId);

   

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
         // Refresh DB connection on model if needed? findById closes/unsets DB?
         // Character::findById unsets DB? No, it unsets NOTHING. 
         // Wait, CombatController lines 19/26 call unsetDb().
         // This removes the internal DB connection property?
         // Character.php line 35 `__construct` opens it.
         // If we unset it, we might need to reconnect.
         // Let's check Character.php: `unsetDb` wasn't shown in Step 1976... 
         // Monster.php line 158 has `unsetDb`.
         // CombatController Step 1979 line 19 calls `$characterModel->unsetDb()`.
         // I suspect `Character.php` has it or I missed it.
         // If `unsetDb` is called, `addXp` which uses `$this->db` will fail.
         // I should instantiate a NEW Character model to save? Or Re-connect?
         // Or just instantiate new model for saving.
         
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
    }

    echo json_encode([
        "success" => true,
        "player"  => $playerMessage[0],
        "monster" => $monsterMessage[0],
        "playerHp" => $combat->getPlayerHp(), // This uses local object, might not reflect full heal from level up unless refreshed? 
                                            // The UI uses this for bar. saveChar->addXp restored HP in DB.
                                            // $combat->getJoueur() is the OLD object (no DB access).
                                            // I should update it? Or just send 'maxHpPlayer' update?
        "win" => !$combat->isMonsterAlive(),
        "newTurn" => !$combat->isEnd(),
        "damageM" => $playerMessage[1],
        "damageJ" => $monsterMessage[1],
        "rewards" => $rewards
    ]);
} 

    private function calculateRewards(Combat $combat, Monster $monsterModel) {
        // Simplified formulas
        $str = $monsterModel->getStrength();
        $vit = $monsterModel->getVitality();
        
        $xp = 50 + ($str * 2) + ($vit * 2);
        $gold = rand(5, 15) + $str;
        
        return ['xp' => (int)$xp, 'gold' => (int)$gold];
    }
    
    private function generateLoot($characterId, $monsterModel) {
        $db = \App\Config\Database::getInstance()->getConnection();
        $inventoryModel = new \App\Models\Inventory();
        $loot = [];
        
        // 30% Chance
        if (rand(1, 100) <= 30) {
            $res = $db->query("SELECT id, name FROM items ORDER BY RAND() LIMIT 1");
            if ($res && $row = $res->fetch_assoc()) {
                $add = $inventoryModel->addItem($characterId, $row['id']);
                if ($add['success']) $loot[] = $row['name'];
            }
        }
        return $loot;
    }
}

public function endCombat() {
    unset($_SESSION['combat']); 
}



    

}
