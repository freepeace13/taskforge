<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\CompletesTaskAction as CompletesTaskContract;
use App\Models\Task;
use App\Models\User;
use App\Support\AuthorizesActions;

class CompleteTaskAction implements CompletesTaskContract
{
    use AuthorizesActions;

    public function complete(User $actor, Task $task): Task
    {
        $this->authorizeForUser($actor, 'complete', $task);

        $task->complete()->save();

        return $task;
    }
}
