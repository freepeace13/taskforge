<?php

namespace Tests\Unit\Organization;

use App\Actions\Organization\AcceptInvitationAction;
use App\Actions\Organization\CancelInvitationAction;
use App\Actions\Organization\CreateOrganizationAction;
use App\Actions\Organization\DeleteOrganizationAction;
use App\Actions\Organization\InviteUserAction;
use App\Actions\Organization\RemoveMemberAction;
use App\Actions\Organization\UpdateMemberRoleAction;
use App\Actions\Organization\UpdateOrganizationAction;
use App\Data\InviteUserData;
use App\Data\MemberData;
use App\Data\OrganizationData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrganizationActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_organization_action_creates_org_and_attaches_owner(): void
    {
        $user = User::factory()->create();

        $action = app(CreateOrganizationAction::class);

        $organization = $action->create(
            actor: $user,
            data: new OrganizationData(
                name: 'Acme Org',
            ),
        );

        $this->assertInstanceOf(Organization::class, $organization);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'owner_id' => $user->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Owner->value,
        ]);
    }

    public function test_update_and_delete_organization_actions_respect_policies(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);
        $other = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $updateAction = app(UpdateOrganizationAction::class);
        $deleteAction = app(DeleteOrganizationAction::class);

        $updated = $updateAction->update(
            actor: $owner,
            organization: $organization,
            data: new OrganizationData(
                name: 'Updated Name',
            ),
        );

        $this->assertSame('Updated Name', $updated->name);

        $deleteAction->delete(
            actor: $owner,
            organization: $organization,
        );

        $this->assertDatabaseMissing('organizations', [
            'id' => $organization->id,
        ]);

        $this->expectException(AuthorizationException::class);

        $updateAction->update(
            actor: $other,
            organization: $organization,
            data: new OrganizationData(
                name: 'Should Fail',
            ),
        );
    }

    public function test_invite_user_action_creates_invitation_and_blocks_duplicates(): void
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

        // Duplicate member
        $member = User::factory()->create(['email' => 'member@example.com']);
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

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

    public function test_invite_user_action_blocks_duplicate_active_invitation(): void
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

    public function test_cancel_invitation_action_deletes_pending_and_blocks_accepted(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $pendingInvite = OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'accepted_at' => null,
        ]);

        $acceptedInvite = OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'accepted_at' => now(),
        ]);

        $action = app(CancelInvitationAction::class);

        $action->cancel(
            actor: $owner,
            invitation: $pendingInvite,
        );

        $this->assertDatabaseMissing('organization_invites', [
            'id' => $pendingInvite->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This invitation has already been accepted.');

        $action->cancel(
            actor: $owner,
            invitation: $acceptedInvite,
        );
    }

    public function test_accept_invitation_action_adds_member_and_marks_accepted(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['email' => 'invitee@example.com']);

        $invite = OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'email' => $user->email,
            'role' => Role::Member->value,
            'expires_at' => now()->addDay(),
            'accepted_at' => null,
        ]);

        $action = app(AcceptInvitationAction::class);

        DB::beginTransaction();
        $action->accept(
            actor: $user,
            invite: $invite,
        );
        DB::rollBack();
    }

    public function test_update_member_role_and_remove_member_actions_respect_policies(): void
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

        $updateAction = app(UpdateMemberRoleAction::class);
        $removeAction = app(RemoveMemberAction::class);

        $updatedMember = $updateAction->update(
            actor: $owner,
            data: new MemberData(
                organization: $organization,
                user: $member,
                role: Role::Admin,
            ),
        );

        $this->assertSame(Role::Admin, $updatedMember->role);

        $removeAction->remove(
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
