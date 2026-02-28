<?php

namespace App\Contracts\Actions\Task;

use App\Data\TaskData;
use App\Models\Task;
use App\Models\User;

interface UpdatesTaskAction
{
    public function update(User $user, Task $task, TaskData $data): Task;
}
