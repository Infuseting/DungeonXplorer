<?php

namespace App\Services;

class TokenService
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = $_ENV['APP_SECRET'] ?? 'default_secret_key_change_me_in_prod';
    }

    public function generateAccessToken($userId)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $userId,
            'exp' => time() + 900         ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function generateRefreshToken()
    {
        return bin2hex(random_bytes(32));
    }

    public function validateToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        [$header, $payload, $signature] = $parts;

        $validSignature = hash_hmac('sha256', $header . "." . $payload, $this->secretKey, true);
        $validBase64UrlSignature = $this->base64UrlEncode($validSignature);

        if (!hash_equals($validBase64UrlSignature, $signature)) return false;

        $payloadData = json_decode($this->base64UrlDecode($payload), true);
        if ($payloadData['exp'] < time()) return false;

        return $payloadData;
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
