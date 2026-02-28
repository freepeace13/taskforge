<?php

namespace App\Contracts\Actions\Project;

use App\Data\ProjectData;
use App\Models\Project;
use App\Models\User;

interface UpdatesProjectAction
{
    public function update(User $actor, Project $project, ProjectData $data): Project;
}
