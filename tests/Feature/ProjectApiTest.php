<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_create_show_update_and_delete_projects(): void
    {
        [$organization, $user] = $this->createOrganizationAndOwner();

        Sanctum::actingAs($user);

        $project = Project::factory()->for($organization)->create();

        $this->getJson('/api/v1/orgs/'.$organization->slug.'/projects')
            ->assertOk()
            ->assertJsonFragment(['id' => $project->id]);

        $createResponse = $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects', [
            'name' => 'New Project',
            'description' => 'Description',
        ])->assertCreated();

        $createdId = $createResponse->json('id');

        $this->getJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$createdId)
            ->assertOk()
            ->assertJsonFragment(['name' => 'New Project']);

        $this->patchJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$createdId, [
            'name' => 'Updated Project',
        ])->assertOk()
            ->assertJsonFragment(['name' => 'Updated Project']);

        $this->deleteJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$createdId)
            ->assertNoContent();

        $this->assertSoftDeleted('projects', [
            'id' => $createdId,
        ]);
    }

    public function test_can_archive_and_restore_projects_and_list_archived(): void
    {
        [$organization, $user] = $this->createOrganizationAndOwner();
        Sanctum::actingAs($user);

        $project = Project::factory()->for($organization)->create([
            'archived_at' => null,
        ]);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/archive')
            ->assertOk()
            ->assertJsonFragment(['id' => $project->id]);

        $this->getJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/archived')
            ->assertOk()
            ->assertJsonFragment(['id' => $project->id]);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/restore')
            ->assertOk()
            ->assertJsonFragment(['id' => $project->id]);
    }

    private function createOrganizationAndOwner(): array
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        $organization->members()->attach($owner->id, ['role' => 'owner']);

        return [$organization, $owner];
    }
}
