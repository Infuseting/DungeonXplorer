<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Models\Character;

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = Database::getInstance()->getConnection();

echo "Verifying SP Logic...\n";

// 1. Get Character
$result = $db->query("SELECT id FROM characters LIMIT 1");
if ($result->num_rows === 0) {
    die("No characters found in database. Please create a character first.\n");
}
$row = $result->fetch_assoc();
$charId = $row['id'];
$characterModel = new Character();
$character = $characterModel->findById($charId);

if (!$character) {
    die("Character ID $charId found but could not be loaded. Model error?\n");
}

echo "Character loaded: " . $character->getName() . "\n";

// 2. Backup SP
$originalSP = $character->getSkillPoints();
echo "Original SP: $originalSP\n";

// 3. Set SP to known value for testing (using SQL directly for setup)
$db->query("UPDATE character_stats SET skill_points = 10 WHERE character_id = $charId");
// Re-hydrate
$character = $characterModel->findById($charId);
echo "Test SP set to: " . $character->getSkillPoints() . "\n";

// 4. Test Spend Success
echo "Attempting to spend 4 SP...\n";
$firstSpend = $character->spendSkillPoints(4);
if ($firstSpend && $character->getSkillPoints() == 6) {
    echo "[PASS] Spent 4 SP. Remaining: " . $character->getSkillPoints() . "\n";
} else {
    echo "[FAIL] Spend 4 SP failed. Result: " . ($firstSpend ? 'True' : 'False') . ", SP: " . $character->getSkillPoints() . "\n";
}

// 5. Test Spend Failure (Not enough)
echo "Attempting to spend 10 SP (should fail)...\n";
$failSpend = $character->spendSkillPoints(10);
if (!$failSpend && $character->getSkillPoints() == 6) {
    echo "[PASS] Spend 10 SP failed as expected. Remaining: " . $character->getSkillPoints() . "\n";
} else {
    echo "[FAIL] Spend 10 SP unexpected result. Result: " . ($failSpend ? 'True' : 'False') . ", SP: " . $character->getSkillPoints() . "\n";
}

// 6. Test Spend Exact
echo "Attempting to spend 6 SP...\n";
$exactSpend = $character->spendSkillPoints(6);
if ($exactSpend && $character->getSkillPoints() == 0) {
    echo "[PASS] Spent 6 SP. Remaining: " . $character->getSkillPoints() . "\n";
} else {
    echo "[FAIL] Spend 6 SP failed. Result: " . ($exactSpend ? 'True' : 'False') . ", SP: " . $character->getSkillPoints() . "\n";
}

// 7. Cleanup
echo "Restoring original SP ($originalSP)...\n";
$db->query("UPDATE character_stats SET skill_points = $originalSP WHERE character_id = $charId");

echo "Verification Complete.\n";
