<?php

namespace Tests\Unit\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use App\Policies\MemberPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_any_allows_members_and_denies_non_members(): void
    {
        $organization = Organization::factory()->create();
        $member = User::query()->findOrFail($organization->owner_id);
        $nonMember = User::factory()->create();

        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $policy = new MemberPolicy;

        $this->assertTrue($policy->viewAny($member, $organization));
        $this->assertTrue($policy->viewAny($nonMember, $organization)->denied());
    }
}
