<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\DeletesTaskAction as DeletesTaskContract;
use App\Models\Task;
use App\Models\User;

class DeleteTaskAction implements DeletesTaskContract
{
    public function delete(User $actor, Task $task): void
    {
        $project = $task->project;

        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not belong to this organization.');
        }

        $task->delete();
    }
}
