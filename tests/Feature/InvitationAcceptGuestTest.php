<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class InvitationAcceptGuestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_accept_invitation_for_existing_user_with_valid_signature(): void
    {
        $organization = $this->createOrganization();
        $user = User::factory()->create(['email' => 'existing@example.com']);

        $invite = OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => $user->email,
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
        ]);

        $url = URL::temporarySignedRoute('invitations.accept', now()->addHour(), [
            'token' => $invite->token,
            'email' => $invite->email,
        ]);

        $this->getJson($url)
            ->assertOk()
            ->assertJson([
                'message' => 'Invitation accepted.',
            ]);

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Member->value,
        ]);
    }

    public function test_guest_accept_invitation_silently_creates_and_authenticates_user(): void
    {
        $organization = $this->createOrganization();
        $invite = OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'new.person@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
        ]);

        $url = URL::temporarySignedRoute('invitations.accept', now()->addHour(), [
            'token' => $invite->token,
            'email' => $invite->email,
        ]);

        $this->getJson($url)->assertOk();

        $createdUser = User::query()->where('email', 'new.person@example.com')->first();

        $this->assertNotNull($createdUser);
        $this->assertNull($createdUser->password);
        $this->assertAuthenticatedAs($createdUser);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $createdUser->id,
            'role' => Role::Member->value,
        ]);
    }

    public function test_guest_accept_fails_with_invalid_signature(): void
    {
        $organization = $this->createOrganization();
        $invite = OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'member@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
        ]);

        $this->getJson('/invitations/'.$invite->token.'/accept?email='.$invite->email)
            ->assertForbidden();
    }

    public function test_guest_accept_fails_when_invite_is_expired(): void
    {
        $organization = $this->createOrganization();
        $invite = OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'expired@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->subMinute(),
        ]);

        $url = URL::temporarySignedRoute('invitations.accept', now()->addHour(), [
            'token' => $invite->token,
            'email' => $invite->email,
        ]);

        $this->getJson($url)
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'This invitation has expired.',
            ]);
    }

    public function test_guest_accept_fails_when_invite_already_accepted(): void
    {
        $organization = $this->createOrganization();
        $invite = OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'accepted@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
            'accepted_at' => now(),
        ]);

        $url = URL::temporarySignedRoute('invitations.accept', now()->addHour(), [
            'token' => $invite->token,
            'email' => $invite->email,
        ]);

        $this->getJson($url)
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'This invitation has already been accepted.',
            ]);
    }

    public function test_guest_accept_fails_when_signed_email_mismatches_invite_email(): void
    {
        $organization = $this->createOrganization();
        $invite = OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'real@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
        ]);

        $url = URL::temporarySignedRoute('invitations.accept', now()->addHour(), [
            'token' => $invite->token,
            'email' => 'other@example.com',
        ]);

        $this->getJson($url)
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Invitation email mismatch.',
            ]);
    }

    private function createOrganization(): Organization
    {
        $owner = User::factory()->create();

        $organization = Organization::query()->create([
            'name' => 'Org '.Str::random(6),
            'slug' => 'org-'.Str::random(8),
            'owner_id' => $owner->id,
        ]);

        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);

        return $organization;
    }
}
