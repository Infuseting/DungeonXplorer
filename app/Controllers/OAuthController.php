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
                $code = $_GET['code'] ?? $_POST['code'] ?? null;
        if (!$code) {
           header('Location: /login?error=oauth_error');
           exit;
        }

                        $userInfo = $this->oauthService->getUserFromCode($provider, $code);

        if (!$userInfo) {
                                       header('Location: /login?error=oauth_config_missing');
             exit;
        }

                
        $providerId = $userInfo['id'];
        $email = $userInfo['email'];
        $name = $userInfo['name'];         
        if (!$providerId) {
             header('Location: /login?error=oauth_no_id');
             exit;
        }

                if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            
                        $stmt = $this->db->prepare("SELECT id FROM user_social_accounts WHERE provider = ? AND provider_user_id = ?");
            $stmt->bind_param("ss", $provider, $providerId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                 header('Location: /personnage?settings=profile&error=already_linked');
                 exit;
            }
            
                        $stmt = $this->db->prepare("INSERT INTO user_social_accounts (user_id, provider, provider_user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $provider, $providerId);
            $stmt->execute();
            
            header('Location: /personnage?settings=profile&success=linked');
            exit;
        }

                $stmt = $this->db->prepare("SELECT user_id FROM user_social_accounts WHERE provider = ? AND provider_user_id = ?");
        $stmt->bind_param("ss", $provider, $providerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
            $userModel = new User();
            $user = $userModel->findById($row['user_id']);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

                        $accessToken = $this->tokenService->generateAccessToken($user['id']);
            $refreshToken = $this->tokenService->generateRefreshToken();
            
                        $selector = bin2hex(random_bytes(12));
            $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30);
            $userModel->createRememberToken($user['id'], $selector, $refreshToken, $expiresAt);

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
            
            header('Location: /game');             exit;
        }

                if ($email) {
            $userModel = new User();
            $existingUser = $userModel->findByEmail($email);
            
            if ($existingUser) {
                                $userId = $existingUser['id'];
                
                $stmt = $this->db->prepare("INSERT INTO user_social_accounts (user_id, provider, provider_user_id) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $userId, $provider, $providerId);
                $stmt->execute();
                
                                $_SESSION['user_id'] = $existingUser['id'];
                $_SESSION['username'] = $existingUser['username'];

                                $accessToken = $this->tokenService->generateAccessToken($existingUser['id']);
                $refreshToken = $this->tokenService->generateRefreshToken();
                
                                $selector = bin2hex(random_bytes(12));
                $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30);
                $userModel->createRememberToken($existingUser['id'], $selector, $refreshToken, $expiresAt);

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

                        $randomPassword = bin2hex(random_bytes(10));
        
                $baseName = $name ?? explode('@', $email)[0] ?? 'User';
        $username = $baseName;
        $counter = 1;
        
                $userModel = new User();
                                
                $userModel->create($username . rand(1000,9999), $email, $randomPassword); 
                
        $newUser = $userModel->findByEmail($email);
        $userId = $newUser['id'];
        
                $stmt = $this->db->prepare("INSERT INTO user_social_accounts (user_id, provider, provider_user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $provider, $providerId);
        $stmt->execute();
        
                $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $newUser['username'];

                $accessToken = $this->tokenService->generateAccessToken($userId);
        $refreshToken = $this->tokenService->generateRefreshToken();
        
                $selector = bin2hex(random_bytes(12));
        $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30);
        $userModel->createRememberToken($userId, $selector, $refreshToken, $expiresAt);

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
        
        header('Location: /personnage/create');         exit;
    }

    public function unlink($provider)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
                $stmt = $this->db->prepare("DELETE FROM user_social_accounts WHERE user_id = ? AND provider = ?");
        $stmt->bind_param("is", $userId, $provider);
        $stmt->execute();

        header('Location: /personnage?settings=profile');         exit;
    }
    
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
