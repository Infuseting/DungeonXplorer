<?php
require_once __DIR__ . '/vendor/autoload.php';
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {}

$db = \App\Config\Database::getInstance()->getConnection();
$res = $db->query('DESCRIBE story_node_connections');
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
