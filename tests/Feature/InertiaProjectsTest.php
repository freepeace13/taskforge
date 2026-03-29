<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_inertia_projects_route_renders_projects_index_component_with_tenant_session(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        Project::factory()->for($organization)->create(['name' => 'Listed', 'archived_at' => null]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $organization->id])
            ->get(route('projects.index', ['org' => $organization->slug]));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Projects/Index', false)
                ->has('projects.data', 1)
                ->where('projects.data.0.name', 'Listed')
        );
    }
}
