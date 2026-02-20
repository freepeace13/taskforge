<?php

namespace Modules\Organization;

use Modules\Organization\Contracts\OrganizationContextResolver;
use Modules\Organization\DTOs\TenantContext;
use Illuminate\Auth\Access\AuthorizationException;

class EloquentOrganizationContextResolver implements OrganizationContextResolver
{
    public function resolveForUser(string $orgSlug, int $userId): TenantContext
    {
        $org = Organization::query()
            ->where('slug', $orgSlug)
            ->firstOrFail();

        $membership = OrganizationMember::query()
            ->where('organization_id', $org->id)
            ->where('user_id', $userId)
            ->first();

        if (! $membership) {
            throw new AuthorizationException('You are not a member of this organization.');
        }

        return new TenantContext(
            organizationId: $org->id,
            organizationSlug: $org->slug,
            organizationName: $org->name,
            userId: $userId,
            role: $membership->role,
        );
    }
}
