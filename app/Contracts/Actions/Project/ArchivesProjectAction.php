<?php

namespace App\Contracts\Actions\Project;

use App\Models\Project;
use App\Models\User;

interface ArchivesProjectAction
{
    public function archive(User $actor, Project $project): Project;
}
