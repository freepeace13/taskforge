<?php

namespace Modules\Organization\Contracts;

use Modules\Organization\DTOs\TenantContext;

interface OrganizationContextResolver
{
    /**
     * Resolve tenant context by org slug for a given user.
     *
     * Should throw ModelNotFoundException if org doesn't exist.
     * Should throw AuthorizationException (or return null) if user is not a member.
     */
    public function resolveForUser(string $orgSlug, int $userId): TenantContext;
}
