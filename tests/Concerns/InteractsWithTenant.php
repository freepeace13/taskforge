<?php

namespace Tests\Concerns;

use App\Data\TenantContext;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait InteractsWithTenant
{
    protected function createOrganizationWithMember(Role $role = Role::Owner): array
    {
        $organization = Organization::factory()->create();
        $user = User::query()->findOrFail($organization->owner_id);

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
        Sanctum::actingAs($user);

        $this->setTenantContext($user, $organization, $role);
    }
}
