<?php

namespace App\Contracts\Actions\Task;

use App\Models\Task;
use App\Models\User;

interface ReopensTaskAction
{
    public function reopen(User $actor, Task $task): Task;
}
