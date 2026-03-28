<?php

namespace App\Actions\Auth;

use App\Contracts\Actions\Auth\SyncsAuthTenantsForUser;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;

class SyncAuthTenantsForUserAction implements SyncsAuthTenantsForUser
{
    public function sync(User $user, array $tenants): void
    {
        $authOrganizationIds = [];

        foreach ($tenants as $tenant) {
            if (! is_array($tenant)) {
                continue;
            }

            $authId = isset($tenant['id']) ? (string) $tenant['id'] : '';

            if ($authId === '') {
                continue;
            }

            $authOrganizationIds[] = $authId;

            $name = isset($tenant['name']) ? (string) $tenant['name'] : 'Organization';

            $baseSlug = isset($tenant['slug']) && $tenant['slug'] !== ''
                ? Str::slug((string) $tenant['slug'])
                : Str::slug($name);

            if ($baseSlug === '') {
                $baseSlug = 'organization';
            }

            $organization = Organization::query()->firstOrNew([
                'auth_organization_id' => $authId,
            ]);

            $slug = $this->uniqueSlugForTenant($baseSlug, $authId, $organization->exists ? $organization->id : null);

            if (! $organization->exists) {
                $organization->fill([
                    'name' => $name,
                    'slug' => $slug,
                    'owner_id' => $user->id,
                ]);
                $organization->save();
            } else {
                $organization->update([
                    'name' => $name,
                    'slug' => $slug,
                ]);
            }

            $role = $this->mapRole($tenant['role'] ?? null);

            if ($user->organizations()->whereKey($organization->id)->exists()) {
                $user->organizations()->updateExistingPivot($organization->id, ['role' => $role->value]);
            } else {
                $user->organizations()->attach($organization->id, ['role' => $role->value]);
            }
        }

        $this->detachRemovedAuthTenants($user, $authOrganizationIds);
    }

    /**
     * @param  list<string>  $authOrganizationIds
     */
    protected function detachRemovedAuthTenants(User $user, array $authOrganizationIds): void
    {
        $query = $user->organizations()
            ->whereNotNull('organizations.auth_organization_id');

        if (count($authOrganizationIds) > 0) {
            $query->whereNotIn('organizations.auth_organization_id', $authOrganizationIds);
        }

        $ids = $query->pluck('organizations.id');

        if ($ids->isEmpty()) {
            return;
        }

        $user->organizations()->detach($ids);
    }

    protected function uniqueSlugForTenant(string $baseSlug, string $authOrganizationId, ?int $exceptOrganizationId): string
    {
        $candidates = array_unique([
            $baseSlug,
            $baseSlug.'-'.$authOrganizationId,
        ]);

        foreach ($candidates as $candidate) {
            $query = Organization::query()->where('slug', $candidate);

            if ($exceptOrganizationId !== null) {
                $query->where('id', '!=', $exceptOrganizationId);
            }

            if (! $query->exists()) {
                return $candidate;
            }
        }

        $n = 2;

        while (true) {
            $candidate = $baseSlug.'-'.$authOrganizationId.'-'.$n;
            $query = Organization::query()->where('slug', $candidate);

            if ($exceptOrganizationId !== null) {
                $query->where('id', '!=', $exceptOrganizationId);
            }

            if (! $query->exists()) {
                return $candidate;
            }

            $n++;
        }
    }

    protected function mapRole(mixed $role): Role
    {
        if (! is_string($role) || $role === '') {
            return Role::Member;
        }

        $normalized = strtolower($role);

        return match ($normalized) {
            'owner' => Role::Owner,
            'admin' => Role::Admin,
            'member' => Role::Member,
            default => Role::Member,
        };
    }
}
