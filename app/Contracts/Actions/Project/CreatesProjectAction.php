<?php

namespace App\Contracts\Actions\Project;

use App\Data\Projectdata;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;

interface CreatesProjectAction
{
    public function create(User $actor, Organization $organization, Projectdata $data): Project;
}
