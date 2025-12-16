<?php

namespace App\Middleware;

class AdminMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Get user from database to check role
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo "Access Denied: Admin privileges required.";
            exit;
        }
    }
}
