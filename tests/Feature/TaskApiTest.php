<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_crud_tasks_and_change_state_and_assignee(): void
    {
        [$organization, $user] = $this->createOrganizationAndOwner();
        Sanctum::actingAs($user);

        $project = Project::factory()->for($organization)->create();
        $assignee = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $assignee->id,
            'role' => Role::Member->value,
        ]);

        $task = Task::factory()->for($project)->create();

        $this->getJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks')
            ->assertOk()
            ->assertJsonFragment(['id' => $task->id]);

        $createResponse = $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks', [
            'title' => 'New Task',
            'description' => 'Desc',
            'priority' => 'high',
        ])->assertCreated();

        $createdId = $createResponse->json('id');

        $this->getJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$createdId)
            ->assertOk()
            ->assertJsonFragment(['title' => 'New Task']);

        $this->patchJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$createdId, [
            'title' => 'Updated Task',
        ])->assertOk()
            ->assertJsonFragment(['title' => 'Updated Task']);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$createdId.'/assign', [
            'user_id' => $assignee->id,
        ])->assertOk()
            ->assertJsonFragment(['assigned_to_user_id' => $assignee->id]);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$createdId.'/complete')
            ->assertOk()
            ->assertJsonFragment(['status' => 'done']);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$createdId.'/reopen')
            ->assertOk()
            ->assertJsonFragment(['status' => 'todo']);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$createdId.'/unassign')
            ->assertOk()
            ->assertJsonFragment(['assigned_to_user_id' => null]);

        $this->deleteJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$createdId)
            ->assertNoContent();

        $this->assertSoftDeleted('tasks', [
            'id' => $createdId,
        ]);
    }

    private function createOrganizationAndOwner(): array
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);

        return [$organization, $owner];
    }
}
