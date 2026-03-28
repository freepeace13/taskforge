<?php

namespace Tests\Unit\Actions\Organization;

use App\Actions\Organization\CancelInvitationAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelInvitationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_pending_invitation(): void
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

        $action = app(CancelInvitationAction::class);

        $action->cancel(
            actor: $owner,
            invitation: $pendingInvite,
        );

        $this->assertDatabaseMissing('organization_invites', [
            'id' => $pendingInvite->id,
        ]);
    }

    public function test_blocks_canceling_accepted_invitation(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $acceptedInvite = OrganizationInvite::factory()->create([
            'organization_id' => $organization->id,
            'accepted_at' => now(),
        ]);

        $action = app(CancelInvitationAction::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This invitation has already been accepted.');

        $action->cancel(
            actor: $owner,
            invitation: $acceptedInvite,
        );
    }
}
