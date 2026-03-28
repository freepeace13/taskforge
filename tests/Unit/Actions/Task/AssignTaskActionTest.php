<?php

namespace Tests\Unit\Actions\Task;

use App\Actions\Task\AssignTaskAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignTaskActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_assigning_member_of_organization(): void
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

    public function test_denies_assigning_non_member(): void
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

    public function test_unassign_clears_assignee_for_member(): void
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
}
