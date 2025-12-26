<?php
namespace App\Controllers;
use App\Services\LoggerService;
use App\Services\DifficultyService;
use App\Services\StatusEffectService;
use App\Services\CharacterStatsService;
use App\Config\Database;
use App\Models\Character;
use App\Models\Monster;
use App\Models\Combat;
use App\Models\CharacterStats;
use App\Models\Inventory;


class CombatController
{
    /**
     * Initie un combat contre un monstre donné.
     * Prépare les objets Combat, Monster et Character, détermine l'initiative,
     * et charge la vue de combat (ou renvoie le HTML via AJAX).
     */
    public function startCombat($monsterId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Chargement du personnage sans garder la connexion DB active dans l'objet
        $characterModel = new Character();
        $characterModel->findById($_SESSION['character_id']);

        // Récupération de la vitalité de base et effective
        $baseVitality = $characterModel->vitality ?? 10;
        $effectiveVitality = $characterModel->getVitality(); // Avec équipement et passives

        // Ajustement proportionnel des HP actuels si l'équipement change la vitalité
        if ($effectiveVitality != $baseVitality) {
            $currentHp = $characterModel->getCurrentHp();
            // Calculer le ratio de HP actuels par rapport à la vitalité de base
            $hpRatio = $baseVitality > 0 ? ($currentHp / $baseVitality) : 1.0;
            // Appliquer ce ratio à la vitalité effective
            $adjustedHp = (int) round($effectiveVitality * $hpRatio);
            $characterModel->current_hp = min($adjustedHp, $effectiveVitality);
        }

        $characterModel->unsetDb();

        // Sauvegarde des PV max pour le calcul des pourcentages côté client
        $_SESSION['maxHpPlayer'] = $effectiveVitality;

        // Chargement du monstre
        $monsterModel = new Monster();

        // Check if we are in story mode (story_id in GET)
        if (isset($_GET['story_id'])) {
            $_SESSION['combat_story_id'] = $_GET['story_id'];
            $monsterModel->loadFromNodeMonsterId($monsterId);
        } else {
            $monsterModel->findById($monsterId);
        }

        $monsterModel->unsetDb();

        // Création de l'instance de combat
        $combat = new Combat($characterModel, $monsterModel);
        $_SESSION['combat'] = $combat;

        // Logging du début du combat
        $logger = new LoggerService();
        $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'COMBAT_START', [
            'monster_id' => $monsterId,
            'monster_name' => $monsterModel->getName()
        ]);

        // Initialisation de la difficulté et des règles (Ironman)
        $difficultyService = new DifficultyService();
        $_SESSION['difficulty_service'] = serialize($difficultyService);
        $_SESSION['current_difficulty'] = $characterModel->getDifficulty();
        $_SESSION['is_ironman'] = $characterModel->isIronman();

        // Calcul de l'initiative (1d20 + Mod Dex)
        $statsModel = new CharacterStats();
        $playerStats = $statsModel->getEffectiveStats($_SESSION['character_id']);
        $playerDex = $playerStats['stats']['dexterity'] ?? 10;
        $playerInit = rand(1, 20) + floor(($playerDex - 10) / 2);

        $monsterDex = $monsterModel->getDexterity();
        $monsterInit = rand(1, 20) + floor(($monsterDex - 10) / 2);

        $monsterStarts = false;
        $initMessage = "";

        // Vérification si le monstre a l'initiative forcée (ex: fuite ratée précédemment)
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

        // Récupération des stats effectives dès le début du combat
        $effectiveStats = CharacterStatsService::getEffectiveStats($_SESSION['character_id']);

        $initialPlayerStats = [
            'attack' => $effectiveStats['attack'],
            'defense' => $effectiveStats['defense'],
            'strength' => $effectiveStats['strength'],
            'intelligence' => $effectiveStats['intelligence'],
            'dexterity' => $effectiveStats['dexterity'],
            'vitality' => $effectiveStats['vitality']
        ];

