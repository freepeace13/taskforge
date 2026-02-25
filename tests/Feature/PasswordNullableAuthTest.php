<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordNullableAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_rejects_users_without_password(): void
    {
        $user = User::factory()->create([
            'email' => 'nopassword@example.com',
            'password' => null,
        ]);

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'any-password',
        ])->assertUnprocessable()
            ->assertJson([
                'message' => 'Set your password first.',
            ]);
    }
}
