<?php

namespace App\Contracts\Actions\Project;

use App\Models\Project;
use App\Models\User;

interface DeletesProjectAction
{
    public function delete(User $actor, Project $project);
}
