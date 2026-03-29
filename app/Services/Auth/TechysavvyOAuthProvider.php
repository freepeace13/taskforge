<?php

namespace App\Services\Auth;

use Illuminate\Support\Arr;
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
        $baseUrl = $this->serverUrl().'/oauth/authorize';

        return $this->buildAuthUrlFromBase($baseUrl, $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return $this->serverUrl().'/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token): array
    {
        $userUrl = $this->serverUrl().'/api/token/user';
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ];

        try {
            $response = $this->getHttpClient()->get($userUrl, [
                \GuzzleHttp\RequestOptions::HEADERS => $headers,
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()?->getStatusCode() !== 404) {
                throw $e;
            }

            $fallbackUrl = $this->serverUrl().'/api/me';
            $response = $this->getHttpClient()->get($fallbackUrl, [
                \GuzzleHttp\RequestOptions::HEADERS => $headers,
            ]);
        }

        return Arr::get(
            json_decode((string) $response->getBody(), true),
            'data',
            [],
        );
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
            'tenants' => Arr::get($user, 'tenants', []),
        ]);
    }
}
