<?php

namespace App\Data;

use App\Enums\Role;

final class InviteUserData
{
    public function __construct(
        public readonly string $email,
        public readonly Role $role
    ) { }
}
