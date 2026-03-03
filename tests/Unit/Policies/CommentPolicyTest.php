<?php

namespace Tests\Unit\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_allows_members_and_denies_non_members_as_not_found(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();

        $member = User::query()->findOrFail($organization->owner_id);
        $nonMember = User::factory()->create();

        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $policy = new CommentPolicy;

        $this->assertTrue($policy->create($member, $task));
        $this->assertTrue($policy->create($nonMember, $task)->denied());
    }
}
