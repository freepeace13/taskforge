<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithTenant;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use InteractsWithTenant, RefreshDatabase;

    public function test_can_list_create_show_update_and_delete_projects(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);

        $this->actingAsInOrganization($user, $organization, Role::Owner);

        $project = Project::factory()->for($organization)->create();

        $this->getJson(route('api.v1.orgs.projects.index', [
            'org' => $organization->slug,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $project->id]);

        $createResponse = $this->postJson(route('api.v1.orgs.projects.store', [
            'org' => $organization->slug,
        ]), [
            'name' => 'New Project',
            'description' => 'Description',
        ])->assertCreated();

        $createdId = $createResponse->json('id');

        $this->getJson(route('api.v1.orgs.projects.show', [
            'org' => $organization->slug,
            'project' => $createdId,
        ]))
            ->assertOk()
            ->assertJsonFragment(['name' => 'New Project']);

        $this->patchJson(route('api.v1.orgs.projects.update', [
            'org' => $organization->slug,
            'project' => $createdId,
        ]), [
            'name' => 'Updated Project',
        ])->assertOk()
            ->assertJsonFragment(['name' => 'Updated Project']);

        $this->deleteJson(route('api.v1.orgs.projects.destroy', [
            'org' => $organization->slug,
            'project' => $createdId,
        ]))
            ->assertNoContent();

        $this->assertSoftDeleted('projects', [
            'id' => $createdId,
        ]);
    }

    public function test_can_archive_and_restore_projects_and_list_archived(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);

        $this->actingAsInOrganization($user, $organization, Role::Owner);

        $project = Project::factory()->for($organization)->create([
            'archived_at' => null,
        ]);

        $this->postJson(route('api.v1.orgs.projects.archive', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $project->id]);

        $this->getJson(route('api.v1.orgs.projects.archived.index', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $project->id]);

        $this->postJson(route('api.v1.orgs.projects.restore', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $project->id]);
    }
}
