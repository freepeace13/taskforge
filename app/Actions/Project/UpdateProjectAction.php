<?php

namespace App\Actions\Project;

use App\Contracts\Actions\Project\UpdatesProjectAction as UpdatesProjectContract;
use App\Data\ProjectData;
use App\Models\Project;
use App\Models\User;
use App\Support\AuthorizesActions;

class UpdateProjectAction implements UpdatesProjectContract
{
    use AuthorizesActions;

    public function update(User $actor, Project $project, ProjectData $data): Project
    {
        $this->authorizeForUser($actor, 'update', $project);

        $project->update([
            'name' => $data->name,
            'description' => $data->description ?? $project->description,
        ]);

        return $project;
    }
}
