<?php

namespace Tests\Unit\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_view_any_allow_members_and_deny_non_members(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $member = User::query()->findOrFail($organization->owner_id);
        $nonMember = User::factory()->create();

        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $policy = new TaskPolicy;

        $this->assertTrue($policy->create($member, $project));
        $this->assertTrue($policy->viewAny($member, $project));
        $this->assertTrue($policy->create($nonMember, $project)->denied());
        $this->assertTrue($policy->viewAny($nonMember, $project)->denied());
    }

    public function test_assign_requires_assignee_to_be_member_when_user_id_given(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();

        $actor = User::query()->findOrFail($organization->owner_id);
        $assignee = User::factory()->create();

        $organization->members()->attach($actor->id, ['role' => Role::Owner->value]);

        $policy = new TaskPolicy;

        $this->assertFalse($policy->assign($actor, $task, $assignee->id));

        $organization->members()->attach($assignee->id, ['role' => Role::Member->value]);

        $this->assertTrue($policy->assign($actor, $task, $assignee->id));
    }
}
