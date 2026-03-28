<?php

namespace Tests\Unit\Actions\Organization;

use App\Actions\Organization\InviteUserAction;
use App\Data\InviteUserData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_invitation(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $action = app(InviteUserAction::class);

        $invite = $action->invite(
            actor: $owner,
            organization: $organization,
            data: new InviteUserData(
                email: 'invitee@example.com',
                role: Role::Member,
            ),
        );

        $this->assertInstanceOf(OrganizationInvite::class, $invite);
    }

    public function test_blocks_duplicate_member(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $member = User::factory()->create(['email' => 'member@example.com']);
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $action = app(InviteUserAction::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This user is already a member.');

        $action->invite(
            actor: $owner,
            organization: $organization,
            data: new InviteUserData(
                email: 'member@example.com',
                role: Role::Member,
            ),
        );
    }

    public function test_blocks_duplicate_active_invitation(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $existingInvite = OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'duplicate@example.com',
            'role' => Role::Member->value,
        ]);

        $action = app(InviteUserAction::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('An active invitation already exists for this email.');

        $action->invite(
            actor: $owner,
            organization: $organization,
            data: new InviteUserData(
                email: $existingInvite->email,
                role: Role::Member,
            ),
        );
    }
}
