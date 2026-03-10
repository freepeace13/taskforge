<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrentUserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_returns_authenticated_user_resource(): void
    {
        $user = User::factory()->create([
            'auth_id' => 'auth-123',
        ]);

        $response = $this->getJson(route('api.v1.me'), [
            'Authorization' => 'Bearer test:auth-123',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson(route('api.v1.me'))
            ->assertUnauthorized();
    }
}
