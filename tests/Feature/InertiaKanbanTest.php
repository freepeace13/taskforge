<?php

namespace Tests\Feature;

use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaKanbanTest extends TestCase
{
    use RefreshDatabase;

    public function test_inertia_tasks_hub_route_renders_tasks_hub_component(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $response = $this->actingAs($user)->get(route('tasks.hub', ['org' => $organization->slug]));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Tasks/Hub', false),
        );
    }
}
