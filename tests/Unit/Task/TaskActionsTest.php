<?php

namespace Tests\Unit\Task;

use App\Actions\Task\AssignTaskAction;
use App\Actions\Task\CompleteTaskAction;
use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\ReopenTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Data\TaskData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_task_action_allows_member_of_project_organization(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Member->value,
        ]);

        $action = app(CreateTaskAction::class);

        $task = $action->create(
            actor: $user,
            project: $project,
            data: new TaskData(
                title: 'My Task',
                description: 'Task description',
                priority: 'high',
                dueDate: now()->addDay()->toDateString(),
            ),
        );

        $this->assertInstanceOf(Task::class, $task);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'project_id' => $project->id,
            'title' => 'My Task',
            'priority' => 'high',
        ]);
    }

    public function test_create_task_action_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        $action = app(CreateTaskAction::class);

        $this->expectException(AuthorizationException::class);

        $action->create(
            actor: $user,
            project: $project,
            data: new TaskData(
                title: 'My Task',
            ),
        );
    }

    public function test_update_task_action_updates_fields_for_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Member->value,
        ]);

        $task = Task::factory()->for($project)->create([
            'title' => 'Old',
            'description' => 'Old desc',
            'priority' => 'medium',
        ]);

        $action = app(UpdateTaskAction::class);

        $updated = $action->update(
            actor: $user,
            task: $task,
            data: new TaskData(
                title: 'New',
                description: 'New desc',
                priority: 'low',
            ),
        );

        $this->assertSame('New', $updated->title);
        $this->assertSame('New desc', $updated->description);
        $this->assertSame('low', $updated->priority);
    }

    public function test_update_task_action_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        $task = Task::factory()->for($project)->create();

        $action = app(UpdateTaskAction::class);

        $this->expectException(AuthorizationException::class);

        $action->update(
            actor: $user,
            task: $task,
            data: new TaskData(
                title: 'New',
            ),
        );
    }

    public function test_delete_task_action_soft_deletes_for_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Member->value,
        ]);

        $task = Task::factory()->for($project)->create();

        $action = app(DeleteTaskAction::class);

        $action->delete(
            actor: $user,
            task: $task,
        );

        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_delete_task_action_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        $task = Task::factory()->for($project)->create();

        $action = app(DeleteTaskAction::class);

        $this->expectException(AuthorizationException::class);

        $action->delete(
            actor: $user,
            task: $task,
        );
    }

    public function test_assign_task_action_allows_assigning_member_of_organization(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();

        $actor = User::factory()->create();
        $assignee = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $actor->id,
            'role' => Role::Owner->value,
        ]);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $assignee->id,
            'role' => Role::Member->value,
        ]);

        $task = Task::factory()->for($project)->create();

        $action = app(AssignTaskAction::class);

        $updated = $action->assign(
            actor: $actor,
            task: $task,
            userId: $assignee->id,
        );

        $this->assertSame($assignee->id, $updated->assigned_to_user_id);
    }

    public function test_assign_task_action_denies_assigning_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();

        $actor = User::factory()->create();
        $assignee = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $actor->id,
            'role' => Role::Owner->value,
        ]);

        $task = Task::factory()->for($project)->create();

        $action = app(AssignTaskAction::class);

        $this->expectException(AuthorizationException::class);

        $action->assign(
            actor: $actor,
            task: $task,
            userId: $assignee->id,
        );
    }

    public function test_unassign_task_action_clears_assignee_for_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();

        $actor = User::factory()->create();
        $assignee = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $actor->id,
            'role' => Role::Owner->value,
        ]);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $assignee->id,
            'role' => Role::Member->value,
        ]);

        $task = Task::factory()->for($project)->create([
            'assigned_to_user_id' => $assignee->id,
        ]);

        $action = app(AssignTaskAction::class);

        $updated = $action->assign(
            actor: $actor,
            task: $task,
            userId: null,
        );

        $this->assertNull($updated->assigned_to_user_id);
    }

    public function test_complete_and_reopen_task_actions_toggle_status_for_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Member->value,
        ]);

        $task = Task::factory()->for($project)->create([
            'status' => 'todo',
            'completed_at' => null,
        ]);

        $completeAction = app(CompleteTaskAction::class);
        $reopenAction = app(ReopenTaskAction::class);

        $completed = $completeAction->complete(
            actor: $user,
            task: $task,
        );

        $this->assertSame('done', $completed->status);
        $this->assertNotNull($completed->completed_at);

        $reopened = $reopenAction->reopen(
            actor: $user,
            task: $completed,
        );

        $this->assertSame('todo', $reopened->status);
        $this->assertNull($reopened->completed_at);
    }

    public function test_complete_task_action_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        $task = Task::factory()->for($project)->create([
            'status' => 'todo',
            'completed_at' => null,
        ]);

        $completeAction = app(CompleteTaskAction::class);

        $this->expectException(AuthorizationException::class);

        $completeAction->complete(
            actor: $user,
            task: $task,
        );
    }

    public function test_reopen_task_action_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        $task = Task::factory()->for($project)->create([
            'status' => 'done',
            'completed_at' => now(),
        ]);

        $reopenAction = app(ReopenTaskAction::class);

        $this->expectException(AuthorizationException::class);

        $reopenAction->reopen(
            actor: $user,
            task: $task,
        );
    }
}
