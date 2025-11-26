<?php
namespace App\Controllers;
use App\Models\Character;
use App\Models\Monster;
use App\Models\Combat;
class CombatController
{
    public function startCombat($characterId, $monsterId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $characterModel = new Character();
        $character = $characterModel->findById($characterId);

        // Verify ownership
        if ($character['user_id'] !== $_SESSION['user_id']) {
            header('Location: /personnage');
            exit;
        }

        $monsterModel = new Monster();
        $monster = $monsterModel->findById($monsterId);

        $combat = new Combat($character, $monster);
        ob_start();
        $combat->start();
        $combatLog = ob_get_clean();

        require_once __DIR__ . '/../Views/combat/result.php';
    }
}
