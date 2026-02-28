<?php

namespace App\Contracts\Actions\Organization;

use App\Models\Organization;
use App\Models\User;

interface DeletesOrganizationAction
{
    public function delete(User $user, Organization $organization);
}
