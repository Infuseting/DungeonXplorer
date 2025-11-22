<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public function login()
    {
        // If already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function register()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    public function registerPost()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($username) || empty($email) || empty($password)) {
            // TODO: Flash error message
            header('Location: /register?error=missing_fields');
            exit;
        }

        $userModel = new User();
        
        // Check if email already exists
        if ($userModel->findByEmail($email)) {
            header('Location: /register?error=email_exists');
            exit;
        }

        try {
            $userModel->create($username, $email, $password);
            // Auto login after register? Or redirect to login
            header('Location: /login?success=registered');
        } catch (\Exception $e) {
            header('Location: /register?error=server_error');
        }
    }

    public function loginPost()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember-me']);

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && $userModel->verifyPassword($password, $user['password'])) {
            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Handle Remember Me
            if ($rememberMe) {
                $selector = bin2hex(random_bytes(12));
                $validator = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30); // 30 days

                $userModel->createRememberToken($user['id'], $selector, $validator, $expiresAt);

                // Set cookie: selector:validator
                setcookie('remember_me', $selector . ':' . $validator, time() + 86400 * 30, '/', '', false, true);
            }

            header('Location: /');
            exit;
        } else {
            header('Location: /login?error=invalid_credentials');
            exit;
        }
    }

    public function logout()
    {
        // Remove remember me cookie
        if (isset($_COOKIE['remember_me'])) {
            list($selector, ) = explode(':', $_COOKIE['remember_me']);
            $userModel = new User();
            $userModel->removeRememberToken($selector);
            setcookie('remember_me', '', time() - 3600, '/', '', false, true);
        }

        session_destroy();
        header('Location: /login');
        exit;
    }
}
