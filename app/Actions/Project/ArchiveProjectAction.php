<?php

namespace App\Actions\Project;

use App\Contracts\Actions\Project\ArchivesProjectAction as ArchivesProjectContract;
use App\Models\Project;
use App\Models\User;
use App\Support\AuthorizesActions;

class ArchiveProjectAction implements ArchivesProjectContract
{
    use AuthorizesActions;

    public function archive(User $actor, Project $project): Project
    {
        $this->authorizeForUser($actor, 'archive', $project);

        $project->archive()->save();

        return $project;
    }
}
