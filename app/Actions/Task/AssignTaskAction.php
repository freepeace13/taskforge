<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\AssignsTaskAction as AssignsTaskContract;
use App\Models\Task;
use App\Models\User;
use App\Support\AuthorizesActions;

class AssignTaskAction implements AssignsTaskContract
{
    use AuthorizesActions;

    public function assign(User $actor, Task $task, ?int $userId = null): Task
    {
        $this->authorizeForUser($actor, 'assign', [$task, $userId]);

        $task->assigned_to_user_id = $userId;
        $task->save();

        return $task;
    }
}
