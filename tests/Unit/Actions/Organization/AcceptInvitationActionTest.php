<?php

namespace Tests\Unit\Actions\Organization;

use App\Actions\Organization\AcceptInvitationAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AcceptInvitationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepts_invitation_within_transaction(): void
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

        $this->assertTrue($user->fresh()->belongsToOrganization($organization));
        $this->assertNotNull($invite->fresh()->accepted_at);

        DB::rollBack();
    }
}
