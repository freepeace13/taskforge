<?php

namespace Tests\Unit\Actions\Organization;

use App\Actions\Organization\UpdateMemberRoleAction;
use App\Data\MemberData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateMemberRoleActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_promote_member(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);
        $member = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'role' => Role::Member->value,
        ]);

        $action = app(UpdateMemberRoleAction::class);

        $updatedMember = $action->update(
            actor: $owner,
            data: new MemberData(
                organization: $organization,
                user: $member,
                role: Role::Admin,
            ),
        );

        $this->assertSame(Role::Admin, $updatedMember->role);
    }
}
