<?php

namespace App\Data;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;

class MemberData
{
    public function __construct(
        public readonly Organization $organization,
        public readonly User $user,
        public readonly Role $role
    ) {}
}
