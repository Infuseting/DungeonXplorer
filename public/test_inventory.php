<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Test inventory loading
session_start();
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Models/Inventory.php';

// Simulate a logged-in character
$_SESSION['character_id'] = 1; // Change this to a valid character ID

$inventoryModel = new \App\Models\Inventory();
try {
    $inventory = $inventoryModel->getCharacterInventory($_SESSION['character_id']);
    echo "<pre>";
    print_r($inventory);
    echo "</pre>";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
    echo "<br>Stack trace:<br>";
    echo nl2br($e->getTraceAsString());
}
