<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrganizationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_only_organizations_user_belongs_to(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $otherUser */
        $otherUser = User::factory()->create();

        $ownedOrg = Organization::factory()->create(['owner_id' => $user->id]);
        $ownedOrg->members()->attach($user->id, ['role' => Role::Owner->value]);
        $this->createOrganizationWithMember(Role::Member); // unrelated org

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/orgs');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $ownedOrg->id)
            ->assertJsonMissing(['owner_id' => $otherUser->id]);
    }

    public function test_store_creates_organization_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/orgs', [
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
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/orgs/'.$organization->slug)
            ->assertOk()
            ->assertJsonFragment(['id' => $organization->id]);

        $this->deleteJson('/api/v1/orgs/'.$organization->slug)
            ->assertNoContent();

        $this->assertDatabaseMissing('organizations', [
            'id' => $organization->id,
        ]);

        // Non-member cannot view
        Sanctum::actingAs($nonMember);

        $this->getJson('/api/v1/orgs/'.$organization->slug)
            ->assertNotFound();
    }
}
