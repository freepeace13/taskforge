<?php

namespace Tests\Feature;

use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_org_member(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);

        $response = $this->actingAs($user)
            ->get(route('dashboard', ['org' => $organization->slug]));

        $response->assertOk();

        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Dashboard', false)
                ->where('organization.slug', $organization->slug)
                ->where('organization.name', $organization->name)
                ->where('stats.projects', 12)
                ->where('stats.openTasks', 34)
                ->where('stats.completed', 128)
                ->where('stats.overdue', 3)
        );
    }
}
