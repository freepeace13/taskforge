<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class OAuthCallbackSyncsAuthTenantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_callback_syncs_tenants_from_oauth_user(): void
    {
        $oauthUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $oauthUser->shouldReceive('getId')->andReturn('oauth-user-1');
        $oauthUser->shouldReceive('getName')->andReturn('Alice');
        $oauthUser->shouldReceive('getEmail')->andReturn('alice@example.com');
        $oauthUser->tenants = [
            [
                'id' => 99,
                'slug' => 'synced-org',
                'name' => 'Synced Org',
                'role' => 'admin',
            ],
        ];

        $provider = Mockery::mock(\Laravel\Socialite\Contracts\Provider::class);
        $provider->shouldReceive('user')->andReturn($oauthUser);

        Socialite::shouldReceive('driver')->with('techysavvy')->andReturn($provider);

        $response = $this->get('/auth/callback');

        $response->assertRedirect();

        $user = User::query()->where('auth_id', 'oauth-user-1')->firstOrFail();
        $this->assertDatabaseHas('organizations', [
            'auth_organization_id' => '99',
            'name' => 'Synced Org',
            'slug' => 'synced-org',
        ]);

        $org = \App\Models\Organization::query()->where('auth_organization_id', '99')->firstOrFail();
        $this->assertTrue($user->belongsToOrganization($org));
        $this->assertSame(\App\Enums\Role::Admin, $user->organizationRole($org));
    }
}
