<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService {
    private string $secretKey;
    private int $tokenLifeTime;

    public function __construct(string $secretKey, int $tokenLifeTime) {
        $this->secretKey = $secretKey;
        $this->tokenLifeTime = $tokenLifeTime;
    }

    public function createToken(array $payload): string {
        $issuedAt = time();
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $issuedAt + $this->tokenLifeTime;

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function decodeToken(string $token): ?array {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}