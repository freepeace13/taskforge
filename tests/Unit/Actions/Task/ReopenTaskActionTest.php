<?php

namespace Tests\Unit\Actions\Task;

use App\Actions\Task\ReopenTaskAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReopenTaskActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_reopens_completed_task_for_member(): void
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
            'status' => 'done',
            'completed_at' => now(),
        ]);

        $action = app(ReopenTaskAction::class);

        $reopened = $action->reopen(
            actor: $user,
            task: $task,
        );

        $this->assertSame('todo', $reopened->status);
        $this->assertNull($reopened->completed_at);
    }

    public function test_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $user = User::factory()->create();

        $task = Task::factory()->for($project)->create([
            'status' => 'done',
            'completed_at' => now(),
        ]);

        $action = app(ReopenTaskAction::class);

        $this->expectException(AuthorizationException::class);

        $action->reopen(
            actor: $user,
            task: $task,
        );
    }
}
