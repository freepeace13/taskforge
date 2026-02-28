<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Access\Gate;

trait AuthorizesActions
{
    public function authorizeForUser($user, $ability, $arguments = [])
    {
        return app(Gate::class)->forUser($user)->authorize($ability, $arguments);
    }
}
