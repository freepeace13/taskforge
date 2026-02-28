<?php

namespace App\Contracts\Actions\Task;

use App\Models\Task;
use App\Models\User;

interface CompletesTaskAction
{
    public function complete(User $actor, Task $task): Task;
}
