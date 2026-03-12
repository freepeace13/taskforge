<?php

namespace App\Services\Auth;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class TechysavvyOAuthProvider extends AbstractProvider
{
    /**
     * Indicates if PKCE should be used.
     *
     * @var bool
     */
    protected $usesPKCE = true;

    protected function serverUrl(): string
    {
        return config('services.techysavvy.server_url');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        $baseUrl = $this->serverUrl() . '/oauth/authorize';

        return $this->buildAuthUrlFromBase($baseUrl, $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return $this->serverUrl() . '/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token): array
    {
        $userUrl = $this->serverUrl() . '/api/user';

        $response = $this->getHttpClient()->get($userUrl, [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return is_array($data) ? $data : [];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'] ?? null,
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'email_verified_at' => $user['email_verified_at'] ?? null,
        ]);
    }
}
