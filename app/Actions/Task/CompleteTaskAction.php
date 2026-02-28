<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\CompletesTaskAction as CompletesTaskContract;
use App\Models\Task;
use App\Models\User;

class CompleteTaskAction implements CompletesTaskContract
{
    public function complete(User $actor, Task $task): Task
    {
        $project = $task->project;

        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not belong to this organization.');
        }

        $task->complete()->save();

        return $task;
    }
}
