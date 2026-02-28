<?php

namespace App\Contracts\Actions\Task;

use App\Models\Task;
use App\Models\User;

interface DeletesTaskAction
{
    public function delete(User $actor, Task $task): void;
}
