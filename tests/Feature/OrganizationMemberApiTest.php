<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrganizationMemberApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_member_role_and_remove_member(): void
    {
        [$organization, $owner] = $this->createOrganizationWithRole(Role::Owner);
        $member = User::factory()->create();
        $organization->members()->attach($member->id, ['role' => Role::Member->value]);

        Sanctum::actingAs($owner);

        $this->patchJson('/api/v1/orgs/'.$organization->slug.'/members/'.$member->id, [
            'role' => Role::Admin->value,
        ])->assertOk();

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'role' => Role::Admin->value,
        ]);

        $this->deleteJson('/api/v1/orgs/'.$organization->slug.'/members/'.$member->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $member->id,
        ]);
    }

    public function test_non_admin_member_cannot_update_or_remove_members(): void
    {
        [$organization, $actor] = $this->createOrganizationWithRole(Role::Member);
        $target = User::factory()->create();
        $organization->members()->attach($target->id, ['role' => Role::Member->value]);

        Sanctum::actingAs($actor);

        $this->patchJson('/api/v1/orgs/'.$organization->slug.'/members/'.$target->id, [
            'role' => Role::Admin->value,
        ])->assertForbidden();

        $this->deleteJson('/api/v1/orgs/'.$organization->slug.'/members/'.$target->id)
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

        Sanctum::actingAs($admin);

        $this->patchJson('/api/v1/orgs/'.$organization->slug.'/members/'.$owner->id, [
            'role' => Role::Member->value,
        ])->assertUnprocessable();

        $this->deleteJson('/api/v1/orgs/'.$organization->slug.'/members/'.$owner->id)
            ->assertUnprocessable();
    }

    private function createOrganizationWithRole(Role $role): array
    {
        $organization = Organization::factory()->create();
        $user = User::query()->findOrFail($organization->owner_id);

        $organization->members()->attach($user->id, ['role' => $role->value]);

        return [$organization, $user];
    }
}
