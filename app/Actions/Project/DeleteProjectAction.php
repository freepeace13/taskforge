<?php

namespace App\Actions\Project;

use App\Contracts\Actions\Project\DeletesProjectAction as DeletesProjectContract;
use App\Models\Project;
use App\Models\User;
use App\Support\AuthorizesActions;

class DeleteProjectAction implements DeletesProjectContract
{
    use AuthorizesActions;

    public function delete(User $actor, Project $project): void
    {
        $this->authorizeForUser($actor, 'delete', $project);

        $project->delete();
    }
}
