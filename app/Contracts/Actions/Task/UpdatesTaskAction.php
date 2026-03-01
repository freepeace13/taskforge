<?php

namespace App\Contracts\Actions\Task;

use App\Data\TaskData;
use App\Models\Task;
use App\Models\User;

interface UpdatesTaskAction
{
    public function update(User $actor, Task $task, TaskData $data): Task;
}
