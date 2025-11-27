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
        

    

        $monsterModel = new Monster();
        $monsterModel->findById($monsterId);

        

        require_once __DIR__ . '/../Views/game/interfaceCombat.php';
    }
       public function rollDice() {
        

        if (isset($_POST['diceRoll'])) {
            $_SESSION['diceRoll'] = intval($_POST['diceRoll']);
            echo json_encode([
                "success" => true,
                "value" => $_SESSION['diceRoll']
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Aucune valeur reçue"
            ]);
        }
    }

}
