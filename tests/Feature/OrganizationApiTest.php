<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithTenant;
use Tests\TestCase;

class OrganizationApiTest extends TestCase
{
    use InteractsWithTenant, RefreshDatabase;

    public function test_index_lists_only_organizations_user_belongs_to(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $otherUser */
        $otherUser = User::factory()->create();

        $ownedOrg = Organization::factory()->create(['owner_id' => $user->id]);
        $ownedOrg->members()->attach($user->id, ['role' => Role::Owner->value]);
        $this->createOrganizationWithMember(Role::Member); // unrelated org

        $this->actingAsAuthServer($user);

        $response = $this->getJson(route('api.v1.orgs.index'));

        $response->assertOk()
            ->assertJsonPath('data.0.id', $ownedOrg->id)
            ->assertJsonMissing(['owner_id' => $otherUser->id]);
    }

    public function test_store_creates_organization_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAsAuthServer($user);

        $response = $this->postJson(route('api.v1.orgs.store'), [
            'name' => 'My Org',
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'My Org']);

        $this->assertDatabaseHas('organizations', [
            'name' => 'My Org',
            'owner_id' => $user->id,
        ]);
    }

    public function test_show_and_destroy_respect_organization_policy(): void
    {
        [$organization, $owner] = $this->createOrganizationWithMember(Role::Owner);
        $nonMember = User::factory()->create();

        // Owner can view and delete
        $this->actingAsInOrganization($owner, $organization, Role::Owner);

        $this->getJson(route('api.v1.orgs.show'))
            ->assertOk()
            ->assertJsonFragment(['id' => $organization->id]);

        $this->deleteJson(route('api.v1.orgs.destroy'))
            ->assertNoContent();

        $this->assertDatabaseMissing('organizations', [
            'id' => $organization->id,
        ]);

        // Recreate org for non-member test
        $organization = Organization::factory()->create();
        $organization->members()->attach($organization->owner_id, ['role' => Role::Owner->value]);

        // Non-member cannot view (TenancyMiddleware will 404 when member not found)
        $this->actingAsAuthServer($nonMember);
        $this->withHeaders(['x-tenant-id' => (string) $organization->id]);

        $this->getJson(route('api.v1.orgs.show'))
            ->assertNotFound();
    }
}
