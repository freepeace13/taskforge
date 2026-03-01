<?php

namespace App\Data;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;

final class TenantContext
{
    public function __construct(
        public readonly User $user,
        public readonly Organization $organization,
        public readonly Role $role,
    ) {}
}
