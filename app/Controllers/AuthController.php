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
        // Check Access Token validity
        if (isset($_COOKIE['access_token'])) {
            if ($this->tokenService->validateToken($_COOKIE['access_token'])) {
                return true;
            }
        }

        // Check Refresh Token validity (simplified check, just existence isn't enough but full validation might be heavy here, 
        // ideally we trust the middleware to handle refresh, but for redirection from login page, we want to be sure)
        // If access token is invalid but refresh token exists, we let them go to /personnage where middleware will handle the refresh.
        // BUT, if middleware fails refresh, it redirects back to login.
        // So here, we should only redirect if we are reasonably sure they are logged in.
        
        // Actually, the safest bet to avoid loops is: 
        // If we are on login page, only redirect if Access Token is VALID.
        // If Access Token is invalid/missing, stay on login page (even if Refresh Token exists).
        // Why? Because if Refresh Token is valid, the user can just click "Login" (or we could auto-login, but that's complex).
        // Wait, if Refresh Token is valid, they ARE logged in.
        
        // Let's try to validate the refresh token too if access token fails.
        if (isset($_COOKIE['refresh_token'])) {
             $parts = explode(':', $_COOKIE['refresh_token']);
             if (count($parts) === 2) {
                 // We can't easily validate against DB here without duplicating logic or making a DB call.
                 // To break the loop, let's assume if Access Token is invalid, we are NOT authenticated for the purpose of the Login page redirection.
                 // This means if your access token expired, you see the login page. 
                 // BUT, if you try to go to /personnage, the middleware will refresh it and let you in.
                 // This is a better UX failure mode than a loop.
                 return false;
             }
        }

        return false;
    }
}
