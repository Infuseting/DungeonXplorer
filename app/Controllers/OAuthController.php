<?php

namespace App\Controllers;

use App\Services\OAuthService;
use App\Models\User;
use App\Config\Database;
use App\Services\TokenService;


class OAuthController
{
    private $oauthService;
    private $db;
    private $tokenService;

    public function __construct()
    {
        $this->oauthService = new OAuthService();
        $this->db = Database::getInstance()->getConnection();
        $this->tokenService = new TokenService();
    }

    public function redirect($provider)
    {
        $url = $this->oauthService->getAuthUrl($provider);
        if ($url) {
            header('Location: ' . $url);
            exit;
        } else {
            echo "Provider not supported or configured.";
        }
    }

    public function callback($provider)
    {
        // 1. Get Code
        $code = $_GET['code'] ?? $_POST['code'] ?? null;
        if (!$code) {
           header('Location: /login?error=oauth_error');
           exit;
        }

        // 2. Exchange Code for User Info
        // Since we don't have real keys, this will fail in production.
        $userInfo = $this->oauthService->getUserFromCode($provider, $code);

        if (!$userInfo) {
             // For DEMO purposes only: Mock success if in development and keys are missing
             // In a real app, we would error here.
             header('Location: /login?error=oauth_config_missing');
             exit;
        }

        // 3. Logic: Login or Register or Link
        
        $providerId = $userInfo['id'];
        $email = $userInfo['email'];
        $name = $userInfo['name']; // Fallback for new users
        
        if (!$providerId) {
             header('Location: /login?error=oauth_no_id');
             exit;
        }

        // CASE A: User is already logged in -> Link Account
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            
            // Check if already linked
            $stmt = $this->db->prepare("SELECT id FROM user_social_accounts WHERE provider = ? AND provider_user_id = ?");
            $stmt->bind_param("ss", $provider, $providerId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                 header('Location: /personnage?settings=profile&error=already_linked');
                 exit;
            }
            
            // Link
            $stmt = $this->db->prepare("INSERT INTO user_social_accounts (user_id, provider, provider_user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $provider, $providerId);
            $stmt->execute();
            
            header('Location: /personnage?settings=profile&success=linked');
            exit;
        }

        // CASE B: Not logged in. Check if social account exists.
        $stmt = $this->db->prepare("SELECT user_id FROM user_social_accounts WHERE provider = ? AND provider_user_id = ?");
        $stmt->bind_param("ss", $provider, $providerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists -> Login
            $row = $result->fetch_assoc();
            $userModel = new User();
            $user = $userModel->findById($row['user_id']);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Generate Tokens
            $accessToken = $this->tokenService->generateAccessToken($user['id']);
            $refreshToken = $this->tokenService->generateRefreshToken();
            
            // Store Refresh Token
            $selector = bin2hex(random_bytes(12));
            $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30);
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
            
            header('Location: /game'); // OR /personnage
            exit;
        }

        // CASE C: Social account doesn't exist. Check email.
        if ($email) {
            $userModel = new User();
            $existingUser = $userModel->findByEmail($email);
            
            if ($existingUser) {
                // Link this social account to existing user
                $userId = $existingUser['id'];
                
                $stmt = $this->db->prepare("INSERT INTO user_social_accounts (user_id, provider, provider_user_id) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $userId, $provider, $providerId);
                $stmt->execute();
                
                // Login
                $_SESSION['user_id'] = $existingUser['id'];
                $_SESSION['username'] = $existingUser['username'];

                // Generate Tokens
                $accessToken = $this->tokenService->generateAccessToken($existingUser['id']);
                $refreshToken = $this->tokenService->generateRefreshToken();
                
                // Store Refresh Token
                $selector = bin2hex(random_bytes(12));
                $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30);
                $userModel->createRememberToken($existingUser['id'], $selector, $refreshToken, $expiresAt);

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
                
                header('Location: /game');
                exit;
            }
        }

        // CASE D: New User -> Register
        // Create a random password
        $randomPassword = bin2hex(random_bytes(10));
        
        // Ensure unique username if name is taken
        $baseName = $name ?? explode('@', $email)[0] ?? 'User';
        $username = $baseName;
        $counter = 1;
        
        // Simple check loop (could be optimized)
        $userModel = new User();
        // Since we don't have checkUsername, we'll try insert and catch error or just append random
        // Ideally we check, but for MVP let's append random suffix if it looks common
        // Or cleaner: create method in User model. Let's assume unique for now or append random.
        
        // Create User
        $userModel->create($username . rand(1000,9999), $email, $randomPassword); 
        // Note: userModel->create doesn't return ID. We need to fetch by email.
        
        $newUser = $userModel->findByEmail($email);
        $userId = $newUser['id'];
        
        // Link
        $stmt = $this->db->prepare("INSERT INTO user_social_accounts (user_id, provider, provider_user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $provider, $providerId);
        $stmt->execute();
        
        // Login
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $newUser['username'];

        // Generate Tokens
        $accessToken = $this->tokenService->generateAccessToken($userId);
        $refreshToken = $this->tokenService->generateRefreshToken();
        
        // Store Refresh Token
        $selector = bin2hex(random_bytes(12));
        $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30);
        $userModel->createRememberToken($userId, $selector, $refreshToken, $expiresAt);

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
        
        header('Location: /personnage/create'); // New users go to character creation
        exit;
    }

    public function unlink($provider)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Remove from DB
        $stmt = $this->db->prepare("DELETE FROM user_social_accounts WHERE user_id = ? AND provider = ?");
        $stmt->bind_param("is", $userId, $provider);
        $stmt->execute();

        header('Location: /personnage?settings=profile'); // Redirect back to settings
        exit;
    }
    
    // API Endpoint to get connected accounts
    public function getConnectedAccounts() {
         if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT provider FROM user_social_accounts WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $accounts = [];
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row['provider'];
        }
        
        echo json_encode(['accounts' => $accounts]);
    }
}
