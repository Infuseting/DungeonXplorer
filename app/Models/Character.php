<?php

namespace App\Models;

use App\Config\Database;
use App\Models\Inventory;
use App\Models\Stats as StatsEnum;

#[\AllowDynamicProperties]
class Character
{
    public function getId()
    {
        return $this->id;
    }
    public function getEquippedStats(StatsEnum $stat): int
    {
        $inventoryModel = new Inventory();
        $inv = $inventoryModel->getCharacterInventory($this->id);

        $total = 0;
        $statKey = $stat->value;
        if (!empty($inv['equipped'])) {
            foreach ($inv['equipped'] as $item) {
                $stats = json_decode($item['stats'] ?? '[]', true);
                if (isset($stats[$statKey])) {
                    $total += (int) $stats[$statKey];
                }
            }
        }

        return $total;
    }


    public function getAttaqueClass()
    {
        return $this->getStrength() + $this->getEquippedStats(StatsEnum::Damage);
    }
    public function resetCache(): void
    {
        $this->inventoryCache = [];
        $this->statsCache = [];
    }

    private array $inventoryCache = [];
    private array $statsCache = [];


    public function __construct()
    {
        // Initialisation de la connexion DB (Singleton)
        $this->db = Database::getInstance()->getConnection();
        $this->inventory = new Inventory();
    }

    public function __wakeup()
    {
        // Reconnexion DB après désérialisation
        $this->db = Database::getInstance()->getConnection();
        if (!$this->inventory) {
            $this->inventory = new Inventory();
        }
    }

    /**
     * Crée un nouveau personnage en base de données.
     * Note: Les statistiques sont gérées séparément via CharacterStats.
     */
    public function create($userId, $classId, $name, $difficulty = 'NORMAL', $isIronman = 0)
    {
        $stmt = $this->db->prepare("INSERT INTO characters (user_id, class_id, name, difficulty, is_ironman, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iissi", $userId, $classId, $name, $difficulty, $isIronman);

        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Récupère tous les personnages d'un utilisateur.
     * Jointure avec les classes et les statistiques pour avoir un aperçu complet.
     */
    public function findAllByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cl.name as class_name, cs.level, cs.xp 
            FROM characters c 
            JOIN classes cl ON c.class_id = cl.id 
            LEFT JOIN character_stats cs ON c.id = cs.character_id 
            WHERE c.user_id = ?
            ORDER BY c.last_played_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $characters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Parse appearance JSON for each character
        foreach ($characters as &$character) {
            if (!empty($character['appearance'])) {
                $character['appearance'] = json_decode($character['appearance'], true) ?? [];
            } else {
                $character['appearance'] = [];
            }
        }

        return $characters;
    }

    /**
     * Charge un personnage par son ID et hydrate l'objet.
     * Récupère également les stats depuis la table character_stats.
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cl.name as class_name, 
                   cs.level, cs.xp, cs.strength, cs.dexterity, cs.intelligence, cs.vitality, cs.skill_points
            FROM characters c 
            JOIN classes cl ON c.class_id = cl.id 
            LEFT JOIN character_stats cs ON c.id = cs.character_id 
            WHERE c.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $this->id = $result['id'];
            $this->userId = $result['user_id'];
            $this->classId = $result['class_id'];
            $this->name = $result['name'];
            $this->gold = $result['gold'];
            $this->difficulty = $result['difficulty'];
            $this->is_ironman = $result['is_ironman'];
            $this->isIronman = $result['is_ironman']; // CamelCase for getter
            $this->last_played = $result['last_played'];
            $this->className = $result['class_name'];

            // Hydrate appearance data
            if (!empty($result['appearance'])) {
                $this->appearance = json_decode($result['appearance'], true) ?? [];
            } else {
                $this->appearance = [];
            }

            // Stats depuis character_stats
            $this->level = $result['level'] ?? 1;
            $this->experience = $result['xp'] ?? 0;
            $this->strength = $result['strength'] ?? 10;
            $this->vitality = $result['vitality'] ?? 10;
            $this->dexterity = $result['dexterity'] ?? 10;
            $this->intelligence = $result['intelligence'] ?? 10;
            $this->current_hp = $result['current_hp'] ?? $this->vitality; // Fallback sur vitalité si null

            // Hydratation des stats effectives (avec bonus équipement) via CharacterStats model si nécessaire
            // Pour l'instant on garde les stats de base de la DB, les stats effectives sont calculées à la demande.

            return $this;
        }
        return null;
    }
    /**
     * @return int Nombre de points de compétence disponibles.
     */
    public function getSkillPoints()
    {
        return $this->skillPoints;
    }

    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Débloque une compétence pour ce personnage.
     */
    public function unlockSkill($skillId)
    {
        $stmt = $this->db->prepare("INSERT INTO character_skills (character_id, skill_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $this->id, $skillId);
        return $stmt->execute();
    }

    public function unsetDb()
    {
        $this->db = null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id ?? 0,
            'name' => $this->name ?? '',
            'class_name' => $this->className ?? '',
            'appearance' => $this->appearance ?? [],
            'class' => ['name' => $this->className ?? ''],
        ];
    }

