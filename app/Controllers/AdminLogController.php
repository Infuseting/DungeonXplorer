<?php

namespace App\Controllers;

use App\Services\LoggerService;

class AdminLogController
{
    private $logger;

    public function __construct() {
        $this->logger = new LoggerService();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $filters = [
            'category' => $_GET['category'] ?? null,
            'action_type' => $_GET['action_type'] ?? null,
            'user_id' => $_GET['user_id'] ?? null,
            'character_id' => $_GET['character_id'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
        ];

        // Clean empty filters
        $filters = array_filter($filters, function($v) { return $v !== '' && $v !== null; });

        $logs = $this->logger->getLogs($filters, $limit, $offset);
        $totalLogs = $this->logger->getTotalLogs($filters);
        $totalPages = ceil($totalLogs / $limit);

        require_once __DIR__ . '/../Views/admin/logs/index.php';
    }
}
