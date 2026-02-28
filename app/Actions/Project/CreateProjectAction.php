<?php

namespace App\Actions\Project;

use App\Contracts\Actions\Project\CreatesProjectAction as CreatesProjectContract;
use App\Data\ProjectData;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Support\AuthorizesActions;

class CreateProjectAction implements CreatesProjectContract
{
    use AuthorizesActions;

    public function create(User $actor, Organization $organization, ProjectData $data): Project
    {
        $this->authorizeForUser($actor, 'create', [Project::class, $organization]);

        $project = $organization->project()->create([
            'name' => $data->name,
            'description' => $data->description
        ]);

        return $project;
    }
}