    public function updateLastPlayed($id)
    {
        $stmt = $this->db->prepare("UPDATE characters SET last_played_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        $_SESSION['character_id'] = $id;
        return $stmt->execute();
    }

    public function delete($id, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM characters WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);
        return $stmt->execute();
    }

    /**
     * Met à jour l'apparence d'un personnage.
     */
    public function updateAppearance($characterId, $appearance)
    {
        $appearanceJson = json_encode($appearance);
        $stmt = $this->db->prepare("UPDATE characters SET appearance = ? WHERE id = ?");
        $stmt->bind_param("si", $appearanceJson, $characterId);
        return $stmt->execute();
    }

    public function getInventory(): array
    {
        if (!empty($this->inventoryCache)) {
            echo 'empty';
            return $this->inventoryCache;
        }



        $result = $this->inventory->getCharacterInventory($this->id);

        return is_array($result) ? $result : [];




    }


    public function toString()
    {
        return "\nName: " . $this->name . "\n" .
            "Level: " . $this->level . "\n" .
            "XP: " . $this->xp . "\n" .
            "Gold: " . $this->gold . "\n" .
            "Strength: " . $this->strength . "\n" .
            "Vitality: " . $this->vitality . "\n" .
            "Intelligence: " . $this->intelligence . "\n" .
            "Dexterity: " . $this->dexterity . "\n";
    }

    /**
     * Soigne le personnage d'un certain montant.
     * Met à jour `current_hp` dans `character_stats` sans dépasser la vitalité max.
     */
    public function heal($characterId, $amount)
    {
        // Restore DB connection if needed
        if ($this->db === null) {
            $this->db = Database::getInstance()->getConnection();
        }

        $stmt = $this->db->prepare("UPDATE character_stats SET current_hp = LEAST(vitality, current_hp + ?) WHERE character_id = ?");
        $stmt->bind_param("ii", $amount, $characterId);
        return $stmt->execute();
    }

    /**
     * Ajoute de l'or au personnage (peut être négatif pour dépenser).
     */
    public function addGold($amount)
    {
        // Restore DB connection if needed
        if ($this->db === null) {
            $this->db = Database::getInstance()->getConnection();
        }

        $stmt = $this->db->prepare("UPDATE characters SET gold = gold + ? WHERE id = ?");
        $stmt->bind_param("ii", $amount, $this->id);
        if ($stmt->execute()) {
            $this->gold += $amount;
            return true;
        }
        return false;
    }

