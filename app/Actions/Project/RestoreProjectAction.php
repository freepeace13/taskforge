<?php

namespace App\Actions\Project;

use App\Contracts\Actions\Project\RestoresProjectAction as RestoresProjectContract;
use App\Models\Project;
use App\Models\User;
use App\Support\AuthorizesActions;

class RestoreProjectAction implements RestoresProjectContract
{
    use AuthorizesActions;

    public function restore(User $actor, Project $project): Project
    {
        $this->authorizeForUser($actor, 'restore', $project);

        $project->restore()->save();

        return $project;
    }
}