        if ($monsterStarts) {
            // Le monstre attaque immédiatement si c'est son tour
            $monsterResult = $combat->monsterTurn();

            $initialData = [
                'message' => $initMessage . " <br>" . $monsterResult[0],
                'hit' => $monsterResult[1],
                'monster_starts' => true,
                'playerHp' => $combat->getPlayerHp(),
                'playerDead' => !$combat->getJoueur()->isAlive(),
                'playerStats' => $initialPlayerStats
            ];

            // Si le joueur est mort immédiatement, terminer le combat
            if (!$combat->getJoueur()->isAlive()) {
                $combat->endCombat();

                $logger = new LoggerService();
                $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'COMBAT_LOSS', [
                    'monster_id' => $monsterModel->getId(),
                    'instant_death' => true
                ]);

                $initialData['gameOver'] = true;
            }
        } else {
            $initialData = [
                'message' => $initMessage . " À vous d'attaquer !",
                'hit' => false,
                'monster_starts' => false,
                'playerHp' => $combat->getPlayerHp(),
                'playerDead' => false,
                'playerStats' => $initialPlayerStats
            ];
        }

        // Préparation des potions pour l'interface de combat
        $inventoryModel = new \App\Models\Inventory();
        $inventoryData = $inventoryModel->getCharacterInventory($_SESSION['character_id']);

        $potions = [];
        if (!empty($inventoryData['inventory'])) {
            foreach ($inventoryData['inventory'] as $item) {
                if (isset($item['type']) && $item['type'] === 'consumable') {
                    $id = $item['item_id'];
                    if (!isset($potions[$id])) {
                        $potions[$id] = $item;
                        $potions[$id]['count'] = 1;
                    } else {
                        $potions[$id]['count']++;
                    }
                }
            }
        }

        // Chargement des compétences débloquées
        $skillModel = new \App\Models\Skill();
        $skills = $skillModel->getUnlockedSkills($_SESSION['character_id']);

        // Réponse AJAX pour charger le contenu du combat sans recharger la page
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
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

        // Fallback redirection classique
        header('Location: /game');
        exit;
    }
    /**
     * Gère le lancer de dé (d20) via AJAX.
     * La valeur est stockée en session pour être utilisée lors de l'action suivante.
     */
    public function rollDice()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json; charset=utf-8');

        if (!empty($_POST['diceRoll'])) {
            $diceRoll = (int) $_POST['diceRoll'];
            $_SESSION['diceRoll'] = $diceRoll;

            echo json_encode([
                "success" => true,
                "value" => $diceRoll
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Aucune valeur reçue"
            ]);
        }
    }

    /**
     * Exécute un tour de combat complet (Action Joueur + Action Monstre).
     * Gère les effets de statut, les attaques et les sorts.
     */
    public function performAction()
    {
        header('Content-Type: application/json; charset=utf-8');
        ob_clean();

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

        // Gestion des effets de statut (Poison, étourdissement, etc.)
        $statusService = new StatusEffectService();
        $statusResult = $statusService->processTurn($charId);

        $statusMessages = $statusResult['messages'];
        $preventAction = $statusResult['prevent_action'];

        $playerMessage = ["", 0];

        // Tour du Joueur
        if ($preventAction) {
            $playerMessage[0] = implode("<br>", $statusMessages) . "<br><strong>Vous ne pouvez pas agir !</strong>";
        } else {
            $skillId = $_POST['skill_id'] ?? null;
            // Exécution de l'action du joueur
            $playerMessage = $combat->playerTurn($action, $skillId);

            if (!empty($statusMessages)) {
                $playerMessage[0] = implode("<br>", $statusMessages) . "<br>" . $playerMessage[0];
            }
        }

        // Tour du Monstre (seulement si le joueur est encore vivant)
        $monsterMessage = null;
        if ($combat->getJoueur()->isAlive() && $combat->isMonsterAlive() && !$preventAction) {
            $monsterMessage = $combat->monsterTurn();
        }

        // Vérifier si le combat est terminé après les deux tours
        if (!$combat->isMonsterAlive() || !$combat->getJoueur()->isAlive()) {
            $combat->endCombat();
        }

        // Reset des bonus temporaires de défense
        if (isset($_SESSION['initialDefence'])) {
            $combat->getJoueur()->setArmorClass($_SESSION['initialDefence']);
            unset($_SESSION['initialDefence']);
        }

        // Consommation du jet de dé
        unset($_SESSION['diceRoll']);

        // Gestion de la Victoire
        $rewards = [];
        if (!$combat->isMonsterAlive()) {
            $monsterModel = $combat->getMonster();

            // Calcul de l'XP et de l'or
            $calc = $this->calculateRewards($combat, $monsterModel);

            $charModel = $combat->getJoueur();

            $saveChar = new Character();
            $saveChar->findById($_SESSION['character_id']);
            $xpRes = $saveChar->addXp($calc['xp']);
            $saveChar->addGold($calc['gold']);

            // Génération du butin
            $loot = $this->generateLoot($_SESSION['character_id'], $monsterModel);

            // Mise à jour des quêtes ("Tuer X monstres")
            $pqModel = new \App\Models\PlayerQuest();
            $questUpdates = $pqModel->onMonsterKilled($_SESSION['character_id'], $monsterModel->getId());

            // Mise à jour des quêtes quotidiennes (KILL_MONSTERS)
            $dailyQuestModel = new \App\Models\DailyQuest();
            $dailyQuestUpdates = $dailyQuestModel->onMonsterKilled($_SESSION['character_id'], $monsterModel->getId());

            // Mise à jour des quêtes quotidiennes (COLLECT_GOLD)
            if ($calc['gold'] > 0) {
                $dailyQuestModel->onGoldCollected($_SESSION['character_id'], $calc['gold']);
            }

            // Reload Character to get final stats (including potential quest rewards)
            $saveChar->findById($_SESSION['character_id']);

            $rewards = [
                'xp' => $calc['xp'],
                'gold' => $calc['gold'],
                'levels_gained' => $xpRes['levels_gained'], // Keep this from monster kill for notification
                'current_xp' => $saveChar->getExperience(),
                'max_xp' => $saveChar->getXpForNextLevel(),
                'current_level' => $saveChar->getLevel(),
                'total_gold' => $saveChar->getGold(),
                'loot' => $loot,
                'quests' => $questUpdates,
                'daily_quest_updates' => $dailyQuestUpdates
            ];

            // Logs de victoire
            $logger = new LoggerService();
            $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'COMBAT_WIN', [
                'monster_id' => $monsterModel->getId(),
                'xp_gained' => $calc['xp'],
                'gold_gained' => $calc['gold'],
                'loot' => $loot
            ]);

            // Détermination de l'ID d'histoire pour le retour au jeu
            if (isset($_GET['story_id']) || isset($_POST['return_story_id']) || isset($_SESSION['combat_story_id'])) {
                $storyId = $_GET['story_id'] ?? ($_POST['return_story_id'] ?? ($_SESSION['combat_story_id'] ?? null));
            }

            if (!isset($storyId)) {
                $progModel = new \App\Models\StoryProgress();
                $active = $progModel->getActiveStory($_SESSION['character_id']);
                if ($active)
                    $storyId = $active['story_id'];
            }

            // Marquage du monstre comme "tué" dans la session pour ne pas le re-combattre
            if (isset($storyId)) {
                $progModel = new \App\Models\StoryProgress();
                $progress = $progModel->getProgress($_SESSION['character_id'], $storyId);

                if ($progress) {
                    $nodeId = $progress['current_node_id'];
                    $monsterId = $monsterModel->getId();

                    $sessionKey = 'killed_monsters_' . $_SESSION['character_id'] . '_' . $nodeId;
                    if (!isset($_SESSION[$sessionKey])) {
                        $_SESSION[$sessionKey] = [];
                    }
                    if (!in_array($monsterId, $_SESSION[$sessionKey])) {
                        $_SESSION[$sessionKey][] = $monsterId;
                    }
                }
            }
        }

        // Nettoyage des messages pour éviter les erreurs JSON
        $pMsg = $playerMessage[0] ?? '';
        $pMsg = str_replace(["\r\n", "\r", "\n"], ' ', $pMsg);
        $mMsg = ($monsterMessage ? $monsterMessage[0] : null);
        if ($mMsg) {
            $mMsg = str_replace(["\r\n", "\r", "\n"], ' ', $mMsg);
        }

        // Gestion de la Défaite (Joueur mort)
        if (!$combat->isAlive($combat->getJoueur())) {
            $monsterModel = $combat->getMonster();
            $logger = new LoggerService();
            $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'COMBAT_LOSS', [
                'monster_id' => $monsterModel->getId()
            ]);

            if ($_SESSION['is_ironman']) {
                $charModel = new Character();
                $charModel->deleteById($charId);

                echo json_encode([
                    "success" => true,
                    "player" => $pMsg,
                    "monster" => $mMsg,
                    "playerHp" => 0,
                    "win" => false,
                    "gameOver" => true,
                    "ironmanDeath" => true,
                    "redirect" => "/personnage/create?error=permadeath"
                ]);
                unset($_SESSION['combat']);
                exit;
            }

            // Défaite en mode non-Ironman : le joueur perd le combat mais survit
            echo json_encode([
                "success" => true,
                "player" => $pMsg,
                "monster" => $mMsg,
                "playerHp" => 0,
                "monsterHp" => $combat->getMonster()->getVitality(),
                "win" => false,
                "gameOver" => true,
                "ironmanDeath" => false
            ]);
            unset($_SESSION['combat']);
            exit;
        }

        // Récupération des stats actuelles du joueur (avec items et compétences passives)
        $effectiveStats = CharacterStatsService::getEffectiveStats($_SESSION['character_id']);

        // Apply Combat Session Buffs (Skill Buffs)
        if (isset($_SESSION['combat_buffs'])) {
            $effectiveStats['strength'] += ($_SESSION['combat_buffs']['str'] ?? 0);
            $effectiveStats['dexterity'] += ($_SESSION['combat_buffs']['dex'] ?? 0);
        }

        // Recalculate derived stats based on buffered attributes
        $dexMod = floor(($effectiveStats['dexterity'] - 10) / 2);
        $effectiveStats['attack'] = $effectiveStats['strength'] + max(0, $dexMod);
        $effectiveStats['defense'] = 10 + $dexMod;

        // Apply Temporary Defense Bonus (Defend Action)
        if (isset($_SESSION['temp_defense_bonus'])) {
            $effectiveStats['defense'] += $_SESSION['temp_defense_bonus'];
        }

        $playerStats = [
            'attack' => $effectiveStats['attack'],
            'defense' => $effectiveStats['defense'],
            'strength' => $effectiveStats['strength'],
            'intelligence' => $effectiveStats['intelligence'],
            'dexterity' => $effectiveStats['dexterity'],
            'vitality' => $effectiveStats['vitality']
        ];

        echo json_encode([
            "success" => true,
            "player" => $pMsg,
            "monster" => $mMsg,
            "playerHp" => $combat->getPlayerHp(),
            "monsterHp" => $combat->getMonster()->getVitality(),
            "playerStats" => $playerStats,
            "win" => (!$combat->isMonsterAlive()),
            "newTurn" => ($combat->isMonsterAlive() && !$preventAction),
            "damageM" => $playerMessage[1] ?? false,
            "damageJ" => $monsterMessage ? $monsterMessage[1] : false,
            "rewards" => $rewards
        ]);
        exit;
    }

    /**
     * Calcule l'XP et l'Or gagnés lors du combat.
     * Prend en compte la force/vitalité du monstre et la difficulté du jeu.
     */
    private function calculateRewards(Combat $combat, Monster $monsterModel)
    {
        $str = $monsterModel->getStrength();
        $vit = $monsterModel->getVitality();

        $xp = 50 + ($str * 2) + ($vit * 2);
        $gold = rand(5, 15) + $str;

        $difficulty = $_SESSION['current_difficulty'] ?? 'NORMAL';
        $diffService = new DifficultyService();
        $xpModifier = $diffService->getXpModifier($difficulty);

        return ['xp' => (int) ($xp * $xpModifier), 'gold' => (int) $gold];
    }

    /**
     * Génère un butin aléatoire basé sur la chance et la difficulté.
     * Ajoute l'objet directement à l'inventaire du personnage.
     */
    private function generateLoot($characterId, $monsterModel)
    {
        $db = Database::getInstance()->getConnection();
        $inventoryModel = new Inventory();
        $loot = [];

        $difficulty = $_SESSION['current_difficulty'] ?? 'NORMAL';
        $diffService = new DifficultyService();
        $lootModifier = $diffService->getLootChanceModifier($difficulty);
        $baseChance = 30;

        if (rand(1, 100) <= ($baseChance * $lootModifier)) {
            $res = $db->query("SELECT id, name FROM items ORDER BY RAND() LIMIT 1");
            if ($res && $row = $res->fetch_assoc()) {
                $add = $inventoryModel->addItem($characterId, $row['id']);
                if ($add['success'])
                    $loot[] = $row['name'];
            }
        }
        return $loot;
    }

    /**
     * Termine proprement la session de combat.
     */
    public function endCombat()
    {
        unset($_SESSION['combat']);
    }

}
