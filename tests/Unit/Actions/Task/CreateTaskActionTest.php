<?php

namespace Tests\Unit\Actions\Task;

use App\Actions\Task\CreateTaskAction;
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

class CreateTaskActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_member_of_project_organization(): void
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

    public function test_denies_non_member(): void
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
}
