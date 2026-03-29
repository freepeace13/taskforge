<?php

namespace Tests\Feature\Project;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProjectInertiaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_index_returns_not_found_when_user_not_member_of_org_in_path(): void
    {
        [, $user] = $this->createOrganizationWithMember(Role::Owner);
        $otherOrg = Organization::factory()->create();

        $response = $this->actingAs($user)->get(route('projects.index', ['org' => $otherOrg->slug]));

        $response->assertNotFound();
    }

    public function test_projects_index_renders_paginated_projects_when_session_tenant_is_set(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create([
            'name' => 'Scoped project',
            'archived_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $organization->id])
            ->get(route('projects.index', ['org' => $organization->slug]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Projects/Index', false)
                ->has('projects.data', 1)
                ->where('projects.data.0.name', 'Scoped project')
                ->where('projects.data.0.id', $project->id)
        );
    }

    public function test_projects_create_renders_when_session_tenant_is_set(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $organization->id])
            ->get(route('projects.create', ['org' => $organization->slug]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Projects/Create', false)
                ->where('organization.slug', $organization->slug)
        );
    }

    public function test_project_can_be_created(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $organization->id])
            ->post(route('projects.store', ['org' => $organization->slug]), [
                'name' => 'New Inertia Project',
                'description' => 'From feature test',
            ]);

        $project = Project::query()->where('name', 'New Inertia Project')->firstOrFail();

        $response->assertRedirect(route('projects.show', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]));
    }

    public function test_project_show_renders(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create([
            'name' => 'Visible project',
        ]);

        $response = $this->actingAs($user)->get(route('projects.show', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('Projects/Show', false)
                ->where('project.name', 'Visible project')
        );
    }

    public function test_project_can_be_updated(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create([
            'name' => 'Before',
        ]);

        $response = $this->actingAs($user)->patch(route('projects.update', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]), [
            'name' => 'After',
            'description' => 'Updated',
        ]);

        $response->assertRedirect(route('projects.show', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]));

        $this->assertSame('After', $project->fresh()->name);
    }

    public function test_project_can_be_deleted(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        $project = Project::factory()->for($organization)->create();

        $response = $this->actingAs($user)->delete(route('projects.destroy', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]));

        $response->assertRedirect(route('projects.index', ['org' => $organization->slug]));

        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_member_without_admin_role_cannot_create_project(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Member);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $organization->id])
            ->post(route('projects.store', ['org' => $organization->slug]), [
                'name' => 'Blocked',
                'description' => null,
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_non_member_cannot_view_foreign_project(): void
    {
        [, $user] = $this->createOrganizationWithMember(Role::Owner);
        $otherOrg = Organization::factory()->create();
        $foreignProject = Project::factory()->for($otherOrg)->create();

        $response = $this->actingAs($user)->get(route('projects.show', [
            'org' => $otherOrg->slug,
            'project' => $foreignProject->id,
        ]));

        $response->assertNotFound();
    }
}
