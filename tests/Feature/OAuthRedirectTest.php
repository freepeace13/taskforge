<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class OAuthRedirectTest extends TestCase
{
    public function test_login_redirects_to_auth_server_with_correct_params(): void
    {
        config([
            'services.techysavvy.client_id' => 'test-client-id',
            'services.techysavvy.server_url' => 'http://auth.techysavvy.test',
            'services.techysavvy.redirect' => 'https://taskforge.test/auth/callback',
        ]);

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertViewIs('auth.oauth-redirect');
        $response->assertViewHas('authUrl');

        $authUrl = $response->viewData('authUrl');
        $this->assertIsString($authUrl);
        $this->assertStringStartsWith('http://auth.techysavvy.test/oauth/authorize', $authUrl);

        parse_str(parse_url($authUrl, PHP_URL_QUERY) ?: '', $params);

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

    public function test_logout_redirects_to_homepage(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect(route('site.home'));
        $this->assertGuest();
    }

    public function test_logout_uses_inertia_location_redirect_for_inertia_requests(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withHeader('X-Inertia', 'true')
            ->post(route('logout'));

        $response->assertStatus(409);
        $response->assertHeader('X-Inertia-Location', route('site.home'));
        $this->assertGuest();
    }
}
