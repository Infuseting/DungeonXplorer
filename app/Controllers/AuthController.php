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
            header('Location: /login?success=registered');
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
            $selector = bin2hex(random_bytes(12));
            $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30); // 30 days
            
            $userModel->createRememberToken($user['id'], $selector, $refreshToken, $expiresAt);

            // Set Cookies
            setcookie('access_token', $accessToken, [
                'expires' => time() + 900,
                'path' => '/',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            setcookie('refresh_token', $selector . ':' . $refreshToken, [
                'expires' => time() + 86400 * 30,
                'path' => '/',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

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

    public function forgotPassword()
    {
        require __DIR__ . '/../Views/auth/forgot_password.php';
    }

    public function forgotPasswordPost()
    {
        $email = $_POST['email'] ?? '';
        $code = $_POST['code'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            header('Location: /forgot-password?error=passwords_do_not_match');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            header('Location: /forgot-password?error=invalid_email');
            exit;
        }

        $resetModel = new \App\Models\PasswordReset();
        $reset = $resetModel->verify($user['id'], $code);

        if ($reset) {
            if ($userModel->updatePassword($user['id'], $newPassword)) {
                // Invalidate the code
                $resetModel->deleteUserCodes($user['id']);
                header('Location: /login?success=password_reset');
                exit;
            }
        }

        header('Location: /forgot-password?error=invalid_code');
        exit;
    }

    private function checkAuth()
    {
        // Check Access Token validity
        if (isset($_COOKIE['access_token'])) {
            if ($this->tokenService->validateToken($_COOKIE['access_token'])) {
                return true;
            }
        }

        // Check Refresh Token existence
        if (isset($_COOKIE['refresh_token'])) {
             $parts = explode(':', $_COOKIE['refresh_token']);
             if (count($parts) === 2) {
                 return true;
             }
        }

        return false;
    }
}
