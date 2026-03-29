<?php

namespace Tests\Feature;

use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaWorkspacesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_workspaces_index(): void
    {
        $response = $this->get(route('workspaces.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_workspaces_index_renders_organization_list(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        $response = $this->actingAs($user)->get(route('workspaces.index'));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Workspaces', false)
                ->has('organizations', fn (Assert $orgs): Assert => $orgs
                    ->has(0, fn (Assert $org): Assert => $org
                        ->where('id', $organization->id)
                        ->where('name', $organization->name)
                        ->where('slug', $organization->slug)
                        ->where('role', 'owner')
                        ->etc()))
        );
    }

    public function test_workspaces_store_sets_tenant_and_redirects_to_projects(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember();

        $response = $this->actingAs($user)->post(route('workspaces.store'), [
            'organization_id' => $organization->id,
        ]);

        $response->assertRedirect(route('projects.index', ['org' => $organization->slug]));
        $this->assertSame($organization->id, session('tenant_id'));
    }

    public function test_workspaces_store_rejects_organization_user_is_not_member_of(): void
    {
        $otherOrg = Organization::factory()->create();
        [, $user] = $this->createOrganizationWithMember();

        $response = $this->actingAs($user)->post(route('workspaces.store'), [
            'organization_id' => $otherOrg->id,
        ]);

        $response->assertSessionHasErrors('organization_id');
    }
}
