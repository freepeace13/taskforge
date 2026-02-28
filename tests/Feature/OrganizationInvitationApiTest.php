<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrganizationInvitationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_invitation(): void
    {
        [$organization, $owner] = $this->createOrganizationWithMember(Role::Owner);
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/invitations', [
            'email' => 'invitee@example.com',
            'role' => Role::Member->value,
        ])->assertCreated()
            ->assertJsonStructure(['id', 'email', 'role', 'token', 'accept_url']);
    }

    public function test_member_cannot_create_invitation(): void
    {
        [$organization, $member] = $this->createOrganizationWithMember(Role::Member);
        Sanctum::actingAs($member);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/invitations', [
            'email' => 'invitee@example.com',
            'role' => Role::Member->value,
        ])->assertForbidden();
    }

    public function test_duplicate_active_invitation_is_blocked(): void
    {
        [$organization, $owner] = $this->createOrganizationWithMember(Role::Owner);
        Sanctum::actingAs($owner);

        OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'duplicate@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
        ]);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/invitations', [
            'email' => 'duplicate@example.com',
            'role' => Role::Member->value,
        ])->assertUnprocessable();
    }

    public function test_inviting_existing_member_is_blocked(): void
    {
        [$organization, $owner] = $this->createOrganizationWithMember(Role::Owner);
        $member = User::factory()->create(['email' => 'already-member@example.com']);
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/orgs/'.$organization->slug.'/invitations', [
            'email' => 'already-member@example.com',
            'role' => Role::Member->value,
        ])->assertUnprocessable();
    }

    public function test_invitation_listing_filters_by_status(): void
    {
        [$organization, $owner] = $this->createOrganizationWithMember(Role::Owner);
        Sanctum::actingAs($owner);

        OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'pending@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
        ]);

        OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'accepted@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
            'accepted_at' => now(),
        ]);

        OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'expired@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->subDay(),
        ]);

        $this->getJson('/api/v1/orgs/'.$organization->slug.'/invitations?status=pending')
            ->assertOk()
            ->assertJsonFragment(['email' => 'pending@example.com'])
            ->assertJsonMissing(['email' => 'accepted@example.com'])
            ->assertJsonMissing(['email' => 'expired@example.com']);
    }

    public function test_revoke_invitation_prevents_acceptance(): void
    {
        [$organization, $owner] = $this->createOrganizationWithMember(Role::Owner);
        Sanctum::actingAs($owner);

        $invite = OrganizationInvite::query()->create([
            'organization_id' => $organization->id,
            'email' => 'revoke@example.com',
            'role' => Role::Member->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDay(),
        ]);

        $this->deleteJson('/api/v1/orgs/'.$organization->slug.'/invitations/'.$invite->id)
            ->assertNoContent();

        $url = URL::temporarySignedRoute('invitations.accept', now()->addHour(), [
            'token' => $invite->token,
            'email' => $invite->email,
        ]);

        $this->getJson($url)->assertNotFound();
    }

    private function createOrganizationWithMember(Role $role): array
    {
        $organization = Organization::factory()->create();
        $user = User::query()->findOrFail($organization->owner_id);

        $organization->members()->attach($user->id, ['role' => $role->value]);

        return [$organization, $user];
    }
}
