<?php

namespace App\Middleware;

use App\Services\TokenService;
use App\Models\User;

/**
 * Middleware d'authentification.
 * Gère la sécurité des routes protégées via JWT (Access Token) et Refresh Token.
 */
class AuthMiddleware
{
    private $tokenService;
    private $userModel;

    public function __construct()
    {
        $this->tokenService = new TokenService();
        $this->userModel = new User();
    }

    /**
     * Exécute la vérification de l'authentification.
     * 1. Vérifie si une session PHP est active.
     * 2. Valide l'Access Token (JWT).
     * 3. Si l'Access Token est invalide/absent, tente d'utiliser le Refresh Token.
     * 4. Si tout échoue, redirige vers la page de connexion.
     */
    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Validation de l'Access Token (JWT)
        if (isset($_COOKIE['access_token'])) {
            $payload = $this->tokenService->validateToken($_COOKIE['access_token']);
            if ($payload) {
                // Si la session PHP est vide mais le token valide, on hydrate la session
                if (!isset($_SESSION['user_id'])) {
                    $user = $this->userModel->findById($payload['user_id']);
                    if ($user) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                    }
                }
                return; // Authentification réussie
            }
        }

        // 2. Tentative de rafraîchissement via Refresh Token (si Access Token expiré)
        if (isset($_COOKIE['refresh_token'])) {
            $parts = explode(':', $_COOKIE['refresh_token']);
            
            if (count($parts) === 2) {
                $selector = $parts[0];
                $validator = $parts[1];

                $user = $this->userModel->findUserByToken($selector, $validator);

                if ($user) {
                    // Rotation du Refresh Token pour la sécurité
                    $this->userModel->deleteToken($selector);

                    $newAccessToken = $this->tokenService->generateAccessToken($user['id']);
                    $newRefreshToken = $this->tokenService->generateRefreshToken();
                    
                    $newSelector = bin2hex(random_bytes(12));
                    $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30);

                    $this->userModel->createRememberToken($user['id'], $newSelector, $newRefreshToken, $expiresAt);

                    // Mise à jour des cookies
                    setcookie('access_token', $newAccessToken, [
                        'expires' => time() + 900, // 15 minutes
                        'path' => '/',
                        'secure' => false, // Mettre à true en PROD (HTTPS)
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);

                    setcookie('refresh_token', $newSelector . ':' . $newRefreshToken, [
                        'expires' => time() + 86400 * 30, // 30 jours
                        'path' => '/',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);

                    // Hydratation de la session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    return; // Authentification restaurée avec succès
                }
            }
        }

        // 3. Echec de l'authentification : Nettoyage et Redirection
        setcookie('access_token', '', time() - 3600, '/');
        setcookie('refresh_token', '', time() - 3600, '/');
        session_destroy();

        header('Location: /login');
        exit;
    }
}
