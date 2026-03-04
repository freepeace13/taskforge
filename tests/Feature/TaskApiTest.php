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

        $this->getJson(route('api.v1.orgs.projects.tasks.index', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $task->id]);

        $createResponse = $this->postJson(route('api.v1.orgs.projects.tasks.store', [
            'org' => $organization->slug,
            'project' => $project->id,
        ]), [
            'title' => 'New Task',
            'description' => 'Desc',
            'priority' => 'high',
        ])->assertCreated();

        $createdId = $createResponse->json('id');

        $this->getJson(route('api.v1.orgs.projects.tasks.show', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $createdId,
        ]))
            ->assertOk()
            ->assertJsonFragment(['title' => 'New Task']);

        $this->patchJson(route('api.v1.orgs.projects.tasks.update', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $createdId,
        ]), [
            'title' => 'Updated Task',
        ])->assertOk()
            ->assertJsonFragment(['title' => 'Updated Task']);

        $this->postJson(route('api.v1.orgs.projects.tasks.assign', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $createdId,
        ]), [
            'user_id' => $assignee->id,
        ])->assertOk()
            ->assertJsonFragment(['assigned_to_user_id' => $assignee->id]);

        $this->postJson(route('api.v1.orgs.projects.tasks.complete', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $createdId,
        ]))
            ->assertOk()
            ->assertJsonFragment(['status' => 'done']);

        $this->postJson(route('api.v1.orgs.projects.tasks.reopen', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $createdId,
        ]))
            ->assertOk()
            ->assertJsonFragment(['status' => 'todo']);

        $this->postJson(route('api.v1.orgs.projects.tasks.unassign', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $createdId,
        ]))
            ->assertOk()
            ->assertJsonFragment(['assigned_to_user_id' => null]);

        $this->deleteJson(route('api.v1.orgs.projects.tasks.destroy', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $createdId,
        ]))
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
