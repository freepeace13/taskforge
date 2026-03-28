<?php

namespace App\Contracts\Actions\Auth;

use App\Models\User;

interface SyncsAuthTenantsForUser
{
    /**
     * Upsert local organizations and memberships from the auth server tenant list.
     *
     * @param  list<array<string, mixed>>  $tenants
     */
    public function sync(User $user, array $tenants): void;
}
