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

   echo json_encode([
    "success" => true,
    "player"  => $playerMessage[0],
    "monster" => $monsterMessage[0],
    "playerHp" => $combat->getPlayerHp(),
    "win" => !$combat->isMonsterAlive(),
    "newTurn" => !$combat->isEnd(),
    "damageM" => $playerMessage[1],
    "damageJ" => $monsterMessage[1]

]);
}

public function endCombat() {
    unset($_SESSION['combat']); 
}



    

}
