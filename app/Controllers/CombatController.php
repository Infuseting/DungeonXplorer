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

        // Check for initiative (e.g. from failed flee)
        $initialData = null;
        if (isset($_SESSION['combat_initiative']) && $_SESSION['combat_initiative'] === 'enemy') {
            unset($_SESSION['combat_initiative']); // Consume flag
            
            // Run Monster Turn Immediately
            $monsterResult = $combat->monsterTurn();
            // $monsterResult is [$message, $bool] (bool is damage dealt?)
            // Let's check Combat.php: monsterTurn returns [$message, $bool]. $bool seems unused in controller?
            // Actually performAction uses $monsterMessage[1] as damageJ (damage to Joueur)
            // But wtf, monsterTurn implementation:
            // if successful attack: $bool = true. $damage calculated.
            // Returns [$message, $bool].
            // Wait, perform action: "damageJ" => $monsterMessage[1] ?? 
            // In Combat.php: return [$message, $bool]; $bool is true on hit.
            // Where is the damage value returned? It is NOT returned in monsterTurn!
            // It modifies the player vitality directly. 
            // We just need the message to show "Monster hit you for X". 
            // The message contains the damage text.
            
            $initialData = [
                'message' => $monsterResult[0],
                'hit' => $monsterResult[1]
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
    $playerMessage = $combat->playerTurn($action);

   

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
