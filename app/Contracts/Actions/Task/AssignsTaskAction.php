<?php

namespace App\Contracts\Actions\Task;

use App\Models\Task;
use App\Models\User;

interface AssignsTaskAction
{
    public function assign(User $actor, Task $task, ?int $userId = null): Task;
}
