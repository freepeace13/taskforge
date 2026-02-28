<?php

namespace App\Data;

use App\Enums\Role;

class MemberData
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $userId,
        public readonly Role $role
    ) {}
}
