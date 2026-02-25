<?php

namespace App\Data;

use App\Enums\Role;

/**
 * Data Transfer Object (DTO) for the tenant context
 *
 * @author Kin Basco
 */
final class TenantContext
{
    public function __construct(
        public readonly int $userId,
        public readonly int $organizationId,
        public readonly Role $role,
    ) {}
}
