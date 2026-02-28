<?php

namespace App\Contracts\Actions\Task;

use App\Data\TaskData;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

interface CreatesTaskAction
{
    public function create(User $actor, Project $project, TaskData $data): Task;
}
