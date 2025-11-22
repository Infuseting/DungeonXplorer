<?php

namespace App\Middleware;

use App\Services\TokenService;
use App\Models\User;

class AuthMiddleware
{
    private $tokenService;
    private $userModel;

    public function __construct()
    {
        $this->tokenService = new TokenService();
        $this->userModel = new User();
    }

    public function handle()
    {
        // Start session if not already started (for flash messages/username)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Check Access Token
        if (isset($_COOKIE['access_token'])) {
            $payload = $this->tokenService->validateToken($_COOKIE['access_token']);
            if ($payload) {
                // Token is valid, user is authenticated
                // Ensure session is synced (optional but good for UI)
                if (!isset($_SESSION['user_id'])) {
                    $user = $this->userModel->findById($payload['user_id']);
                    if ($user) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                    }
                }
                return;
            }
        }

        // 2. Access Token Invalid/Expired -> Try Refresh Token
        if (isset($_COOKIE['refresh_token'])) {
            $parts = explode(':', $_COOKIE['refresh_token']);
            
            if (count($parts) === 2) {
                $selector = $parts[0];
                $validator = $parts[1];

                $user = $this->userModel->findUserByToken($selector, $validator);

                if ($user) {
                    // Refresh Token is valid!
                    // Rotate Tokens: Delete old, create new
                    $this->userModel->deleteToken($selector);

                    // Generate New Tokens
                    $newAccessToken = $this->tokenService->generateAccessToken($user['id']);
                    $newRefreshToken = $this->tokenService->generateRefreshToken();
                    
                    $newSelector = bin2hex(random_bytes(12));
                    $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30);

                    $this->userModel->createRememberToken($user['id'], $newSelector, $newRefreshToken, $expiresAt);

                    // Update Cookies
                    setcookie('access_token', $newAccessToken, [
                        'expires' => time() + 900,
                        'path' => '/',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);

                    setcookie('refresh_token', $newSelector . ':' . $newRefreshToken, [
                        'expires' => time() + 86400 * 30,
                        'path' => '/',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);

                    // Sync Session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    return; // Auth successful via refresh
                }
            }
        }

        // 3. Auth Failed
        // Clear everything
        setcookie('access_token', '', time() - 3600, '/');
        setcookie('refresh_token', '', time() - 3600, '/');
        session_destroy();

        header('Location: /login');
        exit;
    }
}
