<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_org_member(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $otherOrg = Organization::factory()->create();
        $otherOrg->members()->attach($user->id, ['role' => Role::Member->value]);

        $response = $this->actingAs($user)
            ->get(route('dashboard', ['org' => $organization->slug]));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Dashboard', false)
                ->where('auth.user.id', $user->id)
                ->where('auth.user.email', $user->email)
                ->where('auth.tenant.slug', $organization->slug)
                ->where('auth.tenant.role', Role::Owner->value)
                ->has('auth.organizations', 2)
                ->where('organization.slug', $organization->slug)
                ->where('organization.name', $organization->name)
                ->where('stats.projects', 12)
                ->where('stats.openTasks', 34)
                ->where('stats.completed', 128)
                ->where('stats.overdue', 3)
        );
    }
}
