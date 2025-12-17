<?php

namespace App\Middleware;
use App\Models\User;
class AdminMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

                $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        if (!$user || $user['role'] !== 'admin') {
            header('Location: /');
            exit;
        }
    }
}
