<?php

namespace Modules\Organization\DTOs;

use Modules\Organization\Role;

final class TenantContext
{
    public function __construct(
        public readonly int $organizationId,
        public readonly string $organizationSlug,
        public readonly string $organizationName,
        public readonly int $userId,
        public readonly Role $role,
    ) {}

    public function isOwner(): bool
    {
        return $this->role === Role::Owner;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }
}
