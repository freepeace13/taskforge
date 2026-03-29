<?php

namespace App\Services\Auth;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class TechysavvyOAuthProvider extends AbstractProvider
{
    const TOKEN_ENDPOINT = '/oauth/token';

    const AUTHORIZATION_ENDPOINT = '/oauth/authorize';

    const USER_ENDPOINT = '/api/token/user';

    /**
     * Indicates if PKCE should be used.
     *
     * @var bool
     */
    protected $usesPKCE = true;

    private function resolveServerUrl($path = null): string
    {
        return config('services.techysavvy.server_url').($path ? $path : '');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            $this->resolveServerUrl(self::AUTHORIZATION_ENDPOINT), $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return $this->resolveServerUrl(self::TOKEN_ENDPOINT);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token): array
    {
        $userUrl = $this->resolveServerUrl(self::USER_ENDPOINT);

        try {
            $response = $this->getHttpClient()->get($userUrl, [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()?->getStatusCode() !== 404) {
                throw $e;
            }
        }

        $body = json_decode((string) $response->getBody(), true);

        return Arr::get($body, 'data', []);
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
