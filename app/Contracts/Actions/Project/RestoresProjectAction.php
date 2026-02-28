<?php

namespace App\Contracts\Actions\Project;

use App\Models\Project;
use App\Models\User;

interface RestoresProjectAction
{
    public function restore(User $actor, Project $project): Project;
}
