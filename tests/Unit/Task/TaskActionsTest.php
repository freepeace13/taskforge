<?php

namespace Tests\Unit\Task;

use App\Actions\Task\AssignTaskAction;
use App\Actions\Task\CompleteTaskAction;
use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\ReopenTaskAction;
use App\Actions\Task\UnassignTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TaskActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_task_action_creates_task_for_project(): void
    {
        $project = Project::factory()->create();

        $action = app(CreateTaskAction::class);

        $task = $action->create(
            project: $project,
            title: 'My Task',
            description: 'Task description',
            priority: 'high',
            dueDate: now()->addDay()->toDateString(),
        );

        $this->assertInstanceOf(Task::class, $task);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'project_id' => $project->id,
            'title' => 'My Task',
            'priority' => 'high',
        ]);
    }

    public function test_create_task_action_enforces_assignee_membership(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $nonMember = User::factory()->create();

        $action = app(CreateTaskAction::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage('Assignee must be a member of the organization.');

        $action->create(
            project: $project,
            title: 'My Task',
            assignedToUserId: $nonMember->id,
        );
    }

    public function test_update_task_action_updates_editable_fields(): void
    {
        $task = Task::factory()->create([
            'title' => 'Old',
            'description' => 'Old desc',
            'priority' => 'medium',
        ]);

        $action = app(UpdateTaskAction::class);

        $updated = $action->update($task, [
            'title' => 'New',
            'description' => 'New desc',
            'priority' => 'low',
        ]);

        $this->assertSame('New', $updated->title);
        $this->assertSame('New desc', $updated->description);
        $this->assertSame('low', $updated->priority);
    }

    public function test_assign_and_unassign_task_actions_manage_assignee_with_membership_check(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $member = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'role' => Role::Member->value,
        ]);

        $task = Task::factory()->for($project)->create();

        $assignAction = app(AssignTaskAction::class);
        $unassignAction = app(UnassignTaskAction::class);

        $assigned = $assignAction->assign($task, $member->id);

        $this->assertSame($member->id, $assigned->assigned_to_user_id);

        $unassigned = $unassignAction->unassign($assigned);

        $this->assertNull($unassigned->assigned_to_user_id);
    }

    public function test_complete_and_reopen_task_actions_toggle_status_and_completed_at(): void
    {
        $task = Task::factory()->create([
            'status' => 'todo',
            'completed_at' => null,
        ]);

        $completeAction = app(CompleteTaskAction::class);
        $reopenAction = app(ReopenTaskAction::class);

        $completed = $completeAction->complete($task);

        $this->assertSame('done', $completed->status);
        $this->assertNotNull($completed->completed_at);

        $reopened = $reopenAction->reopen($completed);

        $this->assertSame('todo', $reopened->status);
        $this->assertNull($reopened->completed_at);
    }

    public function test_delete_task_action_soft_deletes_task(): void
    {
        $task = Task::factory()->create();

        $action = app(DeleteTaskAction::class);

        $action->delete($task);

        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }
}
