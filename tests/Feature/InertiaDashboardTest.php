<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_inertia_dashboard_route_renders_dashboard_component(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Dashboard')
                ->has('auth.user')
                ->has('stats.projects')
        );
    }
}
