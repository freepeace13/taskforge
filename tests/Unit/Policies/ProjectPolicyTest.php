<?php

namespace Tests\Unit\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_any_allows_members_and_denies_non_members_as_not_found(): void
    {
        $organization = Organization::factory()->create();
        $member = User::query()->findOrFail($organization->owner_id);
        $nonMember = User::factory()->create();

        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $policy = new ProjectPolicy;

        $this->assertTrue($policy->viewAny($member, $organization));
        $this->assertTrue($policy->viewAny($nonMember, $organization)->denied());
    }

    public function test_create_allows_only_admins_and_owners(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);
        $admin = User::factory()->create();
        $member = User::factory()->create();

        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);
        $organization->members()->attach($admin->id, ['role' => Role::Admin->value]);
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $policy = new ProjectPolicy;

        $this->assertTrue($policy->create($owner, $organization));
        $this->assertTrue($policy->create($admin, $organization));
        $this->assertFalse($policy->create($member, $organization));
    }
}
