<?php

namespace App\Services;

class OAuthService
{
    private $config;

    public function __construct()
    {
        $this->config = [
            'google' => [
                'client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? 'PLACEHOLDER_GOOGLE_ID',
                'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? 'PLACEHOLDER_GOOGLE_SECRET',
                'redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'] ?? 'https://dungeonxplorer.infuseting.fr/oauth/callback/google',
                'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'user_url' => 'https://www.googleapis.com/oauth2/v2/userinfo',
                'scope' => 'email profile'
            ],
            'discord' => [
                'client_id' => $_ENV['DISCORD_CLIENT_ID'] ?? 'PLACEHOLDER_DISCORD_ID',
                'client_secret' => $_ENV['DISCORD_CLIENT_SECRET'] ?? 'PLACEHOLDER_DISCORD_SECRET',
                'redirect_uri' => $_ENV['DISCORD_REDIRECT_URI'] ?? 'http://dungeonxplorer.infuseting.fr/oauth/callback/discord',
                'auth_url' => 'https://discord.com/api/oauth2/authorize',
                'token_url' => 'https://discord.com/api/oauth2/token',
                'user_url' => 'https://discord.com/api/users/@me',
                'scope' => 'identify email'
            ],
            'github' => [
                'client_id' => $_ENV['GITHUB_CLIENT_ID'] ?? 'PLACEHOLDER_GITHUB_ID',
                'client_secret' => $_ENV['GITHUB_CLIENT_SECRET'] ?? 'PLACEHOLDER_GITHUB_SECRET',
                'redirect_uri' => $_ENV['GITHUB_REDIRECT_URI'] ?? 'http://dungeonxplorer.infuseting.fr/oauth/callback/github',
                'auth_url' => 'https://github.com/login/oauth/authorize',
                'token_url' => 'https://github.com/login/oauth/access_token',
                'user_url' => 'https://api.github.com/user',
                'scope' => 'read:user user:email'
            ]
        ];

    }

    public function getAuthUrl($provider)
    {
        if (!isset($this->config[$provider])) {
            return null;
        }

        $cfg = $this->config[$provider];
        $params = [
            'client_id' => $cfg['client_id'],
            'redirect_uri' => $cfg['redirect_uri'],
            'response_type' => 'code',
            'scope' => $cfg['scope'],
            'access_type' => 'offline',             'prompt' => 'consent'         ];
        
        if ($provider === 'apple') {
            $params['response_mode'] = 'form_post';
        }

        return $cfg['auth_url'] . '?' . http_build_query($params);
    }

    public function getUserFromCode($provider, $code)
    {
        if (!isset($this->config[$provider])) {
            return null;
        }

        $cfg = $this->config[$provider];

                $params = [
            'client_id' => $cfg['client_id'],
            'client_secret' => $cfg['client_secret'],
            'redirect_uri' => $cfg['redirect_uri'],
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $cfg['token_url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
                $headers = ['Accept: application/json'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        
        if (!isset($data['access_token'])) {
                        return null;
        }

        $accessToken = $data['access_token'];

                $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $cfg['user_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken",
            "User-Agent: DungeonXplorer"         ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($response, true);

        if (!$userData) {
            return null;
        }

                $normalized = [
            'id' => null,
            'email' => null,
            'name' => null
        ];

        if ($provider === 'google') {
            $normalized['id'] = $userData['id'] ?? null;
            $normalized['email'] = $userData['email'] ?? null;
            $normalized['name'] = $userData['name'] ?? null;
        } elseif ($provider === 'discord') {
            $normalized['id'] = $userData['id'] ?? null;
            $normalized['email'] = $userData['email'] ?? null;
            $normalized['name'] = $userData['username'] ?? null;
        } elseif ($provider === 'github') {
            $normalized['id'] = $userData['id'] ?? null;
                        $normalized['email'] = $userData['email'] ?? null; 
            $normalized['name'] = $userData['login'] ?? null;
        }

        return $normalized;
    }
}
