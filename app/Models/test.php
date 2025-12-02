<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use App\Models\Monster;
use App\Models\Character;
use App\Models\Combat;
use App\Models\Stats;



$monster = new Monster();
$monster->findById(1);

$var = $monster->toString();
echo $var;


$char = new Character(1);
$char->findById(19);
$var2 = $char->toString();
echo $var2;

$charAC = $char->getArmorClass();
echo "Character Armor Class: " . $charAC . "\n";
$charPC = $char->getAttaqueClass();
echo "Character Attaque Class: " . $charPC . "\n";

$charInventory = $char->getInventory();



?>