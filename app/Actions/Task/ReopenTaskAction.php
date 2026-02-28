<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\ReopensTaskAction as ReopensTaskContract;
use App\Models\Task;
use App\Models\User;

class ReopenTaskAction implements ReopensTaskContract
{
    public function reopen(User $actor, Task $task): Task
    {
        $project = $task->project;

        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not belong to this organization.');
        }

        $task->reopen()->save();

        return $task;
    }
}
