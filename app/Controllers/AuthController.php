<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\TokenService;

class AuthController
{
    private $tokenService;

    public function __construct()
    {
        $this->tokenService = new TokenService();
    }

    public function register()
    {
        // If already logged in, redirect
        if ($this->checkAuth()) {
            header('Location: /personnage');
            exit;
        }
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function login()
    {
        // If already logged in, redirect
        if ($this->checkAuth()) {
            header('Location: /personnage');
            exit;
        }
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function registerPost()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($password !== $confirmPassword) {
            header('Location: /register?error=passwords_do_not_match');
            exit;
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            header('Location: /register?error=email_already_exists');
            exit;
        }

        if ($userModel->create($username, $email, $password)) {
            header('Location: /login?success=registration_successful');
            exit;
        } else {
            header('Location: /register?error=registration_failed');
            exit;
        }
    }

    public function loginPost()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && $userModel->verifyPassword($password, $user['password'])) {
            
            // Generate Tokens
            $accessToken = $this->tokenService->generateAccessToken($user['id']);
            $refreshToken = $this->tokenService->generateRefreshToken();
            
            // Store Refresh Token (hashed)
            // Using 'selector' as a unique ID for the token (random hex)
            // Using 'validator' as the actual secret token
            $selector = bin2hex(random_bytes(12));
            $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30); // 30 days
            
            $userModel->createRememberToken($user['id'], $selector, $refreshToken, $expiresAt);

            // Set Cookies
            // Access Token: 15 min
            setcookie('access_token', $accessToken, [
                'expires' => time() + 900,
                'path' => '/',
                'secure' => false, // Set to true in production (HTTPS)
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            // Refresh Token: 30 days (selector:token)
            setcookie('refresh_token', $selector . ':' . $refreshToken, [
                'expires' => time() + 86400 * 30,
                'path' => '/',
                'secure' => false, // Set to true in production
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            // Set session for username display (optional, but useful for UI)
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id']; // Keep for legacy checks if any

            header('Location: /personnage');
            exit;
        } else {
            header('Location: /login?error=invalid_credentials');
            exit;
        }
    }

    public function logout()
    {
        // Remove Refresh Token from DB
        if (isset($_COOKIE['refresh_token'])) {
            $parts = explode(':', $_COOKIE['refresh_token']);
            if (count($parts) === 2) {
                $userModel = new User();
                $userModel->deleteToken($parts[0]);
            }
        }

        // Clear Cookies
        setcookie('access_token', '', time() - 3600, '/');
        setcookie('refresh_token', '', time() - 3600, '/');
        
        // Destroy Session
        session_destroy();

        header('Location: /login');
        exit;
    }

    private function checkAuth()
    {
        // Simple check for redirection purposes (Middleware handles real auth)
        return isset($_COOKIE['access_token']) || isset($_COOKIE['refresh_token']);
    }
}
