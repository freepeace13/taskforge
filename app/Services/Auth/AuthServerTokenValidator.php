<?php

namespace App\Services\Auth;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use InvalidArgumentException;

class AuthServerTokenValidator
{
    /**
     * Validate the Bearer token and return the auth server user ID (sub claim).
     *
     * @return string|null The auth server user ID, or null if invalid
     */
    public function validate(string $token): ?string
    {
        if (app()->environment('testing') && str_starts_with($token, 'test:')) {
            return substr($token, 5) ?: null;
        }

        $publicKey = config('services.techysavvy.public_key');

        if (empty($publicKey)) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            return isset($decoded->sub) ? (string) $decoded->sub : null;
        } catch (ExpiredException|SignatureInvalidException|InvalidArgumentException) {
            return null;
        }
    }
}
