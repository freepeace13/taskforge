<?php

namespace Tests\Unit\Actions\Task;

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

class UpdateTaskActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_fields_for_member(): void
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

    public function test_denies_non_member(): void
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
}
