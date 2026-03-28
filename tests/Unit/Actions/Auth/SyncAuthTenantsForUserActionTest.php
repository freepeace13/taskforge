<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\SyncAuthTenantsForUserAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncAuthTenantsForUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_local_organizations_and_membership_from_auth_tenants(): void
    {
        $user = User::factory()->create();
        $action = new SyncAuthTenantsForUserAction;

        $action->sync($user, [
            [
                'id' => 10,
                'slug' => 'acme',
                'name' => 'Acme Corp',
                'role' => 'owner',
            ],
        ]);

        $this->assertDatabaseHas('organizations', [
            'auth_organization_id' => '10',
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'owner_id' => $user->id,
        ]);

        $org = Organization::query()->where('auth_organization_id', '10')->firstOrFail();
        $this->assertTrue($user->fresh()->belongsToOrganization($org));
        $this->assertSame(Role::Owner, $user->fresh()->organizationRole($org));
    }

    public function test_it_updates_organization_name_on_subsequent_sync(): void
    {
        $user = User::factory()->create();
        $action = new SyncAuthTenantsForUserAction;

        $action->sync($user, [
            ['id' => 10, 'slug' => 'acme', 'name' => 'Acme', 'role' => 'member'],
        ]);
        $action->sync($user, [
            ['id' => 10, 'slug' => 'acme', 'name' => 'Acme Renamed', 'role' => 'member'],
        ]);

        $this->assertDatabaseHas('organizations', [
            'auth_organization_id' => '10',
            'name' => 'Acme Renamed',
        ]);
    }

    public function test_it_updates_member_role_when_auth_payload_changes(): void
    {
        $user = User::factory()->create();
        $action = new SyncAuthTenantsForUserAction;

        $action->sync($user, [
            ['id' => 10, 'slug' => 'acme', 'name' => 'Acme', 'role' => 'member'],
        ]);
        $action->sync($user, [
            ['id' => 10, 'slug' => 'acme', 'name' => 'Acme', 'role' => 'admin'],
        ]);

        $org = Organization::query()->where('auth_organization_id', '10')->firstOrFail();
        $this->assertSame(Role::Admin, $user->fresh()->organizationRole($org));
    }

    public function test_it_detaches_user_from_auth_backed_orgs_when_removed_from_payload(): void
    {
        $user = User::factory()->create();
        $action = new SyncAuthTenantsForUserAction;

        $action->sync($user, [
            ['id' => 10, 'slug' => 'acme', 'name' => 'Acme', 'role' => 'member'],
            ['id' => 20, 'slug' => 'globex', 'name' => 'Globex', 'role' => 'member'],
        ]);

        $action->sync($user, [
            ['id' => 10, 'slug' => 'acme', 'name' => 'Acme', 'role' => 'member'],
        ]);

        $globex = Organization::query()->where('auth_organization_id', '20')->firstOrFail();
        $this->assertFalse($user->fresh()->belongsToOrganization($globex));
    }

    public function test_it_does_not_detach_local_only_organizations_when_payload_is_empty(): void
    {
        $user = User::factory()->create();
        $local = Organization::factory()->create(['owner_id' => $user->id]);
        $local->members()->attach($user->id, ['role' => Role::Owner->value]);

        $action = new SyncAuthTenantsForUserAction;
        $action->sync($user, []);

        $this->assertTrue($user->fresh()->belongsToOrganization($local));
    }

    public function test_it_removes_all_auth_backed_memberships_when_payload_is_empty(): void
    {
        $user = User::factory()->create();
        $action = new SyncAuthTenantsForUserAction;

        $action->sync($user, [
            ['id' => 10, 'slug' => 'acme', 'name' => 'Acme', 'role' => 'member'],
        ]);
        $action->sync($user, []);

        $org = Organization::query()->where('auth_organization_id', '10')->firstOrFail();
        $this->assertFalse($user->fresh()->belongsToOrganization($org));
    }
}
