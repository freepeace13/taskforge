<?php

namespace App\Data;

use App\Enums\Role;

final class TenantContext
{
    public function __construct(
        public readonly int $userId,
        public readonly int $organizationId,
        public readonly Role $role,
    ) {}
}