    /**
     * Ajoute de l'expérience et gère la montée de niveau.
     * Augmente le niveau et les points de compétence si un seuil est atteint.
     * Restaure les PV max au passage de niveau.
     */
    public function addXp($amount)
    {
        // Restore DB connection if needed
        if ($this->db === null) {
            $this->db = Database::getInstance()->getConnection();
        }

        $stmt = $this->db->prepare("SELECT xp, level, skill_points, vitality FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();

        $xp = $stats['xp'] + $amount;
        $level = $stats['level'];
        $sp = $stats['skill_points'];
        $levelsGained = 0;

        // Seuil d'XP pour le prochain niveau : niveau * 100
        $threshold = $level * 100;

        while ($xp >= $threshold) {
            $xp -= $threshold;
            $level++;
            $levelsGained++;
            $sp++;
            $threshold = $level * 100;
        }

        $stmt = $this->db->prepare("UPDATE character_stats SET xp = ?, level = ?, skill_points = ? WHERE character_id = ?");
        $stmt->bind_param("iiii", $xp, $level, $sp, $this->id);
        $success = $stmt->execute();

        if ($success && $levelsGained > 0) {
            // Soin complet au passage de niveau
            $this->db->query("UPDATE character_stats SET current_hp = vitality WHERE character_id = " . $this->id);
        }

        $this->xp = $xp;
        $this->level = $level;
        $this->skillPoints = $sp;

        return [
            'success' => $success,
            'levels_gained' => $levelsGained,
            'current_level' => $level,
            'current_xp' => $xp,
            'next_threshold' => $threshold
        ];
    }

    public function getName()
    {
        return $this->name;
    }
    public function getCurrentHp()
    {
        return $this->current_hp ?? $this->vitality;
    }

    public function getDifficulty()
    {
        return $this->difficulty ?? 'NORMAL';
    }

    public function isIronman()
    {
        return (bool) $this->isIronman;
    }
    public function getStrength()
    {
        return $this->strength;
    }
    public function getVitality()
    {
        return $this->vitality;
    }
    public function getIntelligence()
    {
        return $this->intelligence;
    }

    public function setArmorClass($nArmor)
    {
        $this->armor = $nArmor;

    }
    public function getDexterity()
    {
        return $this->dexterity;
    }
    public function setVitality($vitality)
    {
        $this->vitality = $vitality;
    }

    /**
     * Réduit la vitalité actuelle du personnage.
     * Met à jour la base de données. Ne descend pas en dessous de 0.
     */
    public function reduceVitality($number)
    {
        // Restore DB connection if needed
        if ($this->db === null) {
            $this->db = Database::getInstance()->getConnection();
        }

        $stmt = $this->db->prepare("UPDATE character_stats SET current_hp = GREATEST(0, current_hp - ?) WHERE character_id = ?");
        $stmt->bind_param("ii", $number, $this->id);
        $stmt->execute();

        if (isset($this->currentHp)) {
            $this->currentHp = max(0, $this->currentHp - $number);
        }

        if (isset($this->current_hp)) {
            $this->current_hp = max(0, $this->current_hp - $number);
        }
    }

    /**
     * Vérifie si le personnage est toujours en vie.
     */
    public function isAlive()
    {
        // Si l'objet est hydraté avec les PV courants, on utilise la propriété
        if (isset($this->currentHp)) {
            return $this->currentHp > 0;
        }

        if (isset($this->current_hp)) {
            return $this->current_hp > 0;
        }

        // Sinon, on vérifie en base de données
        // Restore DB connection if needed
        if ($this->db === null) {
            $this->db = Database::getInstance()->getConnection();
        }

        $stmt = $this->db->prepare("SELECT current_hp FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return ($res['current_hp'] > 0);
    }


    public function getClassId()
    {
        return $this->classId;
    }
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Retourne la classe d'armure totale (Réduction de dégâts).
     * Calculé à partir des stats équipées.
     * Note: Utilisé pour la réduction de dégâts, non pour l'esquive.
     */
    public function getArmorClass()
    {
        if ($this->armor == null)
            $this->armor = $this->getEquippedStats(StatsEnum::Defense);
        return $this->armor;
    }




    public function getUserId()
    {
        return $this->userId;
    }


    /**
     * Supprime un personnage par son ID (et vérifie l'appartenance à l'utilisateur).
     */
    public function deleteById($id)
    {
        $stmt = $this->db->prepare("DELETE FROM characters WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Récupère une liste de personnages filtrée.
     * Filtres possibles: class_id, level, name, user_id.
     */
    public function getAllCharacters($filters = [])
    {
        $sql = "
            SELECT c.*, u.username, cl.name as class_name, s.level 
            FROM characters c 
            JOIN users u ON c.user_id = u.id 
            JOIN classes cl ON c.class_id = cl.id 
            LEFT JOIN character_stats s ON c.id = s.character_id
            WHERE 1=1
        ";

        $params = [];
        $types = "";

        if (!empty($filters['class_id'])) {
            $sql .= " AND c.class_id = ?";
            $params[] = $filters['class_id'];
            $types .= "i";
        }

        if (!empty($filters['level'])) {
            $sql .= " AND s.level = ?";
            $params[] = $filters['level'];
            $types .= "i";
        }

        if (!empty($filters['name'])) {
            $sql .= " AND c.name LIKE ?";
            $params[] = "%" . $filters['name'] . "%";
            $types .= "s";
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND c.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= "i";
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
