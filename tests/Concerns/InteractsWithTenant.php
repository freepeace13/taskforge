<?php

namespace Tests\Concerns;

use App\Data\TenantContext;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;

trait InteractsWithTenant
{
    protected function createOrganizationWithMember(Role $role = Role::Owner): array
    {
        $organization = Organization::factory()->create();
        $user = User::query()->findOrFail($organization->owner_id);

        if (empty($user->auth_id)) {
            $user->update(['auth_id' => 'test-'.Str::random(20)]);
            $user->refresh();
        }

        $organization->members()->attach($user->id, ['role' => $role->value]);

        return [$organization, $user];
    }

    protected function setTenantContext(User $user, Organization $organization, Role $role): void
    {
        $tenant = new TenantContext(
            user: $user,
            organization: $organization,
            role: $role
        );

        app()->instance(TenantContext::class, $tenant);
    }

    protected function actingAsInOrganization(User $user, Organization $organization, Role $role = Role::Member): void
    {
        if (empty($user->auth_id)) {
            $user->update(['auth_id' => 'test-'.Str::random(20)]);
            $user->refresh();
        }

        $this->withHeaders([
            'Authorization' => 'Bearer test:'.$user->auth_id,
            'x-tenant-id' => (string) $organization->id,
        ]);

        $this->setTenantContext($user, $organization, $role);
    }

    protected function actingAsAuthServer(User $user): void
    {
        if (empty($user->auth_id)) {
            $user->update(['auth_id' => 'test-'.Str::random(20)]);
            $user->refresh();
        }

        $this->withHeaders([
            'Authorization' => 'Bearer test:'.$user->auth_id,
        ]);
    }
}
