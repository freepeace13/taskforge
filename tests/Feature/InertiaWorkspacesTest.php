<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaWorkspacesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_workspaces_index(): void
    {
        $response = $this->get(route('workspaces'));

        $response->assertRedirect(route('site.home'));
    }

    public function test_workspaces_index_renders_organization_list(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();
        $organization->update(['name' => 'BBB']);

        $otherOrg = Organization::factory()->create(['name' => 'AAA']);
        $otherOrg->members()->attach($user->id, ['role' => Role::Member->value]);

        $response = $this->actingAs($user)->get(route('workspaces'));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Workspaces', false)
                ->where('auth.user.id', $user->id)
                ->where('auth.user.email', $user->email)
                ->where('auth.tenant', null)
                ->has('auth.organizations', 2)
                ->has('organizations', 2)
                ->where('organizations.0.name', 'AAA')
                ->where('organizations.1.name', 'BBB')
        );
    }

    public function test_workspaces_post_route_is_not_available(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        $response = $this->actingAs($user)->post('/workspaces', ['organization_id' => $organization->id]);

        $response->assertStatus(405);
    }

    public function test_workspaces_post_route_is_not_available_even_with_invalid_payload(): void
    {
        $otherOrg = Organization::factory()->create();
        [, $user] = $this->createOrganizationWithMember();

        $response = $this->actingAs($user)->post('/workspaces', ['organization_id' => $otherOrg->id]);

        $response->assertStatus(405);
    }
}
