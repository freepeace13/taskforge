<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\ReopensTaskAction as ReopensTaskContract;
use App\Models\Task;
use App\Models\User;
use App\Support\AuthorizesActions;

class ReopenTaskAction implements ReopensTaskContract
{
    use AuthorizesActions;

    public function reopen(User $actor, Task $task): Task
    {
        $this->authorizeForUser($actor, 'reopen', $task);

        $task->reopen()->save();

        return $task;
    }
}
