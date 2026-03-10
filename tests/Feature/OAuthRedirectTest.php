<?php

namespace Tests\Feature;

use Tests\TestCase;

class OAuthRedirectTest extends TestCase
{
    public function test_login_redirects_to_auth_server_with_correct_params(): void
    {
        config([
            'services.techysavvy.client_id' => 'test-client-id',
            'services.techysavvy.redirect' => 'https://taskforge.test/auth/callback',
        ]);

        $response = $this->get(route('login'));

        $response->assertRedirect();

        $location = $response->headers->get('Location');

        $this->assertStringStartsWith('https://auth.techysavvy.me/oauth/authorize', $location);

        parse_str(parse_url($location, PHP_URL_QUERY) ?: '', $params);

        $this->assertArrayHasKey('client_id', $params);
        $this->assertSame('test-client-id', $params['client_id']);
        $this->assertArrayHasKey('redirect_uri', $params);
        $this->assertArrayHasKey('response_type', $params);
        $this->assertSame('code', $params['response_type']);
        $this->assertArrayHasKey('state', $params);
        $this->assertArrayHasKey('code_challenge', $params);
        $this->assertArrayHasKey('code_challenge_method', $params);
        $this->assertSame('S256', $params['code_challenge_method']);
    }
}
