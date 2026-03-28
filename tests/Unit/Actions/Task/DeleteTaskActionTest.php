<?php

namespace Tests\Unit\Actions\Task;

use App\Actions\Task\DeleteTaskAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTaskActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_deletes_for_member(): void
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

    public function test_denies_non_member(): void
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
}
