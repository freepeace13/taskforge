<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\InteractsWithTenant;
use Tests\TestCase;

class OrganizationMemberApiTest extends TestCase
{
    use InteractsWithTenant, RefreshDatabase;

    public function test_owner_can_update_member_role_and_remove_member(): void
    {
        [$organization, $owner] = $this->createOrganizationWithMember(Role::Owner);
        $member = User::factory()->create();
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        $this->actingAsInOrganization($owner, $organization, Role::Owner);

        $this->patchJson(route('api.v1.orgs.members.update', [
            'org' => $organization->slug,
            'user' => $member->id,
        ]), [
            'role' => Role::Admin->value,
        ])->assertOk();

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'role' => Role::Admin->value,
        ]);

        $this->deleteJson(route('api.v1.orgs.members.destroy', [
            'org' => $organization->slug,
            'user' => $member->id,
        ]))
            ->assertNoContent();

        $this->assertDatabaseMissing('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $member->id,
        ]);
    }

    public function test_non_admin_member_cannot_update_or_remove_members(): void
    {
        [$organization, $actor] = $this->createOrganizationWithMember(Role::Member);
        $target = User::factory()->create();
        $organization->members()->attach($target->id, ['role' => Role::Member->value]);

        $this->actingAsInOrganization($actor, $organization, Role::Member);

        $this->patchJson(route('api.v1.orgs.members.update', [
            'org' => $organization->slug,
            'user' => $target->id,
        ]), [
            'role' => Role::Admin->value,
        ])->assertForbidden();

        $this->deleteJson(route('api.v1.orgs.members.destroy', [
            'org' => $organization->slug,
            'user' => $target->id,
        ]))
            ->assertForbidden();
    }

    public function test_owner_cannot_be_updated_or_removed(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $organization = Organization::query()->create([
            'name' => 'Org '.Str::random(6),
            'slug' => 'org-'.Str::random(8),
            'owner_id' => $owner->id,
        ]);

        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);
        $organization->members()->attach($admin->id, ['role' => Role::Admin->value]);

        $this->actingAsInOrganization($admin, $organization, Role::Admin);

        $this->patchJson(route('api.v1.orgs.members.update', [
            'org' => $organization->slug,
            'user' => $owner->id,
        ]), [
            'role' => Role::Member->value,
        ])->assertUnprocessable();

        $this->deleteJson(route('api.v1.orgs.members.destroy', [
            'org' => $organization->slug,
            'user' => $owner->id,
        ]))
            ->assertUnprocessable();
    }

    // createOrganizationWithMember comes from InteractsWithTenant on the base TestCase
}
