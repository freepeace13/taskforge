<?php

namespace Tests\Unit\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use App\Policies\OrganizationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_allows_members_and_denies_non_members_as_not_found(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);
        $nonMember = User::factory()->create();

        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);

        $policy = new OrganizationPolicy;

        $this->assertTrue($policy->view($owner, $organization));
        $this->assertTrue($policy->view($nonMember, $organization)->denied());
    }

    public function test_update_allows_admins_and_owners_only(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);
        $admin = User::factory()->create();
        $member = User::factory()->create();

        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);
        $organization->members()->attach($admin->id, ['role' => Role::Admin->value]);
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $policy = new OrganizationPolicy;

        $this->assertTrue($policy->update($owner, $organization));
        $this->assertTrue($policy->update($admin, $organization));
        $this->assertFalse($policy->update($member, $organization));
    }

    public function test_delete_allows_only_owner(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);
        $admin = User::factory()->create();

        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);
        $organization->members()->attach($admin->id, ['role' => Role::Admin->value]);

        $policy = new OrganizationPolicy;

        $this->assertTrue($policy->delete($owner, $organization));
        $this->assertFalse($policy->delete($admin, $organization));
    }
}
