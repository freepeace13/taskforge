<?php

namespace Tests\Unit\Actions\Organization;

use App\Actions\Organization\RemoveMemberAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveMemberActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_remove_member(): void
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

        $action = app(RemoveMemberAction::class);

        $action->remove(
            actor: $owner,
            organization: $organization,
            userId: $member->id,
        );

        $this->assertDatabaseMissing('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $member->id,
        ]);
    }
}
