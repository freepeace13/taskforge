<?php

namespace Tests\Unit\Actions\Task;

use App\Actions\Task\CompleteTaskAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompleteTaskActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_completes_task_for_member(): void
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

        $action = app(CompleteTaskAction::class);

        $completed = $action->complete(
            actor: $user,
            task: $task,
        );

        $this->assertSame('done', $completed->status);
        $this->assertNotNull($completed->completed_at);
    }

    public function test_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        $task = Task::factory()->for($project)->create([
            'status' => 'todo',
            'completed_at' => null,
        ]);

        $action = app(CompleteTaskAction::class);

        $this->expectException(AuthorizationException::class);

        $action->complete(
            actor: $user,
            task: $task,
        );
    }
}
